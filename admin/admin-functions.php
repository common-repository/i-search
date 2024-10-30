<?php

/*
 * i-Search admin helper functions file.
 *
 * This file is loaded only in admin. Includes all the shared functions between classes.
 *
 *
 *
 *
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      2.0.0
 */

/**
 * Create a randomly generated string.
 * Is used in different classes to create unique hashes for different use cases.
 * Example: On plugin installation it creates a hash for the regenerate string in the url.
 *
 * @param int $length hash length.
 *
 * @return string    Generated random string.
 * @since 1.0.0
 *
 */
function isrc_randomString( $length = 50 ) {
	$str        = "";
	$characters = array_merge( range( 'A', 'Z' ), range( 'a', 'z' ), range( '0', '9' ) );
	$max        = count( $characters ) - 1;
	for ( $i = 0; $i < $length; $i ++ ) {
		$rand = mt_rand( 0, $max );
		$str  .= $characters[ $rand ];
	}

	return $str;
}

/**
 * Create a randomly generated integer.
 * Is used in different classes to create unique hashes for different use cases.
 *
 * @param int $length hash length.
 *
 * @since 1.0.0
 *
 * @return int    Generated random integer.
 */
function isrc_randomInt( $length = 5 ) {
	$str        = "";
	$characters = array_merge( range( '1', '9' ) );
	$max        = count( $characters ) - 1;
	for ( $i = 0; $i < $length; $i ++ ) {
		$rand = mt_rand( 0, $max );
		$str  .= $characters[ $rand ];
	}

	return $str;
}

/**
 * isrc_checked
 *
 * Compares the first two arguments and if identical marks as checked
 *
 * @since 1.0.0
 *
 * @param mixed $checked One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool  $echo    Whether to echo or just return the string
 *
 * @return string html attribute or empty string
 */
function isrc_checked( $checked, $current = true, $echo = true ) {
	return __isrc_checked_selected_helper( $checked, $current, $echo, 'checked' );
}


/**
 * Outputs the html selected attribute.
 *
 * Compares the first two arguments and if identical marks as checked
 *
 * @since 1.0.0
 *
 * @param mixed $checked One of the values to compare
 * @param mixed $current (true) The other value to compare if not just true
 * @param bool  $echo    Whether to echo or just return the string
 *
 * @return string html attribute or empty string
 */
function isrc_selected( $checked, $current = true, $echo = true ) {
	return __isrc_checked_selected_helper( $checked, $current, $echo, 'selected' );
}

/**
 * Helper function for checked, selected, disabled and readonly.
 *
 * Compares the first two arguments and if identical marks as $type
 *
 * @since 1.0.0
 *
 * @param mixed  $helper  One of the values to compare
 * @param mixed  $current (true) The other value to compare if not just true
 * @param bool   $echo    Whether to echo or just return the string
 * @param string $type    The type of checked|selected|disabled|readonly we are doing
 *
 * @return string html attribute or empty string
 */
function __isrc_checked_selected_helper( $helper, $current, $echo, $type ) {
	$result = '';
	if ( is_array( $helper ) ) {
		if ( in_array( $current, $helper ) ) {
			$result = " $type='$type'";
		}
	} else {
		if ( (string) $helper === (string) $current ) {
			$result = " $type='$type'";
		} else {
			$result = '';
		}
	}

	if ( $echo ) {
		echo $result;
	}

	return $result;
}

/**
 * Remove unwanted bad words based on i-Search admin settings.
 *
 * @since 1.0.0
 *
 * @param string $word     the word to check for.
 * @param string $language language.
 *
 * @return mixed    false if its a bad word | the input word if its not a bad word.
 */
function filter_bad_words( $word, $language = '' ) {

	/* bad words from settings */
	if ( empty( $language ) ) {
		$language = isrc_get_lang_admin();
	}

	$options = get_option( 'isrc_opt_adv_' . $language, false );

	if ( empty( $options ) ) {
		return $word;
	}

	if ( ! isset( $options['isrc_bad_words'] ) || empty( $options['isrc_bad_words'] ) ) {
		$bad_words = array();
	} else {
		$bad_words = isrc_maybe_explode( $options['isrc_bad_words'] );
	}

	/* maybe comma separated string? */
	$word_is_raw = false;

	if ( ! is_array( $word ) && strpos( $word, ',' ) !== false ) {
		$word        = isrc_maybe_explode( $word );
		$word_is_raw = true;
	}

	if ( is_array( $word ) ) {

		$filtered_word_arr = array();

		foreach ( $word as $val ) {
			$filter_it = _filter_bad_words_helper( $val, $bad_words );
			if ( $filter_it ) {
				$filtered_word_arr[] = $filter_it;
			}
		}

		if ( $word_is_raw ) {
			return isrc_implode( $filtered_word_arr );
		}

		return $filtered_word_arr;
	}

	return _filter_bad_words_helper( $word, $bad_words );
}

/**
 * Remove unwanted words to be displayed based on i-Search admin settings.
 *
 * @since 1.0.0
 *
 * @param string $word     the word to check for.
 * @param string $language language.
 *
 * @return mixed    false if its a bad word | the input word if its not a bad word.
 */
function filter_hide_words( $word, $language ) {

	/* bad words from settings */
	$options = get_option( 'isrc_opt_adv_' . $language, false );

	if ( ! isset( $options['isrc_hide_words'] ) || empty( $options['isrc_hide_words'] ) ) {
		$bad_words = array();
	} else {
		$bad_words = isrc_maybe_explode( $options['isrc_hide_words'] );
	}

	/* maybe comma separated string? */
	$word_is_raw = false;

	if ( ! is_array( $word ) && strpos( $word, ',' ) !== false ) {
		$word        = isrc_maybe_explode( $word );
		$word_is_raw = true;
	}

	if ( is_array( $word ) ) {

		$filtered_word_arr = array();

		foreach ( $word as $val ) {
			$filter_it = _filter_bad_words_helper( $val, $bad_words );
			if ( $filter_it ) {
				$filtered_word_arr[] = $filter_it;
			}
		}

		if ( $word_is_raw ) {
			return isrc_implode( $filtered_word_arr );
		}

		return $filtered_word_arr;
	}

	return _filter_bad_words_helper( $word, $bad_words );
}

/**
 * Helper function for filter_bad_words.
 *
 * Helper. Remove unwanted words.
 *
 * @since 1.0.0
 *
 * @param string $word              The word to check for
 * @param array  $bad_words         Extra bad words to compare.
 * @param array  $default_bad_words Default bad words to add to the admin options as default.
 *
 * @return mixed    false if its a bad word | the input word if its not a bad word.
 */
function _filter_bad_words_helper( $word = '', $bad_words = array(), $default_bad_words = array() ) {

	if ( is_array( $word ) ) {
		return false;
	}

	if ( empty( $default_bad_words ) ) {

		$default_bad_words = array(
			'*trashed',
			'default',
			'yes',
			'no',
			'color',
			'instock',
			'taxable',
			'uncategorized',
			'Uncategorized'
		);
	}

	if ( empty( $bad_words ) ) {
		$bad_words = $default_bad_words;
	} else {
		$bad_words = array_merge( $default_bad_words, $bad_words );
		$bad_words = array_unique( $bad_words );
		$bad_words = array_values( $bad_words );
	}

	foreach ( $bad_words as $bad_word ) {
		if ( strpos( $bad_word, '*' ) !== false ) {
			// we have a wild card
			$match = isrc_stringMatchWithWildcard( $word, $bad_word );
			if ( $match ) {
				return false;
			}
		} else {
			// we don't have a wild card

			if ( $word == $bad_word ) {
				return false;
			}
		}
	}

	return $word;
}

/**
 * Check for bad words for search logging.
 *
 * Only used for log actions if enabled in admin.
 *
 * @since 1.0.0
 *
 * @param string $word The word to check for.
 * @param string $lang
 *
 * @return string|boolean    false if its a bad word | the input word if its not a bad word.
 */
function is_bad_log_string( $word = '', $lang = 'en' ) {

	if ( function_exists( 'get_option' ) ) {
		$isrc_log_bad_words = get_option( 'isrc_log_bad_words_' . $lang );
	} else {
		global $isrc_log_bad_words;
	}

	if ( empty( $word ) ) {
		return false;
	}

	/* bad word tags from analyse settings */
	$bad_words = $isrc_log_bad_words;
	$bad_words = isrc_maybe_explode( $bad_words );

	if ( empty( $bad_words ) ) {
		return $word;
	}

	return _filter_bad_words_helper( $word, $bad_words );

}

/**
 * Check if a string have a wild card (*).
 *
 * Used in logs and word exclude based on admin settings.
 *
 * @since 1.0.0
 *
 * @param string $source
 * @param string $pattern
 *
 * @return float
 */
function isrc_stringMatchWithWildcard( $source, $pattern ) {
	$pattern = preg_quote( $pattern, '/' );
	$pattern = str_replace( '\*', '.*', $pattern );

	return preg_match( '/^' . $pattern . '$/i', $source );
}

/**
 * Remove unwanted characters from string
 *
 * @since 1.0.0
 *
 * @param string $string
 *
 * @return string    the clean string.
 */
function isrc_clean_txt( $string ) {

	$word = html_entity_decode( trim( str_replace( array( '  ' ), array( ' ' ), $string ) ) );

	if ( empty( $word ) ) {
		return false;
	}

	return $word;
}

/**
 * Check if taxonomy in language allowed
 *
 * @param int   $taxonomy_id
 * @param array $term_obj
 *
 * @return bool
 */
