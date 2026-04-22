<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

//--------ライブラリ読込み------------------------

require('./lib/conf_common.php');
require('./lib/conf_fixed.php');
require($passlog);
require('./lib/func.php');
require($dbfile);
require($dbfieldsfile);

//-------変数-------------------------------------

$kijipl='./lib/admin/kiji_admin.php';
$makedbpl='./lib/admin/makedb.php';
$setpl='./lib/admin/set.php';
$csvpl='./lib/admin/csv.php';

$intvl=5;
$readnum=10;
$ascript='';
$adpagemax='20';
$cookie_adname='pbbs_admin';

//-----------------------------------------------

$o_main=new Main;
$o_func=new Func;
$o_db=new DB;

$fo=$o_main->ParseInt();

if($fo['mode']){
	$o_main->CheckPass($fo);
}

list($lib,$mode)=explode("_",$fo['mode']);

switch($lib){
	case "kiji":
		require($kijipl);
		$o_kiji=new Kiji;
		switch($mode){
			case "del":
				$o_kiji->Del($fo,$delnum);
				break;
			case "edit":
				$o_kiji->Edit($fo);
				break;
			case "editregist":
				$o_kiji->EditRegist($fo);
				break;
			case "tmp":
				$o_kiji->Tmp($fo);
				break;
			case "tmpdel":
				if($fo['edt'] == '削除'){
					$o_kiji->TmpDel($fo,$delnum);
				}
				else{
					$o_kiji->TmpRegist($fo,$delnum);
				}
				break;
			default:
				$o_kiji->Top($fo,"");
				break;
		}

	case "set":
		require($setpl);
		$o_set=new Set;
		switch($mode){
			case "regist":
				$o_set->Regist($fo);
				break;
			default:
				$o_set->Top($fo,"","");
				break;
		}
	case "makedb":
		require($makedbpl);
		$o_makedb=new MakeDB;
		switch($mode){
			case "top":
				$o_makedb->Top($fo,"");
				break;
			case "del":
				$o_makedb->Del($fo);
				break;
			case "table":
				$o_makedb->Table($fo);
				break;
			case "data":
				$o_makedb->Data($fo);
				break;
			default:
				$o_makedb->Top($fo,"");
				break;
		}
		break;
		
	case "csv":
		require($csvpl);
		$o_csv=new Csv;
		switch($mode){
			case "upload":
				$o_csv->Upload($fo);
				break;
			case "dl":
				$o_csv->Dl($fo);
				break;
			default:
				$o_csv->Top($fo);
				break;
		}
	default:
		switch($fo['mode']){
			case "menu":
				$o_main->Menu($fo);
				break;
			default:
				$o_main->Ent($fo);
				break;
		}
}



