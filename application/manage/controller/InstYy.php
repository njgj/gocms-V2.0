<?php
namespace app\manage\controller;
use think\Request;
use think\Validate;

class InstYy extends Base
{
    public function index($Action='list')
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

        $list=db('v_inst_yy')->where($map)->order("id desc");
        if($Action=='list'){
            $res=$list->paginate(['query'=> $data]);
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            return $this->fetch();
        }else{
            $res=$list->select();
            $this->assign([
                'Action'=>$Action,
                'res'=>$res
            ]);
            export($Action,$this->fetch());
        }

        //dump($res);

    }

    public function detail(){
        $res=db('v_inst_yy')->where('id',input('id/d'))->find();
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

        $res=db('v_inst_yy')->where('id',input('id/d'))->find();
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
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

    public function update()
    {
        $data = input('post.');
        $rule = [
            'r_date|预约日期' => 'require|date',
            'b_time|开始时间' => 'require',
            'e_time|结束时间' => 'require',
            'feeB|实际费用' => 'require|number',
            'is_check|审核状态' => 'require|integer',
            'remark|审核意见' => 'requireIf:is_check,-2',

        ];
        //$validate = Validate::make($rule, $msg);
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        } else {
            $data['userid']=session('userid');
            $data['check_time']=date('Y-m-d H:i:s');
            model('InstYy')->allowField(true)->save($data,['id'=>input('id/d')]);
            htmlendjs('审核成功');

        }
    }

    public function del(){
        $res=model('InstYy')->where('id','in',input('post.id'))->delete();
        return $res;
    }

}