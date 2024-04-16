<?php


namespace app\model;

use think\Model;

/**
 * AdminChatData
 * created on 2021/12/17 15:05
 * created by chengzhigang
 */
class AdminChatData extends Model
{
    protected $name = 'admin_chat_data';
    protected $autoWriteTimestamp = false;

    public static function addChat($uid,$touid,$type,$message,$time){
        self::create([
            'uid' => $uid,
            'touid' => $touid,
            'type' => $type,
            'message' => $message,
            'create_time' => $time,
            'update_time' => $time,
        ]);
    }
}