Class Main{


function MatchPass($pass,$coded){
$passflag=0;
if(crypt($pass,$coded) == $coded){
	$passflag=1;
}
return $passflag;
}


function CheckPass($f){
global $passwd;
global $o_func;
global $cookie_adname;

if($f['pass'] == ""){
	$m="";
	if($f['cookie']){
		$o_func->DelCookie($cookie_adname);
		$m="<br>クッキーを削除しました。";
	}
	self::Error("パスワードを入力してください。".$m);
}

if($passwd == ""){
	self::Error("パスワードが設定されていません。");
}

if($passwd == 'admin'){
	if($f['pass'] != $passwd){
		self::Error("パスワードが違います。");
	}
}
else{
	if(!self::MatchPass($f['pass'],$passwd)){
		self::Error("パスワードが違います。");
	}
}
return;
}


function Ent($fo){
global $cookie_adname;
global $ascript;
global $o_func;

$DUMMY=$o_func->GetCookie($cookie_adname);

if($DUMMY){
	$ck=" checked";
}

self::Head("");

print <<<EOM
<body>
<div class="midasi w300">管理用</div>
<p class="t_center">パスワードを入力してログインボタンを押してください。</p>
<form action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="menu">
<table class="table_fo box_center">
<tr>
<td>パスワード</td>
<td><input type="password" name="pass" maxlength="8" size="8" value="{$DUMMY}"> <input type="submit" value="ログイン"></td>
</tr>
<tr>
<td></td>
<td><input type="checkbox" name="cookie" value="on"{$ck}>クッキー</td>
</tr>

</table>
</form>

</html>
EOM;
exit;

}

function Menu($fo){
global $ascript;
global $cookie_adname;
global $cookie_hozon;
global $o_func;

if($fo['cookie']){
	$o_func->SetCookie($fo['pass'],$cookie_adname,"",$cookie_hozon);
}
else{
	$DUMMY=$o_func->GetCookie($cookie_adname);
	if($DUMMY){
		$o_func->DelCookie($cookie_adname);
	}
}


self::Head("");

print <<<EOM
<body>
<p class="t_center">機能を選んでボタンを押してください。</p>
<ul class="btn_table box_center">
<li><input type="button" value="記事の削除と編集" class="bt" onclick="jump('kiji')"></li>
<li><input type="button" value="ダウンロード" class="bt" onclick="jump('csv')"></li>
<li><input type="submit" value="環境設定" class="bt" onclick="jump('set')"></li>
<li><input type="button" value="データベース" class="bt" onclick="jump('makedb')"></li>
</ul>
<form name="fo" action="{$ascript}" method="POST" style="margin:0px">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="">
</form>
<script>
function jump(m){
document.fo.mode.value=m;
document.fo.submit();
}

</script>
</body>
</html>
EOM;
exit;
}



function Head($act){

header("Content-type: text/html");

print <<<EOM
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE">
<title>管理用</title>
<style type="text/css">
<!--

.box_center{
	margin: 0 auto;
}

.btn_table{
	width: 200px;
	list-style: none;
}

.btn_table li{
	margin-bottom: 15px;
}

ul {
    list-style: none;
    margin-left: 0;
    padding-left: 0;
}

.bt{
	width: 200px;
}

.b{
	font-weight: bold;
}

p,
td,
div{
	font-size: 13px;
}


.midasi{
	background-color: #924141;
	color: #ffffff;
	font-weight: bold;
	width: 870px;
	margin: 0 auto;
	text-align: center;
}

.p_box{
	width: 870px;
	margin: 0 auto;
}

.w300{
	width: 300px;
}

.t_center{
	text-align: center;
}

.page{
	width: 870px;
}

.aste{
	color: #cc0000;
}

.site_tbl{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}

.site_tbl tr{
	background-color:#FFF8F4;
}

.site_tbl td{ 
	border:1px dotted #D0B195;
	border-collapse:collapse;
}

.site_tbl thead tr{
	background-color: #F9EFEC;
}

.site_tbl thead td{
	font-weight: bold;
}

.set_tbl{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}
.set_tbl td{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}


.bg{
	background-color:#FFF8F4;
}

.komoku{
	font-weight: bold;
	background-color:#ECDCD7;
}

.noline{
	border:none;
}
.noline td{
	border:none;
}
a.navi:link{
	text-decoration:none;
	color:#0000ff;
}
a.navi:visited{
	text-decoration:none;
	color:#0000ff;
}
a.navi:active{
	text-decoration:none;
	color:#0000ff;
}
a.navi:hover{
	position:relative;left:1px;top:1px;
	color:#0000ff;
}


.btn1{
	width: fit-content;
}

.btn2{
	display: none;
	width: fit-content;
}


.cl{
	width: 100%;
}

.cl:after {
	content: ""; 
	display: block; 
 	clear: both;
}

.mb5{
	margin-bottom: 5px;
}

.mt10{
	margin-top: 10px;
}

.mt20{
	margin-top: 20px;
}

.mt4{
	margin-top: 4px;
}

.mb20{
	margin-bottom: 20px;
}


ul.pagelink{
	list-style-type: none;
	margin: 0;
}

ul.pagelink li{
	float: left;
}

ul.pagelink a, ul.pagelink li.current{
	display: block;
	width: 20px;
	line-height: 20px;
	text-align: center;
	text-decoration: none;
	border-style: solid;
	border-color: #bebebe;
	border-width: 1px;
	margin-right: 3px;
	padding:1px;
	font-size: 13px;
}

ul.pagelink li.ten{
	width: 15px;
	text-align: center;
	margin-right: 3px;
	padding:1px;
}

ul.pagelink a:hover{
	background-color: #F0F0F0;
}

ul.pagelink:after{
	display: block;
	clear: both;
	content: '';
}

ul.pagelink li.current{
	font-weight:bold;
}


div.page {
	position: relative;
	overflow: hidden;
	margin-bottom: 10px;
}


div.page ul {
	float:left;
	left:0%;
	position:relative;
	padding:0px;
}

div.page li {
	float:left;
	left:0%;
	position:relative;
}

#loader{
	display: none;
	width: 300px;
	height: 200px;
	margin: 20px auto 0 auto;
	text-align: center;
	border-style: solid;
	border-color: #000000;
	border-width: 1px;
	padding: 10px;
	box-sizing: border-box;
}

#msg{
	margin-top: 15px;
	margin-bottom: 10px;
}

