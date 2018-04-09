<?php
namespace app\manage\controller;
use \think\Controller;

class Index extends Base
{
    public function index()
    {
		$qx=db('UserGroup')->where('groupid',session('groupid'))->value('qx');
		$userqx=db('UserQx');
		$res=$userqx->where('classid','in',$qx)->where('parentid',0)->order('orderid')->select();
		
		foreach($res as $k=>$v){
			$res2=$userqx->where('classid','in',$qx)->where('parentid',$v['classid'])->order('orderid')->select();
			$res[$k]['child'] = $res2; 
		}

		//return json($res);
		$this->assign('res',$res);
        return $this->fetch();
    }
    public function welcome()
    {
		//return 'demo';
        $arr['users']=db('user_info')->count();
        $arr['news']=db('g_news')->count();
        $arr['sps']=db('video')->count();
        $arr['zxs']=db('g_zixun')->count();
        $arr['yqs']=db('inst')->count();
        $arr['yys']=db('inst_yy')->count();
        $this->assign('arr',$arr);
        return $this->fetch();
    }

}

