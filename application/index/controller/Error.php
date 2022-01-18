<?php
namespace app\index\controller;

use think\Request;

class Error 
{
    public function index(Request $request)
    {
        $action = $request->controller();
        return $action.lang('页面不存在');
    }
}