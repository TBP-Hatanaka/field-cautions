<?php


session_start();
error_reporting( E_ALL );




mb_language( 'ja' );
mb_internal_encoding( 'UTF-8' );




require_once( dirname( __FILE__ ) .'/class.responsive-bbs-js.php' );
$responsive_bbs_js = new Responsive_Bbs_Js();




?>