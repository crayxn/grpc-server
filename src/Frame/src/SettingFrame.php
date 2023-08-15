<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame\src;

use Crayxn\GrpcServer\Frame\Flags;
use Crayxn\GrpcServer\Frame\Frame;

class SettingFrame extends Frame
{
    const MAX_CONCURRENT_STREAM = 3;
    const INITIAL_WINDOWS_SIZE = 4;
    const MAX_FRAME_SIZE = 5;

    public function __construct($max_concurrent_streams = 128, $initial_window_size = 65535, $max_frame_size = 16384)
    {
        $payload = pack('nNnNnN',
            self::MAX_CONCURRENT_STREAM, $max_concurrent_streams,
            self::INITIAL_WINDOWS_SIZE, $initial_window_size,
            self::MAX_FRAME_SIZE, $max_frame_size
        );
        parent::__construct($payload, SWOOLE_HTTP2_TYPE_SETTINGS, Flags::NONE, 0);
    }
}