<?php


require_once( dirname( __FILE__ ) .'/class.responsive-bbs.php' );




class Responsive_Bbs_Display Extends Responsive_Bbs {
	
	// pagination addon property
	protected $now_page       = '1';
	protected $all_post_count = 0;
	
	
	// search addon property
	protected $search_word    = '';
	
	
	
	
	// public construct
	public function __construct() {
		
		parent::__construct();
		
	}
	
	
	
	
	// public get_page
	public function get_page() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/get-page.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/get-page.php' );
		}
		
	}
	
	
	
	
	// public get_search
	public function get_search() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/get-search.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/get-search.php' );
		}
		
	}
	
	
	
	
	// public all_post_count
	public function all_post_count() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/all-post-count.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/all-post-count.php' );
		}
		
	}
	
	
	
	
	// public search_post_count
	public function search_post_count() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-post-count.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-post-count.php' );
		}
		
	}
	
	
	
	
	// public html_header
	public function html_header() {
		
		require_once( dirname( __FILE__ ) .'/../html/html-header.html' );
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-css.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/response-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/response-css.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/attachment-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/attachment-css.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/pagination-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/pagination-css.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-css.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/approval-css.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/approval/approval-css.php' );
		}
		
	}
	
	
	
	
	// public header
	public function header() {
		
		require_once( dirname( __FILE__ ) .'/../html/header.html' );
		
	}
	
	
	
	
	// public footer
	public function footer() {
		
		$admin_js   = '';
		$overlay_js = '';
		
		
		if ( isset( $_SESSION['responsive_bbs_login'] ) ) {
			$admin_js = PHP_EOL.'<script src="'. $this->dir .'/js/responsive-bbs-admin-js.php" defer="defer"></script>';
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/overlay-js.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/overlay-js.php' );
		}
		
		
		require_once( dirname( __FILE__ ) .'/../html/footer.html' );
		
		
		echo <<<EOM

<script src="{$this->dir}/js/responsive-bbs-js.php" defer="defer"></script>{$admin_js}{$overlay_js}
</body>
</html>
EOM;
		
	}
	
	
	
	
	// public bbs_form
	public function bbs_form() {
		
		$hidden_url_no   = '';
		$enctype         = '';
		$blockquote_html = '';
		
		
		if ( $this->upload_max_size !== 0 ) {
			$enctype = ' enctype="multipart/form-data"';
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/blockquote-get.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/blockquote-get.php' );
		}
		
		
		echo <<<EOM


<form action="{$this->url}" method="post" id="bbs-form" class="form-area"{$enctype}>
	<div class="button-area"><span id="blank">　</span></div>
	<h1>新規投稿フォーム</h1>{$blockquote_html}
EOM;
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/form-attachment.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/form-attachment.php' );
		} else {
			require_once( dirname( __FILE__ ) .'/../html/bbs-form.html' );
		}
		
		
		echo <<<EOM

	<p class="submit"><input type="button" id="write-button" value="書き込む" /><input type="hidden" name="token" value="{$this->token}" />{$hidden_url_no}</p>
</form>
EOM;
		
	}
	
	
	
	
	// public edit_form
	public function edit_form() {
		
		if ( ! isset( $_SESSION['responsive_bbs_login'] ) ) {
			return;
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-form.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-form.php' );
		}
		
	}
	
	
	
	
	// public search_form
	public function search_form() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-form.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-form.php' );
		}
		
	}
	
	
	
	
	// public bbs_display
	public function bbs_display() {
		
		$stmt = $this->pdo->prepare( "SELECT * FROM rb_post WHERE delegate = 'true' ORDER BY id DESC" );
		$stmt->execute();
		
		
		while ( $row = $stmt->fetch() ) {
			if ( $row['res'] === '0' ) {
				
				$this->post_display( $row['id'], $row['title'], $row['time'], $row['name'], $row['contents'], $row['addr'], $row['user'], $row['small'], $row['medium'], $row['large'], $row['larger'], $row['largest'], $row['approval'] );
				
			} else {
				
				$parent_stmt = $this->pdo->prepare( "SELECT * FROM rb_post WHERE id = :id" );
				$parent_stmt->bindParam( ':id', $row['res'] );
				$parent_stmt->execute();
				
				$parent_row = $parent_stmt->fetch();
				$this->post_display( $parent_row['id'], $parent_row['title'], $parent_row['time'], $parent_row['name'], $parent_row['contents'], $parent_row['addr'], $parent_row['user'], $parent_row['small'], $parent_row['medium'], $parent_row['large'], $parent_row['larger'], $parent_row['largest'], $parent_row['approval'] );
				
			}
		}
		
	}
	
	
	
	
	// public bbs_display_pagination
	public function bbs_display_pagination() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/bbs-display-pagination.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/bbs-display-pagination.php' );
		}
		
	}
	
	
	
	
	// public search_display
	public function search_display() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-display.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-display.php' );
		}
		
	}
	
	
	
	
	// public post_display
	public function post_display( $id, $title, $time, $name, $contents, $addr, $user, $small, $medium, $large, $larger, $largest, $approval ) {
		
		$time_difference = 0;
		$new_label       = '';
		$response_span   = '';
		$delete_span     = '';
		$edit_span       = '';
		$approval_div    = '';
		$approval_span   = '';
		$image_html      = '';
		$addr_li         = '';
		$button_area     = '';
		
		
		$time_difference = ( strtotime( date( 'Y-m-d H:i:s' ) ) - strtotime( $time ) ) / ( 60 * 60 * 24 );
		if ( (int)$time_difference < (int)$this->new_label_day ) {
			$new_label = '<span class="new-label">NEW</span>';
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/response-span.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/response-span.php' );
		}
		
		
		if ( isset( $_SESSION['responsive_bbs_login'] ) ) {
			$delete_span = '<span class="delete" data-delete="'.$id.'">削除する</span>';
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/parent-span.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/edit/parent-span.php' );
			}
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/parent-span.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/approval/parent-span.php' );
			}
			
			$addr_li     = PHP_EOL.'		<li>IP: <span class="addr-area">'.$addr.'</span></li>';
		}
		
		
		if ( $response_span !== '' || $edit_span !== '' || $delete_span !== '' ) {
			$button_area = PHP_EOL.'	<div class="button-area">'.$response_span.$edit_span.$delete_span.$approval_span.'</div>';
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/parent-image.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/parent-image.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/parent-post.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/approval/parent-post.php' );
		}
		
		
		echo <<<EOM


<div class="bbs-post{$approval_div}">{$button_area}
	<h2>{$new_label}<span class="title-area">{$title}</span></h2>
	<ul>
		<li><span class="time-area">{$time}</span></li>
		<li><span class="name-area">{$name}</span></li>{$addr_li}
	</ul>
	<p>{$image_html}<span class="contents-area">{$contents}</span></p>
EOM;
		
		
		$this->response_display( $id );
		
		
		echo PHP_EOL;
		echo '</div>';
		
	}
	
	
	
	
	// public response_display
	public function response_display( $id ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/response-display.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/response-display.php' );
		}
		
	}
	
	
	
	
	// public pagination_link
	public function pagination_link() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/pagination/pagination-link.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/pagination/pagination-link.php' );
		}
		
	}
	
}

?>