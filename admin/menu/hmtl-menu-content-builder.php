<?php

/**
 * i-Search html menu for the advanced TAB
 *
 * This file will be loaded with include_once in the class-menu.php file.
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
$isrc_nonce      = wp_create_nonce( 'isrc_settings' );
$select2_nonce   = wp_create_nonce( 'isrc_table_nonce' );
$lang            = isrc_get_lang_admin();
$options         = get_option( 'isrc_opt_content_' . $lang );
$options_general = get_option( 'isrc_opt_' . $lang );

if ( isset( $options['builder_data'] ) ) {
	$builder_data = $options['builder_data'];
} else {
	$builder_data = array();
}

include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';

if ( isset( $options_general['include_in_suggestions']['post_types'] ) ) {
	$post_types = $options_general['include_in_suggestions']['post_types'];
} else {
	$post_types = array();
}

if ( isset( $options_general['include_in_suggestions']['taxonomies'] ) ) {
	$taxonomies = $options_general['include_in_suggestions']['taxonomies'];
} else {
	$taxonomies = array();
}
$video_tutorial_title = __( 'Content Builder', 'i_search' );

?>
<div class="isrc-opt-page isrc-opt-page-content-builder <?php echo ( is_rtl() ) ? 'rtl' : ''; ?>">
	<?php
	$string = esc_html__( 'Settings Updated.', 'i_search' );
	$class  = 'settings-updated';
	do_action( 'isrc_admin_notice', $string, $class );
	?>
    <form method="POST" id="post">
        <input type="hidden" name="isrc_opt_page" value="content_builder">
        <input type="hidden" name="isrc_opt[lang]" value="<?php echo isrc_get_lang_admin(); ?>">
        <input type="hidden" name="isrc_opt[builder_data]" value="" id="builder_data">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content" style="position: relative;">
                    <div class="isrc-container cnt-general">
						<?php
						foreach ( $post_types as $key => $post_type ) {
							?>
                            <div class="sttngs-cont cnt-white-shadow">
                                <div class="isrc-inside">
                                    <h2><?php echo __( 'Post Type: ', 'i_search' ) . $post_type; ?></h2>

                                    <div class="isrc-metakeys-wrapper">
                                        <div class="metas-2-outer isrc-metas-source" data-pt="<?php echo $post_type; ?>">
                                            <div class="isrc-preview-preloader" style="display:none;"></div>
                                            <div class="isrc-metas-1">
                                                <span><?php _e( 'Meta Keys', 'i_search' ); ?></span>
                                                <ul class="posttypeul sortable-1">
													<?php
													/* get all meta keys by post type */
													$meta_keys = isrc_get_meta_keys_by_posttype( $post_type );
													if ( empty( $meta_keys ) || ! is_array( $meta_keys ) ) {
														$meta_keys = array();
													}
													foreach ( $meta_keys as $meta_key ) {
														/* is acf? */
														$label = $meta_key;
														if ( strpos( $meta_key, '__acf__' ) !== false ) {
															$temp     = explode( '__acf__', $meta_key );
															$meta_key = $temp[0];
															$label    = 'ACF: ' . $temp[1];
														}
														/* isrc extra keys */
														if ( strpos( $meta_key, '_cb_ex_mk_' ) !== false ) {
															$temp     = explode( '_cb_ex_mk_', $meta_key );
															$label    = 'isrc: ' . $temp[1];
														}
														?>
                                                        <li class="ui-state-default" data-type="meta_key" data-key="<?php echo $meta_key; ?>">
                                                            <span class="cb-name"><?php echo $label; ?></span>
                                                            <span class="cb-example-data"></span>
                                                            <span class="cb-open"></span>
                                                            <div class="clear"></div>
                                                            <div class="isrc-more-cnt">
                                                                <p class="description">
                                                                    <label for="label">
																		<?php _e( 'Label', 'i_search' ); ?><br>
                                                                        <input type="text"
                                                                               class="widefat edit-menu-item-title jslabel"
                                                                               name="ghost"
                                                                               value="">
                                                                    </label>
                                                                </p>
                                                                <p class="description">
                                                                    <label>
                                                                        <input name="ghost" type="checkbox" class="jsmobile">
																		<?php _e( 'Hide on mobile?', 'i_search' ); ?><br>
                                                                    </label>
                                                                </p>
                                                                <p class="description">
                                                                    <label>
                                                                        <input name="ghost" type="checkbox" class="jsclickable">
																		<?php _e( 'Clickable?', 'i_search' ); ?><br>
                                                                    </label>
                                                                </p>
                                                                <span class="cb-remove"><?php _e( 'Remove' ); ?></span>
                                                            </div>
                                                        </li>
														<?php
													}
													?>
                                                </ul>
                                            </div>

                                            <div class="isrc-metas-1">
                                                <span><?php _e( 'Taxonomies', 'i_search' ); ?></span>
                                                <ul class="posttypeul sortable-2">
													<?php
													/* get all taxonomies by post type */
													$taxonomies = get_object_taxonomies( $post_type );
													if ( empty( $taxonomies ) || ! is_array( $taxonomies ) ) {
														$taxonomies = array();
													}
													foreach ( $taxonomies as $taxonomy ) {
														?>
                                                        <li class="ui-state-default" data-type="taxonomy" data-key="<?php echo $taxonomy; ?>">
                                                            <span class="cb-name"><?php echo $taxonomy; ?></span>
                                                            <span class="cb-example-data"></span>
                                                            <span class="cb-open"></span>
                                                            <div class="clear"></div>
                                                            <div class="isrc-more-cnt">
                                                                <p class="description">
                                                                    <label for="label">
																		<?php _e( 'Label', 'i_search' ); ?><br>
                                                                        <input type="text"
                                                                               class="widefat edit-menu-item-title jslabel"
                                                                               name="ghost"
                                                                               value="">
                                                                    </label>
                                                                </p>
                                                                <p class="description">
                                                                    <label>
                                                                        <input name="ghost" type="checkbox" class="jsmobile">
																		<?php _e( 'Hide on mobile?', 'i_search' ); ?><br>
                                                                    </label>
                                                                </p>
                                                                <p class="description">
                                                                    <label>
                                                                        <input name="ghost" type="checkbox" class="jsclickable">
			                                                            <?php _e( 'Clickable?', 'i_search' ); ?><br>
                                                                    </label>
                                                                </p>
                                                                <span class="cb-remove"><?php _e( 'Remove' ); ?></span>
                                                            </div>
                                                        </li>
														<?php
													}
													?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="isrc-arrow-right"><i class="fas fa-sign-in-alt"></i></div>
                                        <div class="metas-2-outer isrc-flex-last">

                                            <div class="isrc-metas-1 isrc-metas-target" data-pt="<?php echo $post_type; ?>">
												<?php
												$js_fields = isrc_get_available_extra_fields();

												foreach ( $js_fields as $key_js => $val ) {
													?>
                                                    <div class="connector-single-outer">
                                                        <span class="isrc-connector-header"><?php echo $val['title']; ?>&nbsp;<?php echo $val['descr']; ?></span>
                                                        <ul class="posttypeul connectwith" data-jskey="<?php echo $key_js; ?>"></ul>
                                                    </div>
													<?php
												}
												?>

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
							<?php
						}
						?>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-reindex.php'; ?>
                        <div id="side-update" class="postbox">
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
									<?php wp_nonce_field( 'isrc_opt_cnt' ); ?>
                                </p>
                            </div>
                        </div>
	                    <?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-show-meta-data.php'; ?>
                        <?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-video-tutorial.php'; ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </form>
</div>
<!-- JS snippets comes here. We are in the admin. We are allowed to put our JS in the html directly. -->
<script>
    /* js variables */
    let isearch_current_page = 'isrc-opt-page';
    let isearch_current_tab = 'content_builder';
    let isrc_js_params = {
        video_tut_url: 'https://all4wp.net/redirect/isearch/tut-menu-settings-4/',
        builder_data: <?php echo json_encode( $builder_data ); ?>,
        label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
        label_label: '<?php _e( 'Label', 'i_search' ); ?>',
        label_remove: '<?php _e( 'Label', 'i_search' ); ?>',
        contentBuilderNonce: '<?php echo $isrc_nonce; ?>',
        select2Nonce: '<?php echo $select2_nonce; ?>',
        confirm_value: '<?php _e( 'This action cannot be undone', 'i_search' ); ?>',
        confirm_value2: '<?php _e( 'This action cannot be undone. It will alse delete all isearch taxonomy meta data.', 'i_search' ); ?>',
    };
</script>
