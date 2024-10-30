<?php

if( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'IAS_Public' ) ) :

class IAS_Public {

	private $lazy_loading;

	function __construct() {
		$this->load_dependencies();
		$this->init_data();

		if ( !$this->lazy_loading ) return;

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_attributes' ) );
		add_filter( 'the_content', array( $this, 'add_content_lazy_attributes' ) );
	}

	/**
	 * Include necessary classes
	 *
	 * @return void
	 */
	private function load_dependencies() {
		include_once dirname( plugin_dir_path( __FILE__ ) ) . '/includes/class-ias-helpers.php';
	}

	private function init_data() {
		$data = IAS_Helpers::get_option();
		$this->lazy_loading = $data['lazy_loading'];
	}

	private function get_image_attributes() {
		return array(
			'src',
			'srcset',
			'sizes'
		);
	}

	function add_lazy_attributes( $attr ) {
		$attr['class'] .= ' lazy';

		foreach ( $this->get_image_attributes() as $img_attr ) {
			if ( isset( $attr[ $img_attr ] ) ) {
				$attr[ 'data-' . $img_attr ] = $attr[ $img_attr ];
				unset( $attr[ $img_attr ] );
			}
		}

		return $attr;
	}

	function add_content_lazy_attributes( $content ) {
		if ( empty( $content ) )
			return $content;

		$content  = mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' );
		$document = new DOMDocument();
		libxml_use_internal_errors( true );
		$document->loadHTML( utf8_decode( $content ) );
		$images = $document->getElementsByTagName('img');

		foreach( $images as $image ) {
			$class = $image->getAttribute( 'class' ) . ' lazy';
			$image->setAttribute( 'class', $class );

			foreach ( $this->get_image_attributes() as $img_attr ) {
				if ( $image->hasAttribute( $img_attr ) ) {
					$image->setAttribute( 'data-' . $img_attr, $image->getAttribute( $img_attr ) );
					$image->removeAttribute( $img_attr );
				}
			}
		}

		return $document->saveHTML();
	}

	function scripts() {
		wp_enqueue_script( 'lazyload', plugins_url( 'js/vendor/lazyload.min.js', __FILE__ ), array(), Images_Advanced_Settings::$version, true );
		wp_enqueue_script( 'ias-public', plugins_url( 'js/ias-public.js', __FILE__ ), array(), Images_Advanced_Settings::$version, true );
	}
}

new IAS_Public();

endif;

