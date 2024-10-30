<?php

if( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'IAS_Admin_Attachments' ) ) :

class IAS_Admin_Attachments {

    /**
	 * Get attachments ids
	 *
     * @param string $size_name Size name
	 * @return array Attachments ids
	 */
    static function get_attachments_ids( $size_name = NULL ) {
		$args = array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_mime_type' => array( 'image/jpeg', 'image/gif', 'image/png' ),
			'fields'         => 'ids'
		);

		if ( !is_null( $size_name ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => '_wp_attachment_metadata',
					'value'   => '"' . $size_name . '"',
					'compare' => 'LIKE'
				)
			);
		}

		$args = apply_filters( 'ias_attachments_args', $args );

		return get_posts( $args );
	}

	/**
	 * Remove a file
	 *
     * @param string $file_path Server file path
	 * @return array
	 */
	private function remove_file( $file_path ) {
		$url_path = IAS_Helpers::url_wrapper( IAS_Helpers::get_url_path( $file_path ) );
		if ( !file_exists( $file_path ) )
			return IAS_Helpers::get_result_array( false, sprintf( __( 'File does not exist and can not be removed: %s', 'images-advanced-settings' ), $url_path ) );

		wp_delete_file( $file_path );

		if ( !file_exists( $file_path ) )
			return IAS_Helpers::get_result_array( true, sprintf( __( 'File has been removed: %s', 'images-advanced-settings' ), $url_path ) );
		else
			return IAS_Helpers::get_result_array( false, sprintf( __( 'File can not be removed due to unknown error: %s', 'images-advanced-settings' ), $url_path ) );
	}

	/**
	 * Get attachment saved sizes
	 *
     * @param int $attachment_id Attachment ID
	 * @return array
	 */
	private function get_attachment_sizes( $attachment_id ) {
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );
		return $attachment_metadata['sizes'];
	}

	/**
	 * Find all attachment versions and remove its
	 *
     * @param int $attachment_id Attachment ID
	 * @return array
	 */
	private function remove_files( $attachment_id ) {
		$file_info = pathinfo( get_attached_file( $attachment_id ) );
		$dir = opendir( $file_info['dirname'] );
		$results = array();

		if ( $dir === false )
			return IAS_Helpers::get_result_array( false, sprintf( __( 'Directory "%s" can not be opened', 'images-advanced-settings' ), $file_info['dirname'] ) );

		while ( ( $file = readdir( $dir ) ) !== false ) {
			if ( strrpos( $file, $file_info['filename'] ) !== false )
				$files[] = $file;
		}

		closedir( $dir );

		if ( empty( $files ) )
			return IAS_Helpers::get_result_array( false, sprintf( __( 'There is no file found for "%s"', 'images-advanced-settings' ), $file_info['filename'] ) );

		foreach ( $files as $file_name ) {
			$file_path = $file_info['dirname'] . DIRECTORY_SEPARATOR . $file_name;
			$file_dimensions = explode( $file_info['dirname'] . DIRECTORY_SEPARATOR . $file_info['filename'], $file_path );
	
			if ( count( explode( 'x', $file_dimensions[1] ) ) === 2 )
				$results[] = $this->remove_file( $file_path );
		}

		return $results;
	}

	/**
	 * Find size version of an attachment and remove it
	 *
     * @param int $attachment_id Attachment ID
	 * @param string $size_name Size to remove
	 * @return array
	 */
	private function remove_attachment_size_file( $attachment_id, $size_name ) {
		$attachment_sizes = $this->get_attachment_sizes( $attachment_id );

		if ( !isset( $attachment_sizes[ $size_name ] ) )
			return IAS_Helpers::get_result_array( false, sprintf( __( 'Size %s was not found for this file', 'images-advanced-settings' ), $size_name ) );

		$dirname = dirname( get_attached_file( $attachment_id ) );
		$file_path = $dirname . DIRECTORY_SEPARATOR . $attachment_sizes[ $size_name ]['file'];

		return $this->remove_file( $file_path );
	}

	/**
	 * Get generated log HTML from removal results
	 *
     * @param array $result 
	 * @return string
	 */
	private function get_log( $result ) {
		ob_start();
		include IAS_Helpers::get_admin_view('ias-admin-part-log');
		return ob_get_clean();
	}

	/**
	 * Check if an ID is set and numeric
	 *
     * @param int $attachment_id Attachment ID
	 * @return void
	 */
	private function check_attachment_id( $attachment_id ) {
		if ( !isset( $attachment_id ) || empty( $attachment_id ) )
			wp_send_json_error( __( 'Attachment ID is missing', 'images-advanced-settings' ) );

		if ( !is_numeric( $attachment_id ) )
			wp_send_json_error( sprintf( __( 'Attachment ID "%d" is incorrect', 'images-advanced-settings' ), $attachment_id ) );
	}

	/**
	 * (AJAX) Remove size version of a file
	 *
	 * @return void
	 */
	function remove_size_file() {
		check_admin_referer( 'images-advanced-settings', 'nonce' );

		set_time_limit(0);

		$this->check_attachment_id( $_POST['attachment_id'] );

		$attachment_id = $_POST['attachment_id'];
		$image_path = get_attached_file( $attachment_id );

		if ( $image_path === '' )
			wp_send_json_error( sprintf( __( 'File not found for ID "%d"', 'images-advanced-settings' ), $attachment_id ) );

		$file_info = pathinfo( $image_path );
		$result = array(
			'path' => $image_path,
			'id' => $attachment_id,
			'name' => $file_info['filename']
		);

		$result['results'][] = $this->remove_attachment_size_file( $attachment_id, $_POST['size_name'] );

		wp_send_json_success( $this->get_log( $result ) );
	}

	/**
	 * (AJAX) Get all attachments ids
	 *
	 * @return void
	 */
	function get_all_attachments() {
		check_admin_referer( 'images-advanced-settings', 'nonce' );

		$attachments_ids = $this->get_attachments_ids();

		if ( empty( $attachments_ids ) )
			wp_send_json_error( __( 'There is no attachment', 'images-advanced-settings' ) );

		wp_send_json_success( $attachments_ids );
	}

	/**
	 * Create attachment files from configured sizes
	 *
     * @param int $attachment_id Attachment ID
	 * @param string $image_path Attachment server path
	 * @param string $dirname Attachment upload directory server path
	 * @return array
	 */
	private function regenerate( $attachment_id, $image_path, $dirname ) {
		$url_path = IAS_Helpers::get_url_path( $dirname );
		$meta = wp_generate_attachment_metadata( $attachment_id, $image_path );
		$result = array();

		if ( !empty( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size => $size_info ) {
				$size_url_path = $url_path . DIRECTORY_SEPARATOR . $size_info['file'];
				$result[] = IAS_Helpers::get_result_array( true, sprintf( __( '%s size has been generated: %s', 'images-advanced-settings' ), $size, IAS_Helpers::url_wrapper( $size_url_path ) ) );
			}
		} else {
			$result[] = IAS_Helpers::get_result_array( true, sprintf( __( 'There is no more regeneration to do with this attachment', 'images-advanced-settings' ) ) );
		}

		wp_update_attachment_metadata( $attachment_id, $meta );

		return $result;
	}

	/**
	 * (AJAX) Remove sizes versions of a file and regenerate it
	 *
	 * @return void
	 */
	function regenerate_attachment() {
		check_admin_referer( 'images-advanced-settings', 'nonce' );

		set_time_limit(0);

		$this->check_attachment_id( $_POST['attachment_id'] );
		
		$attachment_id = $_POST['attachment_id'];
		$image_path = get_attached_file( $attachment_id );

		if ( $image_path === '' )
			wp_send_json_error( sprintf( __( 'File not found for ID "%d"', 'images-advanced-settings' ), $attachment_id ) );

		$file_info = pathinfo( $image_path );
		$result = array(
			'path' => $image_path,
			'id'   => $attachment_id,
			'name' => $file_info['filename']
		);

		$result['results'] = $this->remove_files( $attachment_id );
		$result['results'] = array_merge( $result['results'], $this->regenerate( $attachment_id, $image_path, $file_info['dirname'] ) );

		wp_send_json_success( $this->get_log( $result ) );
	}
}

endif;