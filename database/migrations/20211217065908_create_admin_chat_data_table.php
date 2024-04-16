<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateAdminChatDataTable extends Migrator
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
        $table = $this->table('admin_chat_data',array('collation'=>'utf8mb4_general_ci','comment' => '管理员聊天表--czg'));
        //字段
        $table->addColumn('uid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])->addIndex('uid')
            ->addColumn('touid','integer',['limit'=>11,'null'=>false,'comment'=>'管理员id'])->addIndex('touid')
            ->addColumn('message','text',['null'=>true,'comment'=>'聊天内容'])
            ->addColumn('type','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'内容类型 1文本 2图片 3表情'])
            ->addColumn('read','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'1未读 2已读'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->save();
    }
}
