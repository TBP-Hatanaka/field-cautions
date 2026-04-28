<?php header("Content-Type: text/html;charset=Shift_JIS");?>
<?php
mb_language("Japanese");
mb_regex_encoding("SJIS");
mb_internal_encoding("SJIS");

###########################################################################
# スレッド掲示板1_PHP版
# Ver3.0 PHP8.4
# https://cgi-garage.com/
###########################################################################
$listmark = array("","◆","◇","■","□","●","○","◎","★","☆");
$remaddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
if($remaddr == ""){
	$remaddr = "-";
}
$rehost = isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : '';
if($rehost == ""){
	if($remaddr != "-"){
		$rehost = gethostbyaddr($remaddr);
	} else{
		$rehost = "-";
	}
}

$finallog = "";
$set = setread('./data/set.cgi');
$log_file = setread2($set['logfilename']);
$submit = postget('submit');
$hensin = postget('hensin');
$del = postget('del');
if($submit){
	submit();
} if($hensin){
	hensin();
} if($del){
	del();
}
mainprint();

function mainprint(){
	global $set,$finallog,$log_file,$rehost;

	rsort($log_file);
	$print = readtemp($set['temp']);

	$form = "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n"
		  . "<TABLE border=\"1\">\n"
		  . "<TR><TD>お名前</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"name\"></TD></TR>\n"
		  . "<TR><TD>タイトル</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"title\"></TD></TR>\n"
		  . "<TR><TD>メールアドレス</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"mail\"></TD></TR>\n"
		  . "<TR><TD>ホームページアドレス</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"home\" value=\"https://\"></TD></TR>\n"
		  . "<TR><TD>コメント</TD>\n"
		  . "<TD><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment\"></TEXTAREA></TD></TR>\n"
		  . "<TR><TD colspan=\"2\" align=center>修正・削除パスワード：<input type=password name=delpass size=10>　　\n"
		  . "<input type=submit name=submit value=\"投稿する\"></TD></TR>\n"
		  . "</TABLE>\n"
		  . "</FORM>\n";

	$logdata = "";
	$leng = count($log_file);
	$hyouji = $set['hyouji'];
	$start = postget('start');
	$end = postget('end');
	if($start == "" || $start == "0"){
		$start = 1;
		$end = $hyouji;
	} if($end == ""){
		$end = 10;
	}
	$logdata .= linkstr($leng,$hyouji,$start,$end,''); 

	$leslog = setread2('les.cgi');
	$count = 1;
	foreach ($log_file as $k){
		if($count >= $start && $end >= $count){
			$i0 = "";$i1 = "";$i2 = "";$i3 = "";$i4 = "";$i5 = "";$i6 = "";$i7 = "";$i8 = "";
			$i = explode("\t",$k);
			if(isset($i[0])){$i0 = $i[0];}
			if(isset($i[1])){$i1 = $i[1];}
			if(isset($i[2])){$i2 = $i[2];}
			if(isset($i[3])){$i3 = $i[3];}
			if(isset($i[4])){$i4 = $i[4];}
			if(isset($i[5])){$i5 = $i[5];}
			if(isset($i[6])){$i6 = $i[6];}
			if(isset($i[7])){$i7 = $i[7];}
			if(isset($i[8])){$i8 = $i[8];}
			$i6 = preg_replace("/<!KAIGYOU>/","<BR>",$i6);
			if($i2 == ""){
				$i2 = "名無しさん";
			}
			$logdata .= "<HR>\n";
			$logdata .= "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n";
			if($i4){
				$logdata .= "<A href=\"mailto:".$i4."\">";
			}
			$logdata .= $i2;
			if($i[4]){
				$logdata .= "</A>";
			}
			$logdata .= "　　<B>".$i3."</B>　　投稿日時：".$i0."　　ホスト名：".$i1."\n"
					  . "<input type=submit name=\"hensin\" value=\"返信\"><BR><BR>\n"
					  . "$i6<BR>\n";
			if($i[5]){
				$logdata .= "<BR><A href=\"".$i5."\">$i5</A><BR>\n";
			}
			$logdata .= "<input type=password name=delpass size=10>\n"
					  . "<select name=delcom>\n"
					  . "<option value=1>修正</option>\n"
					  . "<option value=2>削除</option>\n"
					  . "</select>\n"
					  . "<input type=submit name=del value=\"実行\">\n"
					  . "<input type=hidden name=lognum value=\"".$i8."\"></FORM>\n";
			foreach ($leslog as $q){
				if($q){
					$p = array();
					$p = explode("\t",$q);
					$p0 = "";$p1 = "";$p2 = "";$p3 = "";$p4 = "";$p5 = "";$p6 = "";$p7 = "";
					if(isset($p[0])){$p0 = $p[0];}
					if(isset($p[1])){$p1 = $p[1];}
					if(isset($p[2])){$p2 = $p[2];}
					if(isset($p[3])){$p3 = $p[3];}
					if(isset($p[4])){$p4 = $p[4];}
					if(isset($p[5])){$p5 = $p[5];}
					if(isset($p[6])){$p6 = $p[6];}
					if(isset($p[7])){$p7 = $p[7];}
					if($p3 == ""){
						$p3 = "名無しさん";
					}
					if($p0 == $i8){
						$logdata .= "<UL>\n<LI>\n";
						if($p5){
							$logdata .= "<A href=\"$p[5]\">";
						}
						$logdata .= "$p3";
						if($p5){
							$logdata .= "</A>";
						}
						$p7 = preg_replace("/<!KAIGYOU>/","<BR>",$p7);
						$logdata .= "　　<B>$p4</B>　　投稿日時：".$p1."　　ホスト名：".$p2."<BR>\n"
								  . "$p7";
						if($p6){
							$logdata .= "<BR><BR>\n<A href=\"$p6\">$p6</A>\n";
						}
						$logdata .= "</UL>\n";
					}
				}
			}
		}
		$count++;
	}
	if($logdata == ""){
		$logdata .= "まだ投稿されたデータがありません。";
	}

	$print = preg_replace("/<!--FORM-->/",$form,$print);
	$print = preg_replace("/<!--LOGDATA-->/",$logdata,$print);

	echo $print;
	exit;
}
################################################################################
function linkstr($hyoujicount,$hyouji,$start,$end,$searchstr) {
	global $set;

	$frag = 0;
	$numstr = "";
	$numstr2 = "";
	$befnum = "";
	$aftnum = "";
	$mae = "";
	$usiro = "";
	if($set['pattern'] == "1"){
		$mae = "【 ";
		$usiro = " 】";
	} elseif($set['pattern'] == "2"){
		$mae = "";
		$usiro = "";
	} else{
		$mae = "[ ";
		$usiro = " ]";
	}

	$num = floor($hyoujicount / $hyouji);
	if($hyoujicount % $hyouji != 0){
		$num++;
	}
	$nownum = floor($start / $hyouji) + 1;
	for($i = 1; $i <= $num; $i++){
		$startnum = ($i - 1) * $hyouji + 1;
		$endnum = $i * $hyouji;
		if($endnum > $hyoujicount){
			$endnum = $hyoujicount;
		}
		if($i == $nownum){
			$numstr .= $mae."<B>".$i."</B>".$usiro." ";
			$frag++;
		} elseif($set['pagechange'] == "0" || $set['pagechange'] == "" ||
				($frag == "0" && ($nownum - $set['pagechangecount']) <= $i) ||
				($frag != "0" && ($nownum + $set['pagechangecount']) >= $i) ){ 
			$numstr .= $mae."<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$numstr .= "&searchstr=$code";
			}
			$numstr .= "\">$i</A>".$usiro." ";
		}
		if(  $i == ($nownum - 1) && $set['pagechange'] ){
			$befnum = "<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$befnum .= "&searchstr=$code";
			}
			$befnum .= "\"><B>&lt;&lt;前の".$hyouji."件</B></A>　";
		} elseif( $i == ($nownum + 1) && $set['pagechange'] ){
			$aftnum = "　<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$aftnum .= "&searchstr=$code";
			}
			$aftnum .= "\"><B>次の".$hyouji."件&gt;&gt;</B></A>　";
		}
	}
	$numstr2 = $befnum.$numstr.$aftnum;
	return($numstr2);
}

