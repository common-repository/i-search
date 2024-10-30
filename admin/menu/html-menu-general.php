<?php

/**
 * i-Search html menu for the general settings TAB
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
$options = get_option( 'isrc_opt_' . isrc_get_lang_admin() );
// Get WordPress' media upload URL
/* build image size name based on settings */
$h               = ( isset( $options['front']['thumb_size']['h'] ) ) ? $options['front']['thumb_size']['h'] : 70;
$w               = ( isset( $options['front']['thumb_size']['w'] ) ) ? $options['front']['thumb_size']['w'] : 50;
$thumb_size_name = "isrc_thumb_{$h}_{$w}";


$upload_link = esc_url( get_upload_iframe_src( 'image' ) );

// See if there's a media id already saved
if ( isset( $options['front']['no_img'] ) ) {
	$isrc_img_id = $options['front']['no_img'];
	// Get the image src
	$isrc_img_src = wp_get_attachment_image_src( $isrc_img_id, $thumb_size_name );
	// For convenience, see if the array is valid
	$you_have_img = is_array( $isrc_img_src );
} else {
	$isrc_img_id  = false;
	$you_have_img = false;
}

$video_tutorial_title = __( 'General Settings', 'i_search' );

include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';
?>
<div class="isrc-opt-page isrc-opt-page-general <?php echo ( is_rtl() ) ? 'rtl' : ''; ?>">
	<?php
	$string = esc_html__( 'Settings Updated.', 'i_search' );
	$class  = 'settings-updated';
	do_action( 'isrc_admin_notice', $string, $class );
	?>

    <form method="POST">
        <input type="hidden" name="isrc_opt_page" value="general">
        <input type="hidden" name="isrc_opt[front][tabs_ed]" value="1">
        <input type="hidden" name="isrc_opt[lang]" value="<?php echo isrc_get_lang_admin(); ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">

                    <div class="isrc-container cnt-general">
                        <div class="sttngs-cont cnt-white-shadow">
                            <div class="isrc-inside">
                                <div class="isrc-field isrc-settings" id="content_1">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Enable i-Search for Post Types', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_1' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											$post_types = get_post_types( array( 'public' => true ), 'objects', 'and' );
											foreach ( $post_types as $key => $val ) {
												$value = $val->name;
												$label = $val->label;
												echo '<li>';
												isrc_render_fieldset_checkbox(
													'isrc_ptype_' . $value,
													"isrc_opt[post_types][]",
													$value,
													'',
													'',
													$options,
													( isset( $options['post_types'] ) ) ? isrc_checked( $options['post_types'], $value, false ) : '',
													'',
													$label,
													'',
													false
												);
												echo '</li>';
											} ?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_2">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Include in search algorithm', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_2' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_include_tags',
												"isrc_opt[includes][]",
												'post_tag',
												'',
												'',
												$options,
												( isset( $options['includes'] ) ) ? isrc_checked( $options['includes'], 'post_tag', false ) : '',
												'',
												__( 'Post Tags', 'i_search' )
											);
											if ( defined( "ISRC_WOOCOMMERCE_INSTALLED" ) ) {
												isrc_render_fieldset_checkbox(
													'isrc_include_ptags',
													"isrc_opt[includes][]",
													'product_tag',
													'',
													'',
													$options,
													( isset( $options['includes'] ) ) ? isrc_checked( $options['includes'], 'product_tag', false ) : '',
													'',
													__( 'Product Tags', 'i_search' )
												);
											}
											isrc_render_fieldset_checkbox(
												'isrc_include_cats',
												"isrc_opt[includes][]",
												'category',
												'',
												'',
												$options,
												( isset( $options['includes'] ) ) ? isrc_checked( $options['includes'], 'category', false ) : '',
												'',
												__( 'Categories', 'i_search' )
											);
											if ( defined( "ISRC_WOOCOMMERCE_INSTALLED" ) ) {
												isrc_render_fieldset_checkbox(
													'isrc_include_pcats',
													"isrc_opt[includes][]",
													'product_cat',
													'',
													'',
													$options,
													( isset( $options['includes'] ) ) ? isrc_checked( $options['includes'], 'product_cat', false ) : '',
													'',
													__( 'Product Categories', 'i_search' )
												);
											}
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_3">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'WP search', 'i_search' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_front_replace',
												"isrc_opt[front][replace]",
												'1',
												'',
												'',
												$options,
												isset( $options['front']['replace'] ),
												'help_4',
												__( 'Replace WP intern search engine with i-Search', 'i_search' ) );

											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_4">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Live search settings', 'i_search' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'auto_hook',
												"isrc_opt[front][auto_hook]",
												'1',
												'',
												'',
												$options,
												isset( $options['front']['auto_hook'] ),
												'help_6',
												__( 'Replace WP search form', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_4">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Boost up search speed', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_7' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
	                                    <?php echo isrc_pro_only_txt(); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_4_41">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Search keyword handle', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_43' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_select_field(
												'isrc_opt[front][kw_handle]',
												'kw_handle',
												'',
												'',
												'',
												'',
												array(
													'split'          => __( 'Ignore search words order but match all keywords (With pluralization)', 'i_search' ),
													'split_with_or'  => __( 'Match at least one search word (With pluralization)', 'i_search' ),
													'normal'         => __( 'Keep search words order, match all keywords and try to pluralize (Exact match)', 'i_search' ),
													'normal_with_pl' => __( 'Try to keep search words order, try to match all keywords and pluralize (Exact match with pluralization)', 'i_search' ),
												),
												$options['front']['kw_handle']
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div data-group="3_8" class="isrc-field isrc-settings hide-src-tabs isrc-anim-h isrc-mh500 hide-on-live-dis isrc-anim-h">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Order of the tabs', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_15' ); ?>
                                            <p class="description">
												<?php _e( 'Drag and drop to change the default order of the tabs.', 'i_search' ); ?>
                                            </p>
                                        </label>
                                    </div>
                                    <div class="isrc-input taborderdrag">
                                        <div>
                                            <ul id="sortable_tabs">

												<?php
												if ( ! empty( $options['front']['taborder'] ) ) {
													$taborder = $options['front']['taborder'];
													?>
													<?php foreach ( $taborder as $key => $val ) : ?>
                                                        <li class="ui-state-default">
                                                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                            <span class="sort-name">
                              <?php ( $val['type'] == 'post_type' ) ? _e( 'Post Type', 'i_search' ) : _e( 'Taxonomy', 'i_search' ) ?>
                                                                : <b><?php echo $val['name']; ?></b>
                                                                </span>
                                                            <span class="sort-rename">
                                                                    <?php _e( 'Label', 'i_search' ); ?>
                                                                : </span>
                                                            <input id="taborder_temp"
                                                                   name="isrc_opt[front][taborder][<?php echo $key; ?>][label]"
                                                                   type="text"
                                                                   value="<?php echo( isset( $val['label'] ) ? $val['label'] : '' ); ?>">
															<?php if ( $val['type'] == 'taxonomy' ) : ?>
                                                                <span class="remove_taxonomy">
                              <?php _e( 'Remove', 'i_search' ); ?>
                                                                </span>
															<?php endif; ?>
                                                            <input name="isrc_opt[front][taborder][<?php echo $key; ?>][type]"
                                                                   type="hidden"
                                                                   value="<?php echo $val['type']; ?>">
                                                            <input class="s2_exclude_1"
                                                                   name="isrc_opt[front][taborder][<?php echo $key; ?>][name]"
                                                                   type="hidden" value="<?php echo $key; ?>">
                                                        </li>
													<?php endforeach;
												}
												?>
                                            </ul>
                                            <div class="isrc_add_new_tab_outer">
                                                <a href="#" class="button-primary" id="isrc_add_new_tab">
													<?php _e( 'Add New Tab', 'i_search' ); ?>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings hide-on-live-dis isrc-anim-h" id="content_12">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Show thumbnails', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_16' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_show_img',
												"isrc_opt[front][img]",
												'1',
												'isrc-hide-if-disabled',
												'hide-show-thumb',
												$options,
												isset( $options['front']['img'] ),
												'',
												__( 'Yes', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings hide-show-thumb isrc-anim-h hide-on-live-dis">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Thumbnail size in px', 'i_search' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
                                            <label style="margin-right:10px;" for="isrc_thumb_size_w">
												<?php _e( 'Width', 'i_search' ); ?>:
                                            </label>
											<?php
											isrc_render_text_field(
												'isrc_opt[front][thumb_size][w]',
												'isrc_thumb_size_w',
												( isset( $options['front']['thumb_size']['w'] ) ) ? $options['front']['thumb_size']['w'] : '50',
												'',
												'width:70px;',
												'',
												true,
												'number',
												1,
												1000,
												1
											);
											?>
                                            <label style="margin-right:10px;margin-left:20px" for="isrc_thumb_size_h">
												<?php _e( 'Height', 'i_search' ); ?>:
                                            </label>
											<?php
											isrc_render_text_field(
												'isrc_opt[front][thumb_size][h]',
												'isrc_thumb_size_h',
												( isset( $options['front']['thumb_size']['h'] ) ) ? $options['front']['thumb_size']['h'] : '100',
												'',
												'width:70px;',
												'',
												true,
												'number',
												1,
												1000,
												1
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings hide-show-thumb isrc-anim-h hide-on-live-dis">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'No image thumbnail', 'i_search' ); ?>
                                            <p class="description">
												<?php _e( 'This image will be shown if no thumbnail is available.', 'i_search' ); ?>
                                            </p>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="isrc_extra_image hide-if-no-js">
                                            <div id="isrc_extra_image_container">
                                                <div class="isrc-meta-img-container">
													<?php if ( $you_have_img ) : ?>
                                                        <img src="<?php echo $isrc_img_src[0] ?>" alt="" style="max-width:100%;"/>
													<?php endif; ?>
                                                </div>

                                                <!-- isrc add & remove image links -->
                                                <p class="hide-if-no-js" style="text-align: left;margin-top: 3px;">
                                                    <a class="upload-isrc-custom-img <?php echo ( $you_have_img ) ? 'hidden' : ''; ?>" href="<?php echo $upload_link ?>">
														<?php _e( 'Set custom image', 'i_search' ) ?>
                                                    </a> <a class="delete-custom-img <?php echo ( ! $you_have_img ) ? 'hidden' : ''; ?>" href="#">
														<?php _e( 'Remove this image', 'i_search' ) ?>
                                                    </a></p>

                                                <!-- A hidden input to set and post the chosen image id -->
                                                <input
                                                        class="isrc_img_id"
                                                        name="isrc_opt[front][no_img]"
                                                        type="hidden"
                                                        value="<?php echo ( $you_have_img ) ? esc_attr( $isrc_img_id ) : ''; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings hide-on-live-dis isrc-anim-h" id="content_13">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Include excerpt', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_17' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_show_excerpt',
												"isrc_opt[front][excerpt]",
												'1',
												'',
												'',
												$options,
												isset( $options['front']['excerpt'] ),
												'',
												__( 'Yes', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Show categories', 'i_search' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_show_post_cats',
												"isrc_opt[front][cat]",
												'1',
												'isrc-hide-if-disabled',
												'hide-on-postcats-dis',
												$options,
												isset( $options['front']['cat'] ),
												'',
												__( 'Yes', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings hide-on-postcats-dis isrc-anim-h">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Categories label', 'i_search' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
										<?php
										isrc_render_text_field(
											'isrc_opt[front][cats_l]',
											'isrc_postcats_label',
											( isset( $options['front']['cats_l'] ) ) ? $options['front']['cats_l'] : '',
											'',
											''
										);
										?>
                                    </div>
                                    <div class="clear"></div>
                                </div>


                            </div>
                        </div>
                    </div>

					<?php if ( defined( "ISRC_WOOCOMMERCE_INSTALLED" ) ) : ?>
                        <div class="" id="content_14">
                            <div class="isrc-container cnt-general">
                                <div class="sttngs-cont cnt-white-shadow">
                                    <h3>
										<?php _e( 'WooCommerce', 'i_search' ); ?>
                                    </h3>

                                    <div class="isrc-inside">

                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Enable cart button', 'i_search' ); ?>
													<?php isrc_render_help_icon( 'help_31' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_search_atc_label',
														"isrc_opt[front][enable_atc]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-atc-dis',
														$options,
														isset( $options['front']['enable_atc'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-atc-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Cart button label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[front][atc_label]',
													'isrc_atc_label',
													( isset( $options['front']['atc_label'] ) ) ? $options['front']['atc_label'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Enable buy now button', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'buyn_btn',
														"isrc_opt[front][enable_buyn]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-buyn-dis',
														$options,
														isset( $options['front']['enable_buyn'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-buyn-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Buy now button label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[front][buyn_label]',
													'buyn_label',
													( isset( $options['front']['buyn_label'] ) ) ? $options['front']['buyn_label'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show price', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_price',
														"isrc_opt[woo][price]",
														'1',
														'',
														'',
														$options,
														isset( $options['woo']['price'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show rating stars', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_rating',
														"isrc_opt[woo][rating]",
														'1',
														'',
														'',
														$options,
														isset( $options['woo']['rating'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show categories', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_cats',
														"isrc_opt[woo][cat]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-cats-dis',
														$options,
														isset( $options['woo']['cat'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-cats-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Categories label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][cats_l]',
													'isrc_cats_label',
													( isset( $options['woo']['cats_l'] ) ) ? $options['woo']['cats_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show out of stock', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_outofstock',
														"isrc_opt[woo][outofstock]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-oos-dis',
														$options,
														isset( $options['woo']['outofstock'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-oos-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Out of stock label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][outofstock_l]',
													'isrc_outofstock_label',
													( isset( $options['woo']['outofstock_l'] ) ) ? $options['woo']['outofstock_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show detailed stock for variable products', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_variablep',
														"isrc_opt[woo][variablep]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-variablep-dis',
														$options,
														isset( $options['woo']['variablep'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-variablep-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'In stock label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][instock_l]',
													'isrc_instock_label',
													( isset( $options['woo']['instock_l'] ) ) ? $options['woo']['instock_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show Backorder', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_backorder',
														"isrc_opt[woo][backorder]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-backorder-dis',
														$options,
														isset( $options['woo']['backorder'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-backorder-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Backorder label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][backorder_l]',
													'isrc_backorder_label',
													( isset( $options['woo']['backorder_l'] ) ) ? $options['woo']['backorder_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show on sale', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_sale',
														"isrc_opt[woo][sale]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-sale-dis',
														$options,
														isset( $options['woo']['sale'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-sale-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'On sale label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][sale_l]',
													'isrc_sale_label',
													( isset( $options['woo']['sale_l'] ) ) ? $options['woo']['sale_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Show featured', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
                                                <ul class="isrc-checkbox-list isrc-bl">
													<?php
													isrc_render_fieldset_checkbox(
														'isrc_show_featured',
														"isrc_opt[woo][featured]",
														'1',
														'isrc-hide-if-disabled',
														'hide-on-featured-dis',
														$options,
														isset( $options['woo']['featured'] ),
														'',
														__( 'Yes', 'i_search' ),
														'',
														false
													);
													?>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <div class="isrc-field isrc-settings hide-on-featured-dis isrc-anim-h">
                                            <div class="isrc-label">
                                                <label for="isrc-settings-lbl">
													<?php _e( 'Featured label', 'i_search' ); ?>
                                                </label>
                                            </div>
                                            <div class="isrc-input">
												<?php
												isrc_render_text_field(
													'isrc_opt[woo][featured_l]',
													'isrc_featured_label',
													( isset( $options['woo']['featured_l'] ) ) ? $options['woo']['featured_l'] : '',
													'',
													''
												);
												?>
                                            </div>
                                            <div class="clear"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
					<?php endif ?>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-reindex.php'; ?>
                        <div id="side-2" class="postbox">
                            <h2 class="hndle ui-sortable-handle">
                                <span>
                <?php _e( 'Update', 'i_search' ); ?>
                </span>
                            </h2>
                            <div class="inside">
                                <p class="submit">
                                    <button name="save" class="button-primary w100p" type="submit"
                                            value="<?php _e( 'Update Settings', 'i_search' ); ?>">
										<?php _e( 'Update Settings', 'i_search' ); ?>
                                    </button>
									<?php wp_nonce_field( 'isrc_opt_general_settings' ); ?>
                                </p>
                            </div>
                        </div>
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-video-tutorial.php'; ?>
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-general-contents.php'; ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </form>
    <div class="remodal modal_add_new_tab" data-remodal-id="modal_add_new_tab">
        <button data-remodal-action="close" class="remodal-close"></button>
        <h3>
            <label for="isrc_taxonomies_select2"><?php _e( 'Select a taxonomy for the tab', 'i_search' ); ?></label>
        </h3>
        <div class="modal_content">
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <td class="forminp forminp-text">
                        <select id="isrc_taxonomies_select2"
                                class="select2_add_to_post_modal" style="width:100%"
                                name="taxonomies">
                        </select>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <button data-remodal-action="cancel" class="remodal-cancel">
			<?php _e( 'Cancel', 'i_search' ); ?>
        </button>
        <button class="remodal-confirm">
			<?php _e( 'OK', 'i_search' ); ?>
        </button>
    </div>
</div>

<script>
    /* js variables */
    let isearch_current_page = 'isrc-opt-page';
    let isearch_current_tab = 'settings';
    let isrc_js_params = {
        video_tut_url: 'https://all4wp.net/redirect/isearch/tut-menu-settings-1/',
        label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
        label_label: '<?php _e( 'Label', 'i_search' ); ?>',
        label_remove: '<?php _e( 'Remove', 'i_search' ); ?>',
    };
</script>