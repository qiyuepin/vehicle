<?php

use think\migration\Seeder;
use think\facade\Env;
class RegionSeeder extends Seeder
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
        $this->execute('truncate '.$prefix.'region');
        $data = getRegion();
        $this->table('region')->insert($data)->save();
    }
}
