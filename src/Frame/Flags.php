<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame;

class Flags
{
    const NONE = 0;
    const ACK = 1;
    const END_STREAM = 1;
    const END_HEADERS = 4;
    const PADDED = 8;
    const PRIORITY = 20;
}