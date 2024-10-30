<?php
/*
 * i-Search Ajax search form template
 *
 * @author  all4wp.net
 * @package i-Search
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
global $isrc_opt;
$lang      = isrc_get_lang_front();
$is_mobile = false;
/* is user a mobile user? */
if ( class_exists( 'isrc_Mobile_Detect' ) ) {

	$mobile_detect = new isrc_Mobile_Detect;
	/* Any mobile device (phones or tablets). */
	if ( $mobile_detect->isMobile() ) {
		$is_mobile = true;
	}
}

/* get data from db */
if ( isset( $atts['shortcode_id'] ) && ! empty( $atts['shortcode_id'] ) ) {
	$shortcode_id = $atts['shortcode_id'];
	$atts         = isrc_get_shortcode_instance( $atts['shortcode_id'] );
	if ( empty( $atts ) ) {
		return '';
	}
	$atts['shortcode_id'] = (int) $shortcode_id;

} else if ( isset( $atts['is_preview'] ) && $atts['is_preview'] ) {
	$shortcode_id = 0;
} else {
	$shortcode_id = get_option( 'isrc_default_sc_' . $lang, false );
	$shortcode_id = (int) $shortcode_id;
	$atts         = isrc_get_shortcode_instance( $shortcode_id );
	if ( empty( $atts ) ) {
		return '';
	}
	$atts['shortcode_id'] = (int) $shortcode_id;
}

/* custom css */
$css = null;
if ( isset( $atts['css'] ) ) {
	$css = $atts['css'];
}
/* colors*/
if ( isset( $atts['colors'] ) ) {
	$colors = json_decode( base64_decode( $atts['colors'] ), true );
	unset( $atts['colors'] );
} else {
	$colors = array();
}

/* opening type*/
if ( $atts['input_disp_style'] == 'normal' ) {
	unset( $atts['input_open_style'] );
	unset( $atts['inp_opening_label'] );
}

/* themes default search form? */
if ( $atts['input_disp_style'] == 'theme_sw_full' ) {

	/* we need the templates default search form. Remove filter from isearch */
	global $isrc_front_main;

	remove_filter( 'get_search_form', array( $isrc_front_main, 'replace_default_form' ), 99 );
	remove_filter( 'get_product_search_form', array( $isrc_front_main, 'replace_default_form' ), 99 );
	$theme_search_form = get_search_form( false );

	/* add filter again */
	add_filter( 'get_search_form', array( $isrc_front_main, 'replace_default_form' ), 99, 1 );
	add_filter( 'get_product_search_form', array( $isrc_front_main, 'replace_default_form' ), 99, 1 );

}

if ( $atts['input_disp_style'] == 'theme_sw_full' ) {
	$atts['input_open_style']  = 'body';
	$atts['inp_opening_label'] = 'theme_sw_full';
	$atts['input_disp_style']  = 'text';
}

$atts = shortcode_atts(
	array(
		'hide_on_mobile'    => false,
		'hide_on_desk'      => false,
		'atc_btn'           => false,
		'ph_advert'         => false,
		'kw_handle'         => 'split',
		'subm_btn_style'    => 'style_1',
		'color_style'       => 'light',
		'ph_adverts'        => false,
		'shortcode_id'      => 0,
		'atc_label'         => ( isset( $isrc_opt['front']['atc_label'] ) ) ? $isrc_opt['front']['atc_label'] : 'Add to cart',
		'close_icon'        => ISRC_PLUGIN_URL . '/front/css/img/xclose.svg',
		'is_preview'        => false,
		'loader_icon'       => 'search_round.gif',
		'logging'           => false,
		'max_height'        => 'calculate',
		'mh_custom'         => 500,
		'limit'             => 15,
		'min_chars'         => 3,
		'order_by'          => 'post_id',
		'inp_left_label'    => 'SEARCH',
		'input_style'       => 'style_1',
		'orientation'       => 'auto',
		'offset_top'        => 0,
		'placeholder'       => 'Search...',
		'locale'            => $lang,
		'show_popularity'   => false,
		'ptdiv_enabled'     => false,
		'search_in'         => false,
		'search_in_images'  => null,
		'cb_flds'           => false,
		'exc_multi_line'    => null,
		'exc_max_words'     => false,
		'title_only'        => false,
		'subm_label'        => 'Submit',
		'submit_btn'        => false,
		'tabs_enabled'      => false,
		'theme'             => 'clean',
		'ed_noresult'       => false,
		'noresult_label'    => 'No Results',
		'log_popularity'    => false,
		'popular_label'     => 'No Results. Popular Searches',
		'ed_didumean'       => false,
		'didumean_label'    => 'Did you mean',
		'ed_viewall'        => false,
		'ed_continue'       => false,
		'viewall_label'     => 'Show more',
		'inp_opening_label' => 'SEARCH',
		'subm_color'        => '#ea2330',
		'subm_icon_color'   => '#fff',
		'input_open_style'  => 'right',
		'popular_max'       => '5',
		'input_disp_style'  => 'normal',
		'sug_w'             => 'auto',
		'inp_sc_p'          => 'inherit',
		'fx_px_t'           => '50',
		'fx_px_r'           => '10',
		'fx_px_b'           => '',
		'fx_px_l'           => '',
		'show_trendings'    => false,
		'trendings_label'   => 'Trending searches',
		'trendings_max'     => '3',
		'trendings_pos'     => 'below',


	), $atts );