function isrc_is_taxonomy_allowed( $taxonomy_id = 0, $term_obj = array() ) {

	if ( empty( $taxonomy_id ) || empty( $term_obj ) ) {
		return false;
	}

	$current_taxonomy    = $term_obj->taxonomy;
	$taxonomy_lang       = isrc_get_lang( $taxonomy_id, 'taxonomy', $current_taxonomy );
	$available_opt_langs = isrc_get_lang_codes();

	foreach ( $available_opt_langs as $lang_code ) {
		$options = get_option( 'isrc_opt_' . $lang_code );

		if ( isset( $options['include_in_suggestions'] ) && $taxonomy_lang == $lang_code ) {
			$taxonomies = $options['include_in_suggestions']['taxonomies'];
			if ( in_array( $current_taxonomy, $taxonomies ) ) {
				return true;
			} else {
				return false;
			}

		}
	}

	return false;

}

/**
 * Check if the given post type is allowed for i-Search actions.
 *
 * Used in different classes to insert meta box and save actions.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 *
 * @return boolean
 */
function isrc_is_pt_allowed( $post_id = null ) {

	if ( empty( $post_id ) ) {
		return false;
	}

	$post_lang           = isrc_get_lang( $post_id, 'post' );
	$available_opt_langs = isrc_get_lang_codes();
	$curr_post_type      = get_post_type( $post_id );

	foreach ( $available_opt_langs as $lang_code ) {
		$options = get_option( 'isrc_opt_' . $lang_code );

		if ( isset( $options['include_in_suggestions'] ) && $post_lang == $lang_code ) {
			$post_types = $options['include_in_suggestions']['post_types'];
			if ( in_array( $curr_post_type, $post_types ) ) {
				return true;
			} else {
				return false;
			}

		}
	}

	return false;
}


/**
 * Check if the given post is allowed for i-Search include in algorithm.
 *
 * Make different checks. Post is publish, if woocommerce product is visible etc.
 *
 * @since 1.0.0
 *
 * @param int    $post_id  The post id.
 * @param object $post_obj The post object.
 *
 * @return boolean
 */
function isrc_post_allowed( $post_id, $post_obj ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	if ( empty( $post_id ) ) {
		return false;
	}

	if ( empty( $post_obj ) ) {
		$post_obj = get_post( $post_id );
	}

	if ( empty( $post_obj ) ) {
		return false;
	}

	$post_status = $post_obj->post_status;

	if ( $post_status != 'publish' && $post_obj->post_type != 'attachment' ) {
		return false;
	}

	if ( $post_status != 'inherit' && $post_obj->post_type == 'attachment' ) {
		return false;
	}

	/* is PW protected? */
	if ( isset( $post_obj->post_password ) && ! empty( $post_obj->post_password ) ) {
		return false;
	}

	/* is post type allowed in settings? */
	if ( ! isrc_is_pt_allowed( $post_id ) ) {
		return false;
	}

	/* is woocommerce installed and is ist a product? Check visibility */
	if ( ! _check_woo_visibility( $post_id, $post_obj ) ) {
		return false;
	}

	return true;
}


/**
 * Helper for isrc_post_allowed.
 *
 * Check if we have woocommerce installed and make product visibility check.
 *
 * @since 1.0.0
 *
 * @param int    $post_id  The post id.
 * @param object $post_obj The post object.
 *
 * @return boolean
 */
function _check_woo_visibility( $post_id, $post_obj ) {

	/* if we don't have woocommerce installed return true */
	if ( ! defined( "ISRC_WOOCOMMERCE_INSTALLED" ) ) {
		return true;
	}

	/* check if its a product */
	if ( $post_obj->post_type != 'product' ) {
		/* its not a product return true */
		return true;
	}

	$terms = get_the_terms( $post_id, 'product_visibility' );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) && is_array( $terms ) ) {

		foreach ( $terms as $term ) {

			if ( $term->slug == 'exclude-from-catalog' || $term->slug == 'exclude-from-search' ) {
				return false;
			}
		}

	}

	return true;
}

/**
 * Explode string to array.
 *
 * Make different checks to avoid php errors and returns an array.
 *
 * @since 1.0.0
 *
 * @param string $string    The string to explode.
 * @param string $delimiter The delimiter for explode.
 *
 * @return array|string
 */
function isrc_maybe_explode( $string = null, $delimiter = ',' ) {

	if ( empty( $string ) ) {
		return array();
	}

	if ( is_array( $string ) ) {
		return $string;
	}

	$arr = explode( $delimiter, $string );
	if ( empty( $arr ) || ! is_array( $arr ) ) {
		return $string;
	}

	$arr = array_values( array_filter( array_unique( $arr ) ) );

	return $arr;
}

/**
 * Filter out duplicate values in array.
 *
 * Make different checks to avoid php errors, convert string with delimiters to array.
 *
 * @since 1.0.0
 *
 * @param mixed $string The string or array to filter.
 *
 * @return mixed    Array with unique values if the input is an array | String with delimiter if the input is a string.
 */
function isrc_filter_duplicates( $string = null ) {

	if ( empty( $string ) ) {
		return $string;
	}

	if ( is_array( $string ) ) {
		$string_is_array = true;
		$arr             = $string;
	} else {
		$arr             = isrc_maybe_explode( $string );
		$string_is_array = false;
	}

	$unique = array();
	foreach ( $arr as $value ) {
		$unique[ sanitize_title( $value ) ] = $value;
	}

	$arr = array_values( array_filter( array_unique( $unique ) ) );

	/* the source was not an array. Convert to string */
	if ( ! $string_is_array ) {
		return isrc_implode( $arr );
	}

	return $arr;
}

/**
 * Implode array to string.
 *
 * Make different checks to avoid php errors.
 *
 * @since 1.0.0
 *
 * @param array  $array The array to implode.
 * @param string $glue  The glue for implode.
 *
 * @return array|string
 */
function isrc_implode( $array = array(), $glue = ',' ) {

	if ( empty( $array ) ) {
		return '';
	}

	if ( ! is_array( $array ) ) {
		return $array;
	}

	$array = array_values( array_filter( array_unique( $array ) ) );

	$string = implode( $glue, $array );

	return $string;
}

/**
 * Get the admin selected tags and categories to include in algorithm from the settings.
 *
 * @since 1.0.0
 *
 * @return array    An array of included tags and categories.
 */
function get_tags_and_categories() {
	global $isrc_opt;

	if ( $isrc_opt && isset( $isrc_opt['includes'] ) ) {
		return $isrc_opt['includes'];
	}

	return __return_empty_array();

}

/**
 * Get the admin selected meta_keys to include in algorithm from the settings.
 *
 * @since 1.0.0
 *
 * @param $lang
 *
 * @return array    An array of included meta keys.
 */
function get_adv_meta_keys( $lang ) {

	if ( empty( $lang ) ) {
		$lang = isrc_get_lang_admin();
	}

	$isrc_opt_adv = get_option( 'isrc_opt_adv_' . $lang, false );

	if ( $isrc_opt_adv && isset( $isrc_opt_adv['meta_inc'] ) ) {
		return $isrc_opt_adv['meta_inc'];
	}

	return __return_empty_array();

}

/**
 * Get the admin selected taxonomies to include in algorithm from the settings.
 *
 * @since 1.0.0
 *
 * @param $lang
 *
 * @return array    An array of included taxonomies.
 */
function get_adv_taxonomies( $lang ) {

	if ( empty( $lang ) ) {
		$lang = isrc_get_lang_admin();
	}

	$isrc_opt_adv = get_option( 'isrc_opt_adv_' . $lang, false );

	if ( $isrc_opt_adv && isset( $isrc_opt_adv['taxonomy_includes'] ) ) {
		return $isrc_opt_adv['taxonomy_includes'];
	}

	return __return_empty_array();

}

/**
 * Update the posts specified search terms in the database.
 *
 * Called on post update or Re-indexing.
 *
 * @since 1.0.0
 *
 * @param int   $post_id  The post id.
 * @param array $post_obj The post object.
 *
 * @return boolean
 */
