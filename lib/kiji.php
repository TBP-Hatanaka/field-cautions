<?php

Class Kiji{

function Top($fo){
global $pagemax;
global $kijidb;
global $o_db;
global $countlog;

list($head,$form,$footer,$table_top,$restable,$table_bottom)=self::Cut("");

$form=str_replace("#comment","",$form);
$form=str_replace("#title","",$form);
$form=str_replace("#fuki","",$form);

if(!$fo['page']){
	$fo['page']=1;
}

$offset=($fo['page'] - 1)*$pagemax;

$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetQuery("SELECT * FROM kiji WHERE resno='0' ORDER BY timer DESC LIMIT {$pagemax} OFFSET {$offset}",$pdo);
$all=$o_db->GetAllData($stmt);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE resno=?",$pdo);
$reses=array();
foreach($all as $kiji){
	$o_db->DoExc($stmt,[$kiji['no']]);
	$re=$o_db->GetAllData($stmt);
	$reses[$kiji['no']]=$re;
}


$fp=@fopen($countlog,'r');
if(!$fp){
	self::Error("countlogが開けません。");
}
$allnum=fgets($fp);
fclose($fp);


$cnt=floor($allnum/$pagemax);
$a=$allnum % $pagemax;
if($a){
	$cnt++;
}

$pagetmp=self::MakePage("",$fo['page'],$cnt,"");

$footer=str_replace("<!--page-->",$pagetmp,$footer);

header("Content-type: text/html");
print $head;
print $form;

self::Msgs($all,$reses,$table_top,$restable,$table_bottom);

print $footer;
exit;



}

function MakePage($act,$page,$all,$keyword){

$q="";
if($act == "search"){
	$q="&mode=search&keyword=".urlencode($keyword);
}

if(!$page){
	$page=1;
}

$pagenum=5;
$pagenum_h=floor($pagenum/2);

$a=$pagenum % 2;
if($a == 0){
	$pagenum_h--;
}

$page_start=$page-$pagenum_h;

if($page_start < 1){
	$page_start=1;
}

$page_end=$page_start+$pagenum-1;

if($page_end > $all){
	$page_end=$all;
	$page_start=$page_end - $pagenum + 1;
	if($page_start < 1){
		$page_start=1;
	}
}

$ptmp=array();
for($i=1;$i<=$all;$i++){
	if($i < $page_start){
		continue;
	}
	else if($i > $page_end){
		break;
	}
	if($i == $page){
		array_push($ptmp,"<li class=\"current\">$i</li>");
	}
	else{
		array_push($ptmp,"<li><a href=\"?page={$i}{$q}\">$i</a></li>");
	}
}
if($page_start > 1){
	array_unshift($ptmp,"<li class=\"ten\">…</li>");
}
if($page_end < $all){
	array_push($ptmp,"<li class=\"ten\">…</li>");
}

$pagetmp=join('',$ptmp);


if($page > 1){
	$p=$page-1;
	$prev="<li><a href=\"?page=$p{$q}\">&lt;</a></li>";
	$first="<li><a href=\"?page=1{$q}\">≪</a></li>";
}
if($page < $all){
	$p=$page+1;

	$next="<li><a href=\"?page=$p{$q}\">&gt;</a></li>";
	$last="<li><a href=\"?page=$all{$q}\">≫</a></li>";

}

$pagetmp="<ul class=\"pagelink\">".$first.$prev.$pagetmp.$next.$last."</ul>";

return $pagetmp;

}


function Cut($act){
global $cookie_name;
global $skin;
global $o_func;
global $o_main;
global $icons;
global $colors;
global $icon_names;

$DUMMY=$o_func->GetCookie($cookie_name);

$COOKIE=array();

if($DUMMY){
	$c=explode(",",$DUMMY);
	foreach($c as $v){
		list($name,$value)=explode(":",$v);
		$COOKIE[$name]=$value;
	}
}


$fp=@fopen($skin,'r');
if(!$fp){
	 $o_main->Error("{$skin}が開けません。");
}
while($ln=fgets($fp)){
	$skinline.=$ln;
}
fclose($fp);

list($head,$dummy) = explode("<!--msg-->", $skinline);
if($dummy == ""){
	$o_main->Error("スキンに&lt;!--msg--&gt;が書かれていません。");
}
list($dummy,$footer) = explode("<!--/msg-->", $dummy);
if($footer == ""){
	$o_main->Error("スキンに&lt;!--/msg--&gt;が書かれていません。");
}

list($table_top,$dummy) = explode("<!--res-->", $dummy);

if($dummy == ""){
	$o_main->Error("スキンに&lt;!--res--&gt;が書かれていません。");
}

list($restable,$table_bottom) = explode("<!--/res-->", $dummy);
if($table_bottom == ""){
	$o_main->Error("スキンに&lt;!--/res--&gt;が書かれていません。");
}

list($head,$form) = explode("<!--form-->", $head);
if($form == ""){
	$o_main->Error("$fileに&lt;!--form--&gt;が書かれていません。");
}

$form=str_replace("#pass",$COOKIE['pass'],$form);

if($act != "edit"){
	$form=str_replace("#name",$COOKIE['name'],$form);
	$form=str_replace("#url",$COOKIE['url'],$form);
	$form=str_replace("#email",$COOKIE['email'],$form);

	$iconsline="<select name=\"icon\">\n<option value=\"\">選択</option>";
	$i=0;
	foreach($icons as $v){
		if($v == $COOKIE['icon']){
			$ck=" checked";
		}
		else{
			$ck="";
		}
		$iconsline.="<option value=\"{$v}\"{$ck}>{$icon_names[$i]}</option>";
		$i++;
	}
	$iconsline.="</select>";
	$form=str_replace("<!--icons-->",$iconsline,$form);
	$colorsline="";
	$flag=false;
	foreach($colors as $v){
		if(!$flag){
			$ck=" checked";
		}
		else{
			$ck="";
		}
		$colorsline.="<input type=\"radio\" name=\"color\" value=\"{$v}\"{$ck}><span style=\"color:{$v}\">■</span> ";
		$flag=true;
	}
	$form=str_replace("<!--colors-->",$colorsline,$form);
	
	
	$form=str_replace("#q","",$form);

	for($i=1;$i<=5;$i++){
		$form=str_replace("#a".$i,"",$form);
	}
}




return array($head,$form,$footer,$table_top,$restable,$table_bottom);

}

function Msgs(&$all,&$reses,&$table_top,&$restable,&$table_bottom){

foreach($all as $kiji){
	self::PrintMsgs("",$kiji,$table_top);
	if(isset($reses[$kiji['no']])){
		foreach($reses[$kiji['no']] as $kiji_r){
			self::PrintMsgs("res",$kiji_r,$restable);
		}
	}
	print $table_bottom;
}

}

function PrintMsgs($act,&$kiji,&$table){
global $resbutton;
global $pagemax;
global $upimgdir;
global $upimgdir_http;
global $w_max;
global $mailicon;
global $homeicon;
global $imgdir;
global $restag;

if($upimgdir_http){
	$upimgdir=$upimgdir_http;
}

if($kiji['icon']){
	$kiji['icon']="<img src=\"{$imgdir}{$kiji['icon']}\" border=\"0\" alt=\"\">";
}

if($kiji['img']){
	$href="";
	$href2="";
	list($img,$w,$h)=explode(":",$kiji['img']);
	if($w && $h && $w > 0 && $h > 0){
		$real_w=$w+40;
		$real_h=$h+40;
		if($w > $w_max) {
			$w_o=$w;
			$w=$w_max;
			$h=floor($w*$h/$w_o);
			$href="<a href=\"{$upimgdir}{$img}\">";
			$href2="</a>";
		}
	}
	$kiji['img']="{$href}<img src=\"{$upimgdir}{$img}\" border=\"0\" alt=\"{$kiji['title']}\" width=\"{$w}\" height=\"{$h}\" class=\"img\">${href2}";
}


if($kiji['email']){
	$kiji['email']="<a href=\"mailto:{$kiji['email']}\">{$mailicon}</a>";
}
if($kiji['url']){
	$kiji['url']="<a href=\"{$kiji['url']}\" target=\"_blank\">{$homeicon}</a>";
}
$table_d=$table;

$table_d=str_replace("<!--icon-->",$kiji['icon'],$table_d);
$table_d=str_replace("<!--title-->",$kiji['title'],$table_d);
$table_d=str_replace("<!--up-->",$kiji['img'],$table_d);

if($kiji['color']){
	$kiji['comment']="<div style=\"color:{$kiji['color']}\">".$kiji['comment']."</div>";
}

$table_d=str_replace("<!--comment-->",$kiji['comment'],$table_d);
$table_d=str_replace("<!--name-->",$kiji['name'],$table_d);
$table_d=str_replace("<!--no-->",$kiji['no'],$table_d);
$table_d=str_replace("<!--date-->",$kiji['date'],$table_d);
$table_d=str_replace("<!--url-->",$kiji['url'],$table_d);
$table_d=str_replace("<!--email-->",$kiji['email'],$table_d);

$fuki="";
if($kiji['fuki']){
	$fuki="<div class=\"fuki\"><div class=\"fuki_box\">{$kiji['fuki']}</div></div>";
}
$table_d=str_replace("<!--fuki-->",$fuki,$table_d);

if($act == ""){
	$table_d=str_replace("#no",$kiji['no'],$table_d);
	if($kiji['q']){
		$enqline="<div class=\"enquet_title\">{$kiji['q']}</div>";
		$enqline.="<div class=\"enquet_box\">";
		$total=0;
		$top=0;
		for($i_q=1;$i_q<=4;$i_q++){
			if($kiji['a'.$i_q]){
				$c=intval($kiji['c'.$i_q]);
				$total+=$c;
				if($top < $c){
					$top=$c;
				}
			}
		}

		for($i_q=1;$i_q<=4;$i_q++){
			$enqline.="<div>";
			if($kiji['a'.$i_q]){
				$c=intval($kiji['c'.$i_q]);
				if($total){
					$per=floor(($c / $total) * 100);
				}
				else{
					$per=0;
				}
				if($c && $c == $top){
					$cl=" bar_top";
				}
				else{
					$cl=" bar_lower";
				}
				$enqline.="<div class=\"enquet_bar_box\"><div id=\"q_{$kiji['no']}_{$i_q}\" class=\"enquet_bar{$cl}\" style=\"width:{$per}%\"></div><div class=\"enquet_a\" data-no=\"{$kiji['no']}\" data-a=\"{$i_q}\">{$kiji['a'.$i_q]}</div></div><div class=\"enquet_cnt\" id=\"p_{$kiji['no']}_{$i_q}\">{$per}％</div>";
			}
			$enqline.="</div>";
		}
		$enqline.="</div>";
		$enqline.="<div>投票数:<span id=\"t_{$kiji['no']}\">{$total}</span></div>";
		$table_d=str_replace("<!--enquet-->",$enqline,$table_d);
	}

	$lileline="";
	if($kiji['like'] || $kiji['bad']){
		$total=intval($kiji['like']) + intval($kiji['bad']);
		$wari_like=floor((intval($kiji['like']) / $total) * 100);
		$wari_bad=100 - $wari_like;
		$likeline.="<div class=\"like\">{$kiji['like']}</div>";
		$lileline.="<div class=\"bad\">{$kiji['like']}</div>";
	}
	$table_d=str_replace("<!--like-->",$likeline,$table_d);
}

print $table_d;
}

function Res($fo){
global $upimgdir;
global $upimgdir_http;
global $kijidb;
global $o_db;



$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['no']]);
$kiji=$o_db->GetData($stmt);

