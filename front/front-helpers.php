<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $isrc_opt ) ) {
	$isrc_opt = get_option( 'isrc_opt_' . isrc_get_lang_front() );
}

/**
 * Helper for debugging.
 *
 * @since 1.0.0
 *
 * @param string $data
 *
 * @return void
 */
if ( ! function_exists( 'isrc_debug_log' ) ) {
	function isrc_debug_log( $data ) {
		error_log( $data );
	}
}

/**
 * Deprecated will be removed!
 * Generate live search form
 *
 * @param  array $atts
 *
 * @return string
 */
function show_i_search_form( $atts = array() ) {
	global $isrc_front_main;
	$form = $isrc_front_main->isrc_ajax_search_shortcode( $atts );

	return $form;
}

/**
 * Generate live search form
 *
 * @param int  $instance_id
 *
 * @param bool $echo
 *
 * @return string
 */
function isrc_get_instance( $instance_id = 0, $echo = true ) {

	$instance = do_shortcode( "[isrc_ajax_search shortcode_id=$instance_id]" );
	if ( $echo ) {
		echo $instance;
	} else {
		return $instance;
	}

}

$isrc_query_args = array(
	'src_q_changed' => false
);


/**
 * Replace WP search engine with i-Search based on admin settings.
 *
 * @param string $query
 *
 * @return string
 */

function isrc_wp_engine_queryfilter( $query ) {
	global $isrc_query_args, $wpdb, $wp_query;

	if ( ! isset( $wp_query->query['s'] ) ) {
		return $query;
	}
	$string_to_replace_raw = 'SELECT SQL_CALC_FOUND_ROWS ' . $wpdb->prefix . 'posts.ID FROM ' . $wpdb->prefix . 'posts';
	$string_to_replace_san = sanitize_title_with_dashes( $string_to_replace_raw );
	$query_san             = sanitize_title_with_dashes( $query );
	if ( strpos( $query_san, $string_to_replace_san ) !== false && ! $isrc_query_args['src_q_changed'] ) {
		$lang = isrc_get_lang_front();
		require_once ISRC_PLUGIN_DIR . '/admin/admin-functions.php';
		require_once ISRC_PLUGIN_DIR . '/class-ajax.php';
		$isrc_ajax_class = new isrc_ajax_class();

		$post_per_page = $wp_query->query_vars['posts_per_page'];

		$isrc_ajax_class->locale          = $lang;
		$isrc_ajax_class->logging         = false;
		$isrc_ajax_class->log_popularity  = false;
		$isrc_ajax_class->show_popularity = false;
		$isrc_ajax_class->show_didumeans  = false;
		$isrc_ajax_class->sql_limit       = $post_per_page;

		/* search for post_type? */
		if ( isset( $wp_query->query_vars['post_type'] ) && ! empty( trim( $wp_query->query_vars['post_type'] ) ) && $wp_query->query_vars['post_type'] != 'any' ) {
			$search_in = 'pt_' . $wp_query->query_vars['post_type'];
			$isrc_ajax_class->set_customized_post_types( $search_in );
		}

		$search_query = array(
			'query'                    => $wp_query->query['s'],
			'format'                   => 'array',
			'get_post_type_query_only' => true,
			'disable_logging'          => true
		);

		$isrc_query = $isrc_ajax_class->ajax_search_posts( $search_query );
		if ( ! empty( $isrc_query ) ) {
			/* replace LIMIT */
			if ( $wp_query->is_paged ) {
				$page_offset = ( $wp_query->query_vars['paged'] - 1 ) * $post_per_page;
				$limit       = " LIMIT $page_offset, $post_per_page";
			} else {
				$limit = " LIMIT 0, $post_per_page";
			}

			$limit_added = (int) $post_per_page + 1;
			$isrc_query  = str_replace( ' LIMIT ' . $limit_added, ' ' . $limit, $isrc_query );

			/* format sql */
			$table      = 'isearch';
			$search_org = "SELECT output FROM {$wpdb->prefix}{$table} WHERE";
			$search     = '/' . preg_quote( $search_org, '/' ) . '/';
			$replace    = "SELECT SQL_CALC_FOUND_ROWS  {$wpdb->prefix}{$table}.post_id as ID FROM {$wpdb->prefix}{$table} WHERE 1=1 AND ";
			$query_temp = preg_replace( $search, $replace, $isrc_query, 1 );
			$replace    = "SELECT {$wpdb->prefix}{$table}.post_id as ID FROM {$wpdb->prefix}{$table} WHERE 1=1 AND ";
			$query      = str_replace( $search_org, $replace, $query_temp );

		}

		$isrc_query_args['src_q_changed'] = true;
		$query                            = $wpdb->remove_placeholder_escape( $query );

	}

	return $query;
}

