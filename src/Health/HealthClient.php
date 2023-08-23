<?php
# Generated by the protocol buffer compiler. DO NOT EDIT!
# source: src/Health/health.proto
# @see https://github.com/crayxn/grpc-server

namespace Crayxn\GrpcServer\Health;


use Crayxn\GrpcServer\Health\Request;

class HealthClient extends \Crayxn\GrpcClient\AbstractClient
{
	public string $service = "grpc.health.v1.Health";


	/**
     * @param HealthCheckRequest $argument
     * @param array $metadata
     * @param array $options
     * @return array
     */
    public function unary(Request $argument, array $metadata = [], array $options = []): array
    {
        return $this->_simpleRequest('/grpc.health.v1.Health/Check', $argument, [HealthCheckResponse::class, 'decode'], $metadata, $options);
    }



	/**
     * @param array $metadata
     * @param array $options
     * @return \Hyperf\GrpcClient\ServerStreamingCall
     */
    public function serverStreaming(array $metadata = [], array $options = []): \Hyperf\GrpcClient\ServerStreamingCall
    {
        return $this->_serverStreamRequest('/grpc.health.v1.Health/Watch', [HealthCheckResponse::class, 'decode'], $metadata, $options);
    }

}
