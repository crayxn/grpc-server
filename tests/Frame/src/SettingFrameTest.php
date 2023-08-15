<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace HyperfTest\GrpcServer\Frame\src;

use Crayxn\GrpcServer\Frame\src\SettingFrame;
use PHPUnit\Framework\TestCase;

class SettingFrameTest extends TestCase
{

    public function test_setting_frame_construct()
    {
        $frame = new SettingFrame();
        self::assertSame(hex2bin('00030000008000040000ffff000500004000'), $frame->payload);
    }
}
