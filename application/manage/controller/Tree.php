<?php
namespace app\manage\controller;
use think\Validate;

class Tree extends Base
{
    public function index($tb,$id=0){
        $data=db($tb)->where("find_in_set($id,classpath)")->select();
        $arr=[];
        foreach ($data as $v){
            $arr[]=[
                'id'=>$v['classid'],
                'pId'=>$v['parentid'],
                'name'=>$v['classname'],
                'open'=>true,
                'checked'=>false
            ];
        }
		$res=json_encode($arr,JSON_UNESCAPED_UNICODE);
		//return $res;
        $this->assign([
            'res'=>$res
        ]);
        return $this->fetch();
    }

}