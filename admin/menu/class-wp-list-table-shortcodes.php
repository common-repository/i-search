<?php

/**
 * i-Search list table class
 *
 * This file is loaded only in admin.
 * The main class file to build shortcodes table in the admin shortcode builder TAB.
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
 * @since      2.1.0
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

class isrcShortcode_List extends isrc_WP_List_Table {

	public $default_sc = false;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Shortcode', 'i_search' ), //singular name of the listed records
			'plural'   => __( 'Shortcodes', 'i_search' ), //plural name of the listed records
			'ajax'     => true,
			'screen'   => 'isrc-opt-page'
		) );

		$this->default_sc = get_option( 'isrc_default_sc_' . isrc_get_lang_admin(), false );
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
		_e( 'No shortcodes avaliable.', 'i_search' );
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
			case 'title':
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

		if ( empty( $item['title'] ) ) {
			$title = 'No Title';
		} else {
			$title = $item['title'];
		}

		$menu_url = html_entity_decode( menu_page_url( 'isrc-opt-page', false ) );
		$edit_url = html_entity_decode( add_query_arg( array( 'tab' => 'scbuilder-list', 'sub-tab' => 'add_new_sc', 'sc_id' => $item['id'] ), $menu_url ) );

		return "<a href='{$edit_url}'>" . $title . "</a><span data-scid='{$item['id']}' class='isrc-clone'><i class='fas fa-clone tooltipclone'></i></span> ";

	}

	/**
	 * Render the default shortcode table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_default_sc( $item ) {

		if ( $this->default_sc !== false && $this->default_sc == $item['id'] ) {
			$title = '<span class="dashicons dashicons-yes isrc-ic-def"></span>';
		} else {
			$title = '';
		}

		return $title;

	}

	/**
	 * Render the shortcode table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_shortcode( $item ) {
		$id = $item['id'];

		return "<div class='pos-rel'><input readonly type='text' id='sc_inp_{$id}' value='[isrc_ajax_search shortcode_id={$id}]'><span data-sc_id='{$id}' class='sc sc-cpy'><i class='tooltipcopy fas fa-copy'></i></span></div> ";
	}

	/**
	 * Render the phpcode table section.
	 *
	 * @param array $item Current DB row items.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function column_php_code( $item ) {
		$id = $item['id'];

		return "<div class='pos-rel'><input readonly type='text' id='php_inp_{$id}' value='isrc_get_instance( {$id} );'><span data-phpcode_id='{$id}' class='php_c sc-cpy'><i title='" . __( 'Copy Code', 'i_search' ) . "' class='tooltipcopy fas fa-copy'></i></span></div> ";
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
				'status'      => ! empty( $_GET['status'] ) && '' != $_GET['status'] ? (int) $_GET['status'] : '0',
				'orderby'     => ! empty( $_GET['orderby'] ) && '' != esc_attr( wp_unslash( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id',
				'order'       => ! empty( $_GET['order'] ) && '' != esc_attr( wp_unslash( $_GET['order'] ) ) ? $_GET['order'] : 'desc'
			)
		);

		$this->items = self::get_the_shortcodes( $per_page, $current_page );
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

			if ( ! wp_verify_nonce( $nonce, 'isrc_delete_shortcode' ) ) {
				die( 'Wooha' );
			} else {
				self::delete_shortcode( absint( $_GET['log_id'] ) );

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
				foreach ( $delete_ids as $id ) {
					self::delete_shortcode( $id );
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
	public static function delete_shortcode( $id ) {
		global $wpdb;

		return $wpdb->delete(
			"{$wpdb->prefix}isearch_shortcodes",
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
		$sql  = "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_shortcodes WHERE lang = '{$lang}'";

		if ( ! empty( $_GET['status'] ) ) {
			$sql .= ' WHERE status = ' . '"' . esc_sql( $_GET['status'] ) . '"';
		}

		return $wpdb->get_var( $sql );
	}

	/**
	 * Get the shortcodes from DB.
	 *
	 * @param int $per_page    per page to show records.
	 * @param int $page_number current age number for pagination.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array Database results
	 */
	public static function get_the_shortcodes( $per_page = 20, $page_number = 1 ) {

		global $wpdb;

		$lang = isrc_get_lang_admin();
		$sql  = "SELECT * FROM {$wpdb->prefix}isearch_shortcodes WHERE lang = '{$lang}'";

		if ( ! empty( $_GET['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_GET['orderby'] );
			$sql .= ! empty( $_GET['order'] ) ? ' ' . esc_sql( $_GET['order'] ) : ' ASC';
		} else {
			$sql .= ' ORDER BY title ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
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

		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'title'      => __( 'Title', 'i_search' ),
			'shortcode'  => __( 'Shortcode', 'i_search' ),
			'php_code'   => __( 'PHP code', 'i_search' ),
			'default_sc' => __( 'Default', 'i_search' ),
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
			'title' => array( 'title', true ),
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