$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE resno=?",$pdo);
$reses=array();

$o_db->DoExc($stmt,[$fo['no']]);
$re=$o_db->GetAllData($stmt);
$reses[$kiji['no']]=$re;

list($head,$form,$footer,$table_top,$restable,$table_bottom)=self::Cut("");

$form=str_replace("#comment","",$form);

if($kiji['title']){
	$title="Re: ".$kiji['title'];
}
else{
	$title="";
}

$form=str_replace("#title",$title,$form);
$form=str_replace("#fuki","",$form);

$temp="<input type=\"hidden\" name=\"resno\" value=\"{$fo['no']}\">";
$form=str_replace("<!--input-->",$temp,$form);

$form=preg_replace("/<!--enq-->.*?<!--\/enq-->/s","",$form);

$all=array($kiji);


header("Content-type: text/html");
print $head;


self::Msgs($all,$reses,$table_top,$restable,$table_bottom);

print $form;
print $footer;

exit;
}

function Check($fo){
global $o_main;
global $avoid;
global $hostdeny;
global $worddeny;
global $asciideny;
global $httpdeny;
global $kinsiword;
global $kinsihost;
global $httpdenynum;


if($fo['name'] == ""){
	$o_main->Error("名前が記入されていません。");
}
if($fo['comment'] == ""){
	$o_main->Error("本文が記入されていません。");
}
if(preg_match("/\W/",$fo['pass'])){
	$o_main->Error("バスワードは英数字を入力してください。");
}


if($asciideny){
	if(!preg_match("/[ぁ-ん]+/u",$fo['comment'])){
		$o_main->Error("投稿できません。");
	}
}
if($httpdeny){
	preg_match_all("/http/",$fo['comment'],$ma);
	if(count($ma[0]) > intval($httpdenynum)){
		$o_main->Error("投稿できません。");
	}
}


if($worddeny){
	$s=$fo['name'].$fo['title'].$fo['comment'];
	$r=0;
	$flag=false;
	foreach($kinsiword as $v){
		if(mb_strpos($s,$v) !== false){
			$flag=true;
			break;
		}
	}
	if($flag){
		$o_main->Error("投稿できません。");
	}
}

if($hostdeny){
	$r=0;
	$flag=false;
	foreach($kinsihost as $v){
		if(strpos($fo['host'],$v) !== false){
			$flag=true;
			break;
		}
	}
	if($flag){
		$o_main->Error("投稿できません。");
	}
}

}


