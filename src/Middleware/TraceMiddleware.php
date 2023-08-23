<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Middleware;

use Crayxn\GrpcServer\Exception\GrpcServerException;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Grpc\StatusCode;
use Hyperf\HttpMessage\Server\Response;
use OpenTracing\Tracer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use const OpenTracing\Formats\TEXT_MAP;

class TraceMiddleware implements MiddlewareInterface
{
    private Tracer $tracer;

    /**
     * @param ContainerInterface $container
     * @throws Throwable
     */
    public function __construct(private ContainerInterface $container)
    {
        $this->tracer = $this->container->get(Tracer::class);
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws GrpcServerException
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $option = [];
        // add root
        if ($request->hasHeader("tracer.root")) {
            if ($rootSpan = $this->tracer->extract(TEXT_MAP, json_decode($request->getHeaderLine("tracer.root")))) {
                $option['child_of'] = $rootSpan;
            }
        }
        $path = $request->getUri()->getPath();
        $span = $this->tracer->startSpan("[gRPC] {$path}", $option);
        //add path tag
        $span->setTag('rpc.path', $path);
        //add header tag
        foreach ($request->getHeaders() as $key => $value) {
            $span->setTag('rpc.headers' . '.' . $key, implode(', ', $value));
        }
        try {
            /**
             * @var Response $response
             */
            $response = $handler->handle($request);
            $status = $response->getTrailer("grpc-status");
            if ($status != StatusCode::OK) {
                $span->setTag('error', true);
            }
            $span->setTag('rpc.status', $status);
            $span->setTag('rpc.message', $response->getTrailer("grpc-message"));
        } catch (Throwable $exception) {
            $span->setTag('error', true);
            if ($exception instanceof GrpcServerException) {
                $span->setTag('rpc.status', $exception->getCode());
                $span->setTag('rpc.message', $exception->getMessage());
            } else {
                //log error
                $span->log([
                    'error-code' => $exception->getCode(),
                    'error-message' => $exception->getMessage(),
                    'stacktrace' => $exception->getTraceAsString()
                ]);
            }
            throw $exception;
        } finally {
            //submit
            $span->finish();
            $this->tracer->flush();
        }
        return $response;
    }
}