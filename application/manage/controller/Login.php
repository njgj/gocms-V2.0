<?php
namespace app\manage\controller;
use \think\Controller;

class Login extends Controller
{
    public function index()
    {
		//return 'demo';
		session(null);	
        return $this->fetch();
    }
	
	public function chklogin(){
		$data = [
			'username'  => input('post.username'),
			'userpwd' => input('post.userpwd')
		];
		
		$validate = new \app\common\validate\UserInfo;
		if (!$validate->scene('login')->check($data)) {
			//dump($validate->getError());
			$this->error($validate->getError());
		}else{
			$user=model('UserInfo');
			$res=$user->where('username',$data['username'])->where('userpwd',md5($data['userpwd']))->find();
			//dump($res);
			
			if($res){
				session('userid',$res['userid']);
				session('groupid',$res['groupid']);
				session('username',$res['username']);
				session('cityid',$res['cityid']);
				$this->success('登陆成功',url('/manage/index'));
			}else{
				$this->error('用户名或密码错误');
			}
			 
			
		}
		
	}

}
