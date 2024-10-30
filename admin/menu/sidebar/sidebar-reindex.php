<?php

/*
 * i-Search html menu reindex sidebar
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

$hash                   = get_option( 'isrc_hash' );
$ajax_url               = admin_url( 'admin-ajax.php' );
$lang                   = isrc_get_lang_admin();
$need_reindex_full      = isrc_need_attention();
$need_reindex_partitial = false;
$source                 = ( $current_menu == 'content-builder' && ! $need_reindex_full ) ? 'cb' : 'all';
$query_arr              = array(
	'hash'   => $hash,
	'source' => $source,
	'action' => 'isrc_regenerate',
);

if ( $source == 'cb' && ! $need_reindex_full ) {
	$query_arr['lang'] = $lang;
}

$regenerate_url = esc_url( add_query_arg( $query_arr, $ajax_url ) );


/* content builder */
if ( $source == 'cb' && ! $need_reindex_full ) {
	$cb_options = get_option( 'isrc_opt_content_' . $lang, false );
	if ( $cb_options !== false ) {

		$cb_index_hash = get_option( 'isrc_cb_att_hash_ind_' . $lang, rand() );
		$cb_hash       = get_option( 'isrc_cb_att_hash_set_' . $lang, rand() );

		if ( $cb_hash != $cb_index_hash ) {
			$need_reindex_partitial = true;
		}
	}
}

if ( $need_reindex_partitial || $need_reindex_full ) {
	$css = 'bgred';
	if ( $source == 'cb' && ! $need_reindex_full ) {
		$txt = __( 'Partial re-index. You need to re-index your site!', 'i_search' );
	} else {
		$txt = __( 'Important settings changed. You need to re-index your site!', 'i_search' );
	}
} else {
	$css = '';
	$txt = __( 'Reindexing not needed. If you added some filters maybe you need a reindexing.', 'i_search' );
}


?>
<div id="side-1" class="postbox <?php echo $css; ?>">
    <h2 class="hndle ui-sortable-handle"> <span>
    <?php _e( 'Re-index', 'i_search' ); ?>
    </span></h2>
    <div class="inside">
        <div class="need_att">
            <p>
				<?php echo $txt; ?>
            </p>
            <a class="button-secondary  w100p tcenter" href="<?php echo $regenerate_url; ?>"
               target="_<?php echo md5( $ajax_url ); ?>reindex">
				<?php _e( 'Re-index Now', 'i_search' ); ?>
            </a></div>
    </div>
</div>
