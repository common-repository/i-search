<?php
/**
 * i-Search ajax Class
 *
 * This is tha main ajax file for frontend search suggestions.
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

class isrc_ajax_class {

	public $locale = "en";
	public $instance = false;
	public $sql_limit = 15;
	public $logging = false;
	public $logging_at_caret_end = true;
	public $log_popularity = false;
	public $show_popularity = false;
	public $show_didumeans = false;
	public $post_types;
	public $taxonomies;
	public $do_query_for;
	public $is_mobile = false;
	public $postdata = '';
	public $order_by = 'post_id';
	public $popular_max = 5;
	public $pagination = 1;
	public $searchtype = 'firstload';
	public $search_as = 'split';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->set_defaults();

		if ( isset( $this->postdata['action'] ) && ( $this->postdata['action'] == 'isrc_ajax_search_posts' || $this->postdata['action'] == 'isrc_get_instance' ) ) {
			add_action( 'wp_ajax_isrc_ajax_search_posts', array( $this, 'ajax_search_posts' ) );
			add_action( 'wp_ajax_nopriv_isrc_ajax_search_posts', array( $this, 'ajax_search_posts' ) );
			add_action( 'wp_ajax_isrc_get_instance', array( $this, 'isrc_get_instance' ) );
			add_action( 'wp_ajax_nopriv_isrc_get_instance', array( $this, 'isrc_get_instance' ) );

		}

		if ( isset( $this->postdata['action'] ) && $this->postdata['action'] == 'isrc_ajax_flowpopular' ) {
			$this->update_flow_or_popularity();
		}
	}

	/**
	 * Set the defaults for this class.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function set_defaults() {
		global $isrc_opt;

		if ( isset( $_POST ) ) {
			$this->postdata = $_POST;
		} else {
			$this->postdata = array();
		}

		/* is user a mobile user? */
		if ( class_exists( 'isrc_Mobile_Detect' ) ) {

			$mobile_detect = new isrc_Mobile_Detect;
			/* Any mobile device (phones or tablets). */
			if ( $mobile_detect->isMobile() ) {
				$this->is_mobile = true;
			}
		}

		if ( isset( $this->postdata['locale'] ) ) {
			/* security protect */
			$this->locale = substr( $this->postdata['locale'], 0, 2 );
		}

		/*
		 * Logging and popularity from shortcode (overwriting)
		 */
		if ( isset( $this->postdata['logging'] ) ) {
			if ( filter_var( $this->postdata['logging'], FILTER_VALIDATE_BOOLEAN ) ) {
				$this->logging = true;
			} else {
				$this->logging = false;
			}
		}

		/*
		 * Disable logging if caret is not on end of text
		 */
		if ( isset( $this->postdata['caret'] ) && $this->logging_at_caret_end ) {
			if ( ! filter_var( $this->postdata['caret'], FILTER_VALIDATE_BOOLEAN ) && $this->logging = true ) {
				$this->logging = false;
			}
		}

		/*
		 * Show did you mean boolean from shortcode (overwriting)
		 */
		if ( isset( $this->postdata['sh_didumean'] ) ) {
			if ( filter_var( $this->postdata['sh_didumean'], FILTER_VALIDATE_BOOLEAN ) ) {
				$this->show_didumeans = true;
			} else {
				$this->show_didumeans = false;
			}
		}

		if ( isset( $this->postdata['log_popularity'] ) ) {
			if ( filter_var( $this->postdata['log_popularity'], FILTER_VALIDATE_BOOLEAN ) ) {
				$this->log_popularity = true;
			} else {
				$this->log_popularity = false;
			}
		}

		if ( isset( $this->postdata['show_popularity'] ) ) {
			if ( filter_var( $this->postdata['show_popularity'], FILTER_VALIDATE_BOOLEAN ) ) {
				$this->show_popularity = true;
			} else {
				$this->show_popularity = false;
			}
		}

		/*
		 * SQL limit
		 */
		if ( isset( $this->postdata['limit'] ) && $this->postdata['limit'] > 0 ) {
			$this->sql_limit = (int) $this->postdata['limit'];
		}

		/*
		 * Max popularity
		 */
		if ( isset( $this->postdata['popular_max'] ) && $this->postdata['popular_max'] > 0 ) {
			$this->popular_max = (int) $this->postdata['popular_max'];
		}

		/*
		 * Look in POST for search_in. If not or false set default post types.
		 */
		if ( isset( $this->postdata['search_in'] ) && $this->postdata['search_in'] != 'false' ) {
			$this->set_customized_post_types();
		} else {
			$this->set_default_post_types();
		}

		/*
		 * Set order_by.
		 */
		if ( isset( $this->postdata['order_by'] ) && ! empty( $this->postdata['order_by'] ) ) {
			$this->order_by = $this->postdata['order_by'];
		} else {
			$this->order_by = 'post_id';
		}

		/*
		 * Search word handle.
		 */
		if ( isset( $isrc_opt['front']['kw_handle'] ) && ! empty( $isrc_opt['front']['kw_handle'] ) ) {
			$this->search_as = $isrc_opt['front']['kw_handle'];
		}

		/*
		 * Instance.
		 */
		if ( isset( $this->postdata['instance'] ) && ! empty( $this->postdata['instance'] ) ) {
			$this->instance = (int) $this->postdata['instance'];
		} elseif ( isset( $this->postdata['instance'] ) && (int) $this->postdata['instance'] === 0 ) {
			$this->logging        = false;
			$this->log_popularity = false;
		}

		/*
		 * Set pagination.
		 */
		if ( isset( $this->postdata['pagination'] ) && ! empty( $this->postdata['pagination'] ) && $this->postdata['pagination'] > 1 ) {
			$this->pagination = (int) $this->postdata['pagination'];
		}

		/*
		 * Set searchtype.
		 */
		if ( isset( $this->postdata['searchtype'] ) && ! empty( $this->postdata['searchtype'] ) ) {
			$this->searchtype = $this->postdata['searchtype'];
		}

	}

	/**
	 * Set post types based on shortcode atts to search in.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param null $search_in
	 *
	 * @return void
	 */
	public function set_customized_post_types( $search_in = null ) {
		$post_types   = array();
		$taxonomies   = array();
		$do_query_for = array();

		/* format from shortcode string to array */
		if ( empty( $search_in ) ) {
			$search_in = explode( ',', $this->postdata['search_in'] );
		} else {
			$search_in = explode( ',', $search_in );
		}

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

		$this->post_types   = $post_types;
		$this->taxonomies   = $taxonomies;
		$this->do_query_for = $do_query_for;
	}

	/**
	 * Set post types based on admin settings to search in.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function set_default_post_types() {
		global $isrc_opt;

		$post_types   = array();
		$taxonomies   = array();
		$do_query_for = array();

		if ( isset( $isrc_opt['include_in_suggestions'] ) && ! empty( $isrc_opt['include_in_suggestions'] ) ) {
			$include_in_suggestions = $isrc_opt['include_in_suggestions'];

			if ( ! empty( $include_in_suggestions ) ) {

				foreach ( $include_in_suggestions as $key => $val ) {

					if ( $key == 'post_types' ) {
						$do_query_for['post_types'] = true;
						foreach ( $include_in_suggestions['post_types'] as $kpost => $valpost ) {
							$post_types[] = $valpost;
						}
					}

					if ( $key == 'taxonomies' ) {
						$do_query_for['taxonomies'] = true;
						foreach ( $include_in_suggestions['taxonomies'] as $ktax => $valtax ) {
							$taxonomies[] = $valtax;
						}
					}

				}
			}
		}

		$this->post_types   = $post_types;
		$this->taxonomies   = $taxonomies;
		$this->do_query_for = $do_query_for;
	}

	/**
	 * Make the search
	 *
	 * @param array $data
	 *
	 * @return array|bool|mixed
	 */
	public function ajax_search_posts( $data = array() ) {
		global $wpdb;
		$time_start = $this->isrc_get_micro_time();

		if ( ! empty( $data ) ) {
			$this->postdata = $data;
		}

		if ( isset( $data['disable_logging'] ) && $data['disable_logging'] == true ) {
			$this->logging = false;
		}

		$transient_enabled  = 'no';
		$transient_duration = 2;
		$meanings           = array();

		$search_keyword = trim( $this->postdata['query'] );
		if ( empty( $search_keyword ) ) {
			exit();
		}

		$search_keyword = sanitize_title( $search_keyword );
		/* security, max 20 char allowed */
		$search_keyword = substr( $search_keyword, 0, 20 );
		$transient_name = 'isrc_' . $search_keyword;
		if ( $transient_enabled == 'no' || false === ( $suggestions = get_transient( $transient_name ) ) ) {

			$do_query_for = $this->do_query_for;

			/* query for post types? */

			$results_post_types = array();

			if ( isset( $do_query_for['post_types'] ) && $do_query_for['post_types'] ) {

				$sql_arr     = $this->build_sql_string( 'post_type', $search_keyword );
				$query_str   = $sql_arr['query_str'];
				$prepare_arr = $sql_arr['prepare_arr'];
				$db_query    = $wpdb->prepare( $query_str, $prepare_arr );
				if ( isset( $data['get_post_type_query_only'] ) ) {
					return $db_query;
				}

				$results_post_types = $wpdb->get_results( $db_query, ARRAY_A );

				if ( empty( $results_post_types ) ) {
					$results_post_types = array();
				}

			}

			/* do we have taxonomies for suggestions? */
			$results_taxonomies = array();

			if ( isset( $do_query_for['taxonomies'] ) && $do_query_for['taxonomies'] ) {

				$sql_arr = $this->build_sql_string( 'taxonomies', $search_keyword );

				$query_str   = $sql_arr['query_str'];
				$prepare_arr = $sql_arr['prepare_arr'];

				$db_query           = $wpdb->prepare( $query_str, $prepare_arr );
				$results_taxonomies = $wpdb->get_results( $db_query, ARRAY_A );
				if ( empty( $results_taxonomies ) ) {
					$results_taxonomies = array();
				}

			}

			$results          = array_merge( $results_taxonomies, $results_post_types );
			$formated_results = $this->preformat_results( $results );
			$limits           = $formated_results['limits'];
			$suggestions      = $formated_results['suggestions'];

			if ( empty( $suggestions ) ) {

				if ( $this->show_didumeans ) {

					$meaning = $this->log_book( $search_keyword );

					if ( ! empty( $meaning ) ) {
						$meanings[] = array(
							'value' => $meaning,
							'type'  => 'meaning'
						);
					}
				}
			}

			$show_popular_searches = $this->show_popularity;
			$popular_searches      = array();
			if ( $show_popular_searches && empty( $suggestions ) && empty( $meanings ) ) {
				$popular_searches_db = isrc_get_popular_searches( $this->popular_max, $this->post_types, $this->taxonomies, $this->locale );
				if ( ! empty( $popular_searches_db ) ) {
					foreach ( $popular_searches_db as $popular ) {
						$to_add             = array(
							'value' => $popular['title'],
							'type'  => 'popular',
						);
						$popular_searches[] = $to_add;
					}
				}
			}

			$have_results = ( ! empty( $results ) ) ? true : false;
			$time_end     = $this->isrc_get_micro_time();
			$time         = $time_end - $time_start;
			$suggestions  = array(
				'isMobile'         => ( $this->is_mobile ) ? 'yes' : 'no',
				'results'          => $have_results,
				'meanings'         => $meanings,
				'popular_searches' => $popular_searches,
				'suggestions'      => $suggestions,
				'limits'           => $limits,
				'time'             => $time
			);


			if ( $transient_enabled == 'yes' ) {
				set_transient( $transient_name, $suggestions, $transient_duration * HOUR_IN_SECONDS );
			}

		}

		if ( isset( $data['format'] ) && 'array' == $data['format'] ) {
			return $suggestions;
		}

		$suggestions['response_time'] = microtime( true ) - $_SERVER['REQUEST_TIME_FLOAT'];
		wp_send_json( $suggestions );

		/* code inspector expects a return */

		return true;
	}

	/**
	 * Get the micro time.
	 *
	 * @return float
	 */
	public function isrc_get_micro_time() {
		list( $usec, $sec ) = explode( " ", microtime() );

		return ( (float) $usec + (float) $sec );
	}

	/**
	 * Helper function to build the sql string.
	 *
	 * @param $type
	 * @param $search_keyword
	 *
	 * @return array
	 * @since  2.0.0
	 * @access public
	 *
	 */
	public function build_sql_string( $type, $search_keyword ) {
		global $wpdb;

		$search_as = $this->search_as;
		/* change search_as to normal if pluralized is selected but no plurals defined in settings to save search speed */
		global $isrc_opt_adv;
		if ( $search_as == 'normal_with_pl' && ( ! isset( $isrc_opt_adv['plurals'] ) || empty( $isrc_opt_adv['plurals'] ) ) ) {
			$search_as = 'normal';
		}

		if ( $this->searchtype == 'loadmore' ) {
			/* we have a continues scroll */
			return $this->build_sql_string_load_more( $type, $search_keyword );
		}

		/* build db query */
		if ( $type == 'post_type' ) {
			$post_types = $this->post_types;
			$table_name = 'isearch';
		}

		if ( $type == 'taxonomies' ) {
			$post_types = $this->taxonomies;
			$table_name = 'isearch_taxonomy';
		}

		$limit = (int) $this->sql_limit;

		if ( $limit > 50 ) {
			$this->sql_limit = $limit = 50;
		}

		if ( $limit < 1 ) {
			$this->sql_limit = $limit = 1;
		}

		/* to check if we have more results in db add +1 to limit */
		$limit ++;

		$query_str   = "";
		$prepare_arr = array();
		$numItems    = count( $post_types );

		$order_by = $this->order_by;

		if ( $order_by == 'post_id' ) {
			$order = "ORDER BY post_id DESC";
		} elseif ( $order_by == 'random' ) {
			$order = "ORDER BY RAND()";
		} elseif ( $order_by == 'popularity' ) {
			$order = "ORDER BY hit DESC";
		} elseif ( $order_by == 'title' ) {
			$order = "ORDER BY title ASC";
		} else {
			$order = "ORDER BY post_id DESC";
		}

		$lang_str = $wpdb->prepare( " AND lang = %s", $this->locale );

		if ( $search_as == 'split' || $search_as == 'split_with_or' ) {

			$search_terms = explode( '-', $search_keyword );

			$like = array();

			foreach ( $search_terms as $lkey => $lval ) {
				$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( $lval ) . '%' );
			}

			if ( $search_as == 'split' ) {
				$like_str = implode( ' AND ', $like );
			} elseif ( $search_as == 'split_with_or' ) {
				$like_str = implode( ' OR ', $like );
			}

		} elseif ( $search_as == 'normal_with_pl' ) {
			/* exact match with pluralization */

			$search_terms = explode( '-', $search_keyword );

			$like = array();
			$len  = count( $search_terms );
			$i    = 0;

			foreach ( $search_terms as $lkey => $lval ) {
				if ( $len == 1 ) {
					/* if term is only one word */
					$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( ',' . $lval ) . '%' );
				} elseif ( $len > 1 ) {
					/* if more than one search term */
					if ( $i == 0 ) {
						/* is the term the first term in string? */
						$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( ',' . $lval . '-' ) . '%' );
					} elseif ( $i == $len - 1 ) {
						/* term is the last in string */
						$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( '-' . $lval ) . '%' );
					} else {
						/* term is in the middle */
						$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( '-' . $lval . '-' ) . '%' );
					}
				}
				$i ++;
			}
			$like_str = implode( ' AND ', $like );

		} else {
			/* Normal exact match. */
			$like_str = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( $search_keyword ) . '%' );
		}

		/* make union only if more then 1 post type is selected */
		if ( $numItems > 1 ) {
			$i = 0;
			foreach ( $post_types as $post_type ) {
				$query_str .= "( SELECT output FROM {$wpdb->prefix}{$table_name} WHERE {$like_str} AND post_type = %s {$lang_str} {$order} LIMIT {$limit} )";
				if ( ++ $i !== $numItems ) {
					$query_str .= " UNION ";
				}
				$prepare_arr[] = $post_type;
			}
		} else {
			/* we have only one PT no need for union */
			$query_str     = "SELECT output FROM {$wpdb->prefix}{$table_name} WHERE ({$like_str}) AND post_type = %s {$lang_str} {$order} LIMIT {$limit}";
			$prepare_arr[] = $post_types[0];
		}

		$return = array(
			'query_str'   => $query_str,
			'prepare_arr' => $prepare_arr,
		);

		return $return;
	}

	/**
	 * Helper function to build the load more sql string.
	 *
	 * @param $type
	 * @param $search_keyword
	 *
	 * @return array
	 * @since  2.0.0
	 * @access public
	 *
	 */
	public function build_sql_string_load_more( $type, $search_keyword ) {
		global $wpdb;

		/* build db query */
		if ( $type == 'post_type' ) {
			$post_types = $this->post_types;
			$table_name = 'isearch';
		}

		if ( $type == 'taxonomies' ) {
			$post_types = $this->taxonomies;
			$table_name = 'isearch_taxonomy';
		}

		$limit = (int) $this->sql_limit;

		if ( $limit > 50 ) {
			$this->sql_limit = $limit = 50;
		}

		if ( $limit < 1 ) {
			$this->sql_limit = $limit = 1;
		}

		/* to check if we have more results in db add +1 to limit */
		$limit ++;

		$prepare_arr = array();

		$order_by = $this->order_by;

		if ( $order_by == 'post_id' ) {
			$order = "ORDER BY post_id DESC";
		} elseif ( $order_by == 'random' ) {
			$order = "ORDER BY RAND()";
		} elseif ( $order_by == 'popularity' ) {
			$order = "ORDER BY hit DESC";
		} elseif ( $order_by == 'title' ) {
			$order = "ORDER BY title ASC";
		} else {
			$order = "ORDER BY post_id DESC";
		}

		$lang_str  = $wpdb->prepare( " AND lang = %s", $this->locale );
		$search_as = $this->search_as;
		if ( $search_as == 'split' || $search_as == 'split_with_or' ) {

			$search_terms = explode( '-', $search_keyword );

			$like = array();

			if ( empty( $search_terms ) ) {
				if ( $search_as == 'split_with_or' ) {
					$like[] = $wpdb->prepare( "terms LIKE %s", $wpdb->esc_like( $search_keyword ) . '%' );
				} else {
					$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( $search_keyword ) . '%' );
				}
			}

			foreach ( $search_terms as $lkey => $lval ) {
				if ( $search_as == 'split_with_or' ) {
					$like[] = $wpdb->prepare( "terms LIKE %s", $wpdb->esc_like( $lval ) . '%' );
				} else {
					$like[] = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( $lval ) . '%' );
				}
			}

			if ( $search_as == 'split' ) {
				$like_str = implode( ' AND ', $like );
			} elseif ( $search_as == 'split_with_or' ) {
				$like_str = implode( ' OR ', $like );
			}
		} else {
			$like_str = $wpdb->prepare( "terms LIKE %s", '%' . $wpdb->esc_like( $search_keyword ) . '%' );
		}

		/* we have only one PT no need for union */
		$not_in        = $this->postdata['not_in'];
		$query_str     = "SELECT output FROM {$wpdb->prefix}{$table_name} WHERE ({$like_str}) AND post_type = %s  AND post_id NOT IN({$not_in}) {$lang_str} {$order} LIMIT {$limit}";
		$prepare_arr[] = $post_types[0];

		$return = array(
			'query_str'   => $query_str,
			'prepare_arr' => $prepare_arr,
		);

		return $return;
	}

	/**
	 * Format the sql results based on admin settings to needed output for the frontend.
	 *
	 * @param array $results
	 *
	 * @return array
	 *
	 */
	public function preformat_results( $results = array() ) {
		global $isrc_opt_adv;

		$return = array();

		/* mobile detect exclusion? */
		if ( isset( $isrc_opt_adv['mobile'] ) && $this->is_mobile ) {
			/* yes we have an exclusion and the user is a mobile device */
			$mobile_excludes = $isrc_opt_adv['mobile'];
		} else {
			$mobile_excludes = array();
		}

		/* empty array to fill in foreach and check if we have more results in db. We set the limit for db +1. */
		$limits = array();

		foreach ( $results as $result ) {

			$data = json_decode( $result['output'], true );

			$ptn      = $data['ptn'];
			$type     = $data['type'];
			$type_key = ( $type == 'taxonomy' ) ? 'tx_' . $ptn : 'pt_' . $ptn;

			if ( isset( $limits[ $type_key ] ) ) {
				$limits[ $type_key ] ++;
			} else {
				$limits[ $type_key ] = 1;
			}

			/* stop adding more if the current post type/taxonomy have more results as sql limit. sql limit is always +1 to check if more data is waiting for us in the sql */
			if ( $limits[ $type_key ] > $this->sql_limit ) {
				continue;
			}

			$return_data = $data;

			if ( isset( $data['img'] ) && isset( $mobile_excludes['hide_img'] ) ) {
				unset( $return_data['img'] );
			}

			if ( isset( $data['img2'] ) && isset( $mobile_excludes['hide_img'] ) ) {
				unset( $return_data['img2'] );
			}

			if ( isset( $data['cnt'] ) && isset( $mobile_excludes['hide_img'] ) ) {
				unset( $return_data['cnt'] );
			}

			/* excerpt */
			if ( isset( $data['excerpt'] ) && ! empty( $data['excerpt'] ) && isset( $mobile_excludes['hide_excerpt'] ) ) {
				unset( $return_data['excerpt'] );
			}

			/* price */
			if ( isset( $data['price'] ) && ! empty( $data['price'] ) && isset( $mobile_excludes['hide_price'] ) ) {
				unset( $return_data['price'] );
			}

			/* product categories */
			if ( isset( $data['p_cats'] ) && ! empty( $data['p_cats'] ) && isset( $mobile_excludes['hide_cats'] ) ) {
				unset( $return_data['p_cats'] );
			}

			/* badges */
			if ( isset( $data['badges'] ) && ! empty( $data['badges'] ) && isset( $mobile_excludes['hide_badges'] ) ) {
				unset( $return_data['badges'] );
			}

			$return[ $type_key ][] = $return_data;
		}


		return array( 'suggestions' => $return, 'limits' => $limits );
	}

	/**
	 * Log not found search strings based on admin settings.
	 *
	 * @param $search_keyword
	 *
	 * @return string
	 */
	public function log_book( $search_keyword ) {
		global $wpdb;

		$query   = trim( $this->postdata['query'] );
		$last_ip = $this->get_the_user_ip();
		$is_spam = $this->ip_spam_protection( $last_ip );

		/* string should only contain the a to z , A to Z, 0 to 9 */
		if ( preg_match( '/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $query ) ) {
			return '';
		}

		$search_keyword_clean = trim( $search_keyword );
		$didumean             = '';

		$query_str = "SELECT * FROM {$wpdb->prefix}isearch_logs WHERE lang = %s AND instance = %d AND ( 
	'%s' LIKE concat('%', keyword, '%') OR 
	keyword LIKE '%s' ) 
	ORDER BY length DESC LIMIT 1";

		$db_query = $wpdb->prepare( $query_str, $this->locale, $this->instance, $search_keyword_clean, '%' . $wpdb->esc_like( $search_keyword_clean ) . '%' );
		$results  = $wpdb->get_row( $db_query, ARRAY_A );

		if ( ! empty( $results ) ) {
			/* we found a record. update it with the clean one */
			$didumean = ( ! empty( $results['meaning'] ) ) ? $results['meaning'] : '';

			/* spam protection */
			if ( $is_spam || ! $this->logging ) {
				return $didumean;
			}
			if ( ! $this->logging ) {
				return '';
			}
			$count = $results['count'];

			if ( $last_ip != $results['last_ip'] ) {
				$count ++;
			}

			if ( $results['length'] > strlen( $search_keyword_clean ) ) {
				$query_sql                = $results['src_query'];
				$search_keyword_clean_sql = $results['keyword'];
				$length_sql               = $results['length'];
			} else {
				$query_sql                = $query;
				$search_keyword_clean_sql = $search_keyword_clean;
				$length_sql               = strlen( $search_keyword_clean );
			}

			/* bad word protection */
			$is_bad_word = is_bad_log_string( $query_sql, $this->locale );

			if ( false === $is_bad_word ) {

				/* delete this log. Because the new string is a bad word. */
				isrc_delete_log( $results['id'] );

			} else {
				/*
				* future version
				$sql_instances   = explode( ',', $results['instance'] );
				$sql_instances[] = $this->instance;
				$sql_instances   = array_filter( array_unique( $sql_instances ) );
				$new_instances   = implode( ',', $sql_instances );
				*/

				$wpdb->update(
					"{$wpdb->prefix}isearch_logs",
					array(
						'keyword'   => $search_keyword_clean_sql,
						'src_query' => $query_sql,
						'meaning'   => $didumean,
						'length'    => $length_sql,
						'count'     => $count,
						'lang'      => $this->locale,
						'instance'  => $this->instance,
						'last_ip'   => $last_ip
					),
					array( 'id' => $results['id'] ),
					array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%s',
						'%d',
						'%s'
					),
					array( '%d' )
				);
			}

		} else {

			/* spam protection */
			if ( $is_spam ) {
				return $didumean;
			}

			/* bad word protection */
			$is_bad_word = is_bad_log_string( $query, $this->locale );

			if ( false !== $is_bad_word && $this->logging ) {

				$wpdb->insert(
					"{$wpdb->prefix}isearch_logs",
					array(
						'keyword'   => $search_keyword_clean,
						'src_query' => $query,
						'meaning'   => $didumean,
						'length'    => strlen( $search_keyword_clean ),
						'count'     => 1,
						'lang'      => $this->locale,
						'instance'  => $this->instance,
						'last_ip'   => $this->get_the_user_ip()
					),
					array(
						'%s',
						'%s',
						'%s',
						'%d',
						'%d',
						'%s',
						'%d',
						'%s',
					)
				);

			}
		}

		return $didumean;

	}

	/**
	 * Get the users ip address.
	 *
	 * @return mixed
	 */
	public function get_the_user_ip() {

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Check if current user pass the spam protection.
	 *
	 * @param string $user_ip
	 *
	 * @return bool
	 */
	public function ip_spam_protection( $user_ip = '' ) {
		global $wpdb, $hash, $isrc_opt_adv;

		/*
		 * don't check if admin hash is in post
		 */
		if ( isset( $this->postdata['hash'] ) && ! empty( $this->postdata['hash'] ) ) {
			if ( $this->postdata['hash'] == $hash ) {
				return false;
			}
		}

		// check CORS
		$this->security_check();

		if ( empty( $user_ip ) ) {
			$user_ip = $this->get_the_user_ip();
		}

		if ( empty( $user_ip ) ) {
			return true;
		}

		/* define how many records a same ip is allowed to make logs a day */

		$user_allowed_times = ( isset( $isrc_opt_adv['ip_limit'] ) ) ? $isrc_opt_adv['ip_limit'] : 5;
		$query_str          = "SELECT COUNT( * ) FROM {$wpdb->prefix}isearch_logs WHERE DATE(time) = DATE(NOW()) AND last_ip = %s";
		$db_query           = $wpdb->prepare( $query_str, $user_ip );
		$results            = $wpdb->get_var( $db_query );

		if ( $results > $user_allowed_times ) {
			return true;
		}

		return false;

	}

	/**
	 * Security check.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function security_check() {
		global $site_url;

		if ( empty( $site_url ) ) {
			$site_url = get_site_url();
		}

		if ( "POST" == $_SERVER["REQUEST_METHOD"] ) {
			if ( isset( $_SERVER["HTTP_ORIGIN"] ) ) {
				if ( strpos( $site_url, $_SERVER["HTTP_ORIGIN"] ) !== 0 ) {
					exit( "CSRF protection in POST request" );
				}
			}
		} else {
			exit( '101' );
		}

	}

	/**
	 * Update flow or popularity index based on admin settings.
	 *
	 * @return boolean
	 */
	public function update_flow_or_popularity() {

		if ( $this->ip_spam_protection() ) {
			return false;
		}

		/* is logging enabled? */
		if ( $this->logging ) {
			$this->update_clickFlows();
		}

		/* is popularity logging enabled? */
		if ( $this->log_popularity ) {
			$this->update_popularity();
		}

		return true;
	}

	/**
	 * Update Click flows
	 *
	 * @return bool|false|int
	 */
	public function update_clickFlows() {
		global $wpdb;

		$flow = $this->get_clickFlows();

		if ( empty( $flow ) ) {
			return false;
		}

		$search_keyword_clean = trim( $this->postdata['lastNotFoundValue'] );

		$query_str = "SELECT * FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$this->locale}' AND (( '%s' LIKE concat('%', keyword, '%') AND meaning = '' ) OR src_query LIKE '%s' AND meaning = '') ORDER BY length DESC LIMIT 1";
		$db_query  = $wpdb->prepare( $query_str, $search_keyword_clean, '%' . $wpdb->esc_like( $search_keyword_clean ) . '%' );
		$results   = $wpdb->get_row( $db_query, ARRAY_A );

		/* we cant update a click flow if its not already inserted as a log into db */
		if ( ! empty( $results ) ) {

			$flow = $this->get_clickFlows( $results );

			$updated = $wpdb->update(
				"{$wpdb->prefix}isearch_logs",

				array(
					'flow' => $flow,
				),
				array( 'id' => $results['id'] ),
				array(
					'%s',
				),
				array( '%d' )
			);

			return $updated;

		}

		return false;
	}

	/**
	 * Get click flows.
	 *
	 * @param array $results
	 *
	 * @return array|mixed|string
	 */
	public function get_clickFlows( $results = array() ) {

		if ( ! isset( $this->postdata['lastNotFoundValue'] ) || empty( $this->postdata['lastNotFoundValue'] ) || empty( $this->postdata['current_src'] ) ) {
			return '';
		}

		$clicked = trim( $this->postdata['clicked'] );
		$found   = trim( $this->postdata['current_src'] );
		if ( ! empty( $clicked ) ) {
			$found .= ' (' . $clicked . ')';
		}

		if ( ! empty( $results['flow'] ) ) {
			$flow   = $results['flow'];
			$flow   = isrc_maybe_explode( $flow );
			$flow[] = $found;
			$flow   = isrc_implode( $flow );
		} else {
			$flow = $found;
		}

		return $flow;
	}

	/**
	 * Update popularity index. Called direct via ajax function. On user click to suggestion.
	 *
	 * @return string
	 */
	public function update_popularity() {
		global $wpdb;

		if ( ! isset( $this->postdata['selection_id'] ) || empty( $this->postdata['selection_id'] ) || ! isset( $this->postdata['type'] ) || empty( $this->postdata['type'] ) || ! isset( $this->postdata['ptn'] ) || empty( $this->postdata['ptn'] ) ) {
			return '';
		}

		$id    = $this->postdata['selection_id'];
		$type  = $this->postdata['type'];
		$ptn   = $this->postdata['ptn'];
		$title = html_entity_decode( $this->postdata['title'] );

		/* we have a isearch_popular table as backup on reindexing */
		$query = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}isearch_popular (id, type, ptn, title,lang) VALUES( %d, %s,%s, %s, %s ) ON DUPLICATE KEY UPDATE hit = hit + 1", $id, $type, $ptn, $title, $this->locale );
		$wpdb->query( $query );

		/* update also the index table */
		$table = 'isearch';
		if ( $type == 'taxonomy' ) {
			$table = 'isearch_taxonomy';
		}

		$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}{$table} SET hit = hit + 1 WHERE post_id = %d", $id );
		$wpdb->query( $query );

		exit();
	}

	public function isrc_get_instance() {

		if ( ! isset( $this->postdata['shortcodes'] ) ) {
			return false;
		}

		$sc_ids = $this->postdata['shortcodes'];
		$output = $this->postdata['output'];

		if ( ! is_array( $sc_ids ) ) {
			die;
		}

		global $isrc_front_main;

		if ( empty( $isrc_front_main ) ) {
			require_once ISRC_PLUGIN_DIR . '/front/front-includes.php';
		}

		$return = array();
		foreach ( $sc_ids as $shortcode ) {

			$form = isrc_get_instance( $shortcode, false );

			if ( $output == 'html' ) {
				echo $form;
				continue;
			}

			$return[ $shortcode ] = $form;
		}

		if ( $output == 'json' ) {
			wp_send_json( $return );
		}

	}


}

$isrc_ajax_class = new isrc_ajax_class();
