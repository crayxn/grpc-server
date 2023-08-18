<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

namespace Crayxn\GrpcServer\Frame;

use Amp\Http\HPack;
use Exception;

class Parser
{
    public function __construct(protected HPack $hPack)
    {
    }

    /**
     * @param string $frame_data
     * @param array|null $result
     * @return string
     * @throws Exception
     */
    public function unpack(string $frame_data, ?array &$result): string
    {
        // end
        if (strlen($frame_data) < 9) return $frame_data;
        // header
        $headers = unpack('Ctype/Cflags/NstreamId', substr($frame_data, 3, 6));
        // length
        $lengthPack = unpack('C3', substr($frame_data, 0, 3));
        $length = ($lengthPack[1] << 16) | ($lengthPack[2] << 8) | $lengthPack[3];
        // push
        if($length > (strlen($frame_data) - 9)) {
            return $frame_data;
        }
        $result[] = new Frame(substr($frame_data, 9, $length), $headers['type'], $headers['flags'], $headers['streamId'] & 0x7FFFFFFF);
        // continue
        if ('' != $next = substr($frame_data, $length + 9)) return $this->unpack($next, $result);
        return '';
    }

    /**
     * @param Frame $frame
     * @return string
     */
    public function pack(Frame $frame): string
    {
        return (substr(pack("NccN", $frame->length, $frame->type, $frame->flags, $frame->streamId), 1) . $frame->payload);
    }

    /**
     * @param Frame $frame
     * @return array|null
     */
    public function decodeHeaderFrame(Frame $frame): ?array
    {
        return $frame->type !== SWOOLE_HTTP2_TYPE_HEADERS ?: $this->hPack->decode($frame->payload, 4096);
    }

    /**
     * @param $headers
     * @param $streamId
     * @return Frame|null
     */
    public function encodeHeaderFrame($headers, $streamId): ?Frame
    {
        try {
            $compressedHeaders = $this->hPack->encode($headers);
        } catch (HPackException) {
            return null;
        }
        return new Frame($compressedHeaders, SWOOLE_HTTP2_TYPE_HEADERS, Flags::END_HEADERS, $streamId);
    }

    /**
     * @param string $data
     * @return string
     */
    public function exceptUpgrade(string $data): string
    {
        return str_contains($data, "PRI * HTTP/2.0\r\n\r\nSM\r\n\r\n") ? substr($data, 24) : $data;
    }

}