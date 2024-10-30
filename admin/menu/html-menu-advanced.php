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
$isrc_nonce  = wp_create_nonce( 'isrc_settings' );
$options     = get_option( 'isrc_opt_adv_' . isrc_get_lang_admin() );
$delete_data = get_option( 'isrc_delete_data', false );
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';
$video_tutorial_title = __( 'Advanced Settings', 'i_search' );

?>
<div class="isrc-opt-page isrc-opt-page-advanced <?php echo ( is_rtl() ) ? 'rtl' : ''; ?>">
	<?php
	$string = esc_html__( 'Settings Updated.', 'i_search' );
	$class  = 'settings-updated';
	do_action( 'isrc_admin_notice', $string, $class );
	?>
    <form method="POST" id="post">
        <input type="hidden" name="isrc_opt_page" value="advanced">
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
											<?php _e( 'Exclude words from search algorithm', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_21' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="tagsdiv hide-on-exclude" id="isrc_bad_words">
                                            <div class="jaxtag">
                                                <div class="nojs-tags hide-if-js">
                                                    <label for="tax-input-isrc_bad_words">
														<?php _e( 'Add new word', 'i_search' ); ?>
                                                    </label>
                                                    <p>
                                                <textarea name="isrc_opt_adv[isrc_bad_words]" rows="3" cols="20"
                                                          class="the-tags" id="tax-input-isrc_bad_words"
                                                          aria-describedby="new-tag-isrc_bad_words-desc">
                                                    <?php echo ( isset( $options['isrc_bad_words'] ) ) ? $options['isrc_bad_words'] : ''; ?>
                                                </textarea>
                                                    </p>
                                                </div>
                                                <div class="ajaxtag hide-if-no-js">
                                                    <label class="screen-reader-text" for="new-tag-isrc_bad_words">
														<?php _e( 'Add new word', 'i_search' ); ?>
                                                    </label>
                                                    <p style="display: flex;margin-top:0">
                                                        <input data-wp-taxonomy="isrc"
                                                               type="text"
                                                               id="new-tag-isrc_bad_words"
                                                               name="isrc_bad_words_temp"
                                                               class="newtag form-input-tip ui-autocomplete-input" size="16"
                                                               autocomplete="off" aria-describedby="new-tag-isrc_bad_words-desc"
                                                               value="" role="combobox" aria-autocomplete="list"
                                                               aria-expanded="false" aria-owns="ui-id-9999">
                                                        <input type="button"
                                                               class="button tagadd"
                                                               value="<?php _e( 'Add new word', 'i_search' ); ?>">
                                                    </p>
                                                </div>
                                                <p class="howto" id="new-tag-isrc_bad_words-desc">
													<?php _e( 'Below words are ALWAYS excluded from the search algorithm', 'i_search' ); ?>
                                                </p>
                                            </div>
                                            <div class="isrc_term_tag_wrap">
                                                <ul class="tagchecklist" role="list">
                                                </ul>
                                            </div>
                                            <p class="howto">
												<?php _e( 'Wildcards are allowed. "some*word" will exclude for example "somebadword, some_bad_word, some_anything_word..."', 'i_search' ); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_10">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Exclude words from displaying', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_42' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="tagsdiv" id="isrc_hide_words">
                                            <div class="jaxtag">
                                                <div class="nojs-tags hide-if-js">
                                                    <label for="tax-input-isrc_hide_words">
														<?php _e( 'Add new word', 'i_search' ); ?>
                                                    </label>
                                                    <p>
                                                <textarea name="isrc_opt_adv[isrc_hide_words]" rows="3" cols="20"
                                                          class="the-tags" id="tax-input-isrc_hide_words"
                                                          aria-describedby="new-tag-isrc_hide_words-desc">
                                                    <?php echo ( isset( $options['isrc_hide_words'] ) ) ? $options['isrc_hide_words'] : ''; ?>
                                                </textarea>
                                                    </p>
                                                </div>
                                                <div class="ajaxtag hide-if-no-js">
                                                    <label class="screen-reader-text" for="new-tag-isrc_hide_words">
														<?php _e( 'Add new word', 'i_search' ); ?>
                                                    </label>
                                                    <p style="display: flex;margin-top:0">
                                                        <input data-wp-taxonomy="isrc"
                                                               type="text"
                                                               id="new-tag-isrc_hide_words"
                                                               name="isrc_hide_words_temp"
                                                               class="newtag form-input-tip ui-autocomplete-input" size="16"
                                                               autocomplete="off" aria-describedby="new-tag-isrc_hide_words-desc"
                                                               value="" role="combobox" aria-autocomplete="list"
                                                               aria-expanded="false" aria-owns="ui-id-9999">
                                                        <input type="button"
                                                               class="button tagadd"
                                                               value="<?php _e( 'Add new word', 'i_search' ); ?>">
                                                    </p>
                                                </div>
                                                <p class="howto" id="new-tag-isrc_bad_words-desc">
													<?php _e( 'Below words are ALWAYS excluded from displaying in the search suggestions', 'i_search' ); ?>
                                                </p>
                                            </div>
                                            <div class="isrc_term_tag_wrap">
                                                <ul class="tagchecklist" role="list">
                                                </ul>
                                            </div>
                                            <p class="howto">
												<?php _e( 'Wildcards are allowed. "some*word" will exclude for example "somebadword, some_bad_word, some_anything_word..."', 'i_search' ); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_14">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Replace search string', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_46' ); ?>
                                            <p class="description">
		                                        <?php _e( 'Can slow the search speed by 5%', 'i_search' ); ?>
                                            </p>
                                        </label>
                                    </div>

                                    <div class="isrc-input">
	                                        <?php echo isrc_pro_only_txt(); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_14_3">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Plural endings', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_47' ); ?>
                                            <p class="description">
		                                        <?php _e( 'Plural endings in your language. Only the ending letters. Can slow the search speed by 5%', 'i_search' ); ?>
                                            </p>
                                        </label>
                                    </div>

                                    <div class="isrc-input">
	                                    <?php echo isrc_pro_only_txt(); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div class="isrc-field isrc-settings" id="content_2">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Mobile & Tablet Settings', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_29' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											$mobile_fields = array(
												array(
													'fieldset_id' => 'isrc_mobile_hide_img',
													'input_name'  => 'isrc_opt_adv[mobile][hide_img]',
													'value'       => '1',
													'checked'     => isset( $options['mobile']['hide_img'] ),
													'text_string' => __( 'Hide images', 'i_search' )
												),
												array(
													'fieldset_id' => 'isrc_mobile_hide_badges',
													'input_name'  => 'isrc_opt_adv[mobile][hide_badges]',
													'value'       => '1',
													'checked'     => isset( $options['mobile']['hide_badges'] ),
													'text_string' => __( 'Hide badges', 'i_search' )
												),
												array(
													'fieldset_id' => 'isrc_mobile_hide_excerpt',
													'input_name'  => 'isrc_opt_adv[mobile][hide_excerpt]',
													'value'       => '1',
													'checked'     => isset( $options['mobile']['hide_excerpt'] ),
													'text_string' => __( 'Hide excerpt', 'i_search' )
												),
												array(
													'fieldset_id' => 'isrc_mobile_hide_price',
													'input_name'  => 'isrc_opt_adv[mobile][hide_price]',
													'value'       => '1',
													'checked'     => isset( $options['mobile']['hide_price'] ),
													'text_string' => __( 'Hide price', 'i_search' )
												),
												array(
													'fieldset_id' => 'isrc_mobile_hide_cats',
													'input_name'  => 'isrc_opt_adv[mobile][hide_cats]',
													'value'       => '1',
													'checked'     => isset( $options['mobile']['hide_cats'] ),
													'text_string' => __( 'Hide categories', 'i_search' )
												),

											);
											foreach ( $mobile_fields as $fieldset ) {
												isrc_render_fieldset_checkbox(
													$fieldset['fieldset_id'],
													$fieldset['input_name'],
													$fieldset['value'],
													'',
													'',
													$options,
													$fieldset['checked'],
													'',
													$fieldset['text_string'],
													'',
													false
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
											<?php _e( 'Include jQuery in front?', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_30' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_incl_jquery',
												"isrc_opt_adv[jquery]",
												'1',
												'',
												'',
												$options,
												isset( $options['jquery'] ),
												'',
												__( 'Yes include jQuery in front.', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_5">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Database actions', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_26' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="db-delete-btns">
                                            <button type="button" class="button-primary db_actions" id="delete_all_logs"
                                                    value="<?php _e( 'Empty all logs', 'i_search' ); ?>">
												<?php _e( 'Delete all logs', 'i_search' ); ?>
                                            </button>
                                            <button type="button" class="button-primary db_actions" id="delete_popularity"
                                                    value="<?php _e( 'Empty popularity index', 'i_search' ); ?>">
												<?php _e( 'Delete popularity index', 'i_search' ); ?>
                                            </button>
                                            <button type="button" class="button-primary db_actions" id="delete_all_isearch"
                                                    value="<?php _e( 'Empty all i-Search tables', 'i_search' ); ?>">
												<?php _e( 'Delete all i-Search indexes', 'i_search' ); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_6">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Delete data on plugin delete?', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_22' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_fieldset_checkbox(
												'isrc_delete_data',
												"isrc_opt_adv[delete_data]",
												'1',
												'',
												'',
												$options,
												$delete_data,
												'',
												__( 'Yes delete all the plugin settings on plugin delete.', 'i_search' ),
												'',
												false
											);
											?>
                                        </ul>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_6_1">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Spam protection limit', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_44' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <ul class="isrc-checkbox-list isrc-bl">
											<?php
											isrc_render_text_field(
												"isrc_opt_adv[ip_limit]",
												'ip_limit',
												( isset( $options['ip_limit'] ) ) ? $options['ip_limit'] : '5',
												'',
												'',
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
                                <div class="isrc-field isrc-settings" id="content_7">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Include Meta Keys in search algorithm', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_19' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="opt-metakeys">
                                            <div id="meta-keys"></div>
                                            <div id="ajx-preloader" class="isrc-ajx-pre meta-keys-pre">
                                                <div class="isrc_spinner">
                                                    <div class="rect1"></div>
                                                    <div class="rect2"></div>
                                                    <div class="rect3"></div>
                                                    <div class="rect4"></div>
                                                    <div class="rect5"></div>
                                                </div>
                                                <div class="isrc_pre_txt">
													<?php _e( 'Loading Meta Keys... (this may take a while)', 'i_search' ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_8">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Include Taxonomies in search algorithm', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_20' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="opt-taxonomies">
                                            <div id="taxo-keys"></div>
                                            <div id="ajx-preloader" class="isrc-ajx-pre taxo-keys-pre">
                                                <div class="isrc_spinner">
                                                    <div class="rect1"></div>
                                                    <div class="rect2"></div>
                                                    <div class="rect3"></div>
                                                    <div class="rect4"></div>
                                                    <div class="rect5"></div>
                                                </div>
                                                <div class="isrc_pre_txt">
													<?php _e( 'Loading Taxonomies... (this may take a while)', 'i_search' ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="isrc-field isrc-settings" id="content_12">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Exclude Tags from search algorithm', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_45' ); ?>
                                        </label>
                                    </div>

                                    <div class="isrc-input">
	                                    <?php echo isrc_pro_only_txt(); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-reindex.php'; ?>
                        <div id="side-update" class="postbox hide">
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
									<?php wp_nonce_field( 'isrc_opt_adv' ); ?>
                                </p>
                            </div>
                        </div>
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-video-tutorial.php'; ?>
						<?php include_once ISRC_PLUGIN_DIR . '/admin/menu/sidebar/sidebar-advanced-contents.php'; ?>
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
    let isearch_current_page = 'isrc-opt-page',
        isearch_current_tab = 'advanced_settings',
        isrc_def_nonce = '<?php echo wp_create_nonce( 'isrc_default_nonce' ); ?>',
        isrc_js_params = {
            video_tut_url: 'https://all4wp.net/redirect/isearch/tut-menu-settings-2/',
            label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
            label_label: '<?php _e( 'Label', 'i_search' ); ?>',
            pleaseSelectTaxonomyFirst: '<?php _e( 'Please select a Taxonomy first!', 'i_search' ); ?>',
            label_remove: '<?php _e( 'Label', 'i_search' ); ?>',
            advancedTabNonce: '<?php echo $isrc_nonce; ?>',
            confirm_value: '<?php _e( 'This action cannot be undone', 'i_search' ); ?>',
            confirm_value2: '<?php _e( 'This action cannot be undone. It will alse delete all isearch taxonomy meta data.', 'i_search' ); ?>',
        };
</script>
