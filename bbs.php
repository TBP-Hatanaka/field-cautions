<?php header("Content-Type: text/html;charset=Shift_JIS");?>
<?php
mb_language("Japanese");
mb_regex_encoding("SJIS");
mb_internal_encoding("SJIS");

###########################################################################
# 画像掲示板_PHP版
# Ver3.0 PHP8.4
# https://cgi-garage.com/
###########################################################################
$set = setread('./data/set.cgi');

$cookiename = isset($_COOKIE['name']) ? $_COOKIE['name'] : '';
$cookiemail = isset($_COOKIE['mail']) ? $_COOKIE['mail'] : '';
$cookiehome = isset($_COOKIE['home']) ? $_COOKIE['home'] : '';
$cookiecolor = isset($_COOKIE['color']) ? $_COOKIE['color'] : '';
$cookiechar = isset($_COOKIE['char']) ? $_COOKIE['char'] : '';

$gifname = explode(" ",$set['gifname']);
$giffile = explode(" ",$set['giffile']);
$colorarray = array('ff0000','550000','00ff00','0000ff','666666','ff44ff','ffff44','ff4f02','ff367f','b6ff01','ffacac','7faa58','800055','000000','ffffff','2c4855');

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
$log_file = array();
$logfile = postget('logfile');
if($logfile){
	$log_file = setread2('./log/'.$logfile);
} else{
	$log_file = setread2($set['logfilename']);
}
$submit = postget('submit');
$hensin = postget('hensin');
$search = postget('search');
$del = postget('del');

if($submit && $logfile == ""){
	submit();
} if($hensin && $logfile == ""){
	hensin();
} if($del && $logfile == ""){
	del();
}

