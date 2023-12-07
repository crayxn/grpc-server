<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer;

use Crayxn\GrpcServer\Health\ServerHealth;
use Crayxn\GrpcServer\Reflection\ServerReflection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Router\Router;
use Hyperf\ServiceGovernance\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ServiceCenter
{
    private ServiceManager $serviceManager;
    public array $config = [];

    /**
     * @throws Throwable
     */
    public function __construct(private ContainerInterface $container)
    {
        $this->config = $this->container->get(ConfigInterface::class)->get('grpc', []);
        $this->serviceManager = $this->container->get(ServiceManager::class);
    }

    /**
     * @param callable $callback function (Register $register)
     * @param string|null $serverName
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function addServices(callable $callback, ?string $serverName = null): void
    {
        $self = ApplicationContext::getContainer()->get(self::class);
        Router::addServer($serverName ?? $self->config['server'] ?? 'grpc', function () use ($callback, $self) {
            //register reflection
            if ($self->config['reflection']['enable'] !== false) $self->register(ServerReflection::class, true);
            //register health
            $self->register(ServerHealth::class, true);
            //other service register
            $callback($self);
        });
    }

    public function register(string $class, bool $only_route = false): self
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            $this->container->get(StdoutLoggerInterface::class)?->error("Can not reflection $class," . $e->getMessage());
            return $this;
        }
        if ($reflectionClass->hasConstant('METADATA_CLASS')) {
            $reflectionClass->getConstant('METADATA_CLASS')::initOnce();
        }
        if ($reflectionClass->hasConstant('SERVICE_NAME')) {
            $serviceName = $reflectionClass->getConstant('SERVICE_NAME');
            $publishTo = $reflectionClass->getConstant('PUBLISH_TO');
            //register router
            foreach ($reflectionClass->getMethods() as $method) {
                if ($method->isPublic()) {
                    Router::post(
                        "/{$serviceName}/{$method->getName()}", [$class, $method->getName()],
                        $reflectionClass->hasConstant('DEPENDENCY') ? ["dependency" => $reflectionClass->getConstant('DEPENDENCY')] : []
                    );
                }
            }
            //register governance
            if (!$only_route && ($this->config['register']['enable'] ?? true)) {
                //rename
                if (($this->config['register']['service_name_func'] ?? null) instanceof \Closure) {
                    $serviceName = $this->config['register']['service_name_func']($serviceName);
                }
                $this->serviceManager->register($serviceName, '', [
                    'protocol' => 'grpc',
                    'publishTo' => $publishTo ?: ($this->config['register']['driver'] ?? 'nacos'),
                    'server' => $this->config['server'] ?? 'grpc'
                ]);
            }
        }
        return $this;
    }
}