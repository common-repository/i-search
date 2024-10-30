<?php

/*
 * i-Search Taxonomy meta box html file. Render the html.
 *
 * This file is loaded only in admin from the class-admin-main.php file in function: add_the_taxonomy_html
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
global $isrc_opt;

wp_enqueue_script( 'isrc-tagbox' );

$meta_id = $term->term_id;
$options = isrc_get_taxonomy_meta( $meta_id );

/* is img enabled in settings? */
$img_enabled = false;
if ( isset( $isrc_opt['front']['img'] ) && $isrc_opt['front']['img'] ) {
	$img_enabled = true;
}

// Get WordPress' media upload URL if img is enabled
if ( $img_enabled ) {

	/* build image size name based on settings */
	$h               = $isrc_opt['front']['thumb_size']['h'];
	$w               = $isrc_opt['front']['thumb_size']['w'];
	$thumb_size_name = "isrc_thumb_{$h}_{$w}";


	$upload_link = esc_url( get_upload_iframe_src( 'image', $term->term_id ) );

	// See if there's a media id already saved as post meta
	if ( isset( $options['isrc_img_id'] ) ) {
		$isrc_img_id = $options['isrc_img_id'];
		// Get the image src
		$isrc_img_src = wp_get_attachment_image_src( $isrc_img_id, $thumb_size_name );
		// For convenience, see if the array is valid
		$you_have_img = is_array( $isrc_img_src );
	} else {
		$isrc_img_id  = false;
		$you_have_img = false;
	}


}

?>
<style>
    .isrc_tax_form_outer {
        border: 1px solid #dedede;
        background: #fff;
        padding: 0 20px;
    }
</style>
<h2>i-Search</h2>
<div class="isrc_tax_form_outer">
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="isrc_exclude_from_Search">
					<?php _e( 'Exclude from search results?', 'i_search' ); ?>
                </label>
            </th>
            <td class="forminp forminp-text">
                <input class="isrc-hide-if-enabled"
                       data-hide="hide-src-rest"
                       name="i_src_tax[exclude_src]" id="isrc_exclude_from_Search"
                       type="checkbox" style="" value="yes"
                       class="" <?php ( isset( $options['exclude_src'] ) ) ? isrc_checked( $options['exclude_src'], '1' ) : __return_empty_string(); ?>
                >
                <span class="description">
          <?php _e( 'Yes. Exclude this taxonomy from search suggestions', 'i_search' ); ?>
          </span>
            </td>
        </tr>
        <tr valign="top" class="hide-src-rest">
            <th scope="row" class="titledesc"><label for="isrc_extra_terms">
					<?php _e( 'Add extra search terms', 'i_search' ); ?>
                </label>
            </th>
            <td class="forminp forminp-text">
                <div class="tagsdiv" id="isrc_extra_terms">
                    <div class="jaxtag">
                        <div class="nojs-tags hide-if-js">
                            <label for="tax-input-isrc_extra_terms">
								<?php _e( 'Add new term', 'i_search' ); ?>
                            </label>
                            <p>
                                <textarea name="i_src_tax[isrc_extra_terms]" rows="3" cols="20" class="the-tags"
                                          id="tax-input-isrc_extra_terms"
                                          aria-describedby="new-tag-isrc_extra_terms-desc"><?php echo isrc_implode( $options['isrc_extra_terms'] ); ?></textarea>
                            </p>
                        </div>
                        <div class="ajaxtag hide-if-no-js">
                            <label class="screen-reader-text" for="new-tag-isrc_extra_terms">
								<?php _e( 'Add new term', 'i_search' ); ?>
                            </label>
                            <p>
                                <input data-wp-taxonomy="isrc" type="text" id="new-tag-isrc_extra_terms"
                                       name="isrc_extra_terms_temp" class="newtag form-input-tip ui-autocomplete-input"
                                       size="16" autocomplete="off" aria-describedby="new-tag-isrc_extra_terms-desc"
                                       value="" role="combobox" aria-autocomplete="list" aria-expanded="false"
                                       aria-owns="ui-id-9999">
                                <input type="button" class="button tagadd"
                                       value="<?php _e( 'Add new term', 'i_search' ); ?>">
                            </p>
                        </div>
                        <p class="howto" id="new-tag-isrc_extra_terms-desc"></p>
                    </div>
                    <div class="isrc_term_tag_wrap">
                        <ul class="tagchecklist" role="list">
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
        <tr valign="top" class="hide-src-rest">
            <th scope="row" class="titledesc"><label for="isrc_extra_terms">
					<?php _e( 'Add image', 'i_search' ); ?>
                </label>
            </th>
            <td class="forminp forminp-text"><?php if ( $img_enabled ) : ?>
                    <div class="isrc_extra_image hide-if-no-js"> <span style="margin-bottom:10px;display:block">
            <?php _e( 'Show image in live search suggestions?', 'i_search' ); ?>
            </span>
                        <div id="isrc_extra_image_container">
                            <div class="isrc-meta-img-container">
								<?php if ( $you_have_img ) : ?>
                                    <img src="<?php echo $isrc_img_src[0] ?>" alt="" style="max-width:150px;"/>
								<?php endif; ?>
                            </div>

                            <!-- isrc add & remove image links -->
                            <p class="hide-if-no-js">
                                <a class="upload-isrc-custom-img <?php echo ( $you_have_img ) ? 'hidden' : ''; ?>" href="<?php echo $upload_link ?>">
									<?php _e( 'Set custom image', 'i_search' ) ?>
                                </a> <a class="delete-custom-img <?php echo ( ! $you_have_img ) ? 'hidden' : ''; ?>" href="#">
									<?php _e( 'Remove this image', 'i_search' ) ?>
                                </a></p>

                            <!-- A hidden input to set and post the chosen image id -->
                            <input class="isrc_img_id" name="i_src_tax[isrc_img_id]" type="hidden"
                                   value="<?php echo ( $you_have_img ) ? esc_attr( $isrc_img_id ) : ''; ?>"/>
                        </div>
                    </div>
				<?php endif; ?></td>
        </tr>
        </tbody>
    </table>
