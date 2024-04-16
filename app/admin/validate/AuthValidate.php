<?php


namespace app\admin\validate;
use think\Validate;
use ric\captcha\facade\CaptchaApi;
/**
 * AuthValidate
 * created on 2021/7/12 10:32
 * created by chengzhigang
 */
class AuthValidate extends Validate
{
    protected $rule =   [
        'username|用户名'  => 'require|min:2|max:10',
        'password|登录密码' => 'require|min:6|max:18',
        // 'code' => 'require|checkCaptcha',
        // 'key' => 'require',
        'name|权限标识' => 'require|max:40|unique:auth_rule',
        'title|权限名称' => 'require|chs|max:20',
        'sort|权限排序' => 'requirePresent|number|egt:0',
        'component|权限组件' => 'requirePresent',
        'path|模板路径' => 'requirePresent',
        'route|路由地址' => 'requirePresent',
        'pid|权限上级' => 'require|number|egt:0',
        'icon|权限图标' => 'require|alphaDash',
        'id|ID' => 'require|number|gt:0',
        'ids|ID' => 'require|array'
    ];

    protected $message  =   [
        'code.require' => '验证码为空！',
        'code.checkCaptcha' => '验证码错误！'
    ];

    protected $scene = [
        'login'  =>  ['username','password'],
        'addRule' => ['name','title','sort','component','path','pid','icon','route'],
        'editRule' => ['id','name','title','sort','component','path','pid','icon','route'],
        'getInfo' => ['id'],
        'deleteRule' => ['ids']
    ];

    protected function checkCaptcha($value,$rules,$data,$field){
        return CaptchaApi::check($value,$data['key']);
    }

    //自定义验证字段必须存在，值可以为空
    protected function requirePresent($value,$rules,$data,$field){
        return array_key_exists($field,$data);
    }
}
