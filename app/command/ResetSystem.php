<?php
declare (strict_types = 1);

namespace app\command;
use app\model\Admin;
use app\model\FileUpload;
use app\service\RedisService;
use app\traits\EmailTrait;
use app\traits\UploadTrait;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Cache;
use think\facade\Console;

/**
 * ResetSystem
 * created on 2023/2/16 14:26
 * created by chengzhigang
 */
class ResetSystem extends Command
{
    use UploadTrait,EmailTrait;
    protected function configure()
    {
        $this->setName('reset')
            ->setDescription("重置系统");
    }

    //自动运行execute方法
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('ResetSystem Crontab job start...');
        /*** 这里写计划任务列表集 START ***/
        $this->deleteFiles();//删除文件
        Console::call('seed:run');
        $this->clearCache();//清空缓存
        $this->updatePass();//更新密码

        /*** 这里写计划任务列表集 END ***/
        $output->writeln('ResetSystem Crontab job end...');
    }

    protected function deleteFiles(){
        $files = FileUpload::select()->toArray();
        foreach($files as $file){
            $this->deleteFile($file);
        }
    }

    protected function clearCache(){
        Cache::clear();
        $connect = config('redis.connections')['default'];
        $prefix = $connect['prefix'] ?? config('redis.options')['prefix'];
        $redis = RedisService::getRedis();
        $keys = $redis->keys('*');
        if($keys){
            foreach($keys as $k => $key){
                $keys[$k] = substr($key, strlen($prefix));
            }
            $redis->del($keys);
        }
    }

    protected function updatePass(){
        $admin = Admin::where('id',1)->field(['id','halt'])->find();
        $originPassword = rand(100000,999999);
        $password = md5($admin['halt'].$originPassword.$admin['halt']);
        Admin::update(['password'=>$password],['id'=>$admin['id']]);
        //邮箱推送
        $this->send('1256699215@qq.com','ElementUIAdmin后台管理系统密码','密码：'.$originPassword);
    }
}
