<?php

/**
 * i-Search html menu for the analyses TAB
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
$bad_tags   = get_option( 'isrc_log_bad_words_' . isrc_get_lang_admin() );
global $logs_table;
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-header.php';
include_once ISRC_PLUGIN_DIR . '/admin/menu/html-menu-tabs.php';
?>
<div class="isrc_menuloadingDiv" id="loadingDiv">
    <div class="isrc_ajx_spinner">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
        <div class="rect4"></div>
        <div class="rect5"></div>
    </div>
</div>
<div class="wrap isrc-opt-page isrc-opt-page-analyze <?php echo ( is_rtl() ) ? 'rtl' : ''; ?>">
	<?php
	$string = esc_html__( 'Settings Updated.', 'i_search' );
	$class  = 'settings-updated';
	do_action( 'isrc_admin_notice', $string, $class );
	?>
    <form method="POST" id="post">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-1">
                <div id="post-body-content" style="position: relative;">
                    <div class="isrc-container cnt-general">
                        <div class="sttngs-cont cnt-white-shadow">
                            <div class="isrc-inside">

                                <div class="isrc-field isrc-settings" id="content_1">
                                    <div class="isrc-label">
                                        <label for="isrc-settings-lbl">
											<?php _e( 'Exclude words from logging', 'i_search' ); ?>
											<?php isrc_render_help_icon( 'help_23' ); ?>
                                        </label>
                                    </div>
                                    <div class="isrc-input">
                                        <div class="opt-excludes">
                                            <div class="tagsdiv" id="isrc_log_bad_words">
                                                <div class="jaxtag">
                                                    <div class="nojs-tags hide-if-js">
                                                        <label for="tax-input-isrc_log_bad_words">
															<?php _e( 'Add new word', 'i_search' ); ?>
                                                        </label>
                                                        <p>
                                                        <textarea name="isrc_opt_adv[isrc_log_bad_words]" rows="3" cols="20"
                                                                  class="the-tags" id="tax-input-isrc_log_bad_words"
                                                                  aria-describedby="new-tag-isrc_log_bad_words-desc"><?php echo isrc_implode( $bad_tags ); ?>
                                                        </textarea>
                                                        </p>
                                                    </div>
                                                    <div class="ajaxtag hide-if-no-js">
                                                        <label class="screen-reader-text" for="new-tag-isrc_log_bad_words">
															<?php _e( 'Add new word', 'i_search' ); ?>
                                                        </label>
                                                        <p style="display: flex;margin-top:0">
                                                            <input data-wp-taxonomy="isrc" type="text"
                                                                   id="new-tag-isrc_log_bad_words"
                                                                   name="isrc_log_bads_temp"
                                                                   class="newtag form-input-tip ui-autocomplete-input" size="16"
                                                                   autocomplete="off"
                                                                   aria-describedby="new-tag-isrc_log_bad_words-desc"
                                                                   value="" role="combobox" aria-autocomplete="list"
                                                                   aria-expanded="false" aria-owns="ui-id-9999">
                                                            <input type="button" class="button tagadd"
                                                                   value="<?php _e( 'Add new word', 'i_search' ); ?>">
                                                        </p>
                                                    </div>
                                                    <p class="howto" id="new-tag-isrc_log_bad_words-desc">
														<?php _e( 'Below words are ALWAYS excluded from logging.', 'i_search' ); ?>
                                                        <br>
														<?php _e( 'Wildcards ( <strong>*</strong> ) are allowed. <code>asd*</code> will exclude "asd..." or <code>*asd*</code> will exclude " ...asd... "', 'i_search' ); ?>
                                                    </p>
                                                </div>
                                                <div class="isrc_term_tag_wrap">
                                                    <ul class="tagchecklist" role="list">
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                        <h3 class="lighth3">
							<?php _e( 'Not found search queries', 'i_search' ); ?>
							<?php isrc_render_help_icon( 'help_24' ); ?>
                            <div class="analyse-vid" id="isrc_vid_tutorial"><i class="fas fa-video"></i><?php _e( 'Video tutorial', 'i_search' ); ?></div>
                            <div class="clear"></div>
                        </h3>
                        <div class="opt-not-found opt-wrap">
                            <div class="meta-box-sortables ui-sortable">
								<?php
								/* method views, prepare_items, display are defined in the main class where this file is included. */
								$logs_table->views();
								$logs_table->prepare_items();
								$logs_table->display();
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