function Regist($fo){
global $adicon;
global $titlealt;
global $mail;
global $mailto;
global $kijidb;
global $o_main;
global $FIELDS;
global $o_db;
global $o_func;
global $cookie_name;
global $cookie_hozon;
global $max;
global $kijinum;
global $inlink;

$fo['host']=$o_func->GetHost();

self::Check($fo);

if($adicon){
	self::MasterCheck($fo['pass'],$fo['icon']);
}
$pass_o="";
if($fo['pass']){
	$pass_o=$fo['pass'];
	$fo['pass']=$o_func->Encode($fo['pass']);
}

if($fo['title'] == "" && $titlealt){
	$fo['title']=$titlealt;
}

if($_FILES['upfile']['name'] && $_FILES['upfile']['size']){
	list($img,$w,$h)=$o_func->Upload();
	$fo['img']="{$img}:{$w}:{$h}";
}

$timer=time();
$fo['timer']=$timer;

if($inlink){
	self::autolink($fo['comment']);
}

$fo['date']=$o_func->GetDate();

if(!$fo['resno']){
	$fo['resno']="0";
}

$pdo=$o_db->DBOpen($kijidb);
list($nline,$qline)=$o_db->MakeNQLine($FIELDS['kiji']);
$stmt=$o_db->SetPH("INSERT INTO kiji ({$nline}) VALUES ({$qline})",$pdo);
$v_ary=$o_db->MakeVAry($FIELDS['kiji'],$fo);
$o_db->DoExc($stmt,$v_ary);

if($fo['resno']){
	$v_ary=array($timer);
	$v_ary[]=$fo['resno'];
	$stmt=$o_db->SetPH("UPDATE kiji SET timer=? WHERE no=?",$pdo);
	$o_db->DoExc($stmt,$v_ary);
}

$allnum=$o_func->UpdateCount($o_db,$pdo);

if($max){
	if($allnum > intval($kijinum)){
		$stmt=$o_db->SetQuery("SELECT * FROM kiji ORDER BY no DESC LIMIT 1",$pdo);
		$kiji=$o_db->GetData($stmt);
		$stmt=$o_db->SetPH("DELETE FROM kiji WHERE no=?",$pdo);
		$v_ary=array($kiji['no']);
		$o_db->DoExc($stmt,$v_ary);
	}
}

if($mail){
	$mailbody=self::MakeMail($fo);
	$o_func->S_Mail($mailto,$mailto,"投稿者","書き込みがありました。",$mailbody);
}

if($fo['cookie']){
	$cook="name:{$fo['name']},email:{$fo['email']},url:{$fo['url']},icon:{$fo['icon']},color:{$fo['color']},pass:{$pass_o}";
	$o_func->SetCookie($cook,$cookie_name,"",$cookie_hozon);
}
else{
	$DUMMY=$o_func->GetCookie($cookie_name);
	if($DUMMY){
		$o_func->DelCookie($cookie_name);
	}
}


self::Top($fo);
}

