<?php
/**
 * i-Search visual composer class file
 *
 *
 *
 *
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      3.0.0
 */

// Element Class
class isrcVcElement extends WPBakeryShortCode {

	// Element Init
	function __construct() {
		add_action( 'init', array( $this, 'isrc_ajax_search_mapping' ) );
	}

	public function isrc_ajax_search_mapping() {

		// Stop all if VC is not enabled
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		$dropdown = $this->get_the_shortcodes();

		// Map the block with vc_map()
		vc_map(
			array(
				'name'        => __( 'i-Search Instance', 'i_search' ),
				'base'        => 'isrc_ajax_search',
				'description' => __( 'Add i-Search Instance', 'i_search' ),
				'category'    => __( 'i-Search', 'i_search' ),
				'icon'        => ISRC_PLUGIN_URL.'/vc-elements/img/isrc-vc-32.svg',
				'params'      => array(

					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Search instance', 'i_search' ),
						'param_name'  => 'shortcode_id',
						'description' => __( 'Select search instance', 'i_search' ),
						'admin_label' => true,
						'save_always' => true,
						'weight'      => 0,
						'value'       => $dropdown,
					)

				)
			)
		);

	}

	/**
	 * Get the shortcodes from DB.
	 *
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return array Database results
	 */
	public function get_the_shortcodes() {

		global $wpdb;
		if(function_exists('isrc_get_lang_admin')){
			$lang = isrc_get_lang_admin();
		}elseif(function_exists('isrc_get_lang_front')){
			$lang = isrc_get_lang_front();
		}
		$sql = "SELECT id,title FROM {$wpdb->prefix}isearch_shortcodes WHERE lang = '{$lang}' ORDER BY title ASC";

		$results = $wpdb->get_results( $sql, 'ARRAY_A' );

		$dropdown = array();

		if ( ! empty( $results ) ) {
			foreach ( $results as $key => $result ) {
				$id                 = $result['id'];
				$title              = $result['title'];
				$dropdown[ $title ] = $id;
			}
		}

		return $dropdown;
	}

} // End Element Class

// Element Class Init
new isrcVcElement();
