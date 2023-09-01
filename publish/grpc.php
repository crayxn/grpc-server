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
        "enable" => \Hyperf\Support\env("GRPC_REGISTER_ENABLE", false),
        "driver" => \Hyperf\Support\env("GRPC_REGISTER_DRIVER", 'nacos'),
        "algorithm" => 'random'
    ]
];