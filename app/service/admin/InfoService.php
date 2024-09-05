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
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['carhead_plate|carhead_brand','like','%'.$param['keywords'].'%'];
            }

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
                $data['data'][$key]['scrapp_status'] = $value['scrapp_time']?$this->diffdate($value['scrapp_time']):true;  
                $data['data'][$key]['inspection_status'] = $value['inspection_time']?$this->diffdate($value['inspection_time']):true;
                $data['data'][$key]['validity_status'] = $value['validity_time']?$this->diffdate($value['validity_time']):true;
                $data['data'][$key]['traffic_status'] = $value['traffic_time']?$this->diffdate($value['traffic_time']):true;
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

            //车体照片 start
            $carbody_picture = explode(',',$info['carbody_picture']);
            $carbody_pictures = [];
            $info['carbody_pictures'] = [];
            foreach ($carbody_picture as $key => $value) {
                $carbody_pictures[$key]['name'] = $key;
                $carbody_pictures[$key]['url'] = $value;
            }
            //车体照片 end

            $carhead_scope = Db::name("admin_carscope")->where('id','in', $info['carhead_scope'])->field('name')->select();
            $items = $carhead_scope->toArray();
            $itemNames = array_column($items, 'name');
            $info['carhead_scope_name'] = implode(', ', $itemNames);

            foreach ($driving_license as $key => $value) {
                $driving_licenses[$key]['name'] = $key;
                $driving_licenses[$key]['url'] = $value;
            }
            $info['scrapp_status'] = $info['scrapp_time']?$this->diffdate($info['scrapp_time']):true;  
            $info['inspection_status'] = $info['inspection_time']?$this->diffdate($info['inspection_time']):true;
            $info['validity_status'] = $info['validity_time']?$this->diffdate($info['validity_time']):true;
            $info['traffic_status'] = $info['traffic_time']?$this->diffdate($info['traffic_time']):true;
            $info['driving_licenses'] = $driving_licenses;
            //车体照片 start
            $info['carbody_pictures'] = $carbody_pictures;
            //车体照片 end
   
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

            if (isset($param['carbody_picture']) && is_array($param['carbody_picture'])) {
                $param['carbody_picture'] = implode(',', $param['carbody_picture']);
            } else {
            }
            unset($param['id']);
            // dump($param);

            
            $res = Carhead::create($param);
            $info['head_id'] = $res->id;
            $info['head_num'] = $res->carhead_plate;
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
            
            if(empty($Carhead)){
                throw new \Exception('信息不存在');
            }
            $param['inspection_time'] = $param['inspection_time']!=''?$param['inspection_time']:null;
            $param['validity_time'] = $param['validity_time']!=''?$param['validity_time']:null;
            $param['traffic_time'] = $param['traffic_time']!=''?$param['traffic_time']:null;
            $param['regist_time'] = $param['regist_time']!=''?$param['regist_time']:null;
            $param['scrapp_time'] = $param['scrapp_time']!=''?$param['scrapp_time']:null;
            $param['type'] = 1;
    
            //车体照片 start
            if(is_array($param['carbody_picture']) && count($param['carbody_picture']) > 0 && is_array($param['carbody_picture'][0])){
       
                $carbody_picture = array();
                foreach($param['carbody_picture'] as $key => $value){
                    $carbody_picture[]= isset($value['url'])?$value['url']:$value;
                }
            }else{

                $carbody_picture = $param['carbody_picture'];
            }
            
            if (isset($carbody_picture) && is_array($carbody_picture)) {
                $param['carbody_picture'] = implode(',', $carbody_picture);
            } else {
            }
            //车体照片 end
    
            if(is_array($param['driving_license']) && count($param['driving_license']) > 0 && is_array($param['driving_license'][0])){
            
                $driving_license = array();
                foreach($param['driving_license'] as $key => $value){
                    // $driving_license[]= $value['url'];
                    $driving_license[]= isset($value['url'])?$value['url']:$value;
                }
            }else{
                
                $driving_license = $param['driving_license'];
            }
          
            if (isset($driving_license) && is_array($driving_license)) {
                $param['driving_license'] = implode(',', $driving_license);
            } else {
            }
            
            //经营范围 start
            if (isset($param['carhead_scope']) && is_array($param['carhead_scope'])) {
                $param['carhead_scope'] = implode(',', $param['carhead_scope']);
            } else {

            }
            
            //经营范围 end

            // Carhead::update($param,['id'=>$param['id']]);
         
            
   
            // Db::name('admin_info_notice')->where('carhead_plate',$param['carhead_plate'])->update(['isread'=>1]);
            // $exitnotice = Db::name('admin_info_notice')->where('car_id',$param['id'])->where('deal',0)->find();
            // $newTimestamp = strtotime('+30 days', time());

            // // dump($newTimestamp);die;
            // if(isset($exitnotice) && strtotime($param['scrapp_time']) > $newTimestamp){
            //     Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('scrapp_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            // }
            // if(isset($exitnotice) && strtotime($param['inspection_time']) > $newTimestamp){
            //     Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('inspection_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            // }
            // if(isset($exitnotice) && strtotime($param['validity_time']) > $newTimestamp){
            //     Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('validity_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            // }
            // if(isset($exitnotice) && strtotime($param['traffic_time']) > $newTimestamp){
            //     Db::name('admin_info_notice')->where('car_id',$param['id'])->where('carhead_plate',$param['carhead_plate'])->where('deal',0)->where('traffic_time','<>',null)->update(['deal'=>1,'isread'=>1]);
            // }
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
            foreach($param['ids'] as $key =>  $value){
                $exit = Info::where('head_id',$value)->value('id');
                if($exit){
  
                    $res = Info::destroy($exit);
                    $delhead =  Carhead::destroy($value);

                    if(!$delhead){
                        throw new \Exception('删除失败');
                    }
                }
            }

            // $res = Carhead::whereIn('id',$param['ids'])->delete();
            // if(!$res){
            //     throw new \Exception('删除失败');
            // }

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
                
            foreach ($data['data'] as $key => $value) {
         
                $trailer_scope = Db::name("admin_carscope")->where('id','in', $value['trailer_scope'])->field('name')->select();
                $items = $trailer_scope->toArray();

                $itemNames = array_column($items, 'name');
                
                $data['data'][$key]['trailer_scope'] = implode(', ', $itemNames);
                if($value['driving_license']!=null){
                    $driving_license = explode(',', $value['driving_license']);
                    foreach ($driving_license as $k => $val){
                        $data['data'][$key]['driving_licenses'][$k]['url'] = $val;
                    }
                }
                else{
                    
                    $data['data'][$key]['driving_licenses'][0]['url']='';
                }
                // dump($value['scrapp_time']);
                // scrapp_time,inspection_time,validity_time,frame_time
                // $scrapp_time = \DateTime::createFromFormat('Y-m-d', $value['scrapp_time']);
                $data['data'][$key]['date_status'] = 0;
                $data['data'][$key]['scrapp_status'] = $value['scrapp_time']?$this->diffdate($value['scrapp_time']):true;
                $data['data'][$key]['inspection_status'] = $value['inspection_time']?$this->diffdate($value['inspection_time']):true;
                $data['data'][$key]['validity_status'] = $value['validity_time']?$this->diffdate($value['validity_time']):true;
                $data['data'][$key]['frame_status'] = $value['frame_time']?$this->diffdate($value['frame_time']):true;
                // dump($data);die;
                
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
            $info['scrapp_status'] = $info['scrapp_time']?$this->diffdate($info['scrapp_time']):true;  
            $info['inspection_status'] = $info['inspection_time']?$this->diffdate($info['inspection_time']):true;;
            $info['validity_status'] = $info['validity_time']?$this->diffdate($info['validity_time']):true;;
            $info['frame_status'] = $info['frame_time']?$this->diffdate($info['frame_time']):true;;
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
            
            if($param['product_quantity'] == '0'){
                $param['trailer_status'] = 0;
            }else if($param['product_quantity'] > 0){
                $param['trailer_status'] = 1;
            }
            $param['regist_time'] = $param['regist_time'] != ''?$param['regist_time']:null;
            $param['scrapp_time'] = $param['scrapp_time'] != ''?$param['scrapp_time']:null;
            $param['inspection_time'] = $param['inspection_time'] != ''?$param['inspection_time']:null;
            $param['validity_time'] = $param['validity_time'] != ''?$param['validity_time']:null;
            $param['frame_time'] = $param['frame_time'] != ''?$param['frame_time']:null;
            $param['type'] = 1;
            if (isset($param['trailer_scope']) && is_array($param['trailer_scope'])) {
                $param['trailer_scope'] = implode(',', $param['trailer_scope']);
            } else {
            }
            // dump($param['driving_license']);die;
            if(is_array($param['driving_license']) && count($param['driving_license']) > 0 && is_array($param['driving_license'][0])){
       
                $driving_license = array();
                foreach($param['driving_license'] as $key => $value){
                    // $driving_license[]= $value['url'];
                    $driving_license[]= isset($value['url'])?$value['url']:$value;
                }
            }else{

                $driving_license = $param['driving_license'];
            }
            
            if (isset($driving_license) && is_array($driving_license)) {
                $param['driving_license'] = implode(',', $driving_license);
            } else {
            }
            
            // dump($param['product_quantity']);die;
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
            foreach($param['ids'] as $key =>  $value){
                $exit = Info::where('trailer_id',$value)->value('trailer_num');
                if($exit){
                    return $this->error('人员车辆匹配里面有"'.$exit.'"，无法删除');
                }
            }
            // dump(Cartrailer::whereIn('id',$param['ids'])->find());die;
            $res = Cartrailer::whereIn('id',$param['ids'])->delete();
            // Cartrailer::whereIn('id',$param['ids'])->select();
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

    public function getescort($param=[]){
        // dump($param['escort_status']);die;
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['name|phone','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['escort_status'])&&$param['escort_status'] != ""){
                $where['escort_status'] = $param['escort_status'];
            }
            // dump($where);die;
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

            if(isset($param['employ_time'])){
                $param['employ_time'] = $param['employ_time']!=''?$param['employ_time']:NULL;
            }
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
            if(isset($param['employ_time'])){
                $param['employ_time'] = $param['employ_time']!=''?$param['employ_time']:NULL;
            }
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


    public function getinfoList($param=[]){
        // dump($param);die;
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|trailer_num|head_num','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status'] != ""){
                $where['head_status'] = $param['status'];
            }
            // dump($where);die; 
            $data = Info::withJoin(['headid'])
                    ->where($where)
                    ->order('info.create_time', 'desc')
                    ->field("info.id,info.head_id,info.trailer_id,info.escort_id,info.driver_id,info.driver_id,info.update_time,info.create_time")
                    ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                    // dump($data);die;
            // $data1 = Db::table('admin_careinfo')
            //         ->alias('i')
            //         ->join('admin_carhead c', 'i.head_id = c.id', 'LEFT') // 根据实际表名和字段名调整
            //         ->where($where)
            //         ->order('i.create_time', 'desc')
            //         ->fetchsql()
            //         ->select();dump($data1);die;   
            // $data = Info::where($where)->order(['create_time'=>'desc'])
            //     ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                // dump($data['data']);die;carhead
            foreach($data['data'] as $key =>  $value){
                // $posts = Carhead::with('heads')->where('id',$value['head_id'])->value('carhead_plate');
                // $data['data'][$key]['head_num'] = Carhead::where('id',$value['head_id'])->value('carhead_plate');
                $data['data'][$key]['head_status'] = Carhead::where('id',$value['head_id'])->value('head_status');
                // $data['data'][$key]['trailer_num'] = Cartrailer::where('id',$value['trailer_id'])->value('trailer_plate');
                $data['data'][$key]['escort_name'] = Escort::where('id',$value['escort_id'])->value('name');
                // $data['data'][$key]['driver_name'] = Db::name('admin')->where('id',$value['driver_id'])->value('username');
                // $data['data'][$key]['head_status'] = Carhead::where('id',$value['head_id'])->value('head_status');
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
            $driver = Admin::where('id',$info['driver_id'])->value('status');
            if($driver == 1){
                $info['driver_name']='';
            }
            $escort = Escort::where('id',$info['escort_id'])->value('status');
            if(!$escort){
                $info['escort_name']='';
            }
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
            // $head = Info::where('head_id',$param['head_id'])->where('id', '<>', $param['id'])->find();
            // if($head){
            //     return $this->error('车头已存在，请删除历史记录');
            // }
            // $driver = Info::where('driver_id',$param['driver_id'])->where('id', '<>', $param['id'])->find();
            // if($driver){
            //     return $this->error('驾驶员已存在，请删除历史记录');
            // }
            // $trailer = Info::where('trailer_id',$param['trailer_id'])->where('id', '<>', $param['id'])->find();
            // if($trailer){
            //     return $this->error('挂车已存在，请删除历史记录');
            // }
            // $escort = Info::where('escort_id',$param['escort_id'])->where('id', '<>', $param['id'])->find();
            // if($escort){
            //     return $this->error('押运员已存在，请删除历史记录');
            // }
            if (isset($param['trailer_id']))
            {
                    
                //输入的车头是否存在于人员车辆匹配中
                $exit_trailer_num = Info::where('trailer_id',$param['trailer_id'])->find();
                if($info['trailer_id'] != $param['trailer_id'] && $exit_trailer_num){
                    //将原有$param['trailer_num']的info信息置为空
                    Info::where('id',$exit_trailer_num['id'])->update(['trailer_num'=>null,'trailer_id'=>null]);
                }

                $param['trailer_num'] = Cartrailer::where('id',$param['trailer_id'])->value('trailer_plate');

            }
            
            if (isset($param['escort_id'])){
                
             
                //输入的车头是否存在于人员车辆匹配中
                $exit_escort_name = Info::where('escort_id',$param['escort_id'])->find();
                if($info['escort_id'] != $param['escort_id'] && $exit_escort_name){
                    //将原有$param['escort_name']的info信息置为空
                    Info::where('id',$exit_escort_name['id'])->update(['escort_name'=>null,'escort_id'=>null]);
                }
                // dump(Escort::where('id',$param['escort_id'])->value('escort_name'));die;
                $param['escort_name'] = Escort::where('id',$param['escort_id'])->value('name');
                // dump($param['escort_id']);die;
            }
            // dump($param);die;
            if (isset($param['driver_id'])){
                
               
                //输入的车头是否存在于人员车辆匹配中
                $exit_driver_name = Info::where('driver_id',$param['driver_id'])->find();
                if($info['driver_id'] != $param['driver_id'] && $exit_driver_name){
                    //将原有$param['driver_name']的info信息置为空
                    Info::where('id',$exit_driver_name['id'])->update(['driver_name'=>null,'driver_id'=>null]);
                }
                $param['driver_name'] = Db::name('admin')->where('id',$param['driver_id'])->value('username');
            }
            // $param['driver_name'] = Db::name('admin')->where('id',$param['driver_id'])->value('username');
            // $param['head_num'] = Carhead::where('id',$param['head_id'])->value('carhead_plate');
            // $param['trailer_num'] = Cartrailer::where('id',$param['trailer_id'])->value('trailer_plate');
            $res = Info::update($param,['id'=>$param['id']]);
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
            
            $data['driver'] = Admin::where(['type' => 2,'status' => 2])->field('id,username,status,type,driver_status')->select()->toArray();
            $driver = Admin::where(['type' => 2,'status' => 2])->field('id,username,status,type,driver_status')->select()->toArray();
            $trailer = [];
            if(isset($param['trailer']) && $param['trailer']){
                $trailer['trailer_status'] = $param['trailer'];
                $data['trailer'] = Cartrailer::where($trailer)->field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
            }
            // if(isset($param['id']) && $param['id']){

            //     $trailer['product_name'] = $param['product_name'];
            //     $trailer['id'] = array('<>',$param['id']);

            // }

            // dump($trailer);die;

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
            
            $data['escort'] = Escort::where('escort_status','in',[0,1])->field('id,name,escort_status')->select();
            $data['head'] = Carhead::where('head_status','in',[0,1,2,3])->field('id,carhead_plate,head_status')->select();
            // $data['trailer'] = Cartrailer::where($trailer)->field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
            
            if(isset($param['trailer_id']) && $param['trailer_id']){
                $info_trailer = Info::where('trailer_id',$param['trailer_id'])->find();
                $data['trailer'] = Cartrailer::alias('t')->join('admin_careinfo i','t.id=i.trailer_id')
                ->join('admin_carhead h','h.id=i.head_id')->where('h.head_status',0)->field('t.id,t.trailer_plate,t.trailer_status,t.product_name,t.product_quantity')->select();
                $trailerlist = Cartrailer::where($trailer)->field('id,trailer_plate,trailer_status,product_name,product_quantity')->select()->toArray();
                $newtrailer = [];
            
                if($trailerlist){
                    foreach ($trailerlist as $key=>$item) {
                    
                        $info_trailer = Info::where('trailer_id',$item['id'])->find();
                        $trailer_id = Info::where('id',$param['trailer_id'])->value('trailer_id');
                        if ($info_trailer) {
                            $info_head = Info::where('trailer_id',$item['id'])->value('head_id');
                            $head_status = Carhead::where('id',$info_head)->value('head_status');
                            if($head_status == 0){
                                $newtrailer[$key] = $item;
                            }elseif($item['id'] == $trailer_id){
                                // dump($item['id']);
                                $newtrailer[$key] = $item;
                            }
                        }else{
                            $newtrailer[$key] = $item;
                        }
                    }
                    $data['trailer'] = $newtrailer;
                }
            }elseif(isset($param['trailer_num']) && $param['trailer_num']){
                
                $nowtrailer = Cartrailer::where('trailer_plate',$param['trailer_num'])->find();
                if($nowtrailer['trailer_status'] == 1){
                    // $data['trailer'] = Cartrailer::field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
                    $data['trailer'] = Cartrailer::where('product_name',$nowtrailer['product_name'])->whereOr('trailer_status',0)->field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
                }else{
                    $data['trailer'] = Cartrailer::field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
                }
                
            }else{
                $data['trailer'] = Cartrailer::where($trailer)->field('id,trailer_plate,trailer_status,product_name,product_quantity')->select();
            }
            
            
            // dump($newtrailer);die;
            if(isset($param['id']) && $param['id']){
                $data['trailer'] = Cartrailer::where('id', '<>', $param['id'])
                    ->where(function($query) use ($param) {
                        $query->where('product_name', '=', $param['product_name'])
                            ->whereOr('trailer_status', '=', 0);
                    })
                    ->field('id,trailer_plate,trailer_status,product_name,product_quantity')
                    ->select();
            }
           
            // dump($data['trailer']);die;
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
            // $where = [];
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['city|name','like','%'.$param['keywords'].'%'];
            }
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
            $oldtrailer= Cartrailer::where('id',$param['old_trailer'])->find();
            $newtrailer= Cartrailer::where('id',$param['new_trailer'])->find();
            if($newtrailer['product_name'] && $newtrailer['product_name'] != $param['product_name']){
                // throw new \Exception('车辆产品名称不一致');
                return $this->error('车辆产品名称不一致');
            }
            else{
                $oldproduct_quantity = $oldtrailer['product_quantity'] - $param['product_quantity'];
                $old['product_quantity'] = $oldproduct_quantity>0.3 ? $oldproduct_quantity : 0;
                $old['trailer_status'] = $oldproduct_quantity>0.3 ? 1 : 0;
                $new['product_quantity'] = $param['all_product_quantity'];
                $new['product_name'] = $param['product_name'];
                $new['trailer_status'] = 1;
            }
            // $test = Cartrailer::where('trailer_plate',$param['old_trailer'])->fetchsql()->update($old);
            // dump($test);die;
            Cartrailer::where('id',$param['old_trailer'])->update($old);
            $res = Cartrailer::where('id',$param['new_trailer'])->update($new);
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

    public function getproduct(){
        try{
            $data = Db::name("admin_product_master")->select()->toArray();
            // dump($data);die;
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getheadbranch(){
        try{
            $data = Db::name("admin_van_branch_master")->select()->toArray();
            // dump($data);die;
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getdischarge(){
        try{
            $data = Db::name("admin_discharge_level_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getpowersupply(){
        try{
            $data = Db::name("admin_powersupply_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function gettrailerbranch(){
        try{
            $data = Db::name("admin_trailer_branch_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function gettrailermaterial(){
        try{
            $data = Db::name("admin_trailer_material_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function gettrailerdes(){
        try{
            $data = Db::name("admin_trailer_designcode_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function gettrailerkeepwarm(){
        try{
            $data = Db::name("admin_trailer_keepwarm_master")->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
}
