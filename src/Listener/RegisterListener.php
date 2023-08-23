<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Listener;

use Crayxn\GrpcServer\Event\RegisterEvent;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\ServiceGovernance\DriverManager as RegisterDriverManager;
use Psr\Container\ContainerInterface;

class RegisterListener implements ListenerInterface
{
    private RegisterEvent $registerEvent;

    public function __construct(private ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            RegisterEvent::class,
            MainWorkerStart::class
        ];
    }

    /**
     * @param object $event
     * @return void
     * @throws \Throwable
     */
    public function process(object $event): void
    {
        var_dump("111111",$event);
        if ($event instanceof RegisterEvent) {
            $this->registerEvent = $event;
            return;
        }
        $event = $this->registerEvent;
        var_dump("222");
        $driverManager = $this->container->get(RegisterDriverManager::class);
        $config = $this->container->get(ConfigInterface::class)->get("grpc.register");
//            if (!($config['enable'] ?? true)) return;
        $driverName = $config["driver"] ?? "nacos";
        var_dump($driverName);
        if ($governance = $driverManager->get($driverName)) {
            var_dump($event->services);
            foreach ($event->services as $service) {
                if ($governance->isRegistered($service, $event->host, $event->port, ['protocol' => 'grpc'])) {
                    continue;
                }
                $governance->register($service, $event->host, $event->port, ['protocol' => 'grpc']);
            }
        }
    }
}