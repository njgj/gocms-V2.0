<?php
namespace app\manage\controller;
use \think\Controller;
use \think\Session;
/**
 * 后台公共控制器
 */
class Base extends Controller
{
	
	
    protected function _initialize()
    {

        // 判断登陆
        if (!Session::has('userid')) {
            return $this->error('请登陆之后在操作！',url('/manage/login'));
        }
    }
	
}