function update_post_isrc( $post_id, $post_obj = array() ) {
	global $isrc_main;

	if ( empty( $isrc_main ) ) {
		/* on auto comment approve $isrc_main is not initialized. Do it */
		$isrc_main = new isrc_main();
	}


	if ( empty( $post_obj ) ) {
		$post_obj = get_post( $post_id );
	}

	if ( $parent_id = wp_is_post_revision( $post_id ) ) {
		$post_id = $parent_id;
	}

	$is_allowed = isrc_post_allowed( $post_id, $post_obj );

	if ( ! $is_allowed ) {
		delete_isearch( $post_id );
		update_post_meta( $post_id, '_isrc_all', array( 'post_is_not_allowed' => true ) );

		return false;
	}

	$data = get_post_meta( $post_id, '_isrc', true );

	/* apply filters? */
	$isrc_no_filter = ( isset( $data['isrc_no_filter'] ) && $data['isrc_no_filter'] == '1' ) ? true : false;

	$new_data               = array();
	$new_data['isrc_sh']    = isset( $data['isrc_sh'] ) ? $data['isrc_sh'] : '1';
	$new_data['isrc_terms'] = isset( $data['isrc_terms'] ) ? isrc_maybe_explode( $data['isrc_terms'] ) : array();

	if ( $new_data['isrc_sh'] == '0' ) {
		/* delete if isrc is disabled for this post */
		delete_isearch( $post_id );

		return false;
	}

	$extra_data = array();
	$extra_data = apply_filters( 'isrc_post_extra_terms', $extra_data, $post_id );

	/* add post title */
	$title        = $post_obj->post_title;
	$extra_data[] = $title;

	$terms = $new_data['isrc_terms'];

	if ( ! is_array( $terms ) ) {
		$terms = array();
	}

	$lang = isrc_get_lang( $post_id, 'post' );

	/* apply meta keys? */
	if ( $isrc_no_filter ) {
		$extra_meta_keys = array();
	} else {
		$extra_meta_keys = get_adv_meta_keys( $lang );
	}

	if ( ! empty( $extra_meta_keys ) ) {

		foreach ( $extra_meta_keys as $meta_key ) {
			/* ACF */
			if ( strpos( $meta_key, 'field_' ) !== false && function_exists( 'get_field' ) ) {
				$temp = isrc_get_acf_value( $post_id, $meta_key );
				if ( ! empty( $temp ) && is_array( $temp ) ) {
					$extra_data = array_merge( $extra_data, $temp );
				}
			} else {
				$meta_val = get_post_meta( $post_id, $meta_key, true );
				if ( $meta_val ) {
					$extra_data[] = $meta_val;
				}

			}
		}
	}

	/* apply settings for tags, categories? */
	if ( $isrc_no_filter ) {
		$extra_taxonomy_keys = array();
	} else {
		$extra_taxonomy_keys = get_tags_and_categories();
	}

	if ( ! empty( $extra_taxonomy_keys ) ) {
		foreach ( $extra_taxonomy_keys as $taxonomy ) {
			$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
			if ( ! empty( $taxonomy_terms ) ) {
				foreach ( $taxonomy_terms as $taxonomy_term ) {
					$extra_data[] = $taxonomy_term->name;
				}
			}
		}
	}

	/* apply taxonomies? */
	if ( $isrc_no_filter ) {
		$extra_taxonomy_keys = array();
	} else {
		$extra_taxonomy_keys = get_adv_taxonomies( $lang );
	}

	if ( ! empty( $extra_taxonomy_keys ) ) {
		foreach ( $extra_taxonomy_keys as $taxonomy ) {
			$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
			if ( ! empty( $taxonomy_terms ) ) {
				foreach ( $taxonomy_terms as $taxonomy_term ) {
					$extra_data[] = $taxonomy_term->name;
				}
			}
		}
	}

	if ( ! empty( $extra_data ) ) {
		$terms = array_merge( $extra_data, $terms );
	}

	$temp = array();
	foreach ( $terms as $term ) {
		$temp[] = trim( $term );
	}

	$terms          = $temp;
	$terms          = apply_filters( 'isrc_post_before_insert', $terms, $post_id, $data );
	$terms          = array_values( array_filter( $terms ) );
	$inserted_terms = $isrc_main->insert_isearch( $post_id, $terms, $title );

	/* sort terms for better readability */
	sort( $inserted_terms, SORT_NATURAL | SORT_FLAG_CASE );

	/* update post meta based on the real search data */
	update_post_meta( $post_id, '_isrc_all', isrc_filter_duplicates( filter_bad_words( $inserted_terms, $lang ) ) );

	return true;
}

/**
 * Update the taxonomy specified search terms in the database.
 *
 * Called only if a taxonomy term is NOT created from the wp-admin. Like ACF plugin will create terms from outside.
 * Info: Save options for a taxonomy term is called in class-admin-main.php::save_the_taxonomy_extra_form
 *
 * @since 1.0.0
 *
 * @param int $taxonomy_id The taxonomy id.
 *
 * @return boolean
 */
function isrc_update_taxonomy_meta( $taxonomy_id ) {
	global $isrc_main;

	if ( empty( $taxonomy_id ) ) {
		return false;
	}

	$taxonomy_id = (int) $taxonomy_id;

	/* first get meta data for taxonomy */
	$metadata = isrc_get_taxonomy_meta( $taxonomy_id );

	if ( $metadata === false ) {
		/* its the first time */
		/* do nothing */
	} else {
		/* is this taxonomy enabled or disabled in meta data? */
		if ( isset( $metadata['exclude_src'] ) && $metadata['exclude_src'] == '1' ) {
			/* its disabled delete it also from db */
			delete_isearch( $taxonomy_id, 'taxonomy' );

			return false;
		}
	}

	$insert_data = array();

	$term = get_term( $taxonomy_id );
	isrc_is_taxonomy_allowed( $taxonomy_id, $term );

	if ( empty( $term ) ) {
		return false;
	}

	/* get the title and add to the terms array */
	$title         = $term->name;
	$insert_data[] = $title;

	/* extra search terms? */
	if ( isset( $metadata['isrc_extra_terms'] ) && is_array( $metadata['isrc_extra_terms'] ) ) {
		$insert_data = array_merge( $insert_data, $metadata['isrc_extra_terms'] );
	}

	$insert_data = apply_filters( 'isrc_taxonomy_before_insert', $insert_data, $taxonomy_id, $term );
	$isrc_main->insert_isearch_taxonomy( $taxonomy_id, isrc_filter_duplicates( $insert_data ), $title );

	return true;

}

/**
 * @return bool|mixed|string|void
 */