function del(){
	global $set,$log_file,$finallog,$rehost;

	$lognum = postget('lognum');
	$delpass = postget('delpass');
	$delcom = postget('delcom');

	$logs = array();
	if(isset($log_file[$lognum])){
		$logs = explode("\t",$log_file[$lognum]);
		if($logs[7] == $delpass && $delcom == "2"){
			array_splice($log_file,$lognum,1);
			$newlog = "";
			foreach ($log_file as $k){
				$i = explode("\t",$k);
				array_pop($i);
				$newlog .= implode("\t",$i) . "\n";
			}

			if($LOGS = @fopen("les.cgi","r")){
				flock($LOGS,LOCK_SH);;
				$newles = "";
				while(!feof($LOGS)){
					$line = fgets($LOGS);
					if($line){
						$i = explode("\t",$line);
						if(!mb_ereg("$i[0]",$lognum) && $i[0] > $lognum && $line){
							$i[0]--;
							$newles .= implode("\t",$i);
						} elseif(!mb_ereg("$i[0]",$lognum) && $line){
							$newles .= $line;
						}
					}
				}
				fclose($LOGS);
			} else{
				errorprint('file open error!',"スレッドファイルが開けません。データの削除が出来ません。");		
			}
			setchange3($newlog,$set['logfilename']);
			setchange3($newles,'les.cgi');
			$log_file = array();
			$log_file = setread2($set['logfilename']);
		} elseif($logs[7] == $delpass && $delcom == "1"){
			$print = readtemp($set['temp']);
			$logs[6] = preg_replace("/<!KAIGYOU>/","\n",$logs[6]);
			$form = "<B>データの修正</B><BR><BR>\n"
				  . "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n"
				  . "<TABLE border=\"1\">\n"
				  . "<TR><TD>お名前</TD>\n"
				  . "<TD><INPUT size=\"50\" type=\"text\" name=\"name\" value=\"$logs[2]\"></TD></TR>\n"
				  . "<TR><TD>タイトル</TD>\n"
				  . "<TD><INPUT size=\"50\" type=\"text\" name=\"title\" value=\"$logs[3]\"></TD></TR>\n"
				  . "<TR><TD>メールアドレス</TD>\n"
				  . "<TD><INPUT size=\"50\" type=\"text\" name=\"mail\" value=\"$logs[4]\"></TD></TR>\n"
				  . "<TR><TD>ホームページアドレス</TD>\n"
				  . "<TD><INPUT size=\"50\" type=\"text\" name=\"home\" value=\"$logs[5]\"></TD></TR>\n"
				  . "<TR><TD>コメント</TD>\n"
				  . "<TD><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment\">$logs[6]</TEXTAREA></TD></TR>\n"
				  . "<TR><TD>修正･削除パスワード</TD>\n"
				  . "<TD><input type=password name=delpass value=\"$logs[7]\" size=10></TD></TR>\n"
				  . "<TR><TD colspan=\"2\" align=center><input type=submit name=submit value=\"修正する\"></TD></TR>\n"
				  . "</TABLE>\n"
				  . "<input type=hidden name=delcom value=\"1\">\n"
				  . "<input type=hidden name=lognum value=\"$lognum\">\n"
				  . "<input type=hidden name=delpass2 value=\"$logs[7]\">\n"
				  . "</FORM>\n";
			$print = preg_replace("/<!--FORM-->/",$form,$print);
			$print = preg_replace("/<!--LOGDATA-->/","",$print);
			echo $print;
			exit;
		} elseif($logs[7] != $delpass){
			errorprint('修正・削除をできません',"パスワードが一致しません。");
		}
	}
}