#done{
	text-align: center;
}

.r{
	color:#cc0000;
}

.center{
	margin: 0 auto;
}

.tb{
	display: table;
	border-collapse: collapse;
	
}
.tb>div{
	display: table-row;
}
.tb>div>div{
	display: table-cell;
	padding: 2px;
	box-sizing: border-box;
}

.tb>div>div:first-child{
	vertical-align: middle;
	width: 100px;
	font-weight: bold;
}

#hissu{
	text-align: center;
	margin-bottom: 10px;
}



.db_tbl{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}

.db_tbl tr{
	background-color:#FFF8F4;
}

.db_tbl td{ 
	border:1px dotted #D0B195;
	border-collapse:collapse;
}

.db_tbl thead tr{
	background-color: #F9EFEC;
}

.db_tbl thead td{
	font-weight: bold;
}

.tb_tbl{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}

.tb_tbl tr{
	background-color:#FFF8F4;
}

.tb_tbl td{ 
	border:1px dotted #D0B195;
	border-collapse:collapse;
}

.tb_tbl thead tr{
	background-color: #F9EFEC;
}

.tb_tbl thead td{
	font-weight: bold;
}

.data_tbl{ 
	border:1px solid #D0B195;
	border-collapse:collapse;
}

.data_tbl tr{
	background-color:#FFF8F4;
}

.data_tbl td{ 
	border:1px dotted #D0B195;
	border-collapse:collapse;
}

.data_tbl thead tr{
	background-color: #F9EFEC;
}

.data_tbl thead td{
	font-weight: bold;
}