function isrc_get_lang_admin() {
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
 * get available lang codes for the option from db.
 *
 * @param string $option_name
 *
 * @return array
 */
function isrc_get_lang_codes( $option_name = 'isrc_opt_' ) {
	global $wpdb;

	$regexp  = '"^' . $option_name . '[a-z]{2}$"';
	$results = $wpdb->get_results( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name REGEXP {$regexp}", ARRAY_A );
	if ( empty( $results ) ) {
		return array();
	}
	$lang_codes = array();
	foreach ( $results as $optnames ) {
		$opt_names    = $optnames['option_name'];
		$lang_codes[] = substr( $opt_names, - 2 );
	}

	return $lang_codes;
}

/**
 * Get language code for post or taxonomy if ml plugins installed
 *
 * @param int    $post_id
 * @param string $post_or_tax
 * @param string $post_type
 *
 * @return array|bool|string
 */
function isrc_get_lang( $post_id = 0, $post_or_tax = 'post', $post_type = '' ) {

	if ( $post_or_tax == 'post' ) {
		/* its a post */

		/*
		 * WPML
		 */
		if ( function_exists( 'wpml_get_language_information' ) ) {
			/* wpml is installed */
			$lang = apply_filters( 'wpml_post_language_details', null, $post_id );
			if ( ! is_wp_error( $lang ) && isset( $lang['language_code'] ) && ! empty( $lang['language_code'] ) ) {
				return $lang['language_code'];
			} elseif ( isset( $_POST['icl_post_language'] ) && ! empty( $_POST['icl_post_language'] ) ) {
				return $_POST['icl_post_language'];
			}
		}

	}

	if ( $post_or_tax == 'taxonomy' ) {
		/* its a taxonomy */

		/*
		 * WPML
		 */
		if ( function_exists( 'wpml_element_language_code_filter' ) ) {
			/* wpml is installed */
			$lang = wpml_element_language_code_filter( null, array( 'element_id' => (int) $post_id, 'element_type' => $post_type ) );
			if ( ! empty( $lang ) ) {
				return $lang;
			}
		}

	}

	return substr( get_locale(), 0, 2 );
}

/**
 * Delete the shortcode in the database.
 *
 * @since 2.0.0
 *
 * @param int $sc_id The shortcode id.
 *
 * @return boolean
 */
function delete_isearch_shortcode( $sc_id = null ) {
	global $wpdb;

	if ( empty( $sc_id ) ) {
		return false;
	}

	$query = "DELETE FROM {$wpdb->prefix}isearch_shortcodes WHERE id = {$sc_id}";
	$wpdb->query( $query );

	return true;
}

/**
 * Delete the post/taxonomy form the search algorithm in the database.
 *
 * @since 1.0.0
 *
 * @param int    $post_id The post id.
 * @param string $type    Post Type or Taxonomy.
 *
 * @return boolean
 */
function delete_isearch( $post_id, $type = 'post_type' ) {
	global $wpdb;

	if ( empty( $post_id ) ) {
		return false;
	}

	if ( $type == 'post_type' ) {
		$query = "DELETE FROM {$wpdb->prefix}isearch WHERE post_id = {$post_id}";
		$wpdb->query( $query );

		/* delete also the popularity index */
		$query = "DELETE FROM {$wpdb->prefix}isearch_popular WHERE id = {$post_id} AND type = 'post_type'";
		$wpdb->query( $query );

		return true;
	}

	/* Delete for taxonomy */
	if ( $type == 'taxonomy' ) {
		$taxonomy_id = (int) $post_id;

		if ( empty( $taxonomy_id ) ) {
			return false;
		}

		$query = "DELETE FROM {$wpdb->prefix}isearch_taxonomy WHERE post_id = {$taxonomy_id}";
		$wpdb->query( $query );

		/* delete also the popularity index */
		$query = "DELETE FROM {$wpdb->prefix}isearch_popular WHERE id = {$taxonomy_id} AND type = 'taxonomy'";
		$wpdb->query( $query );

		return true;
	}

	return false;
}

/**
 * Get the shortcode data from the database.
 *
 * @since 2.0.0
 *
 * @param int  $sc_id The shortcode id.
 * @param bool $alldata
 *
 * @return array
 */
function get_isearch_shortcode_data( $sc_id = null, $alldata = false ) {
	global $wpdb;

	if ( empty( $sc_id ) ) {
		return array();
	}

	if ( $alldata ) {
		$query   = "SELECT * FROM {$wpdb->prefix}isearch_shortcodes WHERE id = %d";
		$results = $wpdb->get_results( $wpdb->prepare( $query, $sc_id ), ARRAY_A );

		return $results;
	} else {
		$query   = "SELECT settings FROM {$wpdb->prefix}isearch_shortcodes WHERE id = %d";
		$results = $wpdb->get_var( $wpdb->prepare( $query, $sc_id ) );
		if ( ! empty( $results ) ) {
			return maybe_unserialize( $results );
		}
	}

	return array();
}

/**
 * Compares the hashes and warn the admin for re-indexing if important settings changed.
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function isrc_need_attention() {

	$index_hash    = get_option( 'isrc_att_hash_ind_' . isrc_get_lang_admin(), rand() );
	$settings_hash = get_option( 'isrc_att_hash_set_' . isrc_get_lang_admin(), rand() );

	if ( $settings_hash != $index_hash ) {
		return true;
	}

	return false;
}

/**
 * Based on admin settings a hash will be created.
 * Only for algorithm and search results important settings will be covered to build the hash.
 *
 * @since 1.0.0
 *
 * @param boolean $update Define if its a dry check or need to update in the options.
 *
 * @return string    The hash if $update is true.
 */
function isrc_build_attention_hash( $update = true ) {

	/* do not use global. We are using redirect and need fresh options data before redirect */
	$isrc_opt_adv     = get_option( 'isrc_opt_adv_' . isrc_get_lang_admin() );
	$isrc_opt         = get_option( 'isrc_opt_' . isrc_get_lang_admin() );
	$isrc_opt_content = get_option( 'isrc_opt_content_' . isrc_get_lang_admin() );

	/* general settings */
	$general_options = $isrc_opt;
	$hash_arr        = '';

	if ( isset( $general_options['post_types'] ) ) {
		$hash_arr .= md5( json_encode( $general_options['post_types'] ) );
	}

	if ( isset( $general_options['include_in_suggestions'] ) && isset( $general_options['front']['tabs_ed'] ) ) {
		/* no need to reindex if the order changed */
		$include_in_suggestions = array();
		if ( isset( $general_options['include_in_suggestions']['post_types'] ) ) {
			$include_in_suggestions = array_merge( $include_in_suggestions, $general_options['include_in_suggestions']['post_types'] );
		}

		if ( isset( $general_options['include_in_suggestions']['taxonomies'] ) ) {
			$include_in_suggestions = array_merge( $include_in_suggestions, $general_options['include_in_suggestions']['taxonomies'] );
		}
		/* sort natural to get always the same result if the values not changed but the order changed. because we handle the order in JS */
		if ( ! empty( $include_in_suggestions ) ) {
			asort( $include_in_suggestions );
			$include_in_suggestions = array_values( $include_in_suggestions );
			$hash_arr               .= md5( json_encode( $include_in_suggestions ) );
		}
	}

	if ( isset( $general_options['includes'] ) ) {
		$hash_arr .= md5( json_encode( $general_options['includes'] ) );
	}

	if ( isset( $general_options['woo'] ) ) {
		unset( $general_options['woo']['cats_l'] );
		unset( $general_options['woo']['outofstock_l'] );
		unset( $general_options['woo']['sale_l'] );
		unset( $general_options['woo']['featured_l'] );
		$hash_arr .= md5( json_encode( $general_options['woo'] ) );
	}

	if ( isset( $general_options['front']['thumb_size'] ) ) {
		$hash_arr .= md5( json_encode( $general_options['front']['thumb_size'] ) );
	}

	if ( isset( $general_options['front']['img'] ) ) {
		$hash_arr .= md5( json_encode( array( 'img' ) ) );
	}

	if ( isset( $general_options['front']['excerpt'] ) ) {
		$hash_arr .= md5( json_encode( array( 'excerpt' ) ) );
	}

	if ( isset( $general_options['front']['cat'] ) ) {
		$hash_arr .= md5( json_encode( array( 'front_cat' ) ) );
	}

	$hash_arr = md5( $hash_arr );

	/* advanced settings */
	$advanced_options = $isrc_opt_adv;

	if ( isset( $advanced_options['meta_inc'] ) ) {
		$hash_arr .= md5( json_encode( $advanced_options['meta_inc'] ) );
	}

	if ( isset( $advanced_options['taxonomy_includes'] ) ) {
		$hash_arr .= md5( json_encode( $advanced_options['taxonomy_includes'] ) );
	}

	if ( isset( $advanced_options['isrc_bad_words'] ) && ! empty( trim( $advanced_options['isrc_bad_words'] ) ) ) {
		$hash_arr .= md5( json_encode( $advanced_options['isrc_bad_words'] ) );
	}

	if ( isset( $advanced_options['isrc_hide_words'] ) && ! empty( trim( $advanced_options['isrc_hide_words'] ) ) ) {
		$hash_arr .= md5( json_encode( $advanced_options['isrc_hide_words'] ) );
	}

	if ( isset( $advanced_options['exclude_hash_check'] ) && ! empty( $advanced_options['exclude_hash_check'] ) ) {
		$hash_arr .= md5( json_encode( $advanced_options['exclude_hash_check'] ) );
	}


	$att_hash = md5( $hash_arr );

	if ( $update ) {
		update_option( 'isrc_att_hash_set_' . isrc_get_lang_admin(), $att_hash );

		/* seperate content builder hash */
		if ( isset( $isrc_opt_content['hash'] ) ) {
			$cb_hash = md5( json_encode( $isrc_opt_content['hash'] ) );
			update_option( 'isrc_cb_att_hash_set_' . isrc_get_lang_admin(), $cb_hash );
		}

	}

	return $att_hash;
}

/**
 * Get the i-Search default thumbnails if post/taxonomy have no image.
 *
 * @since 1.0.0
 *
 * @param string $sizename
 *
 * @return string    The default thumbnail url.
 */
function isrc_get_def_thumb( $sizename = 'full' ) {

	return ISRC_PLUGIN_URL . '/front/css/img/blank50.png';

}

/**
 * Helper for edit_meaning_actions
 * Add a 'Did you mean' string to the log.
 *
 * @since 1.0.0
 *
 * @param array $data
 *
 * @return array|bool
 */
function _isrc_update_dym_string( $data = array() ) {
	$error = array(
		'success' => false,
		'msg'     => __( 'Only in PRO version.', 'i_search' )
	);

	return $error;
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
		return;
		error_log( $data );
	}
}

/**
 * Helper for edit_meaning_actions
 * Add the string to posts extra search terms.
 *
 * @since 1.0.0
 *
 * @param int   $log_id
 * @param array $selections
 *
 * @return array|bool
 */
function isrc_add_string_to_post_terms( $log_id = 0, $selections = array() ) {
	global $wpdb;

	if ( empty( $selections ) || ! is_array( $selections ) ) {

		$error = array(
			'success' => false,
			'msg'     => __( 'Please select a post.', 'i_search' )
		);

		return $error;

	}

	/* always get the data from DB not from POST */
	$db_query  = $wpdb->prepare( "SELECT src_query FROM {$wpdb->prefix}isearch_logs WHERE id = %d", $log_id );
	$db_result = $wpdb->get_row( $db_query, ARRAY_A );

	if ( empty( $db_result ) ) {

		$error = array(
			'success' => false,
			'msg'     => __( 'Can not find the log in the DB. Please reload the page and try again.', 'i_search' )
		);

		return $error;

	}

	$src_query = $db_result['src_query'];

	foreach ( $selections as $selection ) {
		/* is it a taxonomy? */
		if ( $selection['type'] == 'taxonomy' ) {
			isrc_add_string_to_taxonomy_terms( $selection, $src_query );
			continue;
		}

		$post_id = $selection['id'];
		$meta    = get_post_meta( $post_id, '_isrc', true );

		if ( isset( $meta['isrc_terms'] ) ) {
			$isrc_post_keys     = isrc_maybe_explode( $meta['isrc_terms'] );
			$isrc_post_keys[]   = $src_query;
			$meta['isrc_terms'] = $isrc_post_keys;
		} else {
			$meta = array( 'isrc_terms' => array( $src_query ) );
		}

		update_post_meta( $post_id, '_isrc', $meta );
		isrc_debug_log( 'Func: isrc_add_string_to_post_terms post_id: ' . $post_id );
		update_post_isrc( $post_id );
	}

	/* delete the log */
	isrc_delete_log( $log_id );

	return true;
}

/**
 * Helper for isrc_add_string_to_post_terms
 * Add the string to taxonomies extra search terms.
 *
 * @since 1.0.0
 *
 * @param array  $data
 * @param string $keyword The term to add
 *
 * @return boolean
 */
function isrc_add_string_to_taxonomy_terms( $data, $keyword ) {

	/* check the data */
	if ( ! isset( $data['id'] ) || ! isset( $data['post_type'] ) ) {
		return false;
	}

	$taxonomy_id = $data['id'];
	$taxonomy    = $data['post_type'];

	if ( empty( $taxonomy ) || empty( $taxonomy_id ) ) {
		return false;
	}

	/* get the current metas */
	$current_meta = isrc_get_taxonomy_meta( $taxonomy_id );
	if ( false == $current_meta ) {
		/* there is no meta data its the first meta */
		$metadata = array( 'isrc_extra_terms' => array( $keyword ) );
		$update   = update_isrc_metadata( $taxonomy_id, $metadata );

		return $update;
	}

	/* there is already a meta data check and add */
	$current_terms                    = $current_meta['isrc_extra_terms'];
	$current_terms[]                  = $keyword;
	$new_terms                        = isrc_filter_duplicates( $current_terms );
	$current_meta['isrc_extra_terms'] = $new_terms;
	$update                           = update_isrc_metadata( $taxonomy_id, $current_meta );

	return $update;
}


/**
 * Delete the log from DB.
 *
 * @since 1.0.0
 *
 * @param mixed $data array with 'log_id' or int as log_id
 *
 * @return boolean
 */
function isrc_delete_log( $data ) {
	global $wpdb;

	if ( is_array( $data ) && isset( $data['log_id'] ) && ! empty( $data['log_id'] ) ) {
		$log_id = (int) $data['log_id'];
	}

	if ( ! is_array( $data ) ) {
		$log_id = (int) $data;
	}

	if ( empty( $log_id ) ) {
		return false;
	}

	/* delete log */
	$deleted = $wpdb->delete( "{$wpdb->prefix}isearch_logs", array( 'id' => $log_id ) );
	if ( $deleted !== false ) {
		return true;
	} else {
		return false;
	}

}

/**
 * Helper function for isrc_recheck_logs
 * is called from the wp-list-table. Add some params and call the real function.
 *
 * @since 1.0.0
 *
 * @param array $log_data array with database log results.
 *
 * @return boolean    true if logs are deleted.
 */
function isrc_recheck_logs_by_results( $log_data = array() ) {

	if ( empty( $log_data ) || ! is_array( $log_data ) ) {
		return false;
	}

	return isrc_recheck_logs( $limit = 100, $offset = 0, $log_data );
}

/**
 * Called after re-indexing to check if the log files are still up-to-date
 * Checks every log file for search results. if a result is found then delete it because the new index have a result no need for log.
 *
 * @since 1.0.0
 *
 * @param int   $limit    limit for DB query.
 * @param int   $offset   offset for DB query.
 * @param array $log_data array with database log results if available and called from helper function to prevent double DB access.
 *
 * @return array|bool|object
 */
function isrc_recheck_logs( $limit = 100, $offset = 0, $log_data = array() ) {
	global $wpdb;

	if ( empty( $log_data ) ) {
		$db_query    = $wpdb->prepare( "SELECT id,src_query,instance FROM {$wpdb->prefix}isearch_logs LIMIT %d OFFSET %d", $limit, $offset );
		$log_results = $wpdb->get_results( $db_query, ARRAY_A );
	} else {
		$log_results = $log_data;
	}

	if ( empty( $log_results ) ) {
		return false;
	}

	/* make a search like a human */
	$isrc_ajax_class = new isrc_ajax_class();

	foreach ( $log_results as $key => $val ) {
		$shortcode_ids = $val['instance'];
		$shortcode_ids = array_unique( array_filter( explode( ',', $shortcode_ids ) ) );
		$log_id        = $val['id'];
		foreach ( $shortcode_ids as $keysc => $instance_id ) {
			$shortcode_data = get_isearch_shortcode_data( $instance_id );

			/* skip if shortcode have no search in data */
			if ( ! isset( $shortcode_data['search_in'] ) || empty( $shortcode_data['search_in'] ) ) {
				continue;
			}

			$locale                           = $shortcode_data['locale'];
			$search_in                        = implode( ',', array_keys( $shortcode_data['search_in'] ) );
			$isrc_ajax_class->locale          = $locale;
			$isrc_ajax_class->instance        = $instance_id;
			$isrc_ajax_class->logging         = false;
			$isrc_ajax_class->log_popularity  = false;
			$isrc_ajax_class->show_popularity = false;
			$isrc_ajax_class->show_didumeans  = false;
			$isrc_ajax_class->set_customized_post_types( $search_in );
			$search_query  = array(
				'query'           => $val['src_query'],
				'format'          => 'array',
				'disable_logging' => true
			);
			$search_result = (array) $isrc_ajax_class->ajax_search_posts( $search_query );
			$suggestions   = $search_result['suggestions'];
			$have_results  = ( ! empty( $suggestions ) ) ? true : false;
			/* if have results delete only insatnce id from log or if only one instance id than delete log from db */

			if ( $have_results ) {
				unset( $shortcode_ids[ $keysc ] );

				/* delete instance id or log? */
				if ( count( $shortcode_ids ) > 0 ) {
					/* we have more instances on one log id. delete the instance id only */
					$wpdb->update(
						"{$wpdb->prefix}isearch_logs",
						array(
							'instance' => implode( ',', $shortcode_ids ),
						),
						array( 'id' => $log_id ),
						array(
							'%s',
						),
						array( '%d' )
					);
				} else {
					/* we have only one instance and a result. Delete the log */
					isrc_delete_log( $log_id );
					$log_results[ $key ]['status'] = __( 'Success... Deleting log.', 'i_search' );
				}
			} else {
				/* no results found */
				$log_results[ $key ]['status'] = __( 'No results found.', 'i_search' );
			}
		}

	}

	return $log_results;
}

/**
 * Update the taxonomy data in the DB
 * Taxonomy data is special for i-Search and are saved in a extra table. WP do not support taxonomy meta data.
 *
 * @since 1.0.0
 *
 * @param int   $meta_id the taxonomy id.
 * @param array $value   taxonomy meta data as array.
 *
 * @return boolean
 */
function update_isrc_metadata( $meta_id = 0, $value = array() ) {
	global $wpdb;

	$meta_id = (int) $meta_id;

	if ( empty( $meta_id ) ) {
		return false;
	}

	$term_data = get_term( $meta_id );
	if ( ! empty( $term_data ) && ! is_wp_error( $term_data ) ) {
		$taxonomy_name = $term_data->taxonomy;
		$lang          = isrc_get_lang( $meta_id, 'taxonomy', $taxonomy_name );
	} else {
		$lang = isrc_get_lang_admin();
	}

	isrc_delete_taxonomy_meta( $meta_id );

	$wpdb->insert( "{$wpdb->prefix}isearch_metadata",
		array(
			'meta_id'      => $meta_id,
			'option_value' => maybe_serialize( $value ),
			'lang'         => $lang,
		),
		array(
			'%d',
			'%s',
			'%s',
		)
	);

	return isrc_update_taxonomy_meta( $meta_id );

}

/**
 * Delete the taxonomy data in the DB
 * Taxonomy data is special for i-Search and are saved in a extra table. WP do not support taxonomy meta data.
 *
 * @since 1.0.0
 *
 * @param int $meta_id the taxonomy id.
 *
 * @return boolean
 */
function isrc_delete_taxonomy_meta( $meta_id = null ) {
	global $wpdb;

	$meta_id = (int) $meta_id;

	if ( empty( $meta_id ) ) {
		return false;
	}

	$query = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}isearch_metadata WHERE meta_id = '%d'", $meta_id );

	return $wpdb->query( $query );

}

