<?php
/*
* This file is executed on plugin DELETE.
*
* https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
*
* @since 2.0.0
*
*/
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/* is delete data selected from admin settings? */
$delete_data = get_option( 'isrc_delete_data', false );

if ( $delete_data ) {
	global $wpdb;

	/* delete options */
	$results = $wpdb->get_results( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE '%isrc_%'", ARRAY_A );
	if ( ! empty( $results ) && is_array( $results ) ) {
		foreach ( $results as $result ) {
			$option_name = $result['option_name'];
			delete_option( $option_name );
			delete_site_option( $option_name );
		}
	}

	/* delete all posts meta keys */
	delete_post_meta_by_key( '_isrc' );
	delete_post_meta_by_key( '_isrc_all' );

	/* Drop the isrc database */
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_taxonomy" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_temp" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_logs" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_popular" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_metadata" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}isearch_shortcodes" );
}