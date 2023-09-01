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

class PongFrame extends Frame
{
    public function __construct(string $pong, int $streamId = 0)
    {
        parent::__construct($pong, SWOOLE_HTTP2_TYPE_PING, Flags::ACK, $streamId);
    }
}