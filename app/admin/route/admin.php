<?php
/**
 *admin.php
 *文件描述
 *Created on 2021/10/26 16:07
 *Created by chengzhigang
 */

use think\facade\Route;
use app\service\BaseService;
use app\Request;

Route::get('test/index', 'admin/Test/index');
Route::group('/',function(){
    ###基础路由###
    Route::get('getCaptcha','Base/getCaptcha');//获取验证码
    Route::post('uploadImage','Base/uploadImage');//上传单图片
    Route::post('uploadFile','Base/uploadFile');//上传单文件
    Route::get('readFile','Base/readFile');//上传单文件
    Route::post('gitPull',function (Request $request,BaseService $service) {
        return $service->getPull($request);
    });
    Route::get('queueInfo','Base/queueInfo');//队列信息
    Route::get('queueRetry','Base/queueRetry');//队列重试
    Route::get('queueDelete','Base/queueDelete');//队列删除（redis删除）

    Route::group('index',function(){
       Route::get('ceshi','Index/ceshi');
    });
    ###登录授权和权限###
    Route::group('auth',function(){
        Route::post('login','Auth/login');//登录
        Route::get('freshToken','Auth/freshToken');//刷新token
        Route::get('getRules','Auth/getRules')->middleware(['adminAuth']);//获取权限树
        Route::get('getList','Auth/getList')->middleware(['adminAuth']);//获取权限列表
        Route::get('getdriverList','Auth/getdriverList')->middleware(['adminAuth']);//获取权限列表
        Route::get('getSecondRules','Auth/getSecondRules')->middleware(['adminAuth']);//获取二级权限树
        Route::post('addRule','Auth/addRule')->middleware(['adminAuth']);//新增权限
        Route::post('editRule','Auth/editRule')->middleware(['adminAuth']);//编辑权限
        Route::get('getInfo','Auth/getInfo')->middleware(['adminAuth']);//获取权限
        Route::post('deleteRule','Auth/deleteRule')->middleware(['adminAuth']);//删除权限
    });

    ###用户###
    Route::group('user',function(){
        Route::get('info','User/info');//获取用户信息
        Route::get('author','User/author');//获取用户信息
        Route::post('logout','User/logout');//退出登录
        Route::post('saveInfo','User/saveInfo');//修改信息
        Route::post('updatePass','User/updatePass');//修改密码
        Route::get('author','User/author');//获取作者信息
        Route::post('updateAuthor','User/updateAuthor');//更改作者信息
    })->middleware(['adminAuth']);

    ###管理员###
    Route::group('admin',function(){
        Route::get('getList','Admin/getList');//获取管理员列表
        Route::post('changeStatus','Admin/changeStatus');//更改管理员状态
        Route::get('getRole','Admin/getRole');//获取角色
        Route::post('addAdmin','Admin/addAdmin');//创建管理员
        Route::get('getInfo','Admin/getInfo');//获取单个管理员
        Route::post('editAdmin','Admin/editAdmin');//编辑管理员
        Route::post('deleteAdmin','Admin/deleteAdmin');//删除管理员
        Route::get('getLoginLog','Admin/getLoginLog');//获取登录日志
        Route::get('getHandleLog','Admin/getHandleLog');//获取操作日志
        Route::get('getdriverList','Admin/getdriverList');//
        Route::post('adddriverAdmin','Admin/adddriverAdmin');//创建驾驶员
        Route::get('getdriverInfo','Admin/getdriverInfo');//驾驶员信息
        Route::post('editdriverAdmin','Admin/editdriverAdmin');//修改驾驶员信息
        // Route::get('test','Driver/test');//修改驾驶员信息
        // Route::get('getregulation','Admin/getregulation');//违章信息
        // Route::post('addregulation','Admin/addregulation');//创建违章信息
        // Route::post('editregulation','Admin/editregulation');//修改违章信息
        // Route::get('getaccident','Admin/getaccident');//违章信息
        // Route::post('addaccident','Admin/addaccident');//创建违章信息
        // Route::post('editaccident','Admin/editaccident');//修改违章信息
    })->middleware(['adminAuth']);

    Route::group('driver',function(){
        Route::get('getregulation','Driver/getregulation');//违章信息
        Route::get('getregulationinfo','Driver/getregulationinfo');//删除违章信息
        Route::post('addregulation','Driver/addregulation');//创建违章信息
        Route::post('editregulation','Driver/editregulation');//修改违章信息
        Route::post('delregulation','Driver/delregulation');//删除违章信息
        Route::get('getaccident','Driver/getaccident');//事故信息
        Route::post('addaccident','Driver/addaccident');//创建事故信息
        Route::post('editaccident','Driver/editaccident');//修改事故信息
        Route::post('delaccident','Driver/delaccident');//删除事故信息
        Route::get('test','Driver/test');//修改违章信息
    })->middleware(['adminAuth']);
    ###角色###
    Route::group('role',function(){
        Route::get('getList','Role/getList');//获取角色列表
        Route::get('getInfo','Role/getInfo');//获取角色
        Route::post('addRole','Role/addRole');//新增角色
        Route::post('editRole','Role/editRole');//编辑角色
        Route::post('changeStatus','Role/changeStatus');//更改角色状态
        Route::post('deleteRole','Role/deleteRole');//删除角色
    })->middleware(['adminAuth']);

    ###费用###
    Route::group('cost',function(){
        Route::get('getcost','Cost/getcost');//获取费用列表
        Route::get('getinfo','Cost/getinfo');//获取费用
        Route::post('addcost','Cost/addcost');//新增费用
        Route::post('editcost','Cost/editcost');//编辑费用
        Route::post('change','Cost/change');//更改费用状态
        Route::post('delcost','Cost/delcost');//删除费用
        Route::get('getcosttype','Cost/getcosttype');//费用类别
        Route::get('getperiod','Cost/getperiod');
        Route::get('getcostlist','Cost/getcostlist');
    })->middleware(['adminAuth']);

    ###聊天###
    Route::group('chat',function(){
        Route::get('getUser','Chat/getUser');//获取用户
        Route::post('addFriend','Chat/addFriend');//申请好友
        Route::get('getApplyList','Chat/getApplyList');//获取申请列表
        Route::post('agreeFriendApply','Chat/agreeFriendApply');//同意好友申请
        Route::get('getFriendList','Chat/getFriendList');//获取好友列表
        Route::get('getHistoryMsg','Chat/getHistoryMsg');//获取历史消息
        Route::get('getSessionList','Chat/getSessionList');//获取会话消息
    })->middleware(['adminAuth']);


    Route::group('info',function(){
        Route::get('carhead','Info/carhead');//车头信息
        Route::get('getcarhead','Info/getcarhead');//车头信息
        Route::get('getcarheadInfo','Info/getcarheadInfo');//车头信息详情
        Route::post('addcarhead','Info/addcarhead');//创建车头信息
        Route::post('editcarhead','Info/editcarhead');//修改车头信息
        Route::post('delcarhead','Info/delcarhead');//删除车头信息
        Route::get('getescortInfo','Info/getescortInfo');//押运员信息
        Route::get('getescort','Info/getescort');//押运员信息
        Route::post('addescort','Info/addescort');//创建押运员信息
        Route::post('editescort','Info/editescort');//修改押运员信息
        Route::post('delrescort','Info/delescort');//删除押运员信息
        Route::get('getcartrailer','Info/getcartrailer');//挂车信息
        Route::post('addcartrailer','Info/addcartrailer');//创建挂车信息
        Route::post('editcartrailer','Info/editcartrailer');//修改挂车信息
        Route::get('getcartrailerInfo','Info/getcartrailerInfo');//挂车信息详情
        Route::get('getinfolist','Info/getinfolist');//匹配信息
        Route::post('addinfo','Info/addinfo');//创建匹配信息
        Route::post('editinfo','Info/editinfo');//修改匹配信息
        Route::post('delinfo','Info/delinfo');//删除押运员信息
        Route::get('getinfo','Info/getinfo');//匹配信息详情
        Route::post('Pouring','Info/Pouring');//调度员倒料
        Route::get('infonotice','Info/infonotice');//调度员倒料
        Route::get('getcarlist','Info/getcarlist');//信息详情
        Route::get('getcarscope','Info/getcarscope');//获取经营范围
        Route::get('getfactory','Info/getfactory');//厂家列表
        Route::get('getfactoryinfo','Info/getfactoryinfo');//厂家信息详情
        Route::post('addfactory','Info/addfactory');//创建厂家
        Route::post('editfactory','Info/editfactory');//修改厂家
        Route::post('delfactory','Info/delfactory');//删除厂家
        Route::post('bind', 'Info\WebSocket@bind');
        Route::post('end', 'Info\WebSocket@send');
    })->middleware(['adminAuth']);

    Route::group('plan',function(){
        Route::get('homechart','Plan/homechart');//车头信息
        Route::get('normal','Plan/normal');//车头信息
        Route::get('getnormal','Plan/getnormal');//车头信息
        Route::get('getnormalInfo','Plan/getnormalInfo');//车头信息详情
        Route::post('addnormal','Plan/addnormal');//创建车头信息
        Route::post('editnormal','Plan/editnormal');//修改车头信息
        Route::post('delnormal','Plan/delnormal');//删除车头信息
        Route::get('gettemporaryInfo','Plan/gettemporaryInfo');//押运员信息
        Route::get('gettemporary','Plan/gettemporary');//押运员信息
        Route::post('addtemporary','Plan/addtemporary');//创建押运员信息
        Route::post('edittemporary','Plan/edittemporary');//修改押运员信息
        Route::post('deltemporary','Plan/deltemporary');//删除押运员信息
        Route::get('getplaninfo','Plan/getplaninfo');//挂车信息
        Route::get('getplans','Plan/getplans');//全部任务
        Route::get('getplansinfo','Plan/getplansinfo');//任务详情
        Route::post('addplan','Plan/addplan');//创建任务
        Route::post('editplan','Plan/editplan');//修改任务
        Route::post('distplan','Plan/distplan');//分配任务
        Route::post('delplan','Plan/delplan');//删除任务
        Route::get('driver_normal','Plan/driver_normal');//挂车信息
        Route::get('notice','Plan/notice');//挂车信息
        Route::post('driver_sumitnormal','Plan/driver_sumitnormal');//驾驶员提交信息
        Route::post('addhisplan','Plan/addhisplan');//任务列表删除的历史记录
    })->middleware(['adminAuth']);
});


