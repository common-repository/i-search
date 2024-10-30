<?php
/**
 * i-Search Dashboard widget class
 *
 * This file is loaded only in the dashboard screen in the admin area
 *
 *
 *
 *
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class isrc_dashboard_widget {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/**
		 * is logging enabled? we show only logging data in widget.
		 */
			add_action( 'admin_enqueue_scripts', array( $this, 'register_script_style' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );

	}

	/**
	 * Register the dashboard widget in WP
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function register_dashboard_widget() {

		if ( ! i_src_security_check() ) {
			return false;
		}

		$widget_id        = 'isrc_dahsboard_w';
		$widget_name      = '<img style="vertical-align:middle;margin-right:10px"  src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8IS0tIENyZWF0b3I6IENvcmVsRFJBVyAtLT4NCjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWw6c3BhY2U9InByZXNlcnZlIiB3aWR0aD0iNC4zNjY4bW0iIGhlaWdodD0iNS4yOTE3bW0iIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCINCnZpZXdCb3g9IjAgMCA0LjM2NjggNS4yOTE3Ig0KIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4NCiA8ZGVmcz4NCiAgPHN0eWxlIHR5cGU9InRleHQvY3NzIj4NCiAgIDwhW0NEQVRBWw0KICAgIC5maWwxIHtmaWxsOiMwMDIzMzd9DQogICAgLmZpbDMge2ZpbGw6IzAwQURFRn0NCiAgICAuZmlsMCB7ZmlsbDojRURFREVEfQ0KICAgIC5maWwyIHtmaWxsOiNFRjMyNzR9DQogICAgLmZpbDQge2ZpbGw6I0ZFRUQwMX0NCiAgIF1dPg0KICA8L3N0eWxlPg0KIDwvZGVmcz4NCiA8ZyBpZD0iTGF5ZXJfeDAwMjBfMSI+DQogIDxtZXRhZGF0YSBpZD0iQ29yZWxDb3JwSURfMENvcmVsLUxheWVyIi8+DQogIDxnIGlkPSJfNDA3MjQ3MTI4Ij4NCiAgIDxyZWN0IGlkPSJfNDA0MTcwNTc2IiBjbGFzcz0iZmlsMCIgeD0iMC40NzE0MzYiIHk9IjUuMDg5NjUiIHdpZHRoPSIzLjQyMzkzIiBoZWlnaHQ9IjAuMjAyMDcxIiByeD0iMC4xMDEiIHJ5PSIwLjEwMSIvPg0KICAgPGc+DQogICAgPHBhdGggaWQ9Il80MDQ3MDAwNzIiIGNsYXNzPSJmaWwxIiBkPSJNMi4xODM0IDBjMS4yMDU5LDAgMi4xODM1LDAuOTc1MSAyLjE4MzUsMi4xNzc5IDAsMC42NDYgLTAuMjgyMSwxLjIyNjIgLTAuNzMsMS42MjVsMC4wMDAzIDAgLTEuNDUzOCAxLjQwMTEgLTEuNDUzNyAtMS40MDExIDAuMDAwMiAwYy0wLjQ0NzksLTAuMzk4OCAtMC43Mjk5LC0wLjk3OSAtMC43Mjk5LC0xLjYyNSAwLC0xLjIwMjggMC45Nzc1LC0yLjE3NzkgMi4xODM0LC0yLjE3Nzl6Ii8+DQogICAgPGc+DQogICAgIDxnPg0KICAgICAgPHJlY3QgaWQ9Il80MDQ2OTk5NTIiIGNsYXNzPSJmaWwyIiB4PSIwLjgwMjYwNSIgeT0iMS45NzMwNCIgd2lkdGg9IjAuNzgwMjA1IiBoZWlnaHQ9IjEuMzMwMyIgcng9IjAuMzkwMSIgcnk9IjAuMzkwMSIvPg0KICAgICAgPHJlY3QgaWQ9Il80MDcxNzg1MzYiIGNsYXNzPSJmaWwyIiB4PSIwLjgwMjYwNSIgeT0iMS4wNjkzMyIgd2lkdGg9IjAuNzgwMjA1IiBoZWlnaHQ9IjAuNzY4OTk0IiByeD0iMC4zODQ1IiByeT0iMC4zODQ1Ii8+DQogICAgIDwvZz4NCiAgICAgPGc+DQogICAgICA8cmVjdCBpZD0iXzQwNjM4ODA1NiIgY2xhc3M9ImZpbDMiIHg9IjIuNzgzOTkiIHk9IjEuOTczMDQiIHdpZHRoPSIwLjc4MDIwNSIgaGVpZ2h0PSIxLjMzMDMiIHJ4PSIwLjM5MDEiIHJ5PSIwLjM5MDEiLz4NCiAgICAgIDxyZWN0IGlkPSJfNDA2Mzg3Njk2IiBjbGFzcz0iZmlsMyIgeD0iMi43ODM5OSIgeT0iMS4wNjkzMyIgd2lkdGg9IjAuNzgwMjA1IiBoZWlnaHQ9IjAuNzY4OTk0IiByeD0iMC4zODQ1IiByeT0iMC4zODQ1Ii8+DQogICAgIDwvZz4NCiAgICAgPGc+DQogICAgICA8cmVjdCBpZD0iXzQwNzM1MzU4NCIgY2xhc3M9ImZpbDQiIHg9IjEuNzkzMyIgeT0iMS42MDgxOCIgd2lkdGg9IjAuNzgwMjA1IiBoZWlnaHQ9IjIuMDYwMDEiIHJ4PSIwLjM5MDEiIHJ5PSIwLjM5MDEiLz4NCiAgICAgIDxyZWN0IGlkPSJfNDA3MzUzMjAwIiBjbGFzcz0iZmlsNCIgeD0iMS43OTMzIiB5PSIwLjcxMDA4NyIgd2lkdGg9IjAuNzgwMjA1IiBoZWlnaHQ9IjAuNzY4OTk0IiByeD0iMC4zODQ1IiByeT0iMC4zODQ1Ii8+DQogICAgIDwvZz4NCiAgICA8L2c+DQogICA8L2c+DQogIDwvZz4NCiA8L2c+DQo8L3N2Zz4NCg==">i-Search';
		$callback         = array( $this, 'create_dashboard_widget' );
		$control_callback = '';
		$callback_args    = '';

		wp_add_dashboard_widget( $widget_id, $widget_name, $callback, $control_callback, $callback_args );

		return true;
	}

	/**
	 * Build the HTML widged in dashboard.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function create_dashboard_widget() {
		$latest_logs = $this->get_latest_logs();
		$menu_url     = html_entity_decode( menu_page_url( 'isrc-opt-page', false ) );
		$analyze_url  = html_entity_decode( add_query_arg( 'tab', 'analyze', $menu_url ) );

		if ( ! empty( $latest_logs ) ) {
			?>

            <h3><?php _e( 'Last 10 not found search queries', 'i_search' ); ?></h3>
            <ul>
				<?php
				foreach ( $latest_logs as $log ) {
					$title     = $log['src_query'];
					$time      = $log['time'];
					$timestamp = strtotime( $time );
					$ago       = sprintf( _x( '%s ago', '%s = human-readable time difference', 'i_search' ), human_time_diff( $timestamp, current_time( 'timestamp' ) ) );
					echo "<li>{$title}<span class='isrc_ago'>{$ago}</span></li>";
				}
				?>
            </ul>
			<?php
		}

		/* get popular searches */
		$popular_searches = isrc_get_popular_searches( 10 );

		if ( $popular_searches ) {
			?>
            <h3><?php _e( "Today's popular searches", 'i_search' ); ?></h3>
            <ul>
				<?php
				foreach ( $popular_searches as $log ) {
					$title = $log['title'];
					echo "<li>{$title}</li>";
				}
				?>
            </ul>
            <div><a href="<?php echo $analyze_url; ?>">Go to i-Search</a></div>
			<?php
		}
	}

	/**
	 * Get the last not found queries
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_latest_logs() {
		global $wpdb;

		$latest_logs = $wpdb->get_results( "SELECT id, src_query, time FROM {$wpdb->prefix}isearch_logs WHERE status = 0 ORDER BY id DESC LIMIT 10", ARRAY_A );

		return $latest_logs;

	}


	/**
	 * Register scripts and styles only in dashboard
	 *
	 * @return void
	 */
	public function register_script_style() {

		$screen = get_current_screen();

		if ( ! empty( $screen ) && $screen->base == 'dashboard' ) {
			wp_register_style( 'isrc-admin_dashboard', ISRC_PLUGIN_URL . '/admin/menu/css/isrc-dashboard.css' );
			wp_enqueue_style( 'isrc-admin_dashboard' );
		}

	}

}

$isrc_dashboard_widget = new isrc_dashboard_widget();

