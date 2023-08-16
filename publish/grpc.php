<?php
declare(strict_types=1);
/**
 * @author   crayxn <https://github.com/crayxn>
 * @contact  crayxn@qq.com
 */

return [
    "server" => "grpc",
    "reflection" => [
        //是否开启服务反射 默认是true
        "enable" => (bool)\Hyperf\Support\env("REFLECTION_ENABLE", true)
    ]
];