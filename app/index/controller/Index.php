<?php

declare(strict_types=1);

namespace app\index\controller;

use think\Request;
use app\BaseController;
use think\facade\Event;
use think\facade\Log;

class Index extends BaseController
{
    public function index()
    {
       
        return view('index');
    }
    
}
