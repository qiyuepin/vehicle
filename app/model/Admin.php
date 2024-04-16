<?php


namespace app\model;

use think\facade\Cache;
use think\Model;

/**
 * Admin
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class Admin extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin';
    protected $autoWriteTimestamp = 'datetime';

    public function roles(){
        return $this->belongsToMany(AuthGroup::class, AuthGroupAccess::class,'group_id','uid');
    }

    public static function getBasicUser($uid,$touid){
        $data = Cache::get('admin-'.$uid.'-'.$touid);
        if(empty($data)){
            $data = self::alias('a')->join('admin_relate b','a.id=b.uid')->where('a.id',$uid)->where('b.touid',$touid)->field(['a.id','a.nickname','a.username','a.avatar','b.remark'])->find();
            if($data){
                $data = $data->toArray();
                Cache::set('admin-'.$uid.'-'.$touid,$data,86400);
            }
        }
        return $data;
    }

}
