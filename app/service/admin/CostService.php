<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AdminLoginLog;
use app\model\Cost;
use app\model\AuthGroup;
use app\model\AuthGroupAccess;
use app\service\BaseService;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;

/**
 * CostService
 * created on 2021/11/2 17:55
 * created by chengzhigang
 */
class CostService extends BaseService
{

    /**
     * @desc 获取广告列表
     * @method getList
     * @param array $param
     * @author chengzhigang
     * @time 2021/11/2 17:56
     */
    public function getcost($param=[]){
        try{
            $where = [];
            if(isset($param['title'])){
                $where[] = ['title','like','%'.$param['title'].'%'];
            }
            if(isset($param['status'])&&$param['status']){
                $where[] = ['status','=',$param['status']];
            }
            $data = Cost::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 更改广告状态
     * @method changeStatus
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/3 10:54
     */
    public function changeStatus($param=[]){
        try{
            Cost::update(['status'=>$param['status']],['id'=>$param['id']]);
            return $this->success();
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 新增广告
     * @method addCost
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 17:04
     */
    public function addcost($param=[]){
        try{
            if(isset($param['id'])){
                unset($param['id']);
            }
            $res = Cost::create($param);
            if(!$res){
                throw new \Exception('新增广告失败');
            }
            return $this->success([],'新增成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取广告
     * @method getInfo
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 18:09
     */
    public function getinfo($param=[]){
        try{
            $info = Cost::where('id',$param['id'])->find();
            if(empty($info)){
                return $this->error('广告不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 编辑广告
     * @method editCost
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 17:04
     */
    public function editcost($param=[]){
        try{
            $admin = Cost::where('id',$param['id'])->find();
            if(empty($admin)){
                throw new \Exception('广告不存在');
            }
            $res = Cost::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('新增广告失败');
            }
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 删除广告
     * @method deleteCost
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/5 11:09
     */
    public function delcost($param=[]){
        try{
            //删除广告
            $res = Cost::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除广告失败');
            }
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

}