function MakeMail($ary){
$mailbody="";
$mailbody.="□投稿者: {$ary['name']}\n";
$mailbody.="□タイトル: {$ary['title']}\n";
$mailbody.="□E-mail: {$ary['email']}\n";
$mailbody.="□URL: {$ary['url']}\n";
$mailbody.="□日付: {$ary['date']}\n";
$mailbody.="□ホスト: {$ary['host']}\n";
$mailbody.="□本文: {$ary['comment']}\n";
return $mailbody;
}

function MasterCheck($pass,$icon){
global $adpass;
global $adicons;
global $o_main;
$master=0;
if($pass){
	if($pass == $adpass){
		$master=1;
	}
}
foreach($adicons as $v){
	if(preg_match("/$v/",$icon) && !$master){
		$o_main->Error("このアイコンは管理人専用です。");
	}
}
}


function Del($fo){
global $o_db;
global $kijidb;
global $o_main;
global $mail;
global $upimgdir;
global $o_func;
global $mailto;


$delnum=array();

if($fo['t_no'] == ""){
	$o_main->Error("記事番号を入力してください。");
}
else if($fo['t_pass'] == ""){
	$o_main->Error("削除キーを入力してください。");
}
else if(preg_match("/\W/",$fo['t_pass'])){
	$o_main->Error("削除キーは英数字を入力してください。");
}


$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);
$kiji=$o_db->GetData($stmt);

