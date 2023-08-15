<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame\src;

use Crayxn\GrpcServer\Frame\Flags;
use Crayxn\GrpcServer\Frame\Frame;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\Message;

class DataFrame extends Frame
{
    public function __construct(mixed $payload, int $streamId)
    {
        if ($payload == null) {
            $payload = new GPBEmpty();
        }
        $payload = $payload instanceof Message ? \Hyperf\Grpc\Parser::serializeMessage($payload) : (string)$payload;
        parent::__construct($payload, SWOOLE_HTTP2_TYPE_DATA, Flags::NONE, $streamId);
    }
}