mainprint();
#################################################################################
function mainprint(){
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search,$lognum;

	rsort($log_file);
	$print = readtemp($set['temp']);

	$form = "<FORM ACTION=\"bbs.php\" METHOD=\"POST\" enctype=\"multipart/form-data\">\n"
		  . "<TABLE border=\"1\">\n";

	$dir = "./log";
	$dirarray = array();
	if($DIR = @opendir($dir)){
		while($fl = readdir($DIR)){
			if(!mb_eregi("^(\.+)",$fl) && !mb_eregi("_les\.cgi",$fl)){
				array_push($dirarray,$fl);
			}
		}
		closedir($DIR);
	} else{
		errorprint('ディレクトリオープンエラー',"読み取りに失敗しました。log ディレクトリが存在するか、パーミッションを確認してください。");
	}
	$dirstr = "";
	$dircount = "0";
	foreach ($dirarray as $i){
		if(!mb_ereg("\.htm",$i) && !mb_ereg("_les",$i)){
			$dirstr .= "<option value=\"$i\"";
			if($i == $logfile){
				$dirstr .= " selected";
			}
			$dirstr .= ">$i</option>\n";
			$dircount++;
		}
	}
	if($dircount >= 1){
		$form .= "<TR><TD colspan=\"4\" align=\"center\">ログファイル切り替え:\n"
			   . "<SELECT name=logfile>\n"
			   . "<option value=\"\"";
		if($logfile == ""){
			$form .= " selected";
		}
		$form .= ">-</option>\n"
			   . "$dirstr\n"
			   . "</SELECT>\n"
			   . "<input type=submit name=logbtn value=\"切り替え\"></TD></TR>\n";
	}
	if($logfile == ""){
		$form .= "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>お名前</FONT></TD>\n"
			   . "<TD><INPUT size=\"30\" type=\"text\" name=\"name\" value=\"$cookiename\"></TD>\n"
			   . "<TD bgcolor=\"#cccccc\">メールアドレス</TD>\n"
			   . "<TD><INPUT size=\"40\" type=\"text\" name=\"mail\" value=\"$cookiemail\"></TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>タイトル</FONT></TD>\n"
			   . "<TD><INPUT size=\"30\" type=\"text\" name=\"title\"></TD>\n"
			   . "<TD bgcolor=\"#cccccc\">ホームページアドレス</TD>\n"
			   . "<TD><INPUT size=\"40\" type=\"text\" name=\"home\" value=\"";
		if($cookiehome){
			$form .= "$cookiehome";
		} else{
			$form .= "https://";
		}
		$form .= "\"></TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\">文字色</TD>\n<TD colspan=\"3\">";
		foreach ($colorarray as $i){
			$form .="<input type=radio name=color value=\"#".$i."\"";
			if($cookiecolor == ('#'.$i)){
				$form .= " checked";
			}
			$form .= "><FONT COLOR=\"#".$i."\">■</FONT> \n";
		}
		$form .= "</TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>コメント</FONT></TD>\n"
			   . "<TD colspan=\"3\"><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment\"></TEXTAREA></TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\">キャラクター</TD>\n"
			   . "<TD><select name=char>\n";
		$count = '0';
		foreach ($gifname as $i){
			$form.="<option value=\"$count\"";
			if($count == $cookiechar){
				$form .= " selected";
			}
			$form .= ">$i</option>\n";
			$count++;
		}
		$form .= "</SELECT>\n"
			   . "</TD>\n"
			   . "<TD bgcolor=\"#cccccc\">修正・削除パスワード</TD>\n"
			   . "<TD><input type=password name=delpass size=10></TD></TR>\n";
		if($set['upfile']){
			for ($i = 1; $i <= $set['upfile']; $i++){
				$form .= "<TR><TD bgcolor=\"#cccccc\">ファイル".$i."</TD>\n"
					   . "<TD colspan=\"3\"><INPUT size=\"60\" type=\"file\" name=\"file_".$i."\"></TD></TR>\n";
			}
		}
		$form .= "<TR><TD colspan=\"4\" align=\"center\">\n"
			   . "<input type=checkbox name=cookie value=aaa";
		if($cookiename || $cookiemail || $cookiehome || $cookiecolor || $cookiechar){
			$form .= " checked";
		}
		$form	.= ">クッキーに記録する　　　<input type=submit name=submit value=\"投稿する\"></TD></TR>\n";
	}
	$form .= "</TABLE>\n</FORM>\n";

	if($logfile){
		$form .= '<B>過去ログファイルを表示しています。投稿や削除は出来ません。</B>';
	}

	if($search){
		search($form);
	}
	$logdata = "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n"
			 . "<input type=text name=searchstr size=20><input type=submit name=search value=\"文字列検索\">\n";
	if($logfile){
		$logdata .= "<input type=hidden name=logfile value=\"$logfile\">\n";
	}
	$logdata .= "</FORM>\n";
	$leng = count($log_file);
	$hyouji = $set['hyouji'];
	$start = postget('start');
	$end = postget('end');
	if($start == ""){
		$start = 1;
		$end = $hyouji;
	} if($end == ""){
		$end = 10;
	}
	$logdata .= linkstr($leng,$hyouji,$start,$end,'');

	$leslog = array();
	if($logfile){
		$aa = explode("\.",$logfile);
		$leslog = setread2('./log/'.$aa[0].'_les.cgi');
	} else{
		$leslog = setread2('les.cgi');
	}
	$count = 1;
	$temp = readtemp('./data/dataprint.cgi');
	$kakutyousi = explode(" ",$set['kakutyousi']);
	foreach ($log_file as $k){
		if($count >= $start && $end >= $count){
			$i = explode("\t",$k);
			$i[6] = preg_replace("/<!KAIGYOU>/","<BR>",$i[6]);
			if($i[2] == ""){
				$i[2] = "名無しさん";
			}
			$chara = $giffile[$i[8]];
			$logdata .= "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n" . $temp . "<input type=hidden name=lognum value=\"".$i[11]."\">\n</FORM>\n";
			$logdata = preg_replace("/<!--DAYS-->/",$i[0],$logdata);
			$logdata = preg_replace("/<!--HOST-->/",$i[1],$logdata);
			$logdata = preg_replace("/<!--NAME-->/",$i[2],$logdata);
			$logdata = preg_replace("/<!--TITLE-->/",$i[3],$logdata);
			$logdata = preg_replace("/<!--MAIL-->/",$i[4],$logdata);
			$logdata = preg_replace("/<!--HOME-->/",$i[5],$logdata);
			$logdata = preg_replace("/<!--COMMENT-->/",$i[6],$logdata);
			$logdata = preg_replace("/<!--COLOR-->/",$i[7],$logdata);
			$logdata = preg_replace("/<!--CHAR-->/","<IMG SRC=\"./gif/".$chara."\">",$logdata);
			$logdata = preg_replace("/<!--NUM-->/",$count,$logdata);
			if($logfile == ""){
				$logdata = preg_replace("/<!--DELFORM-->/","<input type=password name=delpass size=10>\n<select name=delcom>\n<option value=1>修正</option>\n<option value=2>削除</option>\n</select>\n<input type=submit name=del value=\"実行\">\n",$logdata);
				$logdata = preg_replace("/<!--HENSIN-->/","<input type=submit name=\"hensin\" value=\"返信\">",$logdata);
			} else{
				$logdata = preg_replace("/<!--DELFORM-->/","",$logdata);
				$logdata = preg_replace("/<!--HENSIN-->/","",$logdata);
			}
			if($set['upfile']){
				$files = explode("<>",$i[9]);
				for ($p = 1; $p <= $set['upfile']; $p++){
					$tag = "<!--FILE".$p."-->";
					$filestr = "";
					if(isset($files[$p-1]) && $files[$p-1]){
						$frag = "0";
						foreach ($kakutyousi as $pp){
							if(mb_eregi("\.$pp$",$files[$p-1])){
								$frag++;
							}
						}
						if($frag != "0"){
							$imgsize = getimagesize($files[$p-1]);
							$w = $imgsize[0];
							$h = $imgsize[1];
							if($w >= $set['gifwidth']){
								$w = $set['gifwidth'];
							} if($h >= $set['gifheight']){
								$h = $set['gifheight'];
							}
							$filestr = "<A href=\"".$files[$p-1]."\" target=\"_blank\"><IMG SRC=\"".$files[$p-1]."\" width=\"$w\" height=\"$h\"></A>\n";
						}
					}
					$logdata = preg_replace("/".$tag."/",$filestr,$logdata);
				}
			}
			$lestemp = readtemp('./data/lesdataprint.cgi');
			$leslogstr = "";
			foreach ($leslog as $q){
				$p = explode("\t",$q);
				if($p[0] == $i[11]){
					$lt = $lestemp;
					$p[7] = preg_replace("/<!KAIGYOU>/","<BR>",$p[7]);
					$lt = preg_replace("/<!--CHAR-->/","<IMG SRC=\"./gif/".$giffile[$p[9]]."\">",$lt);
					$lt = preg_replace("/<!--MAIL-->/",$p[5],$lt);
					$lt = preg_replace("/<!--NAME-->/",$p[3],$lt);
					$lt = preg_replace("/<!--COMMENT-->/",$p[7],$lt);
					$lt = preg_replace("/<!--TITLE-->/",$p[4],$lt);
					$lt = preg_replace("/<!--DAYS-->/",$p[1],$lt);
					$lt = preg_replace("/<!--HOST-->/",$p[2],$lt);
					$lt = preg_replace("/<!--COLOR-->/",$p[8],$lt);
					$lt = preg_replace("/<!--HOME-->/",$p[6],$lt);
					if($set['upfile'] && ($set['hensingif'] == "ON")){
						$giffiles = explode("<>",$p[10]);
						for( $pp = 1; $pp <= $set['upfile']; $pp++){
							$tag = "<!--FILE".$pp."-->";
							$filestr = "";
							if(isset($giffiles[$pp-1]) && $giffiles[$pp-1]){
								$frag = "0";
								foreach ($kakutyousi as $ppp){
									if(mb_eregi("\.$ppp$",$giffiles[$pp-1])){
										$frag++;
										continue;
									}
								}
								if($frag != "0"){
									$imgsize = getimagesize($giffiles[$pp-1]);
									$w = $imgsize[0];
									$h = $imgsize[1];
									if($w >= $set['gifwidth']){
										$w = $set['gifwidth'];
									} if($h >= $set['gifheight']){
										$h = $set['gifheight'];
									}
									$filestr = "<A href=\"".$giffiles[$pp-1]."\" target=\"_blank\"><IMG SRC=\"".$giffiles[$pp-1]."\" width=\"$w\" height=\"$h\"></A>\n";
								}
							}
							$lt = preg_replace("/".$tag."/",$filestr,$lt);
						}
					} else{
						for( $pp = 1; $pp <= $set['upfile']; $pp++){
							$tag = "<!--FILE".$pp."-->";
							$lt = preg_replace("/".$tag."/","",$lt);
						}
					}
					$leslogstr .= $lt;
				}
			}
			$logdata = preg_replace("/<!--LESLOG-->/",$leslogstr,$logdata);
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
function search($form) {
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search,$logfile;

	$searchstr = postget('searchstr');
	$hyouji = $set['hyouji'];
	$start = postget('start');
	$end = postget('end');
	if($start == ""){
		$start = 1;
		$end = $hyouji;
	} if($end == ""){
		$end = 10;
	}

	if($searchstr == ""){
		errorprint('検索文字列が入力されていません','検索したい文字列を指定してください。');
	}

	$filename = "";
	if($logfile){
		$filename =  './log/' . $logfile;
	} else{
		$filename = $set['logfilename'];
	}
	if($LOGS = @fopen($filename,"r")){
		flock($LOGS,LOCK_SH);
		$count = 0;
		$data = array();
		$hit = '0';
		while(!feof($LOGS)){
			$line = fgets($LOGS);
			if(mb_substr_count($line,$searchstr) >= 1){
				$line = preg_replace("/\r\n|\r|\n/","",$line);
				$line .= "\t" . $count;
				array_push($data,$line);
				$hit++;
			}
			$count++;
		}
		fclose($LOGS);
	} else{
		errorprint('file open error!',"ログファイルが開けません。");
	}

	$logdata = "";
	if($hit >= 1){
		$logdata .= "<B>$searchstr</B> の文字が見つかったデータ<BR><BR>\n";
		$files = "";
		if($logfile){
			$filearray = explode("\.",$logfile);
			$files = './log/' . $filearray[0] . '_les.cgi';
		} else{
			$files = 'les.cgi';
		}
		$leslog = setread2($files);
		$leng = count($data);
		rsort($data);
		$logdata .= linkstr($leng,$hyouji,$start,$end,$searchstr);
		$count = 1;
		$temp = readtemp('./data/dataprint.cgi');
		$kakutyousi = explode(" ",$set['kakutyousi']);
		foreach ($data as $k){
			if($count >= $start && $end >= $count){
				$i = explode("\t",$k);
				$i[6] = preg_replace("/<!KAIGYOU>/","<BR>",$i[6]);
				if($i[2] == ""){
					$i[2] = "名無しさん";
				}
				$chara = $giffile[$i[8]];
				$logdata .= "<FORM ACTION=\"bbs.php\" METHOD=\"POST\">\n" . $temp . "<input type=hidden name=lognum value=\"".$i[11]."\">\n</FORM>\n";
				$logdata = preg_replace("/<!--DAYS-->/",$i[0],$logdata);
				$logdata = preg_replace("/<!--HOST-->/",$i[1],$logdata);
				$logdata = preg_replace("/<!--NAME-->/",$i[2],$logdata);
				$logdata = preg_replace("/<!--TITLE-->/",$i[3],$logdata);
				$logdata = preg_replace("/<!--MAIL-->/",$i[4],$logdata);
				$logdata = preg_replace("/<!--HOME-->/",$i[5],$logdata);
				$logdata = preg_replace("/<!--COMMENT-->/",$i[6],$logdata);
				$logdata = preg_replace("/<!--COLOR-->/",$i[7],$logdata);
				$logdata = preg_replace("/<!--CHAR-->/","<IMG SRC=\"./gif/".$chara."\">",$logdata);
				$logdata = preg_replace("/<!--NUM-->/",$count,$logdata);
			if($logfile == ""){
				$logdata = preg_replace("/<!--DELFORM-->/","<input type=password name=delpass size=10>\n<select name=delcom>\n<option value=1>修正</option>\n<option value=2>削除</option>\n</select>\n<input type=submit name=del value=\"実行\">\n",$logdata);
				$logdata = preg_replace("/<!--HENSIN-->/","<input type=submit name=\"hensin\" value=\"返信\">",$logdata);
			} else{
				$logdata = preg_replace("/<!--DELFORM-->/","",$logdata);
				$logdata = preg_replace("/<!--HENSIN-->/","",$logdata);
			}
				if($set['upfile']){
					$files = explode("<>",$i[9]);
					$kakutyousi = explode(" ",$set['kakutyousi']);
					for ($p = 1; $p <= $set['upfile']; $p++){
						$tag = "<!--FILE".$p."-->";
						$filestr = "";
						if($files[$p-1]){
							$frag = "0";
							foreach ($kakutyousi as $pp){
								if(mb_eregi("\.$pp$",$files[$p-1])){
									$frag++;
								}
							}
							if($frag != "0"){
								$imgsize = getimagesize($files[$p-1]);
								$w = $imgsize[0];
								$h = $imgsize[1];
								if($w >= $set['gifwidth']){
									$w = $set['gifwidth'];
								} if($h >= $set['gifheight']){
									$h = $set['gifheight'];
								}
								$filestr = "<A href=\"".$files[$p-1]."\" target=\"_blank\"><IMG SRC=\"".$files[$p-1]."\" width=\"$w\" height=\"$h\"></A>\n";
							}
						}
						$logdata = preg_replace("/".$tag."/",$filestr,$logdata);
					}
				}
				$lestemp = readtemp('./data/lesdataprint.cgi');
				$leslogstr = "";
				foreach ($leslog as $q){
					$p = explode("\t",$q);
					if($p[0] == $i[11]){
						$lt = $lestemp;
						$p[7] = preg_replace("/<!KAIGYOU>/","<BR>",$p[7]);
						$lt = preg_replace("/<!--CHAR-->/","<IMG SRC=\"./gif/".$giffile[$p[9]]."\">",$lt);
						$lt = preg_replace("/<!--MAIL-->/",$p[5],$lt);
						$lt = preg_replace("/<!--NAME-->/",$p[3],$lt);
						$lt = preg_replace("/<!--COMMENT-->/",$p[7],$lt);
						$lt = preg_replace("/<!--TITLE-->/",$p[4],$lt);
						$lt = preg_replace("/<!--DAYS-->/",$p[1],$lt);
						$lt = preg_replace("/<!--HOST-->/",$p[2],$lt);
						$lt = preg_replace("/<!--COLOR-->/",$p[8],$lt);
						$lt = preg_replace("/<!--HOME-->/",$p[6],$lt);
						if($set['upfile'] && $set['hensingif'] == "ON"){
							$giffiles = explode("<>",$p[10]);
							for( $pp = 1; $pp <= $set['upfile']; $pp++){
								$tag = "<!--FILE".$pp."-->";
								$filestr = "";
								if($giffiles[$pp-1]){
									$frag = "0";
									foreach ($kakutyousi as $ppp){
										if(mb_eregi("\.$ppp$",$giffiles[$pp-1])){
											$frag++;
											continue;
										}
									}
									if($frag != "0"){
										$imgsize = getimagesize($giffiles[$pp-1]);
										$w = $imgsize[0];
										$h = $imgsize[1];
										if($w >= $set['gifwidth']){
											$w = $set['gifwidth'];
										} if($h >= $set['gifheight']){
											$h = $set['gifheight'];
										}
										$filestr = "<A href=\"".$giffiles[$pp-1]."\" target=\"_blank\"><IMG SRC=\"".$giffiles[$pp-1]."\" width=\"$w\" height=\"$h\"></A>\n";
									}
								}
								$lt = preg_replace("/".$tag."/",$filestr,$lt);
							}
						} else{
							for( $pp = 1; $pp <= $set['upfile']; $pp++){
								$tag = "<!--FILE".$pp."-->";
								$lt = preg_replace("/".$tag."/","",$lt);
							}
						}
						$leslogstr .= $lt;
					}
				}
				$logdata = preg_replace("/<!--LESLOG-->/",$leslogstr,$logdata);
			}
			$count++;
		}
	} else{
		$logdata .= "該当するデータはありません。";
	}

	$print = readtemp($set['temp']);
	$print = preg_replace("/<!--FORM-->/",$form,$print);
	$print = preg_replace("/<!--LOGDATA-->/",$logdata,$print);
	echo $print;
	exit;
}

function linkstr($hyoujicount,$hyouji,$start,$end,$searchstr) {
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search;

	$frag = "0";
	$numstr = "";
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
	$befnum = "";
	$aftnum = "";
	for($i = 1; $i <= $num; $i++){
		$startnum = ($i - 1) * $hyouji + 1;
		$endnum = $i * $hyouji;
		if($endnum > $hyoujicount){
			$endnum = $hyoujicount;
		}
		if($i == $nownum){
			$numstr .= $mae."<B>".$i."</B>".$usiro." ";
			$frag++;
		} elseif($set['pagechange'] == "0" || 
				($frag == "0" && ($nownum - $set['pagechangecount']) <= $i) ||
				($frag != "0" && ($nownum + $set['pagechangecount']) >= $i) ){ 
			$numstr .= $mae."<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$numstr .= "&searchstr=$code&search=aaa";
			} if($logfile != ""){
				$numstr .= "&logfile=$logfile";
			}
			$numstr .= "\">$i</A>".$usiro." ";
		}
		if(  $i == ($nownum - 1) && $set['pagechange'] ){
			$befnum = "<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$befnum .= "&searchstr=$code&search=aaa";
			} if($logfile != ""){
				$befnum .= "&logfile=$logfile";
			}
			$befnum .= "\"><B>&lt;&lt;前の".$hyouji."件</B></A>　";
		} elseif( $i == ($nownum + 1) && $set['pagechange'] ){
			$aftnum = "　<A href=\"bbs.php?start=$startnum&end=$endnum&hyouji=$hyouji";
			if($searchstr != ""){
				$code = strtocode($searchstr);
				$aftnum .= "&searchstr=$code&search=aaa";
			} if($logfile != ""){
				$aftnum .= "&logfile=$logfile";
			}
			$aftnum .= "\"><B>次の".$hyouji."件&gt;&gt;</B></A>　";
		}
	}
	$numstr2 = $befnum.$numstr.$aftnum;
	return($numstr2);
}

function del(){
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search,$logfile;

	$lognum = postget('lognum');
	$delpass = postget('delpass');
	$delcom = postget('delcom');

	$logs = explode("\t",$log_file[$lognum]);
	if($logs[10] == $delpass && $delcom == "2"){
		array_splice($log_file,$lognum,1);
		$newlog = "";
		foreach ($log_file as $k){
			$i = explode("\t",$k);
			array_pop($i);
			$newlog .= implode("\t",$i) . "\n";
		}

		if($LOGS = @fopen("les.cgi","r")){
			flock($LOGS,LOCK_SH);
			$newles = "";
			while(!feof($LOGS)){
				$line = fgets($LOGS);
				$i = explode("\t",$line);
				if($i[0] != "" && !mb_ereg($i[0],$lognum) && $i[0] > $lognum){
					$i[0]--;
					$newles .= implode("\t",$i);
				} elseif(!mb_ereg("^$lognum\t",$line)){
					$newles .= implode("\t",$i);
				}
			}
			fclose($LOGS);
		} else{
			errorprint('file open error!',"スレッドファイルが開けません。データの削除が出来ません。");
		}
		setchange3($newlog,$set['logfilename']);
		setchange3($newles,'les.cgi');
		$log_file = setread2($set['logfilename']);
		$delfile = explode("<>",$logs[9]);
		foreach ($delfile as $i){
			if(file_exists($i)){
				unlink($i);
			}
		}
	} elseif($logs[10] == $delpass && $delcom == "1"){
		$print = readtemp($set['temp']);
		$logs[6] = preg_replace("/<!KAIGYOU>/","\n",$logs[6]);
		$form = "<B>データの修正</B><BR><BR>\n"
			  . "<FORM ACTION=\"bbs.php\" METHOD=\"POST\" enctype=\"multipart/form-data\">\n"
			  . "<TABLE border=\"1\">\n"
			  . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>お名前</FONT></TD>\n"
			  . "<TD><INPUT size=\"30\" type=\"text\" name=\"name\" value=\"$logs[2]\"></TD>\n"
			  . "<TD bgcolor=\"#cccccc\">メールアドレス</TD>\n"
			  . "<TD><INPUT size=\"40\" type=\"text\" name=\"mail\" value=\"$logs[4]\"></TD></TR>\n"
			  . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>タイトル</FONT></TD>\n"
			  . "<TD><INPUT size=\"30\" type=\"text\" name=\"title\" value=\"$logs[3]\"></TD>\n"
			  . "<TD bgcolor=\"#cccccc\">ホームページアドレス</TD>\n"
			  . "<TD><INPUT size=\"40\" type=\"text\" name=\"home\" value=\"$logs[5]\"></TD></TR>\n"
			  . "<TR><TD bgcolor=\"#cccccc\">文字色</TD>\n"
			  . "<TD colspan=\"3\">";
		foreach ($colorarray as $i){
			$form .= "<input type=radio name=color value=\"#".$i."\"";
			if($logs[7] == ('#'.$i)){
				$form .= " checked";
			}
			$form .= "><FONT COLOR=\"#".$i."\">■</FONT> \n";
		}
		$form .= "</TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>コメント</FONT></TD>\n"
			   . "<TD colspan=\"3\"><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment\">$logs[6]</TEXTAREA></TD></TR>\n"
			   . "<TR><TD bgcolor=\"#cccccc\">キャラクター</TD>\n"
			   . "<TD><select name=char>\n";
		$count = '0';
		foreach ($gifname as $i){
			$form.="<option value=\"$count\"";
			if($count == $logs[8]){
				$form .= " selected";
			}
			$form.=">$i</option>\n";
			$count++;
		}
		$form .= "</SELECT>\n"
			   . "</TD>\n"
			   . "<TD bgcolor=\"#cccccc\">修正・削除パスワード</TD>\n"
			   . "<TD><input type=password name=delpass size=10 value=\"$logs[10]\"></TD></TR>\n";
		if($set['upfile']){
			$logs9 = explode("<>",$logs[9]);
			for ($i = 1; $i <= $set['upfile']; $i++){
				$form .= "<TR><TD bgcolor=\"#cccccc\">ファイル".$i."</TD>\n"
					   . "<TD colspan=\"3\">";
				if(isset($logs9[$i-1]) && $logs9[$i-1]){
					$form .= "<B>・現在のファイル：<A href=\"".$logs9[$i-1]."\">".$logs9[$i-1]."</A></B> 　 \n"
						   . "<input type=checkbox name=\"delfile_".$i."\" value=\"ON\">削除<BR>\n"
						   . "<input type=hidden name=\"nowfile_".$i."\" value=\"".$logs9[$i-1]."\">\n";
				}
				$form .= "<INPUT size=\"60\" type=\"file\" name=\"file_".$i."\"></TD></TR>\n";
			}
		}
		$form .= "<TR><TD colspan=\"4\" align=\"center\">\n"
			   . "<input type=submit name=submit value=\"修正する\"></TD></TR>\n"
			   . "</TABLE>\n"
			   . "<input type=hidden name=delcom value=\"1\">\n"
			   . "<input type=hidden name=lognum value=\"$lognum\">\n"
			   . "<input type=hidden name=delpass2 value=\"$logs[10]\">\n"
			   . "</FORM>\n";
		$print = preg_replace("/<!--FORM-->/",$form,$print);
		$print = preg_replace("/<!--LOGDATA-->/","",$print);
		echo $print;
		exit;
	} elseif($logs[10] != $delpass){
		errorprint('修正・削除をできません',"パスワードが一致しません。");
	}
}

function hensin(){
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search,$logfile;

	$lognum = postget('lognum');
	$submit = postget('submit2');
	$relog = explode("\t",$log_file[$lognum]);

	if($submit){
		$name = postget('name2');
		$title = postget('title2');
		$mail = postget('mail2');
		$color = postget('color2');
		$char = postget('char2');
		$home = postget('home2');
		$comment = postget('comment2');

		$name = preg_replace("/\t/"," ",$name);
		$title = preg_replace("/\t/"," ",$title);
		$mail = preg_replace("/\t/"," ",$mail);
		$home = preg_replace("/\t/"," ",$home);
		$color = preg_replace("/\t/"," ",$color);
		$char = preg_replace("/\t/"," ",$char);
		$comment = preg_replace("/\t/"," ",$comment);

		erchk($name,$title,$mail,$home,$comment,$color,"");
		$giffiles = "";
		if($set['upfile'] && $set['hensingif'] == 'ON'){
			$giffiles = upload();
		}
		$comment = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$comment);
		if($home == "https://"){
			$home = "";
		}

		$ltime = localtime(time());
		$time = sprintf("%04d/%02d/%02d %02d:%02d:%02d",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]);
		$newlog = $lognum."\t".$time."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\t".$color."\t".$char."\t".$giffiles."\n";
		setchange2($newlog,'les.cgi');
		mainprint();
	}

	$relog[6] = preg_replace("/<!KAIGYOU>/","<BR>",$relog[6]);
	$form = "<FIELDSET><LEGEND align=\"left\">$relog[3]</LEGEND>\n"
		  . "$relog[6]</FIELDSET>\n"
		  . "<CENTER>\n"
		  . "<FORM ACTION=\"bbs.php\" METHOD=\"POST\" enctype=\"multipart/form-data\">\n"
		  . "<TABLE border=\"1\">"
		  . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>お名前</FONT></TD>\n"
		  . "<TD><INPUT size=\"30\" type=\"text\" name=\"name2\" value=\"$cookiename\"></TD>\n"
		  . "<TD bgcolor=\"#cccccc\">メールアドレス</TD>\n"
		  . "<TD><INPUT size=\"40\" type=\"text\" name=\"mail2\" value=\"$cookiemail\"></TD></TR>\n"
		  . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>タイトル</FONT></TD>\n"
		  . "<TD><INPUT size=\"30\" type=\"text\" name=\"title2\" value=\"RE,$relog[3]\"></TD>\n"
		  . "<TD bgcolor=\"#cccccc\">ホームページアドレス</TD>\n"
		  . "<TD><INPUT size=\"40\" type=\"text\" name=\"home2\" value=\"";
	if($cookiehome){
		$form.="$cookiehome";
	} else{
		$form.="https://";
	}
	$form	.= "\"></TD></TR>\n"
			 . "<TR><TD bgcolor=\"#cccccc\">文字色</TD>\n<TD colspan=\"3\">";
	foreach ($colorarray as $i){
		$form .="<input type=radio name=color2 value=\"#".$i."\"";
		if($cookiecolor == ('#'.$i)){
			$form .= " checked";
		}
		$form .= "><FONT COLOR=\"#".$i."\">■</FONT> \n";
	}
	$form .= "</TD></TR>\n"
		   . "<TR><TD bgcolor=\"#cccccc\"><FONT COLOR=RED>コメント</FONT></TD>\n"
		   . "<TD colspan=\"3\"><TEXTAREA rows=\"8\" cols=\"50\" name=\"comment2\"></TEXTAREA></TD></TR>\n"
		   . "<TR><TD bgcolor=\"#cccccc\">キャラクター</TD>\n"
		   . "<TD colspan=3><select name=char2>\n";
	$count = '0';
	foreach ($gifname as $i){
		$form.="<option value=\"$count\"";
		if($count == $cookiechar){
			$form .= " selected";
		}
		$form.=">$i</option>\n";
		$count++;
	}
	$form	.= "</SELECT>\n"
			 . "</TD></TR>\n";
	if($set['upfile'] && $set['hensingif'] == 'ON'){
		for ($i = 1; $i <= $set['upfile']; $i++){
			$form .= "<TR><TD bgcolor=\"#cccccc\">ファイル".$i."</TD>\n"
				   . "<TD colspan=\"3\"><INPUT size=\"60\" type=\"file\" name=\"file_".$i."\"></TD></TR>\n";
		}
	}
	$form .= "<TR><TD colspan=\"4\" align=\"center\">\n"
		   . "<input type=checkbox name=cookie value=aaa";
	if($cookiename || $cookiemail || $cookiehome || $cookiecolor || $cookiechar){
		$form .= " checked";
	}
	$form .= ">クッキーに記録する　　　<input type=submit name=submit2 value=\"投稿する\"></TD></TR>\n"
		   . "<input type=hidden name=hensin value=aaa>\n"
		   . "<input type=hidden name=lognum value=\"$lognum\">\n"
		   . "</TABLE>\n</FORM>\n";

	$print = readtemp($set['temp']);
	$print = preg_replace("/<!--FORM-->/",$form,$print);
	$print = preg_replace("/<!--LOGDATA-->/","",$print);
	echo $print;
	exit;
}

