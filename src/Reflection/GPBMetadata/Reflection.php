<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: reflection.proto

namespace Crayxn\GrpcServer\Reflection\GPBMetadata;

class Reflection
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�

reflection.protogrpc.reflection.v1alpha"�
ServerReflectionRequest
host (	
file_by_filename (	H  
file_containing_symbol (	H N
file_containing_extension (2).grpc.reflection.v1alpha.ExtensionRequestH \'
all_extension_numbers_of_type (	H 
list_services (	H B
message_request"E
ExtensionRequest
containing_type (	
extension_number ("�
ServerReflectionResponse

valid_host (	J
original_request (20.grpc.reflection.v1alpha.ServerReflectionRequestS
file_descriptor_response (2/.grpc.reflection.v1alpha.FileDescriptorResponseH Z
all_extension_numbers_response (20.grpc.reflection.v1alpha.ExtensionNumberResponseH N
list_services_response (2,.grpc.reflection.v1alpha.ListServiceResponseH @
error_response (2&.grpc.reflection.v1alpha.ErrorResponseH B
message_response"7
FileDescriptorResponse
file_descriptor_proto ("K
ExtensionNumberResponse
base_type_name (	
extension_number ("P
ListServiceResponse9
service (2(.grpc.reflection.v1alpha.ServiceResponse"
ServiceResponse
name (	":
ErrorResponse

error_code (
error_message (	2�
ServerReflection
ServerReflectionInfo0.grpc.reflection.v1alpha.ServerReflectionRequest1.grpc.reflection.v1alpha.ServerReflectionResponse(0BJ�Crayxn\\GrpcServer\\Reflection�(Crayxn\\GrpcServer\\Reflection\\GPBMetadatabproto3'
        , true);

        static::$is_initialized = true;
    }
}

