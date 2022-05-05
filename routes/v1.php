<?php
/**
 * v1模块路由
 * 自定义rewrite规则
 * match为请求地址，route中为应用内路径,方便使用版本控制
 */
return [
    "test" => [
        "type" => "rewrite",
        "match" => IS_VERSION_ON?"/api/".ZK_VERSION."/test/index":"/api/test/index",
        "route" => [
            'module' => 'V1',
            'controller' => "Test",
            'action' => "index"
        ]
    ],

];