/**
 * Get the taxonomy meta data from DB
 *
 * @since 1.0.0
 *
 * @param int $meta_id the taxonomy id.
 *
 * @return mixed    The meta data as array or false if no meta data is available.
 */
function isrc_get_taxonomy_meta( $meta_id ) {
	global $wpdb;

	$db_query = $wpdb->prepare( "SELECT option_value FROM {$wpdb->prefix}isearch_metadata WHERE meta_id = '%d'", $meta_id );
	$results  = $wpdb->get_row( $db_query, ARRAY_A );

	if ( empty( $results ) ) {
		return false;
	}

	return maybe_unserialize( $results['option_value'] );

}

/**
 * Get the popular searched posts from DB
 *
 * @since 1.0.0
 *
 * @param int    $limit      Limit for DB query.
 * @param array  $post_types Post types.
 * @param array  $taxonomies Taxonomies.
 * @param string $lang
 *
 * @return mixed    The results as array or false if no data is available.
 */
function isrc_get_popular_searches( $limit = 10, $post_types = array(), $taxonomies = array(), $lang = 'en', $output = '*' ) {
	global $wpdb;

	$sql  = $wpdb->prepare( "SELECT {$output} FROM {$wpdb->prefix}isearch_popular WHERE lang = %s", $lang );
	$ptn  = array();
	$type = array();

	if ( ! empty( $post_types ) ) {
		$ptn    = array_merge( $post_types, $ptn );
		$type[] = 'post_type';
	}
	if ( ! empty( $taxonomies ) ) {
		$ptn    = array_merge( $taxonomies, $ptn );
		$type[] = 'taxonomy';
	}

	if ( ! empty( $ptn ) && ! empty( $type ) ) {
		$how_many     = count( $ptn );
		$placeholders = array_fill( 0, $how_many, '%s' );
		$ptn_format   = implode( ', ', $placeholders );

		$how_many          = count( $type );
		$placeholders      = array_fill( 0, $how_many, '%s' );
		$post_types_format = implode( ', ', $placeholders );

		$sql_prep = " AND type IN({$post_types_format}) AND ptn IN({$ptn_format})";
		$sql      .= $wpdb->prepare( $sql_prep, array_merge( $type, $ptn ) );
	}

	$db_query = $wpdb->prepare( "{$sql} ORDER BY time DESC, hit DESC LIMIT %d", $limit );
	$results  = $wpdb->get_results( $db_query, ARRAY_A );

	if ( empty( $results ) ) {
		return false;
	}

	return $results;
}

/**
 * Extra security check based on WP capabilities
 *
 * @since 1.0.0
 *
 * @return boolean
 */
function i_src_security_check() {

	if ( current_user_can( ISRC_SECURITY_CAPABILITIES ) ) {
		return true;
	}

	return false;
}

/**
 * Get the taxonomy image id from taxonomy meta data
 *
 * @since 1.0.0
 *
 * @param int $taxonomy_id The taxonomy id.
 *
 * @return mixed    Image_id if available else false.
 */
function i_src_get_taxonomy_image_id( $taxonomy_id = 0 ) {
	global $wpdb;

	if ( empty( $taxonomy_id ) ) {
		return false;
	}

	$db_query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}isearch_metadata WHERE meta_id = %d", $taxonomy_id );
	$results  = $wpdb->get_row( $db_query, ARRAY_A );

	if ( empty( $results ) ) {
		return false;
	}

	$meta_data = maybe_unserialize( $results['option_value'] );

	if ( isset( $meta_data['isrc_img_id'] ) ) {
		return $meta_data['isrc_img_id'];
	}

	return false;
}


