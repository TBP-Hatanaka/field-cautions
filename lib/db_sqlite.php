<?php

Class DB{

function DBOpen($log){
$pdo=new PDO('sqlite:'.$log);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
return $pdo;
}

function DBClose(&$pdo){
$pdo=null;
}

function GetTable(&$pdo){
global $o_main;

try{
	$stmt=$pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
	$table = array();
	while($row=$stmt->fetch()){
		$table[]=$row['name'];
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $table;
}


function SetPH($s,&$pdo){
global $o_main;

try{
	$stmt=$pdo->prepare($s);
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $stmt;
}


function SetQuery($s,&$pdo){
global $o_main;

try{
	$stmt=$pdo->query($s);
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $stmt;
}


function DoExc(&$stmt,$v){
global $o_main;

try{
	$stmt->execute($v);
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}
}


function GetAllData(&$stmt){
global $o_main;
$data = array();
try{
	while($row=$stmt->fetch()){
		$data[]=$row;
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $data;
}

function GetCount(&$stmt){
global $o_main;
$i=0;
try{
	while($row=$stmt->fetch()){
		$i++;
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $i;
}

function GetAllCol(&$stmt,$n){
global $o_main;

$data = array();
try{
	while($row=$stmt->fetch()){
		$data[]=$row[$n];
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $data;
}


function GetData(&$stmt){
global $o_main;

try{
	$data=$stmt->fetch();
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $data;
}


function GetFieldName(&$stmt){
global $o_main;

$data = array();
try{
	while($row=$stmt->fetch()){
		$data[]=$row['sql'];
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

foreach($data as $v){
	$pos=strpos($v,'CREATE TABLE');
	if($pos == 0){
		$t=$v;
		break;
	}
}


if(strpos($t,'PRIMARY KEY (') !== false){
	list($line,$dum)=explode('PRIMARY KEY (',$t);
	$t=rtrim($line,",");
	
	list($dum)=explode(')',$dum);
	$pr_a=explode(',',$dum);
	if(!is_array($pr_a)){
		$pr_a=array();
	}
}


list($line,$dum)=explode(')',$t);
list($dum,$line)=explode('(',$line);
$t_a=explode(',',$line);
if(!is_array($t_a)){
	$t_a=array();
}
$n_array=array();
$k_array=array();


foreach($t_a as $v){

	list($na,$kata,$pr)=explode(' ',$v);
	$n_array[]=$na;
	$k_array[]=$kata;
	if($pr == "PRIMARY"){
		$pr_t=$na;
	}

}



return array($n_array,$k_array,$pr_t,$t,$pr_a);
}


function DelTable($ary,&$pdo){
global $o_main;

try{
	foreach($ary as $v){
		$pdo->query("DROP TABLE {$v}");//*
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

}

function MakeQLine($nary){
$key=array_keys($nary);
$line="";
foreach($key as $v){
	$line.="$v=?,";
}
$line=rtrim($line,",");
return $line;
}


function MakeVAry($nary,$ary){
$key=array_keys($nary);
$tmp=array();
foreach($key as $v){
	$tmp[]=$ary[$v];
}
return $tmp;
}

function MakeNQLine($nary){
$key=array_keys($nary);
$line=join(",",$key);
$tmp=array();
for($i=0;$i<count($nary);$i++){
	array_push($tmp,"?");
}
$qline=join(",",$tmp);
return array($line,$qline);
}


function GetIndex(&$pdo){
global $o_main;

try{
	$stmt=$pdo->query("SELECT sql FROM sqlite_master WHERE type='index'");
	$data = array();
	while($row=$stmt->fetch()){
		$data[]=$row['sql'];
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

$all=array();
foreach($data as $a){
	if($a){
		$all[]=$a;
	}
}

$t_a=array();
$idx_a=array();
foreach($all as $v){
	list($head,$line)=explode('(',$v);
	list($line,$dum)=explode(')',$line);
	list($d,$d,$tb)=explode(' ',$head);
	$k=explode(",",$line);
	$idx_a[$tb]=array();
	foreach($k as $l){
		$t_a[$l]=$tb;
		array_push($idx_a[$tb],$l);
	}
}
return array($t_a,$idx_a,$all);
}


function AddField($n_array,$k_array,$pr,$idx_a,$n,$k,$table,&$pdo){

$empty=false;
if(!count($n_array)){
	$empty=true;
}
if($empty){
	foreach($n as $v){
		array_push($n_array,$v);
	}
	foreach($k as $v){
		array_push($k_array,$v);
	}
	$r=self::DelTable([$table]);
	$r=self::AddTable($table,$n_array,$k_array,$pr,$idx_a);
}
else{

	$all=self::GetAll($table);

	foreach($n as $v){
		array_push($n_array,$v);
	}
	foreach($k as $v){
		array_push($k_array,$v);
	}

	$r=self::DelTable([$table]);
	$r=self::AddTable($table,$n_array,$k_array,$pr,$idx_a);
	//?
	$nline=join(',',$n_array);
	foreach($all as $ary){
		for($i=0;$i<count($n);$i++){
			$ary[]="";
		}
		$tmp=array();
		foreach($ary as $v){
			$tmp[]="'".$v."'";
		}
		$vline=join(',',$tmp);

		$q="INSERT INTO $table($nline) VALUES($vline);";
		$r=sqlite_query($db,$q);//*
	}
}

}

function GetAll($table,&$pdo){
global $o_main;

try{
	$stmt=$pdo->query("SELECT * FROM {$table}");//*
	$all = array();
	while($row=$stmt->fetch()){
		$all[]=$row;
	}
}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}

return $all;
}

function AddTable($table,$n,$k,$pr,$idx_a,&$pdo){
global $o_main;

$q = "CREATE TABLE {$table}";//*
$q.=" (";
$i=0;
$j=0;

foreach($n as $v){//*
	$j++;
	$q.=$v;
	if($k[$i]){
		$q.=" ".$k[$i];
	}
	if($pr && !is_array($pr)){
		if($pr == $v){
			$q.=" PRIMARY KEY";
		}
	}
	$q.=',';
	$i++;
}



if(is_array($pr)){
	$q.="PRIMARY KEY ";
	$q.="(";
	foreach($pr as $v){
		$q.=$v.",";
	}

	$q=rtrim($q,",");

	$q.=")";
}

$q=rtrim($q,",");

$q.=");";

try{
	$pdo->query($q);
	$keys=array_keys($idx_a);
	if(count($keys)){
		foreach($keys as $k){
			$line=join(",",$idx_a[$k]);
			$q = "CREATE INDEX {$k} ON {$table}({$line});";//*
			$pdo->query($q);
		}
	}

}
catch (Exception $e) {
	$o_main->Error($e->getMessage());
}


}

function BeginTransaction(&$pdo){
$pdo->beginTransaction();
}

function Commit(&$pdo){
$pdo->commit();
}

}//Class End

?>