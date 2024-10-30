<?php
/*
 * Include files ONLY for admin usage.
 *
 *
 *
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once ISRC_PLUGIN_DIR . '/admin/admin-functions.php';

/**
 * Set globals only for admin
 */

$isrc_opt_adv       = get_option( 'isrc_opt_adv_' . isrc_get_lang_admin() );
$isrc_opt           = get_option( 'isrc_opt_' . isrc_get_lang_admin() );
$isrc_log_bad_words = get_option( 'isrc_log_bad_words_' . isrc_get_lang_admin() );
$hash               = get_option( 'isrc_hash' );

/**
 * Include files only for admin area.
 * Exclude files if doing ajax to save server capacities.
 */
if ( ! wp_doing_ajax() ) {
	require_once ISRC_PLUGIN_DIR . '/all4wp-global/global-menu.php';
}

require_once ISRC_PLUGIN_DIR . '/admin/menu/class-wp-list-table-logs.php';
require_once ISRC_PLUGIN_DIR . '/admin/menu/class-wp-list-table-shortcodes.php';
require_once ISRC_PLUGIN_DIR . '/admin/class-admin-main.php';
require_once ISRC_PLUGIN_DIR . '/admin/menu/class-menu.php';

if ( ! wp_doing_ajax() ) {
	require_once ISRC_PLUGIN_DIR . '/admin/metabox/class-post-meta-box.php';
	require_once ISRC_PLUGIN_DIR . '/admin/menu/class-dashboard.php';
}

/*
* Include Ajax class for admin.
*/
require_once ISRC_PLUGIN_DIR . '/class-ajax.php';
