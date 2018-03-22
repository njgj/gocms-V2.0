<?php
header('content-type:text/html;charset=utf-8');
date_default_timezone_set('PRC');  
error_reporting(E_ALL & ~E_NOTICE);

$path=$_GET['path'];
$type=$_GET['type'];
$inputid=$_GET['inputid'];
$imgid=$_GET['imgid'];

switch (strtolower($type)){
case 'pdf':
  $type='pdf';
  break; 
case 'file':
  $type='doc|xls|ppt|pdf|rar';
  break;
case 'media':
  $type='wma|rm|avi|swf';
  break;
default:
  $type='jpg|gif|bmp|png';
}

/**
 * 上传文件函数
 * 参数：表单属性名，文件保存路径，文件最大尺寸（KB），允许的类型。
 */
function upfile ($file,$path,$maxsize=1000,$type="jpg|gif|bmp|png",$inputid='imgurl',$imgid='img') {
  if(!is_array($_FILES[$file]))return false;
  $filename=$_FILES[$file]['name'];  //文件名称不带路径
  $tmpname=$_FILES[$file]['tmp_name']; //临时文件名称带路径，去除转义
  $filesize=abs($_FILES[$file]['size']); //文件大小
  $fileerror=$_FILES[$file]['error'];  //文件上传错误信息  
  $filename_arr=explode('.',$filename);
  $filetype=strtolower(end($filename_arr));  //获取文件后缀名不带.
  $inputid=(empty($inputid))?'imgurl':$inputid;
  $imgid=(empty($imgid))?'img':$imgid;
  
  //路径判断
  if(empty($path)){
  	   return "路径不能为空";
  }else{
  	  $path=str_replace('\\','',$path);
      $path=str_replace('/','',$path);
      $path='../uploadfile/'.$path;
      if(!is_dir($path))return "文件保存路径 {$path} 不存在";
      if(!is_writable($path))return "权限不足，无法写入文件";  
  }
  
  if($filesize<1)return "请选择上传文件";
  if($filesize>$maxsize*1024)return "请不要上传超过 {$maxsize}K 的文件";
  if(!in_array($filetype,explode('|',$type)))return "你能上传的文件类型为 $type";
  if($fileerror)return "未知错误，上传失败";  
  if(!is_uploaded_file($tmpname)){
	 return '需要移动的文件不存在';
  }  
  
  $newfilename=date("YmdHis").rand(100,999).".".$filetype;
  $newfilepath=$path."/".$newfilename;  // ../uploadfile/image/20170224093657714.jpg
  //$savefilepath=dirname(__FILE__).'/'.$newfilepath;
    
  if(@move_uploaded_file($tmpname,$newfilepath)){
    @chmod($newfilepath, 0777);
    //$newfile['name']=$newfilename;
    //$newfile['size']=round($filesize/1024,2);
    return $filename."上传成功<script>window.parent.document.getElementById('$inputid').value='$newfilename';
	if(window.parent.document.getElementById('$imgid')){window.parent.document.getElementById('$imgid').src='../$newfilepath';}</script>";
  }else{
    return "未知错误2，上传失败";
	//return $savefilepath;
  }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文件上传</title>
<script type="text/javascript" src="jquery-1.8.3.min.js"></script>
<style type="text/css">
<!--
body,form {
	margin: 1px;
	padding:0;
	font-size: 12px;
	text-align:center;
}
.a-upload {
    width:80px;
	text-align:center;
    height: 22px;
    line-height: 22px;
    position: relative;
    cursor: pointer;
    color: #1E88C7;
	text-decoration:none;
    background: #D0EEFF;
    border: 1px solid #99D3F5;
    border-radius: 4px;
    overflow: hidden;
    display: inline-block;
    *display: inline;
    *zoom: 1
}

.a-upload  input {
    position: absolute;
    font-size: 100px;
    right: 0;
    top: 0;
    opacity: 0;
    filter: alpha(opacity=0);
    cursor: pointer
}

.a-upload:hover {
    color: #004974;
    background: #AADFFD;
    border-color: #78C3F3;
    text-decoration: none
}
-->
</style>
<script>
$(function(){
	$("input[type='file']").val('');
	$(".a-upload").on("change","input[type='file']",function(){
		    var filePath=$(this).val();
		    //alert(filePath);
			$('form').submit();	
	})
});
</script>
</head>

<body>
<?php 
if($_FILES['pictures']){
	echo upfile ('pictures',$_POST['path'],2000,$_POST['type'],$_POST['inputid'],$_POST['imgid']);
	echo " <a href='javascript:history.back();'>重新上传</a>";
	exit();
}
?>

<form action="upload.php" method="post" enctype="multipart/form-data">
  <a href="javascript:;" class="a-upload">
  <input type="file" name="pictures" id="pictures"/>点击上传
  </a>
  <input type="hidden" name="path" id="path" value="<?php echo $path ?>"/>
  <input type="hidden" name="type" id="type" value="<?php echo $type ?>"/>
  <input type="hidden" name="inputid" id="inputid" value="<?php echo $inputid ?>"/>
  <input type="hidden" name="imgid" id="imgid" value="<?php echo $imgid ?>"/>
</form>
</body>
</html>