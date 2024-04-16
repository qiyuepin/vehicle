<?php


namespace app\model;

use thans\jwt\facade\JWTAuth;
use think\Model;

/**
 * UserJwt
 * created on 2021/8/13 17:09
 * created by chengzhigang
 */
class AdminJwt extends Model
{
    protected $name = 'admin_jwt';
    protected $autoWriteTimestamp = false;

    public static function getUid($token){
        return self::where('token',$token)->value('uid');
    }

    //更新token
    public static function updateToken($uid,$token){
        $user_jwt = self::where('uid',$uid)->field('token')->find();
        if(empty($user_jwt)){
            self::create(['token'=>$token,'uid'=>$uid]);
        }else{
            if($user_jwt['token']){
                JWTAuth::invalidate($user_jwt['token']);
            }
            self::update(['token'=>$token],['uid'=>$uid]);
        }
    }
}
