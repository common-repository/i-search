<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Set globals only for frontend
 */
require_once ISRC_PLUGIN_DIR . '/front/front-helpers.php';

$isrc_opt = get_option( 'isrc_opt_' . isrc_get_lang_front() );
$isrc_opt_adv = get_option( 'isrc_opt_adv_' . isrc_get_lang_front() );
$isrc_log_bad_words = get_option( 'isrc_log_bad_words_' . isrc_get_lang_front() );
$isrc_content_builder = get_option( 'isrc_opt_content_' . isrc_get_lang_front() );



/*
 * Include files only for front
 */
require_once ISRC_PLUGIN_DIR . '/front/class-front-main.php';
