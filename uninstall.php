<?php
if ( 
    !defined( 'WP_UNINSTALL_PLUGIN' ) ||
    !is_admin() ||
    $_REQUEST['plugin'] !== 'images-advanced-settings/images-advanced-settings.php'
) exit;

$options = array(
    'images_advanced_settings'
);

if ( is_multisite() ) {
	global $wpdb;
	$network_blog_id = get_current_blog_id();
	$blogs = get_sites();

	foreach ( $blogs as $blog ) {
		switch_to_blog( $blog->blog_id );

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	switch_to_blog( $network_blog_id );
} else {
	foreach ( $options as $option ) {
		delete_option( $option );
	}
}