</div>

<!-- JS snippets comes here. We are in the admin. We are allowed to put our JS in the html directly. Because we handle also with php in JS-->

<script>
	<?php if( $img_enabled ) : ?>
    jQuery(function () {

        // Set all variables to be used in scope
        let frame,
            metaBox = jQuery('.isrc_tax_form_outer'),
            addImgLink = metaBox.find('.upload-isrc-custom-img'),
            delImgLink = metaBox.find('.delete-custom-img'),
            imgContainer = metaBox.find('.isrc-meta-img-container'),
            imgIdInput = metaBox.find('.isrc_img_id');

        // ADD IMAGE LINK
        addImgLink.on('click', function (event) {

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if (frame) {
                frame.open();
                return;
            }

            // Create a new media frame
            frame = wp.media({
                title: '<?php _e( 'Select or Upload Media For i-Search Image', 'i_search' ) ?>',
                button: {
                    text: '<?php _e( 'Use this media', 'i_search' ) ?>'
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });


            // When an image is selected in the media frame...
            frame.on('select', function () {

                // Get media attachment details from the frame state
                let attachment = frame.state().get('selection').first().toJSON();

                // Send the attachment URL to our custom image input field.
                imgContainer.append('<img src="' + attachment.url + '" alt="" style="max-width:150px;"/>');

                // Send the attachment id to our hidden input
                imgIdInput.val(attachment.id);

                // Hide the add image link
                addImgLink.addClass('hidden');

                // Show the remove image link
                delImgLink.removeClass('hidden');
            });

            // Finally, open the modal on click
            frame.open();
        });


        // DELETE IMAGE LINK
        delImgLink.on('click', function (event) {

            event.preventDefault();

            // Clear out the preview image
            imgContainer.html('');

            // Un-hide the add image link
            addImgLink.removeClass('hidden');

            // Hide the delete image link
            delImgLink.addClass('hidden');

            // Delete the image id from the hidden input
            imgIdInput.val('');

        });

    });
	<?php endif; ?>
    jQuery(function () {

        jQuery('#isrc_extra_terms').isrc_tagBox({tag_key: 'isrc_log_bad_words', ajax_save: false});

        jQuery(".isrc-hide-if-enabled").each(
            function () {
                let toHide = jQuery(this).attr('data-hide');
                if (this.checked) {
                    jQuery('.' + toHide).hide();
                } else {
                    jQuery('.' + toHide).show();
                }

            }
        );

        $('body').on('change', '.isrc-hide-if-enabled', function () {
            let toHide = jQuery(this).attr('data-hide');
            if (this.checked) {
                jQuery('.' + toHide).hide();
            } else {
                jQuery('.' + toHide).show();
            }
        });
    });
</script>