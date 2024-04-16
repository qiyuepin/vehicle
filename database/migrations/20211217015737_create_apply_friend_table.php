<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateApplyFriendTable extends Migrator
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
        $table = $this->table('apply_friend',array('collation'=>'utf8mb4_general_ci','comment' => '申请好友表--czg'));
        //字段
        $table->addColumn('uid','integer',['limit'=>11,'null'=>false,'comment'=>'申请人id'])->addIndex('uid')
            ->addColumn('touid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])->addIndex('touid')
            ->addColumn('remark','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'备注姓名'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'1申请中 2已通过 3已拒绝 4已过期'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->save();
    }
}
