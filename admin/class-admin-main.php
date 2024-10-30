<?php
/**
 * i-Search admin class file
 *
 * This file is loaded only in admin.
 * The main class file for all the admin functions.
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

class isrc_main {

	public $thumb_size_name = 'isrc_thumb_';
	public $thumb_size_w = 50;
	public $thumb_size_h = 70;
	public $image_sizes_all_langs = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		register_activation_hook( ISRC_PLUGIN_FILE, array( $this, 'plugin_activation' ) );

		$this->build_defaults();

		add_action( 'admin_enqueue_scripts', array( $this, 'register_script_style' ) );

		add_action( 'save_post', 'update_post_isrc', 99, 2 );
		add_action( 'attachment_updated', 'update_post_isrc', 99, 2 );
		add_action( 'add_attachment', 'update_post_isrc', 99, 2 );
		add_action( 'delete_post', 'delete_isearch', 99, 1 );
		add_action( 'wp_trash_post', array( $this, 'trash_isearch' ), 99, 1 );
		add_action( 'untrashed_post', 'update_post_isrc', 99, 1 );
		add_action( 'admin_init', array( $this, 'add_nav_menu_meta_boxes' ) );
		add_action( 'wp_ajax_isrc_instance_preview', array( $this, 'show_instance_preview' ) );


		if ( defined( 'ISRC_WOOCOMMERCE_INSTALLED' ) ) {

			/*
			* action on comment approvement or delete or unapprove. We can not hook into WP transition_comment_status, its called BEFORE the woo comment data is updated.
			* Wee hook into updated meta action and check the meta key in the function.
			*/
			add_action( 'updated_post_meta', array( $this, 'isrc_post_meta_updated' ), 99, 3 );
		}


		/* updated object terms */
		/* not update on post save because post update will trigger later */
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {
			// TODO remove filters
		} else {
			add_action( 'set_object_terms', array( $this, 'updated_object_terms' ), 99, 6 );
		}

		/* taxonomy meta forms */
		add_action( 'init', array( $this, 'add_taxonomy_forms' ) );

		/* extra meta keys from cb */
		add_filter( 'isearch_cb_format_extra_meta_data', array( $this, 'isrc_format_special_meta_value' ), 10, 3 );

	}

	/**
	 * Build the class defaults.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function build_defaults() {
		$languages             = isrc_get_lang_codes();
		$image_sizes_all_langs = array();
		$image_sizes_temp      = array();
		foreach ( $languages as $code ) {
			$options = get_option( 'isrc_opt_' . $code );
			if ( isset( $options['front']['img'] ) ) {
				$image_size_to_check = $options['front']['thumb_size']['w'] . '_' . $options['front']['thumb_size']['h'];
				if ( ! in_array( $image_size_to_check, $image_sizes_temp ) ) {
					$image_sizes_temp[]      = $image_size_to_check;
					$image_sizes_all_langs[] = array(
						'lang'   => $code,
						'width'  => $options['front']['thumb_size']['w'],
						'height' => $options['front']['thumb_size']['h']
					);

				}
			}
		}
		$this->image_sizes_all_langs = $image_sizes_all_langs;
	}

	/**
	 * Show the preview in the settings section.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 *
	 * @return string
	 */
	public function show_instance_preview() {
		require_once ISRC_PLUGIN_DIR . '/front/front-includes.php';
		require_once( ISRC_PLUGIN_DIR . '/admin/menu/preview/instance-preview.php' );
		exit();
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

		$this->comment_approve_status( $meta_id, $post_id, $meta_key );

		$watched_keys = $this->isrc_get_content_builder_keys();
		$watched_keys = apply_filters( 'isearch_watch_meta_keys_for_update', $watched_keys );
		$watched_keys = array_values( array_filter( array_unique( $watched_keys ) ) );

		if ( empty( $watched_keys ) ) {
			return;
		}

		if ( in_array( $meta_key, $watched_keys ) ) {
			update_post_isrc( $post_id );
		}

		return;
	}

	/**
	 * If WooCommerce is installed rebuild the stars rating in search index.
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
	public function comment_approve_status( $meta_id, $post_id, $meta_key ) {

		/*  $meta_id is never used but WP MUST return it */
		if ( '_wc_rating_count' == $meta_key || '_wc_average_rating' == $meta_key ) {
			isrc_debug_log( 'Func: comment_approve_status post_id: ' . $post_id );
			update_post_isrc( $post_id );
		}

	}

	/**
	 * Get content builder keys as array. Only meta keys.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function isrc_get_content_builder_keys() {
		$option   = get_option( 'isrc_opt_content_' . isrc_get_lang_admin() );
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

	/**
	 * if a post term is updated via filter or else
	 *
	 * @param $post_id
	 * @param $terms
	 * @param $tt_ids
	 * @param $taxonomy
	 */
	public function updated_object_terms( $post_id, $terms, $tt_ids, $taxonomy ) {
		isrc_debug_log( 'Func: updated_object_terms post_id: ' . $post_id . ' terms: ' . print_r( $terms, true ) . ' Taxonomy: ' . $taxonomy );
		update_post_isrc( $post_id );
	}

	/**
	 * Register the taxonomy mta box forms in WP.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function add_taxonomy_forms() {
		global $isrc_opt;

		/* return if no taxonomies selected for suggestions */
		if ( ! isset( $isrc_opt['include_in_suggestions']['taxonomies'] ) ) {
			return false;
		}

		$taxonomies = $isrc_opt['include_in_suggestions']['taxonomies'];

		foreach ( $taxonomies as $taxonomy ) {
			/* add the ations for selected taxonomies*/
			add_action( "{$taxonomy}_edit_form", array( $this, 'add_the_taxonomy_html' ), 99, 1 );
			add_action( "edited_{$taxonomy}", array( $this, 'save_the_taxonomy_extra_form' ), 99, 0 );
			/* fires after a term is created. Maybe from another plugin like ACF */
			add_action( 'create_term', 'isrc_update_taxonomy_meta', 99, 1 );
			/* fires after a taxonomy is deleted */
			add_action( "delete_{$taxonomy}", 'delete_isearch_taxonomy', 99, 1 );
		}

		return true;
	}

	/**
	 * Render the taxonomy html form.
	 * html forms are in seperate file to keep this file clean and readable.
	 * We will include them if needed.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $term
	 *
	 * @return void    echoes the content directly
	 */
	public function add_the_taxonomy_html( $term ) {

		/* $term is used in the included php file */

		/* enqueue wp media functions for image upload */
		wp_enqueue_media();

		$html_form = ISRC_PLUGIN_DIR . '/admin/metabox/html-taxonomy-metabox.php';
		ob_start();
		require_once( $html_form );
		$content = ob_get_clean();
		echo $content;

	}

	/**
	 * Save the taxonomy meta data for i-Search.
	 * Is called only if the taxonomy is updated in wp admin.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void | boolean
	 */
	public function save_the_taxonomy_extra_form() {

		if ( isset( $_POST['i_src_tax'] ) && ! empty( $_POST['i_src_tax'] ) && isset( $_POST['tag_ID'] ) && isset( $_POST['taxonomy'] ) ) {

			/* check post data for types */
			if ( is_array( $_POST['i_src_tax'] ) && isset( $_POST['i_src_tax']['isrc_extra_terms'] ) && isset( $_POST['i_src_tax']['isrc_img_id'] ) ) {

				/* $_POST['i_src_tax']['isrc_img_id'] is always integer */
				settype( $_POST['i_src_tax']['isrc_img_id'],'integer' );

				/* $_POST['i_src_tax'] is checked for correct type */
				$metadata = $_POST['i_src_tax'];

				if( !is_array($metadata['isrc_extra_terms'])) {
					$metadata['isrc_extra_terms'] = trim( $metadata['isrc_extra_terms'] );
				}

				/* check taxonomy */
				$tag_id = (int) $_POST['tag_ID'];
				$taxonomy = !empty($_POST['taxonomy']) ? $_POST['taxonomy'] : 'post_tag';
				$tag = get_term( $tag_id, $taxonomy );
				if ( !$tag || is_wp_error( $tag ) ) {
					return false;
				}

				if ( isset( $metadata['isrc_extra_terms'] ) && ! empty( $metadata['isrc_extra_terms'] ) ) {
					$metadata['isrc_extra_terms'] = isrc_maybe_explode( $metadata['isrc_extra_terms'] );
				}

				$metadata = apply_filters( 'isrc_save_taxonomy_meta', $metadata, $tag_id, $taxonomy );

				update_isrc_metadata( $tag_id, $metadata );

			}

		}

	}


	/**
	 * Insert i-Search data to DB for Post Types Not Taxonomies.
	 * Insert the raw data. Its never called directly.
	 * Always do filters and cheks before insert.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $post_id int    The post id
	 * @param $query   string|array    The search terms as string comma seperated.
	 * @param $title   string    Title.
	 *
	 * @return boolean
	 */
	public function insert_isearch( $post_id, $query, $title ) {
		global $wpdb;

		if ( empty( $post_id ) || empty( $query ) ) {
			return false;
		}

		if ( empty( trim( $title ) ) ) {
			return false;
		}

		/* get the current popularity before delete_isearch */
		$old_popularity = $this->get_popularity( $post_id, 'post_type', true );

		delete_isearch( $post_id );

		foreach ( $query as $key => $val ) {
			$query[ $key ] = wp_specialchars_decode( $val );
		}

		$query_db = $this->format_terms( $query, 'post', $post_id );
		if ( ! $query_db ) {
			return false;
		}

		$query_db  = ',' . $query_db . ',';
		$out       = json_encode( $this->format_output( $post_id ) );
		$post_type = get_post_type( $post_id );

		$lang = isrc_get_lang( $post_id, 'post', $post_type );

		$wpdb->insert( "{$wpdb->prefix}isearch",
			array(
				'post_id'   => $post_id,
				'terms'     => $query_db,
				'title'     => substr( $title, 0, 10 ),
				'lang'      => $lang,
				'output'    => $out,
				'post_type' => $post_type
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			)
		);

		if ( ! empty( $old_popularity ) ) {
			$this->update_popularity( $post_id, 'post_type', $old_popularity );
		}

		return $query;
	}

	/**
	 * Get the popularity index.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $post_id int    The post/taxonomy id
	 * @param $type    string    post_type or taxonomy.
	 *
	 * @return integer
	 */
	public function get_popularity( $post_id, $type = 'post_type' ) {
		global $wpdb;

		if ( empty( $post_id ) ) {
			return false;
		}

		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}isearch_popular WHERE id = %d AND type = %s", $post_id, $type );
		$hits  = $wpdb->get_row( $query, ARRAY_A );

		return $hits;

	}

	/**
	 * format_terms
	 *
	 * Cleans the terms for a better search results.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $term
	 * @param string $type
	 * @param mixed  $type_data
	 *
	 * @return string|boolean
	 */
	public function format_terms( $term, $type = 'post', $type_data ) {

		$clean_terms = array();

		if ( ! is_array( $term ) ) {
			$words_arr = isrc_maybe_explode( $term );
		} else {
			$words_arr = $term;
		}

		/* get lang */
		if ( $type == 'post' ) {
			$lang = isrc_get_lang( $type_data, 'post' );
		} elseif ( $type == 'taxonomy' ) {
			$taxonomy_id   = $type_data->term_id;
			$taxonomy_name = $type_data->taxonomy;
			$lang          = isrc_get_lang( $taxonomy_id, 'taxonomy', $taxonomy_name );
		}

		$words_arr = apply_filters( 'format_terms_array', $words_arr );

		$words_arr = filter_bad_words( $words_arr, $lang );

		if ( is_array( $words_arr ) ) {
			foreach ( $words_arr as $single_term ) {
				$search_term = isrc_clean_txt( $single_term );
				if ( $search_term ) {
					$clean_terms[] = sanitize_title( json_decode( json_encode( $search_term, true ) ) );
				}
			}
		}

		if ( empty( $clean_terms ) ) {
			return false;
		}

		$clean_terms = isrc_filter_duplicates( $clean_terms );

		$query_db = isrc_implode( $clean_terms );

		return $query_db;
	}

	/**
	 * format_output
	 *
	 * generates the output string for DB.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param int    $post_id post ID
	 * @param string $type
	 *
	 * @return array
	 */
	public function format_output( $post_id, $type = 'post_type' ) {

		if ( $type == 'taxonomy' ) {
			return $this->format_output_taxonomy( $post_id );
		}

		$lang     = isrc_get_lang( $post_id, 'post' );
		$settings = get_option( 'isrc_opt_' . $lang, false );
		if ( ! $settings ) {
			return array();
		}

		$need_double_image = true;

		$post_type = get_post_type( $post_id );
		$out       = array();

		$out['id'] = (int) $post_id;

		$permalink  = get_permalink( $post_id );
		$out['url'] = $permalink;

		/* include thumb? */
		if ( isset( $settings['front']['img'] ) && $settings['front']['img'] ) {
			/* check if we have a custom image in current post */
			$isrc_meta = get_post_meta( $post_id, '_isrc', true );
			if ( isset( $isrc_meta['isrc_img_id'] ) && ! empty( $isrc_meta['isrc_img_id'] ) ) {
				$img_id = (int) $isrc_meta['isrc_img_id'];
			} else {
				/* we don't have a custom image. Use post thumbnail */
				$img_id = get_post_thumbnail_id( $post_id );
				/* Maybe its a attachment post type*/
				$is_image = wp_attachment_is( 'image', $post_id );
				if ( $is_image ) {
					$img_id = $post_id;
				}
			}

			if ( empty( $img_id ) ) {
				$img_id = false;
			}

			/* set base upload directory to save string data if we have double images no need for sending the url twice. */
			$out['cnt']              = content_url();
			$w                       = $settings['front']['thumb_size']['w'];
			$h                       = $settings['front']['thumb_size']['h'];
			$current_lang_thumb_name = "isrc_thumb_{$w}_{$h}";
			$thumb                   = $this->resize_image_otf( $img_id, $current_lang_thumb_name, $w, $h );
			if ( ! $thumb ) {
				$thumb = isrc_get_def_thumb( $current_lang_thumb_name );
			}

			$out['img'] = str_replace( $out['cnt'], '', $thumb );

			/* include thumb double size for advanced theme? */
			if ( $need_double_image ) {
				$thumb = $this->resize_image_otf( $img_id, $current_lang_thumb_name . '_x2', $w * 2, $h * 2 );
				if ( ! $thumb ) {
					$thumb = isrc_get_def_thumb( $current_lang_thumb_name . '_x2' );
				}

				$out['img2'] = str_replace( $out['cnt'], '', $thumb );
			}
		}

		/* include excerpt? */
		if ( isset( $settings['front']['excerpt'] ) && $settings['front']['excerpt'] ) {
			/* add isearch to global shortcode tags because its not registered here*/
			global $shortcode_tags, $post;
			$shortcode_tags['isrc_ajax_search'] = 'isrc_ajax_search';
			/* info: get_the_excerpt need global post data */
			$post = get_post( $post_id, OBJECT );
			setup_postdata( $post_id );
			$excerpt = get_the_excerpt( $post );
			wp_reset_postdata();

			if ( ! empty( $excerpt ) ) {
				$excerpt = strip_shortcodes( $excerpt );
				$excerpt = str_replace( array( "<br>", "</br>", "</p>" ), ' ', $excerpt );
				$excerpt = trim( strip_tags( $excerpt ) );
				$excerpt = str_replace( array( "\n\r", "\n", "\r" ), ' ', $excerpt );
				$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
				$excerpt = strtr( $excerpt, array_flip( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) ) );
				$excerpt = trim( $excerpt, chr( 0xC2 ) . chr( 0xA0 ) );
				/* if advanced theme selected, trimm to 50 words */
				$trimchar       = 100;
				$excerpt        = wp_trim_words( $excerpt, $trimchar );
				$out['excerpt'] = trim( $excerpt );
			}
		}

		/* include post categories? */
		if ( isset( $settings['front']['cat'] ) && $settings['front']['cat'] ) {
			$categories = array();
			$terms      = get_the_terms( $post_id, 'category' );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					/* hide word? */
					$catname = filter_hide_words( $term->name, $lang );
					if ( $catname !== false ) {
						$categories[] = $term->name;
					}
				}
				$out['p_cats'] = implode( ', ', $categories );
			}
		}


		/* WooCommerce */
		if ( defined( "ISRC_WOOCOMMERCE_INSTALLED" ) && $post_type == 'product' ) {
			$product = wc_get_product( $post_id );

			/* include price? */
			if ( isset( $settings['woo']['price'] ) && $settings['woo']['price'] && $product->is_visible() ) {
				$price = $product->get_price_html();
				if ( $price ) {
					$out['price'] = $price;
				}
			}

			/* product type? */
			$product_type   = $product->get_type();
			$out['wc_type'] = $product_type;

			if ( $product_type == 'variable' ) {
				$product_variations = $product->get_available_variations();
			} else {
				$product_variations = array();
			}

			if ( ! empty( $product_variations ) ) {

				foreach ( $product_variations as $key => $variation ) {
					if ( ! $variation['variation_is_active'] || ! $variation['variation_is_visible'] ) {
						continue;
					}
					/* get only out of stock */
					/* variation names */
					$attr_labels = array();
					foreach ( $variation['attributes'] as $attkey => $attval ) {
						if ( ! empty( trim( $attval ) ) ) {
							if ( strpos( $attkey, 'attribute_pa' ) !== false ) {
								/* its a taxonomy*/
								$taxonomy      = str_replace( 'attribute_', '', $attkey );
								$taxonomy_slug = $attval;
								$term          = get_term_by( 'slug', $taxonomy_slug, $taxonomy );
								if ( false !== $term ) {
									$attr_labels[] = $term->name;
								}
							} else {
								$attr_labels[] = wc_attribute_label( $attval, $product );
							}
						}
					}
					$out['wc_var'][ $key ] = array(
						'is_in_stock'        => $variation['is_in_stock'],
						'backorders_allowed' => $variation['backorders_allowed'],
						'price_html'         => $variation['price_html'],
						'attr_labels'        => $attr_labels,
					);
				}
			}

			/* include rating? */
			if ( isset( $settings['woo']['rating'] ) && $product->is_visible() ) {
				$rating_count = get_post_meta( $post_id, '_wc_review_count', true );
				$average      = get_post_meta( $post_id, '_wc_average_rating', true );
				if ( ! empty( $rating_count ) && ! empty( $average ) ) {
					$out['rtng']['rat_c'] = $rating_count;
					$out['rtng']['rat_p'] = ( $average / 5 ) * 100;
				}
			}

			/* include woo categories? */
			if ( isset( $settings['woo']['cat'] ) && $settings['woo']['cat'] && $product->is_visible() ) {
				$categories = array();
				$terms      = get_the_terms( $post_id, 'product_cat' );
				if ( $terms ) {
					foreach ( $terms as $term ) {
						$hide = filter_hide_words( $term->name, $lang );
						if ( $hide !== false ) {
							$categories[] = $term->name;
						}
					}
					$out['p_cats'] = implode( ', ', $categories );
				}
			}

			/* include onsale? */
			if ( isset( $settings['woo']['sale'] ) && $settings['woo']['sale'] && $product->is_visible() ) {
				if ( $product->is_on_sale() ) {
					$out['badges']['on_sale'] = true;
				}
			}

			/* include out of stock? */
			if ( isset( $settings['woo']['outofstock'] ) && $settings['woo']['outofstock'] && $product->is_visible() && ! $product->is_in_stock() ) {
				$out['badges']['outofstock'] = true;
				unset( $out['badges']['on_sale'] );
			}

			/* include backorder? */
			if ( isset( $settings['woo']['backorder'] ) && $settings['woo']['backorder'] && $product->is_visible() && $product->is_on_backorder() ) {
				$out['badges']['backorder'] = true;
			}

			/* include featured */
			if ( isset( $settings['woo']['featured'] ) && $settings['woo']['featured'] && $product->is_visible() ) {
				if ( $product->is_featured() ) {
					$out['badges']['featured'] = true;
				}
			}

			/* if out of stock disable add to cart */
			if ( isset( $out['badges']['outofstock'] ) ) {
				$out['atc_off'] = true;
			} else {
				$out['atc_off'] = false;
			}


		}
		/* END WooCommerce */

		$title        = get_the_title( $post_id );
		$out['value'] = wp_specialchars_decode( $title );

		$post_type_obj = get_post_type_object( $post_type );
		$out['ptn']    = $post_type_obj->name;
		$out['type']   = 'post_type';

		/* content builder */
		$content_builder = get_option( 'isrc_opt_content_' . $lang, array() );
		if ( isset( $content_builder['builder_data'][ $post_type ] ) && ! empty( $content_builder['builder_data'][ $post_type ] ) ) {
			$content_data = $content_builder['builder_data'][ $post_type ];
			foreach ( $content_data as $position => $data_arr ) {
				foreach ( $data_arr as $key => $data ) {
					/* taxonomy */
					if ( $data['data_type'] == 'taxonomy' ) {
						$terms = get_the_terms( $post_id, $data['data_key'] );
						if ( ! is_wp_error( $terms ) && $terms !== false ) {
							$terms_arr = array();
							foreach ( $terms as $term ) {
								$terms_arr[] = $term->name;
							}
							if ( ! empty( $terms_arr ) ) {
								$terms                = implode( ', ', $terms_arr );
								$outkey               = 'tx_' . str_replace( '_cb_ex_mk_', '', $data['data_key'] );
								$out['cb'][ $outkey ] = array(
									'val' => $terms,
								);

							}
						}
					}
					/* meta value */
					if ( $data['data_type'] == 'meta_key' ) {
						/* ACF */
						if ( strpos( $data['data_key'], 'field_' ) !== false && function_exists( 'get_field' ) ) {
							$meta_data = isrc_get_acf_value( $post_id, $data['data_key'] );
						} elseif ( strpos( $data['data_key'], '_cb_ex_mk_' ) !== false ) {
							$meta_data = apply_filters( 'isearch_cb_format_extra_meta_data', '', $post_id, str_replace( '_cb_ex_mk_', '', $data['data_key'] ) );
							if ( ! empty( trim( $meta_data ) ) ) {
								$meta_data = array( $meta_data );
							} else {
								$meta_data = false;
							}
						} else {
							$meta_data = get_post_meta( $post_id, $data['data_key'] );
						}

						if ( $meta_data !== false && ! empty( $meta_data ) ) {
							$terms_arr = array();
							foreach ( $meta_data as $term ) {
								if ( is_array( $term ) ) {
									$term = implode( ', ', $term );
								}
								$terms_arr[] = $term;
							}
							if ( ! empty( $terms_arr ) ) {
								$terms                = implode( ', ', $terms_arr );
								$outkey               = 'mk_' . str_replace( '_cb_ex_mk_', '', $data['data_key'] );
								$out['cb'][ $outkey ] = array(
									'val' => $terms,
								);
							}

						}

					}

				}
			}

		}

		$out_array = apply_filters( "isrc_format_output", $out );

		return $out_array;
	}

	/**
	 * generates the DB string for taxonomies.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param int $taxonomy_id
	 *
	 * @return array
	 */
	public function format_output_taxonomy( $taxonomy_id ) {

		if ( ! $taxonomy_id ) {
			return array();
		}

		$term     = get_term( $taxonomy_id );
		$taxonomy = $term->taxonomy;
		$lang     = isrc_get_lang( $taxonomy_id, 'taxonomy', $taxonomy );
		$settings = get_option( 'isrc_opt_' . $lang, false );

		if ( ! $settings ) {
			return array();
		}

		$need_double_image = true;

		$taxonomy_id = (int) $taxonomy_id;

		$metadata = isrc_get_taxonomy_meta( $taxonomy_id );

		$out['id'] = $taxonomy_id;

		$permalink  = get_term_link( $taxonomy_id, $taxonomy );
		$out['url'] = $permalink;

		/* include thumb? */
		if ( isset( $settings['front']['img'] ) && $settings['front']['img'] ) {

			/* is woocommerce product cat? */
			if ( $taxonomy == 'product_cat' ) {
				if ( ! isset( $metadata['isrc_img_id'] ) || empty( $metadata['isrc_img_id'] ) ) {
					/* if woo cat image available get it */
					$woo_cat_img_id = get_term_meta( $taxonomy_id, 'thumbnail_id', true );
					if ( $woo_cat_img_id ) {
						$metadata['isrc_img_id'] = $woo_cat_img_id;
					}
				}
			}

			/* set base upload directory to save string data if we have double images no need for sending the url twice. */
			$out['cnt']              = content_url();
			$w                       = $settings['front']['thumb_size']['w'];
			$h                       = $settings['front']['thumb_size']['h'];
			$current_lang_thumb_name = "isrc_thumb_{$w}_{$h}";
			if ( isset( $metadata['isrc_img_id'] ) && ! empty( $metadata['isrc_img_id'] ) ) {
				$img_id = (int) $metadata['isrc_img_id'];
				$thumb  = $this->resize_image_otf( $img_id, $current_lang_thumb_name, $w, $h );
			} else {
				$thumb = false;
			}

			if ( ! $thumb ) {
				$thumb = isrc_get_def_thumb( $current_lang_thumb_name );
			}

			$out['img'] = str_replace( $out['cnt'], '', $thumb );

			/* include thumb double size for advanced theme? */
			if ( $need_double_image ) {
				if ( isset( $metadata['isrc_img_id'] ) && ! empty( $metadata['isrc_img_id'] ) ) {
					$img_id = (int) $metadata['isrc_img_id'];
					$thumb2 = $this->resize_image_otf( $img_id, $current_lang_thumb_name . '_x2', $w * 2, $h * 2 );
				} else {
					$thumb2 = false;
				}

				if ( ! $thumb2 ) {
					$thumb2 = isrc_get_def_thumb( $current_lang_thumb_name . '_x2' );
				}

				$out['img2'] = str_replace( $out['cnt'], '', $thumb2 );
			}
		}

		/* include excerpt? */
		if ( isset( $settings['front']['excerpt'] ) && $settings['front']['excerpt'] ) {
			$excerpt = term_description( $taxonomy_id );
			if ( ! empty( $excerpt ) ) {
				$excerpt        = str_replace( array( "<br>", "</br>", "</p>" ), ' ', $excerpt );
				$excerpt        = trim( strip_tags( $excerpt ) );
				$excerpt        = str_replace( array( "\n\r", "\n", "\r" ), ' ', $excerpt );
				$excerpt        = strip_shortcodes( $excerpt );
				$excerpt        = str_replace( ']]>', ']]&gt;', $excerpt );
				$excerpt        = strtr( $excerpt, array_flip( get_html_translation_table( HTML_ENTITIES, ENT_QUOTES ) ) );
				$excerpt        = trim( $excerpt, chr( 0xC2 ) . chr( 0xA0 ) );
				$trimchar       = 100;
				$excerpt        = wp_trim_words( $excerpt, $trimchar );
				$excerpt        = strip_shortcodes( $excerpt );
				$out['excerpt'] = $excerpt;
			}
		}

		$title = $term->name;

		$out['value'] = wp_specialchars_decode( $title );

		$out['ptn']  = $term->taxonomy;
		$out['type'] = 'taxonomy';

		$out_array = apply_filters( 'isrc_format_output_taxonomy', $out );

		return $out_array;
	}

	/**
	 * resize_image_otf
	 *
	 * Resize the suggestion thumbnail on the fly and save it.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param         $id
	 * @param  string $size
	 * @param int     $width
	 * @param int     $height
	 * @param bool    $crop
	 *
	 * @return array|boolean
	 */
	public function resize_image_otf( $id, $size, $width = 0, $height = 0, $crop = false ) {

		if ( empty( $id ) ) {
			return false;
		}

		$imagedata = wp_get_attachment_metadata( $id );

		if ( ! $imagedata ) {
			return false;
		}
		/* check if file exists */
		$img_file = WP_CONTENT_DIR . '/uploads/' . $imagedata['file'];
		if ( ! file_exists( $img_file ) ) {
			return false;
		}
		/* delete old thumbnails created by i-Search if image height or width changed from settings */
		$imagedata = $this->isrc_delete_old_thumbs( $id, $imagedata );
		if ( is_array( $imagedata ) && isset( $imagedata['sizes'][ $size ] ) ) {
			$image_attributes = wp_get_attachment_image_src( $id, $size );

			return $image_attributes[0];
		}

		/* we dont have the needed size. Create it */
		$resized = image_make_intermediate_size(
			get_attached_file( $id ),
			$width,
			$height,
			$crop
		);

		if ( ! $resized ) {
			$image_attributes = wp_get_attachment_image_src( $id, $size );

			return $image_attributes[0];
		}

		/* Save image meta, or WP can't see that the thumb exists now */
		$imagedata['sizes'][ $size ] = $resized;
		wp_update_attachment_metadata( $id, $imagedata );

		/* Return the array for displaying the resized image */
		$image_attributes = wp_get_attachment_image_src( $id, $size );
		$att_url          = $image_attributes[0];

		return dirname( $att_url ) . '/' . $resized['file'];
	}

	/**
	 * isrc_delete_old_thumbs
	 *
	 * Delete unused thumbnails from folder
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param int   $attachment_id
	 * @param array $imagedata
	 * @param bool  $is_taxonomy
	 *
	 * @return boolean|array
	 */
	public function isrc_delete_old_thumbs( $attachment_id, $imagedata = array(), $is_taxonomy = false ) {

		if ( empty( $attachment_id ) ) {
			return false;
		}

		$to_delete  = 'isrc_thumb';
		$condition  = 'like';
		$not_delete = array();

		foreach ( $this->image_sizes_all_langs as $image_sizes_all ) {
			$not_delete[] = "isrc_thumb_{$image_sizes_all['width']}_{$image_sizes_all['height']}";
			$not_delete[] = "isrc_thumb_{$image_sizes_all['width']}_{$image_sizes_all['height']}_x2";
		}

		if ( empty( $not_delete ) ) {
			return false;
		}

		if ( empty( $imagedata ) ) {
			$imagedata = wp_get_attachment_metadata( $attachment_id );
		}

		/* loop and find matches */
		$sizes            = $imagedata['sizes'];
		$need_update_meta = false;

		foreach ( $sizes as $size => $val ) {
			if ( $condition == 'like' ) {
				if ( strpos( $size, $to_delete ) !== false && ! in_array( $size, $not_delete ) ) {
					$delete = $this->_helper_isrc_delete_old_thumbs( $attachment_id, $size );
					if ( $delete ) {
						/* unset from metadata */
						unset( $imagedata['sizes'][ $size ] );
						$need_update_meta = true;
					}
				}
			}
		}

		/* update image data */
		if ( $need_update_meta ) {
			wp_update_attachment_metadata( $attachment_id, $imagedata );
		}

		return $imagedata;
	}

	/**
	 * Helper file for deleting files
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $attachment_id
	 * @param string $size
	 *
	 * @return boolean
	 */
	public function _helper_isrc_delete_old_thumbs( $attachment_id = '', $size = '' ) {

		if ( empty( $attachment_id ) || empty( $size ) ) {
			return false;
		}

		$img_data = wp_get_attachment_image_src( $attachment_id, $size );

		/* check if is_intermediate*/
		if ( ! $img_data[3] ) {
			return false;
		}

		$file_path = $_SERVER['DOCUMENT_ROOT'] . parse_url( $img_data[0], PHP_URL_PATH );
		wp_delete_file( $file_path );

		return true;
	}

	/**
	 * Update the popularity index. Called on insert i-Search functions
	 * To update the fresh zero popularity with the original.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param      $post_id int    The post/taxonomy id
	 * @param      $type    string    post_type or taxonomy.
	 *
	 * @param null $old_popularity
	 *
	 * @return boolean
	 */
	public function update_popularity( $post_id, $type = 'post_type', $old_popularity = null ) {
		global $wpdb;

		if ( empty( $post_id ) ) {
			return false;
		}

		$query = $wpdb->prepare( "SELECT hit FROM {$wpdb->prefix}isearch_popular WHERE id = %d AND type = %s", $post_id, $type );
		$hits  = $wpdb->get_var( $query );

		if ( ! empty( $old_popularity ) && empty( $hits ) ) {
			/* insert in popular table if we have a $old_popularity and current popularity is null */
			$wpdb->insert( "{$wpdb->prefix}isearch_popular",
				$old_popularity,
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
				)
			);
			$hits = $old_popularity['hit'];
		}

		/* update also the index table */
		$table = 'isearch';
		if ( $type == 'taxonomy' ) {
			$table = 'isearch_taxonomy';
		}

		if ( ! empty( $hits ) ) {
			$query = $wpdb->prepare( "UPDATE {$wpdb->prefix}{$table} SET hit = %d WHERE post_id = %d", $hits, $post_id );
			$wpdb->query( $query );
		}

		return true;
	}

	/**
	 * Get format special meta values
	 *
	 * @param int    $post_id
	 * @param string $key
	 * @param string $output
	 *
	 * @return string
	 */
	public function isrc_format_special_meta_value( $output = '', $post_id = 0, $key = '' ) {
		/* post_modified_date */
		if ( $key == 'post_modified_date' ) {
			$output = get_the_modified_date( '', (int) $post_id );
		}

		if ( $key == 'post_created_date' ) {
			$output = get_the_date( '', (int) $post_id );
		}

		return $output;
	}

	/**
	 * Insert i-Search data to DB Taxonomies Not for Post Types.
	 * Insert the raw data. Its never called directly.
	 * Always do filters and cheks before insert.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $taxonomy_id int    The taxonomy id
	 * @param $insert_data array    The search terms as string comma seperated.
	 * @param $title       string    Title.
	 *
	 * @return boolean
	 */
	public function insert_isearch_taxonomy( $taxonomy_id, $insert_data, $title ) {
		global $wpdb;

		if ( empty( trim( $title ) ) ) {
			return false;
		}

		$taxonomy_id = (int) $taxonomy_id;

		if ( empty( $taxonomy_id ) || empty( $insert_data ) ) {
			return false;
		}

		foreach ( $insert_data as $key => $val ) {
			$insert_data[ $key ] = wp_specialchars_decode( $val );
		}

		$term = get_term( (int) $taxonomy_id );

		/* get the current popularity before delete_isearch */
		$old_popularity = $this->get_popularity( $taxonomy_id, 'taxonomy', true );

		delete_isearch( $taxonomy_id, 'taxonomy' );

		$query_db = $this->format_terms( $insert_data, 'taxonomy', $term );

		if ( ! $query_db ) {
			return false;
		}

		$lang = isrc_get_lang( $taxonomy_id, 'taxonomy', $term->taxonomy );

		$out = json_encode( $this->format_output_taxonomy( $taxonomy_id ) );

		$wpdb->insert( "{$wpdb->prefix}isearch_taxonomy",
			array(
				'post_id'   => $taxonomy_id,
				'terms'     => $query_db,
				'title'     => substr( $title, 0, 10 ),
				'lang'      => $lang,
				'output'    => $out,
				'post_type' => $term->taxonomy,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		if ( ! empty( $old_popularity ) ) {
			$this->update_popularity( $taxonomy_id, 'taxonomy', $old_popularity );
		}

		return $query_db;
	}

	/**
	 * Delete i-src if post is in trash (regenerate on untrash is called in construct).
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param int $post_id post ID
	 *
	 * @return void
	 */
	public function trash_isearch( $post_id ) {
		remove_action( 'save_post', 'update_post_isrc', 99 );
		delete_isearch( $post_id );
	}

	/**
	 * Register scripts and styles
	 *
	 * @return void
	 */
	public function register_script_style() {

		wp_register_script( 'isrc-tagbox', ISRC_PLUGIN_URL . '/admin/menu/js/tagbox/tagbox.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_localize_script( 'isrc-tagbox', 'isrc_tagsSuggestL10n', array(
			'tagDelimiter' => ',',
			'removeTerm'   => 'Remove Term',
			'termSelected' => 'Term Selected',
			'termAdded'    => 'Term Added',
			'termRemoved'  => 'Term Removed',
		) );

	}


	/**
	 * function on plugin activation.
	 *
	 * Call other activation function from here
	 *
	 * @since  2.0.0
	 * @access static
	 *
	 * @return void
	 */
	public function plugin_activation() {

		$isrc_db_ver_inst   = get_option( "isrc_db_ver" );
		$isrc_db_ver_plugin = I_SRC_DB_VER;

		if ( ( $isrc_db_ver_inst != $isrc_db_ver_plugin ) || ! $isrc_db_ver_inst ) {
			$this->install_isrc_db();
		}

		/* add default options */
		$options = get_option( 'isrc_opt_' . isrc_get_lang_admin() );
		if ( ! $options ) {
			$defaults = array(
				'post_types' => array( 'post' ),
				'includes'   => array( 'title' ),
				'front'      => array(
					'ajx_enabled'    => '1',
					'kw_handle'      => 'split',
					'inp_label'      => __( 'Search...', 'i_search' ),
					'template'       => 'advanced',
					'order_by'       => 'post_id',
					'maxHeight'      => 'calculate',
					'img'            => '1',
					'subm_label'     => __( 'Submit', 'i_search' ),
					'hook_class'     => 's',
					'thumb_size'     => array( 'w' => 50, 'h' => 70 ),
					'min_char'       => '3',
					'max_res'        => '10',
					'view_all'       => '1',
					'tabs_ed'        => '1',
					'view_all_txt'   => __( 'View All', 'i_search' ),
					'noresult_label' => __( 'No results', 'i_search' ),
					'didumean_label' => __( 'Did you mean', 'i_search' ),
					'popular_label'  => __( 'No Results. Popular Searches', 'i_search' ),
				)
			);
			update_option( 'isrc_opt_' . isrc_get_lang_admin(), $defaults );
		}

		/* add adv options */
		$options = get_option( 'isrc_opt_adv_' . isrc_get_lang_admin() );
		if ( ! $options ) {
			$defaults = array(
				'isrc_bad_words'  => '',
				'isrc_hide_words' => '',
				'ip_limit'        => 5,
			);
			update_option( 'isrc_opt_adv_' . isrc_get_lang_admin(), $defaults );
		}

		/* Set security hash */
		update_option( 'isrc_hash', isrc_randomString() );

	}

	/**
	 * Install the DB tables for i-src.
	 *
	 * @since  2.0.0
	 * @access private
	 *
	 * @return boolean
	 */
	public function install_isrc_db() {
		global $wpdb;

		$table_name            = $wpdb->prefix . 'isearch';
		$table_name_taxonomies = $wpdb->prefix . 'isearch_taxonomy';
		$temp_table_name       = $wpdb->prefix . 'isearch_temp';
		$logs_table_name       = $wpdb->prefix . 'isearch_logs';
		$shortcodes_table_name = $wpdb->prefix . 'isearch_shortcodes';
		$meta_table_name       = $wpdb->prefix . 'isearch_metadata';
		$popularity_table_name = $wpdb->prefix . 'isearch_popular';

		$sql = "CREATE TABLE $table_name (
			post_id int(11) NOT NULL,
			terms longtext NOT NULL,
			title varchar(12) NOT NULL DEFAULT '',
			output longtext NOT NULL,
			post_type varchar(32) NOT NULL,
			lang varchar(8) NOT NULL DEFAULT '',
			hit int(11) NOT NULL DEFAULT '0',
			UNIQUE KEY postidunique (post_id)
			);
			
			CREATE TABLE $table_name_taxonomies (
			post_id int(11) NOT NULL,
			terms longtext NOT NULL,
			title varchar(12) NOT NULL DEFAULT '',
			output longtext NOT NULL,
			post_type varchar(32) NOT NULL,
			lang varchar(8) NOT NULL DEFAULT '',
			hit int(11) NOT NULL DEFAULT '0',
			UNIQUE KEY taxounique (post_id)
			);
			
			CREATE TABLE $popularity_table_name (
			id int(11) NOT NULL,
			ptn varchar(20) NOT NULL,
			type varchar(64) NOT NULL,
			title varchar(128) NOT NULL DEFAULT '',
			lang varchar(8) NOT NULL DEFAULT '',
			hit int(11) NOT NULL DEFAULT '1',
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY popularitykey (id,type)
			);

			CREATE TABLE $temp_table_name (
			post_id int(11) NOT NULL,
			type varchar(64) NOT NULL,
			UNIQUE KEY tempunique (post_id, type)
			);

			CREATE TABLE $shortcodes_table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			title varchar(128) NOT NULL DEFAULT '',
			settings longtext NOT NULL,
			lang varchar(8) NOT NULL DEFAULT '',
			UNIQUE KEY scunique (id)
			);

			CREATE TABLE $meta_table_name (
			meta_id int(11) NOT NULL,
			option_value longtext NOT NULL,
			lang varchar(8) NOT NULL DEFAULT '',
			PRIMARY KEY  (meta_id)
			);
			
			CREATE TABLE $logs_table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			status int(11) NOT NULL DEFAULT '0',
			instance int(11) NOT NULL DEFAULT '0',
			lang varchar(8) NOT NULL DEFAULT 'en',
			keyword varchar(64) NOT NULL,
			src_query varchar(64) NOT NULL,
			meaning varchar(64) DEFAULT NULL,
			flow text,
			length int(11) NOT NULL DEFAULT '0',
			count int(11) NOT NULL DEFAULT '0',
			last_ip varchar(15) NOT NULL,
			time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY logsunique (src_query,instance)
			);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$tablecreate = dbDelta( $sql );

		if ( ! empty( $tablecreate ) ) {
			update_option( 'isrc_db_ver', I_SRC_DB_VER );

			return true;
		} else {
			return false;
		}

	}

	/**
	 * Add menu meta box
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box(
			'isrc_nav_link',
			__( 'i-Search' ),
			array( $this, 'nav_menu_link' ),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Render menu metabox html
	 */
	public function nav_menu_link() {
		$menu_item = ISRC_PLUGIN_DIR . '/admin/menu/html-nav-menu-admin.php';
		require_once( $menu_item );
	}


}

$isrc_main = new isrc_main();