<?php
namespace app\index\controller;
use think\Validate;

class Index extends \think\Controller
{
    public function index()
    {
        return $this->fetch('index');
    }
	
    public function guestbook()
    {
       $data = input('post.');
        $rule = [
            'realname|姓名' => 'require|min:2',
            'tel|电话' => 'require',
            'email|邮箱' => 'require|email',
            'content|留言' => 'require|max:300',
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $validate->getError();
        } else {
            $data['title']=input('post.realname');
            $data['ip']=request()->ip();
            $data['addtime']=date('Y-m-d H:i:s');
            return model('Zixun')->save($data);            
		}
    }	
}
