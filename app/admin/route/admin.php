<?php

use think\facade\Route;


// 登录
Route::post('/user/login','Index/login');
 // 初始化配置信息
 Route::get('/config/initConfig','Config/initConfig');

Route::group('/',function(){
    // 获取用户信息
    Route::get('/user/profile','Index/profile');
    // 用户菜单
    Route::get('/user/menu','Index/userMenu');
    // 存储列表
    Route::get('/storage/list','setting.Storage/list');
    // 存储详情
    Route::get('/storage/detail','setting.Storage/detail');
    // 设置存储
    Route::post('/storage/setup','setting.Storage/setup');
    // 上传图片
    Route::post('/upload/image','Upload/image');
    // 文件组
    Route::resource('/file/group','FileGroup');
    // 文件列表
    Route::get('/file/list','File/list');
    // 更改管理员密码
    Route::put('/auth/admin/updatePassword','auth.Admin/updatePassword');
    // 管理员
    Route::resource('/auth/admin','auth.Admin');
    // 角色
    Route::resource('/auth/role','auth.Role');
    // 菜单
    Route::get('/auth/permissions/options','auth.Permissions/tree');
    Route::resource('/auth/permissions','auth.Permissions');
    // 角色列表tree
    Route::get('/auth/roleTree','auth.Role/tree');
    // 获取系统配置
    Route::get('/system/detail','setting.System/detail');
    // 更新系统配置
    Route::post('/system/update','setting.System/update');
   
    // 更新菜单权限
    Route::put('/role/updateRolePermissions','auth.Role/updateRolePermissions');
    // 应用列表
    Route::get('/app/list','App/list');
    
})->middleware('check_login','admin');




