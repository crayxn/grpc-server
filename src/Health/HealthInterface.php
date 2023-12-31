<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: src/Health/health.proto
# @see https://github.com/crayxn/grpc-server

namespace Crayxn\GrpcServer\Health;

use Crayxn\GrpcServer\ServerContext as Context;

interface HealthInterface
{
    // grpc service name
    public const SERVICE_NAME = "grpc.health.v1.Health";

	// grpc metadata class
	public const METADATA_CLASS = GPBMetadata\Health::class;

	// grpc dependency file
	public const DEPENDENCY = ["src/Health/health.proto"];

    /**
    * @param HealthCheckRequest $request
    * @return HealthCheckResponse
    */
    public function Check(HealthCheckRequest $request): HealthCheckResponse;



    /**
	* output HealthCheckResponse
    * @param Context $context
    * @param HealthCheckRequest $request
    * @return void
    */
    public function Watch(Context $context, HealthCheckRequest $request): void;

}