function submit(){
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search,$logfile;

	$name = postget('name');
	$title = postget('title');
	$mail = postget('mail');
	$home = postget('home');
	$color = postget('color');
	$char = postget('char');
	$comment = postget('comment');
	$delpass = postget('delpass');
	$delpass2 = postget('delpass2');
	$delcom = postget('delcom');
	$lognum = postget('lognum');
	$cookie = postget('cookie');

	$name = preg_replace("/\t/"," ",$name);
	$title = preg_replace("/\t/"," ",$title);
	$mail = preg_replace("/\t/"," ",$mail);
	$home = preg_replace("/\t/"," ",$home);
	$color = preg_replace("/\t/"," ",$color);
	$char = preg_replace("/\t/"," ",$char);
	$comment = preg_replace("/\t/"," ",$comment);
	$delpass = preg_replace("/\t/"," ",$delpass);
	$delpass2 = preg_replace("/\t/"," ",$delpass2);
	$delcom = preg_replace("/\t/"," ",$delcom);
	$lognum = preg_replace("/\t/"," ",$lognum);
	$cookie = preg_replace("/\t/"," ",$cookie);

	erchk($name,$title,$mail,$home,$comment,$color,$delcom);
	$giffiles;
	if($set['upfile']){
		$giffiles = upload();
	}

	$comment = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$comment);
	if($home == "https://"){
		$home = "";
	} if($cookie){
		setcookie("name",$name);
		setcookie("mail",$mail);
		setcookie("home",$home);
		setcookie("color",$color);
		setcookie("char",$char);
		$cookiename = $name;
		$cookiemail = $mail;
		$cookiehome = $home;
		$cookiecolor = $color;
		$cookiechar = $char;
	} else{
		setcookie("name","");
		setcookie("mail","");
		setcookie("home","");
		setcookie("color","");
		setcookie("char","");
		$cookiename = "";
		$cookiemail = "";
		$cookiehome = "";
		$cookiecolor = "";
		$cookiechar = "";
	}

	$ltime = localtime(time());
	$time = sprintf("%04d/%02d/%02d %02d:%02d:%02d",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]);

	if($delcom == "1" && ($lognum || $lognum == "0")){
		$logfiles = setread2($set['logfilename']);
		$newlogs = "";
		$count = "0";
		foreach ($logfiles as $k){
			$i = explode("\t",$k);
			if($count == $lognum && $i[10] == $delpass2){
				$newlog = $i[0]."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\t".$color."\t".$char."\t".$giffiles."\t".$delpass."\n";
				$newlogs .= $newlog;
			} elseif($count == $lognum && $i[10] != $delpass2){
				errorprint('記事の修正を出来ません。','パスワードが一致しません。');
			} else{
				array_pop($i);
				$newlogs .= implode("\t",$i) . "\n";
			}
			$count++;
		}
		setchange3($newlogs,$set['logfilename']);
	} else{
		$newlog = $time."\t".$rehost."\t".$name."\t".$title."\t".$mail."\t".$home."\t".$comment."\t".$color."\t".$char."\t".$giffiles."\t".$delpass."\n";
		setchange2($newlog,$set['logfilename']);
	}

	$log_file = setread2($set['logfilename']);
	if(isset($log_file[$set['logcount']]) && $log_file[$set['logcount']]){
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
			errorprint("file Open Error!","ファイルをオープンできません。<BR>過去ログファイルを作成できませんでした。<BR>log ディレクトリが書き込み不可になっている可能性があります。");
		}
		setchange3($newlog,$set['logfilename']);
		chmod($filename,0604);

		$lesdat = readtemp('les.cgi');
		setchange3('','les.cgi');
		$filename2 = './log/'.sprintf("%04d%02d%02d_%02d%02d%02d_les",$ltime[5]+1900,$ltime[4]+1,$ltime[3],$ltime[2],$ltime[1],$ltime[0]).'.cgi';
		if($SET = @fopen($filename2,"w")){
			flock($SET,LOCK_EX);
			fwrite($SET,$lesdat);
			fclose($SET);
		} else{
			errorprint("file Open Error!","ファイルをオープンできません。<BR>過去スレッドデータのファイルを作成できませんでした。<BR>log ディレクトリが書き込み不可になっている可能性があります。");
		}
		chmod($filename2,0604);
	}
	$log_file = setread2($set['logfilename']);
}

