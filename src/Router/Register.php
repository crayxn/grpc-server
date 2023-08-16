<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Router;

use Crayxn\GrpcServer\Reflection\ServerReflection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Router\Router;

class Register
{
    /**
     * @param callable $callback function (Register $register)
     * @return void
     */
    public static function addServices(callable $callback): void
    {
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class)->get('grpc', []);
        Router::addServer($config['server'] ?? 'grpc', function () use ($callback, $config) {
            $register = new self();
            //register reflection
            if ($config['reflection']['enable'] !== false) $register->register(ServerReflection::class);
            //todo register wealth

            //other service register
            $callback($register);
        });
    }

    public function register(string $class): self
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
            foreach ($reflectionClass->getMethods() as $method) {
                if ($method->isPublic()) {
                    Router::post(
                        "/{$reflectionClass->getConstant('SERVICE_NAME')}/{$method->getName()}", [$class, $method->getName()],
                        $reflectionClass->hasConstant('DEPENDENCY') ? ["dependency" => $reflectionClass->getConstant('DEPENDENCY')] : []
                    );
                }
            }
        }
        return $this;
    }
}