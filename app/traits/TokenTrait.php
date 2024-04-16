<?php
declare (strict_types = 1);

namespace app\traits;
use thans\jwt\facade\JWTAuth;
use think\facade\Cache;

/**
 * TokenTrait
 * created on 2021/10/27 14:21
 * created by chengzhigang
 */
trait TokenTrait
{

    /**
     * @desc 通过用户id获取新token
     * @method getToken
     * @param $uid
     * @return string
     * @author chengzhigang
     * @time 2021/11/2 16:09
     */
    public function getToken($uid){
        return self::builder(['id'=>$uid]);
    }

    private static function builder($data){
        $token = self::createToken();
        $freshToken = self::createfreshToken();
        Cache::set($token,$data,self::getTtl());
        Cache::set($freshToken,$data,self::getFreshTtl());
        return ['token'=>$token,'fresh_token'=>$freshToken,'expire'=>self::getTtl()];
    }

    public function getValue($token){
        if($token){
            $data = Cache::get($token,[]);
            if(empty($data)){
                return 0;
            }else{
                return $data['id']??0;
            }
        }else{
            return 0;
        }
    }

    public function getTokenByFreshToken($freshToken){
        return self::builderByFreshToken($freshToken);
    }

    public function clearToken($token){
        Cache::delete($token);
    }

    private static function builderByFreshToken($freshToken){
        $data = Cache::get($freshToken);
        if(empty($data)){
            return ['code'=>401,'msg'=>'重新登录','data'=>[]];
        }
        $token = self::createToken();
        Cache::set($token,$data,self::getTtl());
        Cache::set($freshToken,$data,self::getFreshTtl());
        return ['code'=>200,'msg'=>'请求成功','data'=>['token'=>$token,'expire'=>self::getTtl()]];
    }

    private static function getTtl():int{
        return 86400;
    }

    private static function getFreshTtl():int{
        return 30*86400;
    }

    private static function createToken(){
        return uniqid();
    }

    private static function createfreshToken(){
        return md5(uniqid());
    }

}
