<?php
use think\migration\Seeder;
/**
 * TruncateDatabase
 * created on 2023/2/16 14:38
 * created by chengzhigang
 */
class TruncateDatabaseSeeder extends Seeder
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
        $databases = [
            'admin_login_log',
            'admin_action_log',
            'admin_jwt',
            'admin_relate',
            'apply_friend',
            'admin_chat_data',
            'advert',
            'file_upload'
        ];
        foreach($databases as $table){
            $this->execute('truncate '.$prefix.$table);
        }
    }
}
