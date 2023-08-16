<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Channel;

use Swoole\Coroutine\Channel;

/**
 * @method pop(float $timeout = -1): mixed
 * @method push(mixed $data, float $timeout = -1): bool
 * @method close(): bool
 */
class ReqChannel
{
    private Channel $channel;

    public bool $active = true;

    public function __construct(public int $id, int $capacity = 10)
    {
        // create receive channel
        $this->channel = new Channel($capacity);
    }

    public function __call(string $name, array $arguments)
    {
        return $this->channel->{$name}(...$arguments);
    }
}