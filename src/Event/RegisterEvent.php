<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Event;

class RegisterEvent
{
    public function __construct(public array $services, public string $host, public int $port)
    {
    }
}