<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateAdminActionLogTable extends Migrator
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
        $table = $this->table('admin_action_log',array('collation'=>'utf8mb4_general_ci','comment' => '操作表--czg'));
        //字段
        $table->addColumn('uid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])
            ->addColumn('route','string',['limit'=>50,'null'=>false,'comment'=>'路由地址'])
            ->addColumn('method','string',['limit'=>10,'null'=>true,'default'=>'','comment'=>'请求方式'])
            ->addColumn('ip','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'请求ip'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>2,'comment'=>'1失败 2成功'])
            ->addColumn('param','text',['null'=>true,'comment'=>'请求参数'])
            ->addColumn('error','text',['null'=>true,'comment'=>'错误信息'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //索引
            ->addIndex(['uid'], ['name' => 'uid'])
            //创建
            ->save();
    }
}
