<?php

use think\facade\Route;

Route::group('/',function(){

    // 评论
    Route::resource('/comment','Comment');
    // 消息
    Route::resource('/message','Message');
    // 动态
    Route::resource('/dynamic','Dynamic');
    // 用户
    Route::resource('/user','User');
    // 圈子
    Route::resource('/circle','Circle');
    // 条款
    Route::resource('/clause','Clause');
    // 获取系统配置
    Route::get('/Config/detail','Config/detail');
    // 更新系统配置
    Route::post('/Config/update','Config/update');
    
})->middleware('check_login','admin');




