<?php


namespace app\model;

use think\Model;

/**
 * ApplyFriend
 * created on 2021/12/17 11:22
 * created by chengzhigang
 */
class ApplyFriend extends Model
{
    protected $name = 'apply_friend';

    public static function addApply($uid,$touid,string $remark=""){
        return self::create([
            'uid' => $uid,
            'touid' => $touid,
            'remark' => $remark,
            'status' => 1
        ]);
    }

    public static function updateStatus($id,$status){
        return self::update(['status'=>$status],['id'=>$id]);
    }
}
