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
use app\model\Driver;

/**
 * AdminService
 * created on 2021/11/2 17:55
 * created by chengzhigang
 */
class AdminService extends BaseService
{

    /**
     * @desc 获取管理员列表
     * @method getList
     * @param array $param
     * @author chengzhigang
     * @time 2021/11/2 17:56
     */
    public function getList($param=[]){
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['username|nickname|phone|email','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status']){
                $where[] = ['status','=',$param['status']];
            }
            $data = Admin::where($where)->field(['id','username','nickname','phone','email','avatar','status','login_ip','login_time','create_time'])
                ->where(['type'=>'1'])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 更改管理员状态
     * @method changeStatus
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/3 10:54
     */
    public function changeStatus($param=[]){
        try{
            Admin::update(['status'=>$param['status']],['id'=>$param['id']]);
            return $this->success();
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取角色
     * @method getRole
     * @return array
     * @author chengzhigang
     * @time 2021/11/3 16:37
     */
    public function getRole(){
        try{
            $data = AuthGroup::where('status',1)->field(['id','title','type'])->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 新增管理员
     * @method addAdmin
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 17:04
     */
    public function addAdmin($param=[]){
        try{
            Db::startTrans();
            $param['halt'] = getNonceStr();
            $param['password'] = md5($param['halt'].$param['password'].$param['halt']);
            // dump($param);die;
            $param['type'] = 1;
            $param['sign'] = $param['autograph'];
            $param['nickname'] = $param['username'];
            unset($param['id']);
            $res = Admin::create($param);
            if(!$res){
                throw new \Exception('新增管理员失败');
            }
            $uid = $res -> id;
            foreach($param['group'] as $group_id){
                $res = AuthGroupAccess::create(['uid'=>$uid,'group_id'=>$group_id]);
                if(!$res){
                    throw new \Exception('新增关联表失败');
                }
            }
            Db::commit();
            return $this->success([],'新增成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function adddriverAdmin($param=[]){
        // dump($param['group']);
        // foreach($param['group'] as $group_id){

        //     dump($group_id);
        // }
        try{
            Db::startTrans();
            $param['halt'] = getNonceStr();
            $param['password'] = md5($param['halt'].$param['password'].$param['halt']);
            // $param['sign'] = $param['autograph'];
            unset($param['id']);
            $res = Admin::create($param);

            if(!$res){
                throw new \Exception('新增管理员失败');
            }
            $uid = $res -> id;
        
            // 确保 'group' 字段存在且为数组
            foreach($param['group'] as $group_id){
                $res = AuthGroupAccess::create(['uid'=>$uid,'group_id'=>$group_id]);
                if(!$res){
                    throw new \Exception('新增关联表失败');
                }
            }
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取管理员
     * @method getInfo
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 18:09
     */
    public function getInfo($param=[]){
        try{
            $info = Admin::with(['roles'])->where('id',$param['id'])->field(['id','username','nickname','phone','email','avatar','sign'])->find();
            if(empty($info)){
                return $this->error('管理员不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 编辑管理员
     * @method editAdmin
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 17:04
     */
    public function editAdmin($param=[]){
        
        try{
            Db::startTrans();
            $admin = Admin::where('id',$param['id'])->find();
            if(empty($admin)){
                throw new \Exception('管理员不存在');
            }
            if($param['password']){
                $param['password'] = md5($admin['halt'].$param['password'].$admin['halt']);
            }else{
                unset($param['password']);
            }
            $param['sign'] = $param['autograph'];
            $param['nickname'] = $param['username'];
            $res = Admin::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('新增管理员失败');
            }
            $res = AuthGroupAccess::where('uid',$param['id'])->delete();
            if(!$res){
                throw new \Exception('删除关联表失败');
            }
            foreach($param['group'] as $group_id){
                $res = AuthGroupAccess::create(['uid'=>$param['id'],'group_id'=>$group_id]);
                if(!$res){
                    throw new \Exception('新增关联表失败');
                }
            }
            Cache::delete('adminInfo:'.$param['id']);
            Db::commit();
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 删除管理员
     * @method deleteAdmin
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/5 11:09
     */
    public function deleteAdmin($param=[]){
        try{
            Db::startTrans();
            //删除管理员
            $res = Admin::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除管理员失败');
            }
            //删除管理员群组绑定关系
            $res = AuthGroupAccess::whereIn('uid',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除关联表失败');
            }
            Cache::delete('adminInfo:'.$param['id']);
            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取登录日志
     * @method getLoginLog
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 16:10
     */
    public function getLoginLog($param=[]){
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['b.username|b.nickname','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['time'])&&$param['time']){
                $start = $param['time'][0];
                $end = $param['time'][1];
                $where[] = ['a.login_time','between',"$start,$end"];
            }
            $data = AdminLoginLog::alias('a')->join('admin b','a.uid=b.id')->where($where)->field(['b.username','b.nickname','b.avatar','a.login_ip','a.login_time'])
                ->order(['login_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取操作日志
     * @method getHandleLog
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 16:10
     */
    public function getHandleLog($param=[]){
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['b.username|b.nickname','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['time'])&&$param['time']){
                $start = $param['time'][0];
                $end = $param['time'][1];
                $where[] = ['a.create_time','between',"$start,$end"];
            }
            $data = AdminActionLog::alias('a')->join('admin b','a.uid=b.id')->where($where)
                ->field(['b.username','b.nickname','b.avatar','a.id','a.route','a.ip','a.param','a.method','a.status','a.error','a.create_time'])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getdriverList($param=[]){
   
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['username|nickname|phone|email','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status']){
                $where[] = ['status','=',$param['status']];
            }
            $data = Admin::where($where)->field(['id','username','phone','card_front','card_back','driver_card_front','driver_card_back','cert_front','cert_back','id_card_num','dirver_card_num','cert_card_num','employ_time','status','login_ip','login_time','create_time'])
                ->where(['type'=>'2'])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
               
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取管理员
     * @method getInfo
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 18:09
     */
    public function getdriverInfo($param=[]){
        try{
            $info = Admin::with(['roles'])->where('id',$param['id'])->field(['id','username','avatar','phone','type','card_front','card_back','driver_card_front','driver_card_back','cert_front','cert_back','id_card_num','dirver_card_num','cert_card_num','employ_time','driver_status'])->find();
            if(empty($info)){
                return $this->error('管理员不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 编辑管理员
     * @method editAdmin
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 17:04
     */
    public function editdriverAdmin($param=[]){
        try{
            Db::startTrans();
            $admin = Admin::where('id',$param['id'])->find();
            if(empty($admin)){
                throw new \Exception('管理员不存在');
            }
            if($param['password']){
                $param['password'] = md5($admin['halt'].$param['password'].$admin['halt']);
            }else{
                unset($param['password']);
            }
            // $param['sign'] = $param['autograph'];
            $res = Admin::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('新增管理员失败');
            }
            $res = AuthGroupAccess::where('uid',$param['id'])->delete();
            if(!$res){
                throw new \Exception('删除关联表失败');
            }
            foreach($param['group'] as $group_id){
                $res = AuthGroupAccess::create(['uid'=>$param['id'],'group_id'=>$group_id]);
                if(!$res){
                    throw new \Exception('新增关联表失败');
                }
            }
            Cache::delete('adminInfo:'.$param['id']);
            Db::commit();
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getregulation($param=[]){

        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['regulation_truth|regulation_place','like','%'.$param['keywords'].'%'];
            }
            $data = Driver::where($where)->field(['id','regulation_time','regulation_place','regulation_truth','regulation_code','regulation_deal','regulation_remark','create_time'])
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
    public function getregulationInfo($param=[]){
        // dump($param);die;
        try{
            $info = Driver::where('id',$param['id'])->field(['id','regulation_time','regulation_place','regulation_truth','regulation_code','regulation_deal','regulation_remark','create_time'])->find();
            if(empty($info)){
                return $this->error('违章不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function addregulation($param=[]){
        
        try{
            Db::startTrans();

            $param['type'] = 1;
            $res = Driver::create($param);
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

    public function editregulation($param=[]){
        try{
            Db::startTrans();
            $regulation = Driver::where('id',$param['id'])->find();
            if(empty($regulation)){
                throw new \Exception('违章不存在');
            }

            $res = Driver::update($param,['id'=>$param['id']]);
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
    public function delregulation($param=[]){
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
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['accident_place|accident_respons','like','%'.$param['keywords'].'%'];
            }
            $data = Driver::where($where)->field(['id','accident_time','accident_place','accident_des','accident_respons','accident_kind','accident_loss','accident_remark'])
                ->where(['driver_id'=>$param['id']])
                ->where(['type'=>2])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();

            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }

        /*try{
            $info = Driver::where('driver_id',$param['id'])->find();
            if(empty($info)){
                return $this->error('事故不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }*/
    }
    public function getaccidentInfo($param=[]){

        try{
            $info = Driver::where('id',$param['id'])->field(['id','accident_time','accident_place','accident_des','accident_respons','accident_kind','accident_loss','accident_remark'])->find();
            if(empty($info)){
                return $this->error('事故不存在');
            }
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function addaccident($param=[]){
        
        try{
            Db::startTrans();
            // dump($param);
            // $info = Driver::where('driver_id',$param['driver_id'])->find();
            // dump($info);
            $param['type'] = 2;
            $res = Driver::create($param);
            // dump($res);
            if(!$res){
                throw new \Exception('新增事故失败');
            }
     
            Db::commit();
            return $this->success([],'新增成功');
    
            
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
    public function editaccident($param=[]){
        try{
            Db::startTrans();
            $regulation = Driver::where('id',$param['id'])->find();
            if(empty($regulation)){
                throw new \Exception('事故不存在');
            }

            $res = Driver::update($param,['id'=>$param['id']]);
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
    public function delaccident($param=[]){
        try{
            Db::startTrans();
//            dump($param);
            $res = Driver::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除事故失败');
            }

            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }
}
