<?php

/*
   Plugin Name: Image Extensions
   Plugin URI: https://github.com/johnpbloch/image-extensions
   Description: Allow short urls to be aliased with image extensions added.
   Version: 0.1
   Author: John P. Bloch
   Author URI: http://www.johnpbloch.com
 */

class JPBImageExtensions {

	protected static $instance;

	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new JPBImageExtensions();
		}
		return self::$instance;
	}

	protected function __construct() {
		yourls_add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}

	public function __clone() {
		exit;
	}

	public function __wakeup() {
		exit;
	}

	public function plugins_loaded() {
		yourls_add_action( 'loader_failed', array( $this, 'check_request' ), 0 );
	}

	public function check_request( $request ) {
		// Because of a bug: http://code.google.com/p/yourls/issues/detail?id=1203
		if ( is_array( $request ) ) {
			$request = $request[0];
		}
		$charset = preg_quote( yourls_get_shorturl_charset(), '-' );
		$pattern = "@^([$charset]+)\.(?i)(jpe?g|gif|png)/?$@";
		if ( preg_match( $pattern, $request, $matches ) ) {
			$keyword = isset( $matches[1] ) ? $matches[1] : '';
			$keyword = yourls_sanitize_keyword( $keyword );
			yourls_do_action( 'load_template_go', $keyword );
			include( YOURLS_ABSPATH . '/yourls-go.php' );
			exit;
		}
	}

}

JPBImageExtensions::instance();

