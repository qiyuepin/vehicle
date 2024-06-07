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
use app\traits\TokenTrait;

/**
 * CostService
 * created on 2021/11/2 17:55
 * created by chengzhigang
 */
class CostService extends BaseService
{
    use TokenTrait;
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
            if(isset($param['driver_name'])&&$param['driver_name']){
                $where[] = ['driver_name','=',$param['driver_name']];
            }
            // dump($where);die;
            if(isset($param['platform'])&&$param['platform'] == "pc"){//电脑端
                $data = Db::name("admin_carplan_period")->where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                foreach($data['data'] as $key => $value ){
                    $data['data'][$key]['total'] = Cost::where('period_id_driver',$value['period_id_driver'])->sum('cost_money');
                }
            }
            else if(isset($param['platform'])&&$param['platform'] == "app"){//app端
                $data = Db::name("admin_carplan_period")->where($where)->order(['create_time'=>'desc'])
                ->select()->toArray();
                foreach($data as $key => $value ){
                    $data[$key]['total'] = Cost::where('period_id_driver',$value['period_id_driver'])->sum('cost_money');
                }
            }
            else if(isset($param['platform'])&&$param['platform'] == "excelall"){//导出excel
                $data = Db::name("admin_cost")->where($where)->order(['period_id_driver'=>'desc'])->select()->toArray();
                // foreach($data as $key => $value ){
                //     $data[$key]['total'] = Cost::where('period_id_driver',$value['period_id_driver'])->sum('cost_money');
                // }
            }
            // dump($data);die;

            // $data = Db::name("admin_carplan_period")->order(['create_time'=>'desc'])
            //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
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

    public function getcostlist($param=[]){
        try{
            // dump($param);
            $where = [];
            if(isset($param['driver_name'])){
                $where[] = ['driver_name','like','%'.$param['driver_name'].'%'];
            }
            if(isset($param['status'])&&$param['status']){
                $where[] = ['status','=',$param['status']];
            }
            if(isset($param['period_id'])&&$param['period_id']){
                $where[] = ['period_id','=',$param['period_id']];
            }
            if(isset($param['id'])&&$param['id']){
                $where[] = ['id','=',$param['id']];
            }
            if(isset($param['period_id_driver'])&&$param['period_id_driver']){
                $where[] = ['period_id_driver','=',$param['period_id_driver']];
            }
            if(isset($param['type_name'])&&$param['type_name']){
                $where[] = ['type_name','=',$param['type_name']];
            }
            // $sql = Db::name("admin_cost")->order(['create_time'=>'desc'])
            //     ->fetchsql()->select();
            // dump($where);
            if(isset($param['platform'])&&$param['platform'] == "pc"){
                $data = Db::name("admin_cost")->where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            }else if(isset($param['platform'])&&$param['platform'] == "app"){
                $data = Db::name("admin_cost")->where($where)->order(['create_time'=>'desc'])
                ->select()->toArray();
            }
            
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addcost($param=[],$Authorization){
        try{
            if(isset($param['id'])){
                unset($param['id']);
            }

            // $token = $this->getValue('664461343744a');
            $userid = $this->getValue($Authorization);
            
            // if(isset($param['cost_money']) && $param['cost_money']){
            //     $period_total = Db::name("admin_carplan_period")->where('period_id_driver',$param['period_id_driver'])->value('total');
            //     $period_total = $period_total + $param['cost_money'];
            //     Db::name("admin_carplan_period")->where('period_id_driver',$param['period_id_driver'])->update(['total' => $period_total]);
            // }
            $param['cost_creater'] = Admin::where('id',$userid)->value('username');
            // $param['cost_creater'] = $userInfo['username'];
            // dump($param);die;
            $res = Cost::create($param);
            if(!$res){
                throw new \Exception('新增失败');
            }
            return $this->success([],'新增成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getinfo($param=[]){
        try{
            
            if(isset($param['id'])){
                // dump('--id--');
                $info = Cost::where('id',$param['id'])->find();
                if(empty($info)){
                    return $this->error('不存在');
                }else{
                    $info = $info->toArray();
                }
            }
            else if(isset($param['period_id'])){
                // dump($param);die;
                $info = Db::name('admin_carplan_period')->where('id',$param['period_id'])->find();
            }
            // dump($info);die;
            if(empty($info)){
                return $this->error('不存在');
            }
            return $this->success($info);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getcosttype($param=[]){
        try{
            
            $type = Db::name('admin_cost_type')->select()->toarray();

            if(empty($type)){
                return $this->error('不存在');
            }
            return $this->success($type);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getperiod($param=[]){
        try{
            $driver = [];
            if(isset($param['driver_name'])){
                $driver['driver_name'] = $param['driver_name'];
            }
            
            $data = Db::name("admin_carplan_period")->where($driver)->order(['create_time'=>'desc'])->field('id,period_id_driver')->select()->toArray();
            return $this->success($data);
            // return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function editcost($param=[]){
        try{
            $admin = Cost::where('id',$param['id'])->find();
            if(empty($admin)){
                throw new \Exception('不存在');
            }
            $res = Cost::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('失败');
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