function hensin(){
	global $set,$log_file,$finallog,$rehost;

	$lognum = postget('lognum');
	$submit = postget('submit2');
	$relog = explode("\t",$log_file[$lognum]);

	if($submit){
		$name = postget('name2');
		$title = postget('title2');
		$mail = postget('mail2');
		$home = postget('home2');
		$comment = postget('comment2');

		$name = preg_replace("/\t/"," ",$name);
		$title = preg_replace("/\t/"," ",$title);
		$mail = preg_replace("/\t/"," ",$mail);
		$home = preg_replace("/\t/"," ",$home);
		$comment = preg_replace("/\t/"," ",$comment);

		erchk($name,$title,$mail,$home,$comment,"1");
		$comment = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$comment);
		if($home == "https://"){
			$home = "";
		}

		$ltime = localtime(time());
		$time = sprintf("%04d/%02d/%02d %02d:%02d:%02d",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]);
		$newlog = "";
		if($lognum ==  '0'){
			$newlog = '0'."\t".$time."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\n";
		} elseif($lognum){
			$newlog = $lognum."\t".$time."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\n";
		}
		setchange2($newlog,'les.cgi');
		mainprint();
	}

	$relog[6] = preg_replace("/<!KAIGYOU>/","<BR>",$relog[6]);
	$form = "<FIELDSET><LEGEND align=\"left\">$relog[3]</LEGEND>\n"
		  . "$relog[6]</FIELDSET>\n"
		  . "<CENTER>\n"
		  . "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n"
		  . "<TABLE border=\"1\">"
		  . "<TR><TD>お名前</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"name2\"></TD></TR>\n"
		  . "<TR><TD>タイトル</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"title2\" value=\"RE,$relog[3]\"></TD></TR>\n"
		  . "<TR><TD>メールアドレス</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"mail2\"></TD></TR>\n"
		  . "<TR><TD>ホームページアドレス</TD>\n"
		  . "<TD><INPUT size=\"50\" type=\"text\" name=\"home2\" value=\"https://\"></TD></TR>\n"
		  . "<TR><TD>コメント</TD>\n"
		  . "<TD><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment2\"></TEXTAREA></TD></TR>\n"
		  . "<TR><TD colspan=\"2\" align=center><input type=submit name=submit2 value=\"投稿する\"></TD></TR>\n"
		  . "<input type=hidden name=hensin value=aaa>\n"
		  . "<input type=hidden name=lognum value=\"$lognum\">\n"
		  . "</TABLE>\n"
		  . "</FORM>\n";

	$print = readtemp($set['temp']);
	$print = preg_replace("/<!--FORM-->/",$form,$print);
	$print = preg_replace("/<!--LOGDATA-->/","",$print);
	echo $print;
	exit;
}

