<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

return [
    "server" => "grpc",
    "reflection" => [
        "enable" => (bool)\Hyperf\Support\env("GRPC_REFLECTION_ENABLE", true)
    ],
    "register" => [
        "enable" => \Hyperf\Support\env("GRPC_REGISTER_ENABLE", true),
        "driver" => \Hyperf\Support\env("GRPC_REGISTER_DRIVER", 'nacos-grpc'),
        "algorithm" => 'random',
        "service_name_func" => null // fn($name) => current(explode('.', $name)) . '.grpc'
    ],
    "discovery" => [
        "service_alias" => [], // service alias
        "service_suffix" => '', //like xxx.grpc
    ]
];