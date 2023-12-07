<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer;

use Crayxn\GrpcServer\Channel\ReqChannel;
use Crayxn\GrpcServer\Channel\ReqChannelDepository;
use Crayxn\GrpcServer\Exception\GrpcServerException;
use Crayxn\GrpcServer\Frame\Flags;
use Crayxn\GrpcServer\Frame\Frame;
use Crayxn\GrpcServer\Frame\Parser;
use Crayxn\GrpcServer\Frame\src\PongFrame;
use Crayxn\GrpcServer\Frame\src\SettingFrame;
use Crayxn\GrpcServer\Frame\Types;
use Hyperf\Context\Context;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Engine\Channel;
use Hyperf\Grpc\StatusCode;
use Hyperf\GrpcServer\Exception\Handler\GrpcExceptionHandler;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\HttpMessage\Uri\Uri;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Support\SafeCaller;
use Swoole\Http\Response;
use Swoole\Server as SwooleServer;
use Throwable;

class Server extends \Hyperf\GrpcServer\Server
{
    public function initCoreMiddleware(string $serverName): void
    {
        $this->serverName = $serverName;
        $this->coreMiddleware = new \Crayxn\GrpcServer\CoreMiddleware($this->container, $serverName);

        $config = $this->container->get(ConfigInterface::class);
        $this->middlewares = $config->get('middlewares.' . $serverName, []);
        $this->exceptionHandlers = $config->get('exceptions.handler.' . $serverName, [
            GrpcExceptionHandler::class,
        ]);
    }

    public array $parserChannel = [];

    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param int $reactor_id
     * @param string $data
     * @return void
     * @throws Throwable
     */
    public function onReceive(SwooleServer $server, int $fd, int $reactor_id, string $data): void
    {
        $parser = $this->container->get(Parser::class);
        // wait
        if (isset($this->parserChannel[$fd]) && $this->parserChannel[$fd] instanceof Channel) {
            if (false !== $parserFailData = $this->parserChannel[$fd]->pop(3.0)) {
                $data = $parserFailData . $data;
            }
        } else {
            // send setting
            $server->send($fd, $parser->pack(new SettingFrame()));
        }
        $this->parserChannel[$fd] = new Channel(1);
        //get frames
        $this->parserChannel[$fd]->push($parser->unpack($parser->exceptUpgrade($data), $frames), 1.0);
        if (empty($frames)) return;

        $channelDepository = $this->container->get(ReqChannelDepository::class);

        /**
         * @var Frame $frame
         */
        foreach ($frames as $index => $frame) {
            /**
             * @var ReqChannel $channel
             */
            $channel = $channelDepository->get("$fd:$frame->streamId");
            if ($frame->type == Types::HEADERS) {
                // new request
                $frame->length > 0 && \Hyperf\Coroutine\go(function () use ($server, $fd, $frame) {
                    $this->request($server, $fd, $frame);
                });
            } elseif ($frame->type == Types::DATA) {
                // push payload
                in_array($frame->flags, [Flags::NONE, Flags::END_STREAM]) && $frame->length > 0 && $channel->push($frame->payload);
            } elseif ($frame->type == Types::GOAWAY || $frame->type == Types::RST_STREAM) {
                //change state
                $channelDepository->down("$fd:$frame->streamId");
            } elseif ($frame->type == Types::PING) {
                // Pong
                $server->send($fd, $parser->pack(new PongFrame($frame->payload)));
            }
            unset($frames[$index]);
            // close stream
            if (in_array($frame->type, [Types::HEADERS, Types::DATA]) && $frame->flags == Flags::END_STREAM) {
                //is not end
                if (count($frames) > 0) {
                    // reset
                    $frames[] = $frame;
                    continue;
                }
                $channel->push(false);
            }
        }
    }

    /**
     * @param SwooleServer $server
     * @param int $fd
     * @param Frame $headerFrame
     * @return void
     * @throws Throwable
     */
    public function request(SwooleServer $server, int $fd, Frame $headerFrame): void
    {
        $channelDepository = $this->container->get(ReqChannelDepository::class);
        $parser = $this->container->get(Parser::class);

        // set context
        $context = new ServerContext($server, $fd, $headerFrame->streamId);
        Context::set(ServerContext::class, $context);
        // new request
        // headers
        $swooleHeaders = [];
        foreach ($parser->decodeHeaderFrame($headerFrame) ?? [] as [$key, $value]) {
            $swooleHeaders[$key] = $value;
        }
        //create request
        $uri = new Uri(sprintf("%s://%s:%d%s", $swooleHeaders[':scheme'] ?? 'http', $server->host, $server->port, $swooleHeaders[':path'] ?? '/'));
        $request = new Request($swooleHeaders[':method'] ?? 'POST', $uri, $swooleHeaders, '', '2');

        try {
            CoordinatorManager::until(Constants::WORKER_START)->yield();
            [$psr7Request,] = $this->initRequestAndResponse($request, new Response());

            $psr7Request = $this->coreMiddleware->dispatch($psr7Request);
            /** @var Dispatched $dispatched */
            $dispatched = $psr7Request->getAttribute(Dispatched::class);
            $middlewares = $this->middlewares;

            if ($dispatched->isFound()) {
                $registeredMiddlewares = MiddlewareManager::get($this->serverName, $dispatched->handler->route, $psr7Request->getMethod());
                $middlewares = array_merge($middlewares, $registeredMiddlewares);
            }
            /**
             * @var \Hyperf\HttpMessage\Server\Response $response
             */
            $response = $this->dispatcher->dispatch($psr7Request, $middlewares, $this->coreMiddleware);

            if ($response->getTrailer('grpc-status') == StatusCode::OK) {
                $context->emit($response->getBody()->getContents());
            }
            // close request
            $context->end();

        } catch (Throwable $throwable) {
            $this->container->get(SafeCaller::class)->call(function () use ($throwable) {
                return $this->exceptionHandlerDispatcher->dispatch($throwable, $this->exceptionHandlers);
            });
            if ($throwable instanceof GrpcServerException) {
                $context->end($throwable->getCode(), $throwable->getMessage());
            } else {
                $context->end(StatusCode::ABORTED, 'service error');
            }
        } finally {
            // close the data channel
            $channelDepository->remove("$fd:$headerFrame->streamId");
            //remove fd parser channel
            if (isset($this->parserChannel[$fd])) {
                $this->parserChannel[$fd] instanceof Channel && $this->parserChannel[$fd]->close();
                unset($this->parserChannel[$fd]);
            }
        }
    }

}