/* user save boolean check */
$atts['hide_on_mobile']  = filter_var( $atts['hide_on_mobile'], FILTER_VALIDATE_BOOLEAN );
$atts['hide_on_desk']    = filter_var( $atts['hide_on_desk'], FILTER_VALIDATE_BOOLEAN );
$atts['tabs_enabled']    = filter_var( $atts['tabs_enabled'], FILTER_VALIDATE_BOOLEAN );
$atts['atc_btn']         = filter_var( $atts['atc_btn'], FILTER_VALIDATE_BOOLEAN );
$atts['submit_btn']      = filter_var( $atts['submit_btn'], FILTER_VALIDATE_BOOLEAN );
$atts['logging']         = filter_var( $atts['logging'], FILTER_VALIDATE_BOOLEAN );
$atts['show_popularity'] = filter_var( $atts['show_popularity'], FILTER_VALIDATE_BOOLEAN );
$atts['is_preview']      = filter_var( $atts['is_preview'], FILTER_VALIDATE_BOOLEAN );
$atts['ptdiv_enabled']   = filter_var( $atts['ptdiv_enabled'], FILTER_VALIDATE_BOOLEAN );
$atts['ed_noresult']     = filter_var( $atts['ed_noresult'], FILTER_VALIDATE_BOOLEAN );
$atts['log_popularity']  = filter_var( $atts['log_popularity'], FILTER_VALIDATE_BOOLEAN );
$atts['ed_didumean']     = filter_var( $atts['ed_didumean'], FILTER_VALIDATE_BOOLEAN );
$atts['ed_viewall']      = filter_var( $atts['ed_viewall'], FILTER_VALIDATE_BOOLEAN );
$atts['ed_continue']     = filter_var( $atts['ed_continue'], FILTER_VALIDATE_BOOLEAN );
$atts['ph_advert']       = filter_var( $atts['ph_advert'], FILTER_VALIDATE_BOOLEAN );
$atts['show_trendings']  = filter_var( $atts['show_trendings'], FILTER_VALIDATE_BOOLEAN );

/* mobile hide */
if ( $atts['hide_on_mobile'] && $is_mobile && ! $atts['is_preview'] ) {
	return '';
}

/* desktop hide */
if ( $atts['hide_on_desk'] && ! $is_mobile && ! $atts['is_preview'] ) {
	return '';
}

unset( $atts['hide_on_mobile'] );
unset( $atts['hide_on_desk'] );


$atts['popular_max'] = (int) $atts['popular_max'];
if ( empty( $atts['popular_max'] ) ) {
	$atts['popular_max'] = 5;
}

/* content builder fields */
if ( $atts['cb_flds'] ) {
	$atts['cb_flds'] = json_decode( base64_decode( $atts['cb_flds'] ), true );
}

if ( isset( $atts['cb_flds'] ) && ! empty( $atts['cb_flds'] ) && is_array( $atts['cb_flds'] ) ) {
	foreach ( $atts['cb_flds'] as $key => $val ) {
		foreach ( $val as $cbkey => $cbval ) {
			$atts['cb_flds'][ $key ][ $cbkey ] = filter_var( $cbval, FILTER_VALIDATE_BOOLEAN );
		}
	}
}

/* placeholder advertising */
if ( $atts['ph_adverts'] ) {
	$atts['ph_adverts'] = json_decode( base64_decode( $atts['ph_adverts'] ), true );
}

if ( $atts['exc_max_words'] ) {
	$atts['exc_max_words'] = json_decode( base64_decode( $atts['exc_max_words'] ), true );
}

/* set max_height value if custom is selected */
if ( $atts['max_height'] == 'custom' ) {
	$atts['max_height'] = $atts['mh_custom'];
}

