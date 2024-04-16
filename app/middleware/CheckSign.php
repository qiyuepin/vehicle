<?php
declare (strict_types = 1);

namespace app\middleware;
use app\model\App;
use app\traits\ResponseTrait;
use app\traits\SignTrait;

class CheckSign
{
    use ResponseTrait;
    use SignTrait;
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        if(config('signature.switch')){
            //白名单
            $route = substr($request->baseUrl(),1);
            // dump($route);
            // dump(config('signature.white_list'));
            if(!in_array($route,config('signature.white_list'))){
                $appId = $request -> header('X-Access-Appid');
                // dump($appId);
                if(empty($appId)){
                    return $this->fail(402);
                }
                $salt = App::getSaltByAppId($appId);
                if(empty($salt)){
                    return $this->fail(402);
                }
                $param = $request->param();//签名
                // dump($param);dump($salt);
                if(!$this->checkSign($param,$salt)){
                    return $this->fail(403);
                }
            }
        }
        return $next($request);
    }

}
