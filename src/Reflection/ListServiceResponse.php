<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: reflection.proto

namespace Crayxn\GrpcServer\Reflection;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * A list of ServiceResponse sent by the server answering list_services request.
 *
 * Generated from protobuf message <code>grpc.reflection.v1alpha.ListServiceResponse</code>
 */
class ListServiceResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * The information of each service may be expanded in the future, so we use
     * ServiceResponse message to encapsulate it.
     *
     * Generated from protobuf field <code>repeated .grpc.reflection.v1alpha.ServiceResponse service = 1;</code>
     */
    private $service;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type array<\Crayxn\GrpcServer\Reflection\ServiceResponse>|\Google\Protobuf\Internal\RepeatedField $service
     *           The information of each service may be expanded in the future, so we use
     *           ServiceResponse message to encapsulate it.
     * }
     */
    public function __construct($data = NULL) {
        \Crayxn\GrpcServer\Reflection\GPBMetadata\Reflection::initOnce();
        parent::__construct($data);
    }

    /**
     * The information of each service may be expanded in the future, so we use
     * ServiceResponse message to encapsulate it.
     *
     * Generated from protobuf field <code>repeated .grpc.reflection.v1alpha.ServiceResponse service = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * The information of each service may be expanded in the future, so we use
     * ServiceResponse message to encapsulate it.
     *
     * Generated from protobuf field <code>repeated .grpc.reflection.v1alpha.ServiceResponse service = 1;</code>
     * @param array<\Crayxn\GrpcServer\Reflection\ServiceResponse>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setService($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Crayxn\GrpcServer\Reflection\ServiceResponse::class);
        $this->service = $arr;

        return $this;
    }

}
