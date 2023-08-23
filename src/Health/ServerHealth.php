<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Health;

use Crayxn\GrpcServer\Health\HealthCheckResponse\ServingStatus;
use Crayxn\GrpcServer\Router\Register;
use Crayxn\GrpcServer\ServerContext as Context;

class ServerHealth implements HealthInterface
{

    public function __construct(private Register $register)
    {
    }

    public function Check(HealthCheckRequest $request): HealthCheckResponse
    {
        return (new HealthCheckResponse())->setStatus(in_array($request->getService(), $this->register->services) ? ServingStatus::SERVING : ServingStatus::UNKNOWN);
    }

    public function Watch(Context $context, HealthCheckRequest $request): void
    {
        $status = in_array($request->getService(), $this->register->services) ? ServingStatus::SERVING : ServingStatus::UNKNOWN;
        while (true === $context->getServer()->exist($context->getFd())) {
            $context->emit((new HealthCheckResponse())->setStatus($status));
            sleep(300);
        }
    }
}