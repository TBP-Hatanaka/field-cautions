<?php

class Responsive_Bbs_Js {
	
	// attachment addon property
	protected $jpg                  = 1;
	protected $png                  = 1;
	protected $gif                  = 1;
	protected $upload_max_size      = 0;
	
	
	// spam-block addon property
	protected $post_url_ok          = 'true';
	
	
	
	
	// PHP public construct
	public function __construct() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/block-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/block-config.php' );
			include( dirname( __FILE__ ) .'/../addon/spam-block/config-include-js.php' );
		}
		
		
		header( 'Content-Type: application/javascript' );
		
		
		echo <<<EOM

/*--------------------------------------------------------------------------
	
	Script Name    : Responsive BBS
	Author         : FIRSTSTEP - Motohiro Tani
	Author URL     : https://www.1-firststep.com
	Create Date    : 2014/03/07
	Version        : 5.0.1
	Last Update    : 2022/06/18
	
--------------------------------------------------------------------------*/


(function( $ ) {
	
	// global variable init
	var scroll_flag = false;
	
	
	
	
	// function slice_method
	function slice_method( el ) {
		var dt      = el.parents( 'dd' ).prev( 'dt' );
		var dt_name = dt.html().replace( /<span>.*<\/span>/gi, '' );
		dt_name     = dt_name.replace( /^<span\s.*<\/span>/gi, '' );
		dt_name     = dt_name.replace( /<br>|<br \/>/gi, '' );
		return dt_name;
	}
	
	
	
	
	// function compare_method
	function compare_method( s, e ) {
		if ( s > e ) {
			return e;
		} else {
			return s;
		}
	}
	
	
	
	
	// function error_span
	function error_span( e, dt, comment, bool ) {
		if ( bool === true ) {
			var m = e.parents( 'dd' ).find( 'span.error-blank' ).text( dt + 'が' + comment + 'されていません' );
		} else {
			var m = e.parents( 'dd' ).find( 'span.error-blank' ).text( '' );
		}
	}
	
	
	
	
	// function hidden_append
	function hidden_append( name, value, element ) {
		
		$( '<input />' )
			.attr({
				type: 'hidden',
				id: name,
				name: name,
				value: value
			})
			.appendTo( element );
		
	}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/response-click.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/response-click.js' );
			include( dirname( __FILE__ ) .'/../addon/response/response-cancel.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	// function pagetop_click
	function pagetop_click() {
		
		if ( $( 'form#bbs-form' ).css( 'display' ) === 'block' ) {
			var scroll_point = $( 'form#bbs-form' ).offset().top;
		} else {
			var scroll_point = $( 'form#edit-form' ).offset().top;
		}
		
		$( 'html, body' ).animate({
			scrollTop: scroll_point - 50
		}, 500 );
		
	}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' );
			include( dirname( __FILE__ ) .'/../addon/attachment/config-include-js.php' );
			include( dirname( __FILE__ ) .'/../addon/attachment/maxsize-accept-js.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/file-change.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/file-change.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-check.js' );
			include( dirname( __FILE__ ) .'/../addon/search/normal-click.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-scroll.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-scroll.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-lightup.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-lightup.js' );
			include( dirname( __FILE__ ) .'/../addon/search/lightup-replace.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/write-scroll/write-scroll.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/write-scroll/write-scroll.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	// function required_check
	function required_check() {
		
		var form         = '';
		var title        = '';
		var name         = '';
		var attach       = '';
		var contents     = '';
		var label        = '';
		var success      = '';
		
		var error        = 0;
		var scroll_point = $( 'body' ).height();
		
		
		if ( $( this ).attr( 'id' ) === 'write-button' ) {
			
			form     = $( 'form#bbs-form' );
			title    = form.find( 'input.title' );
			name     = form.find( 'input.name' );
			attach   = form.find( 'input.attachment' );
			contents = form.find( 'textarea.contents' );
			label    = '書き込み';
			success  = 'write';
EOM;
		
		
		
		
		if ( isset( $_SESSION['responsive_bbs_login'] ) ) {
			if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-element.js' ) ) {
				include( dirname( __FILE__ ) .'/../addon/edit/edit-element.js' );
			}
		}
		
		
		
		
		echo <<<EOM

			
		}
		
		
		if ( $( '.required' ).find( title ).length ) {
			var element = $( '.required' ).find( title );
			var dt_name = slice_method( element );
			if ( element.val() === '' ) {
				error_span( element, dt_name, '入力', true );
				error++;
				scroll_point = compare_method( scroll_point, element.offset().top );
			} else {
				error_span( element, dt_name, '', false );
			}
		}
		
		if ( $( '.required' ).find( name ).length ) {
			var element = $( '.required' ).find( name );
			var dt_name = slice_method( element );
			if ( element.val() === '' ) {
				error_span( element, dt_name, '入力', true );
				error++;
				scroll_point = compare_method( scroll_point, element.offset().top );
			} else {
				error_span( element, dt_name, '', false );
			}
		}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/required-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/required-check.js' );
		}
		
		
		
		
		echo <<<EOM

		
		if ( $( '.required' ).find( contents ).length ) {
			var element = $( '.required' ).find( contents );
			var dt_name = slice_method( element );
			if ( element.val() === '' ) {
				error_span( element, dt_name, '入力', true );
				error++;
				scroll_point = compare_method( scroll_point, element.offset().top );
			} else {
				error_span( element, dt_name, '', false );
			}
		}
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/url-check.js' ) ) {
		if ( $this->post_url_ok === 'false' ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/url-check.js' );
		}
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/filetype-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/filetype-check.js' );
		}
		
		
		
		
		echo <<<EOM

		
		
		if ( error === 0 ) {
			if ( window.confirm( label + 'をしてもよろしいですか？' ) ) {
				
				send_setup( form );
				order_set( form );
				send_method( form, success, label );
				
			}
		} else {
			
			$( 'html, body' ).animate({
				scrollTop: scroll_point - 100
			}, 500 );
			
		}
		
	}
	
	
	
	
	// function send_setup
	function send_setup( form ) {
		
		hidden_append( 'javascript_action', true, form.find( 'p.submit' ) );
		
	}
	
	
	
	
	// function order_set
	function order_set( form ) {
		
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix-1.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix-1.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/mail/dt-name-set.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/mail/dt-name-set.js' );
		}
		
		
		
		
		echo <<<EOM

		
	}
	
	
	
	
	// function send_method
	function send_method( form, success, label ) {
		
		$( '<div>' )
			.addClass( 'loading-layer' )
			.appendTo( 'body' )
			.append( '<span class="loading"></span>' );
		
		setTimeout(function() {
			
			var form_data = new FormData( form.get(0) );
			
			$.ajax({
				type: form.attr( 'method' ),
				url: form.attr( 'action' ),
				cache: false,
				dataType: 'html',
				data: form_data,
				contentType: false,
				processData: false,
				
				success: function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					var response = res.split( ',' );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/write-scroll/cookie-set.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/write-scroll/cookie-set.js' );
		}
		
		
		
		
		echo <<<EOM

					if ( response[0] === success + '_success' ) {
						window.alert( label + 'が完了しました。' );
						window.location.href = response[2];
					} else {
						$( 'input#response-number, input#edit-number' ).nextAll( 'input' ).remove();
						ios_bugfix();
						window.alert( label + 'が失敗しました。' );
					}
				},
				
				error: function( res ) {
					$( 'div.loading-layer, span.loading' ).remove();
					window.alert( 'Ajax通信が失敗しました。\\nページの再読み込みをしてからもう一度お試しください。' );
				}
			});
			
		}, 1000 );
		
	}
	
	
	
	
	// function ios_bugfix
	function ios_bugfix() {
		
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix-2.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/ios-bugfix-2.js' );
		}
		
		
		
		
		echo <<<EOM

	}
	
	
	
	
	// function label_insert
	function label_insert( bbs_dd, bool ) {
		
		for ( var i = 0; i < bbs_dd.length; i++ ) {
			if ( bbs_dd.eq(i).attr( 'class' ) === 'required' ) {
				$( '<span/>' )
					.text( '必須' )
					.addClass( 'required' )
					.prependTo( bbs_dd.eq(i).prev( 'dt' ) );
			} else {
				$( '<span/>' )
					.text( '任意' )
					.addClass( 'optional' )
					.prependTo( bbs_dd.eq(i).prev( 'dt' ) );
			}
			
			$( '<span/>' )
				.addClass( 'error-blank' )
				.appendTo( bbs_dd.eq(i) );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/error-filetype.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/error-filetype.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/spam-block/span-url.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/spam-block/span-url.js' );
		}
		
		
		
		
		echo <<<EOM

		}
		
	}
	
	
	
	
	// DOM
	$( 'form.form-area input[type="text"]' ).on( 'keydown', function( e ) {
		if ( ( e.which && e.which === 13 ) || ( e.keyCode && e.keyCode === 13 ) ) {
			return false;
		} else {
			return true;
		}
	});
	
	
	var bbs_dd = $( 'form#bbs-form dl dd' );
	label_insert( bbs_dd, false );
	hidden_append( 'response-number', '0', $( 'form#bbs-form p.submit' ) );
