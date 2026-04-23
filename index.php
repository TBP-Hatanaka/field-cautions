<?php


session_start();
error_reporting( E_ALL );




mb_language( 'ja' );
mb_internal_encoding( 'UTF-8' );




if ( isset( $_POST['response-number'] ) && $_POST['response-number'] !== '' ) {
	
	require_once( dirname( __FILE__ ) .'/php/class.responsive-bbs-write.php' );
	$responsive_bbs_write = new Responsive_Bbs_Write();
	
	$responsive_bbs_write->javascript_action_check();
	$responsive_bbs_write->referer_check();
	$responsive_bbs_write->token_check();
	$responsive_bbs_write->post_check();
	
	if ( file_exists( dirname( __FILE__ ) .'/addon/spam-block/spam.php' ) ) {
		include( dirname( __FILE__ ) .'/addon/spam-block/spam.php' );
	}
	
	$responsive_bbs_write->db_connect();
	$responsive_bbs_write->bbs_write();
	exit;
	
}




if ( file_exists( dirname( __FILE__ ) .'/addon/edit/edit-1.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/edit/edit-1.php' );
}




if ( file_exists( dirname( __FILE__ ) .'/addon/approval/approval.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/approval/approval.php' );
}




if ( isset( $_POST['delete-number'] ) && $_POST['delete-number'] !== '' ) {
	
	require_once( dirname( __FILE__ ) .'/php/class.responsive-bbs-write.php' );
	$responsive_bbs_write = new Responsive_Bbs_Write();
	
	$responsive_bbs_write->javascript_action_check();
	$responsive_bbs_write->referer_check();
	$responsive_bbs_write->session_check();
	$responsive_bbs_write->token_check();
	$responsive_bbs_write->db_connect();
	$responsive_bbs_write->bbs_delete();
	exit;
	
}




require_once( dirname( __FILE__ ) .'/php/class.responsive-bbs-display.php' );
$responsive_bbs_display = new Responsive_Bbs_Display();




if ( file_exists( dirname( __FILE__ ) .'/addon/pagination/pagination-1.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/pagination/pagination-1.php' );
}




if ( file_exists( dirname( __FILE__ ) .'/addon/search/search-1.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/search/search-1.php' );
}




$responsive_bbs_display->html_header();
$responsive_bbs_display->header();
$responsive_bbs_display->db_connect();

if ( isset( $_GET['search'] ) && file_exists( dirname( __FILE__ ) .'/addon/search/search-2.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/search/search-2.php' );
} else if ( file_exists( dirname( __FILE__ ) .'/addon/pagination/pagination-2.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/pagination/pagination-2.php' );
}

$responsive_bbs_display->bbs_form();

if ( file_exists( dirname( __FILE__ ) .'/addon/edit/edit-2.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/edit/edit-2.php' );
}

if ( file_exists( dirname( __FILE__ ) .'/addon/search/search-3.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/search/search-3.php' );
}

if ( file_exists( dirname( __FILE__ ) .'/addon/pagination/pagination-3.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/pagination/pagination-3.php' );
}

if ( isset( $_GET['search'] ) && file_exists( dirname( __FILE__ ) .'/addon/search/search-4.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/search/search-4.php' );
} else if ( file_exists( dirname( __FILE__ ) .'/addon/pagination/pagination-4.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/pagination/pagination-4.php' );
} else {
	$responsive_bbs_display->bbs_display();
}

if ( file_exists( dirname( __FILE__ ) .'/addon/pagination/pagination-3.php' ) ) {
	include( dirname( __FILE__ ) .'/addon/pagination/pagination-3.php' );
}

$responsive_bbs_display->footer();








?>