/**
 * Fill the temporary database table with all the ids needed for indexing.
 * Function is called only in re-index screen
 *
 * @since 1.0.0
 *
 * @param array  $include_in_suggestions Type of data. Post type or taxonomy.
 *
 * @param string $source
 *
 *
 * @return void
 */
function fill_temp_ids( $include_in_suggestions, $source = 'all' ) {
	global $wpdb;

	/* empty isearch table */
	if ( $source == 'all' ) {

		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}isearch_taxonomy" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}isearch" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}isearch_temp" );

	} elseif ( $source == 'cb' && isset( $include_in_suggestions['post_types'] ) && is_array( $include_in_suggestions['post_types'] ) ) {
		/* empty only needed */

		if ( isset( $_GET['lang'] ) ) {
			$lang = esc_attr( wp_unslash( $_GET['lang'] ) );
		} else {
			$lang = isrc_get_lang_admin();
		}

		$pts_in                = $include_in_suggestions['post_types'];
		$placeholders_to_sting = implode( ', ', array_fill( 0, count( $pts_in ), '%s' ) );
		$query                 = "DELETE FROM {$wpdb->prefix}isearch WHERE post_type IN ({$placeholders_to_sting}) AND lang  = '{$lang}'";
		$wpdb->query( $wpdb->prepare( $query, $pts_in ) );
		_fill_temp_ids_for_post( $pts_in );

		return;
	}

	if ( isset( $include_in_suggestions['post_types'] ) ) {
		$post_types = $include_in_suggestions['post_types'];
		if ( ! empty( $post_types ) ) {
			_fill_temp_ids_for_post( $post_types );
		}
	}

	if ( isset( $include_in_suggestions['taxonomies'] ) ) {
		$taxonomies = $include_in_suggestions['taxonomies'];
		if ( ! empty( $taxonomies ) ) {
			_fill_temp_ids_for_taxonomies( $taxonomies );
		}
	}

}

/**
 * Helper function for fill_temp_ids.
 * Is called only if type is a post type
 *
 * @since 1.0.0
 *
 * @param array $post_types The post type.
 *
 * @return boolean
 */
function _fill_temp_ids_for_post( $post_types ) {
	global $wpdb;

	$post_types_arr = array();

	foreach ( $post_types as $val ) {
		$post_types_arr[] = "pt.post_type = '{$val}'";
	}

	$post_types_sql_str = isrc_implode( $post_types_arr, ' OR ' );
	$query              = "SELECT pt.ID as post_id FROM {$wpdb->prefix}posts pt WHERE pt.ID NOT IN ( SELECT post_id FROM {$wpdb->prefix}isearch ) AND ( {$post_types_sql_str} ) AND (pt.post_status = 'publish' OR (pt.post_status = 'inherit' AND pt.post_type = 'attachment'))";
	$post_ids           = $wpdb->get_col( $query );

	if ( empty( $post_ids ) ) {
		return false;
	}

	$type         = 'post_type';
	$temp_sql_arr = array();

	foreach ( $post_ids as $id ) {
		$temp_sql_arr[] = "('{$id}', '{$type}')";
	}

	$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}isearch_temp" );

	$insert_str      = isrc_implode( $temp_sql_arr, ', ' );
	$query_temp_fill = "INSERT INTO {$wpdb->prefix}isearch_temp ( post_id ,  type ) VALUES {$insert_str};";
	$fill_temp       = $wpdb->query( $query_temp_fill );

	if ( $fill_temp ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Remove filters for get terms
 */
function isrc_get_terms_remove_filter() {

	if ( defined( 'ISRC_WPML_INSTALLED' ) ) {
		global $sitepress;
		// remove WPML term filters
		remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ) );
		remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ) );
		remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
	}
}


/**
 * Helper function for fill_temp_ids.
 * Is called only if type is a taxonomy
 *
 * @since 1.0.0
 *
 * @param array $taxonomies The taxonomy.
 *
 * @return boolean
 */
