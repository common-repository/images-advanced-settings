<?php

if( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'IAS_Helpers' ) ) :

class IAS_Helpers {

    /**
	 * Format response for ajax requests
	 *
     * @param boolean $success False if it is an error
     * @param string $message Message
	 * @return array
	 */
    static function get_result_array( $success, $message ) {
		return array(
			'success' => $success,
			'message' => $message
		);
    }

	/**
	 * Replace upload directory server path by an URL path
	 *
     * @param string $server_path Server path
	 * @return string
	 */
	static function get_url_path( $server_path ) {
		$upload_dir = wp_upload_dir();
		return str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $server_path );
	}

	/**
	 * Get an HTML anchor populated with given URL
	 *
     * @param string $url URL
	 * @return string
	 */
	static function url_wrapper( $url ) {
		return '<a href="' . $url . '" target="_blank" rel="noopener">' . $url . '</a>';
	}

	/**
	 * Get a view file server path
	 *
     * @param string $file_name File name
	 * @return string
	 */
	static function get_admin_view( $file_name ) {
		return dirname( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file_name . '.php';
	}

	/**
	 * Get plugin option
	 *
	 * @return void
	 */
	static function get_option() {
		return get_option( Images_Advanced_Settings::$option_name );
	}

	/**
	 * Update plugin option
	 *
	 * @return void
	 */
	static function update_option( $data ) {
		update_option( Images_Advanced_Settings::$option_name, $data );
	}
}

endif;