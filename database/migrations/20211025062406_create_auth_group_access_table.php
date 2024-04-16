<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAuthGroupAccessTable extends Migrator
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
        $table = $this->table('auth_group_access',array('collation'=>'utf8mb4_general_ci','comment' => '用户组明细表--czg'));
        //字段
        $table->addColumn('uid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])
            ->addColumn('group_id','string',['limit'=>11,'null'=>false,'comment'=>'用户组id'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            ->addIndex(['uid','group_id'],['unique' => true])
            //创建
            ->save();
    }
}
