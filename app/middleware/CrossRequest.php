<?php


namespace app\middleware;


/**
 * CrossRequest
 * created on 2022/12/1 14:20
 * created by chengzhigang
 */
class CrossRequest
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
        return $next($request)->header(config('cors.header'));
    }
}
