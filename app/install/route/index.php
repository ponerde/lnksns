<?php

use think\facade\Route;

Route::get('/', 'Index/index');
Route::any('/index/step1', 'Index/step1');
Route::any('/index/step2', 'Index/step2');
Route::get('/index/step3', 'Index/step3');
Route::get('/index/clear', 'Index/clear');

Route::get('/index/install', 'Index/install');