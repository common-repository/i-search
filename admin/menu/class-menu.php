<?php
/**
 * i-Search Menu Class
 *
 * This file is loaded only in admin.
 * The main class file to build the i-Search options menu.
 * Menu html files are separated and loaded with 'include_once' into this classes submenu_page_callback function to keep this file clean and readable.
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

class isrc_menu {

	public $logs_obj;
	public $sc_obj;
	public $lang;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		/*
		 * Set defaults.
		 */
		add_action( 'init', array( $this, 'set_defaults' ), 1 );

		/*
		 * Settings page save functions. 
		 * We need a redirect before any output. Save actions in save_options. Or you will get a php error 
		 */
		add_action( 'init', array( $this, 'save_options' ), 99 );

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ), 10, 1 );
		add_filter( 'isearch_cb_add_extra_meta_data', array( $this, 'isrc_extra_content_metas' ), 1, 2 );


		add_action( 'isrc_admin_notice', array( $this, 'admin_notices' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
		add_filter( 'set-screen-option', array( $this, 'screen_option_save' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_script_style' ) );

		/* 
		 * Ajax functions for the options menu. 
		 * Ajax actions are not public and can not be called outside of the admin area.
		 */
		add_action( 'wp_ajax_isrc_exlude_taxonomies', array( $this, 'get_taxonomy_by_name' ) );
		add_action( 'wp_ajax_isrc_menu_db_actions', array( $this, 'menu_db_actions' ) );
		add_action( 'wp_ajax_isrc_save_tags', array( $this, 'save_tags' ) );
		add_action( 'wp_ajax__ajax_fetch_remodal', array( $this, 'ajax_fetch_remodal_callback' ) );
		add_action( 'wp_ajax_isrc_help', array( $this, 'get_help_html' ) );
		add_action( 'wp_ajax_isrc_regenerate', array( $this, 'reindex_screen' ) );
		add_action( 'wp_ajax_isrc_get_meta_keys', array( $this, 'get_the_meta_keys' ) );
		add_action( 'wp_ajax_isrc_get_taxo_keys', array( $this, 'get_the_taxo_keys' ) );
		add_action( 'wp_ajax_isrc_fetch_analyse_list', array( $this, 'get_ajax_analyse_table' ) );
		add_action( 'wp_ajax_isrc_select2', array( $this, 'select2_get_post_types' ) );
		add_action( 'wp_ajax_isrc_select2_taxonomies', array( $this, 'select2_get_taxonomies' ) );
		add_action( 'wp_ajax_isrc_cnt_example_data', array( $this, 'build_content_data_example' ) );
		add_action( 'wp_ajax_isrc_set_preview_data', array( $this, 'sb_set_preview_data' ) );
		add_action( 'wp_ajax_isrc_clone_instance', array( $this, 'clone_instance' ) );
	}

	/**
	 * Set the defaults on init.
	 *
	 * @since  3.1.0
	 * @access public
	 *
	 *
	 * @return void
	 */
	public function set_defaults() {
		$this->lang = isrc_get_lang_admin();
	}


	/**
	 * Clone a instance.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function clone_instance() {
		$status = array(
			'status' => 'error'
		);

		if ( ! i_src_security_check() ) {
			wp_send_json( $status );
		}

		check_ajax_referer( 'isrc_settings', 'nonce' );

		if ( ! isset( $_POST['instance_id'] ) || empty( $_POST['instance_id'] ) ) {
			wp_send_json( $status );
		}
		$sc_id = (int) $_POST['instance_id'];
		/* get all from source */
		global $wpdb;
		$sql    = "SELECT * FROM {$wpdb->prefix}isearch_shortcodes WHERE id = '{$sc_id}'";
		$result = $wpdb->get_row( $sql, 'ARRAY_A' );
		if ( empty( $result ) ) {
			wp_send_json( $status );
		}

		/* change title and unset id */
		unset( $result['id'] );
		$result['title'] = $result['title'] . ' (Clone)';

		/* insert */
		$wpdb->insert(
			"{$wpdb->prefix}isearch_shortcodes",
			$result,
			array(
				'%s',
				'%s',
				'%s'
			)
		);

		$sc_id = $wpdb->insert_id;
		if ( $sc_id ) {
			$status = array(
				'status' => 'success'
			);

			wp_send_json( $status );
		}

		wp_send_json( $status );

	}

	/**
	 * Add class to admin body.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function add_body_class( $classes = '' ) {
		global $isrc_admin_page;

		$screen = get_current_screen();
		/*
		 * Check if current screen is $isrc_admin_page
		 * Don't add class if not
		 */
		if ( $screen->id != $isrc_admin_page ) {
			return $classes;
		}

		$css = 'isrc_options_page';

		if ( isset( $_GET['tab'] ) ) {
			$css .= ' ' . sanitize_html_class( $_GET['tab'] ) ;
		}

		if ( isset( $_GET['sub-tab'] ) ) {
			$css .= ' ' . sanitize_html_class( $_GET['sub-tab'] ) ;
		}

		return "$classes $css";
	}

	/**
	 * Save the options for menu tabs.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return boolean
	 */
	public function save_options() {

		if ( ! i_src_security_check() ) {
			return false;
		}

		if ( isset( $_POST['isrc_opt_page'] ) && $_POST['isrc_opt_page'] == 'general' ) {
			/* we have a request from the general options page. Save it */
			$this->_save_options_general();
		}

		if ( isset( $_POST['isrc_opt_page'] ) && $_POST['isrc_opt_page'] == 'advanced' ) {
			/* we have a request from the general options page. Save it */
			$this->save_options_advanced();
		}

		if ( isset( $_POST['isrc_opt_page'] ) && $_POST['isrc_opt_page'] == 'shortcode_builder' ) {
			/* we have a request from the general options page. Save it */
			$this->save_options_shortcode_builder();
		}

		if ( isset( $_POST['isrc_opt_page'] ) && $_POST['isrc_opt_page'] == 'content_builder' ) {
			/* we have a request from the general options page. Save it */
			$this->save_options_content_builder();
		}

		return true;
	}

	/**
	 * Save the options for general options menu tab.
	 * After saving the options a wp_safe_redirect is called.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function _save_options_general() {

		if ( isset( $_POST['save'] ) && is_array( $_POST['isrc_opt'] ) ) {
			check_admin_referer( 'isrc_opt_general_settings' );

			/* pre check for important keys and valid post types and taxonomie names */
			$options = isrc_check_posted_data( 'isrc_opt', $_POST['isrc_opt'] );

			if ( $options === false ) {
				return false;
			}

			/* if no $options['post_types'] present, set an empty array to prevent multiple checks for isset and is array in the next sections. */
			/* post type validations already made in isrc_check_posted_data */
			$post_types = ( isset( $options['post_types'] ) && is_array( $options['post_types'] ) ) ? $options['post_types'] : array();

			/* unset tab order based on selected post types */
			/* Maybe user unselected a post type but its still present in tab order */
			/* this section checks only for duplicates or not present keys.*/
			if ( isset( $options['front']['taborder'] ) && is_array( $options['front']['taborder'] ) ) {

				foreach ( $options['front']['taborder'] as $key => $val ) {

					if ( $val['type'] == 'post_type' ) {

						if ( ! in_array( $key, $post_types ) ) {
							unset( $options['front']['taborder'][ $key ] );
						}

					}
				}
			}

			/* But what if the user newly selected a post type? this is not present in tab order. Add it */
			foreach ( $post_types as $key => $val ) {
				if ( ! isset( $options['front']['taborder'][ $val ] ) ) {
					/* post type is selected but not present in taborder. add it */
					/* No need for valid post type name check. Its already validated */
					$pt_obj                               = get_post_type_object( $val );
					$options['front']['taborder'][ $val ] = array(
						'label' => $pt_obj->label,
						'type'  => 'post_type',
						'name'  => $val
					);
				}
			}

			/* Set an option for back usage */
			if ( isset( $options['front']['taborder'] ) && ! empty( $options['front']['taborder'] ) ) {
				foreach ( $options['front']['taborder'] as $key => $val ) {
					if ( $val['type'] == 'post_type' ) {
						$options['include_in_suggestions']['post_types'][] = $key;
					}
					/* exclude taxonomies if tabs are disabled */
					if ( $val['type'] == 'taxonomy' ) {
						$options['include_in_suggestions']['taxonomies'][] = $key;
					}
				}
			} elseif ( ! empty( $options['post_types'] ) ) {
				$options['include_in_suggestions']['post_types'] = $options['post_types'];
			}

			/* Fill thumbnail size defaults */
			if ( empty( $options['front']['thumb_size']['w'] ) ) {
				$options['front']['thumb_size']['w'] = 50;
			}

			if ( empty( $options['front']['thumb_size']['h'] ) ) {
				$options['front']['thumb_size']['h'] = 70;
			}

			update_option( 'isrc_opt_' . $this->lang, $options );

			/* resize no img */
			if ( isset( $options['front']['img'] ) && $options['front']['img'] && ! empty( $options['front']['no_img'] ) ) {
				$isrc_main               = new isrc_main();
				$w                       = $options['front']['thumb_size']['w'];
				$h                       = $options['front']['thumb_size']['h'];
				$img_id                  = (int) $options['front']['no_img'];
				$current_lang_thumb_name = "isrc_thumb_{$w}_{$h}";
				$isrc_main->resize_image_otf( $img_id, $current_lang_thumb_name, $w, $h );
				$isrc_main->resize_image_otf( $img_id, $current_lang_thumb_name . '_x2', $w * 2, $h * 2 );
			}

			isrc_build_attention_hash();

			$redirect_url = wp_unslash( $_POST['_wp_http_referer'] );

			/* add admin notice */
			$redirect_url = add_query_arg( array( 'saved_id' => '1' ), $redirect_url );

			wp_safe_redirect( $redirect_url );
			exit();
		}

		return true;
	}

	/**
	 * Save the options for advanced options menu tab.
	 * After saving the options a wp_safe_redirect is called.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function save_options_advanced() {

		if ( isset( $_POST['save'] ) ) {
			check_admin_referer( 'isrc_opt_adv' );

			$options = isrc_check_posted_data( 'isrc_opt_adv', $_POST['isrc_opt_adv'] );

			if ( $options === false ) {
				return false;
			}

			if ( isset( $options['isrc_bad_words'] ) ) {
				/* trim */
				$options['isrc_bad_words'] = trim( $options['isrc_bad_words'] );
			}

			if ( isset( $options['isrc_hide_words'] ) ) {
				/* trim */
				$options['isrc_hide_words'] = trim( $options['isrc_hide_words'] );
			}

			/* set delete data option global for all langs */
			if ( isset( $options['delete_data'] ) && $options['delete_data'] == '1' ) {
				update_option( 'isrc_delete_data', true );
			} else {
				update_option( 'isrc_delete_data', false );
			}

			update_option( 'isrc_opt_adv_' . $this->lang, $options );
			isrc_build_attention_hash();

			$redirect_url = wp_unslash( $_POST['_wp_http_referer'] );
			/* add admin notice */
			$redirect_url = html_entity_decode( add_query_arg( array( 'saved_id' => '2' ), $redirect_url ) );

			wp_safe_redirect( $redirect_url );
			exit();
		}

		return true;
	}

	/**
	 * Save the options for shortcode builder menu tab.
	 * After saving the options a wp_safe_redirect is called.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function save_options_shortcode_builder() {
		global $wpdb;

		if ( isset( $_POST['save'] ) ) {
			check_admin_referer( 'isrc_sc_opt' );

			if ( isset( $_POST['sc_id'] ) && ! empty( $_POST['sc_id'] ) ) {
				$_POST['sc_id'] = (int) $_POST['sc_id'];
			}

			$options = isrc_check_posted_data( 'isrc_sc_opt', $_POST['isrc_sc_opt'] );
			if ( $options === false ) {
				return false;
			}

			/* title */
			if ( isset( $options['title'] ) && ! empty( $options['title'] ) ) {
				$title = $options['title'];
			} else {
				$title = '';
			}

			$lang = isrc_get_lang_admin();

			if ( isset( $options['cb_flds'] ) && ! empty( $options['cb_flds'] ) && is_array( $options['cb_flds'] ) ) {
				foreach ( $options['cb_flds'] as $key => $val ) {
					foreach ( $val as $cbkey => $cbval ) {
						$options['cb_flds'][ $key ][ $cbkey ] = filter_var( $cbval, FILTER_VALIDATE_BOOLEAN );
					}
				}
			}

			/* update or new? */
			if ( isset( $_POST['sc_id'] ) && ! empty( $_POST['sc_id'] ) ) {

				/* Update */
				$sc_id = (int) $_POST['sc_id'];

				$wpdb->update(
					"{$wpdb->prefix}isearch_shortcodes",
					array(
						'title'    => $title,
						'lang'     => $lang,
						'settings' => maybe_serialize( $options )
					),
					array( 'id' => $sc_id ),
					array(
						'%s',
						'%s',
						'%s'
					),
					array( '%d' )
				);

			} else {

				/* insert */
				$wpdb->insert(
					"{$wpdb->prefix}isearch_shortcodes",
					array(
						'title'    => $title,
						'lang'     => $lang,
						'settings' => maybe_serialize( $options )
					),
					array(
						'%s',
						'%s',
						'%s'
					)
				);

				$sc_id = $wpdb->insert_id;
			}

			/* default_sc */
			if ( isset( $options['default_sc'] ) && ! empty( $options['default_sc'] ) && ! empty( $sc_id ) ) {
				update_option( 'isrc_default_sc_' . $this->lang, $sc_id );
			} else {
				/* check if this shortcode is default in options but now unchecked the default checkbox */
				$opt_default_sc = get_option( 'isrc_default_sc_' . isrc_get_lang_admin(), false );
				if ( $opt_default_sc !== false && $sc_id == $opt_default_sc ) {
					/* delete option because this was selected as default but now unchecked from admin */
					delete_option( 'isrc_default_sc' );
				}
			}

			$redirect_url = wp_unslash( $_POST['_wp_http_referer'] );
			/* add admin notice */
			$redirect_url = html_entity_decode( add_query_arg( array( 'saved_id' => '3', 'sc_id' => $sc_id ), $redirect_url ) );

			wp_safe_redirect( $redirect_url );
			exit();
		}

	}

	/**
	 * Save the options for content builder menu tab.
	 * After saving the options a wp_safe_redirect is called.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function save_options_content_builder() {
		if ( isset( $_POST['save'] ) ) {
			check_admin_referer( 'isrc_opt_cnt' );

			if ( isset( $_POST['isrc_opt']['builder_data'] ) ) {
			    /* sanitized next */
				parse_str( $_POST['isrc_opt']['builder_data'], $_POST['isrc_opt']['builder_data'] );
			}else{
			    return false;
            }

			$option = isrc_check_posted_data( 'isrc_cb_opt', $_POST['isrc_opt'] );
			if ( $option === false ) {
				return false;
			}

			$hash = array();

			if ( isset( $option['builder_data'] ) ) {
				/* build for hash */
				foreach ( $option['builder_data'] as $key => $val ) {
					foreach ( $val as $key2 => $val2 ) {
						foreach ( $val2 as $key3 => $val3 ) {
							$hash[] = $val3['data_key'] . '_' . $val3['data_type'];
						}

					}
				}
				asort( $hash );
			}
			$option['hash'] = $hash;
			update_option( 'isrc_opt_content_' . $this->lang, $option );
			$redirect_url = wp_unslash( $_POST['_wp_http_referer'] );
			/* add admin notice */
			$redirect_url = html_entity_decode( add_query_arg( array( 'saved_id' => '3' ), $redirect_url ) );
			isrc_build_attention_hash();
			if ( empty( $option['builder_data'] ) ) {
				$cb_hash = get_option( 'isrc_cb_att_hash_set_' . $this->lang );
				update_option( 'isrc_cb_att_hash_ind_' . $this->lang, $cb_hash );
			}
			wp_safe_redirect( $redirect_url );
			exit();
		}

		return true;
	}

	/**
	 * Add an admin notice if option is saved
	 *
	 * @param string $string
	 * @param string $class
	 */
	public function admin_notices( $string = '', $class = 'settings-updated' ) {

		if ( ! isset( $_GET['saved_id'] ) ) {
			return;
		}
		?>
        <div class="isearch-tab-settings-notices rmthis <?php echo sanitize_html_class($class); ?>">
            <div class="isearch-notice isearch-success-notice" data-notice="isearch_standard_notice">
                <div class="isearch-notice-icon isearch-success-notice-icon"></div>
                <div class="isearch-notice-text isearch-success-notice-text">
                    <p class="isearch-notice-message"><?php echo $string; ?></p>
                </div>
                <div class="isearch-notice-icon isearch-remove-notice-icon"></div>

            </div>
        </div>
		<?php
	}

	/**
	 * Register the i-Search submenu in Wordpress.
	 * The i-Search menu is a submenu of the global all4wp plugins menu which is registered before.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_sub_menu() {
		global $isrc_admin_page;

		$isrc_admin_page = add_submenu_page(
			ALL4WP_MENU_SLUG,
			__( 'i-Search Settings', 'i_search' ),
			__( 'i-Search', 'i_search' ),
			ISRC_CAPABILITIES,
			'isrc-opt-page',
			array( $this, 'submenu_page_callback' )
		);

		add_action( "load-$isrc_admin_page", array( $this, 'screen_option' ) );

	}

	/**
	 * Add screen options (logs per page) for the analysis options page TAB.
	 * Function screen_option is called in the $this->register_sub_menu function via add_action.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function screen_option() {
		global $logs_table;
		global $sc_table;

		$option = 'per_page';
		$args   = array(
			'label'   => 'Logs',
			'default' => 20,
			'option'  => 'logs_per_page'
		);

		add_screen_option( $option, $args );

		$logs_table = $this->logs_obj = new isrcLog_List();
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'scbuilder-list' ) {
			$sc_table = $this->sc_obj = new isrcShortcode_List();
		}
	}


	/**
	 * Screen options (logs per page) are returned here.
	 * WP need this action to handle screen options.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return string
	 */
	public function screen_option_save( $status, $option, $value ) {

		if ( 'logs_per_page' == $option ) {
			return $value;
		}

		return $status;
	}


	/**
	 * Render the options menu html.
	 * Html files are seperated to keep this file readable and clean.
	 * Html files will be included with include_once.
	 * Enqueue the scripts and styles here. ONLY the needed files.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return string    echo the included html file contents.
	 */
	public function submenu_page_callback() {

		wp_enqueue_style( 'isrc-admin' );

		$args    = wp_parse_args( $_GET, array( 'tab' => 'general' ) );
		$tab     = ( isset( $args['tab'] ) ) ? $args['tab'] : 'general';
		$sub_tab = ( isset( $args['sub-tab'] ) ) ? $args['sub-tab'] : false;

		/*
		* Enqueue the files for all menu tabs.
		*/
		wp_enqueue_style( 'font-awesome-5' );
		wp_enqueue_style( 'tooltipster-css' );
		wp_enqueue_style( 'tooltipster_light-css' );
		wp_enqueue_script( 'tooltipster' );
		wp_enqueue_style( 'alertify_isrc' );
		wp_enqueue_style( 'alertify_isrc_default' );
		wp_enqueue_script( 'alertify_isrc' );
		wp_enqueue_script( 'isearch-admin' );

		switch ( $tab ) {
			case 'general':
				$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-general.php';
				wp_enqueue_media();
				wp_enqueue_style( 'jquery-ui-css' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'remodal-css' );
				wp_enqueue_style( 'remodal-default-theme-css' );
				wp_enqueue_style( 'select2-css' );
				wp_enqueue_script( 'remodal' );
				wp_enqueue_script( 'select2' );
				wp_enqueue_script( 'sticky' );
				wp_enqueue_script( 'scrollto' );
				break;
			case 'advanced':
				wp_enqueue_style( 'multi-select' );
				$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-advanced.php';
				wp_enqueue_script( 'multi-select' );
				wp_enqueue_script( 'isrc-tagbox' );
				wp_enqueue_script( 'sticky' );
				wp_enqueue_script( 'scrollto' );
				wp_enqueue_style( 'select2-css' );
				wp_enqueue_script( 'select2' );
				break;
			case 'analyze':
				wp_enqueue_style( 'remodal-css' );
				wp_enqueue_style( 'remodal-default-theme-css' );
				wp_enqueue_style( 'select2-css' );
				wp_enqueue_script( 'remodal' );
				wp_enqueue_script( 'select2' );
				wp_enqueue_script( 'isrc-tagbox' );
				$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-analyze.php';
				break;
			case 'content-builder':
				wp_enqueue_style( 'jquery-ui-css' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'sticky' );
				wp_enqueue_style( 'select2-css' );
				wp_enqueue_script( 'select2' );
				$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/hmtl-menu-content-builder.php';
				break;
			case 'scbuilder-list':
				wp_enqueue_script( 'iframe-resizer' );
				wp_enqueue_style( 'common' );
				wp_enqueue_style( 'jquery-ui-css' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'sticky' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'scrollto' );
				wp_enqueue_script( 'color-picker-alpha' );
				wp_enqueue_style( 'codemirror' );
				wp_enqueue_script( 'codemirror' );
				wp_enqueue_script( 'codemirror_css' );

				if ( $sub_tab && ! empty( $sub_tab ) ) {
					$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-sbuilder-new.php';
				} else {
					$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-sbuilder-list.php';
				}
				break;
			default:
				$menu_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-general.php';
		}

		ob_start();
		require_once( $menu_theme );
		$content = ob_get_clean();
		echo $content;

		return true;
	}


	/**
	 * Register all the scripts and styles for admin. BUT NOT enqueue.
	 * We enqueue only files we need in specified options tabs.
	 * Enqueue needed files in the submenu_page_callback function.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_script_style() {

		wp_register_style( 'isrc-admin', ISRC_PLUGIN_URL . '/admin/menu/css/isrc-admin.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'multi-select', ISRC_PLUGIN_URL . '/admin/menu/css/multiselect/multi-select.dist.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'jquery-ui-css', ISRC_PLUGIN_URL . '/admin/menu/css/jquery-ui/jquery-ui.min.css', array(), '1.12.1' );
		wp_register_style( 'remodal-default-theme-css', ISRC_PLUGIN_URL . '/admin/menu/css/remodal/remodal-default-theme.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'remodal-css', ISRC_PLUGIN_URL . '/admin/menu/css/remodal/remodal.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'select2-css', ISRC_PLUGIN_URL . '/admin/menu/css/select2/select2.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'tooltipster-css', ISRC_PLUGIN_URL . '/admin/menu/css/tooltipster/tooltipster.bundle.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'tooltipster_light-css', ISRC_PLUGIN_URL . '/admin/menu/css/tooltipster/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-light.min.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'alertify_isrc', ISRC_PLUGIN_URL . '/admin/menu/css/alertify/alertify.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'alertify_isrc_default', ISRC_PLUGIN_URL . '/admin/menu/css/alertify/themes/default.css', array(), ISRC_SCRIPT_VER );
		wp_register_style( 'font-awesome-5', ISRC_PLUGIN_URL . '/admin/menu/css/font-awesome/css/all.min.css', array(), '5.3.1' );
		wp_register_style( 'codemirror', ISRC_PLUGIN_URL . '/admin/menu/css/codemirror/codemirror.css', array(), '5.40.1' );

		wp_register_script( 'select2', ISRC_PLUGIN_URL . '/admin/menu/js/select2/select2.full.min.js', array( 'jquery' ), '4.0.3', true );
		wp_register_script( 'multi-select', ISRC_PLUGIN_URL . '/admin/menu/js/multiselect/jquery.multi-select.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_localize_script( 'multi-select', 'm_keys_localize', array(
			'select_meta_head' => __( 'Selectable Keys', 'i_search' ),
			'select_meta_foot' => __( 'Selected Keys', 'i_search' )
		) );

		wp_register_script( 'isrc-tagbox', ISRC_PLUGIN_URL . '/admin/menu/js/tagbox/tagbox.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_localize_script( 'isrc-tagbox', 'isrc_tagsSuggestL10n', array(
			'tagDelimiter' => ',',
			'removeTerm'   => __( 'Remove Term', 'i_search' ),
			'termSelected' => __( 'Term Selected', 'i_search' ),
			'termAdded'    => __( 'Term Added', 'i_search' ),
			'termRemoved'  => __( 'Term Removed', 'i_search' )
		) );

		wp_register_script( 'remodal', ISRC_PLUGIN_URL . '/admin/menu/js/remodal/remodal.min.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'sticky', ISRC_PLUGIN_URL . '/admin/menu/js/sticky/jquery.sticky.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'tooltipster', ISRC_PLUGIN_URL . '/admin/menu/js/tooltipster/tooltipster.bundle.min.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'alertify_isrc', ISRC_PLUGIN_URL . '/admin/menu/js/alertify/alertify.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'isearch-admin', ISRC_PLUGIN_URL . '/admin/menu/js/isearch/isearch-admin.js', array( 'jquery' ), ISRC_SCRIPT_VER, true );
		wp_register_script( 'scrollto', ISRC_PLUGIN_URL . '/admin/menu/js/jquery.scrollTo/jquery.scrollTo.min.js', array( 'jquery' ), '2.1.2', true );
		wp_register_script( 'iframe-resizer', ISRC_PLUGIN_URL . '/admin/menu/js/iframeresizer/iframeResizer.min.js', array( 'jquery' ), '3.6.1', true );
		wp_register_script( 'color-picker-alpha', ISRC_PLUGIN_URL . '/admin/menu/js/color-picker-alpha/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '3.6.1', true );
		wp_register_script( 'codemirror', ISRC_PLUGIN_URL . '/admin/menu/js/codemirror/codemirror.min.js', array( 'jquery' ), '5.40.1', true );
		wp_register_script( 'codemirror_css', ISRC_PLUGIN_URL . '/admin/menu/js/codemirror/css.js', array( 'codemirror' ), '5.40.1', true );

	}

	/**
	 * Database AJAX callback actions in the advanced settings tab.
	 * Delete | Empty tables based on admins selection.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function menu_db_actions() {

		if ( ! i_src_security_check() ) {
			wp_send_json(
				array(
					'status' => 'error',
					'msg'    => __( 'You have no permissions.', 'i_search' )
				)
			);
		}

		check_ajax_referer( 'isrc_settings', 'security' );

		global $wpdb;

		$action = isset( $_POST['action_id'] ) ? $_POST['action_id'] : false;

		if ( ! $action ) {
			wp_send_json( array( 'status' => 'error' ) );
		}

		if ( $action == 'delete_all_logs' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$this->lang}'" );
			wp_send_json( array( 'status' => 'success' ) );
		}

		if ( $action == 'delete_popularity' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_popular WHERE lang = '{$this->lang}'" );
			wp_send_json( array( 'status' => 'success' ) );
		}

		if ( $action == 'delete_all_isearch' ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_popular WHERE lang = '{$this->lang}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_logs WHERE lang = '{$this->lang}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_metadata WHERE lang = '{$this->lang}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_taxonomy WHERE lang = '{$this->lang}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch_temp WHERE lang = '{$this->lang}'" );
			$wpdb->query( "DELETE FROM {$wpdb->prefix}isearch WHERE lang = '{$this->lang}'" );

			/* rebuild need index hash */
			update_option( 'isrc_att_hash_ind_' . $this->lang, rand() );
			wp_send_json( array( 'status' => 'success' ) );
		}

		wp_send_json( array( 'status' => 'error' ) );

	}

	/**
	 * AJAX action to save bad word tags in the analyse menu TAB.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function save_tags() {

		if ( ! i_src_security_check() ) {
			wp_send_json(
				array(
					'status' => 'error',
					'msg'    => __( 'You have no permissions.', 'i_search' )
				)
			);
		}

		check_ajax_referer( 'isrc_settings', 'security' );

		if ( empty( $_POST['tag_key'] ) && ! is_array( $_POST['tags'] ) ) {

			$error = array(
				'status' => 'error',
				'msg'    => 'ERROR: Empty tag key'
			);

			wp_send_json( $error );

		}

		$option_key   = $_POST['tag_key'] . '_' . $this->lang;
		$option_value = $_POST['tags'];

		if ( substr( $option_key, 0, 5 ) !== "isrc_" ) {
			exit( 'go home' );
		}

		update_option( $option_key, $option_value );

		$error = array(
			'status' => 'success',
			'msg'    => ''
		);

		wp_send_json( $error );

	}

	/**
	 * AJAX action handle the admin actions in the analysis menu TAB.
	 * Details: Actions are called from the tables Edit buttons popup screen.
	 * This function calls the real action in the admin_helpers.php file.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function ajax_fetch_remodal_callback() {

		if ( ! i_src_security_check() ) {
			wp_send_json(
				array(
					'status' => 'error',
					'msg'    => __( 'You have no permissions.', 'i_search' )
				)
			);
		}

		check_ajax_referer( 'isrc_table_nonce', '_isrc_table_nonce' );

		$response            = array();
		$response['success'] = false;

		$log_action = wp_unslash( $_POST['log_action'] );

		if ( $log_action == 'edit_meaning_action' ) {

			$selection = wp_unslash( $_POST['selection'] );
			$log_id    = (int) $_POST['log_id'];

			if ( ( 'block' == $selection || 'delete' == $selection ) && ! empty( $log_id ) ) {
				/* delete log by id */
				$response_func = isrc_delete_log( $log_id );
			}

			/* did you mean update */
			if ( 'dym' == $selection && ! empty( $log_id ) ) {
				$response_func = _isrc_update_dym_string();
			}

			/* add to post */

			if ( 'addtopost' == $selection && ! empty( $log_id ) ) {
				if ( isset( $_POST['selections'] ) && is_array( $_POST['selections'] ) ) {
					$selected_posts = $_POST['selections'];
					/* sanitation and security. Check if array keys are set properly */
					$allowed_types = array( 'taxonomy', 'post_type' );
					foreach ( $selected_posts as $key => $val ) {
						/* check ids */
						settype( $val['id'], 'integer' );
						if ( ! is_int( $val['id'] ) ) {
							return false;
						}

						/* check "type" */
						$val['type'] = sanitize_title( $val['type'] );
						if ( ! in_array( $val['type'], $allowed_types ) ) {
							return false;
						}

					}

					$response_func = isrc_add_string_to_post_terms( $log_id, $selected_posts );
				}

			}

			if ( isset( $response_func ) && is_array( $response_func ) ) {
				/* array only if a message is returned. */
				$response['success'] = $response_func['success'];
				$response['msg']     = $response_func['msg'];
			} elseif ( isset( $response_func ) ) {
				/* boolean only if no message will be returned */
				$response['success'] = $response_func;
			}
		}

		wp_send_json( $response );

	}

	/**
	 * AJAX action handle the help files.
	 * Help files are separated in the admin/help_files/admin_help_infos.php
	 * to keep this file clean and readable.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_help_html() {
		$help_file = ISRC_PLUGIN_DIR . '/admin/help_files/admin_help_infos.php';
		require_once( $help_file );

	}

	/**
	 * AJAX action handle the regenerate index screen.
	 * Regenerate screen is a stand alone html file included in this function but called as an ajax action to handle the security checks.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void.
	 */
	public function reindex_screen() {

		$hash = get_option( 'isrc_hash' );

		if ( ! isset( $_GET['hash'] ) || $_GET['hash'] != $hash ) {
			exit( 'Wrong hash key' );
		}

		$regenerate_theme = ISRC_PLUGIN_DIR . '/admin/menu/html-menu-reindex.php';
		require_once( $regenerate_theme );
		exit();
	}

	/**
	 * Get all meta keys from DB.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_the_meta_keys() {
		global $isrc_opt_adv;

		check_ajax_referer( 'isrc_settings', 'security' );

		$transient_name     = 'isrc_get_the_meta_keys';
		$transient_enabled  = 'no';
		$transient_duration = 60 * 5;
		if ( $transient_enabled == 'no' || false === ( $meta_keys = get_transient( $transient_name ) ) ) {

			$results        = array();
			$registered_pts = get_post_types( array( 'public' => true ) );
			$meta_keys      = array();
			foreach ( $registered_pts as $pt ) {
				$from_f  = isrc_get_meta_keys_by_posttype( $pt );
				$results = array_merge( $results, $from_f );
			}
			$results       = array_values( array_filter( array_unique( $results ) ) );
			$selected_keys = $isrc_opt_adv;

			if ( ! $selected_keys ) {
				$selected_keys = array( array( 'meta_inc' ) );
			} else {
				$selected_keys = ( isset( $selected_keys['meta_inc'] ) ) ? $selected_keys['meta_inc'] : array();
			}

			/* exclude some? */
			$excludes = array(
				'_isrc',
				'popularity',
				'_isrc_all'
			);

			$excludes_like = array(
				'_cb_ex_mk_',
			);

			foreach ( $results as $key => $val ) {

				if ( in_array( $val, $excludes ) ) {
					continue;
				}

				/* exclude contains */
				foreach ( $excludes_like as $exclude_meta ) {
					if ( strpos( $val, $exclude_meta ) !== false ) {
						continue 2;
					}
				}

				$keydata          = array();
				$keydata['name']  = $val;
				$keydata['label'] = $val;
				if ( strpos( $val, '__acf__' ) !== false ) {
					$temp             = explode( '__acf__', $val );
					$keydata['name']  = $temp[0];
					$keydata['label'] = 'ACF: ' . $temp[1];
				}

				if ( ! empty( $keydata['name'] ) && in_array( $keydata['name'], $selected_keys ) ) {
					$keydata['selected'] = true;
				} else {
					$keydata['selected'] = false;
				}

				$meta_keys[] = $keydata;

			}

			if ( $transient_enabled == 'yes' ) {
				set_transient( $transient_name, $meta_keys, $transient_duration );
			}

		}

		wp_send_json( $meta_keys );

	}


	/**
	 * Get all taxonomy names from DB.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_the_taxo_keys() {

		check_ajax_referer( 'isrc_settings', 'security' );

		$output        = 'names'; // or objects
		$operator      = 'and'; // 'and' or 'or'
		$meta_keys     = array();
		$results       = get_taxonomies( array(), $output, $operator );
		$selected_keys = get_option( 'isrc_opt_adv_' . isrc_get_lang_admin() );

		if ( ! $selected_keys ) {
			$selected_keys = array( array( 'taxonomy_includes' ) );
		} else {
			$selected_keys = ( isset( $selected_keys['taxonomy_includes'] ) ) ? $selected_keys['taxonomy_includes'] : array();
		}

		foreach ( $results as $key => $val ) {

			/* exclude post_tag, product_tag, category, product_cat because we have this option in the general settings page */
			if ( $val == 'post_tag' || $val == 'product_tag' || $val == 'product_cat' || $val == 'category' ) {
				continue;
			}

			$keydata          = array();
			$keydata['name']  = $val;
			$keydata['label'] = $val;

			if ( in_array( $keydata['name'], $selected_keys ) ) {
				$keydata['selected'] = true;
			} else {
				$keydata['selected'] = false;
			}

			$meta_keys[] = $keydata;

		}

		wp_send_json( $meta_keys );

	}

	/**
	 * Fetch the analyse table in admin menu as ajax.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_ajax_analyse_table() {
		$wp_list_table = new isrcLog_List();
		$wp_list_table->ajax_response();
	}


	/**
	 * Select2 callback for admin actions in analyses TAB.
	 * Returns only post types not taxonomies.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function select2_get_post_types() {

		check_ajax_referer( 'isrc_table_nonce', '_isrc_table_nonce' );

		global $wpdb, $isrc_opt;

		$lang       = isrc_get_lang_admin();
		$options    = get_option( 'isrc_opt_' . $lang );
		$post_types = $options['post_types'];

		if ( empty( $post_types ) ) {
			$results = array(
				'results'    => array(),
				'pagination' => array( 'more' => false )
			);
			wp_send_json( $results );
		}

		$numItems       = count( $post_types );
		$limit          = 10;
		$search_keyword = isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : '';
		$search_in      = isset( $_REQUEST['search_in'] ) ? $_REQUEST['search_in'] : 'all';
		$query_str      = '';
		$prepare_arr    = array();

		/* make union only if more then 1 post type is selected */
		if ( $numItems > 1 ) {
			$i = 0;

			foreach ( $post_types as $post_type ) {
				$query_str .= "( SELECT ID, post_title, post_type FROM {$wpdb->prefix}posts WHERE post_title != '' AND (post_status = 'publish' OR post_status = 'inherit') AND post_title LIKE %s AND post_type = %s LIMIT {$limit} )";
				if ( ++ $i !== $numItems ) {
					$query_str .= " UNION ";
				}
				$prepare_arr[] = '%' . $wpdb->esc_like( $search_keyword ) . '%';
				$prepare_arr[] = $post_type;
			}
		} else {
			/* we have only one PT no need for union */
			$query_str     = "SELECT ID, post_title, post_type FROM {$wpdb->prefix}posts WHERE post_title != '' AND (post_status = 'publish' OR post_status = 'inherit') AND post_title LIKE %s AND post_type = %s LIMIT {$limit}";
			$prepare_arr[] = '%' . $wpdb->esc_like( $search_keyword ) . '%';
			$prepare_arr[] = $post_types[0];
		}

		$db_query = $wpdb->prepare( $query_str, $prepare_arr );
		$results  = $wpdb->get_results( $db_query, ARRAY_A );

		/* is taxonomy selected in tab order? so we need to add taxonomies to the array */
		if ( isset( $isrc_opt['include_in_suggestions']['taxonomies'] ) ) {
			/* taxonomies enabled */
			/* get all taxonomies by search keyword and taxonomy */
			$taxonomies = $isrc_opt['include_in_suggestions']['taxonomies'];
			$where_tax  = array();
			foreach ( $taxonomies as $taxonomy ) {
				$where_tax[] = "term_taxonomy.taxonomy = '{$taxonomy}'";
			}

			$where_tax_str = implode( ' or ', $where_tax );

			if ( count( $where_tax ) > 1 ) {
				$where_tax_str = '( ' . $where_tax_str . ' )';
			}

			$query              = "SELECT terms.term_id as ID, terms.name as post_title, term_taxonomy.taxonomy FROM {$wpdb->prefix}terms terms JOIN {$wpdb->prefix}term_taxonomy term_taxonomy WHERE terms.term_id = term_taxonomy.term_taxonomy_id AND terms.slug LIKE %s AND {$where_tax_str} LIMIT {$limit}";
			$search_keyword_san = sanitize_title( $search_keyword );
			$query              = $wpdb->prepare( $query, '%' . $wpdb->esc_like( $search_keyword_san ) . '%' );
			$tax_results        = $wpdb->get_results( $query, ARRAY_A );
			$results            = array_merge( $results, $tax_results );
		}

		if ( empty( $results ) ) {
			$results = array(
				'results'    => array(),
				'pagination' => array( 'more' => false )
			);
			wp_send_json( $results );
		}

		$select2_results = array();
		foreach ( $results as $key => $val ) {
			if ( isset( $val['post_type'] ) ) {
				if ( $search_in != 'post_type' && $search_in != 'all' ) {
					continue;
				}
				/* check language here not in sql for future version updates do not touch the sql */
				$post_lang = isrc_get_lang( $val['ID'], 'post' );
				if ( $post_lang != $lang ) {
					continue;
				}
				$obj            = get_post_type_object( $val['post_type'] );
				$post_type_name = $obj->labels->singular_name;
			} elseif ( isset( $val['taxonomy'] ) ) {
				if ( $search_in != 'taxonomy' && $search_in != 'all' ) {
					continue;
				}

				$term = get_taxonomy( $val['taxonomy'] );
				/* check language here not in sql for future version updates do not touch the sql */
				$term_lang = isrc_get_lang( $val['ID'], 'taxonomy', $term->name );
				if ( $term_lang != $lang ) {
					continue;
				}
				$post_type_name = $term->label;
			}
			$select2_results[ $key ]['id']        = $val['ID'];
			$select2_results[ $key ]['type']      = ( isset( $val['post_type'] ) ) ? 'post_type' : 'taxonomy';
			$select2_results[ $key ]['post_type'] = ( isset( $val['post_type'] ) ) ? $val['post_type'] : $val['taxonomy'];
			$select2_results[ $key ]['text']      = $post_type_name . ': ' . $val['post_title'];
		}

		$results = array(
			'results'    => array_values( $select2_results ),
			'pagination' => array( 'more' => false )
		);

		wp_send_json( $results );

	}

	/**
	 * Select2 callback for admin actions.
	 *
	 * @since  3.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function get_taxonomy_by_name() {
		check_ajax_referer( 'isrc_default_nonce', 'nonce' );
		$taxonomy = sanitize_key( $_POST['taxonomy'] );
		$tax      = get_taxonomy( $taxonomy );
		if ( ! $tax ) {
			wp_die( 0 );
		}
		$s                     = wp_unslash( $_POST['search'] );
		$s                     = trim( $s );
		$term_search_min_chars = 1;
		if ( ( $term_search_min_chars == 0 ) || ( strlen( $s ) < $term_search_min_chars ) ) {
			wp_die();
		}

		$exclude = array();
		if ( isset( $_POST['exclude'] ) && ! empty( $_POST['exclude'] ) ) {
			$exclude = $_POST['exclude'];
		}
		$results    = get_terms( $taxonomy, array( 'name__like' => $s, 'exclude' => $exclude, 'fields' => 'id=>name', 'hide_empty' => false, 'number' => 10 ) );
		$return_arr = array();
		if ( ! empty( $results ) ) {
			foreach ( $results as $key => $val ) {
				$return_arr[] = array(
					'id'    => $key,
					'text'  => $val,
					'label' => $val,
				);
			}
		}

		$results = array(
			'results'    => $return_arr,
			'pagination' => array( 'more' => false )
		);

		wp_send_json( $results );
	}

	/**
	 * Ajax response for content builder preview data.
	 */
	public function sb_set_preview_data() {
		check_ajax_referer( 'isrc_settings', 'nonce' );
		$returndata = array( 'status' => 'error' );

		if ( isset( $_POST['preview_data'] ) && ! empty( $_POST['preview_data'] ) ) {
			parse_str( $_POST['preview_data'], $preview_data );
			update_option( 'isrc_previewdata', $preview_data );
			$returndata['status'] = 'success';
		}
		wp_send_json( $returndata );
	}

	/**
	 * Ajax response for content builder example data.
	 */
	public function build_content_data_example() {

		check_ajax_referer( 'isrc_settings', 'nonce' );

		if ( ! isset( $_POST['post_id'] ) || ! isset( $_POST['post_type'] ) || ! isset( $_POST['meta_data'] ) ) {
			die;
		}

		$post_id   = $_POST['post_id'];
		$meta_data = $_POST['meta_data'];
		$out       = array();

		foreach ( $meta_data as $type => $val ) {
			/* taxonomy */
			if ( $type == 'taxonomy' ) {
				foreach ( $val as $key => $taxo_name ) {
					$terms = get_the_terms( $post_id, $taxo_name );
					if ( ! is_wp_error( $terms ) && $terms !== false ) {
						$terms_arr = array();
						foreach ( $terms as $term ) {
							if ( ! empty( trim( $term->name ) ) ) {
								$terms_arr[] = trim( $term->name );
							}

						}
						if ( ! empty( $terms_arr ) ) {
							$terms                         = implode( ', ', $terms_arr );
							$out['taxonomy'][ $taxo_name ] = $terms;
						}
					}
				}
			}
			/* meta_key */
			if ( $type == 'meta_key' ) {
				foreach ( $val as $key => $meta_key ) {

					/* ACF */
					if ( strpos( $meta_key, 'field_' ) !== false && function_exists( 'get_field' ) ) {
						$meta_data = isrc_get_acf_value( $post_id, $meta_key );
					} elseif ( strpos( $meta_key, '_cb_ex_mk_' ) !== false ) {
						$meta_data = apply_filters( 'isearch_cb_format_extra_meta_data', '', $post_id, str_replace( '_cb_ex_mk_', '', $meta_key ) );
						if ( ! empty( trim( $meta_data ) ) ) {
							$meta_data = array( $meta_data );
						} else {
							$meta_data = false;
						}
					} else {
						$meta_data = get_post_meta( $post_id, $meta_key );
					}


					if ( $meta_data !== false && ! empty( $meta_data ) && is_array( $meta_data ) ) {
						$terms_arr = array();
						foreach ( $meta_data as $term ) {
							if ( is_array( $term ) ) {
								$term = implode( ', ', $term );
							}
							if ( ! empty( trim( $term ) ) ) {
								$terms_arr[] = trim( $term );
							}
						}
						if ( ! empty( $terms_arr ) ) {
							$terms                        = implode( ', ', $terms_arr );
							$out['meta_key'][ $meta_key ] = $terms;
						}

					}
				}

			}

		}

		$return = array( 'meta_data' => $out );
		wp_send_json( $return );
	}

	/**
	 * Add isrc special meta values to content builder
	 *
	 * @param array  $extra_meta_keys
	 * @param string $post_type
	 *
	 * @return array
	 */

	public function isrc_extra_content_metas( $extra_meta_keys = array(), $post_type = 'post' ) {
		$extra_meta_keys[] = 'post_modified_date';
		$extra_meta_keys[] = 'post_created_date';

		return $extra_meta_keys;
	}

	/**
	 * Select2 callback for admin actions in analyses TAB.
	 * Returns only taxonomies not post types.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function select2_get_taxonomies() {

		check_ajax_referer( 'isrc_opt_general_settings', '_isrc_table_nonce' );

		$search_keyword = ( isset( $_REQUEST['search'] ) ) ? sanitize_title( $_REQUEST['search'] ) : '';

		$output   = 'objects'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$results  = get_taxonomies( array(), $output, $operator );

		if ( empty( $results ) ) {
			die;
		}

		$select2_results = array();

		/* exclude some? */
		$exclude = array( 'post_tag', 'product_tag', 'product_cat', 'category' );
		$exclude = array();
		if ( isset( $_REQUEST['exclude'] ) && ! empty( $_REQUEST['exclude'] ) && is_array( $_REQUEST['exclude'] ) ) {
			$exclude = array_merge( $exclude, $_REQUEST['exclude'] );
		}

		foreach ( $results as $key => $val ) {
			$label = $val->label;

			/* exclude post_tag, product_tag, category, product_cat because we have this option in the general settings page */
			if ( in_array( $key, $exclude ) ) {
				continue;
			}

			/* search */
			if ( ! empty( $search_keyword ) ) {
				if ( stripos( sanitize_title( $label ), $search_keyword ) !== false ) {
					$select2_results[] = array(
						'id'    => $key,
						'text'  => $label . ' (' . $key . ')',
						'label' => $label,
					);
				}
			} else {
				$select2_results[] = array(
					'id'    => $key,
					'text'  => $label . ' (' . $key . ')',
					'label' => $label,
				);
			}
		}

		$results = array(
			'results'    => $select2_results,
			'pagination' => array( 'more' => false )
		);

		wp_send_json( $results );

	}


}

$isrc_menu = new isrc_menu();