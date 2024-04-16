<?php

use think\migration\Seeder;

class AdminPowerSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run():void
    {
        $prefix = config('database.connections.mysql.prefix');
        $this->execute('truncate ' . $prefix . 'auth_rule');
        $data = [
            [
                'id' => 1,
                'name' => 'authority',
                'title' => '权限管理',
                'path' => '/authority',
                'route' => '',
                'pid' => 0,
                'icon' => 'password',
                'component' => 'layout',
                'menu' => 1,
                'always_show' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'auth.admin',
                'title' => '管理员列表',
                'path' => 'user',
                'route' => 'admin/admin/getList',
                'pid' => 1,
                'icon' => 'user',
                'component' => 'authority/user',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'auth.admin.add',
                'title' => '新增',
                'path' => '',
                'route' => 'admin/admin/addAdmin',
                'pid' => 2,
                'icon' => 'el-icon-plus',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'auth.admin.edit',
                'title' => '编辑',
                'path' => '',
                'route' => 'admin/admin/editAdmin',
                'pid' => 2,
                'icon' => 'el-icon-edit-outline',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'name' => 'auth.admin.delete',
                'title' => '删除',
                'path' => '',
                'route' => 'admin/admin/deleteAdmin',
                'pid' => 2,
                'icon' => 'el-icon-delete',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 6,
                'name' => 'auth.admin.change',
                'title' => '状态',
                'path' => '',
                'route' => 'admin/admin/changeStatus',
                'pid' => 2,
                'icon' => 'el-icon-more',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 7,
                'name' => 'auth.role',
                'title' => '角色列表',
                'path' => 'role',
                'route' => 'admin/role/getList',
                'pid' => 1,
                'icon' => 'peoples',
                'component' => 'authority/role',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 8,
                'name' => 'auth.role.add',
                'title' => '新增',
                'path' => '',
                'route' => 'admin/role/addRole',
                'pid' => 7,
                'icon' => 'el-icon-plus',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 9,
                'name' => 'auth.role.edit',
                'title' => '编辑',
                'path' => '',
                'route' => 'admin/role/editRole',
                'pid' => 7,
                'icon' => 'el-icon-edit-outline',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 10,
                'name' => 'auth.role.delete',
                'title' => '删除',
                'path' => '',
                'route' => 'admin/role/deleteRole',
                'pid' => 7,
                'icon' => 'el-icon-delete',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 11,
                'name' => 'auth.role.change',
                'title' => '状态',
                'path' => '',
                'route' => 'admin/role/changeStatus',
                'pid' => 7,
                'icon' => 'el-icon-more',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>12,
                'name' => 'auth.auth',
                'title' => '权限列表',
                'path' => 'auth',
                'route' => 'admin/auth/getList',
                'pid' => 1,
                'icon' => 'lock',
                'component' => 'authority/auth',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 13,
                'name' => 'auth.auth.add',
                'title' => '新增',
                'path' => '',
                'route' => 'admin/auth/addRule',
                'pid' => 12,
                'icon' => 'el-icon-plus',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 14,
                'name' => 'auth.auth.edit',
                'title' => '编辑',
                'path' => '',
                'route' => 'admin/auth/editRule',
                'pid' => 12,
                'icon' => 'el-icon-edit-outline',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 15,
                'name' => 'auth.auth.delete',
                'title' => '删除',
                'path' => '',
                'route' => 'admin/auth/deleteRule',
                'pid' => 12,
                'icon' => 'el-icon-delete',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 16,
                'name' => 'system',
                'title' => '系统管理',
                'path' => '/system',
                'route' => '',
                'pid' => 0,
                'icon' => 'system',
                'component' => 'layout',
                'menu' => 1,
                'always_show' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>17,
                'name' => 'system.icon',
                'title' => '图标列表',
                'path' => 'icon',
                'route' => '',
                'pid' => 16,
                'icon' => 'icon',
                'component' => 'system/icon',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>18,
                'name' => 'system.loginlog',
                'title' => '登录日志',
                'path' => 'loginlog',
                'route' => 'admin/admin/getLoginLog',
                'pid' => 16,
                'icon' => 'loginlog',
                'component' => 'system/loginlog',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 19,
                'name' => 'advert',
                'title' => '广告管理',
                'path' => '/advert',
                'route' => '',
                'pid' => 0,
                'icon' => 'guide',
                'component' => 'layout',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 20,
                'name' => 'advert.index',
                'title' => '广告管理',
                'path' => 'index',
                'route' => 'admin/advert/list',
                'pid' => 19,
                'icon' => 'guide',
                'component' => 'advert/index',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 21,
                'name' => 'advert.index.add',
                'title' => '新增',
                'path' => '',
                'route' => 'admin/advert/add',
                'pid' => 20,
                'icon' => 'el-icon-plus',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 22,
                'name' => 'advert.index.edit',
                'title' => '编辑',
                'path' => '',
                'route' => 'admin/advert/edit',
                'pid' => 20,
                'icon' => 'el-icon-edit-outline',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 23,
                'name' => 'advert.index.change',
                'title' => '状态',
                'path' => '',
                'route' => 'admin/advert/change',
                'pid' => 20,
                'icon' => 'el-icon-more',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 24,
                'name' => 'advert.index.delete',
                'title' => '删除',
                'path' => '',
                'route' => 'admin/advert/delete',
                'pid' => 20,
                'icon' => 'el-icon-delete',
                'component' => '',
                'menu' => 2,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>25,
                'name' => 'system.handlelog',
                'title' => '操作日志',
                'path' => 'handlelog',
                'route' => 'admin/admin/getHandleLog',
                'pid' => 16,
                'icon' => 'form',
                'component' => 'system/handlelog',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 26,
                'name' => 'route',
                'title' => '路由嵌套',
                'path' => '/route',
                'route' => '',
                'pid' => 0,
                'icon' => 'nested',
                'component' => 'layout',
                'menu' => 1,
                'always_show' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>27,
                'name' => 'route.first',
                'title' => '路由1',
                'path' => 'first',
                'route' => '',
                'pid' => 26,
                'icon' => 'nested',
                'component' => 'route/first',
                'menu' => 1,
                'always_show' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' =>28,
                'name' => 'route.second',
                'title' => '路由1-1',
                'path' => 'second',
                'route' => '',
                'pid' => 27,
                'icon' => 'nested',
                'component' => 'route/first/second',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 29,
                'name' => 'link',
                'title' => '博客外链',
                'path' => '/link',
                'route' => '',
                'pid' => 0,
                'icon' => 'link',
                'component' => 'layout',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 30,
                'name' => 'link.blog',
                'title' => '博客外链',
                'path' => 'https://www.chengzhigang.cn',
                'route' => '',
                'pid' => 29,
                'icon' => 'link',
                'component' => '',
                'menu' => 1,
                'always_show' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ],
        ];
        $this->table('auth_rule')->insert($data)->save();
        $ids = implode(',',array_column($data,'id'));
        $time = date('Y-m-d H:i:s');
        if($this->fetchRow('select * from '.$prefix.'auth_group where id = 1')){
            $this->query('update '.$prefix.'auth_group set rules="'.$ids.'",update_time="'.$time.'" where id = 1');
        }else{
            $this->query('insert into '.$prefix.'auth_group values (1,"超级角色","'.$ids.'",1,"'.$time.'","'.$time.'")');
        }
    }
}