function submit(){
	global $set,$log_file,$finallog,$rehost;

	$name = postget('name');
	$title = postget('title');
	$mail = postget('mail');
	$home = postget('home');
	$comment = postget('comment');
	$delpass = postget('delpass');
	$delpass2 = postget('delpass2');
	$delcom = postget('delcom');
	$lognum = postget('lognum');

	$name = preg_replace("/\t/"," ",$name);
	$title = preg_replace("/\t/"," ",$title);
	$mail = preg_replace("/\t/"," ",$mail);
	$home = preg_replace("/\t/"," ",$home);
	$comment = preg_replace("/\t/"," ",$comment);
	$delpass = preg_replace("/\t/"," ",$delpass);
	$delpass2 = preg_replace("/\t/"," ",$delpass2);
	$delcom = preg_replace("/\t/"," ",$delcom);
	$lognum = preg_replace("/\t/"," ",$lognum);
	erchk($name,$title,$mail,$home,$comment,$delcom);
	$comment = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$comment);
	if($home == "https://"){
		$home = "";
	}

	$ltime = localtime(time());
	$time = sprintf("%04d/%02d/%02d %02d:%02d:%02d",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]);
	if($delcom == "1" &&  ($lognum || $lognum == "0")){
		$logfile = setread2($set['logfilename']);
		$newlogs = "";
		$count = "0";
		foreach ($logfile as $k){
			$i = explode("\t",$k);
			if($count == $lognum && $i[7] == $delpass2){
				$newlog = $i[0]."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\t".$delpass."\n";
				$newlogs .= $newlog;
			} elseif($count == $lognum && $i[7] != $delpass2){
				errorprint('記事の修正を出来ません。','パスワードが一致しません。');
			} else{
				array_pop($i);
				$newlogs .= implode("\t",$i) . "\n";
			}
			$count++;
		}
		setchange3($newlogs,$set['logfilename']);
	} else{
		$newlog = $time."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\t".$delpass."\n";
		setchange2($newlog,$set['logfilename']);
	}

	$log_file = setread2($set['logfilename']);
	if(isset($log_file[$set['logcount']])){
		$backlog = "";
		$newlog = "";
		$count = "0";
		foreach ($log_file as $k){
			if($count < $set['logcount']){
				$backlog .= $k . "\n";
			} elseif($count == $set['logcount']){
				$log_file = array();
				$i = explode("\t",$k);
				array_pop($i);
				$ii = implode("\t",$i);
				array_push($log_file,$ii);
				$newlog = $ii ."\n";
			}
			$count++;
		}

		$filename = './log/'.sprintf("%04d%02d%02d_%02d%02d%02d",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]).'.cgi';
		if($SET = @fopen($filename,"w")){
			flock($SET,LOCK_EX);
			fwrite($SET,$backlog);
			fclose($SET);
		} else{
			errorprint("file Open Error!","ファイルのバックアップが出来ません。<BR>log ディレクトリのパーミッションを確認してください。");
		}
		setchange3($newlog,$set['logfilename']);

		$lesdat = readtemp('les.cgi');
		setchange3('','les.cgi');
		$filename2 = './log/'.sprintf("%04d%02d%02d_%02d%02d%02d_les",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]).'.cgi';
		if($SET = @fopen($filename2,"w")){
			flock($SET,LOCK_EX);
			fwrite($SET,$lesdat);
			fclose($SET);
		} else{
			errorprint("file Open Error!","ファイルのバックアップが出来ません。<BR>log ディレクトリのパーミッションを確認してください。");
		}
	}
}

