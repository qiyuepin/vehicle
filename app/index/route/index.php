<?php
/**
 *index.php
 *文件描述
 *Created on 2021/12/8 15:42
 *Created by chengzhigang
 */
use think\facade\Route;
Route::get('/','Index/index');//首页
Route::get('/hello','Index/hello');//首页
