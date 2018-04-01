<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/*function json($code,$msg="",$count,$data=array()){
  $result=array(
   'code'=>$code,
   'msg'=>$msg,
   'count'=>$count,
   'data'=>$data
  );
  //输出json
  return json_encode($result,JSON_UNESCAPED_UNICODE);
}*/


function tree($data,$pid=0,$level = 1){
	static $arr=array();
	foreach($data as $v){
		if($v['parentid']==$pid){
			$v['level']=$level;
			$arr[]=$v;//将结果装到$arr中
			tree($data,$v['classid'],$level+1);
		}
	}
	return $arr;
}

function getTreeOption($tableName,$pid=0,$defaultid=0,$self=0){
    $map='FIND_IN_SET('.$pid.',classpath)';
    $map.=($self==0)?' and classid!='.$pid:'';
    $data=db($tableName)->where($map)->order('orderid')->select();
    //dump($data);
    $str='';
    if($data) {
        $arr = tree($data,$pid);
        foreach ($arr as $v) {
            $slt = '';
            if ($v['classid'] == $defaultid) {
                $slt = ' selected';
            }
            $str .= "<option value='$v[classid]' $slt>" . str_replace('-', '——', str_pad('', $v['level'] - 1, '-')) . "$v[classname]</option>";
        }
    }
	return $str;
}

/* 通用alert提示 */  
function htmlendjs($msg,$act='',$url='') {
   $str="<script>parent.layer.msg('$msg',{icon: 1, time: 2000}, function(){parent.layer.closeAll();parent.location.reload() });</script>";
	echo $str;
}

/* 审核状态显示 */
function chkState($num,$font=1) {
    if($num==-3){$v='<span class="layui-badge">抽查未通过</span>';}
    if($num==-2){$v='<span class="layui-badge">审核未通过</span>';}
    if($num==-1){$v='<span class="layui-badge layui-bg-orange">退回修改</span>';}
    if($num==0){$v='<span class="layui-badge layui-bg-black">等待审核</span>';}
    if($num==1){$v='<span class="layui-badge layui-bg-green">审核通过</span>';}
    if($num==2){$v='<span class="layui-badge layui-bg-blue">已置顶</span>';}
    if(is_null($num)){$v='<span class="layui-badge layui-bg-gray">正在填写</span>';}
    $v=($font)?$v:strip_tags($v);
    return $v;
}

function getStateOption($st='',$no='-3,-1,2'){
	$str='';
	for($i=-3;$i<=2;$i++){
	   if(strpos(','.$no.',',','.$i.',')===false){
	       $str.="<option value='$i'";
	       if($st!='' && $st==$i){$str.=' selected';}
	       $str.=">".chkState($i,0)."</option>";
	   } 
	}
    return "<option value=''>- 状态 -</option>".$str;
}

/* 审核是否 */
function chkIsYes($st,$font=1) {
    if($st==0){$v='<span class="layui-badge">否</span>';}
    if($st==1){$v='<span class="layui-badge layui-bg-green">是</span>';}
    $v=($font)?$v:strip_tags($v);
    return $v;
}

/*搜索结果加亮 */
function replaceKey($key,$text){
    $keys = explode(' ', $key);
    foreach($keys as $v){
        if(preg_match('/'.$v.'/iSU', $text)){
            $text = str_replace($v, '<font color="#FF0000">'.$v.'</font>', $text);
        }
    }
    return $text;
}

/*
inputName:控件名称 
defaultValue:默认值
defaultName:默认名称*/
function isyes($inputName,$defaultValue=NULL,$defaultArray=array('是'=>1,'否'=>0)){
	foreach($defaultArray as $k=>$v){
		   $nstr='';
		   if($v==$defaultValue){
			  $nstr=' checked'; 
		   }
		   $str.="<input type='radio' name='$inputName' id='$inputName$v' value='$v'$nstr><label for='$inputName$v'>$k</label>&nbsp;";
	}
	return $str;
}

