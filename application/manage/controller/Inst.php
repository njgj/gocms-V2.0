<?php
namespace app\manage\controller;
use think\Validate;

class Inst extends Base
{
    public function index()
    {
        $data=input('get.');
        //dump($data);
        $map=[];

        if(!empty($data['cname'])){
            $map['cname']=['like','%'.$data['cname'].'%'];
        }
        if(!empty($data['instrCategory'])){
            $map['instrCategory']=['like','%'.$data['instrCategory'].'%'];
        }
        if(@$data['states']!=''){
            $map['states']=$data['states'];
        }
        $res=model('Inst')->where($map)->order("id desc")->paginate(['query'=> $data]);
        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }

    public function detail(){
        $res=model('Inst')->where('id',input('id/d'))->find();
        //dump($res);
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }

    public function add(){

        $this->assign([
            'res'=>[
              'addtime'=>date('Y-m-d')
            ],
            'Action'=>'Add'
        ]);
        return $this->fetch();
    }

    public function edit(){

        $res=model('Inst')->where('id',input('id/d'))->find();
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
            'cname|仪器设备名称' => 'require|min:2',
            'instrCategory|设备分类编码' => 'require',
            'technical|主要技术指标' => 'require'
        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {

            if($data['Action']=='Add'){
                $data['userid']=session('userid');
                $data['addtime']=date('Y-m-d H:i:s');
                if(model('Inst')->allowField(true)->save($data)){
                    //htmlendjs('新增成功');
                    $this->success('新增成功','index');
                }
            }

            if($data['Action']=='Edit'){
                $data['addtime']=date('Y-m-d H:i:s');
                model('Inst')->allowField(true)->save($data,['id'=>input('id/d')]);
                htmlendjs('修改成功');
            }

        }
    }

    public function del(){
        $res=model('Inst')->where('id','in',input('post.id'))->delete();
        return $res;
    }
    public function chk(){
        $res=model('Inst')->save(['states'=>input('post.states/d')],[
            'id'=>input('post.id/d')
        ]);
        return $res;
    }

}