<?php

class Responsive_Bbs_Admin_Js {
	
	// property init
	protected $token                = '';
	
	
	
	
	// PHP public construct
	public function __construct() {
		
		$session_id  = htmlspecialchars( session_id(), ENT_QUOTES, 'UTF-8' );
		$this->token = sha1( $session_id );
		
		
		header( 'Content-Type: application/javascript' );
		
		
		echo <<<EOM

(function( $ ) {
	
	// function delete_click
	function delete_click() {
		
		var delete_number = $( this ).attr( 'data-delete' );
		
		
		if ( window.confirm( '削除してもよろしいですか？' ) ) {
			
			$( '<div>' )
				.addClass( 'loading-layer' )
				.appendTo( 'body' )
				.append( '<span class="loading"></span>' );
			
			setTimeout(function() {
				
				$.ajax({
					type: $( 'form#bbs-form' ).attr( 'method' ),
					url: $( 'form#bbs-form' ).attr( 'action' ),
					cache: false,
					dataType: 'text',
					data: 'delete-number=' + delete_number + '&javascript_action=true&token={$this->token}'
				})
				.done(function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					var response = res.split( ',' );
					if ( response[0] === 'delete_success' ) {
						window.alert( '削除が完了しました。' );
						window.location.reload();
					} else {
						window.alert( '削除が失敗しました。' );
					}
				})
				.fail(function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					window.alert( 'Ajax通信が失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
				});
				
			}, 1000 );
			
		}
		
	}
	
	
	
	
	// function edit_click
	function edit_click() {
		
		if ( ! $( 'form#edit-form' ).length ) {
			return;
		}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/exist-reset.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/exist-reset.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-click.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-click.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/exist-image.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/exist-image.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-scroll.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-scroll.js' );
		}
		
		
		
		
		echo <<<EOM

		
	}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-cancel.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/edit-cancel.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/reset-click.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/reset-click.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/public-private-js.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/approval/public-private-js.php' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	$( 'span.delete' ).on( 'click', delete_click );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/click-cancel.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/edit/click-cancel.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/span-reset.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/span-reset.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/approval/span-public.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/approval/span-public.js' );
		}
		
		
		
		
		echo <<<EOM

	
})( jQuery );
EOM;
		
	}
// PHP public construct end
	
}

?>
