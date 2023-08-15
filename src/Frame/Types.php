<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame;

class Types
{
    const DATA = 0;
    const HEADERS = 1;
    const PRIORITY = 2;
    const RST_STREAM = 3;
    const SETTINGS = 4;
    const PUSH_PROMISE = 5;
    const PING = 6;
    const GOAWAY = 7;
    const WINDOW_UPDATE = 8;
    const CONTINUATION = 9;
}