function erchk($nm,$tt,$ml,$hm,$cm,$dc) {
	global $set,$log_file,$finallog,$rehost;

	$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	if($set['exhost']){
		$exhost = explode(" ",$set['exhost']);
		foreach ($exhost as $i){
			if(mb_ereg("$i",$rehost)){
				errorprint('投稿エラー','投稿できません。');
			}
		}
	} if($set['exstr']){
		$exstr = explode(" ",$set['exstr']);
		foreach ($exstr as $i){
			if(mb_ereg($i,$cm) || mb_ereg("$i",$nm) || mb_ereg("$i",$tt) || mb_ereg("$i",$ml) || mb_ereg("$i",$hm)){
				errorprint('投稿エラー','投稿できない文字列が入力されています。');
			}
		}
	} if($set['erchk'] == "ON"){
		$str = $nm." ".$tt." ".$ml." ".$hm." ".$cm;
		if($tt == "" || $cm == ""){
			errorprint('投稿エラー','タイトルとコメントは必須項目です。');
		} if(mb_ereg("<(.+)>",$str)){
			errorprint('投稿エラー','HTMLタグはつかえません。');
		} if(!mb_ereg("[^(a-zA-Z0-9\\\|\^\~\-\=\)\(\'\&\%\$\#\"\!\ \[\]\{\}\@\`\:\;\*\+\_\/\?\.\>\,\<\s\t\r\n)]",$str)){
			errorprint('投稿エラー',"投稿できません");
		} if($set['path'] && !mb_ereg($set['path'],$ref)){
			errorprint('投稿エラー','不正なアクセスです。');
		} if(!mb_ereg("^(.+)\@(.+)\.(.+)$",$ml) && $ml){
			errorprint('投稿エラー','メールアドレスが不正です。');
		} if($rehost == "-"){
			errorprint('投稿エラー','リモートホスト名を表示してください。');
		}
	}
	$cm = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$cm);
	if($finallog && $dc != "1"){
		$fl = explode("\t",$finallog);
		if($nm == $fl[2] && $rehost == $fl[1] && $tt == $fl[3] && $ml == $fl[4] &&
			(($hm == "https://" && $fl[5] == "") || ($hm && $fl[5] && $hm == $fl[5])) &&
			$cm == $fl[6]){
				errorprint('投稿エラー','二重投稿です。'."<BR> $finallog");
		}
	}
}

function setchange($setfile,$key,$value) {
	global $chstr;
	$data = "";
	$count = 0;
	foreach($key as $i){
		$data .= $i . "=" . $value[$count] . "\n";
		$count++;
	}
	if($SET = @fopen($setfile,"w")){
		flock($SET,LOCK_EX);
		fseek($SET,0,SEEK_SET);
		fwrite($SET,$data);
		fclose($SET);
	} elseif(file_exists($setfile)){
		errorprint('file Open Error!',"ファイルの書き込みが出来ません。パーミッションを確認してみてください。8");
	} else{
		errorprint('file Open Error!',"ファイルが存在しません。<BR>空のファイルを手動で作成してください。7");
	}

	$chstr = "設定を変更しました。";
}

