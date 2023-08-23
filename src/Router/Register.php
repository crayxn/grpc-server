<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Router;

use Crayxn\GrpcServer\Event\RegisterEvent;
use Crayxn\GrpcServer\Health\ServerHealth;
use Crayxn\GrpcServer\Reflection\ServerReflection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\IPReaderInterface;
use Hyperf\HttpServer\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class Register
{
    public string $serverName = 'grpc';
    public array $services = [];
    public array $config = [];

    /**
     * @throws Throwable
     */
    public function __construct(private ContainerInterface $container)
    {
        $this->config = $this->container->get(ConfigInterface::class)->get('grpc', []);
        $this->serverName = $this->config['server'] ?? 'grpc';
    }

    /**
     * @param callable $callback function (Register $register)
     * @return void
     * @throws Throwable
     */
    public static function addServices(callable $callback): void
    {
        $self = ApplicationContext::getContainer()->get(self::class);
        Router::addServer($self->serverName, function () use ($callback, $self) {
            //register reflection
            if ($self->config['reflection']['enable'] !== false) $self->register(ServerReflection::class, true);
            //register health
            $self->register(ServerHealth::class, true);
            //other service register
            $callback($self);
        });
        //dispatch
        $self->dispatch();
    }

    public function register(string $class, bool $only_route = false): self
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            //todo no work
            return $this;
        }
        if ($reflectionClass->hasConstant('METADATA_CLASS')) {
            $reflectionClass->getConstant('METADATA_CLASS')::initOnce();
        }
        if ($reflectionClass->hasConstant('SERVICE_NAME')) {
            $serviceName = $reflectionClass->getConstant('SERVICE_NAME');
            !$only_route && !in_array($serviceName, $this->services) && $this->services[] = $serviceName;
            foreach ($reflectionClass->getMethods() as $method) {
                if ($method->isPublic()) {
                    Router::post(
                        "/{$serviceName}/{$method->getName()}", [$class, $method->getName()],
                        $reflectionClass->hasConstant('DEPENDENCY') ? ["dependency" => $reflectionClass->getConstant('DEPENDENCY')] : []
                    );
                }
            }
        }
        return $this;
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function dispatch(): void
    {
        $this->container->get(EventDispatcherInterface::class)->dispatch(new RegisterEvent($this->services, ...$this->getHostname($this->serverName)));
    }

    /**
     * @param string $serverName
     * @return array
     * @throws \Throwable
     */
    private function getHostname(string $serverName = 'grpc'): array
    {
        $result = ['127.0.0.1', 9501];
        $servers = $this->container->get(ConfigInterface::class)->get('server.servers', []);
        $ipReader = $this->container->get(IPReaderInterface::class);
        foreach ($servers as $server) {
            if (!isset($server['name'], $server['host'], $server['port'])) {
                continue;
            }
            if ($server['name'] == $serverName) {
                $host = $server['host'];
                if (in_array($host, ['0.0.0.0', 'localhost'])) {
                    $host = $ipReader->read();
                }
                if (!filter_var($host, FILTER_VALIDATE_IP)) {
                    throw new \Exception(sprintf('Invalid host %s', $host));
                }
                $port = $server['port'];
                if (!is_numeric($port) || ($port < 0 || $port > 65535)) {
                    throw new \Exception(sprintf('Invalid port %s', $port));
                }
                $result = [$host, (int)$port];
            }
        }
        if (empty($result)) {
            throw new \Exception(sprintf('Invalid serverName %s', $serverName));
        }
        return $result;
    }
}