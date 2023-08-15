<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame\src;

use Amp\Http\HPack;
use Crayxn\GrpcServer\Frame\Flags;
use Crayxn\GrpcServer\Frame\Frame;
use Crayxn\GrpcServer\Frame\Parser;
use Hyperf\Context\ApplicationContext;
use Hyperf\Grpc\StatusCode;

class HeaderFrame extends Frame
{
    /**
     * @param int $streamId
     * @param bool $end
     * @param int $grpcStatus
     * @param string $grpcMessage
     * @throws \Throwable
     */
    public function __construct(int $streamId, bool $end = false, int $grpcStatus = StatusCode::OK, string $grpcMessage = 'OK')
    {
        $headers = [];
        if (!$end || $grpcStatus != StatusCode::OK) {
            $headers = [
                [':status', '200'],
                ['content-type', 'application/grpc'],
                ['trailer', 'grpc-status, grpc-message'],
                ... $end ? [
                    ['grpc-status', (string)$grpcStatus],
                    ['grpc-message', $grpcMessage]
                ] : []
            ];
        } else {
            $headers = [
                ['grpc-status', (string)$grpcStatus],
                ['grpc-message', $grpcMessage]
            ];
        }
        $hPack = ApplicationContext::getContainer()->get(HPack::class);
        $compressedHeaders = $hPack->encode($headers);
        parent::__construct($compressedHeaders, SWOOLE_HTTP2_TYPE_HEADERS, $end ? (Flags::END_HEADERS | Flags::END_STREAM) : Flags::END_HEADERS, $streamId);
    }
}