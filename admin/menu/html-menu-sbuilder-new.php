<?php

/**
 * i-Search html menu for the Shortcode builder list
 *
 * This file will be loaded with include_once in the class-menu.php file.
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
$isrc_nonce = wp_create_nonce( 'isrc_settings' );
/* is this an update or a new shortcode */
if ( isset( $_GET['sc_id'] ) ) {
	$sc_id    = (int) $_GET['sc_id'];
	$isupdate = true;
} else {
	$sc_id    = 0;
	$isupdate = false;
}

/*
 * Transform default options to shortcode needed format
 */
$defaults = get_option( 'isrc_opt_' . isrc_get_lang_admin() );
$taborder = ( isset( $defaults['front']['taborder'] ) ) ? $defaults['front']['taborder'] : array();

if ( $isupdate ) {
	/* get data from db if is a update */
	$settings_for_check = get_isearch_shortcode_data( $sc_id );
	/* set taborder */
	if ( isset( $settings_for_check['search_in'] ) ) {
		$new_taborder = array();
		foreach ( $settings_for_check['search_in'] as $key => $val ) {
			$pt  = substr( $key, 0, 3 );
			$ptn = substr( $key, 3 );

			if ( ! isset( $defaults['front']['taborder'][ $ptn ] ) ) {
				continue;
			}
			$new_taborder[ $ptn ] = $defaults['front']['taborder'][ $ptn ];
		}
		$difference = array_diff_key( $taborder, $new_taborder );
		if ( ! empty( $difference ) ) {
			$new_taborder = array_merge( $new_taborder, $difference );
		}

		$taborder = $new_taborder;
	}
} else {
	/* get data from defaults if its new */
	$settings_for_check                     = array();
	$settings_for_check['theme']            = 'clean';
	$settings_for_check['order_by']         = 'post_id';
	$settings_for_check['placeholder']      = 'Type here to search';
	$settings_for_check['submit_btn']       = null;
	$settings_for_check['subm_label']       = 'Submit';
	$settings_for_check['tabs_enabled']     = true;
	$settings_for_check['max_height']       = 'calculate';
	$settings_for_check['mh_custom']        = 500;
	$settings_for_check['search_in']        = array();
	$settings_for_check['min_chars']        = 3;
	$settings_for_check['limit']            = 15;
	$settings_for_check['logging']          = true;
	$settings_for_check['sug_w']            = 'auto';
	$settings_for_check['show_popularity']  = true;
	$settings_for_check['noresult_label']   = 'No Result';
	$settings_for_check['popular_label']    = 'No Results. Popular Searches';
	$settings_for_check['didumean_label']   = 'Did you mean';
	$settings_for_check['viewall_label']    = 'More results';
	$settings_for_check['ed_viewall']       = true;
	$settings_for_check['log_popularity']   = true;
	$settings_for_check['ed_didumean']      = true;
	$settings_for_check['ed_noresult']      = true;
	$settings_for_check['loader_icon']      = 'style_1';
	$settings_for_check['input_style']      = 'style_1';
	$settings_for_check['subm_btn_style']   = 'style_1';
	$settings_for_check['orientation']      = 'auto';
	$settings_for_check['ed_continue']      = null;
	$settings_for_check['color_style']      = 'light';
	$settings_for_check['input_disp_style'] = 'normal';
	$settings_for_check['input_cl_style']   = 'icon';
	$settings_for_check['input_open_style'] = 'left';
	$settings_for_check['inp_sc_p']         = 'inh';
	$settings_for_check['trendings_pos']    = 'below';
	$settings_for_check['colors']['ts_al']  = 'l';
}

$preview_src_raw = untrailingslashit( admin_url( 'admin-ajax.php' ) );
$hash            = get_option( 'isrc_hash' );
$preview_src     = html_entity_decode(
	add_query_arg(
		array(
			'hash'         => $hash,
			'nonce'        => $isrc_nonce,
			'action'       => 'isrc_instance_preview',
			'isrc_preview' => '1'
		), $preview_src_raw )
);

include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';
$video_tutorial_title = __( 'Instance Builder', 'i_search' );
?>