$delimg=array();
if($kiji['img']){
	$delimg[]=$kiji['img'];
}

if(!$kiji){
	$o_main->Error("該当記事がありません。");
}
if($kiji['pass']){
	$passflag=$o_func->MatchPass($fo['t_pass'],$kiji['pass']);
	if(!$passflag){
		$o_main->Error("削除キーが違います。");
	}
}
else{
	$o_main->Error("記事に削除キーが設定されていません。");
}


$o_db->BeginTransaction($pdo);
$stmt=$o_db->SetPH("DELETE FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);

if(!$kiji['resno']){
	$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE resno=?",$pdo);
	$o_db->DoExc($stmt,[$fo['t_no']]);
	$all=$o_db->GetAllData($stmt);
	foreach($all as $kiji){
		if($kiji['img']){
			$delimg[]=$kiji['img'];
		}
	}
	$stmt=$o_db->SetPH("DELETE FROM kiji WHERE resno=?",$pdo);
	$o_db->DoExc($stmt,[$fo['t_no']]);
}


$o_db->Commit($pdo);


foreach($delimg as $v){
	list($img)=explode(":",$v);
	$file=$upimgdir.$v;
	if(file_exists($file)){
		if(!unlink($file)){
			$o_main->Error("{$file}が削除できません。");
		}
	}
}

if($mail==2){
	$mailbody=self::MakeMail($kiji);
	$o_func->S_Mail($mailto,$mailto,"投稿者","削除がありました。",$mailbody);
}

$o_func->UpdateCount($o_db,$pdo);

self::Top($fo);

}


