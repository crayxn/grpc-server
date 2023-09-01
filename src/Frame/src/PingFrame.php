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

class PingFrame extends Frame
{
    public function __construct(string $ping, int $streamId = 0)
    {
        parent::__construct($ping, SWOOLE_HTTP2_TYPE_PING, Flags::NONE, $streamId);
    }
}