<?php
declare (strict_types = 1);

namespace app\middleware;
use app\traits\ResponseTrait;
use app\traits\TokenTrait;

/**
 * CheckToken
 * created on 2021/10/28 17:58
 * created by chengzhigang
 */
class CheckToken
{
    use ResponseTrait,TokenTrait;
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        try{
            $token = $request->header('Authorization');
            if(empty($token)){
                return $this->fail(405);
            }
            if(!$uid = $this->getValue($token)){
                return $this->fail(405);
            }
            $request -> uid = $uid ?? 0;
        }catch (\Exception $exception){
            return $this->fail(405);
        }
        return $next($request);
    }

}