function Edit($fo){
global $inlink;
global $skin;
global $o_main;
global $o_func;
global $o_db;
global $kijidb;
global $icons;
global $colors;
global $upimgdir;
global $icon_names;

if($fo['t_no'] == ""){
	$o_main->Error("記事番号を入力してください。");
}
else if($fo['t_pass'] == ""){
	$o_main->Error("パスワードを入力してください。");
}
else if(preg_match("/\W/",$fo['t_pass'])){
	$o_main->Error("パスワードは英数字を入力してください。");
}


$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);
$kiji=$o_db->GetData($stmt);


if(!$kiji){
	$o_main->Error("該当記事がありません。");
}
if($kiji['pass']){
	$passflag=$o_func->MatchPass($fo['t_pass'],$kiji['pass']);
	if(!$passflag){
		$o_main->Error("削除キーが違います。");
	}
}
else{
	$o_main->Error("記事に削除キーが設定されていません。");
}

if($inlink){
	$kiji['comment']=preg_replace("/<a href.*?\>/","",$kiji['comment']);
	$kiji['comment']=preg_replace("/<\/a>/","",$kiji['comment']);
}

$kiji['comment']=str_replace("<br>","\n",$kiji['comment']);
$kiji['comment']=str_replace("&lt;","<",$kiji['comment']);
$kiji['comment']=str_replace("&gt;",">",$kiji['comment']);
$kiji['comment']=str_replace("'","&#039;",$kiji['comment']);
$kiji['comment']=str_replace('"',"&quot;",$kiji['comment']);
$kiji['comment']=str_replace("&amp;","&",$kiji['comment']);

list($head,$form,$footer,$table_top,$restable,$table_bottom)=self::Cut("edit");

$form=str_replace("#name",$kiji['name'],$form);
$form=str_replace("#email",$kiji['email'],$form);
$form=str_replace("#url",$kiji['url'],$form);
$form=str_replace("#comment",$kiji['comment'],$form);
$form=str_replace("#title",$kiji['title'],$form);
$form=str_replace("#fuki",$kiji['fuki'],$form);
$iconsline="<select name=\"icon\"><option value=\"\">選択</option>";
$i=0;
foreach($icons as $v){
	if($v == $kiji['icon']){
		$ck=" selected";
	}
	else{
		$ck="";
	}
	$iconsline.="<option value=\"{$v}\"{$ck}>{$icon_names[$i]}</option>";
	$flag=true;
	$i++;
}
$iconsline.="</select>";
$form=str_replace("<!--icons-->",$iconsline,$form);

foreach($colors as $v){
	if($v == $kiji['color']){
		$ck=" checked";
	}
	else{
		$ck="";
	}
	$colorsline.="<input type=\"radio\" name=\"color\" value=\"{$v}\"{$ck}><span style=\"color:{$v}\">■</span> ";
}
$form=str_replace("<!--colors-->",$colorsline,$form);

$form=str_replace("#q",$kiji['q'],$form);

for($i=1;$i<=5;$i++){
	$form=str_replace("#a".$i,$kiji['a'.$i],$form);
}


if($kiji['img']){
	list($img,$w,$h)=explode(":",$kiji['img']);
	$img="<img src=\"{$upimgdir}{$img}\" width=\"120\"><br>";
	$form=str_replace("<!--img-->",$img,$form);
}

$input="\n<input type=\"hidden\" name=\"t_no\" value=\"{$fo['t_no']}\">\n";
$input.="<input type=\"hidden\" name=\"t_pass\" value=\"{$fo['t_pass']}\">\n";

$form=str_replace("<!--input-->",$input,$form);
$form=str_replace("regist","editregist",$form);



header("Content-type: text/html");
print $head;
print $form;
print "</body>\n";
print "</html>\n";
exit;
}

