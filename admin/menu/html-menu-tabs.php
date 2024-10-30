<?php

/*
 * i-Search html menu TABS in the top of the menu.
 *
 * This file will be loaded with include_once in the html-menu php files.
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
	exit; // Exit if accessed directly.
}

$menu_url            = html_entity_decode( menu_page_url( 'isrc-opt-page', false ) );
$general_url         = html_entity_decode( add_query_arg( 'tab', 'general', $menu_url ) );
$advanced_url        = html_entity_decode( add_query_arg( 'tab', 'advanced', $menu_url ) );
$analyze_url         = html_entity_decode( add_query_arg( 'tab', 'analyze', $menu_url ) );
$scbuilder_url       = html_entity_decode( add_query_arg( 'tab', 'scbuilder-list', $menu_url ) );
$content_builder_url = html_entity_decode( add_query_arg( 'tab', 'content-builder', $menu_url ) );

if ( isset( $_GET['tab'] ) ) {
	$current_menu = esc_attr( wp_unslash( $_GET['tab'] ) );
} else {
	$current_menu = 'general';
}
?>

<h1 class="isrc-tab-handler all4wp-nav-container" id="isrc-tabs">
    <a class="nav-tab all4wp-nav-item <?php echo ( $current_menu == 'general' ) ? 'nav-tab-active' : ''; ?>" id="settings-tab" href="<?php echo $general_url; ?>">
        <div class="isrc_tab_txt_wrap"><i class="fas fa-tasks"></i>
            <span class="tab_txt"><?php _e( 'Settings', 'i_search' ); ?></span>
        </div>
    </a>
    <a class="nav-tab all4wp-nav-item <?php echo ( $current_menu == 'advanced' ) ? 'nav-tab-active' : ''; ?>" id="advanced-tab" href="<?php echo $advanced_url; ?>">
        <div class="isrc_tab_txt_wrap"><i class="fas fa-user-md"></i>
            <span class="tab_txt"><?php _e( 'Advanced Settings', 'i_search' ); ?></span>
        </div>
    </a>
    <a class="nav-tab all4wp-nav-item <?php echo ( $current_menu == 'analyze' ) ? 'nav-tab-active' : ''; ?>" id="report-tab" href="<?php echo $analyze_url; ?>">
        <div class="isrc_tab_txt_wrap">
            <i class="fas fa-chart-bar"></i>
            <span class="tab_txt"><?php _e( 'Search Analysis', 'i_search' ); ?></span>
        </div>
    </a>
    <a class="nav-tab all4wp-nav-item <?php echo ( $current_menu == 'scbuilder-list' ) ? 'nav-tab-active' : ''; ?>" id="report-tab" href="<?php echo $scbuilder_url; ?>">
        <div class="isrc_tab_txt_wrap">
            <i class="fas fa-code"></i>
            <span class="tab_txt"><?php _e( 'Instance Builder', 'i_search' ); ?></span>
        </div>
    </a>
    <a class="nav-tab all4wp-nav-item <?php echo ( $current_menu == 'content-builder' ) ? 'nav-tab-active' : ''; ?>" id="report-tab" href="<?php echo $content_builder_url; ?>">
        <div class="isrc_tab_txt_wrap">
            <i class="fas fa-terminal"></i>
            <span class="tab_txt"><?php _e( 'Content Builder', 'i_search' ); ?></span>
        </div>
    </a>
    <a class="nav-tab all4wp-nav-item" href="https://all4wp.net/redirect/isearch/docu/" target="_blank">
        <div class="isrc_tab_txt_wrap">
            <i class="fas fa-file-alt"></i>
            <span class="tab_txt"><?php _e( 'Documentation', 'i_search' ); ?></span>
        </div>
    </a>
</h1>