<div class="remodal modal_meaning_edit" data-remodal-id="modal_meaning_edit">
    <button data-remodal-action="close" class="remodal-close"></button>
    <h3>
		<?php _e( 'Select action for:', 'i_search' ); ?>
        <span class="rm_title"></span>
    </h3>
    <div class="modal_content">
        <input type="hidden" value="" name="data-log-id">
        <input type="hidden" value="" name="data-src_query">
        <table class="form-table">
            <tbody>
            <tr valign="top">
                <td class="forminp forminp-text">
                    <label for="isrc_front_search_hook_class">
						<?php _e( 'Select action:', 'i_search' ); ?>
                    </label>
                </td>
                <td class="forminp forminp-text" align="center">
                    <select id="isrc_front_search_hook_class" name="action_for_meaning"
                            class="action_for_meaning w100p">
                        <option value="block">
							<?php _e( 'Add to bad words', 'i_search' ); ?>
                        </option>
                        <option value="dym">
							<?php _e( 'Enter a "Did You Mean" string (Only in PRO version)', 'i_search' ); ?>
                        </option>
                        <option value="addtopost">
							<?php _e( 'Add this string to a post/taxonomy as an extra search term', 'i_search' ); ?>
                        </option>
                        <option value="delete">
							<?php _e( 'Delete', 'i_search' ); ?>
                        </option>
                    </select>
            </tr>
            <tr valign="top" class="hideonchange didyoumean hide">
                <th scope="row" class="titledesc">
                    <label for="isrc_front_search_input_label">
						<?php _e( 'Did you mean string:', 'i_search' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text dym_for_instance">
                    <input name="didyoumeanstring"
                           id="didyoumeanstring_id"
                           type="text"
                           style=""
                           value=""
                           class="w100p"
                           placeholder="">
                </td>
            </tr>
            <tr valign="top" class="hideonchange addtopost hide">
                <th scope="row" class="titledesc">
                    <label for="posts_id_add">
						<?php _e( 'Select Posts/taxonomies:', 'i_search' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <select id="posts_id_add" class="select2_add_to_post_modal"
                            style="width:100%"
                            name="isrc_add_to_post_tax[]" multiple="multiple">
                    </select>
                </td>
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

<!-- JS snippets comes here. We are in the admin. We are allowed to put our JS in the html directly. Because we handle also with php in JS-->

<script>
    let isrc_nonce = '<?php echo $isrc_nonce; ?>',
        loading,
        current_log_id,
        current_remodal;

    (function ($) {

        table_fn = {

            init: function () {


                // Pagination links, sortable link
                $(document.body).on('click', '.edit_meaning', function (e) {
                    // We don't want to actually follow these links
                    e.preventDefault();

                    let data_id = $(this).attr("data-id");
                    if (!data_id) {
                        return false;
                    }

                    let query_str = $(".src_query[data-id='" + data_id + "']").html();
                    let did_you_mean_string = $(".meaning_txt[data-id='" + data_id + "']").html();

                    let data = {
                        remodal_id: 'modal_meaning_edit',
                        remodal_func: 'update_remodal',
                        log_id: data_id,
                        log_action: 'edit_meaning_action',
                        query_str: query_str,
                        did_you_mean_string: did_you_mean_string,
                    };

                    table_fn.update_remodal(data);

                });

                $(document.body).on('change', '.action_for_meaning', function () {
                    $('.remodal .hideonchange').addClass('hide');
                    if (this.value === 'dym') {
                        let instance_data_name = 'instance_info_for_log_id_' + current_log_id;
                        let instance_data = window[instance_data_name];
                        $('.remodal .didyoumean').removeClass('hide');
                    }
                    if (this.value === 'addtopost') {
                        $('.remodal .addtopost').removeClass('hide');
                    }
                });

                $(document.body).on('closed', '.modal_meaning_edit', function () {
                    let remodal_id = 'modal_meaning_edit';

                    $(".remodal[data-remodal-id='" + remodal_id + "'] .rm_title").html('');
                    $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='data-log-id']").val('0');
                    $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='data-src_query']").val('');
                    $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='didyoumeanstring']").val('');
                    $('.action_for_meaning').val('block').trigger('change');
                    $('.select2_add_to_post_modal').val('').trigger('change');
                });

                $(document.body).on('click', ".remodal[data-remodal-id='modal_meaning_edit'] .remodal-confirm", function () {

                    let log_id = $(".remodal[data-remodal-id='modal_meaning_edit'] input[name='data-log-id']").val(),
                        selection = $(".remodal[data-remodal-id='modal_meaning_edit'] .action_for_meaning").val(),
                        value = $(".remodal[data-remodal-id='modal_meaning_edit'] input[name='data-src_query']").val(),
                        did_you_mean_string = $(".remodal[data-remodal-id='modal_meaning_edit'] #didyoumeanstring_id").val();

                    let data = {
                        remodal_id: 'modal_meaning_edit',
                        remodal_func: 'update_remodal',
                        action_after_response: 'close_and_update',
                        log_id: log_id,
                        log_action: 'edit_meaning_action',
                        selection: selection,
                        did_you_mean_string: did_you_mean_string,
                        value: value,
                    };

                    if (selection === 'addtopost') {
                        /* add select2 extra data */
                        let select2data = $('.select2_add_to_post_modal').select2('data');
                        let select2tosend = [];
                        $.each(select2data, function (key, val) {
                            let newdata = {id: val.id, post_type: val.post_type, type: val.type};
                            select2tosend.push(newdata);
                        });
                        data.selections = select2tosend;
                    }

                    table_fn.update(data);
                })

            },

            update_remodal: function (data) {
                let remodal_id = data.remodal_id;
                current_log_id = data.log_id;
                $(".remodal[data-remodal-id='" + remodal_id + "'] .rm_title").html(data.query_str);
                $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='data-log-id']").val(data.log_id);
                $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='data-src_query']").val(data.query_str);
                $(".remodal[data-remodal-id='" + remodal_id + "'] input[name='didyoumeanstring']").val(data.did_you_mean_string);

                current_remodal = jQuery('[data-remodal-id=' + remodal_id + ']').remodal({hashTracking: false});
                current_remodal.open();

            },


            update: function (data) {

                $.ajax({
                    beforeSend: function () {
                        loading.show();
                    },
                    url: ajaxurl,
                    dataType: 'json',
                    method: 'POST',
                    // Add action and nonce to our collected data
                    data: $.extend(
                        {
                            _isrc_table_nonce: $('#_isrc_table_nonce').val(),
                            action: '_ajax_fetch_remodal',
                        },
                        data
                    ),
                    // Handle the successful result
                    success: function (response) {
                        loading.hide();
                        if (response.success === true) {
                            /* if add to bad word is selected */
                            if (data.selection === 'block') {
                                $('#new-tag-isrc_log_bad_words').val(data.value);
                                $('.button.tagadd').click();
                            }
                            /* close remodal and reload page */
                            current_remodal.close();
                            table_fn.refreshPage();
                            alertify.success('OK');
                        } else {
                            table_fn.showAlert(response);
                        }

                    }
                });
            },
            refreshPage: function () {
                let data = {
                    paged: parseInt($('input[name=paged]').val()) || '1',
                    order: $('input[name=order]').val() || 'desc',
                    orderby: $('input[name=orderby]').val() || 'time'
                };
                list.update(data);
            },
            showAlert: function (data) {
                let alerttxt;
                if (typeof data.msg === 'undefined') {
                    alerttxt = 'Sorry but something went wrong';
                } else {
                    alerttxt = data.msg;
                }
                alertify.error(alerttxt);
            },
            show_meaning_fields: function (data_id) {

                let wrapper = $(".editmeaning[data-id='" + data_id + "']");
                wrapper.find('.meaning_txt').addClass('hide');
                wrapper.find('.inputs_wrap').removeClass('hide');
            },

            hide_meaning_fields: function (data_id) {

                let wrapper = $(".editmeaning[data-id='" + data_id + "']");
                wrapper.find('.meaning_txt').removeClass('hide');
                wrapper.find('.inputs_wrap').addClass('hide');
            },

            __query: function (query, variable) {

                let vars = query.split("&");
                for (let i = 0; i < vars.length; i++) {
                    let pair = vars[i].split("=");
                    if (pair[0] === variable)
                        return pair[1];
                }
                return false;
            },
        };