EOM;
		
		
		
		
		if ( isset( $_SESSION['responsive_bbs_login'] ) ) {
			if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/edit-dd.js' ) ) {
				include( dirname( __FILE__ ) .'/../addon/edit/edit-dd.js' );
			}
		}
		
		
		
		
		echo <<<EOM

	
	
	$( window ).on( 'scroll', function() {
		if ( 150  < $( window ).scrollTop() ) {
			if ( scroll_flag === false ) {
				scroll_flag = true;
				$( 'div#page-top' ).fadeIn( 'fast' );
			}
		} else {
			if ( scroll_flag === true ) {
				scroll_flag = false;
				$( 'div#page-top' ).fadeOut( 'fast' );
			}
		}
	});
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/input-set.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/input-set.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/write-scroll/scroll-cookie.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/write-scroll/scroll-cookie.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-cookie.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-cookie.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/lightup-check.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/lightup-check.js' );
		}
		
		
		
		
		echo <<<EOM

	
	
	
	
	$( 'div#page-top' ).on( 'click', pagetop_click );
EOM;
		
		
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/input-change.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/input-change.js' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/response/click-cancel.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/response/click-cancel.js' );
		}
		
		
		
		
		echo <<<EOM

	
	$( 'input#write-button' ).on( 'click', required_check );
EOM;
		
		
		
		
		if ( isset( $_SESSION['responsive_bbs_login'] ) ) {
			if ( file_exists( dirname( __FILE__ ) .'/../addon/edit/required-check.js' ) ) {
				include( dirname( __FILE__ ) .'/../addon/edit/required-check.js' );
			}
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/search/search-normal.js' ) ) {
			include( dirname( __FILE__ ) .'/../addon/search/search-normal.js' );
		}
		
		
		
		
		echo <<<EOM

	
})( jQuery );
EOM;
		
	}
// PHP public construct end
	
}

?>