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
use app\model\Carhead;
use app\model\Cartrailer;
use app\model\Escort;
use app\model\Info;
use app\model\InfoLog;
use app\model\Factory;
use app\model\Plan;

class InfoService extends BaseService
{

    public function getcarscope(){
        // dump('111');die;
        try{
            $data = Db::name("admin_carscope")->field(['id','name','type'])->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getcarhead($param=[]){
   
        try{
            $where = [];

            $data = Carhead::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
     
            foreach ($data['data'] as $key => $value) {
         
                $carhead_scope = Db::name("admin_carscope")->where('id','in', $value['carhead_scope'])->field('name')->select();
                $items = $carhead_scope->toArray();

                $itemNames = array_column($items, 'name');

                $data['data'][$key]['carhead_scope'] = implode(', ', $itemNames);
                $driving_license = explode(',', $value['driving_license']);
                // $data['data'][$key]['driving_licenses']['url']= explode(',', $value['driving_license']);
                foreach ($driving_license as $k => $val){
                    $data['data'][$key]['driving_licenses'][$k]['url'] = $val;
                }

        
            }

            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getcarheadInfo($param=[]){
        try{
            $info = Carhead::where('id',$param['id'])->find();

            $carhead_scope = explode(',', $info['carhead_scope']);
            $info['carhead_scope'] = array_map('intval', $carhead_scope);
            $driving_license = explode(',', $info['driving_license']);
            $driving_licenses = []; // 确保 $driving_licenses 在循环之前被正确初始化
            $info['driving_licenses'] = [];

            $carhead_scope = Db::name("admin_carscope")->where('id','in', $info['carhead_scope'])->field('name')->select();
            $items = $carhead_scope->toArray();
            $itemNames = array_column($items, 'name');
            $info['carhead_scope_name'] = implode(', ', $itemNames);

            foreach ($driving_license as $key => $value) {
                $driving_licenses[$key]['name'] = $key;
                $driving_licenses[$key]['url'] = $value;
            }

            $info['driving_licenses'] = $driving_licenses;
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addcarhead($param=[]){
        
        try{
            Db::startTrans();

            $param['type'] = 1;
            if (isset($param['carhead_scope']) && is_array($param['carhead_scope'])) {
                $param['carhead_scope'] = implode(',', $param['carhead_scope']);
            } else {
            }
            
            if (isset($param['driving_license']) && is_array($param['driving_license'])) {
                $param['driving_license'] = implode(',', $param['driving_license']);
            } else {
            }

            $res = Carhead::create($param);
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
    public function editcarhead($param=[]){
        try{
            Db::startTrans();
            $Carhead = Carhead::where('id',$param['id'])->find();
            if(empty($Carhead)){
                throw new \Exception('信息不存在');
            }
 
            $param['type'] = 1;
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
            // dump($param);die;
            $res = Carhead::update($param,['id'=>$param['id']]);
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
    public function delcarhead($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Carhead::whereIn('id',$param['ids'])->delete();
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
    public function getcartrailer($param=[]){
        
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['trailer_plate|trailer_brand','like','%'.$param['keywords'].'%'];
            }
            $data = Cartrailer::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data);die;
            foreach ($data['data'] as $key => $value) {
         
                $trailer_scope = Db::name("admin_carscope")->where('id','in', $value['trailer_scope'])->field('name')->select();
                $items = $trailer_scope->toArray();

                $itemNames = array_column($items, 'name');

                $data['data'][$key]['trailer_scope'] = implode(', ', $itemNames);
                $driving_license = explode(',', $value['driving_license']);
                foreach ($driving_license as $k => $val){
                    $data['data'][$key]['driving_licenses'][$k]['url'] = $val;
                }

        
            }
                
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getcartrailerInfo($param=[]){
        // dump($param);
        try{
            $info = Cartrailer::where('id',$param['id'])->find();
            
            $trailer_scope = explode(',', $info['trailer_scope']);
            $info['trailer_scope'] = array_map('intval', $trailer_scope);
            $driving_license = explode(',', $info['driving_license']);
            $driving_licenses = []; // 确保 $driving_licenses 在循环之前被正确初始化
            $info['driving_licenses'] = [];

            $trailer_scope = Db::name("admin_carscope")->where('id','in', $info['trailer_scope'])->field('name')->select();
            $items = $trailer_scope->toArray();
            $itemNames = array_column($items, 'name');
            $info['trailer_scope_name'] = implode(', ', $itemNames);

            foreach ($driving_license as $key => $value) {
                $driving_licenses[$key]['name'] = $key;
                $driving_licenses[$key]['url'] = $value;
            }

            $info['driving_licenses'] = $driving_licenses;
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addcartrailer($param=[]){
        
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

            $res = Cartrailer::create($param);
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
    public function editcartrailer($param=[]){
        // dump($param);
        try{
            Db::startTrans();
            $Cartrailer = Cartrailer::where('id',$param['id'])->find();
            if(empty($Cartrailer)){
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
            $res = Cartrailer::update($param,['id'=>$param['id']]);
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


  
    public function delcartrailer($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Driver::whereIn('id',$param['ids'])->delete();
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

    public function getescort($param=[]){
        // dump($param);die;
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['name|phone','like','%'.$param['keywords'].'%'];
            }
            $data = Escort::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data);die;
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getescortInfo($param=[]){
        // dump($param);
        try{
            $info = Escort::where('id',$param['id'])->find();
            
        
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addescort($param=[]){
        
        try{
            Db::startTrans();

            // $param['escort_status'] = 0;

            $res = Escort::create($param);
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
    public function editescort($param=[]){
        // dump($param);
        try{
            Db::startTrans();
            $Cartrailer = Escort::where('id',$param['id'])->find();
            if(empty($Cartrailer)){
                throw new \Exception('信息不存在');
            }
 
            $param['status'] = 1;
 
            $res = Escort::update($param,['id'=>$param['id']]);
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


  
    public function delescort($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Escort::whereIn('id',$param['ids'])->delete();
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


    public function getinfoList($param=[]){
        // dump($param);die;
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['name|phone','like','%'.$param['keywords'].'%'];
            }
            $data = Info::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data['data']);die;
            foreach($data['data'] as $key =>  $value){
                // $posts = Carhead::with('heads')->where('id',$value['head_id'])->value('carhead_plate');
                $data['data'][$key]['head_num'] = Carhead::where('id',$value['head_id'])->value('carhead_plate');
                $data['data'][$key]['head_status'] = Carhead::where('id',$value['head_id'])->value('head_status');
                $data['data'][$key]['trailer_num'] = Cartrailer::where('id',$value['trailer_id'])->value('trailer_plate');
                $data['data'][$key]['escort_name'] = Escort::where('id',$value['escort_id'])->value('name');
                $data['data'][$key]['driver_name'] = Db::name('admin')->where('id',$value['driver_id'])->value('username');
                // dump($data['data'][$key]);die;
            }
                
                // dump($posts);die;
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function getinfo($param=[]){
        // dump($param);
        try{
            $info = Info::where('id',$param['id'])->find();
            
        
   
            if(empty($info)){
                return $this->error('信息不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addinfo($param=[]){
        
        try{
            Db::startTrans();
            $head = Info::where('head_id',$param['head_id'])->find();
            if($head){
                return $this->error('车头已存在，请删除历史记录');
            }
            $driver = Info::where('driver_id',$param['driver_id'])->find();
            if($driver){
                return $this->error('驾驶员已存在，请删除历史记录');
            }
            $trailer = Info::where('trailer_id',$param['trailer_id'])->find();
            if($trailer){
                return $this->error('挂车已存在，请删除历史记录');
            }
            $escort = Info::where('escort_id',$param['escort_id'])->find();
            if($escort){
                return $this->error('押运员已存在，请删除历史记录');
            }
            // dump($info);die;
            // // $param['escort_status'] = 0;

            $res = Info::create($param);
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
    public function editinfo($param=[]){
        // dump($param);
        try{
            Db::startTrans();
            $info = Info::where('id',$param['id'])->find();
            if(empty($info)){
                throw new \Exception('信息不存在');
            }
            $head = Info::where('head_id',$param['head_id'])->where('id', '<>', $param['id'])->find();
            if($head){
                return $this->error('车头已存在，请删除历史记录');
            }
            $driver = Info::where('driver_id',$param['driver_id'])->where('id', '<>', $param['id'])->find();
            if($driver){
                return $this->error('驾驶员已存在，请删除历史记录');
            }
            $trailer = Info::where('trailer_id',$param['trailer_id'])->where('id', '<>', $param['id'])->find();
            if($trailer){
                return $this->error('挂车已存在，请删除历史记录');
            }
            $escort = Info::where('escort_id',$param['escort_id'])->where('id', '<>', $param['id'])->find();
            if($escort){
                return $this->error('押运员已存在，请删除历史记录');
            }

            $res = Info::update($param,['id'=>$param['id']]);
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


  
    public function delinfo($param=[]){
        try{
            Db::startTrans();
            $res = Info::whereIn('id',$param['ids'])->delete();
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
    public function getcarlist($param=[]){
        $period_id = null;
        try{

            $data['driver'] = Db::name('admin')->where(['type' => 2, 'driver_status' => 0])->field('id,username,status,type,driver_status')->select()->toArray();
            $driver = Db::name('admin')->where(['type' => 2, 'driver_status' => 0])->field('id,username,status,type,driver_status')->select()->toArray();
            $trailer = [];
            if(isset($param['trailer']) && $param['trailer']){
                $trailer['trailer_status'] = $param['trailer'];
            }
            // dump($param['trailer']);die;
            foreach($driver as $key => $value ){

                $plan = Plan::where(['driver_status' => 1, 'plan_type' => 0])
                    ->where('driver_name', $value['username'])
                    ->order('create_time', 'desc')
                    ->find();
                if ($plan) {
 
                    $period_id = $plan['period_id'];
                  
                } else {
                    
                    $period_id = null;
                   
                }
          
                $data['driver'][$key]['period_id'] = $period_id;
                // dump($driver[$key]);die;
                // dump($data['driver'][$key]);
                // $data['driver'][$key]['period_id'] = $period_id;
                // dump($data['driver'][$key]['period_id']);
            }
            // return $this->success($data);
            $data['escort'] = Escort::where('escort_status','in',[0,1])->field('id,name,escort_status')->select();
            $data['head'] = Carhead::where('head_status','in',[0,3])->field('id,carhead_plate,head_status')->select();
            $data['trailer'] = Cartrailer::where($trailer)->field('id,trailer_plate,trailer_status')->select();
            // $data['admin'] = Escort::where($where)->order(['create_time'=>'desc'])
            //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data);die;
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getfactory($param=[]){
   
        try{
            $where = [];
            $data = Factory::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
   
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getfactoryinfo($param=[]){
        try{
            $Factory = Factory::where('id',$param['id'])->find();

            if(empty($Factory)){
                return $this->error('信息不存在');
            }
            return $this->success($Factory->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function addfactory($param=[]){
        
        try{
            Db::startTrans();

            
            $res = Factory::create($param);
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
    public function editfactory($param=[]){
        try{
            Db::startTrans();
            $Carhead = Factory::where('id',$param['id'])->find();
            if(empty($Carhead)){
                throw new \Exception('信息不存在');
            }

            $res = Factory::update($param,['id'=>$param['id']]);
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
    public function delfactory($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $res = Factory::whereIn('id',$param['ids'])->delete();
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
    public function Pouring($param=[]){
        try{
            Db::startTrans();
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            Cartrailer::where('trailer_plate',$param['old_trailer'])->update(['trailer_status'=> 0]);
            $res = Cartrailer::where('trailer_plate',$param['new_trailer'])->update(['trailer_status'=> 1]);
            // dump($param);die;
            // $res = Factory::whereIn('id',$param['ids'])->delete();
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
    
}