function autolink(&$text){
	$text=preg_replace("/([^=^\"])((https|http)?:\/\/[\w\+\.\?\-\/_:~&=#%]+)/","\\1<a href=\"\\2\" target=\"_blank\">\\2</a>",$text);
}


function EditRegist($fo){
global $inlink;
global $upimgdir;
global $o_main;
global $kijidb;
global $o_db;
global $mail;
global $mailto;
global $o_func;
global $FIELDS;

$fo['host']=$o_func->GetHost();

self::Check($fo);

if($inlink){
	self::autolink($fo['comment']);
}

$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);
$kiji=$o_db->GetData($stmt);

if(!$kiji){
	$o_main->Error("該当記事がありません。");
}
if($kiji['pass']){
	$passflag=$o_func->MatchPass($fo['t_pass'],$kiji['pass']);
	if(!$passflag){
		$o_main->Error("削除キーが違います。");
	}
}
else{
	$o_main->Error("記事に削除キーが設定されていません。");
}
if($fo['pass']){
	$fo['pass']=$o_func->Encode($fo['pass']);
}

if($_FILES['upfile']['name'] && $_FILES['upfile']['size']){
	list($img,$w,$h)=$o_func->Upload();
	$fo['img']="{$img}:{$w}:{$h}";
	if($kiji['img']){
		list($img,$w,$h)=explode(":",$kiji['img']);
		$imgfile=$upimgdir.$img;
		if(file_exists($imgfile)){
			if(!unlink($imgfile)){
				$o_main->Error("fileが削除できません。");
			}
		}
	}
}
$fo['resno']=$kiji['resno'];
$fo['date']=$o_func->GetDate();
$fo['timer']=time();

$fo['no']=$fo['t_no'];

$qline=$o_db->MakeQLine($FIELDS['kiji']);
$vary=$o_db->MakeVAry($FIELDS['kiji'],$fo);
$stmt=$o_db->SetPH("UPDATE kiji SET {$qline} WHERE no=?",$pdo);

$vary[]=$fo['t_no'];
$o_db->DoExc($stmt,$vary);

if($mail==2){
	$mailbody=self::MakeMail($kiji);
	$o_func->S_Mail($mailto,$mailto,"投稿者","変更がありました。",$mailbody);
}

self::Top($fo);
}

function Enquet($fo){
global $o_main;
global $kijidb;
global $o_db;

$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['no']]);
$kiji=$o_db->GetData($stmt);

if(!$kiji){
	$o_main->JsError("入力された番号の記事はありません。");
}

$key='c'.$fo['a'];

$kiji[$key]++;

$qline="c{$fo['a']}=?";
$vary=array($kiji[$key]);
$stmt=$o_db->SetPH("UPDATE kiji SET {$qline} WHERE no=?",$pdo);
$vary[]=$fo['no'];
$o_db->DoExc($stmt,$vary);


$total=0;
for($i_q=1;$i_q<=4;$i_q++){
	$total+=intval($kiji['c'.$i_q]);
}

$js="";
for($i_q=1;$i_q<=4;$i_q++){
	if($kiji['a'.$i_q]){
		$c=$kiji['c'.$i_q];
		if($total){
			$per=floor((intval($c) / $total) * 100);
		}
		else{
			$per=0;
		}
		$per=floor((intval($c) / $total) * 100);
		$js.='{"a":"'.$i_q.'","c":"'.$c.'","p":"'.$per.'"},';
	}
}


$js=rtrim($js,",");

$jsline='{"result":"ok",';
$jsline.='"ary":['.$js.'],';
$jsline.='"no":"'.$fo['no'].'",';
$jsline.='"total":"'.$total.'"';
$jsline.='}';


header("Content-type: text/json");
print $jsline;
exit;
}

