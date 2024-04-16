<?php
declare (strict_types = 1);

namespace app\listener\admin;

use app\model\App;
use app\service\admin\SwooleService;
use think\swoole\Websocket;

class WsConnect extends SwooleService
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event,Websocket $ws)
    {
        
        $this->checkSign($event,$ws);
    }

    private function checkSign($event,Websocket $ws){
        dump($ws);
        $appId = $event->param('X-Access-Appid');
        if(empty($appId)){
            $ws -> emit('open',$this->response('非法连接',false));
        }
        $salt = App::getSaltByAppId($appId);
        if(empty($salt)){
            $ws -> emit('open',$this->response('非法连接',false));
        }
    }

}
