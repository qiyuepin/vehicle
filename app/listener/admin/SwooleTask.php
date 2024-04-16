<?php
declare (strict_types=1);

namespace app\listener\admin;

use app\model\AdminChatData;

/**
 * SwooleTask
 * created on 2021/12/14 14:03
 * created by chengzhigang
 */
class SwooleTask
{
    public function handle($event){
        $data = $event->data;
        if(isset($data['scene'])&&$data['scene']){
            switch($data['scene']){
                case 'chat':
                    $this->chat($data['data']);
                    break;
                case 'session':
                    $this->chat($data['data']);
                    break;
            }
        }
    }

    protected function chat($data){
        AdminChatData::addChat($data['uid'],$data['touid'],$data['type'],$data['message'],$data['time']);
    }

}
