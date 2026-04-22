<?php

Class Kiji{

function Top($fo){
global $kijidb;
global $adpagemax;
global $o_db;
global $o_main;
global $ascript;
global $countlog;

if($fo['editpass']){
	$fo['pass']=$fo['editpass'];
}
if($fo['newpass']){
	$fo['pass']=$fo['newpass'];
}

$start=$fo['page']+1;
$end=$fo['page']+$adpagemax;


$offset=$start-1;
$pdo=$o_db->DBOpen($kijidb);
$stmt=$o_db->SetQuery("SELECT * FROM kiji LIMIT {$adpagemax} OFFSET {$offset}",$pdo);
$all=$o_db->GetAllData($stmt);

$fp=@fopen($countlog,'r');
if(!$fp){
	self::Error("countlogが開けません。");
}
$allnum=fgets($fp);
fclose($fp);


$x=1;
$page_next=0;
$pagetmp=array();
$pnum=$allnum;
if($adpagemax > 0 && $pnum > 0){
	while($pnum > 0){
		if($fo['page'] == $page_next){
			$pagetmp[]="[{$x}]";
		}
		else{
			$pagetmp[]="[<a href=\"javascript:void(0)\" onclick=\"Top('{$page_next}');\" style=\"text-decoration:underline\">{$x}</a>]";
		}
		$x++;
		$page_next=$page_next+$adpagemax;
		$pnum=$pnum-$adpagemax;
	}
}

$pageline=join(" | ",$pagetmp);


$o_main->Head("");

print <<<EOM
<body>
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ 記事の編集と削除</div>
<div class="midasi">記事の編集と削除</div>
<p class="p_box mt10 mb20">
記事を削除するときは、チェックボックスに印をつけて
削除ボタンを押してください。(複数選択可。親記事を
削除すると、レスも削除されます。) 記事の編集をするときは、
編集ボタンを押してください。入力フォームが
出ますので、それで編集して送信ボタンを押してください。
</p>
<form name="fo_edit" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="kiji_edit">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="t_no" value="">
</form>
<div class="page box_center">全{$allnum}件中 {$start}-{$end}件を表示 ページ:{$pageline}</div>

<form name="delfo" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="kiji_del">
<input type="hidden" name="pass" value="{$fo['pass']}">
<table class="site_tbl box_center" border="0">
<thead>
<tr>
<td align="center" width="50">番号</td><td align="center" width="100">投稿者</td><td align="center" width="120">日付</td><td align="center" width="150">タイトル</td><td align="center" width="250">コメント</td><td width="100" align="center">ホスト</td><td align="center" width="50"><input type="button" value="削除" onclick="KijiDel()"></td><td width="50">&nbsp;</td>
</tr>
</thead>
<tbody>
EOM;

if(!is_array($all)){
	$all=array();
}

$i=0;
foreach($all as $kiji){
	$i++;
	if($kiji['resno'] == ""){
		$cl=" bgcolor=\"#e0e0e0\"";
		$i++;
	}
	else{
		$cl=" bgcolor=\"#eeeeee\"";
	}

print <<<EOM
<tr$cl>
<td align="center" data-label="No">{$kiji['no']}</td>
<td data-label="投稿者">{$kiji['name']}</td>
<td data-label="日付">{$kiji['date']}</td>
<td data-label="タイトル">{$kiji['title']}</td>
<td data-label="コメント">{$kiji['comment']}</td>
<td data-label="ホスト">{$kiji['host']}</td>
<td align="center">
<div class="btn1"><input type="checkbox" name="dnum{$i}" value="{$kiji['no']}"></div>
<div class="btn2"><input type="button" value="削除" onclick="DelCheck('{$kiji['no']}');"> <input type="button" value="編集" onclick="Edit('{$kiji['no']}');"></div>
</td>
<td align="center">
<input type="button" value="編集" onclick="Edit('{$kiji['no']}')">
</td>
</tr>
EOM;

}

print <<<EOM
</tbody>
</table>
</form>

<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="menu">
</form>
<form name="fo_top" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="kiji_top">
<input type="hidden" name="page" value="">
</form>
<form name="fo_del" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="kiji_del">
<input type="hidden" name="dnum" value="">
</form>
<script>

function Top(p){
document.fo_top.page.value=p;
document.fo_top.submit();
}

function KijiDel(){
	if(window.confirm("本当に削除しますか？")){ 
		document.delfo.submit();
	} 
	else{
		return false;
	}
}

function DelCheck(n){
	if(window.confirm("本当に削除しますか？")){ 
		Del(n);
	} 
	else{
		return false;
	}
}

function Del(n){
document.fo_del.dnum.value=n;
document.fo_del.submit();
}

function Edit(n){
document.fo_edit.t_no.value=n;
document.fo_edit.submit();
}


</script>
</body>
</html>
EOM;

exit;
}


function Del($fo){
global $kijidb;
global $o_main;
global $o_func;
global $o_db;
global $upimgdir;

$delnum=array();
foreach($fo as $n=>$v){
	if(preg_match("/^dnum/",$n)){
		$delnum[]=$v;
	}
}

if(!count($delnum)){
	$o_main->Error("チェックを入れてください。");
}

$pdo=$o_db->DBOpen($kijidb);

$o_db->BeginTransaction($pdo);

$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$stmt2=$o_db->SetPH("DELETE FROM kiji WHERE no=?",$pdo);

$delimgs=array();
foreach($delnum as $v){
	$o_db->DoExc($stmt,[$v]);
	$kiji=$o_db->GetData($stmt);
	if($kiji['img']){
		list($img)=explode(":",$kiji['img']);
		$delimgs[]=$img;
	}
	
	$o_db->DoExc($stmt2,[$v]);

	if(!$kiji['resno']){
		$stmt3=$o_db->SetPH("SELECT * FROM kiji WHERE resno=?",$pdo);
		$o_db->DoExc($stmt3,[$v]);
		$all=$o_db->GetAllData($stmt3);
		foreach($all as $kiji){
			if($kiji['img']){
				$delimgs[]=$kiji['img'];
			}
		}
		$stmt4=$o_db->SetPH("DELETE FROM kiji WHERE resno=?",$pdo);
		$o_db->DoExc($stmt4,[$v]);
	}
}

$o_db->Commit($pdo);

foreach($delimgs as $img){
	$file=$upimgdir.$img;
	if(file_exists($file)){
		if(!unlink($file)){
			$o_main->Error("{$file}が削除でません。");
		}
	}

}

$o_func->UpdateCount($o_db,$pdo);

self::Top($fo,"",null);

}




function Edit($fo){
global $kijidb;
global $skin;
global $upimgdir;
global $o_main;
global $o_func;
global $o_db;
global $inlink;
global $icons;
global $colors;
global $ascript;


$pdo=$o_db->DBOpen($kijidb);

$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);
$kiji=$o_db->GetData($stmt);

if(!$kiji){
	$o_main->Error("該当記事がありませんでした。");
}


if($inlink){
	$kiji['comment']=preg_replace("/<a href.*?\>/","",$kiji['comment']);
	$kiji['comment']=preg_replace("/<\/a>/","",$kiji['comment']);
}

$fp=@fopen($skin,'r');
if(!$fp){
	$o_main->Error("skinが開けません。");
}
while($ln=fgets($fp)){
	$skinline.=$ln;
}
fclose($fp);



list($head,$dummy)=explode("<!--msg-->",$skinline);

if($dummy == ""){
	$o_main->Error("skinに&lt;!--msg--&gt;が書かれていません。");
}

list($dummy,$form)=explode("<!--form-->",$head);

if($form == ""){
	$o_main->Error("skinに&lt;!--form--&gt;が書かれていません。");
}


$form=str_replace("#name",$kiji['name'],$form);
$form=str_replace("#url",$kiji['url'],$form);
$form=str_replace("#email",$kiji['email'],$form);
$form=str_replace("name=\"pass\"","name=\"upass\"",$form);
$form=str_replace("#pass","",$form);
$form=str_replace("#title",$kiji['title'],$form);

$kiji['comment']=str_replace("<br>","\n",$kiji['comment']);


$kiji['comment']=str_replace("&lt;","<",$kiji['comment']);
$kiji['comment']=str_replace("&gt;",">",$kiji['comment']);
$kiji['comment']=str_replace("'","&#039;",$kiji['comment']);
$kiji['comment']=str_replace('"',"&quot;",$kiji['comment']);
$kiji['comment']=str_replace("&amp;","&",$kiji['comment']);


$form=str_replace("#comment",$kiji['comment'],$form);
$iconsline="<select name=\"icon\">";
foreach($icons as $v){
	if($v == $kiji['icon']){
		$ck=" checked";
	}
	else{
		$ck="";
	}
	$iconsline.="<option value=\"{$v}\"{$ck}>{$v}</option>";
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

$form=str_replace("#q",$kiji['q'],$form);

for($i=1;$i<=5;$i++){
	$form=str_replace("#a".$i,$kiji['a'.$i],$form);
}

if($kiji['img']){
	list($img,$w,$h)=explode(":",$kiji['img']);
	if($w > 50) {
		$w_o=$w;
		$w=50;
		$h=floor($w*$h/$w_o);
	}
	$t="<br>現在の画像<br><img src=\"{$upimgdir}{$img}\" width=\"{$w}\" height=\"{$h}\"><input type=\"checkbox\" name=\"imgdel\" value=\"1\">削除<br>\n";
	$form=str_replace("<!--img-->",$t,$form);
}


$input="\n<input type=\"hidden\" name=\"t_no\" value=\"{$fo['t_no']}\">\n";
$input.="<input type=\"hidden\" name=\"pass\" value=\"{$fo['pass']}\">\n";
$form=str_replace("<!--input-->",$input,$form);
$form=str_replace("regist","kiji_editregist",$form);
$form=preg_replace("/(action=\".*?\")/","action=\"{$ascript}\"",$form);
$form=str_replace("<input type=\"checkbox\" name=\"cookie\" value=\"on\" checked>","",$form);


$o_main->Head("");

print <<<EOM
<body>
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ <a href="javascript:void(0)" onclick="document.fo_top.submit();" class="navi">記事の編集と削除</a> ＞ 編集</div>
<div class="midasi">編集</div>
<p class="p_box mt10 mb20">
入力して送信ボタンを押してください。
</p>
EOM;



print $form;

print <<<EOM
<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="menu">
</form>
<form name="fo_top" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="kiji">
</form>
</body>
</html>
EOM;

exit;
}


function EditRegist($fo){
global $kijidb;
global $upimgdir;
global $o_main;
global $o_func;
global $o_db;
global $inlink;
global $FIELDS;

$pdo=$o_db->DBOpen($kijidb);

$stmt=$o_db->SetPH("SELECT * FROM kiji WHERE no=?",$pdo);
$o_db->DoExc($stmt,[$fo['t_no']]);
$kiji=$o_db->GetData($stmt);

$delimg=array();

if($_FILES['upfile']['name']){
	if($kiji['img']){
		$delimg[]=$kiji['img'];
	}
	list($imgfile,$w,$h)=$o_main->Upload();
	$kiji['img']=$imgfile.':'.$w.':'.$h;
}
else{
	if($fo['imgdel']){
		list($img,$w,$h)=explode(":",$kiji['img']);
		$file=$upimgdir.$img;
		if(file_exists($file)){
			if(!unlink($file)){
				$o_main->Error("{$file}が削除できません。");
			}
		}
		$kiji['img']="";
	}

}


if($inlink){
	$fo['comment']=preg_replace("/([^=^\"])(https?:\/\/[\w\+\.\?\-\/_:~&=#%]+)/","\\1<a href=\"\\2\" target=\"_blank\">\\2<\/a>",$fo['comment']);
}

$pass=$fo['pass'];

$fo['pass']=$fo['upass'];
if($fo['pass']){
	$fo['pass']=$o_func->Encode($fo['pass']);
}

$fo['resno']=$kiji['resno'];
$fo['date']=$o_func->GetDate();
$fo['timer']=time();

$fo['host']=$kiji['host'];
$fo['no']=$fo['t_no'];

$qline=$o_db->MakeQLine($FIELDS['kiji']);
$vary=$o_db->MakeVAry($FIELDS['kiji'],$fo);

$stmt=$o_db->SetPH("UPDATE kiji SET {$qline} WHERE no=?",$pdo);
$vary[]=$fo['t_no'];
$o_db->DoExc($stmt,$vary);

$fo['pass']=$pass;

self::Top($fo);

}




}//Class End

?>