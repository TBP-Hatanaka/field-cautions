<?php

class Responsive_Bbs {
	
	// property init
	protected $url                   = '';
	protected $dir                   = '';
	
	protected $new_label_day         = '1';
	
	protected $admin_user            = '';
	protected $admin_pass            = '';
	
	protected $domain_name           = '';
	protected $token                 = '';
	
	protected $pdo                   = '';
	
	
	// attachment addon property
	protected $jpg                   = 1;
	protected $png                   = 1;
	protected $gif                   = 1;
	protected $upload_max_size       = 0;
	
	protected $resize_user_width     = 300;
	protected $resize_user_height    = 300;
	
	protected $resize_small_width    = 200;
	protected $resize_small_height   = 200;
	protected $resize_medium_width   = 750;
	protected $resize_medium_height  = 750;
	protected $resize_large_width    = 1080;
	protected $resize_large_height   = 1080;
	protected $resize_larger_width   = 1280;
	protected $resize_larger_height  = 720;
	protected $resize_largest_width  = 1920;
	protected $resize_largest_height = 1080;
	
	
	// pagination addon property
	protected $show_posts            = '0';
	
	
	// spam-block addon property
	protected $post_url_ok           = 'true';
	protected $block_ip              = array();
	protected $block_word            = array();
	
	
	// mail-notification addon property
	protected $send_address          = array();
	protected $send_subject          = '';
	protected $send_body             = '';
	
	
	
	
	// public construct
	public function __construct() {
		
		require_once( dirname( __FILE__ ) .'/config.php' );
		
		$this->url                 = '//'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$this->dir                 = '//'.$_SERVER['HTTP_HOST'].dirname( $_SERVER['SCRIPT_NAME'] );
		
		$this->new_label_day       = $rb_new_label_day;
		
		$this->admin_user          = $rb_admin_user;
		$this->admin_pass          = $rb_admin_pass;
		
		$this->domain_name         = $_SERVER['HTTP_HOST'];
		$session_id                = htmlspecialchars( session_id(), ENT_QUOTES, 'UTF-8' );
		$this->token               = sha1( $session_id );
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' );
			include( dirname( __FILE__ ) .'/../addon/attachment/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/pagination-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/pagination-config.php' );
			include( dirname( __FILE__ ) .'/../addon/pagination/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/block-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/block-config.php' );
			include( dirname( __FILE__ ) .'/../addon/spam-block/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/mail/mail-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/mail/mail-config.php' );
			include( dirname( __FILE__ ) .'/../addon/mail/config-include.php' );
		}
		
	}
	
	
	
	
	// public db_connect
	public function db_connect() {
		
		try {
			$this->pdo = new PDO( "sqlite:". dirname( __FILE__ ) ."/../db/responsive_bbs.sqlite3" );
		} catch ( PDOException $e ) {
			exit( 'データベース接続に失敗しました。'. $e->getMessage() );
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS `rb_post`"
			."( "
			."`id` INTEGER PRIMARY KEY AUTOINCREMENT, "
			."`title` TEXT, "
			."`time` DATETIME, "
			."`name` VARCHAR(255), "
			."`contents` TEXT, "
			."`res` VARCHAR(255), "
			."`delegate` VARCHAR(255), "
			."`addr` VARCHAR(255), "
			."`user` VARCHAR(255), "
			."`small` VARCHAR(255), "
			."`medium` VARCHAR(255), "
			."`large` VARCHAR(255), "
			."`larger` VARCHAR(255), "
			."`largest` VARCHAR(255), "
			."`approval` VARCHAR(255)"
			." );";
		
		$stmt = $this->pdo->prepare( $sql );
		$stmt->execute();
		
	}
	
	
	
	
	// public javascript_action_check
	public function javascript_action_check() {
		
		if ( ! ( isset( $_POST['javascript_action'] ) && $_POST['javascript_action'] === 'true' ) ) {
			echo 'spam_failed-0001';
			exit;
		}
		
	}
	
	
	
	
	// public referer_check
	public function referer_check() {
		
		if ( $this->domain_name !== '' ) {
			if ( strpos( $_SERVER['HTTP_REFERER'], $this->domain_name ) === false ) {
				echo 'spam_failed-0002';
				exit;
			}
		}
		
	}
	
	
	
	
	// public session_check
	public function session_check() {
		
		if ( ! ( isset( $_SESSION['responsive_bbs_login'] ) && $_SESSION['responsive_bbs_login'] === 'responsive_bbs_login_ok' ) ) {
			echo 'spam_failed-0003';
			exit;
		}
		
	}
	
	
	
	
	// public token_check
	public function token_check() {
		
		if ( ! ( isset( $_POST['token'] ) && $_POST['token'] === $this->token ) ) {
			echo 'spam_failed-0004';
			exit;
		}
		
	}
	
	
	
	
	// public copy_file
	public function copy_file( $source, $output ) {
		
		if ( ! copy( $source, $output ) ) {
			echo 'copy_failed';
			exit;
		}
		
	}
	
	
	
	
	// public get_width_height
	public function get_width_height( $image, $width_height ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/get-width-height.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/get-width-height.php' );
			return $return_size;
		}
		
	}
	
}

?>