<?php
namespace app\manage\controller;
use think\Validate;

class InstYy extends Base
{
    public function index()
    {
        $data=input('get.');
        //dump($data);
        $map=[];

        if(!empty($data['cname'])){
            $map['cname']=['like','%'.$data['cname'].'%'];
        }
        if(!empty($data['username'])){
            $map['username']=['like','%'.$data['username'].'%'];
        }
        if(@$data['is_check']!=''){
            $map['is_check']=$data['is_check'];
        }
        $res=db('v_inst_yy')->where($map)->order("id desc")->paginate(['query'=> $data]);
        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }

    public function detail(){
        $res=model('InstYy')->where('id',input('id/d'))->find();
        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }

    public function add(){
        //return getYyBm();
        return $this->fetch();
    }

    public function edit(){

        $res=model('InstYy')->where('id',input('id/d'))->find();
        $this->assign([
            'res'=>$res,
            'Action'=>'Edit'
        ]);
        return $this->fetch('add');
    }

    public function save()
    {
        $data = input('post.');
        $rule = [
            'ypname|样品名称' => 'require|min:2',
            'yps|样品数（个）' => 'require|integer',
            'instid|仪器' => 'require|integer',
            'r_date|预约日期' => 'require|date',
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {
            $data['bm']=getYyBm();
            $data['person_id']=session('userid');
            $data['person_addtime']=date('Y-m-d H:i:s');
            if(model('InstYy')->allowField(true)->save($data)){
                htmlendjs('新增成功');
            }

        }
    }

    public function del(){
        $res=model('InstYy')->where('id','in',input('post.id'))->delete();
        return $res;
    }
    public function chk(){
        $res=model('InstYy')->save(['states'=>input('post.states/d')],[
            'id'=>input('post.id/d')
        ]);
        return $res;
    }

}