<?php


namespace app\service;

use think\facade\Log;

/**
 * BaseService
 * created on 2021/10/28 14:18
 * created by chengzhigang
 */
class BaseService
{
    protected function success($data=[],$msg='请求成功'){
        return ['code'=>200,'msg'=>$msg,'data'=>$data];
    }

    protected function error($msg='服务异常'){
        return ['code'=>500,'msg'=>$msg,'data'=>[]];
    }

    protected function code($code){
        return ['code'=>$code,'msg'=>'','data'=>[]];
    }

    protected function response($message,$code,$data=[]){
        $data = [
            'code' => $code,
            'message' => $message,
            'data' => empty($data)?new \stdClass():$data,
            'time' => date('Y-m-d H:i:s')
        ];
        return json($data,200);
    }

    protected function recordLog(\Exception $exception){
        Log::info('错误日志');
        $message = $exception->getMessage();
        $line = $exception->getLine();
        $file = $exception->getFile();
        Log::error('信息：'.$message);
        Log::error('行数：'.$line);
        Log::error('所在文件：'.$file);
        request()->error = ['message'=>$message,'line'=>$line,'file'=>$file];
    }

    /**
     * @desc 自动拉取
     * @method getPull
     * @param $request
     * @author chengzhigang
     * @time 2021/12/8 17:05
     */
    public function getPull($request){
        $param = $request->param();
        if (empty($param)) {
            return $this->response("参数缺失",400);
        }
        //验证密码,验证码云上配置的webhook密码
        $password = config('git.gitee.password');
        if (empty($param['password']) || $param['password'] != $password) {
            return $this->response("密码错误",400);
        }
        $path = config('git.path'); //项目存放物理路径
        //判断master分支上是否有提交
        if ($param['ref']=='refs/heads/master' && $param['total_commits_count']>0) {
            $res = shell_exec("cd {$path} && git pull --rebase origin master 2>&1");//当前为www用户
            $res_log = '------------------------->'.PHP_EOL;
            $res_log .= '用户'. $param['user_name'] . ' 于' . date('Y-m-d H:i:s') . '向' . $param['repository']['name'] . '项目的' . $param['ref'] . '分支push了' . $param['total_commits_count'] . '个commit：' . PHP_EOL;
            $res_log .= $res.PHP_EOL;
            $x = file_put_contents("git_webhook_log.txt", $res_log, FILE_APPEND);//追加写入日志文件
            if ($x) {
                return $this->response("拉取成功",200);
            } else {
                return $this->response("拉取失败",500);
            }
        }
        return $this->response("请求成功",200);
    }
}
