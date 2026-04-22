<?php

Class Set{

function Top($fo,$act,$act2){
global $kinsiword;
global $kinsihost;
global $max;
global $pagemax;
global $titlealt;
global $inlink;
global $adicon;
global $adicons;
global $asciideny;
global $httpdeny;
global $httpdenynum;
global $worddeny;
global $kword;
global $hostdeny;
global $khost;
global $mail;
global $sendmail;
global $mailto;
global $w_max;
global $commonpl;
global $o_main;
global $colors;
global $icons;
global $icon_names;
global $kijinum;


if($act == 'regist'){
	include($commonpl);
}

if($adicon){
	$CK['adicon2']=" checked";
}
else{
	$CK['adicon1']=" checked";
}

if($asciideny){
	$CK['asciideny2']=" checked";
}
else{
	$CK['asciideny1']=" checked";
}

if($httpdeny){
	$CK['httpdeny2']=" checked";
}
else{
	$CK['httpdeny1']=" checked";
}
if($past){
	$CK['past2']=" checked";
}
else{
	$CK['past1']=" checked";
}


if($hostdeny == 1){
	$CK['hostdeny2']=" checked";
}
else{
	$CK['hostdeny1']=" checked";
}

if($worddeny){
	$CK['worddeny2']=" checked";
}
else{
	$CK['worddeny1']=" checked";
}

if($mail == 2){
	$CK['mail3']=" checked";
}
else if($mail == 1){
	$CK['mail2']=" checked";
}
else{
	$CK['mail1']=" checked";
}
if($inlink){
	$CK['inlink2']=" checked";
}
else{
	$CK['inlink1']=" checked";
}

if($max){
	$CK['max2']=" checked";
}
else{
	$CK['max1']=" checked";
}

$o_main->Head($act2);

foreach($kinsiword as $v){
	$kword.=$v."\n";
}

foreach($kinsihost as $v){
	$khost.=$v."\n";
}


print <<<EOM
<body>
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ 環境設定</div>
<div class="midasi">基本設定</div>
<div class="t_center">
<span class="aste">*</span>は入力必須です。
</div>

<form action="{$ascript}" method="POST" name="fo" onsubmit="return Check()">
<input type="hidden" name="mode" value="set_regist">
<input type="hidden" name="pass" value="{$fo['pass']}">

<table width="650" cellpadding="4" class="table_fo set_tbl box_center">
<tr>
<td colspan="2" class="komoku" align="center">管理パスワード</td>
</tr>
<tr>
<td width="200" class="bg">管理パスワード</td><td width="450"><input type="password" size="10" name="newpass" value="">(変更するときは入力)</td>
</tr>
<tr>
<td colspan="2" class="komoku" align="center">基本設定</td>
</tr>
<tr>
<td class="bg">1ページに表示する数<span class="aste">*</span></td><td><input type="text" size="10" name="pagemax" value="{$pagemax}"></td>
</tr>
<tr>
<td class="bg">タイトルが未入力のときのタイトル</td><td><input type="text" size="30" name="titlealt" value="{$titlealt}"></td>
</tr>
<tr>
<td class="bg">記事登録数を設定するか</td><td>
1.<input type="radio" name="max" value="0"{$CK['max1']}>しない<br>
2.<input type="radio" name="max" value="1"{$CK['max2']}>する<br>
上を2にしたとき<br>
最大記録数…<input type="text" size="10" name="kijinum" value="{$kijinum}"><br>
(これを超えると古いものは削除されます。)

</td>
</tr>
<tr>
<td class="bg">URLを自動リンクするか</td><td>

1.<input type="radio" name="inlink" value="0"{$CK['inlink1']}>しない<br>
2.<input type="radio" name="inlink" value="1"{$CK['inlink2']}>する<br>
</td>
</tr>
<tr>
<td class="bg">アップする画像の最大横サイズ</td><td><input type="text" size="20" name="w_max" value="{$w_max}">
これより大きい場合は縮小します。
</td>
</tr>
<tr>
<td class="bg">文字色</td><td>
EOM;

$j=0;
for($i=0;$i<7;$i++){
	$j++;
	print "<input type=\"text\" size=\"20\" name=\"color{$j}\" value=\"{$colors[$i]}\"><br>";
}


print <<<EOM
(例)#993300<br>
未入力にすると文字色の選択フォームが表示されなくなってそのページのデフォルトの文字色が適用されます。
</td>
</tr>

<tr>
<td colspan="2" class="komoku" align="center">アイコン</td>
</tr>
<tr>
<td class="bg">アイコン画像</td><td>
EOM;
$j=0;
for($i=0;$i<20;$i++){
	$j++;
	print "<input type=\"text\" size=\"20\" name=\"icon{$j}\" value=\"{$icons[$i]}\"> <input type=\"text\" size=\"20\" name=\"icon_name{$j}\" value=\"{$icon_names[$i]}\"><br>";
}

print <<<EOM
左側にはimgフォルダの中の画像のファイル名を記入(例)kuma1.gif<br>
右側にはその名前を記入(例)くま
</td>
</tr>
<tr>
<td class="bg">管理人専用アイコン</td><td>
管理人専用のアイコンは他のひとに使わせないか<br>
1.<input type="radio" name="adicon" value="0"{$CK['adicon1']}>使わせる<br>
2.<input type="radio" name="adicon" value="1"{$CK['adicon2']}>使わせない<br>
<br>
上を2にしたとき<br>
管理人を判別するためのパスワード(英数字8字以内)…<input type="text" size="10" name="adpass" value="{$adpass}"><br>
(このパスワードと同じパスワードを、投稿するときに記事削除用のパス欄に記入)
<br><br>
<input type="text" size="20" name="adicon1" value="{$adicons[0]}"><br>
<input type="text" size="20" name="adicon2" value="{$adicons[1]}"><br>
<input type="text" size="20" name="adicon3" value="{$adicons[2]}"><br>
上のアイコン画像に入力した中から選んで記入(例)kuma1.gif
</td>
</tr>

<tr>
<td colspan="2" class="komoku" align="center">セキュリティ</td>
</tr>
<tr>
<td class="bg">書込みに日本語(ひらがな)がないときは弾く</td><td>
1.<input type="radio" name="asciideny" value="0"{$CK['asciideny1']}>弾かない<br>
2.<input type="radio" name="asciideny" value="1"{$CK['asciideny2']}>弾く<br>
</td>
</tr>
<tr>
<td class="bg">投稿に"http"の文字があるときは弾く</td><td>
1.<input type="radio" name="httpdeny" value="0"{$CK['httpdeny1']}>弾かない<br>
2.<input type="radio" name="httpdeny" value="1"{$CK['httpdeny2']}>弾く<br>
上を2にしたとき<br>
許可するhttpの数を設定…<input type="text" size="10" name="httpdenynum" value="{$httpdenynum}"><br>
(投稿文の中にここに設定した個数より"http"があれば弾きます)

</td>
</tr>
<tr>
<td class="bg">書き込み禁止にする語句を設定するか</td><td>
1.<input type="radio" name="worddeny" value="0"{$CK['worddeny1']}>しない<br>
2.<input type="radio" name="worddeny" value="1"{$CK['worddeny2']}>する<br>

上を2したとき禁止する語句を設定<br>
<textarea name="kword" rows="10" cols="50">{$kword}</textarea>
<br>
禁止語句A<br>
禁止語句B<br>
のように改行して複数登録可
</td>
</tr>
<tr>
<td class="bg">ホスト名で書き込みを拒否するか</td><td>
1.<input type="radio" name="hostdeny" value="0"{$CK['hostdeny1']}>拒否しない<br>
2.<input type="radio" name="hostdeny" value="1"{$CK['hostdeny2']}>拒否する<br>
上を2にしたとき禁止するホスト名を設定<br>
<textarea name="khost" rows="10" cols="50">{$khost}</textarea>
<br>
禁止ホストA<br>
禁止ホストB<br>
のように改行して複数登録可
(123hoge.ne.jp、456hoge.ne.jpのように変化する部分があるときは同じ部分(この場合hoge.ne.jp)を記入)

</td>
</tr>

<tr>
<td colspan="2" class="komoku" align="center">その他</td>
</tr>
<tr>
<td class="bg">書き込みの際にメールで通知</td><td>
1.<input type="radio" name="mail" value="0"{$CK['mail1']}>しない<br>
2.<input type="radio" name="mail" value="1"{$CK['mail2']}>する(書き込みのみ通知)<br>
3.<input type="radio" name="mail" value="2"{$CK['mail3']}>する(書き込みと削除と編集を通知)<br>
上を2か3にしたとき、<br>
sendmailのパス…<input type="text" size="30" name="sendmail" value="{$sendmail}"><br>
管理者のメールアドレス…<input type="text" size="30" name="mailto" value="{$mailto}"><br>
</td>
</tr>
</table>
<div class="t_center mt10">
<input type="submit" value="登録する">
</div>
</form>
<form name="fo_menu" action="" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="menu">
</form>
<script>
function Check(){
var err="";
if(document.fo.kanri.value == ""){
	err+="管理者名が入力されていません。\\n";
}
if(document.fo.mailto.value == ""){
	err+="管理者のメールアドレスが入力されていません。\\n";
}
if(document.fo.pass.value == ""){
	err+="パスワードが入力されていません。\\n";
}
if(document.fo.sendmail.value == ""){
	err+="sendmailのパスが入力されていません。\\n";
}


if(err){
	alert(err);
	return false;
}

}
</script>
</body>
</html>
EOM;
exit;
}

function Regist($fo){
global $commonpl;
global $passlog;
global $passwd;
global $o_main;
global $o_func;

if($fo['max'] && ($fo['kijinum'] == "")){
	$o_main->Error("記事の最大記録数が入力されていません。");
}
if($fo['pagemax'] == ""){
	$o_main->Error("1ページに表示する数が入力されていません。");
}


if(isset($fo['khost'])){
	$hosttmp=explode("<br>",$fo['khost']);
	foreach($hosttmp as $v){
		if($v){
			$hostline.="'".$v."'".",";
		}
	}
	
	$hostline=preg_replace("/,$/","",$hostline);
}
if(isset($fo['kword'])){
	$wordtmp=explode("<br>",$fo['kword']);
	foreach($wordtmp as $v){
		if($v){
			$wordline.="'".$v."'".",";
		}
	}
	
	$wordline=preg_replace("/,$/","",$wordline);
}

$colorsline="";
for($i=1;$i<=7;$i++){
	if($fo['color'.$i]){
		$colorsline.="'".$fo['color'.$i]."',";
	}
}
$colorsline=rtrim($colorsline,",");

$iconsline="";
for($i=1;$i<=20;$i++){
	if($fo['icon'.$i]){
		$iconsline.="'".$fo['icon'.$i]."',";
	}
}
$iconsline=rtrim($iconsline,",");

$icon_namesline="";
for($i=1;$i<=20;$i++){
	if($fo['icon_name'.$i]){
		$icon_namesline.="'".$fo['icon_name'.$i]."',";
	}
}
$icon_namesline=rtrim($icon_namesline,",");

$adiconline="";
for($i=1;$i<=3;$i++){
	if($fo['adicon'.$i]){
		$adiconline.="'".$fo['adicon'.$i]."',";
	}
}
$adiconline=rtrim($adiconline,",");

$line=<<<EOM
<?php
\$max='{$fo['max']}';
\$pagemax='{$fo['pagemax']}';
\$titlealt='{$fo['titlealt']}';
\$inlink='{$fo['inlink']}';
\$w_max='{$fo['w_max']}';
\$adicon='{$fo['adicon']}';
\$adpass='{$fo['adpass']}';
\$adicons=array($adiconline);
\$asciideny='{$fo['asciideny']}';
\$httpdeny='{$fo['httpdeny']}';
\$httpdenynum='{$fo['httpdenynum']}';
\$worddeny='{$fo['worddeny']}';
\$kinsiword=array($wordline);
\$hostdeny='{$fo['hostdeny']}';
\$kinsihost=array($hostline);
\$mail='{$fo['mail']}';
\$sendmail='{$fo['sendmail']}';
\$mailto='{$fo['mailto']}';
\$max='{$fo['max']}';
\$kijinum='{$fo['kijinum']}';
\$colors=array({$colorsline});
\$icons=array({$iconsline});
\$icon_names=array({$icon_namesline});
?>
EOM;

$fp=@fopen($commonpl,'w');
if(!$fp){
	$o_main->Error("commonplが開けません。ファイルを確認してください。");
}

fputs($fp,$line);

fclose($fp);


if($fo['newpass']){
	$coded=$o_func->Encode($fo['newpass']);

	$fp=@fopen($passlog,'w');
	if(!$fp){
		$o_main->Error('passlogに書き込めません。パーミッションなどを確認してください。');
	}
	fputs($fp,"<?php\n");
	fputs($fp,"\$passwd='$coded';");
	fputs($fp,"\n?>\n");
	fclose($fp);

	$fo['pass']=$fo['newpass'];
	$passwd=$coded;

}

self::Top($fo,'regist',"変更しました。");

}


}//Class End

?>
