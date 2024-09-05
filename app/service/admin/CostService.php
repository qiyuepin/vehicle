<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AdminLoginLog;
use app\model\Cost;
use app\model\Plan;
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
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|head_num','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status'] !=''){
                $where[] = ['status','=',$param['status']];
            }
            if(isset($param['driver_name'])&&$param['driver_name'] !=''){
                $where[] = ['driver_name','=',$param['driver_name']];
            }

            if(isset($param['date'])&&$param['date']){
                list($year, $month, $day) = explode('/',$param['date'][0]);
                list($year1, $month1, $day1) = explode('/',$param['date'][1]);

                $where[] = ['year','=',$year];
                $where[] = ['month','>=',$month];
                $where[] = ['month','<=',$month1];
            }
            // dump($where);die;
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
            else if(isset($param['platform'])&&$param['platform'] == "selectdriver"){//app端
                $data = Db::name("admin_carplan")->where($where)->where('driver_status',1)->find();
                
                if($data){
                    return $this->success(['data'=>$data]);
                }else{
                    // dump(88);die;
                    $data = Db::name("admin_carplan_period")->where($where)->where('status',1)->find();
                    // dump(88);die;
                    return $this->success(['data'=>$data]);
                }
            }
            else if(isset($param['platform'])&&$param['platform'] == "excelall"){//导出excel
                $group = Db::name("admin_carplan_period")->where($where)->group('period_id_driver')->column('period_id_driver');
                // dump($group);die;
                // $where[] = ['status','=',$param['status']];
                $data = Db::name("admin_cost")->where('period_id_driver','in',$group)->select()->toArray();
                // $period = '';
                // foreach($data as $key => $value ){
                //     if($value['period_id_driver'] != $period){
                //         $period = $value['period_id_driver'];
                //         $data[$key]['start_mile'] = Db::name("admin_carplan_period")->where('period_id_driver',$value['period_id_driver'])->value('start_mile');
                //         $data[$key]['end_mile'] = Db::name("admin_carplan_period")->where('period_id_driver',$value['period_id_driver'])->value('end_mile');
                //     }
                // }
                $newData = []; // 新数组用于存放处理后的数据

                foreach ($data as $key => $value) {
                    // 获取当前 period_id_driver 对应的 start_mile 和 end_mile
                    $start_mile = Db::name("admin_carplan_period")->where('period_id_driver', $value['period_id_driver'])->value('start_mile');
                    $end_mile = Db::name("admin_carplan_period")->where('period_id_driver', $value['period_id_driver'])->value('end_mile');

                    // 判断是否需要新增数组元素
                    if ($key === 0 || $value['period_id_driver'] !== $data[$key - 1]['period_id_driver']) {
                        // 如果是第一个元素或者当前 period_id_driver 不同于前一个元素的 period_id_driver，则新增数组元素
                        // $newValue = $value; // 复制原始数据到新数组元素
                        $newValue['period_id_driver'] = $value['period_id_driver'];
                        $newValue['start_mile'] = $start_mile; // 添加 start_mile
                        $newValue['end_mile'] = $end_mile; // 添加 end_mile
                        $newData[] = $newValue; // 将新元素添加到新数组中
                        $newData[] = $value;
                    } else {
                        // 如果当前 period_id_driver 与前一个元素的 period_id_driver 相同，直接将原始数据添加到新数组中
                        $newData[] = $value;
                    }
                }

                // 如果需要将处理后的数据重新赋值给原始数组 $data，可以使用以下语句
                $data = $newData;

                



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
            
            if(isset($param['id'])){//费用详情
                // dump('--id--');
                $info = Cost::where('id',$param['id'])->find();
                if(empty($info)){
                    return $this->error('不存在');
                }else{
                    $info = $info->toArray();
                }
            }
            else if(isset($param['period_id'])){//周期详情
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
            // 【YB分类整理】问题描述20240726-2 No.82 顺序调整 by baolei start
            // $data = Db::name("admin_carplan_period")->where($driver)->order(['create_time'=>'desc'])->field('id,period_id_driver,trailer_num,year')->select()->toArray();
            $data = Db::name("admin_carplan_period")->where($driver)->order(['create_time'=>'desc'])->field('id,period_id_driver,trailer_num,year,head_num')->select()->toArray();
            // 【YB分类整理】问题描述20240726-2 No.82 顺序调整 by baolei end
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
            
            if($param['type'] == 'delete'){
                $res = Cost::whereIn('id',$param['ids'])->delete();
                if(!$res){
                    throw new \Exception('删除失败');
                }
                return $this->success([],'删除成功');
            }elseif($param['type'] == 'finish'){
                $res = Db::name('admin_carplan_period')->whereIn('id',$param['ids'])->update(['status'=>2]);
                // dump($res);die;
                if(!$res){
                    throw new \Exception('修改失败');
                }
                return $this->success([],'修改成功');
            }
            
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

}
