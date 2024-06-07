<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AdminActionLog;
use app\model\AdminLoginLog;
use app\model\AuthGroup;
use app\model\AuthGroupAccess;
use app\service\BaseService;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use app\model\Plan;
use app\model\Plans;
use app\model\Carhead;
use app\model\Cartrailer;
use app\model\Escort;
use app\model\Info;
use app\model\Factory;


class PlanService extends BaseService
{

    public function getplaninfo(){
        // dump('111');die;
        try{
            // $data = Info::where('status','in','0,5,9')->select();
            $data = Info::select();
            // dump($data);
            foreach($data as $key => $value){
                $head = Info::carhead($value['head_id']);
                $trailer = Info::cartrailer($value['trailer_id']);
                $driver = Info::cardriver($value['driver_id']);
                $escort = Info::carescort($value['escort_id']);
                $trailer_status = Info::cartrailer($value['trailer_status']);
                // $info = $head.'-'.$trailer.'-'.$driver.'-'.$escort;
                $test = Info::cartrailer($value['trailer_status']);
                // dump($trailer['trailer_status']);die;
                $info = $trailer['trailer_plate'].'-'.$driver;
                $data[$key]['info'] = $info;
                $data[$key]['driver_name'] = $driver;
                $data[$key]['escort_name'] = $escort;
                $data[$key]['head_num'] = $head;
                $data[$key]['trailer_num'] = $trailer['trailer_plate'];
                $data[$key]['trailer_status'] = $trailer['trailer_status'];
            }
            $factory = Factory::where('status',2)->select();
            return $this->success(['data'=>$data,'factory'=>$factory]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getnormal($param=[]){
   
        try{
            $where['plan_type'] = 0;
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|trailer_num|load_factory|unload_factory','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])){
                
                if($param['status'] == 'null'){
                    $where['status'] = null;
                }elseif($param['status'] != ''){
                    $where['status'] = $param['status'];
                }

            }

            $data = Plan::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            // dump($data);
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getnormalInfo($param=[]){
        try{
            $info = Plan::where('id',$param['id'])->find();

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

    public function addnormal($param=[]){
        
        try{
            Db::startTrans();
            $Plan = Plan::where('driver_name',$param['driver_name'])->order(['id'=>'desc'])->find();
           
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
            $param['status'] = 0;
            if($param['start_periodic'] == '1' && $Plan['end_periodic'] == '1'){
                $param['periodic_times'] = $Plan['periodic_times'] + 1;
            }
            else{
                $param['periodic_times'] = $Plan['periodic_times'];
            }
            if($param['start_periodic'] == '1'){
                $max = $param['periodic_times'] +1;
                if($max<10){
                    $id = '00'.$max;
                }
                else if($max>10){
                    $id = '0'.$max;
                }
                else if($max>100){
                    $id = $max;
                }
                $period['period_id'] = $currentYear.'-'.$currentMonth.'-'.$id;
                $period['info_id']= $param['info_id'];
                $period['driver_name']= $param['driver_name'];
                $period['year']= $currentYear;
                $resperiod = Db::name("admin_carplan_period")->insert($period);
                if(!$resperiod){
                    throw new \Exception('新增编号失败');
                }
            }
            else{
                $period['period_id'] = $Plan['period_id'];
            }
            $param['plan_type'] = 0;
            $param['period_id'] = $period['period_id'];
            
            // $period1 = [
            //     'field1' => 'value1',
            //     'field2' => 'value2',
            //     // 添加更多字段和对应的值
            // ];
            // dump($period1);
            // dump($period);die;
            
            // dump($period);die;
            // dump($param);die;
            // // if (isset($param['Plan_scope']) && is_array($param['Plan_scope'])) {
            // //     $param['Plan_scope'] = implode(',', $param['Plan_scope']);
            // // } else {
            // // }
            
            // // if (isset($param['driving_license']) && is_array($param['driving_license'])) {
            // //     $param['driving_license'] = implode(',', $param['driving_license']);
            // // } else {
            // // }
            //     die;
            $res = Plan::create($param);
            // dump($res);
            if(!$res){
                throw new \Exception('新增违章失败');
            }
     
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function editnormal($param=[]){
        try{
            Db::startTrans();
            $Plan = Plan::where('id',$param['id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }
 
            // $param['type'] = 1;
            // if(is_array($param['driving_license'][0])){
       
            //     $driving_license = array();
            //     foreach($param['driving_license'] as $key => $value){
            //         $driving_license[]= $value['url'];
            //     }
            // }else{

            //     $driving_license = $param['driving_license'];
            // }
            
            // if (isset($driving_license) && is_array($driving_license)) {
            //     $param['driving_license'] = implode(',', $driving_license);
            // } else {
            // }
            // dump($param);die;
            $res = Plan::update($param,['id'=>$param['id']]);
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
    public function delnormal($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Plan::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除违章失败');
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
            $where['plan_type'] = array('1','2');
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|trailer_num|load_factory|unload_factory','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])){
                
                if($param['status'] == 'null'){
                    $where['status'] = null;
                }elseif($param['status'] != ''){
                    $where['status'] = $param['status'];
                }

            }
            // dump($param);die;
            $data = Plan::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data);die;
            // foreach ($data['data'] as $key => $value) {
         
            //     $trailer_scope = Db::name("admin_carscope")->where('id','in', $value['trailer_scope'])->field('name')->select();
            //     $items = $trailer_scope->toArray();

            //     $itemNames = array_column($items, 'name');

            //     $data['data'][$key]['trailer_scope'] = implode(', ', $itemNames);
            //     $driving_license = explode(',', $value['driving_license']);
            //     foreach ($driving_license as $k => $val){
            //         $data['data'][$key]['driving_licenses'][$k]['url'] = $val;
            //     }

        
            // }
                
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

    public function addtemporary($param=[]){
        
        try{
            Db::startTrans();

            $param['type'] = 2;
            if (isset($param['trailer_scope']) && is_array($param['trailer_scope'])) {
                $param['trailer_scope'] = implode(',', $param['trailer_scope']);
            } else {
            }
            
            if (isset($param['driving_license']) && is_array($param['driving_license'])) {
                $param['driving_license'] = implode(',', $param['driving_license']);
            } else {
            }

            $res = Plan::create($param);
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
    public function edittemporary($param=[]){
        // dump($param);
        try{
            Db::startTrans();
            $Plan = Plan::where('id',$param['id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }
 
            $param['type'] = 1;
            if (isset($param['trailer_scope']) && is_array($param['trailer_scope'])) {
                $param['trailer_scope'] = implode(',', $param['trailer_scope']);
            } else {
            }
            
            if(is_array($param['driving_license'][0])){
       
                $driving_license = array();
                foreach($param['driving_license'] as $key => $value){
                    $driving_license[]= $value['url'];
                }
            }else{

                $driving_license = $param['driving_license'];
            }
            
            if (isset($driving_license) && is_array($driving_license)) {
                $param['driving_license'] = implode(',', $driving_license);
            } else {
            }
     
            // dump($param['driving_license']);die;
            // foreach($param['driving_license'] as $key => $value){
            //     // $driving_license[]= $value['url'];
            //     dump($value);
            //     $param['driving_license'] = implode(',', $value);
            // }
            // $param['driving_license'] = implode(',', $param['driving_license']);
            // dump($param['driving_license']);die;
            // if (isset($driving_license) && is_array($driving_license)) {
            //     $param['driving_license'] = implode(',', $driving_license);
            // } else {
            // }
            // dump($param['driving_license']);die;
            // dump($param);die;
            $res = Plan::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('新增管理员失败');
            }
           
      
            Db::commit();
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }


  
    public function deltemporary($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Plan::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除违章失败');
            }

            Db::commit();
            return $this->success([],'删除成功');
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
                $where[] = ['load_factory|unload_factory','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status'] != ''){
                $where['status'] = $param['status'];
            }
            if(isset($param['plan_type'])&&$param['plan_type'] != ''){
                $where['plan_type'] = $param['plan_type'];
            }
            // dump($param);
            if(isset($param['dist'])&&$param['dist']){
                if($param['dist'] == 1){
                    $data = Plans::where($where)->order(['create_time'=>'desc'])->select()->toArray();
                }else{
                    // dump($param);
                    // $where = $param;

                    $data = Plan::where($where)->order(['create_time'=>'desc'])->select()->toArray();

                }
            }else{
                // dump("8");
                // if($param['limit'] == '-1'){
                //     $data = Plans::where($where)->order(['create_time'=>'desc'])
                //     ->select()->toArray();
                // }else{
                //     $data = Plans::where($where)->order(['create_time'=>'desc'])
                //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // }
                $data = Plans::where($where)->order(['create_time'=>'desc'])
                    ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            }
            
            
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

    public function addplan($param=[]){
        
        try{
            Db::startTrans();
            $Plan = Plans::where('start_periodic',1)->order(['id'=>'desc'])->find();
            // dump($param);die;
            $currentYear = date('Y');
            $currentMonth = date('m');

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
            if($param['start_periodic'] == '1' && $param['plan_type'] == 0){
                $max = $param['periodic_times'];
                if($max<10){
                    $id = '00'.$max;
                }
                else if($max>10){
                    $id = '0'.$max;
                }
                else if($max>100){
                    $id = $max;
                }
                $period['period_id'] = $currentYear.'-'.$currentMonth.'-'.$id;
                $period['year']= $currentYear;
                $resperiod = Db::name("admin_carplan_period")->insert($period);
                if(!$resperiod){
                    throw new \Exception('新增失败');
                }
            }
            else if($param['plan_type'] == 0){
                $period['period_id'] = $Plan['period_id'];
            }else if($param['plan_type'] != 0){
                $period['period_id'] = null;
            }else
            $param['platform'] = 'pc';
            $param['period_id'] = $period['period_id'];
            // dump($param);die;
            $res = Plans::create($param);
            // dump($res);
            if(!$res){
                throw new \Exception('新增违章失败');
            }
     
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function editplan($param=[]){
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
    public function distplan($param=[]){
        // dump($param);die;
        try{
            Db::startTrans();
            $where['driver_name']=$param['driver_name'];
            $Plan = Plan::where($where)->order(['id'=>'desc'])->find();
            // $plans = Plans::where('id',$param['id'])->get()->toArray();
            $plan0 = Plans::find($param['id']);
            $plans = $plan0 ? $plan0->toArray() : null;

            if(isset($Plan) && $Plan['driver_status'] > 1){
                $plans['driver_status'] = 1;
                // $param['status'] = 1;
            }
            else if(isset($Plan) && $Plan['driver_status'] < 2){
                $plans['driver_status'] = 0;
            }
            else if(!isset($Plan)){
                $plans['driver_status'] = 1;
                // $param['status'] = 1;
            }
            // $plans = $param;
            unset($plans['id']);
            unset($plans['head_num']);
            unset($plans['escort_name']);
            unset($plans['status']);
            $plans['driver_name'] = $param['driver_name'];
            $plans['trailer_num'] = $param['trailer_num'];
            $plans['plans_id'] = $param['id'];
            $plans['trailer_status'] = $param['trailer_status'];
            // dump($param);
            // dump($plans);die;
            // $res = Plans::update($param,['id'=>$param['id']]);
            $res = Plan::create($plans);
            if(!$res){
                throw new \Exception('分配失败');
            }
           
      
            Db::commit();
            return $this->success([],'分配成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function delplan($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Plans::whereIn('id',$param['ids'])->delete();
            // if($Plan['driver_status'] > 1){
            //     $param['driver_status'] = 1;
            // }
            // else{
            //     $param['driver_status'] = 0;
            // }
            if(!$res){
                throw new \Exception('删除违章失败');
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
            
            // if(isset($param['keywords'])&&$param['keywords']){
            //     $where[] = ['trailer_plate|trailer_brand','like','%'.$param['keywords'].'%'];
            // }
            $max_id_plan = Plan::where($where)->order(['id'=>'desc'])->find();
            $plan = Plan::where($where)->where('id','<',$max_id_plan['id'])->where('id',$max_id_plan['id'])->order(['id'=>'desc'])->find();
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

            // dump($plan);die;
            $data = Plan::where($where)->order(['create_time'=>'desc'])->select();
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

    public function driver_sumitnormal($param=[]){
        // dump($param);
        try{
            Db::startTrans();
            $Plan = Plan::where('id',$param['id'])->find();
            if(empty($Plan)){
                throw new \Exception('信息不存在');
            }
            $last['period_id'] = $Plan['period_id'];
            $last['status'] = 0;
            $last['driver_status'] = 0;
            $lastplan = Plan::where($last)->order(['id'=>'desc'])->find();
            if(isset($param['status']) && $param['status'] == 5 && $lastplan !== null){
                $param['driver_status'] = 2;
            }
            else if(isset($param['status']) && $param['status'] == 0){
                $param['driver_status'] = 2;
            }
            // dump($param['escort_name']);die;
            if(isset($param['status'])){
                $carhead_plate = isset($param['head_num'])?$param['head_num']:$Plan['head_num'];
                $escort_name = isset($param['escort_name'])?$param['escort_name']:$Plan['escort_name'];
                // dump($escort_name);die;
                if($param['status'] == 1 || $param['status'] == 3 || $param['status'] == 5){
                    $driver['driver_status'] = 1;
                    $head['head_status'] = 3;
                    $escort['escort_status'] = 1;
                    // $trailer['trailer_status'] = 1;
                }
                else if($param['status'] == 2){
                    $driver['driver_status'] = 1;
                    $head['head_status'] = 1;
                    $escort['escort_status'] = 1;
                }
                else if($param['status'] == 4){
                    $driver['driver_status'] = 1;
                    $head['head_status'] = 2;
                    $escort['escort_status'] = 1;
                    $trailer['trailer_status'] = 4;
                }
                else if($param['status'] == 0){
                    $driver['driver_status'] = 0;
                    $head['head_status'] = 0;
                    $escort['escort_status'] = 0;
                    
                }
                // dump($head);die;
                $trailer['trailer_status'] = $param['status']-1;
                // dump($trailer);die;
                // Plan::update($param,['id'=>$param['id']]);
                Carhead::update($head,['carhead_plate'=>$carhead_plate]);
                Cartrailer::update($trailer,['trailer_plate'=>$Plan['trailer_num']]);
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
}
