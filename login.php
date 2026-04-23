<?php


session_start();
error_reporting( E_ALL );




mb_language( 'ja' );
mb_internal_encoding( 'UTF-8' );




require_once( dirname( __FILE__ ) .'/php/class.responsive-bbs-login.php' );
$responsive_bbs_login = new Responsive_bbs_Login();




if ( isset( $_POST['user'] ) && $_POST['user'] !== '' ) {
if ( isset( $_POST['pass'] ) && $_POST['pass'] !== '' ) {
	$responsive_bbs_login->javascript_action_check();
	$responsive_bbs_login->referer_check();
	$responsive_bbs_login->login_check();
	exit;
}
}




if ( isset( $_POST['logout'] ) && $_POST['logout'] !== '' ) {
	$responsive_bbs_login->javascript_action_check();
	$responsive_bbs_login->referer_check();
	$responsive_bbs_login->session_check();
	$responsive_bbs_login->logout_check();
	exit;
}




if ( isset( $_SESSION['responsive_bbs_login'] ) && $_SESSION['responsive_bbs_login'] === 'responsive_bbs_login_ok' ) {
	$responsive_bbs_login->header();
	$responsive_bbs_login->logout_form();
	$responsive_bbs_login->footer();
} else {
	$responsive_bbs_login->header();
	$responsive_bbs_login->login_form();
	$responsive_bbs_login->footer();
}








?>