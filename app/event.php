<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        // 新建连接
        'swoole.websocket.Open' => [
            \app\listener\admin\WsConnect::class
        ],
        //关闭连接
        'swoole.websocket.Close' => [
            \app\listener\admin\WsClose::class
        ],
        //发送消息场景
        'swoole.websocket.Event' => [
            \app\listener\admin\WsMessage::class
        ],
        'swoole.task' => [
            \app\listener\admin\SwooleTask::class
        ],
    ],

    'subscribe' => [
    ],
];
