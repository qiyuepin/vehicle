<?php


namespace app\model;

use think\Model;

/**
 * AdminActionLog
 * created on 2021/11/9 14:56
 * created by chengzhigang
 */
class AdminActionLog extends Model
{

    // 设置数据表（不含前缀）
    protected $name = 'admin_action_log';
    protected $autoWriteTimestamp = 'datetime';

    public function setParamAttr($value)
    {
        return json_encode($value);
    }

    public function setErrorAttr($value)
    {
        return json_encode($value);
    }

    public function getErrorAttr($value)
    {
        return json_decode($value,true);
    }

    //新增登录日志
    public static function addActionLog($uid,$route,$method,$param,$ip,$status=2,$error=[]){
        return self::create(['uid'=>$uid,'route'=>$route,'method'=>$method,'ip'=>$ip,'param'=>$param,'status'=>$status,'error'=>$error]);
    }
}
