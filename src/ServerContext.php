<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer;

use Crayoon\HyperfGrpc\Server\Http2Frame\FrameParser;
use Crayoon\HyperfGrpc\Server\Http2Frame\Http2Frame;
use Crayoon\HyperfGrpc\Server\Http2Stream\StreamManager;
use Crayxn\GrpcServer\Channel\ReqChannel;
use Crayxn\GrpcServer\Channel\ReqChannelDepository;
use Crayxn\GrpcServer\Frame\Flags;
use Crayxn\GrpcServer\Frame\Frame;
use Crayxn\GrpcServer\Frame\Parser;
use Crayxn\GrpcServer\Frame\src\DataFrame;
use Crayxn\GrpcServer\Frame\src\HeaderFrame;
use Crayxn\GrpcServer\Frame\src\SettingFrame;
use Crayxn\GrpcServer\Frame\Types;
use Google\Protobuf\Internal\Message;
use Hyperf\Context\ApplicationContext;
use Hyperf\Grpc\StatusCode;
use Psr\Container\ContainerInterface;
use Swoole\Server as SwooleServer;

class ServerContext
{
    /**
     * is polluted
     * @var bool
     */
    private bool $polluted = false;

    private ContainerInterface $container;

    /**
     * @param SwooleServer $swooleServer
     * @param int $fd
     * @param int $streamId
     * @throws \Throwable
     */
    public function __construct(
        private SwooleServer $swooleServer,
        private int          $fd = 0,
        private int          $streamId = 0,
    )
    {
        $this->container = ApplicationContext::getContainer();
    }

    /**
     * @return SwooleServer
     */
    public function getServer(): SwooleServer
    {
        return $this->swooleServer;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getSteamId(): int
    {
        return $this->streamId;
    }

    /**
     * @param string|array|null $deserialize
     * @return mixed
     */
    public function receive(mixed $deserialize = null): mixed
    {
        try {
            $payload = $this->container->get(ReqChannelDepository::class)
                ->get("$this->fd:$this->streamId")
                ->pop();
            if ($deserialize && $payload) {
                return \Hyperf\Grpc\Parser::deserializeMessage(is_array($deserialize) ? $deserialize : [$deserialize, 'mergeFromString'], $payload);
            }
            return $payload;
        } catch (\Throwable) {
        }
        return false;
    }

    /**
     * write frame
     * @param Frame $frame
     * @return bool
     */
    public function write(Frame $frame): bool
    {
        try {
            $channelDepository = $this->container->get(ReqChannelDepository::class);
            $parser = $this->container->get(Parser::class);
            // check stream status
            if (!$channelDepository->active("$this->fd:$this->streamId")) {
                return false;
            }
            $frames = [];
            if (!$this->polluted && $frame->type == Types::DATA) {
                //with header
                $frames[] = $parser->pack(new HeaderFrame($this->streamId));
                $this->polluted = true;
            }
            $frames[] = $parser->pack($frame);
            // send
            return !!$this->swooleServer->send($this->fd, implode('', $frames));
        } catch (\Throwable $exception) {
        }
        return false;
    }

    /**
     * emit message
     * @param string|Message|null $message
     * @return bool
     */
    public function emit(mixed $message = null): bool
    {
        return $this->write(new DataFrame($message, $this->streamId));
    }

    /**
     * end stream
     * @param int $status
     * @param string $message
     * @return bool
     */
    public function end(int $status = StatusCode::OK, string $message = 'ok'): bool
    {
        try {
            $this->write(new HeaderFrame($this->streamId, true, $status, $message));
            $this->write(new Frame(pack('N', 0), Types::RST_STREAM, Flags::NONE, $this->streamId));
            return true;
        } catch (\Throwable) {
        }
        return false;
    }

}