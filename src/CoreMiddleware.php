<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer;

use Google\Protobuf\Internal\Message as ProtobufMessage;
use \RuntimeException;
use Hyperf\Context\Context;
use Hyperf\Di\MethodDefinitionCollector;
use Hyperf\Di\ReflectionManager;
use function Hyperf\Support\value;

class CoreMiddleware extends \Hyperf\GrpcServer\CoreMiddleware
{
    protected function parseMethodParameters(string $controller, string $action, array $arguments): array
    {
        $injections = [];
        $definitions = MethodDefinitionCollector::getOrParse($controller, $action);

        /**
         * @var ServerContext $context
         */
        $context = Context::get(ServerContext::class);

        foreach ($definitions ?? [] as $definition) {
            if (! is_array($definition)) {
                throw new RuntimeException('Invalid method definition.');
            }
            if (! isset($definition['type']) || ! isset($definition['name'])) {
                $injections[] = null;
                continue;
            }
            $injections[] = value(function () use ($definition, $context) {
                switch ($definition['type']) {
                    case 'object':
                        $ref = $definition['ref'];
                        $class = ReflectionManager::reflectClass($ref);
                        $parentClass = $class->getParentClass();
                        if ($parentClass && $parentClass->getName() === ProtobufMessage::class) {
                            //todo eof
                            return $context->receive($ref);
                        }
                        //Add handler
                        if ($ref == ServerContext::class) {
                            return $context;
                        }

                        if (! $this->container->has($definition['ref']) && ! $definition['allowsNull']) {
                            throw new RuntimeException(sprintf('Argument %s invalid, object %s not found.', $definition['name'], $definition['ref']));
                        }

                        return $this->container->get($definition['ref']);
                    default:
                        throw new RuntimeException('Invalid method definition detected.');
                }
            });
        }

        return $injections;
    }
}