@media screen and (max-width:600px){
	div,
	p,
	td{
		font-size: 15px;
		line-height:150%;
	}

	body{
		font-size: 15px;
		line-height:150%;
	}
	
	div,p,td,th{
		font-size: 15px;
		line-height:150%;
	}

	input[type=text],input[type=password],textarea{
		border: 1px solid #999999;
		border-radius: 3px;
		margin-bottom: 1px;
	}
	
	select{
		border: 1px solid #999999;
		width: 80px;
	}

	input,select {
	 	height: 34px;
	 	box-sizing: border-box;
	}

	textarea {
		box-sizing: border-box;
		line-height: 1.25;
		padding: 4px;
	}
	
	input[type=checkbox]{
		width: 24px;
		height: 24px;
	}

	.table_fo {
		width: 100%;
	}

	.table_fo tr,
	.table_fo th,
	.table_fo td {
		display: block;
		width: auto;
		padding: 2px;
	}
	
	.table_fo tr{
		margin-bottom: 20px;
	}

	.table_fo th {
		padding-bottom: 0;
	}

	.table_fo input[type='text'],
	.table_fo input[type='password'],
	.table_fo select,
	.table_fo textarea {
		width: 100%;
	}

	.btn_table li{
		margin-bottom: 20px;
	}
	
	.midasi{
		width: 100%;
	}
	
	.p_box{
		width: 100%;
	}
	
	.set_tbl{ 
		border: 0;
	}
	.set_tbl td{ 
		border: 0;
	}
	
	.touroku_tbl td{
		border: 0;
	}

	.site_tbl {
		border: none;
		width: 100%;
	}
	.site_tbl thead {
		display: none;
	}

	.site_tbl tr {
		display: block;
		width: auto;
		border: 1px solid #D0B195;
		border-radius: 5px;
		margin-bottom: 20px;

	}
	.site_tbl td {
		display: block;
		border: none;
		border-bottom: 1px dotted #D0B195;
		text-align: left;
		padding: 3px;

	}
	.site_tbl td:nth-last-child(2) {
		border-bottom: none;
	}
	.site_tbl td:last-child {
		display: none;
	}
	.site_tbl td:nth-of-type(n+1):nth-of-type(-n+6):before {
		content: attr(data-label);
		display: block;
		font-weight: bold;
		float: left;
		padding-right: 5px;
		white-space: nowrap;
		width: 70px;
		border-right-style: dotted;
		border-right-width: 1px;
		border-color: #D0B195;
	}
	

	.comment{
		width: 100%;
	}
	
	.btn1{
		display: none;
	}

	.btn2{
		display: inline;
	}
	
	.page{
		width: 100%;
	}


	ul.pagelink a, ul.pagelink li.current{
		display: block;
		width: 30px;
		line-height: 30px;
		text-align: center;
		text-decoration: none;
		border-style: solid;
		border-color: #bebebe;
		border-width: 1px;
		margin-right: 5px;
		padding:1px;
		font-size: 13px;
		border-radius: 3px;
	}
	
	ul.pagelink li.ten{
		width: 20px;
		text-align: center;
		margin-right: 5px;
		padding:1px;
	}

	ul.pagelink a:hover{
		background-color: #F0F0F0;
	}
	
	.tb {
		width: 98%;
		display: block;
	}
	
	.tb>div{
		width: 100%;
		display: block;
	}
	.tb>div>div{
		width: 100%;
		display: block;
		padding: 5px;
	}
	
	.tb>div>div:first-child{
		width: 100%;
	}

	.tb input[type='text'],
	.tb input[type='file'],
	.tb select,
	.tb textarea {
		width: 100%;
	}
	
	
	.db_tbl {
		border: none;
		width: 100%;
	}
	.db_tbl thead {
		display: none;
	}

	.db_tbl tr {
		display: block;
		width: auto;
		border: 1px solid #D0B195;
		border-radius: 5px;
		margin-bottom: 20px;

	}
	.db_tbl td {
		display: block;
		border: none;
		border-bottom: 1px dotted #D0B195;
		text-align: left;
		padding: 3px;

	}
	.db_tbl td:nth-last-child(1){
		border-bottom: none;
	}
	
	.db_tbl td:nth-of-type(1):before {
		content: attr(data-label);
		display: block;
		font-weight: bold;
		float: left;
		padding-right: 5px;
		white-space: nowrap;
		width: 120px;
		border-right-style: dotted;
		border-right-width: 1px;
		border-color: #D0B195;
	}
	
	.tb_tbl {
		border: none;
		width: 100%;
	}
	.tb_tbl thead {
		display: none;
	}

	.tb_tbl tr {
		display: block;
		width: auto;
		border: 1px solid #D0B195;
		border-radius: 5px;
		margin-bottom: 20px;

	}
	.tb_tbl td {
		display: block;
		border: none;
		border-bottom: 1px dotted #D0B195;
		text-align: left;
		padding: 3px;

	}
	
	.tb_tbl td:nth-last-child(1){
		border-bottom: none;
	}

	.tb_tbl td:nth-of-type(n+1):nth-of-type(-n+4):before {
		content: attr(data-label);
		display: block;
		font-weight: bold;
		float: left;
		padding-right: 5px;
		white-space: nowrap;
		width: 120px;
		border-right-style: dotted;
		border-right-width: 1px;
		border-color: #D0B195;
	}
}

-->
</style>
<script>
function openwin(win,w,h){
var features="toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,top=0,left=0,"+"width="+w+",height="+h;
window.open(win,"s_win",features);
}

function Preview(){
var features="toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,top=0,left=0,width=800,height=600";
window.open("","pre",features);
}

function delcheck(){
if(window.confirm("本当に削除しますか？")){ 
return true;
} 
else{
return false;
}
}



EOM;


if($act){
print <<<EOM
function Alert(){
	alert("{$act}");
}
window.onload=Alert;
EOM;
}


print <<<EOM
</script>
</head>
EOM;

}


function Error($e){

self::Head("");

print <<<EOM
<body>
ERROR
EOM;

print "$e\n";

print <<<EOM
</body>
</html>
EOM;

exit;

}

function JsError($e){

header("Content-type: text/json");

$jsline='{"result":"err",';
$jsline.='"msg":"'.$e.'"';
$jsline.='}';

header("Content-type: text/json");
print $jsline;

exit;

}

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
	$value=str_replace('"',"&quot;",$value);
	$value=str_replace("'","&#039;",$value);
	$value=str_replace("<","&lt;",$value);
	$value=str_replace(">","&gt;",$value);
	if($name == "comment" || $name == "khost" || $name == "kword"){
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



}//Class End

exit;

?>