/*inputType:radio/checkbox/select
inputName:控件名称
id:所属大类id
defaultValue:默认值(多值用","分隔)
zdname:返回字段*/
function selectBox($tableName,$inputType,$inputName,$id,$defaultValue=NULL,$zdName='classid'){
    global $db;
    $classname=getClassName($tableName,$id);
    $res=db($tableName)->where('parentid',$id)->order('orderid')->select();
    $str=$mstr='';
    foreach ($res as $row){
        if(strtolower($inputType)=='radio' || strtolower($inputType)=='checkbox'){
            $flag=false;
            $arr=explode(',',$defaultValue);
            foreach($arr as $v){
                if(!empty($v)){
                    if($v==$row[$zdName]){
                        $flag=true;
                        break;
                    }
                }
            }
            $ctr=(strtolower($inputType)=='checkbox')?'[]':'';
            $mstr=($flag==true)?' checked':'';
            $str.="<input type='".strtolower($inputType)."' name='".$inputName.$ctr."' value='".$row[$zdName]."'$mstr  title='".$row['classname']."'/>&nbsp;";
        }
        if(strtolower($inputType)=='select'){
            if(!empty($defaultValue)){
                $mstr=($row[$zdName]==$defaultValue)?' selected':'';
            }
            $str.="<option value='".$row[$zdName]."'$mstr>".$row['classname']."</option>";
        }
    }
    if(strtolower($inputType)=="select"){
        $str2="<select name='".$inputName."'><option value=''>— 请选择 —</option>".$str."</select>";
    }else{
        $str2=$str;
    }
    return $str2;
}

/*'id:所属大类id
'defaultValue:默认值(多值用","分隔)*/
function readCheckBox($tableName,$id,$defaultValue,$zdName='classid'){
    global $db;
	$sql="select * from $tableName where parentid=$id order by orderid";
	$query=$db->query($sql);
	while($row=$db->fetch_array($query)){
			$flag=false;
			$arr=explode(',',$defaultValue);
			foreach($arr as $v){
			    if(!empty($v)){
				    if($v==$row[$zdName]){
					     $flag=true;
						 break;
					}
				}
			}
			$mstr=($flag==true)?'checked':'';
			$str.="<input type=checkbox name=s value='".$row[$zdName]."' $mstr onclick='return false'/>".$row['classname']."&nbsp;";
	}
	return $str;
}


//获取类名
function getClassName($tablename,$classid){
    return db($tablename)->where('classid',$classid)->value('classname');
}
function getClassPath($tablename,$classpath,$line=' > '){
    $arr=explode($classpath,',');
    $str='';
    foreach ($arr as $v){
        if(strlen($v)){
            $str.=getClassName($tablename,$v).$line;
        }
    }
    return $str;
}
function getCodeName($tablename,$code){
    return db($tablename)->where('code',$code)->value('classname');
}
function getCodePath($tablename,$code,$line=' > '){
    $str='';
    switch (strlen($code)){
        case strlen($code)<4:
            $str=getCodeName($tablename,$code);
            break;
        case 4:
            $str=getCodeName($tablename,substr($code,0,2)).$line;
            $str.=getCodeName($tablename,$code);
            break;
        case 6:
            $str=getCodeName($tablename,substr($code,0,2)).$line;
            $str.=getCodeName($tablename,substr($code,0,4)).$line;
            $str.=getCodeName($tablename,$code);
            break;
    }
    return $str;
}

//获取用户名称
function getUserName($userid){
    return db('user_info')->where('userid',$userid)->value('username');
}

//获取用户组名称
function getGroupName($groupid){
    return db('user_group')->where('groupid',$groupid)->value('groupname');
}
function getGroupOption($groupid){
    $res=db('user_group')->order('orderid')->select();
    $str='';
    foreach ($res as $row){
        $str.="<option value='".$row['groupid']."'";
        if($row['groupid']==(int)$groupid){ $str.=' selected'; }
        $str.='>'.$row['groupname'].'</option>';
    }
    return "<option value=''>- 用户类型 -</option>".$str;
}

//inst
function getInstOption($id){
    $res=db('Inst')->where('states>0')->select();
    $str='';
    foreach ($res as $row){
        $str.="<option value='".$row['id']."'";
        if($row['id']==(int)$id){ $str.=' selected'; }
        $str.='>'.$row['cname'].'</option>';
    }
    return "<option value=''>- 请选择仪器 -</option>".$str;
}

//order编码
function getYyBm(){
    $now=date('ymd');
    $max=db('inst_yy')->where("date(person_addtime)='".date('Y-m-d')."'")->max('right(bm,3)',false)+1;
    //return $max;
    return $now.'-'.substr('00'.$max,-3);
}

//导出文件（数据表格）
function export($type,$html,$filename='导出文件'){
    $mime = array(
        'doc'        => 'application/msword',
        'pdf'        => 'application/pdf',
        'xls'        => 'application/vnd.ms-excel',
        'ppt'        => 'application/vnd.ms-powerpoint',
    );
    if(preg_match("@<table(.*?)</table>@is", $html, $matchs)){
        $str=$matchs[0];

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:$mime[$type]]");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header("Content-Disposition:attachment;filename=$filename.$type");
        header("Content-Transfer-Encoding:binary");
        echo $str;
    }
}