// Show time!
        table_fn.init();

    })(jQuery);

    (function ($) {

        list = {

            init: function () {

                // This will have its utility when dealing with the page number input
                let timer;
                let delay = 500;

                // Pagination links, sortable link
                $('.tablenav-pages a, .manage-column.sortable a, .manage-column.sorted a').on('click', function (e) {
                    // We don't want to actually follow these links
                    e.preventDefault();
                    // Simple way: use the URL to extract our needed variables
                    let query = this.search.substring(1);
                    let data = {
                        paged: list.__query(query, 'paged') || '1',
                        order: list.__query(query, 'order') || 'desc',
                        orderby: list.__query(query, 'orderby') || 'time'
                    };
                    list.updateOrder(data);
                    list.update(data);
                });

                // Page number input
                $('input[name=paged]').on('keyup', function (e) {

                    // If user hit enter, we don't want to submit the form
                    // We don't preventDefault() for all keys because it would
                    // also prevent to get the page number!
                    if (13 === e.which)
                        e.preventDefault();

                    // This time we fetch the variables in inputs
                    let data = {
                        paged: parseInt($('input[name=paged]').val()) || '1',
                        order: $('input[name=order]').val() || 'desc',
                        orderby: $('input[name=orderby]').val() || 'time'
                    };

                    // Now the timer comes to use: we wait half a second after
                    // the user stopped typing to actually send the call. If
                    // we don't, the keyup event will trigger instantly and
                    // thus may cause duplicate calls before sending the intended
                    // value
                    window.clearTimeout(timer);
                    timer = window.setTimeout(function () {
                        list.update(data);
                    }, delay);
                });
            },
            updateOrder: function (data) {
                let order = data.order;
                let orderby = data.orderby;
                $('input[name=order]').val(order);
                $('input[name=orderby]').val(orderby);
            },
            update: function (data) {
                $.ajax({
                    // /wp-admin/admin-ajax.php
                    url: ajaxurl,
                    // Add action and nonce to our collected data
                    data: $.extend(
                        {
                            _ajax_custom_list_nonce: $('#_ajax_custom_list_nonce').val(),
                            action: 'isrc_fetch_analyse_list',
                            status: $('#status_fltr').val(),
                            current_url: window.location.href,
                        },
                        data
                    ),
                    // Handle the successful result
                    success: function (response) {

                        // Add the requested rows
                        if (response.rows.length)
                            $('#the-list').html(response.rows);
                        // Update column headers for sorting
                        if (response.column_headers.length)
                            $('thead tr, tfoot tr').html(response.column_headers);
                        // Update pagination for navigation
                        if (response.pagination.bottom.length)
                            $('.tablenav.top .tablenav-pages').html($(response.pagination.top).html());
                        if (response.pagination.top.length)
                            $('.tablenav.bottom .tablenav-pages').html($(response.pagination.bottom).html());
                        if (response.views_top.length)
                            $('.subsubsub').html($(response.views_top).html());

                        // Init back our event handlers
                        list.init();
                    }
                });
            },
            __query: function (query, variable) {

                let vars = query.split("&");
                for (let i = 0; i < vars.length; i++) {
                    let pair = vars[i].split("=");
                    if (pair[0] === variable)
                        return pair[1];
                }
                return false;
            },
        };

// Show time!
        list.init();

    })(jQuery);
    /* js variables */
    let isearch_current_page = 'isrc-opt-page';
    let isearch_current_tab = 'search_analyze';
    let isrc_js_params = {
        video_tut_url: 'https://all4wp.net/redirect/isearch/tut-menu-settings-analyze/',
        label_taxonomy: '<?php _e( 'Taxonomy', 'i_search' ); ?>',
        label_label: '<?php _e( 'Label', 'i_search' ); ?>',
        label_remove: '<?php _e( 'Label', 'i_search' ); ?>',
        advancedTabNonce: '<?php echo $isrc_nonce; ?>',
        confirm_value: '<?php _e( 'This action cannot be undone', 'i_search' ); ?>',
        confirm_value2: '<?php _e( 'This action cannot be undone. It will alse delete all isearch taxonomy meta data.', 'i_search' ); ?>',
    };

</script> 
