<!--KAKOLOG-->
<TABLE border="1">
<TR><TD>ログファイル切り替え</TD>
<TD><SELECT name=logfile>
<option value="" selected>-</option>
<option value="20071111_133810.cgi">20071111_133810.cgi</option>
<input type=submit name=logbtn value="切り替え"></TD></TR>
<TR><TD>お名前</TD>
<TD><INPUT size="50" type="text" name="name" value="CGI-GARAGE"></TD></TR>
<TR><TD>タイトル</TD>
<TD><INPUT size="50" type="text" name="title"></TD></TR>
<TR><TD>メールアドレス</TD>
<TD><INPUT size="50" type="text" name="mail" value="****@cgi-garage.com"></TD></TR>
<TR><TD>ホームページアドレス</TD>
<TD><INPUT size="50" type="text" name="home" value="https://cgi-garage.com/"></TD></TR>
<TR><TD>文字色</TD>
<TD><input type=radio name=color value="#ff0000"><FONT COLOR="#ff0000">■</FONT> 
<input type=radio name=color value="#550000"><FONT COLOR="#550000">■</FONT> 
<input type=radio name=color value="#00ff00"><FONT COLOR="#00ff00">■</FONT> 
<input type=radio name=color value="#0000ff" checked><FONT COLOR="#0000ff">■</FONT> 
<input type=radio name=color value="#666666"><FONT COLOR="#666666">■</FONT> 
<input type=radio name=color value="#ff44ff"><FONT COLOR="#ff44ff">■</FONT> 
<input type=radio name=color value="#ffff44"><FONT COLOR="#ffff44">■</FONT> 
<input type=radio name=color value="#ff4f02"><FONT COLOR="#ff4f02">■</FONT> 
<BR>
<input type=radio name=color value="#ff367f"><FONT COLOR="#ff367f">■</FONT> 
<input type=radio name=color value="#b6ff01"><FONT COLOR="#b6ff01">■</FONT> 
<input type=radio name=color value="#ffacac"><FONT COLOR="#ffacac">■</FONT> 
<input type=radio name=color value="#7faa58"><FONT COLOR="#7faa58">■</FONT> 
<input type=radio name=color value="#800055"><FONT COLOR="#800055">■</FONT> 
<input type=radio name=color value="#000000"><FONT COLOR="#000000">■</FONT> 
<input type=radio name=color value="#ffffff"><FONT COLOR="#ffffff">■</FONT> 
<input type=radio name=color value="#2c4855"><FONT COLOR="#2c4855">■</FONT> 
</TD></TR>
<TR><TD>キャラクター</TD>
<TD><select name=char>
<option value="0">かに</option>
<option value="1">かめ</option>
<option value="2">かぶとむし</option>
<option value="3">ぶた</option>
<option value="4">かっぱ</option>
<option value="5">かたつむり</option>
<option value="6">とんぼ</option>
<option value="7">ちょう</option>
<option value="8">ぞう</option>
</TD></TR>
<TR><TD>コメント</TD>
<TD><TEXTAREA rows="8" cols="50" name="comment"></TEXTAREA></TD></TR>
<TR><TD>削除パスワード</TD>
<TD><input type=password name=delpass size=10></TD></TR>
<TR><TD colspan="2"><input type=checkbox name=cookie value=aaa checked>クッキーに記録する　　　<input type=submit name=submit value="投稿する"></TD></TR>
</TABLE>