function setchange2($changestr,$changefile) {
	global $chstr;
	if($SET = @fopen($changefile,"a")){
		flock($SET,LOCK_EX);
		fseek($SET,0,SEEK_END);
		fwrite($SET,$changestr);
		fclose($SET);
	} elseif(file_exists($changefile)){
		errorprint("file Open Error!","ファイルの書き込みが出来ません。パーミッションを確認してみてください。6");
	} else{
		errorprint('file Open Error!',"ファイルが存在しません。<BR>空のファイルを手動で作成してください。5");
	}
	$chstr = "設定を変更しました。";
}

function setchange3($changelist,$changefile){
	global $chstr;
	if($SET = @fopen($changefile,"w")){
		flock($SET,LOCK_EX);
		fseek($SET,0,SEEK_SET);
		fwrite($SET,$changelist);
		fclose($SET);
	} elseif(file_exists($changefile)){
		errorprint("file Open Error!","ファイルの書き込みが出来ません。パーミッションを確認してみてください。4");
	} else{
		errorprint('file Open Error!',"ファイルが存在しません。<BR>空のファイルを手動で作成してください。3");
	}
	$chstr = "設定を変更しました。";
}

function setread($filename) {
	$data = array();

	if($LOGS = @fopen($filename,"r")){
		flock($LOGS,LOCK_SH);
		while(!feof($LOGS)){
			$line = fgets($LOGS);
			$line = preg_replace("/\r\n|\r|\n/","",$line);
			$setname = explode("=",$line);
			$name = $setname[0];
			$value = preg_replace("/^($name)=/","",$line);
			if($line != ""){
				$data[$setname[0]] = $value;
			}
		}
		fclose($LOGS);
	} else{
		errorprint("file open error","ファイルが開けません。ファイルが存在するか、ファイルのパーミッションを確認してください。2");
	}
	return $data;
}

function setread2($filename) {
	global $set;
	$data = array();

	if($LOGS = @fopen($filename,"r")){
		flock($LOGS,LOCK_SH);
		$count = 0;
		while(!feof($LOGS)){
			$line = fgets($LOGS);
			$line = preg_replace("/\r\n|\r|\n/","",$line);
			if($line != ""){
				if($filename == $set['logfilename']){
					$finallog = $line;
					$line .= "\t" . $count;
				}
				array_push($data,$line);
				$count++;
			}
		}
		fclose($LOGS);
	} else{
		errorprint("file open error","ファイルが開けません。ファイルが存在するか、ファイルのパーミッションを確認してください。1");
	}
	return $data;
}

function readtemp($filename) {
	if($DAT = @fopen($filename,"r")){
		flock($DAT,LOCK_SH);
		$data = "";
		while(!feof($DAT)){
			$line = fgets($DAT);
			if(isset($line)){
				$data .= $line;
			}
		}
		fclose($DAT);
	} else{
		errorprint("File Open Error!","ファイルが開けません。");
	}
	return $data;
}

function postget($name) {
	$input = "";
	if(isset($_POST[$name])){
		$input = $_POST[$name];
	}
	if($input == "" && isset($_GET[$name])){
		$input = $_GET[$name];
	}
	return($input);
}

function postarray($name) {
	$value = array();
	if(isset($_POST[$name]) && is_array(@$_POST[$name])){
			for($i = 0; $i < count(@$_POST[$name]); $i++){
				$input = $_POST[$name][$i];
				array_push($value,$input);
			}
	} elseif($name == "" && isset($_GET[$name])){
		$value[0] = $_GET[$name];
	}
	return($value);
}

function strtocode($str) {
	$code = "";
	for($i = 0; $i < strlen($str); $i++){
		$code .= "%" . dechex(ord(substr($str,$i,1)));
	}
	return($code);
}

function errorprint($title,$erstr) {
	echo "<HTML>\n<HEAD>\n";
	echo "<LINK rel=stylesheet href=cgigarage.css type=text/css>\n";
	echo "<TITLE>$title</TITLE>\n";
	echo "</HEAD>\n";
	echo "<BODY>\n";
	echo "<H1>$title</H1>\n";
	echo "<P>$erstr</P>\n";
	echo "<HR>\n";
	echo "<A href=\"https://cgi-garage.com/\">CGI-Garage</A>\n";
	echo "</BODY>\n";
	echo "</HTML>\n";
	exit;
}

?>