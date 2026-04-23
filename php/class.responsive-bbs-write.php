<?php


require_once( dirname( __FILE__ ) .'/class.responsive-bbs.php' );




class Responsive_Bbs_Write Extends Responsive_Bbs {
	
	// property init
	protected $post_title           = 'タイトルなし';
	protected $post_time            = '';
	protected $post_name            = '';
	protected $post_contents        = '';
	protected $addr                 = '';
	
	
	// attachment addon property
	protected $resize_path_user    = '';
	protected $resize_path_small   = '';
	protected $resize_path_medium  = '';
	protected $resize_path_large   = '';
	protected $resize_path_larger  = '';
	protected $resize_path_largest = '';
	
	
	// mail-notification addon property
	protected $order_isset          = array();
	protected $order_count          = '0';
	
	
	
	
	// public construct
	public function __construct() {
		
		parent::__construct();
		
	}
	
	
	
	
	// public post_check
	public function post_check() {
		
		if ( isset( $_POST['title'] ) && $_POST['title'] !== '' ) {
			$this->post_title = htmlspecialchars( $_POST['title'], ENT_QUOTES, 'UTF-8' );
		}
		
		$this->post_time = date( 'Y-m-d H:i:s' );
		
		if ( isset( $_POST['name'] ) && $_POST['name'] !== '' ) {
			$this->post_name = htmlspecialchars( $_POST['name'], ENT_QUOTES, 'UTF-8' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/post-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/post-check.php' );
		}
		
		
		if ( isset( $_POST['contents'] ) && $_POST['contents'] !== '' ) {
			$this->post_contents = htmlspecialchars( $_POST['contents'], ENT_QUOTES, 'UTF-8' );
			$this->post_contents = nl2br( $this->post_contents );
			$this->post_contents = str_replace( array( "\n", "\r", "\r\n" ), '', $this->post_contents );
		}
		
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$this->addr = htmlspecialchars( $_SERVER['REMOTE_ADDR'], ENT_QUOTES, 'UTF-8' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/mail/post-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/mail/post-check.php' );
		}
		
	}
	
	
	
	
	// public bbs_write
	public function bbs_write() {
		
		$post_res  = '0';
		
		
		if ( isset( $_POST['response-number'] ) && $_POST['response-number'] !== '' ) {
			$post_res = htmlspecialchars( $_POST['response-number'], ENT_QUOTES, 'UTF-8' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/delegate-all.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/delegate-all.php' );
		}
		
		
		$stmt = $this->pdo->prepare( "INSERT INTO rb_post ( title, time, name, contents, res, delegate, addr, user, small, medium, large, larger, largest, approval ) VALUES ( :title, :time, :name, :contents, :res, 'true', :addr, :user, :small, :medium, :large, :larger, :largest, 'private' )" );
		$stmt->bindParam( ':title', $this->post_title );
		$stmt->bindParam( ':time', $this->post_time );
		$stmt->bindParam( ':name', $this->post_name );
		$stmt->bindParam( ':contents', $this->post_contents );
		$stmt->bindParam( ':res', $post_res );
		$stmt->bindParam( ':addr', $this->addr );
		$stmt->bindParam( ':user', $this->resize_path_user );
		$stmt->bindParam( ':small', $this->resize_path_small );
		$stmt->bindParam( ':medium', $this->resize_path_medium );
		$stmt->bindParam( ':large', $this->resize_path_large );
		$stmt->bindParam( ':larger', $this->resize_path_larger );
		$stmt->bindParam( ':largest', $this->resize_path_largest );
		$stmt->execute();
		
		
		if ( $stmt->errorInfo()[0] !== '00000' ) {
			echo 'write_failed,'.$this->post_time.','.$this->url;
		} else {
			$this->mail_send();
			echo 'write_success,'.$this->post_time.','.$this->url;
		}
		
	}
	
	
	
	
	// public bbs_edit
	public function bbs_edit() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/select-edit.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/select-edit.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/update-stmt.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/update-stmt.php' );
		} else {
			$update_stmt = $this->pdo->prepare( "UPDATE rb_post SET title = :title, name = :name, contents = :contents WHERE ( id = :id )" );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/bind-param.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/bind-param.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/update-smalllarge.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/update-smalllarge.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-success.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-success.php' );
		}
		
	}
	
	
	
	
	// public bbs_delete
	public function bbs_delete() {
		
		$delete_post_id = '';
		
		
		if ( isset( $_POST['delete-number'] ) && $_POST['delete-number'] !== '' ) {
			$delete_post_id = htmlspecialchars( $_POST['delete-number'], ENT_QUOTES, 'UTF-8' );
		}
		
		
		$stmt = $this->pdo->prepare( "SELECT * FROM rb_post WHERE ( id = :id )" );
		$stmt->bindParam( ':id', $delete_post_id );
		$stmt->execute();
		
		$row = $stmt->fetch();
		if ( $row['res'] === '0' ) {
			
			$delete_stmt = $this->pdo->prepare( "DELETE FROM rb_post WHERE ( id = :id )" );
			$delete_stmt->bindParam( ':id', $delete_post_id );
			$delete_stmt->execute();
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/response/delete-res.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/response/delete-res.php' );
			}
			
		} else {
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/response/delegate-one.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/response/delegate-one.php' );
			}
			
		}
		
		
		if ( $delete_stmt->errorInfo()[0] !== '00000' ) {
			echo 'delete_failed,'.$this->url;
		} else {
			echo 'delete_success,'.$this->url;
		}
		
	}
	
	
	
	
	// public canvas_ratio
	public function canvas_ratio( $original_width, $original_height, $resize_width, $resize_height ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/canvas-ratio.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/canvas-ratio.php' );
			return $return_ratio;
		}
		
	}
	
	
	
	
	// public image_resize
	public function image_resize( $attachment_name, $type, $upload_dir, $original_width, $original_height, $canvas_width, $canvas_height ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/image-resize.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/image-resize.php' );
			return $resize_path;
		}
		
	}
	
	
	
	
	// public url_check
	public function url_check() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/url-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/url-check.php' );
		}
		
	}
	
	
	
	
	// public block_ip
	public function block_ip() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/block-ip.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/block-ip.php' );
		}
		
	}
	
	
	
	
	// public block_word
	public function block_word() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/block-word.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/block-word.php' );
		}
		
	}
	
	
	
	
	// public mail_send
	public function mail_send() {
		
		if ( ! isset( $_SESSION['responsive_bbs_login'] ) ) {
			if ( file_exists( dirname( __FILE__ ) .'/../addon/mail/mail-send.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/mail/mail-send.php' );
			}
		}
		
	}
	
	
	
	
	// public approval_change
	public function approval_change( $mode ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/approval-change.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/approval/approval-change.php' );
		}
		
	}
	
}

?>