<div class="isrc-opt-page isrc-opt-page-add-new-sc <?php echo ( is_rtl() ) ? 'rtl' : ''; ?>">
	<?php
	$string = esc_html__( 'Instance Updated.', 'i_search' );
	$class  = 'settings-updated';

	if ( isset( $_GET['saved_id'] ) ) {
		if ( $_GET['saved_id'] == 4 ) {
			$string = esc_html__( 'Instance NOT Updated.', 'i_search' );
			$class  = 'settings-not-updated';
		}
	}
	do_action( 'isrc_admin_notice', $string, $class );
	?>

    <form method="POST" id="post" class="sc-builder-form">
        <input type="hidden" name="isrc_opt_page" value="shortcode_builder">
        <input type="hidden" name="sc_id" value="<?php echo $sc_id; ?>">
        <input type="hidden" name="isrc_sc_opt[locale]" value="<?php echo isrc_get_lang_admin(); ?>">

        <div id="poststuff" class="shortcode-list">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div class="isrc-container cnt-general">

                        <div class="addnew-wrap">
                            <h1 class="wp-heading-inline">
								<?php _e( 'Build New Search Instance', 'i_search' ); ?>
                            </h1>
                            <div class="isrc-input">
								<?php
								isrc_render_text_field(
									'isrc_sc_opt[title]',
									'isrc_sc_title',
									( isset( $settings_for_check['title'] ) ) ? $settings_for_check['title'] : '',
									'',
									'',
									'sc_title',
									true,
									'text',
									'',
									'',
									'',
									__( 'Enter title for this instance', 'i_search' )
								);
								?>
                            </div>
                            <div class="sc-body">
                                <div class="sc_left_sidebar">
                                    <h3><?php _e( 'Settings', 'i_search' ); ?></h3>

                                    <div class="sttngs-cont cnt-white-shadow">
                                        <div class="isrc-inside">
                                            <div class="isrc-field isrc-settings" id="content_1_5">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Search input screen position', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_48' ); ?>
                                                        <p class="description">
															<?php _e( 'Inherit: Normal on position.', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[inp_sc_p]',
														'input_screen_position',
														'isrc-hide-if-disabled-select prv-upd',
														'',
														'',
														'',
														array(
															'inh' => __( 'Inherit', 'i_search' ),
															'fix' => __( 'Fixed screen position', 'i_search' ),
														),
														$settings_for_check['inp_sc_p']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field btw-0 isrc-settings isrc-anim-h" data-showonoparent_select="input_screen_position" data-showonselection="fix">
                                                <div class="isrc-label" style="margin-bottom:10px;">
                                                    <label>
														<?php _e( 'Distance from screen (in px or %)', 'i_search' ); ?>
                                                        <p class="description">
															<?php _e( '0 is not equal to an empty value!', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input isrc-fix-p-opt">
                                                    <label>
														<?php _e( 'Top:', 'i_search' ); ?>
														<?php
														isrc_render_text_field(
															'isrc_sc_opt[fx_px_t]',
															'fx_px_t',
															( isset( $settings_for_check['fx_px_t'] ) ) ? $settings_for_check['fx_px_t'] : '10',
															'',
															'',
															'prv-upd',
															true,
															'text'
														);
														?>
                                                    </label>
                                                    <label>
														<?php _e( 'Right:', 'i_search' ); ?>
														<?php
														isrc_render_text_field(
															'isrc_sc_opt[fx_px_r]',
															'fx_px_r',
															( isset( $settings_for_check['fx_px_r'] ) ) ? $settings_for_check['fx_px_r'] : '10',
															'',
															'',
															'prv-upd',
															true,
															'text'
														);
														?>
                                                    </label>
                                                    <label>
														<?php _e( 'Bottom:', 'i_search' ); ?>
														<?php
														isrc_render_text_field(
															'isrc_sc_opt[fx_px_b]',
															'fx_px_b',
															( isset( $settings_for_check['fx_px_b'] ) ) ? $settings_for_check['fx_px_b'] : '0',
															'',
															'',
															'prv-upd',
															true,
															'text'
														);
														?>
                                                    </label>
                                                    <label>
														<?php _e( 'Left:', 'i_search' ); ?>
														<?php
														isrc_render_text_field(
															'isrc_sc_opt[fx_px_l]',
															'fx_px_l',
															( isset( $settings_for_check['fx_px_l'] ) ) ? $settings_for_check['fx_px_l'] : '0',
															'',
															'',
															'prv-upd',
															true,
															'text'
														);
														?>
                                                    </label>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_1">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Search input style', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[input_style]',
														'input_style',
														'isrc-hide-if-disabled-select prv-upd',
														'',
														'',
														'',
														array(
															'style_1' => __( 'Label on left', 'i_search' ),
															'style_2' => __( 'Plain', 'i_search' ),
															'style_3' => __( 'Underlined', 'i_search' ),
														),
														$settings_for_check['input_style']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field btw-0 isrc-settings isrc-anim-h" data-showonoparent_select="input_style" data-showonselection="style_1">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Left label', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[inp_left_label]',
														'',
														( isset( $settings_for_check['inp_left_label'] ) ) ? $settings_for_check['inp_left_label'] : 'SEARCH',
														'',
														'',
														'prv-upd'
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings" id="content_1">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Search input display style', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[input_disp_style]',
														'search_input_style',
														'prv-upd',
														'',
														'',
														'',
														array(
															'normal'        => __( 'Normal on position', 'i_search' ),
															'theme_sw_full' => __( 'Templates default search form with switch to fullscreen on click', 'i_search' ),
															'icon'          => __( 'Icon only', 'i_search' ),
															'text'          => __( 'Text only', 'i_search' ),
															'text_icon'     => __( 'Text and icon', 'i_search' ),
														),
														$settings_for_check['input_disp_style']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field btw-0 isrc-settings isrc-anim-h isrc-hide-on-hidden">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Text label', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[inp_opening_label]',
														'txt_label_inp_style',
														( isset( $settings_for_check['inp_opening_label'] ) ) ? $settings_for_check['inp_opening_label'] : 'SEARCH',
														'',
														'',
														'prv-upd'
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field btw-0 isrc-settings isrc-anim-h isrc-hide-on-hidden">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Input field opening style', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[input_open_style]',
														'inp_opening_Style',
														'prv-upd',
														'',
														'',
														'',
														array(
															'left'  => __( 'Slide to left', 'i_search' ),
															'right' => __( 'Slide to right', 'i_search' ),
															'body'  => __( 'Window center', 'i_search' ),
														),
														$settings_for_check['input_open_style']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings" id="content_3">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Spinner style', 'i_search' ); ?>
                                                        <span class="sh_preloader"><?php _e( 'Show/Hide Spinner', 'i_search' ); ?></span>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[loader_icon]',
														'loader_icon',
														'prv-upd',
														'',
														'',
														'',
														array(
															'spin_1.svg' => __( 'Style 1', 'i_search' ),
															'spin_2.svg' => __( 'Style 2', 'i_search' ),
															'spin_3.svg' => __( 'Style 3', 'i_search' ),
															'spin_4.svg' => __( 'Style 4', 'i_search' ),
														),
														$settings_for_check['loader_icon']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_5">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Input placeholder', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_8' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[placeholder]',
														'',
														( isset( $settings_for_check['placeholder'] ) ) ? $settings_for_check['placeholder'] : '',
														'',
														'',
														'prv-upd'
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_7">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Placeholder advertising', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_40' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ph_advert_label',
															"isrc_sc_opt[ph_advert]",
															'1',
															'isrc-hide-if-disabled prv-upd',
															'hide-on-advert-dis',
															$settings_for_check,
															isset( $settings_for_check['ph_advert'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <!-- START template for new row -->
                                            <template id="adv_template_row">
                                                <div class="plc-advert-wrap " id="advert_row_TPL_ADV_ID">
                                                    <div class="adv-no">TPL_ADV_NO</div>
                                                    <div class="adv-inp">
                                                        <input autocomplete="off" name="isrc_sc_opt[ph_adverts][]" type="text" class="prv-upd" value="">
                                                    </div>
                                                    <div class="adv-handler">
														<?php _e( 'Remove', 'i_search' ); ?>
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- END template for new row -->

                                            <div class="isrc-field isrc-settings hide-on-advert-dis btw-0">
                                                <div class="isrc-input">
                                                    <div class="plc-adverts-wrap">
														<?php if ( empty( $settings_for_check['ph_adverts'] ) ) { ?>
                                                            <div class="plc-advert-wrap" id="advert_row_0">
                                                                <div class="adv-no">1</div>
                                                                <div class="adv-inp">
                                                                    <input autocomplete="off" name="isrc_sc_opt[ph_adverts][]" type="text" class="prv-upd" value="">
                                                                </div>
                                                                <div class="adv-handler">
																	<?php _e( 'Remove', 'i_search' ); ?>
                                                                </div>
                                                            </div>
														<?php } else {
															foreach ( $settings_for_check['ph_adverts'] as $key => $val ) {
																?>
                                                                <div class="plc-advert-wrap" id="advert_row_<?php echo $key + 1; ?>">
                                                                    <div class="adv-no"><?php echo $key + 1; ?></div>
                                                                    <div class="adv-inp">
                                                                        <input autocomplete="off" name="isrc_sc_opt[ph_adverts][]" type="text" class="prv-upd"
                                                                               value="<?php echo stripslashes( $val ); ?>">
                                                                    </div>
                                                                    <div class="adv-handler">
																		<?php _e( 'Remove', 'i_search' ); ?>
                                                                    </div>
                                                                </div>

																<?php
															}
														} ?>
                                                    </div>
                                                    <div class="adv-add-new button-primary" id="isrc_adv_add">
														<?php _e( 'Add New Row', 'i_search' ); ?>
                                                    </div>
                                                </div>

                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_9">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Enable submit button', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_9' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_search_submit_label',
															"isrc_sc_opt[submit_btn]",
															'1',
															'isrc-hide-if-disabled prv-upd',
															'hide-on-submit-dis',
															$settings_for_check,
															isset( $settings_for_check['submit_btn'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="hide-on-submit-dis isrc-anim-h-250">
                                                <div class="isrc-field isrc-settings btw-0">
                                                    <div class="isrc-label">
                                                        <label for="isrc-settings-lbl">
															<?php _e( 'Button style', 'i_search' ); ?>
                                                        </label>
                                                    </div>
                                                    <div class="isrc-input">
														<?php
														isrc_render_select_field(
															'isrc_sc_opt[subm_btn_style]',
															'subm_btn_style',
															'prv-upd',
															'',
															'hide-on-subm_btn_style',
															'',
															array(
																'style_1' => __( 'With label', 'i_search' ),
																'style_2' => __( 'With icon', 'i_search' ),
															),
															$settings_for_check['subm_btn_style']
														);
														?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>

                                                <div class="isrc-field btw-0 isrc-settings isrc-anim-h" data-showonoparent_select="subm_btn_style" data-showonselection="style_1">
                                                    <div class="isrc-label">
                                                        <label for="isrc-settings-lbl">
															<?php _e( 'Submit button label', 'i_search' ); ?>
                                                        </label>
                                                    </div>
                                                    <div class="isrc-input">
														<?php
														isrc_render_text_field(
															'isrc_sc_opt[subm_label]',
															'isrc_search_submit_label',
															( isset( $settings_for_check['subm_label'] ) ) ? $settings_for_check['subm_label'] : '',
															'',
															'',
															'prv-upd'
														);
														?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                            <div class="isrc-field isrc-settings isrc-anim-h" id="content_11">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Minimum number of characters', 'i_search' ); ?>
                                                        <p class="description">
															<?php _e( 'Minimum number of characters required to trigger autosuggestion.', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[min_chars]',
														'isrc_min_char',
														( isset( $settings_for_check['min_chars'] ) ) ? $settings_for_check['min_chars'] : '3',
														'',
														'',
														'prv-upd',
														true,
														'number',
														1,
														10,
														1
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings" id="content_13">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Suggestions template', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[theme]',
														'isrc_template',
														'prv-upd',
														'',
														'',
														'',
														array(
															'clean'    => __( 'Clean Template', 'i_search' ),
															'advanced' => __( 'Advanced Template', 'i_search' ),
														),
														$settings_for_check['theme']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Template color scheme', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[color_style]',
														'isrc_template',
														'prv-upd',
														'',
														'',
														'',
														array(
															'light' => __( 'Light', 'i_search' ),
															'dark'  => __( 'Dark', 'i_search' ),
														),
														$settings_for_check['color_style']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings hide-on-live-dis" id="content_15">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Height of the suggestions container', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_13' ); ?>
                                                        <p class="description">
															<?php _e( 'Automatic calculation = Preview will always show 500 px', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[max_height]',
														'isrc_maxHeight',
														'isrc-hide-if-disabled-select prv-upd',
														'',
														'hide-on-maxHeightCustom',
														'',
														array(
															'calculate' => __( 'Automatic calculation', 'i_search' ),
															'auto'      => __( 'Maximum', 'i_search' ),
															'custom'    => __( 'Custom value', 'i_search' )
														),
														$settings_for_check['max_height']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings btw-0 isrc-anim-h" data-showonoparent_select="isrc_maxHeight" data-showonselection="custom">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Custom height', 'i_search' ); ?>
                                                        <p class="description">
															<?php _e( 'Enter custom value for container height.', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[mh_custom]',
														'isrc_maxHeight_custom',
														( isset( $settings_for_check['mh_custom'] ) ) ? $settings_for_check['mh_custom'] : '500',
														'',
														'',
														'prv-upd',
														true,
														'number',
														1,
														1000,
														1
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="isrc-field isrc-settings" id="content_17">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Suggestions width', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[sug_w]',
														'isrc_sug_w',
														'prv-upd',
														'',
														'',
														'',
														array(
															'auto'  => __( 'Full width', 'i_search' ),
															'input' => __( 'Input width', 'i_search' ),
														),
														$settings_for_check['sug_w']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_19">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Container offset', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_37' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[offset_top]',
														'isrc_offset_top',
														( isset( $settings_for_check['offset_top'] ) ) ? $settings_for_check['offset_top'] : '0',
														'',
														'',
														'prv-upd',
														true,
														'number',
														0,
														500,
														1
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_21">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Suggestions orientation', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_36' ); ?>
                                                        <p class="description">
															<?php _e( '* Preview is always bottom.', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[orientation]',
														'isrc_orientation',
														'',
														'',
														'',
														'',
														array(
															'auto'   => __( 'Auto', 'i_search' ),
															'top'    => __( 'Top', 'i_search' ),
															'bottom' => __( 'Bottom', 'i_search' ),
														),
														$settings_for_check['orientation']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings isrc-anim-h" id="content_23">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Maximum number of results', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_12' ); ?>
                                                        <p class="description">
															<?php _e( 'Maximum number of results to show in the suggestion box. (Max 50).', 'i_search' ); ?>
                                                        </p>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_text_field(
														'isrc_sc_opt[limit]',
														'isrc_posts_per_page',
														( isset( $settings_for_check['limit'] ) ) ? $settings_for_check['limit'] : '10',
														'',
														'',
														'prv-upd',
														true,
														'number',
														1,
														50,
														1
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

											<?php if ( ! empty( $taborder ) ) : ?>
                                                <div class="isrc-field isrc-settings hide-on-live-dis" id="content_25">
                                                    <div class="isrc-label">
                                                        <label for="isrc-settings-lbl">
															<?php _e( 'Search limitation', 'i_search' ); ?>
                                                            <p class="description">
																<?php _e( 'Will search only for selected types. Drag and drop to re-order', 'i_search' ); ?>
                                                            </p>
                                                        </label>
                                                    </div>
                                                    <div class="isrc-input">
                                                        <ul id="sortable_tabs" class="ui-sortable sc-tabs">
															<?php
															foreach ( $taborder as $key => $val ):
																if ( $val['type'] == 'post_type' ) {
																	$checkbox_name = 'pt_' . $key;
																} elseif ( $val['type'] == 'taxonomy' ) {
																	$checkbox_name = 'tx_' . $key;
																}
																?>
                                                                <li data-type="<?php echo $val['type']; ?>" class="ui-state-default _closed">
                                                                    <label for="search_in_<?php echo $key; ?>">
                                                                        <input
																			<?php echo ( isset( $settings_for_check['search_in'][ $checkbox_name ] ) || ! $isupdate ) ? 'checked="checked"' : ""; ?>
                                                                                id="search_in_<?php echo $key; ?>"
                                                                                type="checkbox"
                                                                                name="isrc_sc_opt[search_in][<?php echo $checkbox_name; ?>]"
                                                                                class="prv-upd">
                                                                    </label>

                                                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                                    <span class="sort-name">
                                                                  <?php
                                                                  ( $val['type'] == 'post_type' ) ?
	                                                                  _e( 'Post Type', 'i_search' ) :
	                                                                  _e( 'Taxonomy', 'i_search' )
                                                                  ?>
                                                                        : <b><?php echo $val['name']; ?></b> (<?php echo $val['label']; ?>)
                                                                </span>
                                                                    <span class="isrc-tab-arrow"></span>
                                                                    <div class="tabmore">
                                                                        <label class="" for="image_in_<?php echo $key; ?>">
                                                                            <input
																				<?php echo ( isset( $settings_for_check['search_in_images'][ $checkbox_name ] ) || ! $isupdate ) ? 'checked="checked"' : ""; ?>
																				<?php echo ( isset( $defaults['front']['img'] ) ) ? '' : ' disabled'; ?>
                                                                                    id="image_in_<?php echo $key; ?>"
                                                                                    type="checkbox"
                                                                                    name="isrc_sc_opt[search_in_images][<?php echo $checkbox_name; ?>]"
                                                                                    class="prv-upd">
																			<?php _e( 'Show image', 'i_search' ); ?>
                                                                        </label>
                                                                        <label class="" for="title_only_<?php echo $key; ?>">
                                                                            <input
																				<?php echo ( isset( $settings_for_check['title_only'][ $checkbox_name ] ) ) ? 'checked="checked"' : ""; ?>
                                                                                    id="title_only_<?php echo $key; ?>"
                                                                                    type="checkbox"
                                                                                    name="isrc_sc_opt[title_only][<?php echo $checkbox_name; ?>]"
                                                                                    class="prv-upd">
																			<?php _e( 'Show only title', 'i_search' ); ?>

                                                                        </label>
                                                                        <label class="" for="excerpt_style_<?php echo $key; ?>">
                                                                            <input
																				<?php echo ( isset( $settings_for_check['exc_multi_line'][ $checkbox_name ] ) ) ? 'checked="checked"' : ""; ?>
                                                                                    id="excerpt_style_<?php echo $key; ?>"
                                                                                    type="checkbox"
                                                                                    name="isrc_sc_opt[exc_multi_line][<?php echo $checkbox_name; ?>]"
                                                                                    class="prv-upd">
																			<?php _e( 'Excerpt multi lines', 'i_search' ); ?>

                                                                        </label>
                                                                        <label style="margin-top:10px" class="" for="excerpt_max_<?php echo $key; ?>">
																			<?php _e( 'Excerpt max. words', 'i_search' );
																			isrc_render_text_field(
																				"isrc_sc_opt[exc_max_words][$checkbox_name]",
																				'',
																				( isset( $settings_for_check['exc_max_words'][ $checkbox_name ] ) ) ? $settings_for_check['exc_max_words'][ $checkbox_name ] : '75',
																				__( 'Show max. number of WORDS (not characters)', 'i_search' ),
																				'',
																				'prv-upd',
																				true,
																				'number',
																				0,
																				150,
																				1
																			);
																			?>
                                                                        </label>

																		<?php if ( $val['type'] == 'post_type' ) { ?>
                                                                            <div class="ib-cb-desc"><?php _e( 'Hide content builder fields:', 'i_search' ); ?> </div>
                                                                            <div class="src-inst-cb-flds">
																				<?php $cb_fields = isrc_get_available_extra_fields();
																				foreach ( $cb_fields as $cb_key => $cb_val ) { ?>
                                                                                    <label>
                                                                                        <input
																							<?php echo ( isset( $settings_for_check['cb_flds'][ $checkbox_name ][ $cb_key ] ) ) ? 'checked="checked"' : ""; ?>
                                                                                                id="cb_<?php echo $cb_key; ?>"
                                                                                                type="checkbox"
                                                                                                name="isrc_sc_opt[cb_flds][<?php echo $checkbox_name; ?>][<?php echo $cb_key; ?>]"
                                                                                                class="prv-upd">
																						<?php _e( $cb_val['title'], 'i_search' ); ?>
                                                                                    </label>
																				<?php } ?>
                                                                            </div>
																		<?php } ?>

                                                                    </div>

                                                                </li>
															<?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
											<?php endif ?>

                                            <div class="isrc-field isrc-settings" id="content_27">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Enable Tabs', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ed_tabs',
															"isrc_sc_opt[tabs_enabled]",
															'1',
															'isrc-show-if-disabled prv-upd',
															'hide-on-tabs-dis',
															$settings_for_check,
															isset( $settings_for_check['tabs_enabled'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings hide-on-tabs-dis isrc-anim-h">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Enable post type divider', 'i_search' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ed_pt_div',
															"isrc_sc_opt[ptdiv_enabled]",
															'1',
															'prv-upd',
															'',
															$settings_for_check,
															isset( $settings_for_check['ptdiv_enabled'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_29">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Enable logging for search analysis', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_5' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ed_logging',
															"isrc_sc_opt[logging]",
															'1',
															'prv-upd',
															'',
															$settings_for_check,
															isset( $settings_for_check['logging'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Enable logging for popularity index', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_27' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ed_popular',
															"isrc_sc_opt[log_popularity]",
															'1',
															'prv-upd',
															'',
															$settings_for_check,
															isset( $settings_for_check['log_popularity'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>


                                            <div class="isrc-field isrc-settings" id="content_31">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Results order', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_25' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
													<?php
													isrc_render_select_field(
														'isrc_sc_opt[order_by]',
														'isrc_template',
														'prv-upd',
														'',
														'',
														'',
														array(
															'title'      => __( 'By title', 'i_search' ),
															'post_id'    => __( 'By latest post', 'i_search' ),
															'popularity' => __( 'By click popularity', 'i_search' ),
															'random'     => __( 'Randomly', 'i_search' )
														),
														$settings_for_check['order_by']
													);
													?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings" id="content_33">
                                                <div class="isrc-label">
                                                    <label for="isrc-settings-lbl">
														<?php _e( 'Show "No result" string', 'i_search' ); ?>
														<?php isrc_render_help_icon( 'help_10' ); ?>
                                                    </label>
                                                </div>
                                                <div class="isrc-input">
                                                    <ul class="isrc-checkbox-list isrc-bl">
														<?php
														isrc_render_fieldset_checkbox(
															'isrc_ed_noresult',
															"isrc_sc_opt[ed_noresult]",
															'1',
															'isrc-hide-if-disabled prv-upd',
															'hide-on-noresult-dis',
															$settings_for_check,
															isset( $settings_for_check['ed_noresult'] ),
															'',
															__( 'Yes', 'i_search' ),
															'',
															false,
															true,
															true
														);
														?>
                                                    </ul>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div class="isrc-field isrc-settings hide-on-noresult-dis isrc-anim-h"
                                            ">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'No results label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[noresult_label]',
													'',
													( isset( $settings_for_check['noresult_label'] ) ) ? $settings_for_check['noresult_label'] : 'No Result',
													'',
													'',
													'prv-upd'
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings" id="content_35">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show "Did you mean"', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_11' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_ed_didumean',
														"isrc_sc_opt[ed_didumean]",
														'1',
														'isrc-hide-if-disabled prv-upd',
														'hide-on-didumean-dis',
														$settings_for_check,
														isset( $settings_for_check['ed_didumean'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false,
														true,
														true
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-didumean-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Did you mean label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[didumean_label]',
													'',
													( isset( $settings_for_check['didumean_label'] ) ) ? $settings_for_check['didumean_label'] : 'Did you mean',
													'',
													'',
													'prv-upd'
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings" id="content_37">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show "Popular Searches"', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_28' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_popularity',
														"isrc_sc_opt[show_popularity]",
														'1',
														'isrc-hide-if-disabled prv-upd',
														'hide-on-popular-dis',
														$settings_for_check,
														isset( $settings_for_check['show_popularity'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false,
														true,
														true
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-popular-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Popular searches label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[popular_label]',
													'',
													( isset( $settings_for_check['popular_label'] ) ) ? $settings_for_check['popular_label'] : 'No Results. Popular Searches',
													'',
													'',
													'prv-upd'
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-popular-dis isrc-anim-h btw-0">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Popular searches max number (1-50)', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[popular_max]',
													'',
													( isset( $settings_for_check['popular_max'] ) ) ? $settings_for_check['popular_max'] : 5,
													'',
													'',
													'prv-upd',
													true,
													'number',
													1,
													50,
													1

												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings" id="content_37">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show "Trending Searches"', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_49' ); ?>
                                                    <p class="description">
														<?php _e( 'Under the input field.', 'i_search' ); ?>
                                                    </p>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_trendings',
														"isrc_sc_opt[show_trendings]",
														'1',
														'isrc-hide-if-disabled prv-upd',
														'hide-on-trendings-dis',
														$settings_for_check,
														isset( $settings_for_check['show_trendings'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false,
														true,
														true
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-trendings-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Trending searches label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[trendings_label]',
													'',
													( isset( $settings_for_check['trendings_label'] ) ) ? $settings_for_check['trendings_label'] : 'Trending searches:',
													'',
													'',
													'prv-upd'
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-trendings-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Trending searches position', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_select_field(
													'isrc_sc_opt[trendings_pos]',
													'',
													'hide-on-trendings-dis prv-upd',
													'',
													'',
													'',
													array(
														'above' => __( 'Above the search field', 'i_search' ),
														'below' => __( 'Below the search field', 'i_search' ),
														'in'    => __( 'In the search field', 'i_search' ),
													),
													$settings_for_check['trendings_pos']
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-trendings-dis isrc-anim-h btw-0">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Trending searches max number (1-10)', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[trendings_max]',
													'',
													( isset( $settings_for_check['trendings_max'] ) ) ? $settings_for_check['trendings_max'] : 3,
													'',
													'',
													'prv-upd',
													true,
													'number',
													1,
													10,
													1

												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>


                                        <div class="isrc-field isrc-settings" id="content_39">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show "More results" button', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_39' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_ed_view_all',
														"isrc_sc_opt[ed_viewall]",
														'1',
														'isrc-hide-if-disabled prv-upd',
														'hide-on-viewall-dis',
														$settings_for_check,
														isset( $settings_for_check['ed_viewall'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false,
														true,
														true
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-viewall-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Enable continuous scrolling ', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_38' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_ed_continue',
														"isrc_sc_opt[ed_continue]",
														'1',
														'prv-upd',
														'',
														$settings_for_check,
														isset( $settings_for_check['ed_continue'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false,
														true,
														true
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings hide-on-viewall-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'More results label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_sc_opt[viewall_label]',
													'',
													( isset( $settings_for_check['viewall_label'] ) ) ? $settings_for_check['viewall_label'] : 'Show more',
													'',
													'',
													'prv-upd'
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings" id="content_41">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-css">
													<?php _e( 'Custom CSS', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_35' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                            <textarea id="codemirror" name="isrc_sc_opt[css]"
                                                      placeholder="<?php _e( 'Enter your custom CSS here', 'i_search' ); ?>"><?php echo ( isset( $settings_for_check['css'] ) ) ? $settings_for_check['css'] : ''; ?></textarea>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
										<?php
										require_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-helper-css-builder.php';
										?>
                                    </div>
                                </div>
                            </div>
                            <div class="sc_right_content cnt-white-shadow iframewrapper">
                                <h3><?php _e( 'Preview', 'i_search' ); ?></h3>
                                <div class="isrc-preview-wrap">
                                    <div class="isrc-preview-preloader"></div>
                                    <iframe id="isrc_frame"
                                            width="<?php echo ( isset( $settings_for_check['preview']['preview_width'] ) ) ? $settings_for_check['preview']['preview_width'] : '100%' ?>"></iframe>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>

                    </div>
                </div>
            </div>


            <div id="postbox-container-1" class="postbox-container">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div id="side-update" class="postbox">
                        <h2 class="hndle ui-sortable-handle">
            <span>
            <?php _e( 'Update', 'i_search' ); ?>
            </span>
                        </h2>
                        <div class="inside">
                            <p class="submit">
                                <button name="save" class="button-primary w100p" type="submit"
                                        value="<?php _e( 'Build Instance', 'i_search' ); ?>">
									<?php
									if ( $isupdate ) {
										_e( 'Update Instance', 'i_search' );
									} else {
										_e( 'Build Instance', 'i_search' );
									}
									?>
                                </button>
                            </p>
                            <div class="isrc-field isrc-settings">
                                <div class="isrc-label">
                                    <label for="isrc-settings-lbl">
										<?php _e( 'Make this instance default.', 'i_search' ); ?>
										<?php isrc_render_help_icon( 'help_41' ); ?>
                                    </label>
                                </div>
                                <div class="isrc-input isrc-default-instance">
                                    <ul class="isrc-checkbox-list isrc-bl">
										<?php
										isrc_render_fieldset_checkbox(
											'isrc_default_instance',
											"isrc_sc_opt[default_sc]",
											'1',
											'',
											'',
											$settings_for_check,
											isset( $settings_for_check['default_sc'] ),
											'',
											__( 'Yes', 'i_search' ),
											'',
											false,
											true,
											true
										);
										?>
                                    </ul>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="isrc-field isrc-settings">
                                <div class="isrc-label">
                                    <label for="isrc-settings-lbl">
										<?php _e( 'Hide on mobile devices?', 'i_search' ); ?>
                                    </label>
                                </div>
                                <div class="isrc-input isrc-default-instance">
                                    <ul class="isrc-checkbox-list isrc-bl">
										<?php
										isrc_render_fieldset_checkbox(
											'isrc_mobile_hide',
											"isrc_sc_opt[hide_on_mobile]",
											'1',
											'',
											'',
											$settings_for_check,
											isset( $settings_for_check['hide_on_mobile'] ),
											'',
											__( 'Yes', 'i_search' ),
											'',
											false,
											true,
											true
										);
										?>
                                    </ul>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="isrc-field isrc-settings">
                                <div class="isrc-label">
                                    <label for="isrc-settings-lbl">
										<?php _e( 'Hide on desktop devices?', 'i_search' ); ?>
                                    </label>
                                </div>
                                <div class="isrc-input isrc-default-instance">
                                    <ul class="isrc-checkbox-list isrc-bl">
										<?php
										isrc_render_fieldset_checkbox(
											'hide_on_desk',
											"isrc_sc_opt[hide_on_desk]",
											'1',
											'',
											'',
											$settings_for_check,
											isset( $settings_for_check['hide_on_desk'] ),
											'',
											__( 'Yes', 'i_search' ),
											'',
											false,
											true,
											true
										);
										?>
                                    </ul>
                                </div>
                                <div class="clear"></div>
                            </div>
							<?php wp_nonce_field( 'isrc_sc_opt' ); ?>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle otp">
                            <i class="fas fa-desktop"></i>
                            <span>
                                <?php _e( 'Preview options', 'i_search' ); ?>
                            </span>
                        </h2>
                        <div class="inside">
                            <div class="isrc-field isrc-settings isrc-havecolorpicker btw-0">
                                <div class="isrc-label">
                                    <label for="isrc-settings-lbl">
										<?php _e( 'Preview background color', 'i_search' ); ?>
                                    </label>
                                </div>
                                <div class="isrc-input isrc-default-instance w99pi">
									<?php
									isrc_render_text_field(
										'isrc_sc_opt[preview][bg_color]',
										'preview_bg_color',
										( isset( $settings_for_check['preview']['bg_color'] ) ) ? $settings_for_check['preview']['bg_color'] : '',
										'',
										'',
										'color-picker isrc-colors'
									);
									?>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <div class="inside">
                            <div class="isrc-field isrc-settings">
                                <div class="isrc-label">
                                    <label for="isrc-settings-lbl">
										<?php _e( 'Preview width (% or px)', 'i_search' ); ?>
                                    </label>
                                </div>
                                <div class="isrc-input isrc-default-instance w99pi">
									<?php
									isrc_render_text_field(
										'isrc_sc_opt[preview][preview_width]',
										'preview_width',
										( isset( $settings_for_check['preview']['preview_width'] ) ) ? $settings_for_check['preview']['preview_width'] : '100%',
										'',
										'width:100%',
										''
									);
									?>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>
					<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-video-tutorial.php'; ?>
					<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-instance-contents.php'; ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
</div>
</form>
</div>

<!-- JS snippets comes here. We are in the admin. We are allowed to put our JS in the html directly. Because we handle also with php in JS-->

<script>
    let isrc_nonce = '<?php echo $isrc_nonce; ?>',
        isearch_current_page = 'isrc-opt-page',
        isearch_current_tab = 'shortcode_add_new',
        isrc_js_params = {
            video_tut_url: 'https://all4wp.net/redirect/isearch/tut-menu-settings-3/',
            enter_title: '<?php _e( 'Please enter a title for this instance.', 'i_search' ); ?>',
            label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
            label_label: '<?php _e( 'Label', 'i_search' ); ?>',
            label_remove: '<?php _e( 'Label', 'i_search' ); ?>',
            preview_src: '<?php echo $preview_src; ?>',
            confirm_value: '<?php _e( 'This action cannot be undone', 'i_search' ); ?>',
            confirm_value2: '<?php _e( 'This action cannot be undone. It will alse delete all isearch taxonomy meta data.', 'i_search' ); ?>',
            clicks_disabled: '<?php _e( 'Clicks are disabled in preview', 'i_search' ); ?>',
        };
</script>