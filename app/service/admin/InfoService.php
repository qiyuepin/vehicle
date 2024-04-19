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
            if (isset($param['carhead_scope']) && is_array($param['carhead_scope'])) {
                $param['carhead_scope'] = implode(',', $param['carhead_scope']);
            } else {
            }
            
            if (isset($param['driving_license']) && is_array($param['driving_license'])) {
                $param['driving_license'] = implode(',', $param['driving_license']);
            } else {
            }
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
    public function gettrailer($param=[]){

        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['regulation_truth|regulation_place','like','%'.$param['keywords'].'%'];
            }
            $data = Cartrailer::where($where)->field(['id','regulation_time','regulation_place','regulation_truth','regulation_code','regulation_deal','regulation_remark','create_time'])
                ->where(['driver_id'=>$param['id']])
                ->where(['type'=>1])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function gettrailerInfo($param=[]){
        // dump($param);die;
        try{
            $info = Cartrailer::where('id',$param['id'])->field(['id','regulation_time','regulation_place','regulation_truth','regulation_code','regulation_deal','regulation_remark','create_time'])->find();
            if(empty($info)){
                return $this->error('违章不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function addtrailer($param=[]){
        
        try{
            Db::startTrans();

            $param['type'] = 1;
            $res = Cartrailer::create($param);
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

    public function edittrailer($param=[]){
        try{
            Db::startTrans();
            $regulation = Cartrailer::where('id',$param['id'])->find();
            if(empty($regulation)){
                throw new \Exception('违章不存在');
            }

            $res = Cartrailer::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('失败');
            }

            Db::commit();
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function deltrailer($param=[]){
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

    public function getaccident($param=[]){
        try{
            $info = Cartrailer::where('driver_id',$param['id'])->find();
            if(empty($info)){
                return $this->error('事故不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

}
