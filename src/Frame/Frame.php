<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame;

class Frame
{
    public int $length = 0;

    public function __construct(
        public string $payload,
        public int    $type,
        public int    $flags,
        public int    $streamId
    )
    {
        $this->length = strlen($this->payload);
    }
}