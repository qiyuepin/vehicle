<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateAdminTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //表
        $table = $this->table('admin',array('collation'=>'utf8mb4_general_ci','comment' => '管理员表--czg'));
        //字段
        $table->addColumn('username','string',['limit'=>20,'null'=>false,'comment'=>'用户名'])
            ->addColumn('nickname','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'用户昵称'])
            ->addColumn('email','string',['limit'=>50,'null'=>false,'comment'=>'邮箱'])
            ->addColumn('phone','string',['limit'=>20,'null'=>false,'comment'=>'手机号'])
            ->addColumn('avatar','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'头像'])
            ->addColumn('password','string',['limit'=>128,'null'=>false,'comment'=>'登录密码'])
            ->addColumn('sign','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'签名'])
            ->addColumn('halt','string',['limit'=>64,'null'=>false,'comment'=>'密码盐'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>2,'comment'=>'1禁用 2启用'])
            ->addColumn('login_time','datetime',['null'=>true,'comment'=>'登录时间'])
            ->addColumn('login_ip','string',['limit'=>50,'null'=>true,'default'=>'','comment'=>'登录IP'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //索引
            ->addIndex(['username'], ['unique' => true])
            ->addIndex(['phone'], ['unique' => true])
            ->addIndex(['email'], ['unique' => true])
            //创建
            ->save();
    }
}
