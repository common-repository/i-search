<?php

/**
 * i-Search list table class
 *
 * This file is loaded only in admin.
 * The main class file to build logs table in the admin analysis TAB.
 * WP codex says: you should make a copy to use and distribute with your own project.
 * See: https://codex.wordpress.org/Class_Reference/WP_List_Table
 *
 * All the functions here extending the original wp list table functions.
 * For detailed information about the functions please take a look into the original table functions. They are documented.
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

/**
 * include the copy of the original WP list class .
 * See: https://codex.wordpress.org/Class_Reference/WP_List_Table
 */
if ( ! class_exists( 'isrc_WP_List_Table' ) ) {
	require_once( ISRC_PLUGIN_DIR . '/admin/menu/class-wp-list-table-org.php' );
}

class isrcLog_List extends isrc_WP_List_Table {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Search log', 'i_search' ), //singular name of the listed records
			'plural'   => __( 'Search logs', 'i_search' ), //plural name of the listed records
			'ajax'     => true,
			'screen'   => 'isrc-opt-page'
		) );

	}

	/**
	 * Add views (tabs) to the table.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_views() {
		global $wpdb;

		$lang = isrc_get_lang_admin();

		$all_count      = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$lang}'" );
		$dym_count      = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_logs WHERE status = '2' AND lang = '{$lang}'" );
		$populars_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_popular WHERE lang = '{$lang}'" );

		if ( isset( $_GET['current_url'] ) ) {
			$current_url = esc_url_raw( $_GET['current_url'] );
		} else {
			$current_url = esc_url_raw( $_SERVER['REQUEST_URI'] );
		}

		$all_url     = add_query_arg( array( 'status' => absint( 0 ) ), $current_url );
		$dym_url     = add_query_arg( array( 'status' => absint( 2 ) ), $current_url );
		$popular_url = add_query_arg( array( 'status' => absint( 3 ) ), $current_url );

		$status_links = array(
			"all"     => "<a class='{$this->get_views_css( 0, 'current' )}' href='{$all_url}' data-tabtab='all'>" . __( 'Not found search queries', 'i_search' ) . " ({$all_count})</a>",
			"dym"     => "<a class='{$this->get_views_css( 2, 'current' )}' href='{$dym_url}' data-tabtab='dym'>" . __( 'Did you mean strings', 'i_search' ) . " ({$dym_count})</a>",
			"popular" => "<a class='{$this->get_views_css( 3, 'current' )}' href='{$popular_url}' data-tabtab='popular'>" . __( 'Popularity index', 'i_search' ) . " ({$populars_count})</a>",
		);

		return $status_links;
	}

	/**
	 * Add a class name to the active view (tab).
	 *
	 * @param int    $key the current views key.
	 * @param string $css the name of the css class to add.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_views_css( $key, $css ) {

		$status = ( isset( $_GET['status'] ) ) ? $_GET['status'] : 0;
		$status = (int) $status;
		$key    = (int) $key;

		if ( $status === $key ) {
			return $css;
		}

		return '';
	}

	/**
	 * Text to display when no logs are available.
	 * echo directly the text.
	 *
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function no_items() {
		_e( 'No logs avaliable.', 'i_search' );
	}

	/**
	 * Catch the default columns for the table.
	 *
	 * @param array  $item        Current DB row items.
	 * @param string $column_name DB row item name.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'keyword':
			case 'src_query':
			case 'status':
			case 'meaning':
			case 'length':
			case 'count':
			case 'last_ip':
			case 'time':
				return $item[ $column_name ];
			default:
				return '';
		}
	}

	/**
	 * Render the bulk edit checkbox.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
	 * Render the last_ip field.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_last_ip( $item ) {

		$ip = $item['last_ip'];

		$ip_items = explode( '.', $ip );

		$filtered_ip = array(); //The var to store the filtered ip
		$i           = 0;
		foreach ( $ip_items as $item ) {
			if ( $i == 0 || $i == 1 ) { //check if its the last part of the IP
				$ip_part = '***';
			} else {
				$ip_part = $item;
			}

			$filtered_ip[] = $ip_part;
			$i ++;
		}

		return implode( '.', $filtered_ip );
	}

	/**
	 * Render the flow section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_flow( $item ) {

		$flows = $item['flow'];

		if ( empty( $flows ) ) {
			return $flows;
		}

		$flows = isrc_maybe_explode( $flows );

		$return = array();
		foreach ( $flows as $flow ) {
			$return[] = '<li>' . $flow . '</li>';
		}

		$flows = '<div class="isrc_flow"><ul>' . isrc_implode( $return, '' ) . '</ul></div>';

		return $flows;
	}

	/**
	 * Render the edit button.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_edit_btn( $item ) {
		return sprintf(
			'<button data-id="%s" name="edit_sinlge_log" class="edit_meaning button-secondary w100p" value="%s">%s</button>', $item['id'], __( 'Edit', 'i_search' ), __( 'Edit', 'i_search' )
		);
	}

	/**
	 * Render the searched string table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_src_query( $item ) {

		$title = '<span class="src_query" data-id="' . $item['id'] . '">' . $item['src_query'] . '</span>';

		return $title;
	}

	/**
	 * Render the time table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_time( $item ) {

		$dateformat = get_option( 'date_format' );
		$timeformat = get_option( 'time_format' );
		$last_hit   = date( $dateformat . ' ' . $timeformat, strtotime( $item['time'] ) );

		return $last_hit;
	}

	/**
	 * Render the action table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_meaning( $item ) {

		$status = (int) $item['status'];

		switch ( $status ) {
			case 2:
				$status_txt = __( 'Did You Mean:', 'i_search' );
				break;
			case 3:
				$status_txt = __( 'Popular Searches:', 'i_search' );
				break;
			default:
				$status_txt = ' ';
		}

		$div_1 = "<div class='status_1'>{$status_txt}</div>";
		$div_2 = "<div class='status_2'></div>";

		if ( $status == 2 ) {
			$div_2 = "<div data-id='{$item['id']}' class='meaning_txt status_2'>{$item['meaning']}</div>";
		}

		return $div_1 . $div_2;

	}

	/**
	 * Render the title table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_title( $item ) {

		return $item['title'];

	}

	/**
	 * Render the hits table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_hit( $item ) {

		return $item['hit'];

	}

	/**
	 * Render the instance table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_instance( $item ) {
		$instances = explode( ',', $item['instance'] );
		$title     = array();
		foreach ( $instances as $shortcode_id ) {
			$data    = get_isearch_shortcode_data( $shortcode_id, true );
			$title[] = $data[0]['title'];
		}

		return implode( ', ', $title );

	}

	/**
	 * Render the table and echo it.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function display() {

		wp_nonce_field( 'isrc_table_nonce', '_isrc_table_nonce' );
		wp_nonce_field( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

		echo '<input id="order" type="hidden" name="order" value="' . $this->_pagination_args['order'] . '" />';
		echo '<input id="status_fltr" type="hidden" name="status" value="' . $this->_pagination_args['status'] . '" />';
		echo '<input id="orderby" type="hidden" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';

		parent::display();
	}

	/**
	 * Ajax actions for the table. Our table updates his self via ajax.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function ajax_response() {

		check_ajax_referer( 'ajax-custom-list-nonce', '_ajax_custom_list_nonce' );

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_GET['no_placeholder'] ) ) {
			$this->display_rows();
		} else {
			$this->display_rows_or_placeholder();
		}
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->views();
		$views_top = ob_get_clean();

		ob_start();
		$this->pagination( 'top' );
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination( 'bottom' );
		$pagination_bottom = ob_get_clean();

		$response                         = array( 'rows' => $rows );
		$response['pagination']['top']    = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers']       = $headers;
		$response['views_top']            = $views_top;

		if ( isset( $total_items ) ) {
			$response['total_items_i18n'] = sprintf( __( '%s items', $total_items ), number_format_i18n( $total_items ) );
		}

		if ( isset( $total_pages ) ) {
			$response['total_pages']      = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		wp_send_json( $response );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'logs_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
				'total_items' => $total_items, //WE have to calculate the total number of items
				'per_page'    => $per_page, //WE have to determine how many items to show on a page
				'total_pages' => ceil( $total_items / $per_page ),
				// Set ordering values if needed (useful for AJAX)
				'status'      => ! empty( $_GET['status'] ) && '' != (int) $_GET['status'] ? $_GET['status'] : '0',
				'orderby'     => ! empty( $_GET['orderby'] ) && '' != esc_attr( wp_unslash( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'time',
				'order'       => ! empty( $_GET['order'] ) && '' != esc_attr( wp_unslash( $_GET['order'] ) ) ? $_GET['order'] : 'desc'
			)
		);

		$this->items = self::get_the_logs( $per_page, $current_page );
	}

	/**
	 * Handles bulk actions.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_GET['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'isrc_delete_log' ) ) {
				die( 'Wooha' );
			} else {
				self::delete_log( absint( $_GET['log_id'] ) );

				$current_url  = esc_url_raw( add_query_arg() );
				$redirect_url = remove_query_arg(
					array(
						'log_id',
						'action',
						'_wpnnoce'
					),
					$current_url
				);
				wp_redirect( $redirect_url );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' ) ) {
			if ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) {
				$delete_ids = esc_sql( $_POST['bulk-delete'] );
				// loop over the array of record IDs and delete them
				if ( isset( $_POST['status'] ) && $_POST['status'] == 3 ) {
					foreach ( $delete_ids as $id ) {
						isrc_delete_popularity_log( $id );
					}
				} else {
					foreach ( $delete_ids as $id ) {
						self::delete_log( $id );
					}
				}
			}

		}
	}

	/**
	 * Delete the log file.
	 *
	 * @param int $id Log id in the DB.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public static function delete_log( $id ) {
		global $wpdb;

		return $wpdb->delete(
			"{$wpdb->prefix}isearch_logs",
			array( 'id' => $id ),
			array( '%d' )
		);
	}

	/**
	 * Get the record counts in the db.
	 *
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return int
	 */
	public static function record_count() {
		global $wpdb;

		$lang = isrc_get_lang_admin();
		$sql  = "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$lang}'";

		if ( ! empty( $_GET['status'] ) ) {
			$sql .= ' AND status = ' . '"' . esc_sql( $_GET['status'] ) . '"';
		}

		return $wpdb->get_var( $sql );
	}

	/**
	 * Get the logs from DB.
	 *
	 * @param int $per_page    per page to show records.
	 * @param int $page_number current age number for pagination.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array Database results
	 */
	public static function get_the_logs( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$lang = isrc_get_lang_admin();

		if ( isset( $_GET['status'] ) ) {
			$status = (int) $_GET['status'];
		} else {
			$status = 0;
		}

		/* status is_ popular logs? */
		if ( $status === 3 ) {

			$sql = "SELECT * FROM {$wpdb->prefix}isearch_popular WHERE lang = '{$lang}'";
			if ( ! empty( $_GET['orderby'] ) ) {
				$sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
				$sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
			} else {
				$sql .= ' ORDER BY time DESC';
			}

			$sql    .= " LIMIT $per_page";
			$sql    .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			return $result;
		} else {

			$sql = "SELECT * FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$lang}'";

			if ( ! empty( $_GET['status'] ) ) {
				$sql .= ' AND status = ' . '"' . esc_sql( $_GET['status'] ) . '"';
			}

			if ( ! empty( $_GET['orderby'] ) ) {
				$sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
				$sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
			} else {
				$sql .= ' ORDER BY time DESC';
			}

			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

			$result = $wpdb->get_results( $sql, 'ARRAY_A' );

			/* check if not found queries maybe now found because of changes in the log settings */
			$deleted_logs = isrc_recheck_logs_by_results( $result );

			if ( $deleted_logs !== false ) {
				/* reload data from db because we have deleted logs */
				$result = $wpdb->get_results( $sql, 'ARRAY_A' );
			}

			return $result;
		}
	}

	/**
	 * Associative array of columns.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_columns() {

		$status = ( isset( $_GET['status'] ) ) ? $_GET['status'] : 0;

		if ( $status == 3 ) {
			$columns = array(
				'cb'    => '<input type="checkbox" />',
				'title' => __( 'Title', 'i_search' ),
				'hit'   => __( 'Hits', 'i_search' ),
				'time'  => __( 'Time', 'i_search' ),
			);

			return $columns;
		}

		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'src_query' => __( 'Searched string', 'i_search' ),
			'flow'      => __( 'Flow', 'i_search' ),
			'meaning'   => __( 'Action', 'i_search' ),
			'instance'  => __( 'Instance', 'i_search' ),
			'count'     => __( 'Count', 'i_search' ),
			'time'      => __( 'Time', 'i_search' ),
			'last_ip'   => __( 'latest IP', 'i_search' ),
			'edit_btn'  => '',
		);

		return $columns;
	}

	/**
	 * Which columns should be sortable.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_sortable_columns() {

		$sortable_columns = array(
			'src_query' => array( 'src_query', true ),
			'count'     => array( 'count', true ),
			'instance'  => array( 'instance', false ),
			'meaning'   => array( 'meaning', false ),
			'time'      => array( 'time', false ),
			'hit'       => array( 'hit', false ),
			'last_ip'   => array( 'last_ip', false )
		);

		return $sortable_columns;

	}

	/**
	 * Define the allowed bulk actions.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete'
		);

		return $actions;
	}

}