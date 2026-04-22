<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

//--------ライブラリ読込み------------------------

require('./lib/conf_common.php');
require('./lib/conf_fixed.php');
require($dbfile);
require($dbfieldsfile);
require('./lib/func.php');
require('./lib/kiji.php');

//-----------------------------------------------


$o_db=new DB;
$o_main=new Main;
$o_kiji=new Kiji;
$o_func=new Func;
$fo=$o_main->ParseInt();
list($lib,$mode)=explode("_",$fo['mode']);


switch($fo['mode']){
	case "res":
		$o_kiji->Res($fo);
		break;
	case "regist":
		$o_kiji->Regist($fo);
		break;
	case "usr":
		if($fo['act'] == 'edit'){
			$o_kiji->Edit($fo);
		}
		else{
			$o_kiji->Del($fo);
		}
		break;
	case "howto":
		$o_kiji->Howto($fo);
		break;
	case "editregist":
		$o_kiji->EditRegist($fo);
		break;
	case "enquet":
		$o_kiji->Enquet($fo);
		break;
	case "search":
		$o_kiji->Search($fo);
		break;
	default:
		$o_kiji->Top($fo);
		break;
}



Class Main{

function ParseInt(){

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$ary=&$_POST;
}
else{
	$ary=&$_GET;
}

$in=array();
foreach($ary as $name => $value){
	$value=str_replace("&","&amp;",$value);
	$value=str_replace(",","&#44;",$value);
	$value=str_replace("\"","&quot;",$value);
	$value=str_replace("'","&#039;",$value);
	$value=str_replace("<","&lt;",$value);
	$value=str_replace(">","&gt;",$value);
	if($name == 'comment'){
		$value=str_replace("\r\n","<br>",$value);
		$value=str_replace("\n","<br>",$value);
	}
	else{
		$value=str_replace("\r\n","",$value);
		$value=str_replace("\n","",$value);
	}
	$name=SQLite3::escapeString($name);
	$value=SQLite3::escapeString($value);
	$in[$name]=$value;

}

return $in;
}


function Error($e){
header("Content-type: text/html");
print <<<EOM
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>ERROR</title>
<style type="text/css">
</style>
</head>
<body>
<div align="center"><b>ERROR</b>
EOM;

print "$e\n";

print <<<EOM
</div>
<p align="center"><a href="#" onclick="history.back()">戻る</a></p>
</body>
</html>
EOM;
exit;

}

function JsError($e){

header("Content-type: text/json");

$jsline='{"result":"err",';
$jsline.='"err":"'.$e.'"';
$jsline.='}';

header("Content-type: text/json");
print $jsline;

exit;

}

}//Class End




?>