/* Replace WP search engine? */
if ( isset( $isrc_opt['front']['replace'] ) ) {
	add_filter( 'query', 'isrc_wp_engine_queryfilter', 99, 1 );
}

/**
 * Check if current view is admin preview.
 *
 * @return boolean
 */
function isrc_is_preview() {
	if ( isset( $_GET['isrc_preview'] ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get the shortcode data if the attr. shortcode_id is set in shortcode.
 *
 * @param null $sc_id
 *
 * @return array
 */
function isrc_get_shortcode_instance( $sc_id = null ) {
	global $wpdb;

	if ( empty( $sc_id ) ) {
		return array();
	}

	$query   = "SELECT settings FROM {$wpdb->prefix}isearch_shortcodes WHERE id = %d";
	$results = $wpdb->get_var( $wpdb->prepare( $query, $sc_id ) );
	if ( ! empty( $results ) ) {
		$sc_atts = maybe_unserialize( $results );
	} else {
		return array();
	}

	$shortcode_atts = array();
	/* unset before */
	unset( $sc_atts['title'] );
	foreach ( $sc_atts as $key => $val ) {

		if ( $key == 'ph_adverts' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'exc_max_words' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'colors' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'cb_flds' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( is_array( $val ) ) {
			/* change array in to acceptable shortcode string */
			$temp = array();
			foreach ( $val as $key2 => $val2 ) {
				if ( filter_var( $val2, FILTER_VALIDATE_BOOLEAN ) ) {
					$temp[] = $key2;
				}
			}

			if ( ! empty( $temp ) ) {
				$val = implode( ',', $temp );
			}
		}

		if ( is_array( $val ) ) {
			/* something went wrong we have still array as $val. Skip this value to avoid errors */
			continue;
		}

		/* convert false string to 0/1 */
		if ( $val === 'false' ) {
			$val = '0';
		} elseif ( $val === 'true' ) {
			$val = '1';
		}

		$shortcode_atts[ $key ] = $val;
	}

	return $shortcode_atts;

}

/**
 * Convert array to css
 *
 * @param array $colors
 *
 * @return string
 */
function isrc_colorbuilder_to_css( $colors = array() ) {
	$css = '';

	foreach ( $colors as $key => $val ) {
		if ( empty( trim( $val ) ) ) {
			continue;
		}

		if ( $key == 'overl_bg' ) {
			$css .= '.isrc-boxH{background-color:' . $val . '}';
		}

		if ( $key == 'overl_ic_bg' ) {
			$css .= '.isrc-win-close{background-color:' . $val . '}';
		}

		if ( $key == 'input_bg' ) {
			$css .= '.isrc-input-wrapper{background:' . $val . '}';
		}

		if ( $key == 'input_bc' ) {
			$css .= '.isrc-input-wrapper{border-color:' . $val . '}';
		}

		if ( $key == 'input_bc_f' ) {
			$css .= '.isrc-focused .isrc-input-wrapper{border-color:' . $val . ' !important}';
		}

		if ( $key == 'input_ll' ) {
			$css .= '.isrc-input-wrapper .inp_style_1{color:' . $val . '}';
		}

		if ( $key == 'plchldr_cl' ) {
			$css .= '.isrc-s::-webkit-input-placeholder{color:' . $val . '}';
			$css .= '.isrc-s::-moz-input-placeholder{color:' . $val . '}';
		}

		if ( $key == 'plchldr_adv_cl' ) {
			$css .= '.isrc-s.advert::-webkit-input-placeholder{color:' . $val . '}';
			$css .= '.isrc-s.advert::-moz-input-placeholder{color:' . $val . '}';
		}

		if ( $key == 'inpt_cl' ) {
			$css .= '.isrc-s{color:' . $val . '}';
			$css .= '.isrc-s:focus{color:' . $val . '}';
		}

		if ( $key == 'spin_cl' ) {
			$css .= '.isrc_preloader.ispreload{background-color:' . $val . ' !important}';
		}

		if ( $key == 'xclose_cl' ) {
			$css .= '.isrc_preloader.xclose{background-color:' . $val . '}';
		}

		if ( $key == 'subm_txt_cl' ) {
			$css .= '.isrc-searchsubmit{color:' . $val . '}';
		}

		if ( $key == 'underl_cl' ) {
			$css .= '.inp-st-style_3 .inp-underl{background-color:' . $val . '}';
		}

		if ( $key == 'underl_ed' ) {
			$underline_ed = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
			if ( $underline_ed ) {
				$css .= '.inp-st-style_3 .inp-underl{display:none !important}';
			}
		}


		if ( $key == 'cont_bc' ) {
			$css .= '.isrc_autocomplete-suggestions{border-color:' . $val . '}';
		}

		if ( $key == 'cont_bc_ed' ) {
			$border_ed = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
			if ( $border_ed ) {
				$css .= '.isrc_autocomplete-suggestions{border:none !important}';
			}
		}

		if ( $key == 'ptdivider_bg' ) {
			$css .= '.isrc_pt_div{background:' . $val . '}';
		}

		if ( $key == 'ptdivider_cl' ) {
			$css .= '.isrc_pt_div{color:' . $val . '}';
			$css .= '.isrc_pt_div:after{background-color:' . $val . '}';
		}

		if ( $key == 'tab_sel_top_bc' ) {
			$css .= '.suggestion-tabs .tab-result.selected{border-top-color:' . $val . '}';
		}

		if ( $key == 'tab_nsel_bg_cl' ) {
			$css .= '.suggestion-tabs .tab-result{background-color:' . $val . '}';
		}

		if ( $key == 'tab_nsel_hvr_bg_cl' ) {
			$css .= '.suggestion-tabs .tab-result:hover{background-color:' . $val . '}';
		}

		if ( $key == 'tab_sel_bg_cl' ) {
			$css .= '.suggestion-tabs .tab-result.selected{background-color:' . $val . '}';
		}

		if ( $key == 'tab_sel_bg_hvr_cl' ) {
			$css .= '.suggestion-tabs .tab-result.selected:hover{background-color:' . $val . '}';
		}

		if ( $key == 'tab_cl' ) {
			$css .= '.isrc_autocomplete-suggestions .suggestion-tabs .tab-result{color:' . $val . '}';
		}

		if ( $key == 'cont_bg_cl' ) {
			$css .= '.isrc_autocomplete-suggestions{background-color:' . $val . '}';
		}

		if ( $key == 'sug_act_bg_cl' ) {
			$css .= '.isrc_autocomplete-suggestion.detail-selector{background-color:' . $val . '}';
			$css .= '.isrc_autocomplete-suggestion.isrc_autocomplete-selected{background-color:' . $val . '}';
			$css .= '.isrc_autocomplete-suggestion:hover{background-color:' . $val . '}';
		}

		if ( $key == 'sug_bd_cl' ) {
			$css .= '.isrc_autocomplete-suggestion{border-color:' . $val . ' !important}';
			$css .= '.isrc_autocomplete-suggestion{border-right-color:' . $val . '}';
		}

		if ( $key == 'sug_fnt_cl' ) {
			$css .= '.isrc_autocomplete-suggestion,.isrc_extras,.isrc_autocomplete-suggestions .isrc_result_content .sug_exc{color:' . $val . '}';
			$css .= '.isrc_details_excerpt,.adv_title_isrc{color:' . $val . '}';
		}

		if ( $key == 'mr_bg_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_autocomplete-suggestions .link-result{background-color:' . $val . '}';
		}

		if ( $key == 'mr_bg_hvr_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_autocomplete-suggestions .link-result:hover{background-color:' . $val . '}';
		}

		if ( $key == 'mr_fnt_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_autocomplete-suggestions .link-result{color:' . $val . '}';
		}

		if ( $key == 'mr_icn_cl' ) {
			$css .= '.load-more-label:before{background-color:' . $val . '}';
		}

		if ( $key == 'inp_ic_cl' ) {
			$css .= '.isr-ic-search, .isrc-cl-op{color:' . $val . '}';
		}

		if ( $key == 'inp_h' ) {
			$css .= '.isrc-mh-val{min-height:' . $val . 'px}';
			$css .= '.isrc-h-val{height:' . $val . 'px}';
		}

		if ( $key == 'border_r' ) {
			$css .= '.isrc-input-wrapper{overflow:hidden;border-radius:' . $val . 'px;-webkit-border-radius:' . $val . 'px;-moz-border-radius:' . $val . 'px}';
		}

		if ( $key == 'wrap_min_w' ) {
			$css .= '.isrc-slide{min-width:' . $val . '}';
		}

		if ( $key == 'match_bg' ) {
			$css .= '.isrc_extras .ex_val strong,.sug_cats strong{font-weight: inherit;background:' . $val . '}';
		}

		if ( $key == 'match_cl' ) {
			$css .= '.isrc_extras .ex_val strong,.sug_cats strong{font-weight: inherit;color:' . $val . '}';
		}

		if ( $key == 'cb_fnt_cl' ) {
			$css .= '.isrc_extras {color:' . $val . '}';
		}

		if ( $key == 'cb_clc_cl' ) {
			$css .= 'iclick {padding: 1px 4px;background-color:' . $val . '}';
		}

		if ( $key == 'badg_bg' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_onsale {background-color:' . $val . '}';
		}

		if ( $key == 'badg_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_onsale {color:' . $val . '}';
		}

		if ( $key == 'badg_ft_bg' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_featured {background-color:' . $val . '}';
		}

		if ( $key == 'badg_ft_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_featured {color:' . $val . '}';
		}

		if ( $key == 'badg_oos_bg' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_outofstock {background-color:' . $val . '}';
		}

		if ( $key == 'badg_oos_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_outofstock {color:' . $val . '}';
		}

		if ( $key == 'badg_bo_bg' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_backorder {background-color:' . $val . '}';
		}

		if ( $key == 'badg_bo_cl' ) {
			$css .= '.isrc-ajaxsearchform-container .isrc_badge.sug_backorder {color:' . $val . '}';
		}

		if ( $key == 'atc_fr_bg' ) {
			$css .= '.isrc_autocomplete-suggestions.isrc_clean .isrc_atc{background:' . $val . '}';
		}

		if ( $key == 'atc_ed' ) {
			$atc_ed = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
			if ( $atc_ed ) {
				$css .= '.isrc_autocomplete-suggestions.isrc_clean .isrc_atc{display:none !important}';
			}

		}

		if ( $key == 'ts_lb' ) {
			$css .= '.isrc-trendgings-wrap .i-src-trendings-label{color:' . $val . '}';
		}

		if ( $key == 'ts_tag' ) {
			$css .= '.isrc-trendgings-wrap, .isrc-trendgings-wrap .ttag{color:' . $val . '}';
		}

		if ( $key == 'ts_tag_hv' ) {
			$css .= '.isrc-trendgings-wrap .ttag:hover{color:' . $val . '}';
		}

		if ( $key == 'cont_r' ) {
			$css .= '.isrc_autocomplete-suggestions{border-radius:' . $val . 'px}';
		}

		if ( $key == 'ts_mt' ) {
			$css .= '.isrc-trendgings-wrap{margin:' . $val . 'px 0px}';
		}

		if ( $key == 'ts_al' ) {
			if($val == 'r'){
				$css .= '.isrc-trendgings-wrap{text-align:right}';
			}
		}

	}

	return $css;
}

/**
 * Get the trending data tags
 *
 * @param int    $trendings_max
 * @param string $search_in
 * @param string $lang
 *
 * @return array
 */
function isrc_get_trending_tags( $trendings_max = 3, $search_in = '', $lang = 'en' ) {

	/* post types format from shortcode string to array */
	if ( empty( $search_in ) ) {
		return array();
	} else {
		$search_in = explode( ',', $search_in );
	}
	$post_types = array();
	$taxonomies = array();
	foreach ( $search_in as $raw ) {

		if ( substr( $raw, 0, 3 ) === "pt_" ) {

			/* we have a post type */
			$post_types[]               = substr( $raw, 3 );
			$do_query_for['post_types'] = true;

		} elseif ( substr( $raw, 0, 3 ) === "tx_" ) {

			/* we have a taxonomy */
			$taxonomies[]               = substr( $raw, 3 );
			$do_query_for['taxonomies'] = true;

		}
	}

	require_once ISRC_PLUGIN_DIR . '/admin/admin-functions.php';

	return isrc_get_popular_searches( $trendings_max, $post_types, $taxonomies, $lang, 'title' );
}

/**
 * Get the language for front functions
 *
 * @return bool|mixed|string|void
 */
function isrc_get_lang_front() {

	if ( isset( $_GET['locale'] ) ) {
		return $_GET['locale'];
	}

	/*
    * Check for WPML
    */
	$lang = apply_filters( 'wpml_current_language', null );
	if ( ! empty( $lang ) ) {
		return $lang;
	}

	return substr( get_locale(), 0, 2 );

}

/**
 * Assign that main class to every css to prevent overwriting main css
 *
 * @param string $css
 * @param string $main_class
 *
 * @return string
 */
function isrc_assign_custom_css( $css = '', $main_class = '' ) {

	$css = trim( $css );

	if ( empty( $css ) ) {
		return $css;
	}

	// some of the following functions to minimize the css-output are directly taken
	// from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
	// all credits to Christian Schaefer: http://twitter.com/derSchepp
	// remove comments
	$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
	// backup values within single or double quotes
	preg_match_all( '/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER );
	for ( $i = 0; $i < count( $hit[1] ); $i ++ ) {
		$css = str_replace( $hit[1][ $i ], '##########' . $i . '##########', $css );
	}
	// remove traling semicolon of selector's last property
	$css = preg_replace( '/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css );
	// remove any whitespace between semicolon and property-name
	$css = preg_replace( '/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css );
	// remove any whitespace surrounding property-colon
	$css = preg_replace( '/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css );
	// remove any whitespace surrounding selector-comma
	$css = preg_replace( '/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css );
	// remove any whitespace surrounding opening parenthesis
	$css = preg_replace( '/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css );
	// remove any whitespace between numbers and units
	$css = preg_replace( '/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css );
	// shorten zero-values
	$css = preg_replace( '/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css );
	// constrain multiple whitespaces
	$css = preg_replace( '/\p{Zs}+/ims', ' ', $css );
	// remove newlines
	$css = str_replace( array( "\r\n", "\r", "\n" ), '', $css );
	// Restore backupped values within single or double quotes
	for ( $i = 0; $i < count( $hit[1] ); $i ++ ) {
		$css = str_replace( '##########' . $i . '##########', $hit[1][ $i ], $css );
	}
	// Add shorcode id before
	$css = str_replace( '}.', '}.isrc_sc_' . $main_class . ' .', $css );
	$css = str_replace( ',.', ',.isrc_sc_' . $main_class . ' .', $css );
	$css = ".isrc_sc_{$main_class} " . $css;

	return $css;
}

function isrc_get_content_builder_keys_front() {
	$option   = get_option( 'isrc_opt_content_' . isrc_get_lang_front() );
	$keys_all = array();
	if ( isset( $option['builder_data'] ) ) {
		foreach ( $option['builder_data'] as $key => $val ) {
			foreach ( $val as $key2 => $val2 ) {
				foreach ( $val2 as $key3 => $val3 ) {
					if ( strpos( $val3['data_key'], '_cb_ex_mk_' ) !== false ) {
						continue;
					}
					if ( $val3['data_type'] == 'meta_key' ) {
						$keys_all[] = $val3['data_key'];
					}
				}

			}
		}
		$keys_all = array_values( array_filter( array_unique( $keys_all ) ) );
	}

	return $keys_all;
}


function isrc_watched_keys_def( $meta_keys ) {

	$meta_keys[] = '_wc_rating_count'; /* comment approve */
	$meta_keys[] = '_wc_average_rating'; /* comment approve */
	$meta_keys[] = '_stock_status'; /* stock change */

	/* get keys from content */
	$cb_keys = isrc_get_content_builder_keys_front();
	if ( ! empty( $cb_keys ) ) {
		$meta_keys = array_merge( $cb_keys, $meta_keys );
	}

	return $meta_keys;
}

add_filter( 'isearch_watch_meta_keys_for_update', 'isrc_watched_keys_def', 10, 1 );