<?php

/**
 * i-Search admin class file
 *
 * This file is loaded only on frontend.
 * The main class file for all the frontend functions.
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

class isrc_front_main {

	public $iswoo_active = false;
	public $settings_general = false;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $isrc_opt;

		if ( defined( 'ISRC_WOOCOMMERCE_INSTALLED' ) ) {
			$this->iswoo_active = true;
		}

		$this->settings_general = $isrc_opt;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 99 );

		add_shortcode( 'isrc_ajax_search', array( $this, 'isrc_ajax_search_shortcode' ) );

		$this->maybe_apply_filters();

		add_action( 'updated_post_meta', array( $this, 'isrc_post_meta_updated' ), 99, 3 );

		if ( defined( 'ISRC_WOOCOMMERCE_INSTALLED' ) ) {
			/*
			 * Redirect function if buy now is activated
			 */
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'isrc_buy_now' ), 99 );

		}


	}

	/**
	 * Apply WP filters based on admin settings. Like auto hook into search input field.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 *
	 * @return void
	 */
	public function maybe_apply_filters() {
		global $isrc_opt;

		if ( isset( $isrc_opt['front']['auto_hook'] ) ) {
			add_filter( 'get_search_form', array( $this, 'replace_default_form' ), 99, 1 );

			if ( $this->iswoo_active ) {
				add_filter( 'get_product_search_form', array( $this, 'replace_default_form' ), 99, 1 );
			}
		}
	}


	/**
	 * On post meta updated hooks.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $meta_id
	 * @param $post_id
	 * @param $meta_key
	 *
	 * @return void
	 */
	public function isrc_post_meta_updated( $meta_id, $post_id, $meta_key ) {

		/* never update on post update */
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {
			return;
		}

		/* filter */
		$watched_keys = array();
		$watched_keys = apply_filters( 'isearch_watch_meta_keys_for_update', $watched_keys );
		$watched_keys = array_values( array_filter( array_unique( $watched_keys ) ) );

		if ( empty( $watched_keys ) ) {
			return;
		}

		if ( in_array( $meta_key, $watched_keys ) ) {
			require_once ISRC_PLUGIN_DIR . '/admin/admin-includes.php';
			update_post_isrc( $post_id );
		}

		return;
	}

	/**
	 * Replace the wp default form if checked in admin settings.
	 *
	 * @param string $form
	 *
	 * @return string
	 */
	public function replace_default_form( $form = '' ) {
		$opt_default_sc = get_option( 'isrc_default_sc_' . isrc_get_lang_front(), false );

		if ( $opt_default_sc !== false ) {

			wp_enqueue_script( 'isrc_autocomplete' );
			wp_enqueue_script( 'isrc_frontend' );

			$form = do_shortcode( "[isrc_ajax_search shortcode_id={$opt_default_sc}]" );

		}

		return $form;
	}


	/**
	 * Shortcode function.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function isrc_ajax_search_shortcode( $atts = array() ) {
		global $isrc_opt;

		/* $atts is used in included file */

		wp_enqueue_script( 'isrc_autocomplete' );
		wp_enqueue_script( 'isrc_frontend' );
		ob_start();
		include( ISRC_PLUGIN_DIR . '/front/templates/isrc_ajax_template.php' );

		return ob_get_clean();

	}

	/**
	 * Enqueue styles and scripts for front.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function enqueue_styles_scripts() {
		global $isrc_opt, $isrc_opt_adv, $isrc_content_builder;

		$settings = $isrc_opt;

		/* content builder */
		if ( isset( $isrc_content_builder['builder_data'] ) && ! empty( $isrc_content_builder['builder_data'] ) ) {
			$builder_data = $isrc_content_builder['builder_data'];
		} else {
			$builder_data = 'no';
		}

		/* tab order */
		if ( isset( $settings['front']['taborder'] ) && ! empty( $settings['front']['taborder'] ) && isset( $settings['front']['tabs_ed'] ) && $settings['front']['tabs_ed'] == 1 ) {
			$tab_order = $settings['front']['taborder'];
		} else {
			$tab_order = array();
		}


		/* ajax url */
		$ajax_url    = admin_url( 'admin-ajax.php' );
		$ajax_action = 'isrc_ajax_search_posts';

		/* Category label */
		$post_category_label = ( isset( $settings['front']['cats_l'] ) && isset( $settings['front']['cat'] ) ) ? $settings['front']['cats_l'] : '';

		/* Woo Category label */
		$category_label = ( isset( $settings['woo']['cats_l'] ) && isset( $settings['woo']['cat'] ) ) ? $settings['woo']['cats_l'] : '';

		/* Show woo categories */
		$show_cat = ( isset( $settings['woo']['cat'] ) ) ? 'yes' : 'no';

		/* Show post categories */
		$show_post_cat = ( isset( $settings['front']['cat'] ) ) ? 'yes' : 'no';

		/* out of stock label */
		$outofstock_label = ( isset( $settings['woo']['outofstock_l'] ) && isset( $settings['woo']['outofstock'] ) ) ? $settings['woo']['outofstock_l'] : '';

		/* in stock label */
		$instock_label = ( isset( $settings['woo']['instock_l'] ) && isset( $settings['woo']['variablep'] ) ) ? $settings['woo']['instock_l'] : '';

		/* show dtailed stock for variable products */
		$detailed_stock = ( ! empty( $settings['woo']['variablep'] ) ) ? 'yes' : 'no';

		/* backorder label */
		$backorder_label = ( isset( $settings['woo']['backorder_l'] ) && isset( $settings['woo']['backorder'] ) ) ? $settings['woo']['backorder_l'] : '';

		/* sale label */
		$sale_label = ( isset( $settings['woo']['sale_l'] ) && isset( $settings['woo']['sale'] ) ) ? $settings['woo']['sale_l'] : '';

		/* featured label */
		$featured_label = ( isset( $settings['woo']['featured_l'] ) && isset( $settings['woo']['featured'] ) ) ? $settings['woo']['featured_l'] : '';

		/* Add to cart button? */
		$atc_btn = ( ! empty( $settings['front']['enable_atc'] ) ) ? 'yes' : 'no';

		/* Add to cart button label? */
		$atc_label = ( ! empty( $settings['front']['atc_label'] ) ) ? $settings['front']['atc_label'] : 'Add to cart';

		/* Buy now in results enabled? */
		if ( isset( $settings['front']['enable_buyn'] ) && $settings['front']['enable_buyn'] == '1' ) {
			$ed_buynow    = 'yes';
			$buynow_label = $settings['front']['buyn_label'];
		} else {
			$ed_buynow    = 'no';
			$buynow_label = '';
		}

		/* Tabs in results enabled? */
		if ( isset( $settings['front']['tabs_ed'] ) ) {
			$tabresult = 'yes';
		} else {
			$tabresult = 'no';
		}

		/* No image */
		if ( isset( $settings['front']['no_img'] ) ) {
			$thumb_size_h       = $settings['front']['thumb_size']['h'];
			$thumb_size_w       = $settings['front']['thumb_size']['w'];
			$thumb_size_name    = "isrc_thumb_{$thumb_size_w}_{$thumb_size_h}";
			$thumb_size_name_x2 = "isrc_thumb_{$thumb_size_w}_{$thumb_size_h}_x2";

			$no_image    = wp_get_attachment_image_src( $settings['front']['no_img'], $thumb_size_name, false );
			$no_image_x2 = wp_get_attachment_image_src( $settings['front']['no_img'], $thumb_size_name_x2, false );
			if ( isset( $no_image[0] ) ) {
				$no_image = $no_image[0];
			} else {
				$no_image = false;
			}
			if ( isset( $no_image_x2[0] ) ) {
				$no_image_x2 = $no_image_x2[0];
			} else {
				$no_image_x2 = false;
			}
		} else {
			$no_image    = false;
			$no_image_x2 = false;
		}

		/* check if user is admin. Add hash to variables to disable spam protection for admin */
		if ( current_user_can( ISRC_CAPABILITIES ) ) {
			$hashvar = get_option( 'isrc_hash' );
		} else {
			$hashvar = false;
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		//$suffix = '';

		if ( isset( $isrc_opt_adv['jquery'] ) ) {
			/* include wp default jquery if the user select it in the settings. Some templates don't have jquery included by default */
			wp_enqueue_script( 'jquery' );
		}

		$locale = isrc_get_lang_front();

		wp_enqueue_style( 'isrc_frontend', ISRC_PLUGIN_URL . '/front/css/i-search' . $suffix . '.css', array(), ISRC_SCRIPT_VER );

		wp_register_script( 'isrc_autocomplete', ISRC_PLUGIN_URL . '/front/js/isrc-autocomplete' . $suffix . '.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'isrc_frontend', ISRC_PLUGIN_URL . '/front/js/frontend' . $suffix . '.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );

		wp_localize_script( 'isrc_autocomplete', 'isrc_params', array(
			'ajax_url'         => $ajax_url,
			'ajax_url_org'     => admin_url( 'admin-ajax.php' ),
			'ajax_action'      => $ajax_action,
			'ptlabels'         => $tab_order,
			'locale'           => $locale,
			'tabs'             => $tabresult,
			'is_woo'           => ( $this->iswoo_active ) ? 'yes' : 'no',
			'show_cat'         => $show_cat,
			'show_post_cat'    => $show_post_cat,
			'no_image'         => $no_image,
			'no_image_x2'      => $no_image_x2,
			'cat_label'        => $category_label,
			'post_cat_label'   => $post_category_label,
			'outofstock_label' => $outofstock_label,
			'instock_label'    => $instock_label,
			'backorder_label'  => $backorder_label,
			'is_rtl'           => is_rtl() ? 'yes' : 'no',
			'sale_label'       => $sale_label,
			'featured_label'   => $featured_label,
			'isMobile'         => false,
			'atc_btn'          => $atc_btn,
			'atc_label'        => $atc_label,
			'detailed_stock'   => $detailed_stock,
			'hashvar'          => $hashvar,
			'ed_buynow'        => $ed_buynow,
			'builder'          => $builder_data,
			'buynow_label'     => $buynow_label,
			'bloginfo'         => trailingslashit( get_bloginfo( 'url' ) )

		) );

		wp_enqueue_script( 'isrc_autocomplete' );
		wp_enqueue_script( 'isrc_frontend' );

		/*
		* Load iframeresizer js only if is admin preview
		*/
		if ( isrc_is_preview() ) {
			wp_register_script( 'iframe-resizer-content', ISRC_PLUGIN_URL . '/admin/menu/js/iframeresizer/iframeResizer.contentWindow.min.js', array( 'jquery' ), '3.6.1', true );
			wp_enqueue_script( 'iframe-resizer-content' );
		}

		return true;
	}


	/**
	 * Function to redirect user after buy now buy button is submitted
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function isrc_buy_now( $url ) {
		if ( isset( $_REQUEST['isrc_buy_now'] ) && $_REQUEST['isrc_buy_now'] == true && function_exists( 'wc_get_checkout_url' ) ) {
			return wc_get_checkout_url();
		}

		return $url;
	}

}

$isrc_front_main = new isrc_front_main();