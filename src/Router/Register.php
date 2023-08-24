<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Router;

use Crayxn\GrpcServer\Health\ServerHealth;
use Crayxn\GrpcServer\Reflection\ServerReflection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Router\Router;
use Hyperf\ServiceGovernance\ServiceManager;
use Psr\Container\ContainerInterface;
use Throwable;

class Register
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
     * @return void
     * @throws Throwable
     */
    public static function addServices(callable $callback): void
    {
        $self = ApplicationContext::getContainer()->get(self::class);
        Router::addServer($self->config['server'] ?? 'grpc', function () use ($callback, $self) {
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
            //todo no work
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