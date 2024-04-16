<?php


namespace app\model;

use think\facade\Cache;
use think\Model;

/**
 * App
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class App extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'app';
    protected $autoWriteTimestamp = 'datetime';

    //获取秘钥
    public static function getSaltByAppId($appId){
        $salt = Cache::get('Appid:'.$appId);
        if(empty($salt)){
            $salt = self::where('app_id',$appId)->value('app_secret');
            if($salt){
                Cache::set('Appid:'.$appId,$salt,3600);
            }
        }
        return $salt;
    }

}