/* add trendings data to array and unset from atts. We don't need it as JS var. */
$trendings_data = array(
	'show_trendings'  => $atts['show_trendings'],
	'trendings_pos'   => $atts['trendings_pos'],
	'trendings_label' => $atts['trendings_label'],
	'trendings_max'   => (int) $atts['trendings_max'],
	'tags'            => array(),
);

unset( $atts['show_trendings'] );
unset( $atts['trendings_label'] );
unset( $atts['trendings_max'] );
unset( $atts['trendings_pos'] );

/* get trending data */
if ( $trendings_data['show_trendings'] ) {
	$trending_tags_arr = isrc_get_trending_tags( $trendings_data['trendings_max'], $atts['search_in'], $lang );
	if ( empty( $trending_tags_arr ) ) {
		$trendings_data['show_trendings'] = false;
	} else {
		$t_tags_arr = array();
		foreach ( $trending_tags_arr as $trending_tag ) {
			if ( $trendings_data['trendings_pos'] == 'in' ) {
				$t_tags_arr[] = $trending_tag['title'];
			} else {
				$t_tags_arr[] = '<span class="ttag">' . $trending_tag['title'] . '</span>';
			}
		}

		$trendings_data['tags'] = implode( ', ', $t_tags_arr );

		if ( $trendings_data['trendings_pos'] == 'in' ) {
			$atts['ph_adverts'] = array( $trendings_data['trendings_label'] . ' ' . $trendings_data['tags'] );
			$atts['ph_advert']  = true;
		}

	}

}

/* loader icon */
$atts['loader_icon'] = ISRC_PLUGIN_URL . '/front/css/img/' . $atts['loader_icon'];

/* if set search_in, we need a reorder variable for JS. */
/* set default if we have an error */
if ( ! isset( $isrc_opt['front']['taborder'] ) ) {
	return false;
}
$shortcode_order = $isrc_opt['front']['taborder'];

if ( ! empty( $atts['search_in'] ) ) {
	/* format from shortcode string to array */
	$search_in = explode( ',', $atts['search_in'] );

	if ( isset( $atts['search_in_images'] ) ) {
		$search_in_images = array_flip( explode( ',', $atts['search_in_images'] ) );
	} else {
		$search_in_images = array();
	}

	if ( isset( $atts['title_only'] ) ) {
		$title_only = array_flip( explode( ',', $atts['title_only'] ) );
	} else {
		$title_only = array();
	}

	if ( isset( $atts['exc_multi_line'] ) && ! empty( $atts['exc_multi_line'] ) ) {
		$exc_multi_line = array_flip( explode( ',', $atts['exc_multi_line'] ) );
	} else {
		$exc_multi_line = array();
	}

	if ( isset( $atts['exc_max_words'] ) ) {
		$exc_max_words = $atts['exc_max_words'];
		unset( $atts['exc_max_words'] );
	} else {
		$exc_max_words = array();
	}

	$default_order  = $isrc_opt['front']['taborder'];
	$new_order      = array();
	$search_in_temp = array();

	foreach ( $search_in as $raw ) {
		$post_or_tax_type_key = substr( $raw, 3 );
		/* build new order */
		if ( isset( $default_order[ $post_or_tax_type_key ]['label'] ) ) {
			$search_in_temp[]           = $raw;
			$new_order[ $raw ]['label'] = $default_order[ $post_or_tax_type_key ]['label'];

			if ( isset( $search_in_images[ $raw ] ) ) {
				$new_order[ $raw ]['have_img'] = true;
			} else {
				$new_order[ $raw ]['have_img'] = false;
			}

			if ( isset( $title_only[ $raw ] ) ) {
				$new_order[ $raw ]['title_only'] = true;
			} else {
				$new_order[ $raw ]['title_only'] = false;
			}

			if ( isset( $exc_multi_line[ $raw ] ) ) {
				$new_order[ $raw ]['exc_multi_line'] = true;
			} else {
				$new_order[ $raw ]['exc_multi_line'] = false;
			}

			if ( isset( $exc_max_words[ $raw ] ) ) {
				$new_order[ $raw ]['exc_max_words'] = (int) $exc_max_words[ $raw ];
			} else {
				$new_order[ $raw ]['exc_max_words'] = 75;
			}
		}

	}

	$atts['search_in'] = implode( ',', $search_in_temp );

	if ( ! empty( $new_order ) ) {
		$shortcode_order = $new_order;
	}
}

