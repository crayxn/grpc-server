<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Health;

use Crayxn\GrpcServer\Health\HealthCheckResponse\ServingStatus;
use Crayxn\GrpcServer\ServerContext as Context;
use Hyperf\ServiceGovernance\ServiceManager;

class ServerHealth implements HealthInterface
{

    public function __construct(private ServiceManager $serviceManager)
    {
    }

    public function Check(HealthCheckRequest $request): HealthCheckResponse
    {
        return (new HealthCheckResponse())->setStatus(isset($this->serviceManager->all()[$request->getService()]) ? ServingStatus::SERVING : ServingStatus::UNKNOWN);
    }

    public function Watch(Context $context, HealthCheckRequest $request): void
    {
        $status = isset($this->serviceManager->all()[$request->getService()]) ? ServingStatus::SERVING : ServingStatus::UNKNOWN;
        while (true === $context->getServer()->exist($context->getFd())) {
            $context->emit((new HealthCheckResponse())->setStatus($status));
            sleep(300);
        }
    }
}