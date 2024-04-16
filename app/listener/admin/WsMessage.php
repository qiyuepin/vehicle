<?php
declare (strict_types = 1);

namespace app\listener\admin;

use app\model\AdminJwt;
use app\service\admin\SwooleService;
use think\swoole\Websocket;
use think\swoole\Manager;

class WsMessage extends SwooleService
{
    public $ws;
    public $fd;
    public $server;
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event,Websocket $ws,Manager $manager)
    {
        $this -> ws = $ws;
        $this -> fd = $this -> ws -> getSender();
        $this -> server = $manager -> getServer();
        $this -> toFun($event);

    }

    private function toFun($event){
        switch ($event['type']){
            case 'login':
                $this->login($event['data'][0]);
                break;
            case 'chat':
                $this->chat($event['data'][0]);
                break;
            case 'tips':
                $this->tips($event['data'][0]);
                break;
            case 'room':
                $this->room($event['data'][0]);
                break;
            case 'close':
                $this->close();
                break;
        }
    }

    //登录
    public function login($data){
        $token = $data['token'];
        if(empty($token)||!$uid = AdminJwt::getUid($token)){
            $this -> ws -> emit('login',$this->response('登录过期',false));
        }
        $this->bind($uid,$this->fd);//双向绑定
        //获取会话列表
        $data = $this -> getSessionList($uid);
        $this -> ws -> emit('login',$this->response('登录成功',true,$data));

    }

    //单聊
    public function chat($data){
        if($data['to']){
            $toFd = $this -> getFd($data['to']);
            $sender = $this->getSender($this->fd);
            if($this->fd&&$sender){
                $time = date('Y-m-d H:i:s',strtotime($data['time']));
                $count = $this -> saveSession($data,$sender);
                if($toFd&&$this->server->isEstablished(intval($toFd))){
                    //在线模式
                    $this -> ws -> to($toFd) -> emit('chat',$this->response('发送成功',true,[
                        'sender' => $sender,
                        'touid' => $data['to'],
                        'type' => $data['type'],
                        'message' => $data['message'],
                        'count' => $count,
                        'time' => $time
                    ]));
                }else{
                    //离线消息
                    $this -> ws -> emit('chat',$this->response('发送成功',true,[
                        'sender' => $sender,
                        'touid' => $data['to'],
                        'type' => $data['type'],
                        'message' => $data['message'],
                        'count' => $count,
                        'time' => $time
                    ]));
                }
                $this->server->task(['scene'=>'chat','data'=>['uid'=>$sender['id'],'touid'=>$data['to'],'type'=>$data['type'],'message'=>$data['message'],'time'=>$time]]);
            }else{
                $this -> ws -> emit('login',$this->response('连接断开',false,$data));
            }
        }
    }

    //离线取消
    public function tips($data){
        $sender = $this->getSender($this->fd);
        if($sender&&$this->fd){
            $this -> deleteSessionTips($sender['id'],$data['to']);
        }
    }

    //群聊
    public function room($data){
        $this -> ws -> emit('room',$this->response('hello world'));
    }

    public function close(){
        $this -> ws -> disconnect();
    }
}
