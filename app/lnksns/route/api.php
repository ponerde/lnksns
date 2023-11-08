<?php

use think\facade\Route;


Route::group('/api',function(){
    // 测试
    Route::get('/index/test','Index/test');
    // 登录
    Route::post('/user/wx_empower','User/wx_empower');
    // 配置
    Route::get('/index/config','Index/config');
    // 条款
    Route::get('/clause/details','Clause/details');
});

Route::group('/api',function(){

    // 评论动态
    Route::post('/comment/comment_dynamic','Comment/comment_dynamic');
    // 动态评论
    Route::get('/comment/dynamic_comment','Comment/dynamic_comment');
    // 评论的评论
    Route::get('/comment/son_comment','Comment/son_comment');
    // 删除评论
    Route::post('/comment/del_comment','Comment/del_comment');
    // 未读消息数量
    Route::get('/message/get_message_count','Message/get_message_count');
    // 消息列表
    Route::get('/message/get_message','Message/get_message');
    // 读消息
    Route::post('/message/read_message','Message/read_message');
    // 喜欢动态
    Route::post('/dynamic/like_dynamic','Dynamic/like_dynamic');
    // 动态详情
    Route::get('/dynamic/dynamic_details','Dynamic/dynamic_details');
    // 动态数据
    Route::get('/dynamic/get_dynamic_info','Dynamic/get_dynamic_info');
    // 推荐动态
    Route::get('/dynamic/recommend_dynamic','Dynamic/recommend_dynamic');
    // 删除动态
    Route::post('/dynamic/del_dynamic','Dynamic/del_dynamic');
    // 编辑动态
    Route::post('/dynamic/save_dynamic','Dynamic/save_dynamic');
    // 用户圈子
    Route::get('/circle/user_circle','Circle/user_circle');
    // 关注圈子
    Route::post('/circle/follow_circle','Circle/follow_circle');
    // 圈子动态
    Route::get('/circle/get_circle_dynamic','Circle/get_circle_dynamic');
    // 动态圈子
    Route::get('/circle/dynamic_circle','Circle/dynamic_circle');
    // 顶部推荐圈子
    Route::get('/circle/get_top_circle','Circle/get_top_circle');
    // 推荐圈子列表
    Route::get('/circle/get_circle_list','Circle/get_circle_list');
    // 圈子详情
    Route::get('/circle/get_circle_details','Circle/get_circle_details');
    // 圈子粉丝
    Route::get('/circle/circle_fans','Circle/circle_fans');
    // 用户详情
    Route::get('/user/user_details','User/user_details');
    // 用户数据
    Route::get('/user/user_publish_content','User/user_publish_content');
    // 关注用户
    Route::post('/user/follow_user','User/follow_user');
    // 用户关注数据
    Route::get('/user/user_follow','User/user_follow');
    // 用户刷新IP
    Route::post('/user/user_refresh_ip','User/user_refresh_ip');
    // 用户绑定手机号
    Route::post('/user/user_bind_mobile','User/user_bind_mobile');
    // 刷新用户资料
    Route::get('/user/user_refresh_info','User/user_refresh_info');
    // 编辑用户资料
    Route::post('/user/edit_user_info','User/edit_user_info');
    // 用户通知
    Route::get('/message/user_message_count','Message/user_message_count');
    // 上传图片
    Route::post('/upload/image','Upload/image');
})->middleware(\app\lnksns\middleware\Check::class);