function upload(){
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search;

	$return = array();

	for ($i = 1; $i <= $set['upfile']; $i++){
		$tmpfilename = $_FILES['file_'.$i]['tmp_name'];
		$filename = $_FILES['file_'.$i]['name'];
		$filename = preg_replace("/\t/"," ",$filename);
		$nowfilename = postget('nowfile_'.$i);
		$gifdel = postget('delfile_'.$i);
		$filename2 = "";
		$newname = "";
		$tenp = "";
		$size = $set['upsize'];
		if($filename){
			$dir = array();
			if($DIR = opendir('./atache')){
				while($fl = readdir($DIR)){
					if(!mb_eregi("^\.+",$fl) || !mb_eregi("^index.html",$fl)){
						array_push($dir,$fl);
					}
				}
				closedir($DIR);
			} else{
				errorprint("アップロード失敗","画像ディレクトリをオープンできません。");
			}
			$kt = explode("\.",$filename);
			$kakutyousi = array_pop($kt);
			$filenum = "0";
			foreach ($dir as $k){
				$aa = explode("\.",$k);
				if($filenum < $aa[0] && !mb_ereg("[^0-9]",$aa[0])){
					$filenum = $aa[0];
				}
			}
			$filenum++;
			$newname = "./atache/" . $filenum . "." . $kakutyousi;
			if(filesize($tmpfilename) < ($size * 1024)){
				if(!@copy($tmpfilename,$newname)){
					echo "<B>Warning:</B> アップロード失敗。ファイルのアップロードに失敗しました。->$filename <BR>";
				}
				array_push($return,$newname);
			} else{
				errorprint("アップロードできません","ファイルサイズが大きすぎます。<BR>ファイルサイズは $size キロバイト以下にしてください。");
			}
		}
		if(($nowfilename && $filename) || ($gifdel == "ON")){
			unlink($nowfilename);
		} elseif($nowfilename){
			array_push($return,$nowfilename);
		}
	}
	$ret = implode("<>",$return);
	return($ret);
}

