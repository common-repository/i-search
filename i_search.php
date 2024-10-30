<?php
/**
 * Plugin Name: i-Search - Advanced Live Search
 * Plugin URI: https://i-search.all4wp.net
 * Description: Powerful search engine for your WP.
 * Version: 1.2.0
 * Author: All4Wp
 * Author URI: https://all4wp.net
 *
 * Text Domain: i_search
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/* <----- Define Constants -----> */

define( 'ISRC_PLUGIN_SLUG', 'i-search' );
define( 'ISRC_PLUGIN_FILE', __FILE__ );
define( 'ISRC_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'ISRC_PLUGIN_URL', plugins_url( ISRC_PLUGIN_SLUG ) );
define( 'ISRC_GLOBAL_MENU_URL', plugins_url( ISRC_PLUGIN_SLUG ) );
define( 'ISRC_CONNECT_URL', ISRC_PLUGIN_URL . '/i-search-connect/connect.php' );
define( 'ISRC_VER', '1.2.0' );
define( 'ISRC_SCRIPT_VER', '1.2.0' );
define( 'I_SRC_DB_VER', '1.0.1' );

if ( ! defined( 'ALL4WP_CAPABILITIES' ) ) {
	define( 'ALL4WP_CAPABILITIES', 'manage_options' );
}

if ( ! defined( 'ISRC_CAPABILITIES' ) ) {
	define( 'ISRC_CAPABILITIES', 'manage_options' );
}

if ( ! defined( 'ISRC_SECURITY_CAPABILITIES' ) ) {
	define( 'ISRC_SECURITY_CAPABILITIES', 'manage_options' );
}


/*
* Load Translations
*/

function i_Search_load_plugin_textdomain() {
	load_plugin_textdomain( 'i_search', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'i_Search_load_plugin_textdomain' );

/*
* Check installed plugins
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	define( 'ISRC_WOOCOMMERCE_INSTALLED', true );
}

if ( is_plugin_active( 'i_search_pro/i_search_pro.php' ) ) {
	define( 'ISRC_PRO_INSTALLED', true );
}

if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
	define( 'ISRC_WPML_INSTALLED', true );
}

/*
* Include mobile detect class in booth. Admin and Front
*/
require_once ISRC_PLUGIN_DIR . '/front/class-mobile-detect.php';

/*
* Include widget class in booth. Admin and Front
*/
require_once ISRC_PLUGIN_DIR . '/widget/class-widget.php';

/*
* Register widget class in booth. Admin and Front
*/
function register_isrc_widget() {
	register_widget( 'i_search_widged' );
}

add_action( 'widgets_init', 'register_isrc_widget' );

/*
* Include admin files only if we are in admin or the request is coming from the admin section.
*/
if ( is_admin() ) {
	require_once ISRC_PLUGIN_DIR . '/admin/admin-includes.php';
} else {
	/*
	* Include front files only if we are not in admin or the request is not from the admin section.
	*/
	require_once ISRC_PLUGIN_DIR . '/front/front-includes.php';
}

if ( is_admin() || isset( $_GET['vc_editable'] ) ) {
	// Before VC Init
	add_action( 'vc_before_init', 'vc_before_init_actions' );
}

function vc_before_init_actions() {

	// Require new custom Element
	require_once( ISRC_PLUGIN_DIR . '/vc-elements/class-wp-bakery.php' );

}