$container_classes   = array();
$container_classes[] = ( $atts['submit_btn'] ) ? 'submit_enabled' : 'submit_disabled';
$container_classes[] = ( $atts['is_preview'] ) ? 'preview-mode' : '';
$container_classes[] = 'isrc-color-' . $atts['color_style'];
$container_classes[] = 's-btn-' . $atts['subm_btn_style'];
$container_classes[] = 'inp-st-' . $atts['input_style'];
if ( isset( $atts['input_open_style'] ) ) {
	$container_classes[] = 'inp-opn-st-' . $atts['input_open_style'];
}
$container_classes[] = 'inp-disp-st-' . $atts['input_disp_style'];
$container_classes[] = is_rtl() ? 'isrc-is-rtl' : 'isrc-is-ltr';


$theme                = $atts['theme'];
$atts['custom_order'] = $shortcode_order;
$inline_css           = '';
/* custom colors */
if ( ! empty( $colors ) ) {
	$color_css  = isrc_colorbuilder_to_css( $colors );
	$color_css  = isrc_assign_custom_css( $color_css, $shortcode_id );
	$inline_css .= $color_css;
}

/* custom css */
if ( ! empty( $css ) ) {
	$css        = isrc_assign_custom_css( $css, $shortcode_id );
	$inline_css .= $css;
}

/* combine both */
if ( ! empty( $inline_css ) ) {
	echo "<style>{$inline_css}</style>";
}


$random_int = rand();
$random_id  = $random_int . '_' . $shortcode_id;
$fixcss_str = '';

if ( $atts['inp_sc_p'] == 'inh' ) {
	unset( $atts['fx_px_t'] );
	unset( $atts['fx_px_r'] );
	unset( $atts['fx_px_b'] );
	unset( $atts['fx_px_l'] );
}

if ( isset( $atts['inp_sc_p'] ) && $atts['inp_sc_p'] == 'fix' ) {
	$fixcss_arr           = array();
	$fixcss_arr['top']    = $atts['fx_px_t'];
	$fixcss_arr['right']  = $atts['fx_px_r'];
	$fixcss_arr['bottom'] = $atts['fx_px_b'];
	$fixcss_arr['left']   = $atts['fx_px_l'];


	foreach ( $fixcss_arr as $key => $val ) {
		if ( $val === '' ) {
			continue;
		}
		if ( substr( $val, - 1 ) !== '%' ) {
			$val = preg_replace( "/[^0-9]/", "", $val );
			$val = $val . 'px';
		} else {
			$val = preg_replace( "/[^0-9]/", "", $val );
			$val = $val . '%';
		}

		$fixcss_str .= $key . ':' . $val . ';';
	}
	$fixcss_str .= 'position:fixed;z-index:999999999;';
}
?>
<script>
    var isrcData_<?php echo $random_id;?> = <?php echo json_encode( $atts ); ?>;