function erchk($nm,$tt,$ml,$hm,$cm,$cl,$dc) {
	global $set,$cookiename,$cookiemail,$cookiehome,$cookiecolor,$cookiechar,$gifname,$giffile,$colorarray,$rehost,$finallog,$log_file,$logfile,$search;

	if($set['exhost']){
		$exhost = explode(" ",$set['exhost']);
		foreach ($exhost as $i){
			if(mb_ereg($i,$rehost)){
				errorprint('投稿エラー','投稿できません。');
			}
		}
	} if($set['exstr']){
		$exstr = explode(" ",$set['exstr']);
		foreach ($exstr as $i){
			if(mb_ereg($i,$cl) || mb_ereg($i,$nm) || mb_ereg($i,$tt) || mb_ereg($i,$ml) || mb_ereg($i,$hm) || mb_ereg($i,$cm)){
				errorprint('投稿エラー','投稿できない文字列が入力されています。');
			}
		}
	} if($set['erchk'] == "ON"){
		$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$str = $nm." ".$tt." ".$ml." ".$hm." ".$cm." ".$cl;
		if($tt == "" || $cm == ""){
			errorprint('投稿エラー','タイトルとコメントは必須項目です。');
		} if(mb_ereg("<(.+)>",$str)){
			errorprint('投稿エラー','HTMLタグはつかえません。');
		} if(!mb_ereg("[^(a-zA-Z0-9\\\|\^\~\-\=\)\(\'\&\%\$\#\"\!\ \[\]\{\}\@\`\:\;\*\+\_\/\?\.\>\,\<\s\t\r\n)]",$str)){
			errorprint('投稿エラー','投稿できません');
		} if(!mb_ereg($set['path'],$ref)){
			errorprint('投稿エラー','不正なアクセスです。');
		} if(!mb_ereg("^(.+)\@(.+)\.(.+)$",$ml) && $ml){
			errorprint('投稿エラー','メールアドレスが不正です。');
		} if($rehost == "-"){
			errorprint('投稿エラー','ホスト名を表示してください。');
		}
	}
	$cm = preg_replace("/\r\n|\r|\n/","<!KAIGYOU>",$cm);
	if($finallog && $dc != "1"){
		$fl = explode("\t",$finallog);
		if($nm == $fl[2] && $rehost == $fl[1] && $tt == $fl[3] && $ml == $fl[4] &&
			(($hm == "https://" && $fl[5] == "") || ($hm && $fl[5] && $hm == $fl[5])) &&
			$cm == $fl[6]){
				errorprint('投稿エラー','二重投稿です。');
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
	global $set,$finallog;
	$data = array();

	if($LOGS = @fopen($filename,"r")){
		flock($LOGS,LOCK_SH);
		$count = "0";
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
		errorprint("file open error","ファイルが開けません。管理者にお問い合わせ下さい。9");
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
