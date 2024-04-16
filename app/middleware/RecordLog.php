<?php
declare (strict_types = 1);

namespace app\middleware;

use app\model\AdminActionLog;

/**
 * RecordLog
 * created on 2022/11/14 9:32
 * created by chengzhigang
 */
class RecordLog
{

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $method = $request->method();
        if($method!='GET'&&$request -> uid > 0){
            $route = $request->baseUrl();
            if(!isset($this->exceptRoute()[$route])&&$data = $response->getData()){
                if($data['code']==200||$data['code']==500){
                    $ip = $request->ip();
                    $method = strtolower($request->method());
                    $param = $request->param();
                    unset($param['sign']);
                    unset($param['timestamp']);
                    $error = $request->error ?? [];
                    AdminActionLog::addActionLog($request -> uid,$route,$method,$param,$ip,$data['code']==200?2:1,$error);
                }
            }
        }
        return $response;
    }

    private function exceptRoute(){
        return [
            '/admin/auth/login' => 0,
            '/admin/upload' => 1
        ];
    }

}
