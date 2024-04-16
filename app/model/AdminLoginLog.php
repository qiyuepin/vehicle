<?php


namespace app\model;

use think\Model;

/**
 * AdminLoginLog
 * created on 2021/11/9 14:56
 * created by chengzhigang
 */
class AdminLoginLog extends Model
{

    // 设置数据表（不含前缀）
    protected $name = 'admin_login_log';
    protected $autoWriteTimestamp = false;

    //新增登录日志
    public static function addLoginLog($uid,$login_time,$login_ip){
        return self::create(['uid'=>$uid,'login_time'=>$login_time,'login_ip'=>$login_ip]);
    }
}
