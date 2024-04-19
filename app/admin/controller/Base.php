<?php


namespace app\admin\controller;

use app\BaseController;
use app\Request;
use app\service\QueueService;
use ric\captcha\facade\CaptchaApi;
use app\traits\UploadTrait;

/**
 * Base
 * created on 2021/7/12 13:34
 * created by chengzhigang
 */
class Base extends BaseController
{
    use UploadTrait;
    /**
     * @desc 获取验证码
     * @method getCaptcha
     * @return \think\Response
     * @author chengzhigang
     * @time 2021/10/26 16:33
     */
    public function getCaptcha(Request $request){
        $data = CaptchaApi::create();
        return $this->success($data);
    }

    public function uploadImage(Request $request){
        $image = $request -> file('image');
        
        if(empty($image)){
            return $this->error('图片不存在');
        }
        $data =  $this -> upload($image,'image');
        return $this->responseData($data);
    }

    public function readFile(Request $request){
        $url = $request -> param('url');
        // 创建cURL句柄
        $ch = curl_init();
        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        header("Content-type:application/pdf");
        header('Content-Disposition: inline; filename="sample.pdf"');
        //按照字节格式返回
        // 发送请求并获取响应
        $pdf = curl_exec($ch);
        // 关闭cURL句柄
        curl_close($ch);
        // 输出响应结果
        return response($pdf,200);
    }

    public function uploadFile(Request $request){
        $file = $request -> file('file');
        if(empty($file)){
            return $this->error('文件不存在');
        }
        $data =  $this -> upload($file,'doc');
        return $this->responseData($data);
    }

    public function queueInfo(Request $request,QueueService $service){
        $jobId = $request -> param('job');
        $data = $service->info($jobId);
        return $this->responseData($data);
    }

    public function queueRetry(Request $request,QueueService $service){
        $jobId = $request -> param('job');
        $data = $service->retry($jobId);
        return $this->responseData($data);
    }

    public function queueDelete(Request $request,QueueService $service){
        $jobId = $request -> param('job');
        $data = $service->delete($jobId);
        return $this->responseData($data);
    }

    /**
     * @desc json响应
     * @method responseData
     * @param $res
     * @param array $header
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 8:54
     */
    public function responseData($res,$header=[]){
        if($res['code']==200){
            return $this->success($res['data'],$res['msg'],$header);
        }elseif ($res['code']==500){
            return $this->error($res['msg']);
        }else{
            return $this->fail($res['code'],$res['msg']);
        }
    }
}