function Search($fo){
global $kijidb;
global $o_main;
global $o_db;
global $pagemax;

if($fo['keyword'] == ""){
	$o_main->Error("検索語を入力してください。");
}

list($head,$form,$footer,$table_top,$restable,$table_bottom)=self::Cut("");

$form=str_replace("#comment","",$form);
$form=str_replace("#title","",$form);
$form=str_replace("#fuki","",$form);

$ary=array();

$fo['keyword']=str_replace("　"," ",$fo['keyword']);
$tmp=explode(" ",$fo['keyword']);

$keys=array();
foreach($tmp as $v){
	if($v){
		$keys[]=$v;
	}
}


if(count($keys) > 1){
	$c_q="(name LIKE ? OR title LIKE ? OR comment LIKE ?) AND (name LIKE ? OR title LIKE ? OR comment LIKE ?)";
	$key1='%'.$keys[0].'%';
	$key2='%'.$keys[1].'%';
	$ary[]=$key1;
	$ary[]=$key1;
	$ary[]=$key1;
	$ary[]=$key2;
	$ary[]=$key2;
	$ary[]=$key2;
}
else{
	$c_q="name LIKE ? OR title LIKE ? OR comment LIKE ?";
	$key='%'.$keys[0].'%';
	$ary[]=$key;
	$ary[]=$key;
	$ary[]=$key;
}

$start=$fo['page']+1;
$end=$fo['page']+$pagemax;
$offset=$start-1;


$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetPH("SELECT count(no) FROM kiji WHERE {$c_q} AND resno='0'",$pdo);
$o_db->DoExc($stmt,$ary);
$re=$o_db->GetData($stmt);
$allnum=$re['count(no)'];


$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE {$c_q} AND resno='0' LIMIT {$pagemax} OFFSET {$offset}",$pdo);
$o_db->DoExc($stmt,$ary);
$all=$o_db->GetAllData($stmt);
$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE resno=?",$pdo);
$reses=array();
foreach($all as $kiji){
	$o_db->DoExc($stmt,[$kiji['no']]);
	$re=$o_db->GetAllData($stmt);
	$reses[$kiji['no']]=$re;
}

$cnt=floor($allnum/$pagemax);
$a=$allnum % $pagemax;
if($a){
	$cnt++;
}

$pagetmp=self::MakePage("search",$fo['page'],$cnt,$fo['keyword']);

$footer=str_replace("<!--page-->",$pagetmp,$footer);

header("Content-type: text/html");
print $head;
print $form;
print "<div class=\"search_result center\">検索結果: {$result}件</div>";

self::Msgs($all,$reses,$table_top,$restable,$table_bottom);

print $footer;
exit;


}


function Howto(){
global $inlink;
global $kijinum;
global $script;


if($inlink){
	$li2='<li>・記事中のURLは自動でリンクされます</li>';
}
else{
	$li2="";
}

list($head,$form,$footer,$table_top,$restable,$table_bottom)=self::Cut("");

$head=str_replace("<!--bbs_a1-->","<a href=\"{$script}\">",$head);
$head=str_replace("<!--bbs_a2-->","</a>",$head);
$head=str_replace("<!--howto_a-->","&gt; 使い方",$head);

header("Content-type: text/html");
print $head;

print <<<EOM
<ul id="howto_list">
<li>・タグは一切使えません</li>
<li>・不適当とみなした投稿は削除することがあります。</li>
<li>・適当に改行して投稿してください。</li>
<li>・投稿時に削除キー(英数8字以内)を入力しておくと、自分の投稿の削除と編集ができます。</li>
{$li2}
<li>・アンケートを実施することができます。アンケート名にはアンケートの質問(例 この写真はどうかな?)を、選択肢にはその回答(例 すごくいい、よい、普通)を入力してください。<br>
アンケートに入力するとグラフが表示され、グラフ上の選択肢をクリックするとカウントとグラフがアップします。</li>
<li>・吹き出しに文字を入力するとアイコンに吹き出しをつけることができます。</li>
<li>・検索フォームに入力すると記事が検索できますが検索できるのは親記事のみです。</li>
</ul>
</body>
</html>
EOM;

exit;
}

}//Class End

?>