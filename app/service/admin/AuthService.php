<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AuthGroup;
use app\model\AuthRule;
use app\service\BaseService;
use think\facade\Db;
use think\facade\Event;
use think\wenhainan\Auth as Authority;
use app\traits\TokenTrait;

/**
 * AuthService
 * created on 2021/10/28 14:18
 * created by chengzhigang
 */
class AuthService extends BaseService
{
    use TokenTrait;

    /**
     * @desc 登录
     * @method login
     * @param $param
     * @return array
     * @author chengzhigang
     * @time 2021/10/28 15:08
     */
    public function login(array $param=[]){
        try{
            $admin = Admin::where('username',$param['username'])->field(['id','type','username','nickname','email','phone','avatar','password','halt','status'])->find();
            if(empty($admin)){
                return $this->error('用户名不存在');
            }
            if($admin['status']!=2){
                return $this->error('账户已被禁用');
            }
            if(md5($admin['halt'].$param['password'].$admin['halt'])!=$admin['password']){
                return $this->error('密码错误');
            }
            unset($admin['password']);
            unset($admin['halt']);
            //权限
            $auth = Authority::instance();
            $groups = $auth->getGroups($admin['id'])->toArray();
            if(empty($groups)){
                return $this->error('账号没有权限');
            }
            $updateData = ['login_time'=>date('Y-m-d H:i:s'),'login_ip'=>request()->ip()];
            $res = Admin::update($updateData,['id'=>$admin['id']]);
            if(!$res){
                throw new \Exception('更新管理员失败');
            }
            $data = $this->getToken($admin['id']);
            $data['user'] = $admin;
            Event::trigger('AdminUserLogin',[$admin,$updateData]);
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function freshToken($param){
        return $this->getTokenByFreshToken($param['fresh_token']);
    }

    /**
     * @desc 获取权限列表
     * @method getRules
     * @return array
     * @author chengzhigang
     * @time 2021/11/5 17:22
     */
    public function getRules(){
        try{
            $rules = AuthRule::field(['id','title as label','status','pid'])
                ->order(['pid'=>'asc','sort'=>'desc'])->select()->toArray();
            $rules = $this->tree(array_column($rules,Null,'id'));
            return $this->success($rules);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取权限列表
     * @method getList
     * @return array
     * @author chengzhigang
     * @time 2021/11/8 13:18
     */
    public function getList(){
        try{
            $rules = AuthRule::field(['id','name','title','sort','icon','path','component','menu','always_show','route','status','pid','0 as selected'])
                ->order(['pid'=>'asc','sort'=>'desc'])->select()->toArray();
            $rules = $this->tree(array_column($rules,Null,'id'));
            return $this->success($rules);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取二级权限列表
     * @method getSecondRules
     * @return array
     * @author chengzhigang
     * @time 2021/11/5 17:22
     */
    public function getSecondRules(){
        try{
            $rules = AuthRule::where('menu',1)->field(['id','title as label','pid'])
                ->order(['pid'=>'asc','sort'=>'desc'])->select()->toArray();
            $rules = $this->tree(array_column($rules,Null,'id'));
            return $this->success($rules);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 新增权限
     * @method addRule
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 10:25
     */
    public function addRule($param=[]){
        try{
            if($param['pid']>0&&empty($param['component'])&&empty($param['path'])){
                $param['menu'] = 2;
            }
            $res = AuthRule::create($param);
            if($res){
                return $this->success([],'新增成功');
            }else{
                return $this->error('新增失败');
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取权限
     * @method getInfo
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 11:01
     */
    public function getInfo($param=[]){
        try{
            $data = AuthRule::where('id',$param['id'])->find();
            if($data){
                return $this->success($data->toArray());
            }else{
                return $this->error('权限不存在');
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 编辑权限
     * @method editRule
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 11:04
     */
    public function editRule($param=[]){
        try{
            $res = AuthRule::update($param,['id'=>$param['id']]);
            if($res){
                return $this->success([],'编辑成功');
            }else{
                return $this->error('编辑失败');
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 删除权限
     * @method deleteRule
     * @param array $params
     * @return array
     * @author chengzhigang
     * @time 2021/11/9 13:39
     */
    public function deleteRule($params=[]){
        try{
            Db::startTrans();
            //查看当前权限是否被使用
            foreach ($params['ids'] as $id) {
                $group = AuthGroup::whereRaw("FIND_IN_SET(".$id.",rules)")->select()->toArray();
                if($group){
                    return $this->error('无法删除正在使用的权限');
                }
            }
            //删除
            foreach($params['ids'] as $id){
                //查询下级
                $children = AuthRule::where('pid',$id)->field(['id'])->select()->toArray();
                if($children){
                    foreach($children as $child){
                        $childs = AuthRule::where('pid',$child['id'])->field(['id'])->select()->toArray();
                        if($childs){
                            //删除第三级
                            $res = AuthRule::where('pid',$child['id'])->delete();
                            if(!$res){
                                throw new \Exception('删除第三级权限失败');
                            }
                        }
                    }
                    //删除第二级
                    $res = AuthRule::where('pid',$id)->delete();
                    if(!$res){
                        throw new \Exception('删除第二级权限失败');
                    }
                }
            }
            $res = AuthRule::whereIn('id',$params['ids'])->delete();
            if(!$res){
                throw new \Exception('删除第一级权限失败');
            }
            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    //权限树
    private function tree($rules){
        $tree = [];
        foreach($rules as $k => $rule){
            if(isset($rules[$rule['pid']])){
                $rules[$rule['pid']]['children'][] = &$rules[$k];
            }else{
                $tree[] = &$rules[$k];
            }
        }
        return $tree;
    }
}
