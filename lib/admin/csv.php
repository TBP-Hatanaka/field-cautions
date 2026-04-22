<?php

Class Csv{

function Top($fo){
global $o_main;
global $tmplog;
global $intvl;
global $ascript;
global $readnum;



$o_main->Head("");

print <<<EOM
<body>
<div class="navi"><a href="javascript:void(0)" onclick="document.fo_menu.submit();" class="navi">機能の選択</a> ＞ ダウンロード</div>
<div class="midasi">ダウンロード</div>

<div class="t_center mt20" id="box_fo">
<form action="{$ascript}" method="POST" enctype="multipart/form-data">
<input type="hidden" name="mode" value="csv_up">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input name="upfile" id="upfile" type="file" onchange="Up(event);">
</form>

「参照」をクリックしてCSVファイルを選択するとアップされます。

<form name="fo_dl" action="{$ascript}" class="mt20">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="csv_dl">
<input type="submit" value="ダウンロード">
</form>

</div>

<div id="done"></div>
<div id="loader">
記事の登録をしています...<br>
<div id="msg"></div>
<img src="./img/loader.gif">
</div>
<div class="t_center" id="err"></div>
<form name="fo_menu" action="{$ascript}" method="POST">
<input type="hidden" name="pass" value="{$fo['pass']}">
<input type="hidden" name="mode" value="menu">
</form>
<script>
let mode="";
let all;
const intvl=parseInt("{$intvl}");
const timeout=intvl*1000;
const u="{$ascript}";
const readnum=parseInt("{$readnum}");

function Up(e){

document.getElementById("box_fo").style.display="none";

document.getElementById("loader").style.display="block";

const files = e.target.files;
const fd = new FormData();
fd.append("upfile",files[0]);
fd.append("mode","csv_upload");
fd.append('page',"0");
fd.append('endflag',"0");
fd.append('count',"0");
fd.append('pass',"{$fo['pass']}");
mode="up";
Send(fd);

}


function Send(data){

const xhr=new XMLHttpRequest();

xhr.onreadystatechange = function(){
	switch(xhr.readyState){
		case 0:
			break;
		case 1:
			break;
		case 2:
			break;
		case 3:
			break;
		case 4:
			if(xhr.status == 200 || xhr.status == 304) {
				const txt=xhr.responseText;
				try{

					const data = JSON.parse(txt);

					if(data['result'] == "ok"){
						if(data['endflag'] != "1") {
							const done=parseInt(data['count'])*readnum;
							const left=data['all']-done;
							let ti=left*intvl;
							let unit;
							if(ti >= 60){
								ti=Math.floor(ti/60);
								unit='分';
							}
							else{
								unit='秒';
							}

							let msg=done+"記事を登録しました。<br>";
							msg+="全"+data['all']+"記事<br>";
							msg+="残り"+left+"記事<br>";
							msg+="残り時間約"+ti+unit+"<br>";
							Msg(msg);
							setTimeout(Send.bind(this,data),timeout);
						}
						else{
							let msg="全"+data['all']+"記事を登録完了しました。<br>";
							Done(msg);
							End();
						}
					}
					else{
						Err(data['msg']);
					}
				}
				catch(e){
					const r=txt.indexOf("<body>");
					const r2=txt.indexOf("</body>");
					let a="";
					if(r != -1 && r2 != -1){
						let tmp=txt.split("<body>");
						tmp=tmp[1].split("</body>");
						a=tmp[0];

					}
					else if(e.message.indexOf("at") != -1){
						a=e.message;
						console.log(a);
					}
					else{
						a=txt;
					}
					Err(a);
				}
			}
			break;
	}
}

xhr.open("POST",u);
if(mode != "up"){
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=UTF-8');
	data=EncodeHTMLForm(data);
}
mode="wt";

xhr.send(data);
}

function Msg(m){
	const ob=document.getElementById("msg");
	ob.innerHTML=m;
}

function Done(m){
	const ob=document.getElementById("done");
	ob.innerHTML=m;
}

function Err(e){
	document.getElementById("loader").style.display="none";
	const ob=document.getElementById("err");
	ob.innerHTML=e;
}

function End(){
	document.getElementById("loader").style.display="none";
}

function EncodeHTMLForm(data){
var params=[];
for(let name in data){
	const value=data[name];
	const param=encodeURIComponent(name)+'='+encodeURIComponent(value);
	params.push(param);
}
return params.join('&').replace(/%20/g,'+');
}

</script>
</body>
</html>
EOM;
exit;
}

function Upload($fo){
global $tmplog;
global $o_db;
global $o_main;
global $intvl;
global $ascript;
global $readnum;
global $o_func;

$o_func->UploadTmp();

$all=@file($tmplog);
if(!is_array($all)){
	$o_main->JsError("tmplogが開けません。ファイルを確認してください。");
}

$allnum=count($all);

if(!$allnum){
	$o_main->JsError("記事のデータがありません。");
}

$fo['all']=$allnum;

self::Update($fo);


}

function Update($fo){
global $o_db;
global $kijidb;
global $tmplog;
global $FIELDS;
global $makedbpl;
global $readnum;

$count=intval($fo['count']);
$page=intval($fo['page']);
$all=intval($fo['all']);

if($count == 0){
	require($makedbpl);
	$o_makedb=new MakeDB("csv");
	$fo['db']="kiji";
	$o_makedb->Del($fo);
}

$start=$page+1;

$end=$start+$readnum-1;


$fp=@fopen($tmplog,'r');
if(!$fp){
	$o_main->JsError("tmplogが開けません。ファイルを確認してください。");
}

$i=0;
$kijis=array();
while($ln=fgets($fp)){
	$i++;
	if($i < $start || $i > $end){
		continue;
	}
	
	$ln=rtrim($ln,"\n");
	$kiji=explode(",",$ln);
	$kijis[]=$kiji;
}
fclose($fp);

$pdo=$o_db->DBOpen($kijidb);
$o_db->BeginTransaction($pdo);
list($nline,$qline)=$o_db->MakeNQLine($FIELDS['kiji']);
$stmt=$o_db->SetPH("INSERT INTO kiji ({$nline}) VALUES ({$qline})",$pdo);
foreach($kijis as $kiji){
	$o_db->DoExc($stmt,$kiji);
}
$o_db->Commit($pdo);

$count++;

$page+=$readnum;

if($all <= $end){
	$fo['endflag']="1";
}

$data=array();
$data['pass']=$fo['pass'];
$data['mode']=$fo['mode'];
$data['result']="ok";
$data['endflag']=$fo['endflag'];
$data['count']=$count;
$data['page']=$page;
$data['all']=$all;

$js=self::MakeJS($data);


header("Content-type: text/json");
print $js;

exit;

}


function MakeJS($ary){

$js='{';
foreach($ary as $n=>$v){
	$js.='"'.$n.'":'.'"'.$v.'",';
}

$js=rtrim($js,",");

$js.='}';

return $js;
}


function Dl($fo){
global $kijidb;
global $o_db;


$tmp=explode("/",$kijidb);
$filename=$tmp[count($tmp)-1];

list($filename)=explode(".",$filename);

$filename=$filename.'.csv';

$pdo=$o_db->DBOpen($kijidb);

$stmt=$o_db->SetQuery("SELECT * FROM kiji",$pdo);
$data=$o_db->GetAllData($stmt);


foreach($data as $row){
	$l="";
	$last=count($row);
	$i=0;
	foreach($row as $n=>$v){
		$i++;
		$l.=$v;
		if($i < $last){
			$l.=',';
		}
	}
	$l.="\n";
	$buffer.=$l;
}


$len=strlen($buffer);

header("Content-Disposition: attachment; filename=\"{$filename}\"\n");
header("Content-Type: application/octet-stream\n");
header("Content-length: {$len}\n\n");
print $buffer;

exit;

}



}//Class End


?>
