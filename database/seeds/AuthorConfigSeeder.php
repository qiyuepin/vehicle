<?php
use think\migration\Seeder;
/**
 * AuthorConfigSeeder
 * created on 2022/11/11 17:19
 * created by chengzhigang
 */
class AuthorConfigSeeder extends Seeder
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
        $this->execute('truncate '.$prefix.'author_config');
        $data = [
            [
                'id' => 1,
                'name' => '观海听潮',
                'avatar' => 'https://img1.baidu.com/it/u=2927610883,3845649985&fm=253&fmt=auto&app=138&f=JPEG?w=400&h=400',
                'gender' => 1,
                'email' => '1256699215@qq.com',
                'phone' => '15063337229',
                'qq' => '1256699215',
                'position' => 'PHP开发工程师',
                'birthday' => '1994-01-07',
                'company' => '互联网-开发部',
                'link' => 'https://www.chengzhigang.cn',
                'speciality' => 'PHP、Go、Mysql、Redis、Vue',
                'signature' => '吃得苦中苦，方为人上人',
                'label' => '内向|闷骚|乐观|最强王者',
                'introduction' => '平凡人生塑造不平凡的我，心有沟壑便一往无前！',
                'address' => '中国-山东省-青岛市',
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ]
        ];
        $this->table('author_config')->insert($data)->save();
    }
}
