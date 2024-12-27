<?php


namespace app\service\admin;

use app\model\Admin;

use app\service\BaseService;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use app\model\Plan;
use app\model\Plans;
//改修 No.9
use app\model\PlanHistory;
//改修 No.9
use app\model\Carhead;
use app\model\Cartrailer;
use app\model\Escort;
use app\model\Info;
use app\model\Factory;
use app\model\Cost;
use app\model\AuthorConfig;
use think\facade\Env;
// 导入对应产品模块的client
use TencentCloud\Sms\V20210111\SmsClient;
// 导入要请求接口对应的Request类
// use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Credential;
// 导入可选配置类
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use app\traits\TokenTrait;
use Carbon\Carbon;
use TencentCloud\Ic\V20190307\Models\CardInfo;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;

class PlanService extends BaseService
{
    use TokenTrait;

    public function homechart($param=[]){
        
        try{
            $currentYear = date('Y');
            //驾驶员、运输任务、装货任务、卸货任务数量
            $home['driver_count'] = Admin::where('type',2)->count();//驾驶员数量
            $home['plan_normal_count'] = Plan::where('plan_type', 0)->where('year', $currentYear)->count(); 
            $home['plan_load_count'] = Plan::where('plan_type', 1)->where('year', $currentYear)->count();
            $home['plan_unload_count'] = Plan::where('plan_type', 2)->where('year', $currentYear)->count();
            //运输任务、装货任务、卸货任务曲线图表line
            $plan_normal_data = [];
            $plan_load_data = [];
            $plan_unload_data = [];
            $cost_money = [];
            for ($i = 1; $i <= 12; $i++) {
                $plan_normal_data[$i] = Plan::where('plan_type', 0)
                                             ->where('month', $i)
                                             ->where('year', $currentYear)
                                             ->count(); // 每个月运输任务数量
                $plan_load_data[$i] = Plan::where('plan_type', 1)
                                           ->where('month', $i)
                                           ->where('year', $currentYear)
                                           ->count(); // 每个月装货任务数量
                $plan_unload_data[$i] = Plan::where('plan_type', 2)
                                             ->where('month', $i)
                                             ->where('year', $currentYear)
                                             ->count(); // 每个月卸货任务数量
                $cost_money[$i] = Cost::where('month', $i)->where('year', $currentYear)->count();//每个月费用统计
                
            }
            $home['plan_normal_data'] = array_values($plan_normal_data);
            $home['plan_load_data'] = array_values($plan_load_data);
            $home['plan_unload_data'] = array_values($plan_unload_data);

            //12个月费用统计
            $home['cost_money'] = array_values($cost_money);
            //第不同类别费用统计
            $types = Db::name('admin_cost_type')->select();
            $cost_type = [];
            $cost_name = [];
            foreach($types as $key => $value){
                $cost_type[$key] = Cost::where('type_name', $value['type_name'])->where('year', $currentYear)->sum('cost_money');
                $cost_name[$key] = $value['type_name'];
            }
            $home['cost_type']['name'] = $cost_name;
            $home['cost_type']['money'] = $cost_type;
            // foreach($types as $key => $value){
            //     // $type[]
            // }
            // $home['plan_normal_count'] = Plan::where('plan_type', 0)->where('year', $currentYear)->count(); 
            // $home['plan_load_count'] = Plan::where('plan_type', 1)->where('year', $currentYear)->count();
            // $home['plan_unload_count'] = Plan::where('plan_type', 2)->where('year', $currentYear)->count();

            return $this->success(['data'=>$home]);
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getplaninfo($param=[]){
        // dump('111');die;
        try{
            // $data = Info::where('status','in','0,5,9')->select();
            // $data = Info::where('driver_name','<>',null)->select();
            $data = Info::where('driver_name','<>',null)->select();
     
            foreach($data as $key => $value){
                $head = Info::carhead($value['head_id']);
                $trailer = Info::cartrailer($value['trailer_id']);
                $driver = Info::cardriver($value['driver_id']);
                $escort = Info::carescort($value['escort_id']);
                $trailer_status = Info::cartrailer($value['trailer_status']);
                // $info = $head.'-'.$trailer.'-'.$driver.'-'.$escort;
                $test = Info::cartrailer($value['trailer_status']);
                // dump($trailer['trailer_status']);die;
                // dump($head);die;
           
                $info = $head.'-'.$driver;
                $data[$key]['info'] = $info;
                $data[$key]['driver_name'] = $driver;
                $data[$key]['escort_name'] = $escort;
                $data[$key]['head_num'] = $head;
                $data[$key]['trailer_num'] = $trailer['trailer_plate'];
                $data[$key]['trailer_status'] = $trailer['trailer_status'];
                $period_id_now = Plan::where('driver_name',$value['driver_name'])->where('driver_status',1)->value('period_id');
                // $period_id = Plan::where('driver_name',$value['driver_name'])->where('driver_status',1)->where('start_periodic',1)->where('plan_type',0)->order(['id'=>'desc'])->value('period_id');
                $period_id = Db::name('admin_carplan_period')->where('driver_name',$value['driver_name'])->where('status',1)->value('period_id_driver');
                $data[$key]['period_id'] = $period_id_now?$period_id_now:($period_id?$period_id:null);
            }
            $factory = Factory::where('status',2)->select();
            $product_name = Factory::where('status',2)->select();
            return $this->success(['data'=>$data,'factory'=>$factory]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getnormal($param=[]){
   
        try{
            // $where['plan_type'] = 0;
            // $whereOr['plan_type'] = 0;
            $where[] = ['plan_type', '=', 0];
            $whereOr[] = ['plan_type', '=', 0];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|product_name|trailer_num|load_factory|unload_factory|head_num','like','%'.$param['keywords'].'%'];
                $whereOr[] = ['driver_name|product_name|trailer_num|load_factory|unload_factory|head_num','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['date'])&&$param['date']){
                $where[] = ['update_time','>',$param['date'][0]];
                $where[] = ['update_time','<',$param['date'][1]];
                $whereOr[] = ['update_time','>',$param['date'][0]];
                $whereOr[] = ['update_time','<',$param['date'][1]];
            }
            // dump($param);
 
            if(isset($param['start_periodic']) && $param['start_periodic'] != ''){
                $where[] = ['start_periodic', '=',$param['start_periodic']];


                $whereOr[] = ['start_periodic', '=',$param['start_periodic']];
            }
            // dump($param);
            // dump($where);
            // die;
            if(isset($param['status']) && $param['status'] != ''){

                if($param['status'] == 'null'){

                    $where[] = ['status', '=', null];
                    $where[] = ['driver_status', '=', 0];
                    $whereOr[] = ['status', '=', null];
                    $whereOr[] = ['driver_status', '=', 0];
                }elseif($param['status'] == 6){

                    $where[] = ['status', '=', null];
                    $where[] = ['driver_status', '=', 1];
                    $whereOr[] = ['status', '=', null];
                    $whereOr[] = ['driver_status', '=', 1];
                }elseif($param['status'] == 7){

                    $where[] = ['driver_status', '=', 3];
                    $whereOr[] = ['status', '=', 9];
                }elseif($param['status'] == 4){

                    $where[] = ['status', '=', 8];
                    $where[] = ['driver_status', '=', 1];
                    $whereOr[] = ['status', '=', 0];
                    $whereOr[] = ['driver_status', '=', 4];
                }elseif($param['status'] == 10){
                    $where[] = ['driver_status', '=', 2];
                    $whereOr[] = ['driver_status', '=', 2];

                }elseif($param['status'] < 6){
                    $where[] = ['status', '=', $param['status']];
                    $whereOr[] = ['status', '=', $param['status']];

                }

            }
            // dump($where);
            // die;
            if(isset($param['type']) && $param['type'] == "excel"){
                // $data = Plan::where(function ($query) use ($where) {
                //     foreach ($where as $condition) {
                //         $query->where($condition[0], $condition[1], $condition[2]);
                //     }
                // })
                // ->whereOr(function ($query) use ($whereOr) {
                //     foreach ($whereOr as $condition) {
                //         $query->where($condition[0], $condition[1], $condition[2]);
                //     }
                // })
                // ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                // ->select()->toArray();
                $data = Plan::whereIn('id',$param['ids'])
                ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                ->select()->toArray();
            }
            else{
        
                $data = Plan::where(function ($query) use ($where) {
                    foreach ($where as $condition) {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                })
                ->whereOr(function ($query) use ($whereOr) {
                    foreach ($whereOr as $condition) {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                })
                ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                
            }
            // $data = Plan::where($where)->order(['plan_order'=>'desc','id'=>'desc'])
            //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            // dump($data);
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getnormalInfo($param=[]){
        try{
            $plan = $param;
            
            $info = Plan::where('id',$plan['id'])->find();
            $info['trailer_material'] = Cartrailer::where('trailer_plate',$info['trailer_num'])->value('trailer_material');
            // dump($info['period_id']);die;
            if(isset($param['platform']) && $param['platform'] == "app"){
                $where['plan_id'] = $info['id'];
                $where['period_id_driver'] = $info['period_id'];
                $where['driver_name'] = $info['driver_name'];
                $where['load_factory'] = $info['load_factory'];
                $where['unload_factory'] = $info['unload_factory'];
                // $notice = Db::name("admin_car_notice")->where($where)->find();
                // dump($notice);die;
                // $notice = Db::name("admin_car_notice")->where($where)->update(['read' => 1]);
            }
            // $Plan_scope = explode(',', $info['Plan_scope']);
            // $info['Plan_scope'] = array_map('intval', $Plan_scope);
            // $driving_license = explode(',', $info['driving_license']);
            // $driving_licenses = []; // 确保 $driving_licenses 在循环之前被正确初始化
            // $info['driving_licenses'] = [];

            // $Plan_scope = Db::name("admin_carscope")->where('id','in', $info['Plan_scope'])->field('name')->select();
            // $items = $Plan_scope->toArray();
            // $itemNames = array_column($items, 'name');
            // $info['Plan_scope_name'] = implode(', ', $itemNames);

            // foreach ($driving_license as $key => $value) {
            //     $driving_licenses[$key]['name'] = $key;
            //     $driving_licenses[$key]['url'] = $value;
            // }

            // $info['driving_licenses'] = $driving_licenses;
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addnormal($param=[],$Authorization){
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        try{
            Db::startTrans();
            // $Plan = Plan::where('driver_name',$param['driver_name'])->where('period_id',$param['period_id'])->order(['id'=>'desc'])->find();
            // $Plan = Plan::where('driver_name',$param['driver_name'])->order(['sort'=>'desc'])->find();
            
            $Plan = Plan::where('driver_name', $param['driver_name'])
                    ->where('driver_status', 1)
                    // ->order('plan_order', 'desc')
                    ->find();
            // $trailer = Cartrailer::where('trailer_plate',$param['trailer_num'])->find();
            // if($trailer['trailer_status'] == 1 && $trailer['product_name'] != $param['product_name']){
            //     return $this->error('货品名称与挂车现有货品不同，请修改货品名称或者挂车信息');
            // }
            $currentYear = date('Y');
            $currentMonth = date('m');
            // dump($currentYear);
            // dump($currentMonth);
            $periodPlan = Plan::where('driver_name', $param['driver_name'])
                    ->where('driver_status', 0)
                    ->where('period_id', $param['period_id'])
                    ->order(['plan_order'=>'desc'])
                    ->find();
            $finishperiod = Db::name('admin_carplan_period')->where('period_id_driver',$param['period_id'])->find();
            if($finishperiod && $finishperiod['status'] == 2){
                // return $this->error(['msg' => '该周期已经结束，请重新填写周期']);
                return $this->error('该周期已经结束，请重新填写费用周期');
            }else if($finishperiod && $finishperiod['driver_name'] != $param['driver_name'] && $param['period_id'] != '' && $param['period_id'] != null){
                // return $this->error(['msg' => '该周期已经结束，请重新填写周期']);
                return $this->error('该周期不属于该驾驶员，请重新填写费用周期');
            }
            // $Plan正在进行中的任务
            $exitperiodPlan = Plan::where('period_id', $Plan['period_id'])->where('driver_status',0)->order(['plan_order'=>'desc'])->find();
            if($Plan && in_array($Plan['status'], [5, 8, 9]) && $Plan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!='' && !isset($periodPlan)){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1 && $Plan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!='' && !isset($periodPlan)){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && in_array($Plan['status'], [5, 8, 9]) && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1 && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan){
                $param['driver_status'] = 0;
            }
            else{
                $param['driver_status'] = 1;
            }
            // dump($param['driver_status']);die;
            $param['start_periodic'] = 0;
            $param['plan_type'] = 0;
            $param['month'] = $currentMonth;
            $param['year'] = $currentYear;
            // $param['plan_id'] = null;
            $userInfo = Cache::get('userinfo');
            // $param['plan_type'] = $userInfo['username'];
            // $param['dispatcher'] = $userInfo['username'];

            $param['fixed'] = 0;
            $userid = $this->getValue($Authorization);
            $param['initiator'] = Admin::where('id',$userid)->value('username');
            // 【YB分类整理】问题描述20240726-2 74 by baolei start
            $param['dispatcher'] = $param['initiator'];
            // 【YB分类整理】问题描述20240726-2 74 by baolei end
            if(isset($param['id'])){
                unset($param['id']);
            }
            
            $phone = Admin::where('username',$param['driver_name'])->value('phone');
            $res = Plan::create($param);
            if($param['driver_status'] == 1){
                // dump($param);die;
                $id = $res->id;
                $cid = Admin::where('username',$param['driver_name'])->value('user_cid');
                if($res->plan_type == 0){
                    $plantype = "运输任务";
                    $planmsg = "从".$res->load_factory."出发到".$res->unload_factory;
                }
                else if($res->plan_type == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$res->load_factory."出发装货";
                }
                else if($res->plan_type == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$res->unload_factory."卸货";
                }
                // dump($cid);dump($plantype);die;
                $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory'],'SMS_472080097',$time);
                $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                Db::commit();
                if($r['code'] == 0){
                    return $this->success(['msg' => '创建成功']);
                }
            }
            
            // dump($res);
            if(!$res){
                throw new \Exception('新增失败');
            }
     
            // $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory']);
            // $data = json_decode($sendmessage, true);
     
            // $code = $data['SendStatusSet'][0]['Code'];
            // // dump($sendmessage);die;
            // if(!$res){
            //     throw new \Exception('分配失败');
            // }
            // $msg = '分配成功,已发送给手机号为：'.$phone.'的驾驶员'.$param['driver_name'];
            // $appmsg = '已发送给'.$param['driver_name'];
       
            
            Db::commit();
            return $this->success(['msg' => '创建成功']);
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function editnormal($param=[],$Authorization){
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        try{
            Db::startTrans();
            $Plan = Plan::where('id',$param['id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }
 
            $Plan = Plan::where('driver_name',$param['driver_name'])->where('period_id',$param['period_id'])->order(['id'=>'desc'])->find();
            
            $currentYear = date('Y');
            $currentMonth = date('m');
            // dump($currentYear);
            // dump($currentMonth);
            if($Plan['driver_status'] > 1){
                $param['driver_status'] = 1;
            }
            else{
                $param['driver_status'] = 0;
            }
            $param['start_periodic'] = 0;
            $param['plan_type'] = 0;
            // $param['plan_id'] = null;
            $userInfo = Cache::get('userinfo');
            // $param['plan_type'] = $userInfo['username'];
            $param['dispatcher'] = $userInfo['username'];
            // unset($param['id']);
            $phone = Admin::where('username',$param['driver_name'])->value('phone');
            $res = Plan::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('编辑失败');
            }
     
            $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory'],'SMS_472080097',$time);
            $data = json_decode($sendmessage, true);
     
            $code = $data['SendStatusSet'][0]['Code'];
            // dump($sendmessage);die;
            if(!$res){
                throw new \Exception('分配失败');
            }
            $msg = '分配成功,已发送给手机号为：'.$phone.'的驾驶员'.$param['driver_name'];
            $appmsg = '已发送给'.$param['driver_name'];
            
            Db::commit();
            if($code == 'Ok'){
                return $this->success(['msg' => $msg,'appmsg' => $appmsg]);
            }else{
                return $this->success(['msg' => '分配成功,短信发送失败']);
            }


        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function delnormal($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $finishplan = Plan::whereIn('id',$param['ids'])->column('driver_status');
            if (in_array(2, $finishplan)) {
                // return $this->error([],'删除成功');
                return $this->error('已完成任务无法删除，请重新选择');
            } else {
                // dump($finishplan);die;
                // $res = Plan::whereIn('id',$param['ids'])->delete();
                $res = Plan::destroy($param['ids']);
                if(!$res){
                    throw new \Exception('删除失败');
                }
            }
            
            

            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function gettemporary($param=[]){
        
        try{
            // $where = [];
            // $where['plan_type'] = array('1','2');
            // $whereOr['plan_type'] = array('1','2');
            // $goods = array(1,2);
            // $where['plan_type']=array("in",$goods);
            // $whereOr['plan_type'] = array("in",$goods);
            // $where = [
            //     ['plan_type', 'IN', [1, 2]]
            // ];
            
            // $whereOr = [
            //     ['plan_type', 'IN', [1, 2]]
            // ];
            // $where = [];
            // $whereOr = [];
            $where[] = ['plan_type','in',[1,2]];
            $whereOr[] = ['plan_type','in',[1,2]];
            // dump($where);
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|product_name|trailer_num|load_factory|unload_factory|head_num','like','%'.$param['keywords'].'%'];
                $whereOr[] = ['driver_name|product_name|trailer_num|load_factory|unload_factory|head_num','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['date'])&&$param['date']){
                $where[] = ['update_time','>',$param['date'][0]];
                $where[] = ['update_time','<',$param['date'][1]];
                $whereOr[] = ['update_time','>',$param['date'][0]];
                $whereOr[] = ['update_time','<',$param['date'][1]];
            }
            // dump($where);die;
            if(isset($param['status']) && $param['status'] != ''){
                
                // if($param['status'] == 'null'){
                //     $where['status'] = null;
                //     $where['driver_status'] = 0;
                // }elseif($param['status'] == 6){
                //     $where['status'] = null;
                //     $where['driver_status'] = 1;
                // }elseif($param['status'] == 7){
                //     $where['status'] = null;
                //     $where['driver_status'] = 3;
                // }elseif($param['status'] == 8){
                //     $where['status'] = 8;
                //     $where['driver_status'] = 4;
                // }elseif($param['status'] < 6){
                //     $where['status'] = $param['status'];
                // }
                // dump($where);die;
                if($param['status'] == 'null'){
                    // $where['status'] = null;
                    // $where['driver_status'] = 0;
                    // $whereOr['status'] = null;
                    // $whereOr['driver_status'] = 0;
                    $where[] = ['status', '=', null];
                    $where[] = ['driver_status', '=', 0];
                    $whereOr[] = ['status', '=', null];
                    $whereOr[] = ['driver_status', '=', 0];
                }elseif($param['status'] == 6){
                    // $where['status'] = null;
                    // $where['driver_status'] = 1;
                    // $whereOr['status'] = null;
                    // $whereOr['driver_status'] = 1;
                    $where[] = ['status', '=', null];
                    $where[] = ['driver_status', '=', 1];
                    $whereOr[] = ['status', '=', null];
                    $whereOr[] = ['driver_status', '=', 1];
                }elseif($param['status'] == 7){
                    // $where['status'] = null;
                    // $where['driver_status'] = 3;
                    // $whereOr['status'] = 9;
                    $where[] = ['driver_status', '=', 3];
                    $whereOr[] = ['status', '=', 9];
                }elseif($param['status'] == 4){
                    // $where['status'] = 8;
                    // $where['driver_status'] = 1;
                    // $whereOr['status'] = 0;
                    // $whereOr['driver_status'] = 4;
                    $where[] = ['status', '=', 8];
                    $where[] = ['driver_status', '=', 1];
                    $whereOr[] = ['status', '=', 0];
                    $whereOr[] = ['driver_status', '=', 4];
                }elseif($param['status'] == 10){
                    $where[] = ['driver_status', '=', 2];
                    $whereOr[] = ['driver_status', '=', 2];
                    // $where['status'] = $param['status'];
                    // $whereOr['status'] = $param['status'];
                }elseif($param['status'] < 6){
                    $where[] = ['status', '=', $param['status']];
                    $whereOr[] = ['status', '=', $param['status']];
                    // $where['status'] = $param['status'];
                    // $whereOr['status'] = $param['status'];
                }
            }
            if(isset($param['type']) && $param['type'] == "excel"){
                // $data = Plan::where(function ($query) use ($where) {
                //     foreach ($where as $condition) {
                //         $query->where($condition[0], $condition[1], $condition[2]);
                //     }
                // })
                // ->whereOr(function ($query) use ($whereOr) {
                //     foreach ($whereOr as $condition) {
                //         $query->where($condition[0], $condition[1], $condition[2]);
                //     }
                // })
                // ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                // ->select()->toArray();
                $data = Plan::whereIn('id',$param['ids'])
                ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                ->select()->toArray();
            }
            else{
               
                // dump($where);
                $data = Plan::where(function ($query) use ($where) {
                    foreach ($where as $condition) {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                })
                ->whereOr(function ($query) use ($whereOr) {
                    foreach ($whereOr as $condition) {
                        $query->where($condition[0], $condition[1], $condition[2]);
                    }
                })
                ->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data);die;
            }
            // dump($param);die;
            // dump($whereOr);die;
                
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function gettemporaryInfo($param=[]){
        // dump($param);
        try{
            $info = Plan::where('id',$param['id'])->find();
            
            // $trailer_scope = explode(',', $info['trailer_scope']);
            // $info['trailer_scope'] = array_map('intval', $trailer_scope);
            // $driving_license = explode(',', $info['driving_license']);
            // $driving_licenses = []; // 确保 $driving_licenses 在循环之前被正确初始化
            // $info['driving_licenses'] = [];

            // $trailer_scope = Db::name("admin_carscope")->where('id','in', $info['trailer_scope'])->field('name')->select();
            // $items = $trailer_scope->toArray();
            // $itemNames = array_column($items, 'name');
            // $info['trailer_scope_name'] = implode(', ', $itemNames);

            // foreach ($driving_license as $key => $value) {
            //     $driving_licenses[$key]['name'] = $key;
            //     $driving_licenses[$key]['url'] = $value;
            // }

            // $info['driving_licenses'] = $driving_licenses;
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addriver_temporary($param=[],$Authorization){
    
        try{
            Db::startTrans();
            $userid = $this->getValue($Authorization);
            $param['driver_name'] = Admin::where('id',$userid)->value('username');
            if($param['plan_type'] == 2){
                $param['product_name'] = $param['product_name'];
                $param['load_product_quantity'] = $param['product_quantity'];
                $param['unload_factory'] = $param['load_factory'];
                $param['unload_address'] = $param['load_address'];
                $param['unload_wait_remark'] = $param['load_waiting_remark'];
                $param['unload_weight_inspection'] = $param['load_weight_inspection'];
                $param['load_product_quantity'] = null;
                $param['load_factory'] = null;
                $param['load_address'] = null;
                $param['load_waiting_remark'] = null;
                $param['load_weight_inspection'] = null;
            }
            // dump($param);die;
            $param['initiator'] = '驾驶员创建';
            $param['driver_status'] = 2;
            $res = Plan::create($param);
            // dump($res);die;
            if(!$res){
                throw new \Exception('新增失败');
            }
     
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function addtemporary($param=[],$Authorization){
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        // $this->SDKsendSms('18642646375', $param['driver_name'], $param['load_factory'], $param['unload_factory'],'SMS_472135128',$time);
        // die;
        try{
            Db::startTrans();

            $Plan = Plan::where('driver_name', $param['driver_name'])
                    // ->order('plan_order', 'desc')
                    ->where('driver_status', 1)
                    // ->order('id', 'desc')
                    ->find();
            
            $periodPlan = Plan::where('driver_name', $param['driver_name'])
                    ->where('driver_status', 0)
                    // ->where('period_id', $param['period_id'])
                    ->order(['plan_order'=>'desc'])
                    ->find();
            $currentYear = date('Y');
            $currentMonth = date('m');
            // dump($Plan['period_id']);
            // dump($periodPlan);die;
            if($Plan && in_array($Plan['status'], [5, 8, 9]) && $Plan['period_id'] == $param['period_id'] && !isset($periodPlan) && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1 && $Plan['period_id'] == $param['period_id'] && !isset($periodPlan) && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && in_array($Plan['status'], [5, 8, 9]) && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1 && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['period_id'] == $param['period_id'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan){
                $param['driver_status'] = 0;
            }
            else{
                $param['driver_status'] = 1;
            }


            $param['month'] = $currentMonth;
            $param['year'] = $currentYear;
            // $param['plan_id'] = null;
            $userInfo = Cache::get('userinfo');
            // $param['plan_type'] = $userInfo['username'];
            // $param['dispatcher'] = $userInfo['username'];

            $userid = $this->getValue($Authorization);
            $param['initiator'] = Admin::where('id',$userid)->value('username');
            // 【YB分类整理】问题描述20240726-2 68 by baolei start
            $param['dispatcher']=$param['initiator'];
            // 【YB分类整理】问题描述20240726-2 68 by baolei end
            // dump($param);die;
            if(isset($param['id'])){
                unset($param['id']);
            }
            
            $phone = Admin::where('username',$param['driver_name'])->value('phone');
            $res = Plan::create($param);
            if($param['driver_status'] == 1){
                // dump($param);die;
                $id = $res->id;
                $cid = Admin::where('username',$param['driver_name'])->value('user_cid');
                if($res->plan_type == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$res->load_factory."出发装货";
                    $SMSCODE = 'SMS_472135128';
                }
                else if($res->plan_type == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$res->unload_factory."卸货";
                    $SMSCODE = 'SMS_472095114';
                }
                $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory'],$SMSCODE,$time);
                $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                Db::commit();
                if($r['code'] == 0){
                    return $this->success(['msg' => '分配成功']);
                }
            }
            
            // dump($res);
            if(!$res){
                throw new \Exception('新增失败');
            }
     

            Db::commit();
            return $this->success(['msg' => '分配成功']);
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function edittemporary($param=[]){
        // dump($param);
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        try{
            Db::startTrans();

            $Plan = Plan::where('driver_name', $param['driver_name'])
                    ->where('driver_status', 1)
                    ->find();
            $currentYear = date('Y');
            $currentMonth = date('m');
            $finishperiod = Db::name('admin_carplan_period')->where('period_id_driver',$param['period_id'])->find();
            if($finishperiod['status'] == 2){
                // return $this->error(['msg' => '该周期已经结束，请重新填写周期']);
                return $this->error('该周期已经结束，请重新填写费用周期');
            }else if($finishperiod['driver_name'] != $param['driver_name'] && $param['period_id'] != '' && $param['period_id'] != null){
                // return $this->error(['msg' => '该周期已经结束，请重新填写周期']);
                return $this->error('该周期不属于该驾驶员，请重新填写费用周期');
            }
            $periodPlan = Plan::where('driver_name', $param['driver_name'])
                    ->where('driver_status', 0)
                    ->where('period_id', $param['period_id'])
                    ->order(['plan_order'=>'desc'])
                    ->find();
                    
            if($Plan && in_array($Plan['status'], [5, 8, 9])  && $Plan['period_id'] == $param['period_id'] && !isset($periodPlan) && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1  && $Plan['period_id'] == $param['period_id'] && !isset($periodPlan) && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            elseif($Plan && in_array($Plan['status'], [5, 8, 9])  && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['plan_order']<$param['plan_order'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan && $Plan['status'] == 3 && $Plan['plan_type'] == 1  && $Plan['period_id'] == $param['period_id'] && isset($periodPlan) && $periodPlan['plan_order']<$param['plan_order'] && $param['period_id']!=null && $param['period_id']!=''){
                $param['driver_status'] = 1;
                Plan::where('driver_name', $param['driver_name'])->where('driver_status', 1)->update(['driver_status'=>2]);
            }
            else if($Plan){
                $param['driver_status'] = 0;
            }
            else{
                $param['driver_status'] = 1;
            }

            // dump($param);die;
            $param['month'] = $currentMonth;
            $param['year'] = $currentYear;

            // if(isset($param['id'])){
            //     unset($param['id']);
            // }
            
            $phone = Admin::where('username',$param['driver_name'])->value('phone');
            // $res = Plan::create($param);
            $exitPlan = Plan::where('id',$param['id'])->find();
            
            if(empty($exitPlan)){
                throw new \Exception('信息不存在');
            }
 
            $res = Plan::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('编辑失败');
            }
            if($param['driver_status'] == 1){
                // dump($param);die;
                $id = $res->id;
                $cid = Admin::where('username',$param['driver_name'])->value('user_cid');
                if($res->plan_type == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$res->load_factory."出发装货";
                    $SMSCODE = 'SMS_472135128';
                }
                else if($res->plan_type == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$res->unload_factory."卸货";
                    $SMSCODE = 'SMS_472095114';
                }
                else if($res->plan_type == 0){
                    $plantype = "运输任务";
                    $planmsg = "从".$res->load_factory."出发到".$res->unload_factory;
                    $SMSCODE = 'SMS_472080097';
                }
                $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory'],$SMSCODE,$time);
                $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                Db::commit();
                if($r['code'] == 0){
                    return $this->success(['msg' => '分配成功']);
                }
            }


            
           
      
            Db::commit();
            // return $this->success([],'编辑成功');
            return $this->success(['msg' => '编辑成功']);
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }


  
    public function deltemporary($param=[]){
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            
            $id = Plan::where('id',$param['id'])->find();
            
            $Planing = Plan::where('driver_name',$id['driver_name'])->where('driver_status',1)->find();
            if($Planing['period_id'] != null){
                $Plan = Plan::where('driver_name',$id['driver_name'])->where('period_id',$Planing['period_id'])->where('driver_status',0)->order(['plan_type'=>'desc'])->find();
            }else{
                $Plan = Plan::where('driver_name',$id['driver_name'])->where('start_periodic',0)->where('driver_status',0)->order(['plan_type'=>'desc'])->find();
            }
            // dump($Planing);die;
            if(isset($Plan)){
                $driver_status['driver_status'] = 3;
                $new['driver_status'] = 1;
                // Plan::where('id',$Plan['id'])->update($driver_status);
                Plan::update($new,['id'=>$Plan['id']]);
                $phone = Admin::where('username',$id['driver_name'])->value('phone');
                $cid = Admin::where('username',$id['driver_name'])->value('user_cid');
                if($Plan->plan_type == 0){
                    $plantype = "常规任务";
                    $planmsg = "从".$Plan->load_factory."出发到".$Plan->unload_factory;
                    $SMSCODE = 'SMS_472080097';
                }
                else if($Plan->plan_type == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$Plan->load_factory."出发装货";
                    $SMSCODE = 'SMS_472095114';
                }
                else if($Plan->plan_type == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$Plan->unload_factory."卸货";
                    $SMSCODE = 'SMS_472135128';
                }
                // dump($Plan['id']);
                // dump($cid);
                // dump($Plan['load_factory']);
                // die;
                // $this->SDKsendSms($phone, $id['driver_name'], $Plan['load_factory'], $Plan['unload_factory']);
                $this->SDKsendSms($phone, $id['driver_name'], $Plan['load_factory'], $Plan['unload_factory'],$SMSCODE,$time);
                $r = $this->pushToSingleByCids($Plan['id'],$cid,$plantype,$planmsg);
                Db::commit();
                $res = Plan::update($driver_status,['id'=>$param['id']]);
                if($r['code'] == 0){
                    return $this->success(['msg' => '成功']);
                }
            }
            else{
                // $driver_status['driver_status'] = 1;
                $driver_status['status'] = 9;
            }
            // dump($Plan);die;
            // Carhead::where('carhead_plate',$id['head_num'])->update(['head_status'=>3]);
            Carhead::where('carhead_plate',$id['head_num'])->update(['head_status'=>3]);
            $res = Plan::update($driver_status,['id'=>$param['id']]);
            // $res = Plan::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('失败');
            }

            Db::commit();
            return $this->success([],'成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getplans($param=[]){
   
        try{
            $where = [];
            
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['load_factory|unload_factory|product_name','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status'] != ''){
                $where['status'] = $param['status'];
            }
            // if(isset($param['plan_type'])&&$param['plan_type'] != ''){
            //     $where['plan_type'] = $param['plan_type'];
            // }
            // dump($param);
            if(isset($param['dist'])&&$param['dist']){
                if($param['dist'] == 1){
                    $data = Plans::where($where)->order(['create_time'=>'desc'])->select()->toArray();
                }else{

                    $data = Plan::where($where)->order(['driver_status' => 'asc', 'plan_order' => 'desc','id'=>'desc'])->select()->toArray();

                }
            }else{
                if(isset($param['type']) && $param['type'] == "excel"){
                    $data = Plans::whereIn('id',$param['ids'])->order(['create_time'=>'desc'])
                    ->select()->toArray();
                }
                else{
                    $data = Plans::where($where)->order(['create_time'=>'desc'])
                    ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                }
                // $data = Plans::where($where)->order(['create_time'=>'desc'])
                //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            }
            
            // dump($data);
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getplansinfo($param=[]){
        try{
            $info = Plans::where('id',$param['id'])->find();
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addplan($param=[],$Authorization){
        
        try{
            Db::startTrans();
            $Plan = Plans::where('start_periodic',1)->order(['id'=>'desc'])->find();
            // $author = Cache::get('author');
            // // $initiator = AuthorConfig::where('id',1)->find();dump($data);die;
            // $initiator = $author['name'];
            $userid = $this->getValue($Authorization);
            $initiator = Admin::where('id',$userid)->value('username');
            $plansbefore = Plans::where('start_periodic',$param['start_periodic'])->where('status',0)->where('product_name',$param['product_name'])->where('load_factory',$param['load_factory'])->where('unload_factory',$param['unload_factory'])->count();
            
            if($plansbefore>1){
                return $this->error('已有两条相同，未完成的运输计划');
            }
            $currentYear = date('Y');
            $currentMonth = date('m');
            $month_id = Plans::where('start_periodic',1)->where('month',$currentMonth)->order(['id'=>'desc'])->find();
            // dump($month_id);die;
            $param['status'] = 0;
            if($param['start_periodic'] == '1' && isset($Plan)){
                $param['periodic_times'] = $Plan['periodic_times'] + 1;
            }
            else if($param['start_periodic'] == '0' && isset($Plan)){
                $param['periodic_times'] = $Plan['periodic_times'];
            }
            else if($param['start_periodic'] == '1' && !isset($Plan)){
                $param['periodic_times'] = 1;
            }
            else{
                $param['periodic_times'] = 0;
            }
            // dump($param);die;
            if($param['start_periodic'] == '1' && $param['plan_type'] == 0){
                // if(empty($month_id)){
                //     $max = 1;
                // }else{
                //     $max = Plans::where('start_periodic',1)->where('month',$currentMonth)->count();
                //     $max= $max+1;
                // }
                // if($max<10){
                //     $id = '00'.$max;
                // }
                // else if($max>=10){
                //     $id = '0'.$max;
                // }
                // else if($max>=100){
                //     $id = $max;
                // }

                // $period['period_id'] = $currentYear.'-'.$currentMonth.'-'.$id;
                $period['year']= $currentYear;
                $period['month']= $currentMonth;
                $period['initiator']= $initiator;
 
            }
            else if($param['plan_type'] == 0){
                // $period['period_id'] = $Plan['period_id'];
            }
            else if($param['plan_type'] != 0){
                // $period['period_id'] = null;
            }
            $period_id = Plan::where('driver_status',1)->order(['id'=>'desc'])->find();
            $param['platform'] = 'pc';
            // $param['period_id'] = $period_id['period_id'];
            $param['year']= $currentYear;
            $param['month']= $currentMonth;
            $param['initiator']= $initiator;
            // dump($param);die;
            $res = Plans::create($param);
            // dump($res);
            if(!$res){
                throw new \Exception('新增失败');
            }
     
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    //修改 No.10
    public function addhisplan($param=[],$Authorization){

        try {
            Db::startTrans();

            $userid = $this->getValue($Authorization);
            $initiator = Admin::where('id',$userid)->value('username');

            $res = PlanHistory::create($param);
            // dump($res);
            if(!$res){
                throw new \Exception('新增失败');
            }

            Db::commit();
            return $this->success([],'新增成功');


        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getPlanCount($param=[]){
        try{
            $cnt = Plans::where('status',0)
                ->where('product_name',$param['product_name'])
                ->where('product_quantity',$param['product_quantity'])
                ->where('load_factory',$param['load_factory'])
                ->where('load_address',$param['load_address'])
                ->where('unload_factory',$param['unload_factory'])
                ->where('unload_address',$param['unload_address'])
                ->count();

            $returnArr[]=$cnt;

            return $this->success($returnArr);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    //修改 No.10
    public function editplan($param=[],$Authorization){
        try{
            Db::startTrans();
            // dump($param);die;
            $Plan = Plans::where('id',$param['id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }

            $res = Plans::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('编辑失败');
            }
           
      
            Db::commit();
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function distplan($param=[],$Authorization){
        $currentHour = date('H');
        // $currentHour = 4;
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        // $this->SDKsendSms('18642646375', $param['driver_name'], $param['load_factory'], $param['unload_factory'],'SMS_472135128',$time);
        try{
            Db::startTrans();
            $where['driver_name']=$param['driver_name'];
            $Plan = Plan::where($where)->where('driver_status','<',2)->order(['plan_order'=>'desc'])->find();//上一条进行的任务
            // $plans = Plans::where('id',$param['id'])->get()->toArray();
            $periodPlan = Plan::where($where)->where('period_id',$param['period_id'].'-'.$param['driver_name'])->where('start_periodic',1)->where('plan_type',0)->order(['plan_order'=>'desc'])->find();
             //dump($param['product_quantity']);
            $plan0 = Plans::find($param['id']);
            $plans = $plan0 ? $plan0->toArray() : null;
            // $trailer = Cartrailer::where('trailer_plate',$param['trailer_num'])->find();
            // if($trailer['trailer_status'] == 1 && $trailer['product_name'] != $param['product_name']){
            //     return $this->error('货品名称与挂车现有货品不同，请修改货品名称或者挂车信息');
            // }
            
            if(!empty($periodPlan)){
                $plans['escort_name'] = $periodPlan['escort_name'];
                $plans['head_num'] = $periodPlan['head_num'];
            }
            $exitPlan = Plan::where($where)->where('driver_status',1)->find();
            
            
            // $Plan = Plan::where($where)->order(['id'=>'desc'])->find();
            
            // dump($plans);die;
            // $plans = $param;
            unset($plans['id']);
            unset($plans['create_time']);
            unset($plans['status']);
            $plans['product_quantity'] = $param['product_quantity'];
            $plans['driver_name'] = $param['driver_name'];
            $plans['trailer_num'] = $param['trailer_num'];
            $plans['head_num'] = $param['head_num'];
            $plans['escort_name'] = $param['escort_name'];
            $plans['plans_id'] = $param['id'];
            $plans['info_id'] = isset($param['info_id'])?$param['info_id']:'';
            $plans['trailer_status'] = $periodPlan['trailer_status'];
            $phone = Admin::where('username',$param['driver_name'])->value('phone');
            
            // dump($phone);
            $userid = $this->getValue($Authorization);
            // $initiator = Admin::where('id',$userid)->value('username');
            $plans['dispatcher'] = Admin::where('id',$userid)->value('username');
           
            // $period['period_id'] = $plans['period_id'];
            // $period_id = $plans['period_id'];
            if($param['plan_type'] == 0){
                $period_id_now = Plan::where('driver_name',$param['driver_name'])->where('driver_status',1)->value('period_id');
                $period_id_exit= Db::name('admin_carplan_period')->where('driver_name',$param['driver_name'])->where('status',1)->value('period_id_driver');
                $period_id = $period_id_now?$period_id_now:($period_id_exit?$period_id_exit:'');
            }else{
                $period_id= null;
            }
            // dump($period_id);die;
            $currentYear = (int)date('Y');
            $currentMonth = (int)date('m');
            if($param['start_periodic'] == 1){

                $countperiod = Plan::where('year',$currentYear)->where('month',$currentMonth)->count();
                $maxperiod = Plan::where('year',$currentYear)->where('month',date('m'))->order('period_id', 'desc')->value('period_id');
                // dump($maxperiod);die;
                if($maxperiod == null){
                    $period_id = date('Y').'-'.date('m').'-001';
                }
                else{
                    $lastmaxperiod = intval(substr($maxperiod, -3)) +1;
                    
                    if($lastmaxperiod<10){
                        $period_num = '00'.$lastmaxperiod;
                    }
                    elseif($lastmaxperiod>=10 && $lastmaxperiod<100){
                        $period_num = '0'.$lastmaxperiod;
                    }
                    $period_id = date('Y').'-'.date('m').'-'.$period_num;
                }
            }

            $exitperiodPlan = Plan::where($where)->where('period_id',$period_id)->where('driver_status',0)->order(['plan_order'=>'desc'])->find();
            // $exitPlan当前进行中的任务  $exitperiodPlan是待接单列表里面同周期任务
            if(!isset($exitperiodPlan) && !isset($exitPlan)){//如果不存在同周期任务，则现在的任务直接变为进行中
                $plans['driver_status'] = 1;
                
            }
            else if(isset($exitPlan) && $exitPlan['driver_status'] < 2){
                // dump($Plan);
                if($exitPlan['driver_status'] == 1 && $exitPlan['status'] == 5 && $param['start_periodic'] == 0 && $exitPlan['period_id'] == $period_id && !isset($exitperiodPlan)){//如果完成，更新下一个任务状态为进行中
                    $plans['driver_status'] = 1;
                    // dump(33);
                    Plan::where('id', $exitPlan['id'])->update(['driver_status' => 2]);
                }elseif($exitPlan['driver_status'] == 1 && $exitPlan['status'] == 8 && $param['start_periodic'] == 0 && $exitPlan['period_id'] == $period_id && !isset($exitperiodPlan)){//如果完成，更新下一个任务状态为进行中
                    $plans['driver_status'] = 1;
                    // dump($exitPlan['id']);
                    // dump(44);
                    Plan::where('id', $exitPlan['id'])->update(['driver_status' => 4]);
                }else{
                    $plans['driver_status'] = 0;
                }
                
            }
            else if(!isset($Plan)){
                // dump(99);
                $plans['driver_status'] = 1;
                // $param['status'] = 1;
                
            }else{
                $plans['driver_status'] = 0;
            }
            // dump($period_id);
            // dump($Plan);die;
            // dump($period_id);die;
            $plans['period_id'] = $period_id;

            // $period['period_id'] = $plans['period_id'];
            $period['period_id_driver'] = $plans['period_id'];
            $period['driver_name'] = $plans['driver_name'];
            $period['trailer_num'] = $plans['trailer_num'];
            $period['month'] = $currentMonth;
            $period['year'] = $currentYear;
            // 【YB分类整理】问题描述20240726-2 No.82 顺序调整 by baolei start
            $period['head_num'] = $plans['head_num'];
            // 【YB分类整理】问题描述20240726-2 No.82 顺序调整 by baolei end
            
            $periodid = Db::name('admin_carplan_period')->where($period)->find();
            // dump($period);die;
            if($periodid == null && $plans['plan_type'] == 0 && $param['start_periodic'] == 1){
                $period['initiator'] = $plans['initiator'];
                $period['dispatcher'] = $plans['dispatcher'];
                $period['status'] = $plans['driver_status'];
                Db::name('admin_carplan_period')->insert($period);
            }
            // $res = Db::name('admin_carplan_period')->create($plans);
            // dump($plans);die;
            // $res = Plans::update($param,['id'=>$param['id']]);
            // dump($plans);die;
            // $r = $this->pushToSingleByCids();
            
            $plans['fixed'] = 1;
            $plans['month'] = $currentMonth;
            $plans['year'] = $currentYear;
            $plans['update_time'] = date('Y-m-d H:i:s');
            $res = Plan::create($plans);
            if($plans['driver_status'] == 1){
                $id = $res->id;
                $cid = Admin::where('username',$param['driver_name'])->value('user_cid');
                if($res->plan_type == 0){
                    $plantype = "运输任务";
                    $planmsg = "从".$res->load_factory."出发到".$res->unload_factory;
                    $SMSCODE = 'SMS_472080097';
                }
                else if($res->plan_type == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$res->load_factory."出发装货";
                    $SMSCODE = 'SMS_472135128';
                }
                else if($res->plan_type == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$res->unload_factory."卸货";
                    $SMSCODE = 'SMS_472095114';
                }
                // dump($phone);dump($SMSCODE);die;
                // dump($planmsg);die;
                // $this->SDKsendSms($phone, $param['driver_name'], $plans['load_factory'], $plans['unload_factory']);
                // $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $plans['load_factory'], $plans['unload_factory']);
                $sendSms_code = $this->SDKsendSms($phone, $param['driver_name'], $param['load_factory'], $param['unload_factory'],$SMSCODE,$time);

                // dump($sendmessage);die;
                $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);

                Db::commit();
                if($sendSms_code == 'OK'){
                    return $this->success(['msg' => '分配成功']);
                }
            }
            // die;
            // $notice = $period;
            // $notice['plan_id'] = $res->id;
            // $notice['read'] = 0;
            // $notice['msg'] = "有新的任务分配";
            // $notice['load_factory'] = $plans['load_factory'];
            // $notice['unload_factory'] = $plans['unload_factory'];
            // $notice['plan_type'] = $plans['plan_type'];
            // $notice['platform'] = $plans['platform'];
            // Db::name('admin_car_notice')->insert($notice);
            // $sendmessage = $this->SDKsendSms($phone, $param['driver_name'], $plans['load_factory'], $plans['unload_factory']);
            // $data = json_decode($sendmessage, true);
            // $phone_number = $data['SendStatusSet'][0]['PhoneNumber'];
            // $code = $data['SendStatusSet'][0]['Code'];
            // // dump($sendmessage);die;
            // if(!$res){
            //     throw new \Exception('分配失败');
            // }
            // $msg = '分配成功,已发送给手机号为：'.$phone.'的驾驶员'.$param['driver_name'];
            // $appmsg = '已发送给'.$param['driver_name'];
            
            
            if(!$res){
                throw new \Exception('新增失败');
            }
     
            Db::commit();
            return $this->success(['msg' => '分配成功']);
    
            // return $this->success([],'分配成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    // public function sendnotice($plans,$phone){
    //     try{
    //         // $notice = $period;
    //         $notice['period_id_driver'] = $plans['period_id'];
    //         $notice['driver_name'] = $plans['driver_name'];
    //         $notice['trailer_num'] = $plans['trailer_num'];
    //         $notice['month'] = $plans['month'];
    //         $notice['year'] = $plans['year'];
    //         $notice['status'] = 0;
    //         $notice['initiator'] = $plans['initiator'];
    //         $notice['dispatcher'] = $plans['dispatcher'];
    //         $notice['plan_id'] = $plans->id;
    //         $notice['read'] = 0;
    //         $notice['msg'] = "有新的任务分配";
    //         $notice['load_factory'] = $plans['load_factory'];
    //         $notice['unload_factory'] = $plans['unload_factory'];
    //         $notice['plan_type'] = $plans['plan_type'];
    //         $notice['platform'] = $plans['platform'];
    //         $res = Db::name('admin_car_notice')->insert($notice);
    //         $sendmessage = $this->SDKsendSms($phone, $plans['driver_name'], $plans['load_factory'], $plans['unload_factory']);
    //         $data = json_decode($sendmessage, true);
    //         $phone_number = $data['SendStatusSet'][0]['PhoneNumber'];
    //         $code = $data['SendStatusSet'][0]['Code'];
    //         // dump($sendmessage);die;
    //         if(!$res){
    //             throw new \Exception('分配失败');
    //         }
    //         $msg = '分配成功,已发送给手机号为：'.$phone.'的驾驶员'.$plans['driver_name'];
    //         $appmsg = '已发送给'.$plans['driver_name'];
            
            
    //         Db::commit();
    //         if($code == 'Ok'){
    //             return $this->success(['msg' => $msg,'appmsg' => $appmsg]);
    //         }else{
    //             return $this->success(['msg' => '分配成功,短信发送失败']);
    //         }
    //     }catch (\Exception $exception){
    //         Db::rollback();
    //         $this->recordLog($exception);
    //         return $this->error();
    //     }
    // }
    public function delplan($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $finishplan = Plans::whereIn('id',$param['ids'])->column('status');
            if (in_array(1, $finishplan)) {
                // return $this->error([],'删除成功');
                return $this->error('已完成计划无法删除，请重新选择');
            } else {
                // dump($finishplan);die;
                // $res = Plan::whereIn('id',$param['ids'])->delete();
                $res = Plans::destroy($param['ids']);
                if(!$res){
                    throw new \Exception('删除失败');
                }
            }
            // $res = Plans::whereIn('id',$param['ids'])->delete();
            // if($Plan['driver_status'] > 1){
            //     $param['driver_status'] = 1;
            // }
            // else{
            //     $param['driver_status'] = 0;
            // }
            if(!$res){
                throw new \Exception('删除失败');
            }

            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    
    //驾驶员app操作
    public function driver_normal($param=[],$name){
        
        try{
            $where = $param;
            $where['driver_name'] = $name;
            // dump($param);die;
            // if(isset($param['keywords'])&&$param['keywords']){
            //     $where[] = ['trailer_plate|trailer_brand','like','%'.$param['keywords'].'%'];
            // }
            $max_id_plan = Plan::where($where)->order(['plan_order'=>'desc'])->find();
            $plan = Plan::where($where)->where('plan_order','<',$max_id_plan['plan_order'])->order(['plan_order'=>'desc'])->find();
            if($max_id_plan['status'] == 1 && $max_id_plan['driver_status'] == 0){
                if($plan['status'] > 4 && $plan['driver_status'] > 1){
                    $param['driver_status'] = 1;
                    Plan::update($param,['id'=>$max_id_plan['id']]);
                }
            }
            // if($where['driver_status']>1){
            //     // $where['driver_status'] = array('gt',1);
            //     $where['driver_status']  = array('2','3');
            // }
            // dump($where);die;
            if (in_array("0", $param['driver_status'])) {
                $data = Plan::where($where)->order(['plan_order'=>'desc'])->select();
            } else {
                $data = Plan::where($where)->order(['id'=>'desc','plan_order'=>'desc'])->select();
            }
            // $data = Plan::where($where)->order(['id'=>'desc','plan_order'=>'desc'])->select();
            // dump($data);die;
            $count = count($data);
            
            // $data = Plan::where($where)->order(['create_time'=>'desc'])
            //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
         
            return $this->success(['count'=>$count,'data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function driver_sumitnormal($param=[],$Authorization){
        $currentHour = date('H');
        if ($currentHour >= 4 && $currentHour < 8) {
            $time = '早上';
        } elseif ($currentHour >= 8 && $currentHour < 11) {
            $time = '上午';
        } elseif ($currentHour >= 11 && $currentHour < 13) {
            $time = '中午';
        } elseif ($currentHour >= 13 && $currentHour < 18) {
            $time = '下午';
        } else {
            $time = '晚上';
        }
        try{
            Db::startTrans();
            // dump($param);die;
            if (isset($param['load_weight_inspection']) && is_array($param['load_weight_inspection'])) {
                $param['load_weight_inspection'] = implode(',', $param['load_weight_inspection']);
            } else {
            }
            if (isset($param['unload_weight_inspection']) && is_array($param['unload_weight_inspection'])) {
                $param['unload_weight_inspection'] = implode(',', $param['unload_weight_inspection']);
            } else {
            }
            // dump($param['load_weight_inspection']);die;
            $Plan = Plan::where('id',$param['id'])->find();
            if($Plan['driver_status'] != 1){
                return $this->error('任务状态变更，请返回列表刷新');
            }
            $firstPlan = Plan::where('period_id',$Plan['period_id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }
            // $trailer = Cartrailer::where('trailer_plate',$Plan['trailer_num'])->find();
            // if($trailer['trailer_status'] == 1 && $trailer['product_name'] != $Plan['product_name']){
            //     return $this->error('货品名称与挂车现有货品不同，请联系调度员调整');
            // }
            //  dump($param);die;
            // $last['period_id'] = $Plan['period_id'];
            // $last['status'] = 0;
            $last['driver_status'] = 0;
            $last['driver_name'] = $Plan['driver_name'];
            // $last['start_periodic'] = 1;
            // $lastplan = Plan::where($last)->wherer('id','>',$param['id'])->order(['plan_order'=>'asc'])->find();//下一条任务信息
            $exitperiodPlan1 = Plan::where('driver_name', $Plan['driver_name'])->where('driver_status',1)->find();
            $exitperiodPlan = Plan::where('period_id', $Plan['period_id'])->where('driver_name', $Plan['driver_name'])->where('driver_status',0)->order(['plan_order'=>'desc'])->find();
            if($exitperiodPlan && $Plan['period_id'] != null && $Plan['period_id'] != ''){
                $lastplan =$exitperiodPlan;
            }else{
                $lastplan = Plan::where($last)->where('id','<>',$param['id'])->order(['plan_order'=>'desc'])->find();//下一条非新周期任务信息
            }
            
            $newplan = Plan::where('driver_name',$Plan['driver_name'])->where('id','<>',$param['id'])->order(['plan_order'=>'desc'])->find();//下一条非新周期任务信息
            // dump($lastplan);die;
            // dump($param);
            if (isset($param['mileage'])) {
                Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['start_mile'=>$param['mileage']]);
            }
            if (isset($param['back_mileage'])) {
                Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['end_mile'=>$param['back_mileage']]);
            }
            if (isset($param['carrying_money'])) {
                Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['carrying_money'=>$param['carrying_money']]);
            }
            if (isset($param['remaining_money'])) {
                Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['remaining_money'=>$param['remaining_money']]);
            }

            if (isset($param['status'])) {
                if($lastplan['plan_type'] == 0){
                    $plantype = "常规任务";
                    $planmsg = "从".$lastplan['load_factory']."出发到".$lastplan['unload_factory'];
                    $SMSCODE = 'SMS_472080097';
                }
                else if($lastplan['plan_type'] == 1){
                    $plantype = "装货任务";
                    $planmsg = "从".$lastplan['load_factory']."出发装货";
                    $SMSCODE = 'SMS_472135128';
                }
                else if($lastplan['plan_type'] == 2){
                    $plantype = "卸货任务";
                    $planmsg = "到".$lastplan['unload_factory']."卸货";
                    $SMSCODE = 'SMS_472095114';
                }


                if (isset($param['trailer_num']) && $param['status'] == 1 && $Plan['plan_type'] == 0){
                    
                    $trailer_num = Info::where('id',$Plan['info_id'])->find();
                    //输入的车头是否存在于人员车辆匹配中
                    $exit_trailer_num = Info::where('trailer_num',$param['trailer_num'])->find();
                    if($trailer_num['trailer_num'] != $param['trailer_num'] && $exit_trailer_num){
                        //将原有$param['trailer_num']的info信息置为空
                        Info::where('id',$exit_trailer_num['id'])->update(['trailer_num'=>null,'trailer_id'=>null]);
                    }
                    
                    $trailer_num = Cartrailer::where('trailer_plate',$param['trailer_num'])->find();
                    Info::where('id',$Plan['info_id'])->update(['trailer_num'=>$param['trailer_num'],'trailer_id'=>$trailer_num['id']]);
                }
                
                if (isset($param['escort_name']) && $param['status'] == 1 && $Plan['plan_type'] == 0){
                    
                    $escort_name = Info::where('id',$Plan['info_id'])->find();
                    // dump($escort_name);
                    //输入的车头是否存在于人员车辆匹配中
                    $exit_escort_name = Info::where('escort_name',$param['escort_name'])->find();

                    if(($escort_name['escort_name'] != $param['escort_name']) && $exit_escort_name){
                        // dump($exit_escort_name);
                        //将原有$param['escort_name']的info信息置为空
                        Info::where('id',$exit_escort_name['id'])->update(['escort_name'=>null,'escort_id'=>null]);
                    }
                    $escort_name = Escort::where('name',$param['escort_name'])->find();
                    // dump($Plan['info_id']);die;
                    Info::where('id',$Plan['info_id'])->update(['escort_name'=>$param['escort_name'],'escort_id'=>$escort_name['id']]);
                }
                
                
                $phone = Admin::where('username',$Plan['driver_name'])->value('phone');
                // dump($lastplan['start_periodic']);
                // dump($Plan['period_id']);die;
                if ($param['status'] == 5 && $exitperiodPlan != null && $exitperiodPlan['period_id'] != null) {
                    // dump(9999);
                    // 如果没有下一个任务，则将当前任务的 driver_status 更新为 2，下一个任务的 driver_status 更新为 1
                    $param['driver_status'] = 2;
                    
                    $lastplan = $exitperiodPlan;
                    Plan::where('id', $exitperiodPlan['id'])->update(['driver_status' => 1]);
                    
                    $next = Plan::where('id', $lastplan['id'])->find();
                    
                    $id = $lastplan['id'];
                    $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                    $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                    // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                    $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    
                } else if ($param['status'] == 5 && $lastplan !== null && $lastplan['start_periodic'] != 1 && $lastplan['period_id'] == $Plan['period_id'] && $lastplan['period_id'] != null) {
                    // 如果没有下一个任务，则将当前任务的 driver_status 更新为 2，下一个任务的 driver_status 更新为 1
                    $param['driver_status'] = 2;
                    
                    Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                    
                    $next = Plan::where('id', $lastplan['id'])->find();
                    
                    $id = $lastplan['id'];
                    $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                    $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                    // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                    $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    
                } else if ($param['status'] == 8 && $exitperiodPlan !== null && $lastplan['period_id'] != null){
                    $param['driver_status'] = 4;
                    Plan::where('id', $exitperiodPlan['id'])->update(['driver_status' => 1]);
                    $next = Plan::where('id', $lastplan['id'])->find();
                    
                    $id = $exitperiodPlan['id'];
                    $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                    $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                } else if ($param['status'] == 8 && $lastplan !== null && $lastplan['start_periodic'] != 1 && $lastplan['period_id'] == $Plan['period_id'] && $lastplan['period_id'] != null) {
                    // 如果没有下一个任务，则将当前任务的 driver_status 更新为 2，下一个任务的 driver_status 更新为 1
                    $param['driver_status'] = 4;
                    Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                    $next = Plan::where('id', $lastplan['id'])->find();
                    
                    $id = $lastplan['id'];
                    $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                    $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                    // dump($r);die;
                    // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                    // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    
                } else if ($param['status'] == 8 && $lastplan['period_id'] == $Plan['period_id']) {
                    // 如果没有下一个任务，则将当前任务的 driver_status 更新为 4
                    $param['driver_status'] = 1;
                    
                } else if ($param['status'] == 0 && $Plan['status'] == 8) {

                    $param['driver_status'] = 4;
                    Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['status'=>2]);
                    if($lastplan !== null){//如果下一条数据存在
                        if($lastplan['start_periodic'] == 1){
                            Db::name('admin_carplan_period')->where('period_id_driver',$lastplan['period_id'])->update(['status'=>1]);
                        }
                        // dump($lastplan['id']);die;
                        Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                        $id = $lastplan['id'];
                        $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                        $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                        // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                        $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    }
                }  else if ($param['status'] == 0 && $Plan['status'] == 9) {
                    $param['driver_status'] = 3;
                    Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['status'=>2]);
                    if($lastplan !== null){//如果下一条数据存在
                        if($lastplan['start_periodic'] == 1){
                            Db::name('admin_carplan_period')->where('period_id_driver',$lastplan['period_id'])->update(['status'=>1]);
                        }
                        Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                        $id = $lastplan['id'];
                        $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                        $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                        // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                        $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    }
                } else if ($param['status'] == 0) {
                    $param['driver_status'] = 2;
                    $test = Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->find();
                    // dump($lastplan['period_id']);die;
                    // dump($lastplan);die;
                    Db::name('admin_carplan_period')->where('period_id_driver',$Plan['period_id'])->update(['status'=>2]);
                    // dump($lastplan);die;
                    if($lastplan !== null){//如果下一条数据存在
                        if($lastplan['start_periodic'] == 1){
                            Db::name('admin_carplan_period')->where('period_id_driver',$lastplan['period_id'])->update(['status'=>1]);
                        }
                        Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                        $id = $lastplan['id'];
                        $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                        $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                        // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                        $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    }
                    // 如果有下一个任务，则将当前任务的 driver_status 更新为 2
                    // 【YB分类整理】问题描述20240726-2 68 by baolei start
                    //  } else if ($param['status'] == 3 && $Plan['unload_factory'] == null) {
                } else if ($param['status'] == 3 && $Plan['plan_type'] == 1) {
                    // 【YB分类整理】问题描述20240726-2 68 by baolei start
                    // 如果 unload_factory 为空，将当前任务的 status 更新为 5，并将当前任务的 driver_status 更新为 2，
                    // 同时将上一个任务的 driver_status 更新为 1
                    // 【YB分类整理】问题描述20240726-2 68 by baolei start
                    /*$param['status'] = 5;*/
                    // 【YB分类整理】问题描述20240726-2 68 by baolei end

                    
                    if ($lastplan != null && $lastplan['start_periodic'] == 0 && $lastplan['period_id'] == $Plan['period_id']) {
                        $param['driver_status'] = 2;
                        // Db::name('admin_carplan_period')->where('period_id_driver',$lastplan['period_id'])->update(['status'=>1]);
                        Plan::where('id', $lastplan['id'])->update(['driver_status' => 1]);
                        $id = $lastplan['id'];
                        $cid = Admin::where('username',$Plan['driver_name'])->value('user_cid');
                        $r = $this->pushToSingleByCids($id,$cid,$plantype,$planmsg);
                        // $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory']);
                        $this->SDKsendSms($phone, $lastplan['driver_name'], $lastplan['load_factory'], $lastplan['unload_factory'],$SMSCODE,$time);
                    }
                }
                // 【YB分类整理】问题描述20240726-2 68 by baolei start
                else if ($param['status'] == 3 && $Plan['plan_type'] == 2) {
                    $param['status'] = 4;
                }
                // 【YB分类整理】问题描述20240726-2 68 by baolei end
                else if ($param['status'] == 3 && $Plan['plan_type'] == 0) {
                    
                    $quantity = Db::name('admin_carplans')->where('id',$Plan['plans_id'])->find();
                    
                    if($quantity){
                        $product_quantity = $quantity['product_quantity']-30>0?$quantity['product_quantity']-30:0;
                        Db::name('admin_carplans')->where('id',$Plan['plans_id'])->update(['product_quantity'=> $product_quantity]);
                    }
                    
                    // dump(999);die;
                }
                // dump($Plan);die;
                $carhead_plate = isset($param['head_num'])?$param['head_num']:$Plan['head_num'];
                $escort_name = isset($param['escort_name'])?$param['escort_name']:$Plan['escort_name'];
                // dump($Plan);die;
                if (in_array($param['status'], [1, 3, 5])) {
                    $driver['driver_status'] = 1;
                    $head['head_status'] = 3;
                    $escort['escort_status'] = 1;
                } elseif ($param['status'] == 2 || $param['status'] == 4 || $param['status'] == 0) {
                    $driver['driver_status'] = ($param['status'] == 0) ? 0 : 1;
                    $head['head_status'] = ($param['status'] == 0) ? 0 : $param['status']/2;
                    $escort['escort_status'] = ($param['status'] == 0) ? 0 : 1;
                } elseif ($param['status'] == 8) {
                    $driver['driver_status'] = Admin::where(['username' => $Plan['driver_name']])->value('driver_status');
                    $head['head_status'] = 3;
                    $escort['escort_status'] = Escort::where(['name' => $escort_name])->value('escort_status');
                } else {
                    $driver['driver_status'] = Admin::where(['username' => $Plan['driver_name']])->value('driver_status');
                    $head['head_status'] = Carhead::where(['carhead_plate' => $Plan['head_num']])->value('head_status');
                    $escort['escort_status'] = Escort::where(['name' => $escort_name])->value('escort_status');
                }
 
                $trailer['trailer_plate'] = $Plan['trailer_num'];
                $trailerexit = Cartrailer::where('trailer_plate',$Plan['trailer_num'])->find();
                // dump($trailerexit['product_quantity']);
                // dump($param);die;
                if(isset($param['load_product_quantity']) && ($trailerexit['product_name'] == $Plan['product_name'])){
                    $load_product_quantity = $trailerexit['product_quantity'] + $param['load_product_quantity'];
                }elseif(isset($param['load_product_quantity'])){
                    $load_product_quantity = $param['load_product_quantity'];
                }else{
                    // $load_product_quantity = $Plan['load_product_quantity'];
                }
                
                
                // dump($Plan);die;
                $trailerdata['product_name'] = $Plan['product_name'];
                if ($param['status'] == 3) {
                    $trailerdata['trailer_status'] = 1;
                    $trailerdata['product_quantity'] = $load_product_quantity;
                    $trailerdata['product_name'] = $Plan['product_name'];
                } elseif ($param['status'] == 5 && $Plan['plan_type'] == 0) {
                    
                    $product_quantity = Cartrailer::where(['trailer_plate' => $Plan['trailer_num']])->value('product_quantity');
                    $quantity = $product_quantity - $param['unload_product_quantity'];
                    $trailerdata['product_quantity'] = ($quantity > 0.3) ? $quantity : null;
                    $trailerdata['trailer_status'] = ($quantity > 0.3) ? 1 : 0;
                    $trailerdata['product_name'] = ($quantity > 0.3) ? $Plan['product_name'] : null;
                } elseif ($param['status'] == 5 && $Plan['plan_type'] == 2) {
                    $product_quantity = Cartrailer::where(['trailer_plate' => $Plan['trailer_num']])->value('product_quantity');
                    $quantity = $product_quantity - $param['unload_product_quantity'];
                    $trailerdata['product_quantity'] = ($quantity > 0.3) ? $quantity : null;
                    $trailerdata['trailer_status'] = ($quantity > 0.3) ? 1 : 0;
                    $trailerdata['product_name'] = ($quantity > 0.3) ? $Plan['product_name'] : null;
                } else {
                    // dump(99);die;
                    $trailer['trailer_status'] = Cartrailer::where(['trailer_plate' => $Plan['trailer_num']])->value('trailer_status');
                    $trailerdata['product_name'] = Cartrailer::where(['trailer_plate' => $Plan['trailer_num']])->value('product_name');
                }
                // dump($trailerdata);die;
                // return $this->success([],'提交成功'.$load_product_quantity.'+'.$param['load_product_quantity']);
                Carhead::update($head,['carhead_plate'=>$carhead_plate]);
                Cartrailer::where('trailer_plate',$Plan['trailer_num'])->update($trailerdata);
                Escort::update($escort,['name'=>$escort_name]);
                Admin::update($driver,['username'=>$Plan['driver_name']]);
                
            }
            // dump($param);die;
        
            $res = Plan::update($param,['id'=>$param['id']]);

            if(!$res){
                throw new \Exception('编辑失败');
            }
     
            Db::commit();
            return $this->success([],'提交成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public static function createClient($accessKeyId, $accessKeySecret)
    {
        $config = new Config([
            // 您的 AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 您的 AccessKey Secret
            "accessKeySecret" => $accessKeySecret
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }
    function SDKsendSms($phone, $drivername, $loadfactory, $unloadfactory, $templateCode, $time)
    {
        // dump($phone);die;
        $Ali_SMS_APPID = Env::get('MSGSDK.Ali_SMS_APPID');
        $Ali_SMS_APPKEY = Env::get('MSGSDK.Ali_SMS_APPKEY');

        $client = self::createClient($Ali_SMS_APPID, $Ali_SMS_APPKEY);
        $sendSmsRequest = new SendSmsRequest([
            "phoneNumbers" => $phone,//接收短信的手机号码
            "signName" => "七星智运",//短信签名名称。
            "templateCode" => $templateCode,//短信模板CODE。
            // "templateParam" => "{\"code\":\"0822\"}"//短信模板变量对应的实际值。
            "templateParam" => json_encode(array('name' => $drivername,'time' => $time,'address1' => $loadfactory,'address2' => $unloadfactory))
        ]);
        $runtime = new RuntimeOptions([]);
        // dump($sendSmsRequest);die;
        try {
            //发送短信
            $result = $client->sendSmsWithOptions($sendSmsRequest, $runtime);
            
            if ($result->body->code == 'OK') {
                //发送成功操作
                // dump($result);die;
                return $result->body->code;
                // return true;
            }else {
                //发送失败操作
                return $result->body->code;
                // return false;
            }
        } catch (\Exception $error) {
            // dump(999);die;
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                // return ["code" => $error->getCode(), "msg" => $error->getMessage()];
            }
 
            return false;
        }
    }
 

    function SDKsendSms0($phone, $drivername, $loadfactory, $unloadfactory) {
        try {
            $client = self::createClient();
            // echo Env::get('MSGSDK.TENCENT_SMS_APPKEY');die;
            $APPID = Env::get('MSGSDK.TENCENT_SMS_APPID');
            $APPKEY = Env::get('MSGSDK.TENCENT_SMS_APPKEY');
            $cred = new Credential($APPID, $APPKEY);
            // 实例化一个http选项，可选的，没有特殊需求可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
        
            // 实例化一个client选项，可选的，没有特殊需求可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            // 实例化要请求产品的client对象,clientProfile是可选的
            $client = new SmsClient($cred, "ap-beijing", $clientProfile);
        
            // 实例化一个请求对象,每个接口都会对应一个request对象
            $req = new SendSmsRequest();
        
            $params = array(
                // "PhoneNumberSet" => array( "15041726679" ),
                "PhoneNumberSet" => array( $phone ),
                "SmsSdkAppId" => "1400908125",
                "SignName" => "大连钰波科技有限公司",
                "TemplateId" => "2148739",
                "TemplateParamSet" => array( $drivername, $loadfactory, $unloadfactory )
            );
            // dump($params);
            // $req->fromJsonString(json_encode($params));
            // 返回的resp是一个SendSmsResponse的实例，与请求对象对应
            $resp = $client->SendSms($req);
            // dump($resp->toJsonString());die;
            return $resp->toJsonString();
            // 输出json格式的字符串回包
            print_r($resp->toJsonString());
        }
        catch(TencentCloudSDKException $e) {
            echo $e;
        }
    }

    function sign($key, $msg) {
        return hash_hmac("sha256", $msg, $key, true);
    }
    
    function sendSms($phone, $drivername, $loadfactory, $unloadfactory) {
        $secret_id = "AKIDvomjnw38GUtS9yminv7PlBcG5qgZQ0ZN";
        $secret_key = "6SxZIe9UxlpyHhIuaSPLNnK1tlgwNnGp";
        $token = "";
        // $phone = '18642646375'; // 电话号码
        // $drivername = 'John Doe'; // 司机姓名
        // $loadfactory = 'Load Factory'; // 装货工厂
        // $unloadfactory = 'Unload Factory'; // 卸货工厂
        $service = "sms";
        $host = "sms.tencentcloudapi.com";
        $req_region = "ap-beijing";
        $version = "2021-01-11";
        $action = "SendSms";
        // $payload = "{\"PhoneNumberSet\":[\"18642646375\"],\"SmsSdkAppId\":\"1400908125\",\"SignName\":\"大连钰波科技有限公司\",\"TemplateId\":\"2146893\",\"TemplateParamSet\":[\"韩璐女士\",\"辽宁沈阳\",\"辽宁大连\"]}";
        $payload = "{\"PhoneNumberSet\":[\"".$phone."\"],\"SmsSdkAppId\":\"1400908125\",\"SignName\":\"大连钰波科技有限公司\",\"TemplateId\":\"2148739\",\"TemplateParamSet\":[\"".$drivername."\",\"".$loadfactory."\",\"".$unloadfactory."\"]}";
            
        $params = json_decode($payload);
        $endpoint = "https://sms.tencentcloudapi.com";
        $algorithm = "TC3-HMAC-SHA256";
        $timestamp = time();
        $date = gmdate("Y-m-d", $timestamp);
    
        // ************* 步骤 1：拼接规范请求串 *************
        $http_request_method = "POST";
        $canonical_uri = "/";
        $canonical_querystring = "";
        $ct = "application/json; charset=utf-8";
        $canonical_headers = "content-type:".$ct."\nhost:".$host."\nx-tc-action:".strtolower($action)."\n";
        $signed_headers = "content-type;host;x-tc-action";
        $hashed_request_payload = hash("sha256", $payload);
        $canonical_request = "$http_request_method\n$canonical_uri\n$canonical_querystring\n$canonical_headers\n$signed_headers\n$hashed_request_payload";
    
        // ************* 步骤 2：拼接待签名字符串 *************
        $credential_scope = "$date/$service/tc3_request";
        $hashed_canonical_request = hash("sha256", $canonical_request);
        $string_to_sign = "$algorithm\n$timestamp\n$credential_scope\n$hashed_canonical_request";
    
        // ************* 步骤 3：计算签名 *************
        $secret_date = $this->sign("TC3".$secret_key, $date);
        // dump($secret_date);
        // echo $secret_date;die;
        $secret_service = $this->sign($secret_date, $service);
        $secret_signing = $this->sign($secret_service, "tc3_request");
        $signature = hash_hmac("sha256", $string_to_sign, $secret_signing);
    
        // ************* 步骤 4：拼接 Authorization *************
        $authorization = "$algorithm Credential=$secret_id/$credential_scope, SignedHeaders=$signed_headers, Signature=$signature";
    
        // ************* 步骤 5：构造并发起请求 *************
        $headers = [
            "Authorization" => $authorization,
            "Content-Type" => "application/json; charset=utf-8",
            "Host" => $host,
            "X-TC-Action" => $action,
            "X-TC-Timestamp" => $timestamp,
            "X-TC-Version" => $version
        ];
        if ($req_region) {
            $headers["X-TC-Region"] = $req_region;
        }
        if ($token) {
            $headers["X-TC-Token"] = $token;
        }
    
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_map(function ($k, $v) { return "$k: $v"; }, array_keys($headers), $headers));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);    
            // dump($response);
            return $response;
            echo $response;
        } catch (\Exception $err) {
            echo $err->getMessage();
        }
    }
    

    public function notice($param=[]){
        
        try{
            $where = $param;
            // $where['driver_name'] = $name;
            if(isset($param['driver_name'])&&$param['driver_name'] != ''){
                $where['driver_name'] = $param['driver_name'];
            }
            if(isset($param['read'])&&$param['read'] != ''){
                $where['read'] = $param['read'];
            }
            $data = Db::name('admin_car_notice')->where($where)->select();
            // $this->pushToSingleByCids();
    
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function pushToSingleByCids($id,$cid,$plantype,$planmsg){
        
        $payloadData = array(
            "plan_id" => $id
            // 可以添加更多的键值对
        );
        $payload = json_encode($payloadData);

        //创建API，APPID等配置参考 环境要求 进行获取
        $api = new \GTClient("https://restapi.getui.com","3InbtkNNcg5p9MQhAzuIK", "cuEAutipwY6xSvWR2HbJ3","hvovgkQXQK9CGChodDYm66");
        //设置推送参数
        $push = new \GTPushRequest();
        $push->setRequestId($this->micro_time());
        $message = new \GTPushMessage();
        $notify = new \GTNotification();
        $notify->setTitle($plantype);
        $notify->setBody($planmsg);
        
        //点击通知后续动作，目前支持以下后续动作:
        //1、intent：打开应用内特定页面url：打开网页地址。2、payload：自定义消息内容启动应用。3、payload_custom：自定义消息内容不启动应用。4、startapp：打开应用首页。5、none：纯通知，无后续动作
        $notify->setClickType("payload");
        $notify->setnotifyId('115');
        $notify->setPayload($payload);
        // $notify->setIntent("plan");
        // dump($notify);die;
        $message->setNotification($notify);
        $push->setPushMessage($message);
        $push->setCid($cid);
        // $push->setnotifyId('115');
        //处理返回结果
        $result = $api->pushApi()->pushToSingleByCid($push);
        return $result;
        // dump($result);die;
    }
    public function micro_time()
    {
        list($usec, $sec) = explode(" ", microtime());
        $time = ($sec . substr($usec, 2, 3));
        return $time;
    }

}
