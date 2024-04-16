<?php
declare (strict_types = 1);

namespace app\listener\admin;

use app\service\admin\SwooleService;
use think\swoole\Websocket;

class WsClose extends SwooleService
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event,Websocket $ws)
    {
        //取消绑定
        $this->unBind($ws->getSender());
    }
}
