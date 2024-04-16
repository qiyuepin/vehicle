<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateAdminJwtTable extends Migrator
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
        $table = $this->table('admin_jwt',array('collation'=>'utf8mb4_general_ci','comment' => '管理员jwt表--czg'));
        //字段
        $table->addColumn('uid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])
            ->addColumn('token','text',['comment'=>'token值'])
            ->addIndex(['uid'], ['unique' => true])
            ->save();
    }
}
