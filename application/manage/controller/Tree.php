<?php
namespace app\manage\controller;
use think\Validate;

class Tree extends Base
{
    public function index(){
        $groupid=input('groupid/d');
        $qx=db('UserGroup')->where('groupid',$groupid)->value('qx');
        $data=db('UserQx')->order('orderid')->select();
        $arr=[];
        foreach ($data as $v){
            if( strpos(','.$qx.',', ','.$v['classid'].',') !== false){
                $chk=true;
                }else{
                $chk=false;
                }
            $arr[]=[
                'id'=>$v['classid'],
                'pId'=>$v['parentid'],
                'name'=>$v['classname'],
                'open'=>true,
                'checked'=>$chk
            ];
        }
        $this->assign([
            'groupid'=>$groupid,
            'res'=>json_encode($arr,JSON_UNESCAPED_UNICODE)
        ]);
        return $this->fetch();
    }

}