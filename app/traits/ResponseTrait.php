<?php
declare (strict_types = 1);

namespace app\traits;

/**
 * ResponseTrait
 * created on 2021/10/26 14:30
 * created by chengzhigang
 */
trait ResponseTrait
{

    public $errorCode = [
        200 => '请求成功',
        400 => '验证失败',
        401 => '登录过期',
        402 => '未知应用',
        403 => '签名失败',
        404 => '未知请求',
        405 => 'token失效',
        500 => '服务异常',
    ];

    private function response(int $code,string $message='',array $data=[],array $header=[]){
        $reponseData = [
            'code' => $code,
            'message' => empty($message)?$this->getMessageByCode($code):$message,
            'data' => empty($data)?new \stdClass():$data,
            'time' => date('Y-m-d H:i:s')
        ];
        $header = array_merge(config('cors.header'),$header);
        return json($reponseData,200,$header);
    }

    private function getMessageByCode($code){
        return $this->errorCode[$code]??'服务异常';
    }

    public function success( $data=[], $message="请求成功", $header=[]){
        return $this->response(200,$message,$data,$header);
    }

    public function fail( $code,$message="", $data=[]){
        return $this->response($code,$message,$data);
    }

    public function error( $message="服务异常"){
        return $this->response(500,$message);
    }
}
