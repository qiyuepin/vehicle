<?php


namespace app\service\admin;

use app\model\Admin;
use app\service\RedisService;

/**
 * SwooleService
 * created on 2021/12/9 13:42
 * created by chengzhigang
 */
class SwooleService
{
    public $redis;
    public function __construct()
    {
        $this -> redis = RedisService::getRedis();
    }

    protected function response(string $message = '发送成功',bool $status = true,array $data = []){
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    //绑定
    protected function bind($uid,$fd){
        //获取用户信息
        $user = Admin::where('id',$uid)->field(['id','nickname','username','avatar'])->find();
        $this -> redis -> set('uid:'.$uid,$fd);
        $this -> redis -> set('fd:'.$fd,json_encode($user->toArray()));
    }

    //解绑
    protected function unBind($fd){
        echo '断开fd：'.$fd . "\n";
        $user = $this->getSender($fd);
        $this -> redis -> del('fd:'.$fd);
        if($user){
            $this -> redis -> del('uid:'.$user['id']);
        }
    }

    //获取fd
    protected function getFd($uid){
        return $this -> redis -> get('uid:'.$uid);
    }

    //获取发送人信息
    protected function getSender($fd){
        $data = $this -> redis -> get('fd:'.$fd);
        if($data){
            return json_decode($data,true);
        }
        return [];
    }

    //获取会话
    public function getSessionList($uid){
        $data = $this -> redis -> get('session:'.$uid);
        if($data){
            return json_decode($data,true);
        }
        return ['count'=>0,'messages'=>[]];
    }

    //存储会话消息
    protected function saveSession($data,$sender){
        //接受方
        $receiverData = $this -> redis -> get('session:'.$data['to']);
        if($receiverData){
            $receiverData = json_decode($receiverData,true);
        }else{
            $receiverData = ['messages'=>[],'count'=>0];
        }
        $receiverData['count'] += 1;
        $messages = array_column($receiverData['messages'],Null,'id');
        $message = ['last_msg' => $data['message'],'type'=>$data['type'],'time'=>$data['time']];
        $message = array_merge($sender,$message);
        if(!isset($messages[$sender['id']])){
            $message['count'] = 1;
        }else{
            $message['count'] = $messages[$sender['id']]['count'] + 1;
            unset($messages[$sender['id']]);
        }
        $receiverCount = $message['count'];
        $receiverData['messages'] = array_values($messages);
        array_unshift($receiverData['messages'],$message);
        $this -> redis -> set('session:'.$data['to'],json_encode($receiverData));
        //发送方
        $senderData = $this -> redis -> get('session:'.$sender['id']);
        if($senderData){
            $senderData = json_decode($senderData,true);
        }else{
            $senderData = ['messages'=>[],'count'=>0];
        }
        $messages = array_column($senderData['messages'],Null,'id');
        $message = ['last_msg' => $data['message'],'type'=>$data['type'],'time'=>$data['time'],'count'=>0];
        $receiver = Admin::getBasicUser($data['to'],$sender['id']);
        $receiver['nickname'] = $receiver['remark'] ?? $receiver['nickname'];
        unset($receiver['remark']);
        $message = array_merge($receiver,$message);
        if(isset($messages[$data['to']])){
            unset($messages[$data['to']]);
        }
        $senderData['messages'] = array_values($messages);
        array_unshift($senderData['messages'],$message);
        $this -> redis -> set('session:'.$sender['id'],json_encode($senderData));
        return ['total'=>$receiverData['count'],'message_count'=>$receiverCount];
    }

    //删除会话提示
    protected function deleteSessionTips($uid,$touid){
        $data = $this -> getSessionList($uid);
        $messages = array_column($data['messages'],Null,'id');
        if(isset($messages[$touid])){
            $data['count'] -= $messages[$touid]['count'];
            $messages[$touid]['count'] = 0;
        }
        $data['messages'] = array_values($messages);
        $this -> redis -> set('session:'.$uid,json_encode($data));
    }
}
