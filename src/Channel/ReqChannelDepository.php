<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Channel;

class ReqChannelDepository
{
    /**
     * @var ReqChannel[] $cache
     */
    private array $cache = [];

    /**
     * @param string $id
     * @return ReqChannel
     */
    public function get(string $id, int $capacity = 3): ReqChannel
    {
        if (!isset($this->cache[$id])) {
            $this->cache[$id] = new ReqChannel($id, $capacity);
        }
        return $this->cache[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function remove(string $id): bool
    {
        if (isset($this->cache[$id])) {
            if ($this->cache[$id] instanceof ReqChannel && !$this->cache[$id]->close()) {
                //close channel fail
                return false;
            }
            unset($this->cache[$id]);
        }
        return true;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function active(string $id): bool
    {
        return isset($this->cache[$id]) ? $this->cache[$id]->active : false;
    }

    /**
     * @param string $id
     * @return void
     */
    public function down(string $id): void
    {
        if (isset($this->cache[$id])) {
            $channel = $this->cache[$id];
            //change status
            $channel->active = false;
            //set
            $this->cache[$id] = $channel;
        }
    }
}