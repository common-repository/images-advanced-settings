<?php

if( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'IAS_Admin' ) ) :

class IAS_Admin {

	private $hook;
	private $ias_sizes;
	private $ias_attachments;
	private $ias_optimizations;
	private $data;

	function __construct() {
		$this->load_dependencies();

		add_action( 'admin_menu', array( $this, 'option_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );

		add_filter( 'plugin_action_links_images-advanced-settings/images-advanced-settings.php', array( $this, 'action_links' ) );

		// Sizes
		add_action( 'wp_ajax_ias_disable_default', array( $this->ias_sizes, 'disable_default' ) );
		add_action( 'wp_ajax_ias_add_size', array( $this->ias_sizes, 'add_size' ) );
		add_action( 'wp_ajax_ias_update_sizes', array( $this->ias_sizes, 'update_sizes' ) );
		add_action( 'wp_ajax_ias_remove_size', array( $this->ias_sizes, 'remove_size' ) );
		
		add_filter( 'intermediate_image_sizes_advanced', array( $this->ias_sizes, 'disable_sizes_generation' ) );
		
		// Attachments
		add_action( 'wp_ajax_ias_remove_size_file', array( $this->ias_attachments, 'remove_size_file' ) );
		add_action( 'wp_ajax_ias_regenerate_attachment', array( $this->ias_attachments, 'regenerate_attachment' ) );
		add_action( 'wp_ajax_ias_get_all_attachments', array( $this->ias_attachments, 'get_all_attachments' ) );

		// Optimizations
		add_action( 'wp_ajax_ias_toggle_lazy_loading', array( $this->ias_optimizations, 'toggle_lazy_loading' ) );
	}

	/**
	 * Include necessary classes
	 *
	 * @return void
	 */
	private function load_dependencies() {
		include_once dirname( plugin_dir_path( __FILE__ ) ) . '/includes/class-ias-helpers.php';

		include_once 'includes/class-ias-admin-sizes.php';
		$this->ias_sizes = new IAS_Admin_Sizes();

		include_once 'includes/class-ias-admin-attachments.php';
		$this->ias_attachments = new IAS_Admin_Attachments();

		include_once 'includes/class-ias-admin-optimizations.php';
		$this->ias_optimizations = new IAS_Admin_Optimizations();
	}

	/**
	 * Get all AJAX actions
	 *
	 * @return array
	 */
	private function get_actions() {
		return array(
			'default'           => 'ias_disable_default',
			'add'               => 'ias_add_size',
			'update'            => 'ias_update_sizes',
			'remove'            => 'ias_remove_size',
			'regenerate'        => 'ias_regenerate_attachment',
			'getAllAttachments' => 'ias_get_all_attachments',
			'removeSizeFile'    => 'ias_remove_size_file',
			'lazy'              => 'ias_toggle_lazy_loading'
		);
	}

	/**
	 * Get JS translated messages
	 *
	 * @return array
	 */
	private function get_messages() {
		return apply_filters( 'ias_messages',
			array(
				'ajaxFailure' => array(
					'server'     => __( 'Server error, please retry', 'images-advanced-settings' ),
					'connection' => __( 'Connection error, please retry', 'images-advanced-settings' )
				)
			)
		);
	}

	/**
	 * Get plugin sections for tabs
	 *
	 * @return array
	 */
	private function get_view_sections() {
		return array(
			'sizes'        => __( 'Sizes', 'images-advanced-settings' ),
			'regeneration' => __( 'Regeneration', 'images-advanced-settings' ),
			'optimization' => __( 'Optimization', 'images-advanced-settings' )
		);
	}

	/**
	 * Create plugin option page
	 *
	 * @return void
	 */
	function option_page() {
		$this->hook = add_options_page(
			__( 'Images', 'images-advanced-settings' ),
			__( 'Images (advanced)', 'images-advanced-settings' ),
			'manage_options',
			'image-settings',
			array( $this, 'page_display' )
		);
	}

	/**
	 * Display plugin option page
	 *
	 * @return void
	 */
	function page_display() {
		if ( !current_user_can( 'manage_options' ) ) return;

		$crop_positions = $this->ias_sizes->get_crop_positions();
		$sections = $this->get_view_sections();
		$default_sizes = array_diff( get_intermediate_image_sizes(), $this->ias_sizes->get_custom_sizes_names() );
		$this->data = $this->ias_sizes->data;

		include IAS_Helpers::get_admin_view('ias-admin-page');
	}

	/**
	 * Add settings link to extension
	 * 
	 * @param array $links Extension menu links
	 * @return array
	 */
	function action_links( $links ) {
		$link = '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=image-settings' ) ) . '">' . __( 'Settings', 'images-advanced-settings' ) . '</a>';
		array_unshift( $links, $link );

		return $links;
	}

	function scripts( $hook ) {
		if ( $this->hook !== $hook ) return;

		wp_enqueue_script( 'ias-admin', plugins_url( 'js/ias-admin.js', __FILE__ ), array(), Images_Advanced_Settings::$version, true );
		wp_localize_script( 'ias-admin', 'IAS',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'actions'  => $this->get_actions(),
				'nonce'    => wp_create_nonce( 'images-advanced-settings' ),
				'messages' => $this->get_messages(),
			)
		);
	}

	function styles( $hook ) {
		if ( $this->hook !== $hook ) return;

		wp_enqueue_style( 'ias-admin', plugins_url( 'css/ias-admin.css', __FILE__ ), array(), Images_Advanced_Settings::$version );
	}
}

new IAS_Admin();

endif;

