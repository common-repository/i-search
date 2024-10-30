<?php

/**
 * i-Search global all4wp menu builder class
 *
 * This file is loaded only in admin.
 * The main class file for the all4wp menu.
 *
 *
 *
 *
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      1.0.0
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'all4wp_global' ) ) {

	class all4wp_global {

		public $global_menu_url;
		public $global_menu_path;

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			if ( ! defined( 'ALL4WP_MENU_SLUG' ) ) {
				add_action( 'admin_menu', array( $this, 'register_all4wp_menu' ) );
				define( 'ALL4WP_MENU_SLUG', 'all4wp_glob' );

				add_action( 'admin_enqueue_scripts', array( $this, 'register_script_style' ) );

				$this->global_menu_path = dirname( __FILE__ );
				$this->global_menu_url  = trailingslashit( plugin_dir_url( dirname( __FILE__ ) ) ) . 'all4wp-global';

			}

		}

		/**
		 * Register the all4wp menu in WP.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_all4wp_menu() {

			$page_title = 'all4wp';
			$menu_title = 'all4wp Plugins';
			$capability = ALL4WP_CAPABILITIES;
			$menu_slug  = 'all4wp_glob';
			$icon_url   = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNzUuNjcwOG1tIiBoZWlnaHQ9Ijc1LjY3MDhtbSIgc3R5bGU9InNoYXBlLXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IHRleHQtcmVuZGVyaW5nOmdlb21ldHJpY1ByZWNpc2lvbjsgaW1hZ2UtcmVuZGVyaW5nOm9wdGltaXplUXVhbGl0eTsgZmlsbC1ydWxlOmV2ZW5vZGQ7IGNsaXAtcnVsZTpldmVub2RkIg0Kdmlld0JveD0iMCAwIDgzLjg1NSA4My44NTUiDQogeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPg0KIDxkZWZzPg0KICA8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KICAgPCFbQ0RBVEFbDQogICAgLnN0cjAge3N0cm9rZTojMDA3RkM0O3N0cm9rZS13aWR0aDowLjIyMTYzMX0NCiAgICAuZmlsMCB7ZmlsbDojMDA3RkM0fQ0KICAgIC5maWwxIHtmaWxsOndoaXRlfQ0KICAgXV0+DQogIDwvc3R5bGU+DQogPC9kZWZzPg0KIDxnIGlkPSJMYXllcl94MDAyMF8xIj4NCiAgPG1ldGFkYXRhIGlkPSJDb3JlbENvcnBJRF8wQ29yZWwtTGF5ZXIiLz4NCiAgPGNpcmNsZSBjbGFzcz0iZmlsMCIgY3g9IjQxLjkyNzUiIGN5PSI0MS45Mjc1IiByPSI0MS45Mjc1Ii8+DQogIDxwb2x5Z29uIGNsYXNzPSJmaWwxIHN0cjAiIHBvaW50cz0iNDYuOTYyOCw1LjIyOTgzIDYuMzU0NzEsNDEuOTI3NSAzMi40NDk0LDQxLjkyNzUgNDEuMDk4OCwzMy4zNzcgIi8+DQogIDxwb2x5Z29uIGNsYXNzPSJmaWwxIHN0cjAiIHBvaW50cz0iNDYuOTYyOCw1LjIyOTgzIDYyLjIwOTIsMTQuOTA1NSA1Ny4yMjQ4LDM5LjgyNzMgMzkuOTI2LDM5LjgyNzMgIi8+DQogIDxwb2x5Z29uIGNsYXNzPSJmaWwxIHN0cjAiIHBvaW50cz0iNi4zNTQ3MSw0MS45Mjc1IDcxLjAwNTIsNDEuOTI3NSA2OC4zNjY0LDU4Ljg4NTQgMTMuOTc3OSw1OC44ODU0ICIvPg0KICA8cG9seWdvbiBjbGFzcz0iZmlsMSBzdHIwIiBwb2ludHM9IjM2Ljk2MTUsNjAuNzc0IDUzLjg4MDgsNjAuNzc0IDUwLjcwMzgsNzcuNzc5NiAzMy43ODQ1LDc3Ljc3OTYgIi8+DQogPC9nPg0KPC9zdmc+DQo=";
			$position   = 99;

			add_menu_page( $page_title, $menu_title, $capability, $menu_slug, array(
				$this,
				'create_all4wp_menu'
			), $icon_url, $position );

		}

		/**
		 * Render the all4wp menu.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function create_all4wp_menu() {

			wp_enqueue_style( 'allwp-global-css' );
			ob_start();
			require_once $this->global_menu_path . '/html/global_menu_html.php';
			$content = ob_get_clean();
			echo $content;
		}

		/**
		 * Register scripts and styles.
		 *
		 * @since  2.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_script_style() {

			wp_register_style( 'allwp-global-css', $this->global_menu_url . '/css/all4wp-global.css' );

		}

	}

	$all_4_wp_global = new all4wp_global();

}