</script>
<div class="isrc_sc_<?php echo ( isset( $shortcode_id ) ) ? $shortcode_id : '0'; ?> isrc-min-w0 isrc-closest isrc-color-<?php echo $atts['color_style']; ?> isrc-screen-<?php echo $atts['inp_sc_p']; ?>"
     style="<?php echo  $fixcss_str; ?>">
	<?php
	if ( $atts['input_disp_style'] != 'normal' && ( $atts['input_open_style'] == 'right' || $atts['input_open_style'] == 'left' ) ) { ?>
    <div class="isrc-slide-wrap isrc-slide-dir-<?php echo $atts['input_open_style']; ?>">
		<?php } ?>

		<?php
		if ( $atts['input_disp_style'] == 'text' && ! isset( $theme_search_form ) ) {
			?>
            <div class="isrc-cl-op"><?php echo $atts['inp_opening_label']; ?></div>
		<?php } elseif ( $atts['input_disp_style'] == 'text' && isset( $theme_search_form ) ) {
			?>
            <span class="isrc_theme_sw_full"><?php echo $theme_search_form; ?></span>
			<?php
		}
		if ( $atts['input_disp_style'] == 'icon' ) { ?>
            <div class="isrc-cl-op"><i class="isrc-icon isr-ic-search"></i></div>
		<?php }
		if ( $atts['input_disp_style'] == 'text_icon' ) { ?>
            <div class="isrc-cl-op isrc-icon-text"><?php echo $atts['inp_opening_label']; ?> <i class="isrc-icon isr-ic-search"></i></div>
		<?php } ?>

		<?php
		if ( $atts['input_disp_style'] != 'normal' && $atts['input_open_style'] == 'body' ) { ?>
        <div class="isrc-boxH" style="display: none;">
            <div class="isrc-boxV">
                <div class="isrc-boxM">
					<?php }
					if ( $atts['input_disp_style'] != 'normal' && ( $atts['input_open_style'] == 'right' || $atts['input_open_style'] == 'left' ) )  { ?>
                    <div class="isrc-slide slide-out isrc-to-<?php echo $atts['input_open_style']; ?>">
						<?php } ?>
						<?php if ( $trendings_data['show_trendings'] && $trendings_data['trendings_pos'] == 'above' ) { ?>
                            <div class="isrc-trendgings-wrap">
                            <span class="i-src-trendings-label">
                                <?php echo $trendings_data['trendings_label']; ?>
                            </span>
								<?php echo $trendings_data['tags']; ?>
                            </div>
						<?php } ?>
                        <div class="isrc-ajaxsearchform-container <?php echo implode( ' ', $container_classes ); ?>">
                            <form role="i-search" method="get" class="isrc-ajaxsearchform" action="<?php echo esc_url( home_url( '/' ) ) ?>">
                                <div class="isrc-input-wrapper isrc-mh-val">
									<?php
									if ( $atts['input_style'] == 'style_1' ) { ?>
                                        <div class="inp_style_1"><?php echo $atts['inp_left_label'] ?></div>
									<?php } ?>

                                    <input type="text"
                                           data-key='isrcData_<?php echo $random_id; ?>'
                                           autocomplete="off"
                                           value=""
                                           name="s"
                                           class="isrc-s isrc-min-w0 isrc-mh-val">
                                    <span class="isrc_delete_btn isrc_preloader isrc-h-val xclose"></span>
									<?php
									if ( $atts['input_style'] == 'style_3' ) { ?>
                                        <div class="inp-underl"></div>
									<?php } ?>

                                    <button type="submit" class="isrc-searchsubmit isrc-submit-bg isrc-h-val" style="background-color:<?php echo $atts['subm_color']; ?>">
                                        <span class="isrc-sbm-svg" style="background-color:<?php echo $atts['subm_icon_color']; ?>"></span>
                                        <span class="isrc-sbm-label">
                                <?php echo $atts['subm_label'] ?>
                                </span>
                                    </button>
									<?php
									if ( $atts['input_disp_style'] != 'normal' && ( $atts['input_open_style'] == 'body' ) ) { ?>
                                        <span class="isrc-win-close isrc-h-val"></span>
									<?php } ?>
                                    <input type="hidden" class="isrc_form_post_type" name="post_type" value=""/>
                                </div>
                            </form>
                        </div>
						<?php if ( $trendings_data['show_trendings'] && $trendings_data['trendings_pos'] == 'below' ) { ?>
                            <div class="isrc-trendgings-wrap">
                            <span class="i-src-trendings-label">
                                <?php echo $trendings_data['trendings_label']; ?>
                            </span>
								<?php echo $trendings_data['tags']; ?>
                            </div>
						<?php } ?>
						<?php
						if ( $atts['input_disp_style'] != 'normal' && $atts['input_open_style'] == 'body' ) { ?>
                    </div>
                </div>
            </div>
			<?php } ?>
			<?php
			if ( $atts['input_disp_style'] != 'normal' && ( $atts['input_open_style'] == 'right' || $atts['input_open_style'] == 'left' ) ) { ?>
        </div>
    </div>
<?php } ?>

</div>
<?php
/* Add notice in front if jquery is not available. This notice will be only visible for admin. This field will not be rendered for non admins. */
/* with a one line javascript snipped to check if jquery is available. */
if ( current_user_can( ISRC_CAPABILITIES ) ) {
	$rand = rand( 5, 100 );
	echo "<!--- You see this admin notice in the source only because you are logged in is admin or you have the capabilities to edit options. --->";
	echo '<div id="js_' . $rand . '" class="isrc_jquery_notice" style="display:none;">' . __( 'jQuery is not available. i-Search need jQuery to work properly. Please activate jQuery in i-Search settings (Advanced Settings Tab). This notice is only for administrators visible', 'isrc-woocommerce-ajax-search' ) . '</div>';
	echo "<script>(typeof jQuery === 'undefined' ) ? document.getElementById('js_" . $rand . "').style.display = 'block':''</script>";
}
?>

<?php
/* wp-bakery live editor */
if ( isset( $_GET['vc_editable'] ) ) {
	?>
    <script>
        jQuery(function () {
            if (typeof iSearchActivate !== 'undefined') {
                iSearchActivate();
            }
        });
    </script>
	<?php
}
?>
