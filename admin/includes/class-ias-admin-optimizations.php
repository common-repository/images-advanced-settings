<?php

if( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'IAS_Admin_Optimizations' ) ) :

class IAS_Admin_Optimizations {

	/**
	 * (AJAX) Toggle lazy loading
	 *
	 * @return void
	 */
	function toggle_lazy_loading() {
		check_admin_referer( 'images-advanced-settings', 'nonce' );

		if ( !isset( $_POST['lazy_enabling'] ) || !is_numeric( $_POST['lazy_enabling'] ) )
			wp_send_json_error( __( 'Field is missing or incorrect', 'images-advanced-settings' ) );

		$data = get_option( Images_Advanced_Settings::$option_name );
		$data['lazy_loading'] = $_POST['lazy_enabling'];
		IAS_Helpers::update_option( $data );

		wp_send_json_success( __( 'Update done', 'images-advanced-settings' ) );
	}
}
endif;