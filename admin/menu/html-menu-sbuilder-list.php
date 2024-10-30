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
$isrc_nonce  = wp_create_nonce( 'isrc_settings' );
$menu_url    = html_entity_decode( menu_page_url( 'isrc-opt-page', false ) );
$add_new_url = html_entity_decode( add_query_arg( array( 'tab' => 'scbuilder-list', 'sub-tab' => 'add_new_sc' ), $menu_url ) );

global $sc_table;
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';
?>

<div class="wrap isrc-opt-page isrc-opt-page-analyze">
	<?php
	$string = esc_html__( 'Settings Updated.', 'i_search' );
	$class  = 'settings-updated';
	do_action( 'isrc_admin_notice', $string, $class );
	?>
    <form method="POST" id="post">
        <div id="poststuff" class="shortcode-list">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content" style="position: relative;">
                    <div class="isrc-container cnt-general">

                        <div class="listwrap">
                            <h1>
								<?php _e( 'Search Instances', 'i_search' ); ?>
								<?php isrc_render_help_icon( 'help_34' ); ?>
                            </h1>
                            <a href="<?php echo $add_new_url; ?>" class="shortcode-add-new button-all4wp button-blue-all4wp">Add New</a>
                            <div class="opt-not-found opt-wrap">
                                <div class="meta-box-sortables ui-sortable">
									<?php
									/* method views, prepare_items, display are defined in the main class where this file is included. */
									$sc_table->prepare_items();
									$sc_table->display();
									?>
                                </div>
                            </div>
                        </div>
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
        isearch_current_tab = 'scbuilder-list',
        isrc_js_params = {
            label_clone_sc: '<?php _e( 'Clone Instance', 'i_search' ); ?>',
            label_cloned_sc: '<?php _e( 'Cloning...', 'i_search' ); ?>',
            label_copy_code: '<?php _e( 'Copy Code', 'i_search' ); ?>',
            label_copied_code: '<?php _e( 'Copied', 'i_search' ); ?>',
            label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
            label_label: '<?php _e( 'Label', 'i_search' ); ?>',
            label_remove: '<?php _e( 'Label', 'i_search' ); ?>',
            advancedTabNonce: '<?php echo $isrc_nonce; ?>',
            confirm_value: '<?php _e( 'This action cannot be undone', 'i_search' ); ?>',
            confirm_value2: '<?php _e( 'This action cannot be undone. It will alse delete all isearch taxonomy meta data.', 'i_search' ); ?>',
        };
</script>
