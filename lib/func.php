<?php


Class Func{

function UploadTmp(){
global $tmplog;
global $o_main;

foreach($_FILES as $key => $hash){
	$size=$_FILES[$key]['size'];
	$name=$_FILES[$key]['name'];
	$type=$_FILES[$key]['type'];
	$tmpfile=$_FILES[$key]['tmp_name'];
	
	list($dum,$t)=explode("/",$type);
	$ext="";
	if($t == 'csv' && preg_match("/\.csv$/",$name)){
		$ext="csv";
	}
	if(!$ext){
		$o_main->Error("アップロードできないファイル形式です。");
	}
	if($_FILES[$key]['error']){
		$o_main->Error("{$key}のアップロードに失敗しました。");
	}
	$filename=$tmplog;
	if(!move_uploaded_file($tmpfile,$filename)){
		$o_main->Error("アップロードに失敗しました。");
	}

}

}


function Upload(){
global $maxsize;
global $upimgdir;
global $o_main;

$key='upfile';

$s=$_FILES[$key]['size'];
$n=$_FILES[$key]['name'];
$t=$_FILES[$key]['type'];
$tmp=$_FILES[$key]['tmp_name'];

$ext=self::FileType($t);
if(!$ext){
	$o_main->Error("アップロードできないファイル形式です。");
}

if($s > $maxsize){
	$o_main->Error("投稿のサイズが大きすぎます。");
}
if($_FILES[$key]['error']){
	$o_main->Error("{$key}のアップロードに失敗しました。");
}
$na=time().getmypid().'.'.$ext;
$imgfile=$upimgdir.$na;

if(!move_uploaded_file($tmp,$imgfile)){
	$o_main->Error("アップロードに失敗しました。");
}
$size=@getimagesize($imgfile);
$w=$size[0];
$h=$size[1];

return array($na,$w,$h);
}


function FileType($t){
global $UPOK;
if($t == ""){
	return;
}
list($dum,$t)=explode("/",$t);

$type=array('jpeg'=>'jpg','gif'=>'gif','png'=>'png');

if(!$type[$t]){
	return;
}
$tt=$type[$t];

if(!$UPOK[$tt]){
	return;
}
return $tt;
}



function GetDate(){
$date=localtime();
$sec=$date[0];
$sec=sprintf("%02d",$sec);
$min=$date[1];
$min=sprintf("%02d",$min);
$hour=$date[2];
$mday=$date[3];
$mday=sprintf("%02d",$mday);
$mon=$date[4];
$mon++;
$mon=sprintf("%02d",$mon);
$year=$date[5];
$year=$year+1900;
$wday=$date[6];
$week=array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
$date="$year/$mon/$mday({$week[$wday]}) $hour:$min";
return $date;
}


function TagCheck($ar){
global $avoid;
foreach($avoid as $v){
	if(preg_match("/$v/",$ar)){
		self::Error("禁止タグが含まれています。");
	}
}
}


function GetHost(){
$ip=$_SERVER['REMOTE_ADDR'];
$host=$_SERVER['REMOTE_HOST'];

if($host == ""){
	$host=gethostbyaddr($ip);
}

if($host){
	$ho=$host;
}
else{
	$ho=$ip;
}

return $ho;
}


function Encode($pass){
$str=array_merge(range('a','z'),range('A','Z'),range('0','9'));
$max=22;
$salt='$2y$04$';
for($i=0;$i<$max;$i++) {
	$salt.=$str[rand(0,count($str)-1)];
}
$newcode=@crypt($pass,$salt);
return $newcode;
}

function MatchPass($pass,$coded){
$passflag=0;
if(crypt($pass,$coded) == $coded){
	$passflag=1;
}
return $passflag;
}


function SetCookie($cook,$cookie_name,$path,$time){


if($path == "root"){
	$cpath='/';
}
else{
	$cpath="";
}

if($time){
	$ctime=time()+$time*24*3600;
}
else{
	$ctime=0;
}


setcookie($cookie_name,$cook,$ctime,$cpath);

}

function DelCookie($cookie_name){
setcookie($cookie_name,'');
}

function GetCookie($cookie_name){
$cookies=$_SERVER['HTTP_COOKIE'];
$DUMMY=array();
$pairs=explode(";",$cookies);
foreach($pairs as $v){
	list($name,$value)=explode("=",$v);
	$DUMMY[$name]=urldecode($value);
}

return $DUMMY[$cookie_name];
}


function S_Mail($to,$from,$fromname,$sub,$mailbody){
global $sendmail;
global $o_main;

if($to == ""){
	$o_main->Error("送信先メールアドレスがありません。");
}
if(!preg_match("/^[a-zA-Z0-9\.\-_]+@[a-zA-Z0-9\.\-_]+$/",$to)){
	$o_main->Error("メールアドレスが正しくありません。");
}

if($from == ""){
	$from=$to;
}

if(preg_match("/,/",$from) || preg_match("/;/",$from)){
	$o_main->Error("メールアドレスが不適切です。");
}
if(strlen($from) > 100){
	self::Error("メールアドレスが不適切です。");
}
if(!preg_match("/^[a-zA-Z0-9\.\-_]+@[a-zA-Z0-9\.\-_]+$/",$from)){
	$o_main->Error("メールアドレスが正しくありません。");
}

if($fromname){
	$fromname=mb_encode_mimeheader(mb_convert_encoding($fromname,"JIS","UTF-8"),"JIS");
	$fromname="$fromname <$from>";
}
else{
	$fromname=$from;
}
if($sub){
	$sub=mb_encode_mimeheader(mb_convert_encoding($sub,"JIS","UTF-8"),"JIS");
}
$mailbody=mb_convert_encoding($mailbody,"JIS","UTF-8");
$m=popen("{$sendmail} -t -i -f {$to}", "w");
fputs($m,"To: $to\n");
fputs($m,"From: $fromname\n");
fputs($m,"Subject: $sub\n");
fputs($m,"MIME-Version: 1.0\n");
fputs($m,"Content-Type: text/plain\; charset=\"ISO-2022-JP\"\n");
fputs($m,"Content-Transfer-Encoding: 7bit\n\n");
fputs($m,"$mailbody\n");
fclose($m);
}

function UpdateCount(&$o_db,&$pdo){
global $countlog;

$stmt=$o_db->SetQuery("SELECT count(no) FROM kiji WHERE resno='0'",$pdo);
$re=$o_db->GetData($stmt);
$allnum=$re['count(no)'];

$fp=@fopen($countlog,'w');
if(!$fp){
	$o_main->Error("countlogに書き込みできません。");
}
flock($fp,LOCK_EX);
fputs($fp,$allnum);
fclose($fp);

return $allnum;
}


}
?>