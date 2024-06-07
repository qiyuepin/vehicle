<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AuthGroupAccess;
use app\model\AuthorConfig;
use app\model\AuthRule;
use app\service\BaseService;
use app\traits\TokenTrait;
use think\facade\Cache;
use think\wenhainan\Auth;

/**
 * UserService
 * created on 2021/10/29 17:13
 * created by chengzhigang
 */
class UserService extends BaseService
{
    use TokenTrait;
    /**
     * @desc 获取用户详情
     * @method info
     * @param $id
     * @return array
     * @author chengzhigang
     * @time 2021/10/29 17:17
     */
    public function info($id){
        
        try{
            $data = Cache::get('adminInfo:'.$id);
            if(empty($data)){
                $data = Admin::where('id',$id)->field(['id','username','nickname','phone','email','avatar','sign'])->find();
                $data = $data->toArray();
                $data['rule'] = $this->getRule($id);
                $data['roles'] = AuthGroupAccess::where('uid',$id)->column('group_id');
                
                Cache::set('adminInfo:'.$id,$data,3600);
            }
            // dump($data);
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function author(){
        try{
            $data = Cache::get('author');
            // dump($data);die;
            if(empty($data)){
                $data = AuthorConfig::where('id',1)->find();
                $data = $data->toArray();
                Cache::set('author',$data,3600);
            }
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function updateAuthor($param){
        try{
            $res = AuthorConfig::update($param,['id'=>1]);
            if($res){
                Cache::delete('author');
                return $this->success([],'保存成功');
            }else{
                return $this->error('保存失败');
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取用户权限树
     * @method getRule
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author chengzhigang
     * @time 2021/10/29 17:17
     */
    public function getRule($id){
        $auth = Auth::instance();
        $access = $auth->getGroups($id)->toArray();
        $ruleIds = array_column($access,'rules');
        $ruleIds = array_unique(explode(',',implode(',',$ruleIds)));//去除重复项
        $rules = AuthRule::whereIn('id',$ruleIds)->where('status',1)
            ->field(['id','name','title','path','icon','pid','menu','component','always_show','redirect','show'])
            ->order(['pid'=>'asc','sort'=>'desc'])->select()->toArray();
        $tree = $this->tree(array_column($rules,Null,'id'));
        return $tree;
    }

    //权限树
    private function tree($rules){
        $tree = [];
        foreach($rules as $k => $rule){
            $rules[$k]['always_show'] = boolval($rule['always_show']);
            if(isset($rules[$rule['pid']])){
                $rules[$rule['pid']]['children'][] = &$rules[$k];
            }else{
                $tree[] = &$rules[$k];
            }
        }
        return $tree;
    }

    /**
     * @desc 退出登录
     * @method logout
     * @param $id
     * @return array
     * @author chengzhigang
     * @time 2021/11/2 16:18
     */
    public function logout($token,$id){
        try{
            $this->clearToken($token);
            Cache::delete('adminInfo:'.$id);//删除用户缓存数据
            return $this->success([],'退出成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 保存信息
     * @method saveInfo
     * @param $id
     * @param $param
     * @return array
     * @author chengzhigang
     * @time 2022/11/11 15:23
     */
    public function saveInfo($id,$param){
        try{
            $res = Admin::update([
                'nickname' => $param['nickname'],
                'phone' => $param['phone'],
                'email' => $param['email'],
                'avatar' => $param['avatar'],
                'sign' => $param['signature']
            ],['id'=>$id]);
            
            if($res){
                Cache::delete('adminInfo:'.$id);
                return $this->success([],'保存成功');
            }else{
                return $this->error('保存失败');
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 修改密码
     * @method updatePass
     * @param $id
     * @param $param
     * @return array
     * @author chengzhigang
     * @time 2022/11/11 15:26
     */
    public function updatePass($id,$param){
        try{
            $admin = Admin::where('id',$id)->field(['halt'])->find();
            if($admin){
                $param['password'] = md5($admin['halt'].$param['password'].$admin['halt']);
                $res = Admin::update([
                    'password' => $param['password'],
                ],['id'=>$id]);
                if($res){
                    return $this->success([],'修改成功');
                }
            }
            return $this->error('保存失败');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }
}
