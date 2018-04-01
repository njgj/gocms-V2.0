<?php
namespace app\manage\controller;
use think\Validate;

class Tj extends Base
{
    public function inst($Action='list')
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
        if(!empty($data['lab_no'])){
            $map['lab_no']=['like','%'.$data['instrCategory'].'%'];
        }
        $map['is_check']=1;
        //SELECT cname,lab_no,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all FROM v_inst_yy where is_check=1 group by cname order by kjs desc
        $res=db('v_inst_yy')
            ->field('cname,lab_no,FLOOR(sum(TIME_TO_SEC(timediff(e_time,b_time)))/60) as kjs,COUNT(ID) as cs,count(distinct instid) as fws,sum(feeB) as fees,sum(yps) as yps_all')
            ->where($map)
            ->group('cname')
            ->order("kjs desc")->paginate(['query'=> $data]);
        //dump($res);
        $this->assign([
            'Action'=>$Action,
            'res'=>$res
        ]);
        return $this->fetch();
    }
    public function user_yy()
    {

        return $this->fetch();
    }
    public function year_yy()
    {

        return $this->fetch();
    }
    public function year_daoshi()
    {

        return $this->fetch();
    }
    public function user_dept()
    {

        return $this->fetch();
    }
}