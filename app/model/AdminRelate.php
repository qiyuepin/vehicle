<?php


namespace app\model;

use think\facade\Log;
use think\Model;

/**
 * AdminRelate
 * created on 2021/12/17 11:21
 * created by chengzhigang
 */
class AdminRelate extends Model
{
    protected $name = 'admin_relate';

    //新增好友
    public static function addFriend($uid,$touid,$origin=1,$remark=""){
        Log::info($uid);
        Log::info($touid);
        self::create([
            'uid' => $uid,
            'touid' => $touid,
            'origin' => $origin,
            'remark' => $remark
        ]);
        self::create([
            'uid' => $touid,
            'touid' => $uid,
            'origin' => $origin,
        ]);
    }
}
