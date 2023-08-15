<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Exception;

use Hyperf\Grpc\StatusCode;

class GrpcServerException extends \Exception
{
    protected $code = StatusCode::ABORTED;
    protected $message = 'server error!';
}