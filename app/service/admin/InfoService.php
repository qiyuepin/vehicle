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
use GatewayWorker\Lib\Gateway;
use think\worker\Server;
use Overtrue\Pinyin\Pinyin;
// use Overtrue\Pinyin;

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
                $data['data'][$key]['date_status'] = 0;
                $data['data'][$key]['scrapp_status'] = $this->diffdate($value['scrapp_time']);  
                $data['data'][$key]['inspection_status'] = $this->diffdate($value['inspection_time']);
                $data['data'][$key]['validity_status'] = $this->diffdate($value['validity_time']);
                $data['data'][$key]['traffic_status'] = $this->diffdate($value['traffic_time']);
                if($data['data'][$key]['scrapp_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['inspection_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['validity_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['traffic_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
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
            $info['scrapp_status'] = $this->diffdate($info['scrapp_time']);  
            $info['inspection_status'] = $this->diffdate($info['inspection_time']);
            $info['validity_status'] = $this->diffdate($info['validity_time']);
            $info['traffic_status'] = $this->diffdate($info['traffic_time']);
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

            // $param['type'] = 1;
            if (isset($param['carhead_scope']) && is_array($param['carhead_scope'])) {
                $param['carhead_scope'] = implode(',', $param['carhead_scope']);
            } else {
            }
            
            if (isset($param['driving_license']) && is_array($param['driving_license'])) {
                $param['driving_license'] = implode(',', $param['driving_license']);
            } else {
            }
            
            // dump($param);
            $res = Carhead::create($param);
            $info['head_id'] = $res->id; 
            $resinfo = Info::create($info);
            // dump($info);die;
            if(!$resinfo){
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
    public function editcarhead($param=[]){
        try{
            Db::startTrans();
            $Carhead = Carhead::where('id',$param['id'])->find();
            // dump($Carhead);die;
            if(empty($Carhead)){
                throw new \Exception('信息不存在');
            }
 
            $param['type'] = 1;
            
            if(isset($param['driving_license'])){
                $driving_license = array();
                foreach($param['driving_license'] as $value){
                    // dump($value);die;
                    $driving_license[]= $value['url']?$value['url']:$value;
                }
                // dump($driving_license);die;
            }else{

                $driving_license = $param['driving_license'];
            }
            
            if (isset($driving_license) && is_array($driving_license)) {
                $param['driving_license'] = implode(',', $driving_license);
            } else {
            }
            // dump($param);die;
            Db::name('admin_info_notice')->where('carhead_plate',$param['carhead_plate'])->update(['isread'=>1]);
            $exitnotice = Db::name('admin_info_notice')->where('car_id',$param['id'])->where('deal',0)->find();
            $newTimestamp = strtotime('+30 days', time());

            // dump($newTimestamp);die;
            if(isset($exitnotice) && strtotime($param['scrapp_time']) > $newTimestamp){
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('scrapp_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['inspection_time']) > $newTimestamp){
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('inspection_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['validity_time']) > $newTimestamp){
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('validity_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['traffic_time']) > $newTimestamp){
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('traffic_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            // dump($param);die;
            $res = Carhead::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('新增失败');
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
                // scrapp_time,inspection_time,validity_time,frame_time
                // $scrapp_time = \DateTime::createFromFormat('Y-m-d', $value['scrapp_time']);
                $data['data'][$key]['date_status'] = 0;
                $data['data'][$key]['scrapp_status'] = $this->diffdate($value['scrapp_time']);  
                $data['data'][$key]['inspection_status'] = $this->diffdate($value['inspection_time']);
                $data['data'][$key]['validity_status'] = $this->diffdate($value['validity_time']);
                $data['data'][$key]['frame_status'] = $this->diffdate($value['frame_time']);
                if($data['data'][$key]['scrapp_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['inspection_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['validity_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
                if($data['data'][$key]['frame_status']==false){
                    $data['data'][$key]['date_status'] = $data['data'][$key]['date_status']+1;
                }
            }
                
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function diffdate($time){
        $difftime = \DateTime::createFromFormat('Y-m-d', $time);
        $now = new \DateTime();
        $diff = $now->diff($difftime);
        $days = $diff->days;

        $currentTime = time();
        $beforetime = strtotime($time);
        $timeDiff = $beforetime - $currentTime;
        $daysDiff = floor($timeDiff / (60 * 60 * 24));
        // dump($time);
        // dump($difftime);
        // dump($now);
        // dump($daysDiff);
        // die;
        if ($daysDiff < 30) {
            // 日期小于30天
            return false;
        } else {
            // 日期大于等于30天
            return true;
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
            $info['scrapp_status'] = $this->diffdate($info['scrapp_time']);  
            $info['inspection_status'] = $this->diffdate($info['inspection_time']);
            $info['validity_status'] = $this->diffdate($info['validity_time']);
            $info['frame_status'] = $this->diffdate($info['frame_time']);
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
            
            $exitnotice = Db::name('admin_info_notice')->where('car_id',$param['id'])->where('deal',0)->find();
            $newTimestamp = strtotime('+30 days', time());
            // dump($newTimestamp);

            if(isset($exitnotice) && strtotime($param['scrapp_time']) > $newTimestamp){
                // dump('111');
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('trailer_plate',$param['trailer_plate'])->where('deal',0)->where('scrapp_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['inspection_time']) > $newTimestamp){
                // dump('111');
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('trailer_plate',$param['trailer_plate'])->where('deal',0)->where('inspection_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['validity_time']) > $newTimestamp){
                // dump('111');
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('trailer_plate',$param['trailer_plate'])->where('deal',0)->where('validity_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            if(isset($exitnotice) && strtotime($param['frame_time']) > $newTimestamp){
                // dump('111');
                Db::name('admin_info_notice')->where('car_id',$param['id'])->where('trailer_plate',$param['trailer_plate'])->where('deal',0)->where('frame_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            }
            // die;
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
                $data['data'][$key]['head_status'] = Carhead::where('id',$value['head_id'])->value('head_status');
                $data['data'][$key]['trailer_status'] = Cartrailer::where('id',$value['trailer_id'])->value('trailer_status');
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
            // $data = Factory::where($where)->order(['pname_letter'=>'asc','name_letter'=>'asc'])
            //     ->fetchsql()->select();
            //     dump($data);die;
            $data = Factory::where($where)->order(['pname_letter'=>'asc','city_letter'=>'asc','name_letter'=>'asc'])
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
 
            $pinyin = new Pinyin();
            $param['pname_letter'] =  $pinyin->abbr($param['pname']);
            $param['city_letter'] =  $pinyin->abbr($param['city']);
            $param['name_letter'] =  $pinyin->abbr($param['name']);
            // dump($param);die;
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
            $pinyin = new Pinyin();
            $param['pname_letter'] =  $pinyin->abbr($param['pname']);
            $param['city_letter'] =  $pinyin->abbr($param['city']);
            $param['name_letter'] =  $pinyin->abbr($param['name']);

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

  
    // 自定义比较函数  
    
    public function updatenotice(){
        $now = date('Y-m-d', strtotime('+30 days'));
        $now30 = date('Y-m-d',time());

        $trailerlist = Cartrailer::where('scrapp_time', '<', $now)
                ->whereOr('inspection_time', '<', $now)
                ->whereOr('validity_time', '<', $now)
                ->whereOr('frame_time', '<', $now)
                ->field('id,trailer_plate,scrapp_time,inspection_time,validity_time,frame_time')
                ->select()->toArray();
                
        $headlist = Carhead::where('scrapp_time', '<', $now)
                ->whereOr('inspection_time', '<', $now)
                ->whereOr('validity_time', '<', $now)
                ->whereOr('traffic_time', '<', $now)
                ->field('id,carhead_plate,scrapp_time,inspection_time,validity_time,traffic_time')
                ->select()->toArray();
        $mergedData = array_merge($trailerlist, $headlist); 

        $where['deal'] = 0;
        $where['isread'] = 0;
        $newTimestamp = strtotime('+30 days', time());
        $currentTimestamp = time();
        $plate='';
        foreach($mergedData as $key => $value){
            $where['car_id'] = $value['id'];
            if(isset($value['trailer_plate'])){
                $where['trailer_plate'] = $value['trailer_plate'];
                $msg_start = "挂车[";
                $plate=$value['trailer_plate'];
                unset($where['carhead_plate']);
                $where['path'] = "#/info/trailer";
            }else if(isset($value['carhead_plate'])){
                $where['carhead_plate'] = $value['carhead_plate'];
                $msg_start = "车头[";
                $plate=$value['carhead_plate'];
                unset($where['trailer_plate']);
                $where['path'] = "#/info/carhead";
            }
            
            
            // dump($where);die;
            if(strtotime($value['scrapp_time']) > $currentTimestamp && strtotime($value['scrapp_time']) < $newTimestamp){

                $where['scrapp_time'] = $value['scrapp_time'];
                unset($where['inspection_time']);
                unset($where['validity_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']);
                
                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['scrapp_time'];
                    $where['msg'] = $msg_start.$plate."]强制报废日期即将到期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }else if(strtotime($value['scrapp_time']) < $currentTimestamp){
                
                unset($where['inspection_time']);
                unset($where['validity_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']); 
                $where['scrapp_time'] = $value['scrapp_time'];
                $exit = Db::name('admin_info_notice')->where($where)->find();

                if($exit == null){
                    $where['overdue'] = $value['scrapp_time'];
                    $where['msg'] = $msg_start.$plate."]强制报废日期已过期";
                    dump($where);
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }
            if(strtotime($value['inspection_time']) > $currentTimestamp && strtotime($value['inspection_time']) < $newTimestamp){
                unset($where['scrapp_time']);
                unset($where['validity_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']);
                $where['inspection_time'] = $value['inspection_time'];

                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['inspection_time'];
                    $where['msg'] = $msg_start.$plate."]检验有效期即将到期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }else if(strtotime($value['inspection_time']) < $currentTimestamp){
                unset($where['scrapp_time']);
                unset($where['validity_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']);
                $where['inspection_time'] = $value['inspection_time'];
                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['inspection_time'];
                    $where['msg'] = $msg_start.$plate."]检验有效期已过期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }
            
            if(strtotime($value['validity_time']) > $currentTimestamp && strtotime($value['validity_time']) < $newTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']);
                $where['validity_time'] = $value['validity_time'];

                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['validity_time'];
                    $where['msg'] = $msg_start.$plate."]审验有效期即将到期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }else if(strtotime($value['validity_time']) < $currentTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['traffic_time']);
                unset($where['frame_time']);
                $where['validity_time'] = $value['validity_time'];
                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['validity_time'];
                    $where['msg'] = $msg_start.$plate."]审验有效期已过期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }
            
            if(isset($value['traffic_time']) && strtotime($value['traffic_time']) > $currentTimestamp && strtotime($value['traffic_time']) < $newTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['validity_time']);
                $where['traffic_time'] = $value['traffic_time'];

                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['traffic_time'];
                    $where['msg'] = $msg_start.$plate."]交强险有效期即将到期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }else if(isset($value['traffic_time']) && strtotime($value['traffic_time']) < $currentTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['validity_time']);

                $where['traffic_time'] = $value['traffic_time'];
                $exit = Db::name('admin_info_notice')->where($where)->find();

                if($exit == null){
                    $where['overdue'] = $value['traffic_time'];
                    $where['msg'] = $msg_start.$plate."]交强险有效期已过期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }

            if(isset($value['frame_time']) && strtotime($value['frame_time']) > $currentTimestamp && strtotime($value['frame_time']) < $newTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['validity_time']);

                $where['frame_time'] = $value['frame_time'];

                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['frame_time'];
                    $where['msg'] = $msg_start.$plate."]罐检报告有效期即将到期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }else if(isset($value['frame_time']) && strtotime($value['frame_time']) < $currentTimestamp){
                unset($where['scrapp_time']);
                unset($where['inspection_time']);
                unset($where['validity_time']);
                
                $where['frame_time'] = $value['frame_time'];
                $exit = Db::name('admin_info_notice')->where($where)->find();
                if($exit == null){
                    $where['overdue'] = $value['frame_time'];
                    $where['msg'] = $msg_start.$plate."]罐检报告有效期已过期";
                    Db::name('admin_info_notice')->insert($where);
                    Db::commit();
                }
            }
            
        }
    }
    
    // 现在 $trailerlist 是按照最小日期排序的数组
    public function infonotice($param=[]){
        try{
            Db::startTrans();
            $this->updatenotice();
            $list['data'] = Db::name('admin_info_notice')->where('deal','0')->where('isread','0')->field('id as command,id,msg,path,overdue')->select()->toArray();
            $list['count'] = count($list['data'] );
            // dump()
            if(empty($list)){
                return $this->error('信息不存在');
            }
            return $this->success($list);
            // return $this->success($overdue);
            // dump(Driver::whereIn('id',$param['ids'])->find());die;
            $now = date('Y-m-d', strtotime('+30 days'));
            $now30 = date('Y-m-d',time());
            // dump($now);
            // dump($now30);
            // die;
            $trailerlist = Cartrailer::where('scrapp_time', '<', $now)
                    ->whereOr('inspection_time', '<', $now)
                    ->whereOr('validity_time', '<', $now)
                    ->whereOr('frame_time', '<', $now)
                    ->field('id,trailer_plate,scrapp_time,inspection_time,validity_time,frame_time')
                    ->select()->toArray();
            $headlist = Carhead::where('scrapp_time', '<', $now)
                    ->whereOr('inspection_time', '<', $now)
                    ->whereOr('validity_time', '<', $now)
                    ->whereOr('traffic_time', '<', $now)
                    ->field('id,carhead_plate,scrapp_time,inspection_time,validity_time,traffic_time')
                    ->select()->toArray();
            $mergedData = array_merge($trailerlist, $headlist); 
            
            $msg = '';
            foreach ($mergedData as $key => $record) {  
                $dates = [  
                    $record['scrapp_time'] ?? '9999-12-31', // 假设一个未来的日期作为默认值  
                    $record['inspection_time'] ?? '9999-12-31',  
                    $record['validity_time'] ?? '9999-12-31',  
                    $record['frame_time'] ?? '9999-12-31',  
                    $record['traffic_time'] ?? '9999-12-31', 
                ];  
                
                $minDate = min($dates);   
                $minDates[] = $minDate;  
                $mergedData[$key]['minDates'] = $minDate;  
                $count = 0;
                $mergedData[$key]['msg']=[];
                if($record['scrapp_time']<$now){
                    $count = $count +1;
                    if(strtotime($record['scrapp_time']) > time()){
                        $msg = "强制报废日期即将到期";
                    }else{
                        $msg = "强制报废日期已过期";
                    }
                    $mergedData[$key]['msg'][]=$msg;
                }
                if($record['inspection_time']<$now){
                    $count = $count +1;
                    if(strtotime($record['inspection_time']) > time()){
                        $msg = "检验有效期即将到期";
                    }else{
                        $msg = "检验有效期已过期";
                    }
                    $mergedData[$key]['msg'][]=$msg;
                }
                // if($record['inspection_time']<$now){
                //     $count = $count +1;
                // }
                // if($record['validity_time']<$now){
                //     $count = $count +1;
                // }
                // if($record['frame_time']<$now){
                //     $count = $count +1;
                // }
                // if($record['traffic_time']<$now){
                //     $count = $count +1;
                // }
                $mergedData[$key]['msg'] = $msg; 
            } 
            usort($mergedData, function($a, $b) {  
                return strtotime($a['minDates']) - strtotime($b['minDates']);  
            });  
            // dump($mergedData);die;
            foreach ($trailerlist as $key => $record) {  
                $dates = [  
                    $record['scrapp_time'] ?? '9999-12-31', // 假设一个未来的日期作为默认值  
                    $record['inspection_time'] ?? '9999-12-31',  
                    $record['validity_time'] ?? '9999-12-31',  
                    $record['frame_time'] ?? '9999-12-31',  
                ];  
                
                $minDate = min($dates);   
                $minDates[] = $minDate;  
                $trailerlist[$key]['minDates'] = $minDate;  
            }  
 
            foreach ($headlist as $key => $record) {  
                $dates = [  
                    $record['scrapp_time'] ?? '9999-12-31', // 假设一个未来的日期作为默认值  
                    $record['inspection_time'] ?? '9999-12-31',  
                    $record['validity_time'] ?? '9999-12-31',  
                    $record['traffic_time'] ?? '9999-12-31',  
                ];  
                 
                $minDate = min($dates);  
                $headlist[$key]['minDates'] = $minDate;  
            }  
            
            $mergedData = array_merge($trailerlist, $headlist); 
            usort($mergedData, function($a, $b) {  
                return strtotime($a['minDates']) - strtotime($b['minDates']);  
            });  
            // 现在 $trailerlist 是按照最小时间排序的数组  
            // print_r($trailerlist); 
            dump($mergedData);
           
            die;
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
    public function bind($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(['type' => 'connect', 'message' => 'WebSocket connected']));
    }

    public function send($client_id, $data)
    {
        Gateway::sendToAll(json_encode(['type' => 'message', 'message' => $data]));
    }

    // public function bind()
    // {
    //     $this->initializeGatewayClient();

    //     while (true) {
    //         // 获取客户端发送的数据
    //         $data = Websocket::recv(Gateway::getSender());

    //         if ($data === '') {
    //             Gateway::closeCurrentClient();
    //             continue;
    //         }
    //         $message = json_decode($data, true);

  
    //     }
    // }

    // private function initializeGatewayClient()
    // {
    //     Gateway::$registerAddress = '127.0.0.1:1236';
    // }
    // public function onClose($client_id)
    // {
    //     Gateway::sendToAll(json_encode(['type' => 'disconnect', 'message' => 'WebSocket disconnected']));
    // }
}
