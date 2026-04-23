<?php


require_once( dirname( __FILE__ ) .'/class.responsive-bbs.php' );




class Responsive_bbs_Login Extends Responsive_Bbs {
	
	// public construct
	public function __construct() {
		
		parent::__construct();
		
	}
	
	
	
	
	// public header
	public function header() {
		
		echo <<<EOM
<!DOCTYPE html>
<html lang="ja" dir="ltr">
<head>
<meta charset="UTF-8" />
<title>レスポンシブBBS - 管理画面</title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<link rel="stylesheet" href="css/style.css" />
<link rel="stylesheet" href="css/login.css" />
</head>
<body>
EOM;
		
	}
	
	
	
	
	// public footer
	public function footer() {
		
		echo <<<EOM



<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js" defer="defer"></script>
<script src="{$this->dir}/js/responsive-bbs-login-js.php" defer="defer"></script>
</body>
</html>

EOM;
		
	}
	
	
	
	
	// public login_form
	public function login_form() {
		
		echo <<<EOM


<form action="{$this->url}" method="post" id="login-form">
	<h1>RESPONSIVE BBS LOGIN</h1>
	<dl>
		<dt>ユーザー名</dt>
		<dd><input type="text" id="user" name="user" value="" /></dd>
		<dt>パスワード</dt>
		<dd><input type="password" id="pass" name="pass" value="" /></dd>
	</dl>
	<p class="submit"><input type="button" id="login-button" value="ログイン" /></p>
</form>
EOM;
		
	}
	
	
	
	
	// public logout_form
	public function logout_form() {
		
		echo <<<EOM


<form action="{$this->url}" method="post" id="logout-form">
	<h1>ログイン中です</h1>
	<p><a href="{$this->dir}/" target="_blank">BBSページに行く</a></p>
	<p class="submit"><input type="button" id="logout-button" value="ログアウト" /></p>
</form>
EOM;
		
	}
	
	
	
	
	// public login_check
	public function login_check() {
		
		$post_user = htmlspecialchars( $_POST['user'], ENT_QUOTES, 'UTF-8' );
		$post_pass = htmlspecialchars( $_POST['pass'], ENT_QUOTES, 'UTF-8' );
		
		if ( $post_user === $this->admin_user && $post_pass === $this->admin_pass ) {
			$_SESSION['responsive_bbs_login'] = 'responsive_bbs_login_ok';
			session_regenerate_id( true );
			echo 'login_success,'.$this->url;
		} else {
			echo 'login_failed,'.$this->url;
		}
		
	}
	
	
	
	
	// public logout_check
	public function logout_check() {
		
		$logout_flag = htmlspecialchars( $_POST['logout'], ENT_QUOTES, 'UTF-8' );
		
		if ( $logout_flag === 'true' ) {
			$_SESSION = array();
			if ( isset( $_COOKIE[session_name()] ) ) {
				setcookie( session_name(), '', time() - 1000, '/' );
			}
			session_destroy();
			echo 'logout_success,'.$this->url;
		}
		
	}
	
}

?>