function _fill_temp_ids_for_taxonomies( $taxonomies ) {
	global $wpdb;

	$taxonomy_ids = array();

	isrc_get_terms_remove_filter();

	foreach ( $taxonomies as $val ) {

		$terms = get_terms( array(
			'taxonomy'   => $val,
			'parent'     => 0,
			'hide_empty' => false
		) );

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$taxonomy_ids[] = $term->term_id;
			}
		}
	}


	if ( empty( $taxonomy_ids ) ) {
		return false;
	}

	$type         = 'taxonomy';
	$temp_sql_arr = array();

	foreach ( $taxonomy_ids as $id ) {
		$temp_sql_arr[] = "('{$id}', '{$type}')";
	}

	$insert_str      = isrc_implode( $temp_sql_arr, ', ' );
	$query_temp_fill = "INSERT INTO {$wpdb->prefix}isearch_temp ( post_id ,  type ) VALUES {$insert_str};";
	$fill_temp       = $wpdb->query( $query_temp_fill );

	if ( $fill_temp ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Get post ids from the temporary table.
 * Is called only in the re-index screen.
 *
 * @since 1.0.0
 *
 * @param string $limit Limit for DB query.
 *
 * @return array    The post ids as array.
 */
function isrc_get_post_ids( $limit ) {
	global $wpdb;

	$query    = "SELECT * FROM {$wpdb->prefix}isearch_temp WHERE 1=1 LIMIT {$limit}";
	$post_ids = $wpdb->get_results( $query, ARRAY_A );

	return $post_ids;
}

/**
 * Count the entries in the temporary table for calculations.
 * Is called only in the re-index screen.
 *
 * @since 1.0.0
 *
 * @return int    Total logs
 */
function isrc_get_total_count() {
	global $wpdb;

	$query = "SELECT COUNT(*) FROM {$wpdb->prefix}isearch_temp";
	$count = $wpdb->get_var( $query );

	return $count;
}

/**
 * Calculate the rest percentage of the temporary logs.
 * Is called only in the re-index screen.
 *
 * @since 1.0.0
 *
 * @param int $total Total entries in the temp log table
 *
 * @return int    Total logs
 */
function calc_percent( $total ) {
	global $limit;

	$x = $limit / $total;
	$y = $x * 100;

	return round( $y, 1 );
}

/**
 * Delete the indexed id from the temporary table.
 * Is called only in the re-index screen.
 *
 * @since 1.0.0
 *
 * @param int $post_id The id to delete
 *
 * @return boolean
 */
function isrc_delete_temp_id( $post_id ) {
	global $wpdb;

	if ( empty( $post_id ) ) {
		return false;
	}

	$query   = "DELETE FROM {$wpdb->prefix}isearch_temp WHERE post_id = {$post_id}";
	$deleted = $wpdb->query( $query );

	return $deleted;
}


/**
 * Render the checkbox field in admin menu.
 *
 * @param string $fieldset_id
 * @param string $input_name
 * @param string $value
 * @param string $class
 * @param string $data_hide
 * @param array  $options
 * @param bool   $is_checked
 * @param string $help_id
 * @param string $text_string
 * @param string $description
 * @param bool   $fieldset_enabled
 * @param bool   $echo
 *
 * @return string
 */
function isrc_render_fieldset_checkbox( $fieldset_id = '', $input_name = '', $value = '', $class = '', $data_hide = '', $options = array(), $is_checked = false, $help_id = '', $text_string = '', $description = '', $fieldset_enabled = false, $echo = true, $with_list = true ) {

	if ( $is_checked ) {
		$maybe_checked = 'checked="checked"';
	} else {
		$maybe_checked = '';
	}

	$fieldset = '';
	if ( $fieldset_enabled ) {
		$fieldset = '<fieldset>';
	}
	$fieldset .= '<label for="' . $fieldset_id . '">';
	$fieldset .= '<input name="' . $input_name . '" id="' . $fieldset_id . '" type="checkbox"  value="' . $value . '" class="' . $class . '" data-hide="' . $data_hide . '" ' . $maybe_checked . '>';
	if ( ! empty( $text_string ) ) {
		$fieldset .= $text_string;
	}
	if ( ! empty( $description ) ) {
		$fieldset .= '<span class="description">' . $description . '</span>';
	}
	$fieldset .= '</label>';

	if ( ! empty( $help_id ) ) {
		$fieldset .= '<span class="isrc_help_icon_outer"><button type="button" class="isrc_help_icon dashicons isrc_tooltip" id="' . $help_id . '" aria-expanded="false"></button></span>';
	}

	if ( $fieldset_enabled ) {
		$fieldset .= '</fieldset>';
	}

	if ( $with_list ) {
		$fieldset = '<li>' . $fieldset . '</li>';
	}

	if ( $echo ) {
		echo $fieldset;
	}

	return $fieldset;

}

/**
 * Render the help icon in admin menu.
 *
 * @param int  $help_id
 * @param bool $echo
 *
 * @return bool|string
 */
function isrc_render_help_icon( $help_id = 0, $echo = true ) {

	$html = '<span class="isrc_help_icon_outer"><button type="button" class="isrc_help_icon dashicons isrc_tooltip" id="' . $help_id . '" aria-expanded="false"></button></span>';
	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Render the text input fields html in admin menu.
 *
 * @param string $name
 * @param string $id
 * @param string $value
 * @param string $description
 * @param string $style
 * @param string $class
 * @param bool   $echo
 * @param string $type
 * @param string $min
 * @param string $max
 * @param string $step
 * @param string $placeholder
 *
 * @return string
 */
function isrc_render_text_field( $name = '', $id = '', $value = '', $description = '', $style = '', $class = '', $echo = true, $type = 'text', $min = '', $max = '', $step = '', $placeholder = '' ) {

	$html = '<input name="' . $name . '" type="' . $type . '" id="' . $id . '" class="' . $class . '" style="' . $style . '" placeholder="' . $placeholder . '" value="' . esc_attr( $value ) . '" ';
	if ( ! empty( $min ) ) {
		$html .= ' min="' . $min . '"';
	}
	if ( ! empty( $max ) ) {
		$html .= ' max="' . $max . '"';
	}
	if ( ! empty( $step ) ) {
		$html .= ' step="' . $step . '"';
	}
	$html .= '>';
	if ( ! empty( $description ) ) {
		$html .= '<span class="description">' . $description . '</span>';
	}
	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Render the select fields html in admin menu.
 *
 * @param string $name
 * @param string $id
 * @param string $class
 * @param string $style
 * @param string $data_hide
 * @param string $description
 * @param array  $values
 * @param array  $options
 * @param bool   $echo
 *
 * @return string
 */
function isrc_render_select_field( $name = '', $id = '', $class = '', $style = '', $data_hide = '', $description = '', $values = array(), $options = array(), $echo = true ) {

	$html = '<select name="' . $name . '" id="' . $id . '" class="' . $class . '" style="' . $style . '"  ';
	if ( ! empty( $data_hide ) ) {
		$html .= ' data-hide="' . $data_hide . '"';
	}
	$html .= '>';

	if ( ! empty( $values ) ) {
		foreach ( $values as $key => $val ) {
			$html .= '<option value="' . $key . '" ' . isrc_selected( $options, $key, false ) . '>' . $val . '</option>';
		}
	}

	$html .= '</select>';
	if ( ! empty( $description ) ) {
		$html .= '<span class="description">' . $description . '</span>';
	}
	if ( $echo ) {
		echo $html;
	}

	return $html;

}

/**
 * Delete popularity log
 *
 * @param $log_id
 *
 * @return bool
 */
function isrc_delete_popularity_log( $log_id ) {
	global $wpdb;

	if ( empty( $log_id ) ) {
		return false;
	}

	$wpdb->delete(
		"{$wpdb->prefix}isearch_popular",
		array( 'id' => $log_id ),
		array( '%d' )
	);

	$wpdb->update(
		"{$wpdb->prefix}isearch",
		array(
			'hit' => 0,
		),
		array( 'post_id' => $log_id ),
		array(
			'%d',
		),
		array( '%d' )
	);

	return true;
}

/**
 * Get the shortcodes from DB.
 *
 * @param bool $for_vc
 *
 * @return array|null|object
 */
function get_the_shortcodes( $for_vc = false ) {

	global $wpdb;

	$lang = isrc_get_lang_admin();

	$sql = "SELECT id,title FROM {$wpdb->prefix}isearch_shortcodes WHERE lang = '{$lang}'";

	$results = $wpdb->get_results( $sql, 'ARRAY_A' );

	if ( ! $for_vc ) {
		return $results;
	}

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

/**
 * @param string $post_type
 *
 * @return array
 */
function isrc_get_meta_keys_by_posttype( $post_type = 'post' ) {
	global $wpdb;

	/* ACF */
	if ( function_exists( 'acf_get_field_groups' ) ) {
		$acf_groups = acf_get_field_groups( array( 'post_type' => $post_type ) );
		$not_in     = array();
		$acf_keys   = array();
		if ( ! empty( $acf_groups ) ) {
			foreach ( $acf_groups as $group ) {
				$fields = acf_get_fields( $group['key'] );
				if ( is_array( $fields ) ) {
					foreach ( $fields as $field ) {
						$not_in[]   = "AND $wpdb->postmeta.meta_key NOT LIKE '_{$field['name']}%'";
						$not_in[]   = "AND $wpdb->postmeta.meta_key NOT LIKE '{$field['name']}%'";
						$acf_keys[] = $field['key'] . '__acf__' . $field['label'];
						if ( isset( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
							foreach ( $field['sub_fields'] as $sub_field ) {
								$not_in[] = "AND $wpdb->postmeta.meta_key NOT LIKE '_{$sub_field['name']}%'";
								$not_in[] = "AND $wpdb->postmeta.meta_key NOT LIKE '{$sub_field['name']}%'";
								//$acf_keys[] = $sub_field['key'] . '__acf__' . $sub_field['label'];
							}
						}
					}
				}

			}
		}
	}

	$sql_not_in = '';
	if ( isset( $not_in ) && ! empty( $not_in ) ) {
		$sql_not_in = implode( ' ', $not_in );
	}

	$query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key) 
        FROM $wpdb->posts 
        LEFT JOIN $wpdb->postmeta 
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
        WHERE $wpdb->posts.post_type = '%s' 
        AND $wpdb->postmeta.meta_key != '' 
        AND $wpdb->postmeta.meta_key NOT LIKE '_isrc%' 
        $sql_not_in 
        AND $wpdb->postmeta.meta_key != 'popularity' 
        ORDER BY $wpdb->postmeta.meta_key ASC
    ";

	$meta_keys = $wpdb->get_col( $wpdb->prepare( $query, $post_type ) );
	if ( isset( $acf_keys ) && ! empty( $acf_keys ) ) {
		$meta_keys = array_merge( $meta_keys, $acf_keys );
	}

	/* isearch extra keys */
	$extra_meta_keys = apply_filters( 'isearch_cb_add_extra_meta_data', array(), $post_type );
	if ( ! empty( $extra_meta_keys ) && is_array( $extra_meta_keys ) ) {
		foreach ( $extra_meta_keys as $key => $val ) {
			$meta_keys[] = '_cb_ex_mk_' . $val;
		}
	}

	return $meta_keys;
}

/**
 * Get tha acf value
 *
 * @param int    $post_id
 * @param string $acf_key
 *
 * @return array
 */
function isrc_get_acf_value( $post_id = 0, $acf_key = '' ) {

	$meta_data = array();
	$acf_data  = get_field( $acf_key, $post_id, false );
	/* @TODO Add all types of ACF */
	if ( ! empty( $acf_data ) && ! is_array( $acf_data ) ) {
		/* normal field */
		$meta_data = array( $acf_data );
	} elseif ( ! empty( $acf_data ) && is_array( $acf_data ) ) {
		foreach ( $acf_data as $rep_key => $rep_val ) {
			if ( is_array( $rep_val ) ) {
				foreach ( $rep_val as $acfmeta ) {
					$meta_data[] = $acfmeta;
				}
			}
		}
	}

	return $meta_data;
}


/**
 * @param null  $taxo_name
 * @param array $taxo_ids
 * @param int   $key
 */
function isrc_render_exclude_taxo_fields( $taxo_name = null, $taxo_ids = array(), $key = 1 ) {
	if ( empty( $taxo_ids ) ) {
		$array_no = 'ARRAY_NO';
	} else {
		$array_no = $key;
	}

	?>
    <div class="isrc-excl-outer-wrap">
        <div class="closest-wrapper">
            <div class="isrc-excl-tax-select">
                <select class="excl_taxo_selector" name="isrc_opt_adv[exclude_tags][<?php echo $array_no; ?>][taxo_name]">
                    <option value="off"><?php _e( 'Please select a post type.', 'i_search' ); ?></option>
					<?php
					/* build groups */
					$taxonomies        = get_taxonomies(
						array(
							'public'  => true,
							'show_ui' => true,
						), 'objects' );
					$taxonomies_to_pts = array();
					foreach ( $taxonomies as $key => $val ) {
						$in_post_types = $val->object_type;
						foreach ( $in_post_types as $ptkey => $ptval ) {
							$taxonomies_to_pts[ $ptval ][] = array(
								'name'  => $val->name,
								'label' => $val->label
							);
						}
					}
					/* build select groups */
					foreach ( $taxonomies_to_pts as $key => $val ) {
						$post_type_data = get_post_type_object( $key );
						echo '<optgroup label="Post Type: ' . $post_type_data->label . '">';
						foreach ( $val as $taxo_data ) {
							$selected = '';
							if ( $taxo_data['name'] == $taxo_name ) {
								$selected = 'selected="selected"';
							}
							echo '<option ' . $selected . ' value="' . $taxo_data['name'] . '">' . $taxo_data['name'] . '&nbsp;(' . $taxo_data['label'] . ')</option>';
						}
						echo '</optgroup>';
					}
					?>
                </select>
            </div>
            <div class="isrc-excl-tax-select2">
				<?php
				$disabled = '';
				if ( empty( $taxo_ids ) ) {
					$disabled = 'disabled="disabled"';
				}
				?>
                <select class="select2_exclude_taxonomies" name="isrc_opt_adv[exclude_tags][<?php echo $array_no; ?>][taxo_ids][]" <?php echo $disabled; ?> multiple>
					<?php
					if ( ! empty( $taxo_ids ) ) {
						foreach ( $taxo_ids as $selected_taxo_id ) {
							$term = get_term( $selected_taxo_id, $taxo_name );
							$name = $term->name;
							echo "<option selected='selected' value='{$selected_taxo_id}'>{$name}</option>";
						}
					}
					?>
                </select>
            </div>
            <div class="isrc-excl-tax-remove-wrap">
                <span><?php _e( 'Remove', 'i_search' ); ?></span>
            </div>
        </div>
    </div>
	<?php
}

/**
 * Add pro only string
 *
 * @return string
 */

function isrc_pro_only_txt() {
	return '<div class="isrc-pro-only"><a href="https://all4wp.net/redirect/lite-to-pro/" target="_blank">' . __( 'Only in PRO version', 'i_search' ) . '</a></div>';
}

/**
 * Returns the available cb fields for js render
 *
 * @return array
 */
function isrc_get_available_extra_fields() {
	return array(
		'append_to_title'   => array(
			'title' => __( 'Append To Title', 'i_search' ),
			'descr' => ''
		),
		'after_title'       => array(
			'title' => __( 'After Title', 'i_search' ),
			'descr' => ''
		),
		'before_categories' => array(
			'title' => __( 'Before Categories', 'i_search' ),
			'descr' => __( '(Advanced theme)', 'i_search' )
		),
		'after_categories'  => array(
			'title' => __( 'After Categories', 'i_search' ),
			'descr' => ''
		),
		'after_price'       => array(
			'title' => __( 'After Price', 'i_search' ),
			'descr' => ''
		),
		'before_excerpt'    => array(
			'title' => __( 'Before Excerpt', 'i_search' ),
			'descr' => __( '(Advanced theme)', 'i_search' )
		),
		'after_excerpt'     => array(
			'title' => __( 'After Excerpt', 'i_search' ),
			'descr' => ''
		),
		'before_badges'     => array(
			'title' => __( 'Before Badges', 'i_search' ),
			'descr' => ''
		),
		'after_badges'      => array(
			'title' => __( 'After Badges', 'i_search' ),
			'descr' => ''
		),
	);
}

/**
 * @param string $options_tab
 * @param array  $data
 *
 * @return array|bool
 */
function isrc_check_posted_data( $options_tab = 'isrc_opt', $data = array() ) {

	if ( $options_tab == 'isrc_opt' ) {
		return _isrc_check_posted_data_general_opt( $data );
	}

	if ( $options_tab == 'isrc_opt_adv' ) {
		return _isrc_check_posted_data_adv_opt( $data );
	}

	if ( $options_tab == 'isrc_sc_opt' ) {
		return _isrc_check_posted_data_isrc_sc_opt( $data );
	}

	if ( $options_tab == 'isrc_cb_opt' ) {
		return _isrc_check_posted_data_isrc_cb_opt( $data );
	}

	return false;
}

/**
 * @param array $data
 *
 * @return array|bool
 */
function _isrc_check_posted_data_isrc_cb_opt( $data = array() ) {

	if ( empty( $data ) || ! is_array( $data ) ) {
		return false;
	}

	if ( ! is_array( $data['builder_data'] ) ) {
		$data['builder_data'] = array();

		return $data;
	}

	/* sanitize */
	foreach ( $data['builder_data'] as $key1 => $val1 ) {

		foreach ( $val1 as $key2 => $val2 ) {

			foreach ( $val2 as $key3 => $val3 ) {
				$data['builder_data'][ $key1 ][ $key2 ][ $key3 ] = array_map( 'sanitize_text_field', $val3 );
			}

		}

	}

	return $data;
}

/**
 * @param array $data
 *
 * @return array|bool
 */
function _isrc_check_posted_data_isrc_sc_opt( $data = array() ) {

	if ( empty( $data ) || ! is_array( $data ) ) {
		return false;
	}

	/* we have too many data. Better to loop and look for exceptions */
	foreach ( $data as $key => $val ) {
		/* define exceptions first */
		if ( $key == 'css' ) {
			/* do not touch CSS */
		} elseif ( is_array( $val ) ) {
			$data[ $key ] = array_map( 'sanitize_text_field', $data[ $key ] );
		} else {
			$data[ $key ] = sanitize_text_field( $data[ $key ] );
		}

	}

	return $data;
}

/**
 * @param array $data
 *
 * @return array|bool
 */
function _isrc_check_posted_data_adv_opt( $data = array() ) {

	if ( empty( $data ) || ! is_array( $data ) ) {
		return false;
	}

	/* sanitize bad words */
	if ( isset( $data['isrc_bad_words'] ) && ! empty( $data['isrc_bad_words'] ) ) {
		$data['isrc_bad_words'] = sanitize_text_field( $data['isrc_bad_words'] );
	}

	/* sanitize isrc_hide_words */
	if ( isset( $data['isrc_hide_words'] ) && ! empty( $data['isrc_hide_words'] ) ) {
		$data['isrc_hide_words'] = sanitize_text_field( $data['isrc_hide_words'] );
	}

	/* sanitize ip_limit */
	if ( isset( $data['ip_limit'] ) && ! empty( $data['ip_limit'] ) ) {
		$data['ip_limit'] = (int) $data['ip_limit'];
	} else {
		/* set default */
		$data['ip_limit'] = 5;
	}

	/* sanitize meta_inc */
	if ( isset( $data['meta_inc'] ) && ! empty( $data['meta_inc'] ) ) {
		$data['meta_inc'] = array_map( 'sanitize_text_field', $data['meta_inc'] );
	}

	return $data;
}

/**
 * Pre check post data
 *
 * @param array $data
 *
 * @return array|bool
 */
function _isrc_check_posted_data_general_opt( $data = array() ) {

	if ( empty( $data ) ) {
		return false;
	}

	if ( ! isset( $data['front'] ) || empty( $data['front'] ) || ! is_array( $data['front'] ) ) {
		return false;
	}

	if ( ! isset( $data['lang'] ) || empty( $data['lang'] ) ) {
		return false;
	}

	if ( isset( $data['front']['taborder'] ) ) {
		if ( ! is_array( $data['front']['taborder'] ) ) {
			/* we need an array something wrong here. unset it from the original $data */
			unset( $data['front']['taborder'] );
		} else {
			/* check if names are valid */
			foreach ( $data['front']['taborder'] as $key => $val ) {
				/* first sanitize and handle always with original data not with the key, val */
				$data['front']['taborder'][ $key ]['label'] = sanitize_text_field( $val['label'] );
				$data['front']['taborder'][ $key ]['type']  = sanitize_text_field( $val['type'] );
				$data['front']['taborder'][ $key ]['name']  = sanitize_text_field( $val['name'] );

				if ( $data['front']['taborder'][ $key ]['type'] == 'taxonomy' ) {
					/* exist this taxonomy? is it a good idea to check here? maybe a plugins register priority is above 99 */
					if ( ! taxonomy_exists( $data['front']['taborder'][ $key ]['name'] ) ) {
						/* taxonomy not exists in system. unset this key from original $data */
						echo $data['front']['taborder'][ $key ]['name'];
						unset( $data['front']['taborder'][ $key ] );
					}
				} elseif ( $data['front']['taborder'][ $key ]['type'] == 'post_type' ) {
					/* exist this post_type? */
					if ( ! post_type_exists( $data['front']['taborder'][ $key ]['name'] ) ) {
						/* post_type not exists in system. unset this key from original $data */
						unset( $data['front']['taborder'][ $key ] );
					}
				} else {
					/* we do not expect another type*/
					/* if so. unset it */
					unset( $data['front']['taborder'][ $key ] );
				}

			}
		}

	}

	/* sanitize kw_handle */
	if ( isset( $data['front']['kw_handle'] ) ) {
		$data['front']['kw_handle'] = sanitize_text_field( $data['front']['kw_handle'] );
	} else {
		$data['front']['kw_handle'] = 'split';
	}

	/* sanitize thumb_size */
	if ( isset( $data['front']['thumb_size'] ) && is_array( $data['front']['thumb_size'] ) ) {
		/* thumb size is integer and both w and h needed */
		if ( ! isset( $data['front']['thumb_size']['w'] ) || empty( $data['front']['thumb_size']['w'] ) || $data['front']['thumb_size']['w'] < 1 ) {
			/* set default */
			$data['front']['thumb_size']['w'] = 50;
		} else {
			$data['front']['thumb_size']['w'] = (int) $data['front']['thumb_size']['w'];
		}
		if ( ! isset( $data['front']['thumb_size']['h'] ) || empty( $data['front']['thumb_size']['h'] ) || $data['front']['thumb_size']['h'] < 1 ) {
			/* set default */
			$data['front']['thumb_size']['h'] = 70;
		} else {
			$data['front']['thumb_size']['h'] = (int) $data['front']['thumb_size']['h'];
		}
	}

	/* sanitize default thumbnail image id */
	if ( isset( $data['front']['no_img'] ) ) {
		$data['front']['no_img'] = (int) $data['front']['no_img'];
	}

	/* sanitize cats label */
	if ( isset( $data['front']['cats_l'] ) ) {
		$data['front']['cats_l'] = sanitize_text_field( $data['front']['cats_l'] );
	}

	/* sanitize add to cart label */
	if ( isset( $data['front']['atc_label'] ) ) {
		$data['front']['atc_label'] = sanitize_text_field( $data['front']['atc_label'] );
	}

	/* sanitize buynow label */
	if ( isset( $data['front']['buyn_label'] ) ) {
		$data['front']['buyn_label'] = sanitize_text_field( $data['front']['buyn_label'] );
	}

	/* sanitize woo settings labels */
	if ( isset( $data['woo']['cats_l'] ) ) {
		$data['woo']['cats_l'] = sanitize_text_field( $data['woo']['cats_l'] );
	}

	if ( isset( $data['woo']['outofstock_l'] ) ) {
		$data['woo']['outofstock_l'] = sanitize_text_field( $data['woo']['outofstock_l'] );
	}

	if ( isset( $data['woo']['instock_l'] ) ) {
		$data['woo']['instock_l'] = sanitize_text_field( $data['woo']['instock_l'] );
	}

	if ( isset( $data['woo']['backorder_l'] ) ) {
		$data['woo']['backorder_l'] = sanitize_text_field( $data['woo']['backorder_l'] );
	}

	if ( isset( $data['woo']['sale_l'] ) ) {
		$data['woo']['sale_l'] = sanitize_text_field( $data['woo']['sale_l'] );
	}

	if ( isset( $data['woo']['featured_l'] ) ) {
		$data['woo']['featured_l'] = sanitize_text_field( $data['woo']['featured_l'] );
	}

	return $data;
}