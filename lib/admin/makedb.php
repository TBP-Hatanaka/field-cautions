<?php

Class MakeDB{

private $act;

function  __construct($act = null){
	$this->act = $act;
}


function Top(&$fo,$act){
global $o_main;
global $ascript;
global $dbdir;


$o_main->Head($act);

print <<<EOM
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ データベース</div>
<div class="midasi">データベース</div>


<div class="t_center mt20 mb20">
使用されているデータベース
</div>

<form name="makedbtablefo" method="POST" action="{$ascript}">
<input type="hidden" name="mode" value="makedb_table">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="db" value="">
</form>
<form name="makedbdatafo" method="POST" action="{$ascript}">
<input type="hidden" name="mode" value="makedb_data">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="db" value="">
</form>
<form name="delfo" method="POST" action="{$ascript}">
<input type="hidden" name="mode" value="makedb_del">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="db" value="">
</form>
<table cellpadding="4" class="db_tbl box_center">
<thead>
<tr>
<td align="center" width="200">データベース名</td>
<td align="center" class="ltd">&nbsp;</td>
<td align="center" class="ltd">&nbsp;</td>
<!--<td align="center" class="ltd">&nbsp;</td>-->
</tr>
</thead>
<tbody>
EOM;

$all=array();
if($dir=opendir($dbdir)){
	while($f=readdir($dir)){
		if($f != "." && $f != ".." && preg_match("/\.db$/",$f)){
			$all[]=$f;
		}
	}
	closedir($dir);
}
else{
	$o_main->Error("dbdirが開けません。");
}


$i=0;
foreach($all as $v){
	$i++;
	list($db)=explode(".",$v);

print <<<EOM
<tr>
<td class="bg" align="center" data-label="データベース名">{$db}</td>
<td align="center"><input type="button" value="初期化" onclick="DelCheck('{$db}')"></td>
<td align="center"><input type="button" value="テーブル確認" onclick="Table('{$db}')"></td>
<!--<td align="center"><input type="button" value="データ確認" onclick="Data('{$db}')"></td>-->
</tr>
EOM;

}

print <<<EOM
</tbody>
</table>
<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="menu">
</form>
<script>

function SubmitDel(db){
	document.delfo.db.value=db;
	document.delfo.submit();
}

function DelCheck(db){
	if(window.confirm("初期化するとすべてのデータが削除されます。本当に初期化しますか？")){ 
		SubmitDel(db);
	} 
	else{
		return false;
	}
}

function Data(n){
	document.makedbdatafo.db.value=n;
	document.makedbdatafo.submit();
}

function Table(n){
	document.makedbtablefo.db.value=n;
	document.makedbtablefo.submit();
}

</script>
</body>
</html>
EOM;



exit;

}

function GetDB($fo_db){
global $o_main;

$db="";

if($fo_db == "kiji"){
	$db='kiji.db';
}


if($db == ""){
	$o_main->Error("データベースが不適切です。");
}

return $db;
}

function GetTable($fo_db){
global $o_main;
global $dbdir;

$tb="";

if($fo_db == "kiji"){
	$tb='kiji';
}

if($tb == ""){
	$o_main->Error("データベースが不適切です。");
}

return $tb;
}

function Regist(&$fo){
global $o_main;
global $dbdir;

if(!$fo['db']){
	$o_main->Error("データベース名を入力してください。");
}

$db_d=self::GetDB($fo['db']);

$all=array();
if($dir=opendir($dbdir)){
	while($f=readdir($dir)){
		if($f != "." && $f != ".."){
			$all[]=$f;
		}
	}
	closedir($dir);
}
else{
	$o_main->Error("dbdirが開けません。");
}


if(in_array($db_d,$all)){
	$o_main->Error("このデータベースはすでに登録されています。");
}


$dblog=$dbdir.$db_d;

$fp=@fopen($dblog,'w');
if(!$fp){
	$o_main->Error("{$dblog}が作成できません。");
}
fclose($fp);

self::TableRegist($fo);

}


function Del(&$fo){
global $o_main;
global $dbdir;

$dbfile=$dbdir.$fo['db'].'.db';
if(file_exists($dbfile)){
	if(!unlink($dbfile)){
		$o_main->Error("{$dbfile}が削除できません。");
	}
}

self::Regist($fo);

if($this->act != "csv"){
	self::Top($fo,"初期化しました。");
}

}



function TableRegist(&$fo){
global $o_main;
global $o_db;
global $dbdir;
global $FIELDS;
global $ini_pkey;
global $ini_index;

$dblog=$dbdir.self::GetDB($fo['db']);

$fo_table=self::GetTable($fo['db']);

$ary=$FIELDS[$fo['db']];

$pr=$ini_pkey[$fo['db']];
$idxquery=$ini_index[$fo['db']];


if($idxquery){
	$tmp=explode(";",$idxquery);
	array_pop($tmp);
}
else{
	$tmp=array();
}

$idxnamearray=array();
$idx_a=array();

foreach($tmp as $v){
	list($head,$line)=explode('(',$v);
	list($line)=explode(')',$line);
	list($d,$d,$tb)=explode(' ',$head);
	$k=explode(",",$line);
	$dum=explode(" ",$head);
	$idxname=$dum[2];
	$idx_a[$idxname]=array();
	foreach($k as $l){
		array_push($idx_a[$idxname],$l);
	}
}

$pdo=$o_db->DBOpen($dblog);

$table=$o_db->GetTable($pdo);

if(in_array($fo_table,$table)){
	$o_main->Error("テーブル{$fo_table}はすでに登録されています。");
}

$n=array();
$k=array();

$i=0;

foreach($ary as $na => $v){

	$n[]=$na;
	$k[]=$v;

	$i++;
}


$o_db->AddTable($fo_table,$n,$k,$pr,$idx_a,$pdo);


}

function Table($fo){
global $o_main;
global $o_db;
global $ascript;
global $dbdir;

$o_main->Head("");

$dblog=$dbdir.self::GetDB($fo['db']);

$pdo=$o_db->DBOpen($dblog);

$table=self::GetTable($fo['db']);

$stmt=$o_db->SetPH("SELECT sql FROM sqlite_master WHERE name=?",$pdo);

$o_db->DoExc($stmt,[$table]);
list($fi,$ka,$pr)=$o_db->GetFieldName($stmt);
list($t_a,$idx_a)=$o_db->GetIndex($pdo);


print <<<EOM
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ <a href="javascript:void(0)" onclick="document.fo_makedbtop.submit();" class="navi">データベース</a> ＞ テーブルの確認</div>
<div class="midasi">テーブルの確認</div>

<table class="box_center mt20"><tr><td>データベース名:</td><td><b>{$fo['db']}</b></td></tr><tr><td>テーブル名:</td><td><b>{$fo['db']}</b></td></tr></table>

<table align="center" border="0" cellpadding="4" class="tb_tbl box_center mt20">
<thead>
<tr>
<td align="center" class="ltd">項目名</td><td align="center" class="ltd">型</td><td align="center" class="ltd">プライマリー</td><td align="center" class="ltd">インデックス</td>
</tr>
</thead>
<tbody>
EOM;

for($j=0;$j<count($fi);$j++){
	$p="　";
	if($pr){
		if($fi[$j] == $pr){
			$p="○";
		}
	}
	$idx="　";
	if($t_a[$fi[$j]]){
		$idx="{$t_a[$fi[$j]]}";
	}
	print "<tr>";
	print "<td data-label=\"項目名\">{$fi[$j]}</td>";
	print "<td align=\"center\" data-label=\"型\">{$ka[$j]}</td>";
	print "<td align=\"center\" data-label=\"プライマリー\">{$p}</td>";
	print "<td align=\"center\" data-label=\"インデックス\">{$idx}</td>";
	print "</tr>";
}

print <<<EOM
</tbody>
</table>
<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="menu">
<input type="hidden" name="pass" value="{$fo['pass']}">
</form>
<form name="fo_makedbtop" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="makedb_top">
<input type="hidden" name="pass" value="{$fo['pass']}">
</form>

</body>
</html>
EOM;


exit;

}



function Data(&$fo){
global $o_main;
global $o_db;
global $o_func;
global $pagemax;
global $ascript;
global $dbdir;

$o_main->Head("");

print <<<EOM
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ <a href="javascript:void(0)" onclick="document.fo_makedbtop.submit();" class="navi">データベース</a> ＞ データの確認</div>
<div class="midasi">データの確認</div>

<table class="box_center"><tr><td></td><td><span class="b">{$fo['db']}</span></b></td></tr><tr><td>テーブル名:</td><td><span class="b">{$fo['db']}</span></td></tr></table>
EOM;


$dblog=$dbdir.self::GetDB($fo['db']);

$pdo=$o_db->DBOpen($dblog);

$table=self::GetTable($fo['db']);

$stmt=$o_db->SetPH("SELECT sql FROM sqlite_master WHERE name=?",$pdo);

$o_db->DoExc($stmt,[$table]);
list($fi,$ka,$pr)=$o_db->GetFieldName($stmt);

list($t_a)=$o_db->GetIndex($pdo);

$stmt=$pdo->query("SELECT * FROM {$table}");
$all=array();
while($row=$stmt->fetch()){
	$all[]=$row;
}

if(!$fo['page']){
	$fo['page']=1;
}

$start=($fo['page'] - 1) * $pagemax + 1;
$end=$start+$pagemax-1;
$num=count($all);


$cnt=floor($num/$pagemax);
$a=$num % $pagemax;
if($a){
	$cnt++;
}

$pagetmp=self::MakePage($fo['page'],$cnt,$u);

print $pagetmp;

print "<table cellpadding=\"4\" class=\"data_tbl box_center mt20 mb20\">\n";
print "<thead>\n";
print "<tr>\n";
for($j=0;$j<count($fi);$j++){
	$p="";
	if($pr){
		if($fi[$j] == $pr){
			$p="プライマリー";
		}
	}
	$idx="";
	if($t_a[$fi[$j]]){
		$idx="インデックス:{$t_a[$fi[$j]]}";
	}
	print "<td align=\"center\" class=\"ltd\"><span class=\"b\">{$fi[$j]}</span> ({$ka[$j]}){$p}{$idx}</td>";
}
print "</tr>\n";
print "</thead>\n";
print "<tbody>\n";



$i=0;
foreach($all as $ary){
	$i++;
	if($i > $end){
		break;
	}
	if($i < $start){
		continue;
	}
	print "<tr>\n";
	foreach($ary as $n => $v){
		print "<td>{$v}</td>";
	}
	print "</tr>\n";
}
print "</tbody>\n";
print "</table>";

print $pagetmp;

print <<<EOM
<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="menu">
<input type="hidden" name="pass" value="{$fo['pass']}">
</form>
<form name="fo_makedbtop" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="makedb_top">
<input type="hidden" name="pass" value="{$fo['pass']}">
</form>
<form name="fo_makedbdata" action="{$ascript}" method="POST">
<input type="hidden" name="mode" value="makedb_data">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="db" value="{$fo['db']}">
<input type="hidden" name="page" value="">
</form>

<script>
function Page(p){
	document.fo_makedbdata.page.value=p;
	document.fo_makedbdata.submit();
}
</script>
</body>
</html>
EOM;


exit;

}



function MakePage($page,$all,$u){

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
		array_push($ptmp,"<li><a href=\"javascript:void(0);\" onclick=\"Page('{$i}');\">{$i}</a></li>");
	}
}
if($page_start > 1){
	array_unshift($ptmp,"<li class=\"ten\">...</li>");
}
if($page_end < $all){
	array_push($ptmp,"<li class=\"ten\">...</li>");
}

$pagetmp=join('',$ptmp);

if($page > 1){
	$p=$page-1;
	$prev="<li><a href=\"javascript:void(0);\" onclick=\"Page('{$p}');\">&lt;</a></li>";
	$first="<li><a href=\"javascript:void(0);\" onclick=\"Page('1');\">≪</a></li>";

}
if($page < $all){
	$p=$page+1;
	$next="<li><a href=\"javascript:void(0);\" onclick=\"Page('{$p}');\">&gt;</a></li>";
	$last="<li><a href=\"javascript:void(0);\" onclick=\"Page('{$all}');\">≫</a></li>";
}

$pagetmp="<ul class=\"pagelink\">".$first.$prev.$pagetmp.$next.$last."</ul>";

return $pagetmp;
}


}//Class End

?>
