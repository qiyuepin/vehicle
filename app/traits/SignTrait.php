<?php
declare (strict_types = 1);

namespace app\traits;

/**
 * SignTrait
 * created on 2021/10/27 14:21
 * created by chengzhigang
 */
trait SignTrait
{

    /**
     * @desc 获取签名
     * @method makeSign
     * @param array $param
     * @param string $salt
     * @return string
     * @throws \Exception
     * @author chengzhigang
     * @time 2021/10/27 14:31
     */
    private function makeSign(array $param,string $salt){
        //签名步骤一：按字典序排序参数
        ksort($param);
        $string = paramsToString($param);
        //签名步骤二：在string后加入KEY
        $string = $string . "&salt=".$salt;
        //签名步骤三：MD5加密或者HMAC-SHA256
        if(config('signature.type') == "MD5"){
            $string = md5($string);
        }else if("SHA1") {
            $string = sha1($string);
        } else{
            throw new \Exception("签名类型不支持！");
        }
        //签名步骤四：所有字符转为小写
        $result = strtolower($string);
        // dump($result);
        return $result;
    }

    //验签
    public function checkSign(array $param,string $salt){
        
        $sign = $param['sign']??'';
        $timestamp = $param['timestamp']??'';

        if($sign&&$timestamp){
            //验证请求超时
            if($timestamp+config('signature.expire')<time()){
                return false;
            }
        
            //验证签名
            unset($param['sign']);
            $sign1 = $this->makeSign($param,$salt);
            if($sign==$sign1);{
                return true;
            }
        }
        return true;
    }

}
