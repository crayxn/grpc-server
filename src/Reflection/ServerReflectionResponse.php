<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: reflection.proto

namespace Crayxn\GrpcServer\Reflection;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The message sent by the server to answer ServerReflectionInfo method.
 *
 * Generated from protobuf message <code>grpc.reflection.v1alpha.ServerReflectionResponse</code>
 */
class ServerReflectionResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string valid_host = 1;</code>
     */
    protected $valid_host = '';
    /**
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ServerReflectionRequest original_request = 2;</code>
     */
    protected $original_request = null;
    protected $message_response;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $valid_host
     *     @type \Crayxn\GrpcServer\Reflection\ServerReflectionRequest $original_request
     *     @type \Crayxn\GrpcServer\Reflection\FileDescriptorResponse $file_descriptor_response
     *           This message is used to answer file_by_filename, file_containing_symbol,
     *           file_containing_extension requests with transitive dependencies. As
     *           the repeated label is not allowed in oneof fields, we use a
     *           FileDescriptorResponse message to encapsulate the repeated fields.
     *           The reflection service is allowed to avoid sending FileDescriptorProtos
     *           that were previously sent in response to earlier requests in the stream.
     *     @type \Crayxn\GrpcServer\Reflection\ExtensionNumberResponse $all_extension_numbers_response
     *           This message is used to answer all_extension_numbers_of_type requst.
     *     @type \Crayxn\GrpcServer\Reflection\ListServiceResponse $list_services_response
     *           This message is used to answer list_services request.
     *     @type \Crayxn\GrpcServer\Reflection\ErrorResponse $error_response
     *           This message is used when an error occurs.
     * }
     */
    public function __construct($data = NULL) {
        \Crayxn\GrpcServer\Reflection\GPBMetadata\Reflection::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string valid_host = 1;</code>
     * @return string
     */
    public function getValidHost()
    {
        return $this->valid_host;
    }

    /**
     * Generated from protobuf field <code>string valid_host = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setValidHost($var)
    {
        GPBUtil::checkString($var, True);
        $this->valid_host = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ServerReflectionRequest original_request = 2;</code>
     * @return \Crayxn\GrpcServer\Reflection\ServerReflectionRequest|null
     */
    public function getOriginalRequest()
    {
        return $this->original_request;
    }

    public function hasOriginalRequest()
    {
        return isset($this->original_request);
    }

    public function clearOriginalRequest()
    {
        unset($this->original_request);
    }

    /**
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ServerReflectionRequest original_request = 2;</code>
     * @param \Crayxn\GrpcServer\Reflection\ServerReflectionRequest $var
     * @return $this
     */
    public function setOriginalRequest($var)
    {
        GPBUtil::checkMessage($var, \Crayxn\GrpcServer\Reflection\ServerReflectionRequest::class);
        $this->original_request = $var;

        return $this;
    }

    /**
     * This message is used to answer file_by_filename, file_containing_symbol,
     * file_containing_extension requests with transitive dependencies. As
     * the repeated label is not allowed in oneof fields, we use a
     * FileDescriptorResponse message to encapsulate the repeated fields.
     * The reflection service is allowed to avoid sending FileDescriptorProtos
     * that were previously sent in response to earlier requests in the stream.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.FileDescriptorResponse file_descriptor_response = 4;</code>
     * @return \Crayxn\GrpcServer\Reflection\FileDescriptorResponse|null
     */
    public function getFileDescriptorResponse()
    {
        return $this->readOneof(4);
    }

    public function hasFileDescriptorResponse()
    {
        return $this->hasOneof(4);
    }

    /**
     * This message is used to answer file_by_filename, file_containing_symbol,
     * file_containing_extension requests with transitive dependencies. As
     * the repeated label is not allowed in oneof fields, we use a
     * FileDescriptorResponse message to encapsulate the repeated fields.
     * The reflection service is allowed to avoid sending FileDescriptorProtos
     * that were previously sent in response to earlier requests in the stream.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.FileDescriptorResponse file_descriptor_response = 4;</code>
     * @param \Crayxn\GrpcServer\Reflection\FileDescriptorResponse $var
     * @return $this
     */
    public function setFileDescriptorResponse($var)
    {
        GPBUtil::checkMessage($var, \Crayxn\GrpcServer\Reflection\FileDescriptorResponse::class);
        $this->writeOneof(4, $var);

        return $this;
    }

    /**
     * This message is used to answer all_extension_numbers_of_type requst.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ExtensionNumberResponse all_extension_numbers_response = 5;</code>
     * @return \Crayxn\GrpcServer\Reflection\ExtensionNumberResponse|null
     */
    public function getAllExtensionNumbersResponse()
    {
        return $this->readOneof(5);
    }

    public function hasAllExtensionNumbersResponse()
    {
        return $this->hasOneof(5);
    }

    /**
     * This message is used to answer all_extension_numbers_of_type requst.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ExtensionNumberResponse all_extension_numbers_response = 5;</code>
     * @param \Crayxn\GrpcServer\Reflection\ExtensionNumberResponse $var
     * @return $this
     */
    public function setAllExtensionNumbersResponse($var)
    {
        GPBUtil::checkMessage($var, \Crayxn\GrpcServer\Reflection\ExtensionNumberResponse::class);
        $this->writeOneof(5, $var);

        return $this;
    }

    /**
     * This message is used to answer list_services request.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ListServiceResponse list_services_response = 6;</code>
     * @return \Crayxn\GrpcServer\Reflection\ListServiceResponse|null
     */
    public function getListServicesResponse()
    {
        return $this->readOneof(6);
    }

    public function hasListServicesResponse()
    {
        return $this->hasOneof(6);
    }

    /**
     * This message is used to answer list_services request.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ListServiceResponse list_services_response = 6;</code>
     * @param \Crayxn\GrpcServer\Reflection\ListServiceResponse $var
     * @return $this
     */
    public function setListServicesResponse($var)
    {
        GPBUtil::checkMessage($var, \Crayxn\GrpcServer\Reflection\ListServiceResponse::class);
        $this->writeOneof(6, $var);

        return $this;
    }

    /**
     * This message is used when an error occurs.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ErrorResponse error_response = 7;</code>
     * @return \Crayxn\GrpcServer\Reflection\ErrorResponse|null
     */
    public function getErrorResponse()
    {
        return $this->readOneof(7);
    }

    public function hasErrorResponse()
    {
        return $this->hasOneof(7);
    }

    /**
     * This message is used when an error occurs.
     *
     * Generated from protobuf field <code>.grpc.reflection.v1alpha.ErrorResponse error_response = 7;</code>
     * @param \Crayxn\GrpcServer\Reflection\ErrorResponse $var
     * @return $this
     */
    public function setErrorResponse($var)
    {
        GPBUtil::checkMessage($var, \Crayxn\GrpcServer\Reflection\ErrorResponse::class);
        $this->writeOneof(7, $var);

        return $this;
    }

    /**
     * @return string
     */
    public function getMessageResponse()
    {
        return $this->whichOneof("message_response");
    }

}

