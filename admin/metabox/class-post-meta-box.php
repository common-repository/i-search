<?php

/**
 * i-Search Metabox Class
 *
 * This file is loaded only in admin.
 * The main class file to build the i-Search meta box with options to add extra search terms for posts.
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

class isrc_meta_box {

	public $isrc_options = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
		add_action( 'save_post', array( $this, 'isrc_save_meta_box_data' ) );
		add_action( 'attachment_updated', array( $this, 'isrc_save_meta_box_data' ), 1 );
		add_action( 'add_attachment', array( $this, 'isrc_save_meta_box_data' ), 1 );

	}

	/**
	 * Register the meta box in WP.
	 * We check here if this post type is in the allowed post types defined in the i-Search settings.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_meta_box() {
		global $isrc_opt;

		$isrc_options = $isrc_opt;

		if ( ! empty( $isrc_options['post_types'] ) && is_array( $isrc_options['post_types'] ) ) {
			$post_types = $isrc_options['post_types'];
			add_meta_box( 'isrc_meta_box', __( 'i-Search Options', 'i_search' ), array(
				$this,
				'build_meta_box'
			), $post_types, 'side', 'low' );
		}

	}

	/**
	 * Render the meta box html in posts.
	 *
	 * @param object | array $post The Post object.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return void    echo directly like in the WP codex
	 */
	public function build_meta_box( $post ) {
		global $isrc_opt;
		wp_enqueue_media();
		wp_enqueue_style( 'isrc-admin' );
		wp_enqueue_script( 'isrc-tagbox' );

		// make sure the form request comes from WordPress
		wp_nonce_field( basename( __FILE__ ), 'isrc_meta_box_nonce' );

		// retrieve the isrc_meta values as array
		$isrc_meta     = get_post_meta( $post->ID, '_isrc', true );
		$isrc_meta_all = get_post_meta( $post->ID, '_isrc_all', true );
		$you_have_img  = false;

		if ( empty( $isrc_meta ) ) {
			/* its the first time. Set defaults */
			$isrc_meta            = array();
			$isrc_meta['isrc_sh'] = 1;
		}

		/* is img enabled in settings? */
		$img_enabled = false;
		if ( isset( $isrc_opt['front']['img'] ) && $isrc_opt['front']['img'] ) {
			$img_enabled = true;
		}

		$searchwords_descr = '';
		if ( get_post_status() != 'publish' ) {
			$searchwords_descr = __( 'This post is not public. It will be not included in search results.', 'i_search' );
			$isrc_meta_all     = array();
		} elseif ( isset( $isrc_meta_all['excluded_by_tax_conn'] ) && $isrc_meta_all['excluded_by_tax_conn'] ) {
			$searchwords_descr = __( 'This post is excluded from suggestions because one of the connected terms are set to exclude in the i-Search advanced setting.', 'i_search' );
			$isrc_meta_all     = array();
		}

		// Get WordPress' media upload URL if img is enabled
		if ( $img_enabled ) {

			/* build image size name based on settings */
			$h               = $isrc_opt['front']['thumb_size']['h'];
			$w               = $isrc_opt['front']['thumb_size']['w'];
			$thumb_size_name = "isrc_thumb_{$h}_{$w}";
			$upload_link     = esc_url( get_upload_iframe_src( 'image', $post->ID ) );

			// See if there's a media id already saved as post meta

			if ( isset( $isrc_meta['isrc_img_id'] ) && ! empty( $isrc_meta['isrc_img_id'] ) ) {
				$isrc_img_id = $isrc_meta['isrc_img_id'];

				// Get the image src
				$isrc_img_src = wp_get_attachment_image_src( $isrc_img_id, $thumb_size_name );

				// For convenience, see if the array is valid
				$you_have_img = is_array( $isrc_img_src );
			} else {
				$you_have_img = false;
			}

		}
		?>

        <p>
            <label for="isrc_metabox"></label>
            <select name="isrc_sh" class="showhide isrc-hide-if-disabled" data-hide="hide-on-exclude" id="isrc_metabox">
                <option value="1"
					<?php ( isset( $isrc_meta['isrc_sh'] ) ) ? isrc_selected( $isrc_meta['isrc_sh'], '1' ) : __return_empty_string(); ?>>
					<?php _e( 'Include in search results', 'i_search' ); ?>
                </option>
                <option value="0"
					<?php ( isset( $isrc_meta['isrc_sh'] ) ) ? isrc_selected( $isrc_meta['isrc_sh'], '0' ) : __return_empty_string(); ?>>
					<?php _e( 'Exclude from search results', 'i_search' ); ?>
                </option>
            </select>
        </p>

        <div class="tagsdiv hide-on-exclude" id="isrc_terms">
            <h3><?php _e( 'Add extra search terms', 'i_search' ); ?></h3>
            <div class="jaxtag">
                <div class="nojs-tags hide-if-js">
                    <label for="tax-input-isrc_terms"><?php _e( 'Add new words', 'i_search' ); ?></label>
                    <p>
                        <textarea name="isrc_terms"
                                  rows="3" cols="20" class="the-tags" id="tax-input-isrc_terms"
                                  aria-describedby="new-tag-isrc_terms-desc">
                            <?php echo ( isset( $isrc_meta['isrc_terms'] ) ) ? isrc_implode( $isrc_meta['isrc_terms'] ) : ''; ?>
                        </textarea>
                    </p>
                </div>

                <div class="ajaxtag hide-if-no-js">
                    <label class="screen-reader-text"
                           for="new-tag-isrc_terms"><?php _e( 'Add new word', 'i_search' ); ?></label>
                    <p>
                        <input data-wp-taxonomy="isrc" type="text" id="new-tag-isrc_terms" name="newtag[isrc_terms]"
                               class="newtag form-input-tip ui-autocomplete-input" size="16" autocomplete="off"
                               aria-describedby="new-tag-isrc_terms-desc" value="" role="combobox"
                               aria-autocomplete="list" aria-expanded="false" aria-owns="ui-id-9999">
                        <input type="button" class="button tagadd" value="<?php _e( 'Add', 'i_search' ); ?>"></p>
                </div>
            </div>
            <div class="isrc_term_tag_wrap">
                <ul class="tagchecklist" role="list"></ul>
            </div>
            <p class="howto" id="new-tag-isrc_terms-desc">
				<?php _e( 'This post will shown if one of the below words are searched:', 'i_search' ); ?>
            </p>
			<?php if ( ! empty( $isrc_meta_all ) && empty( $searchwords_descr ) ) { ?>
                <div class="isrc_filtered">
                    <span>
                        <?php echo isrc_implode( $isrc_meta_all, '</span><span>' ); ?>
                    </span>
                </div>
                <p class="howto" id="new-tag-isrc_terms-desc">
					<?php _e( '* Update the post to see all included terms based on your i-Search settings.', 'i_search' ); ?>
                </p>
			<?php } elseif ( ! empty( $searchwords_descr ) ) { ?>
                <p class="howto" id="new-tag-isrc_terms-desc">
                    <strong style="color:red;">
						<?php echo $searchwords_descr; ?>
                    </strong>
                </p>

			<?php } ?>

            <div class="applyfilters">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php _e( 'Do not apply filters from settings. Include only manuel added terms.', 'i_search' ); ?></span>
                    </legend>
                    <label for="isrc_apply_filters">
                        <input name="isrc_no_filter" id="isrc_apply_filters" type="checkbox" class="" value="1"
							<?php echo ( isset( $isrc_meta['isrc_no_filter'] ) ) ? isrc_checked( $isrc_meta['isrc_no_filter'], '1', false ) : ''; ?>>
						<?php _e( 'Do not apply filters from settings. Include only manuel added terms.', 'i_search' ); ?>
                    </label>
                </fieldset>
            </div>

			<?php if ( $img_enabled ) : ?>
                <div class="isrc_extra_image hide-if-no-js">
                    <span style="margin-bottom:10px;display:block"><?php _e( 'Show different image in live search suggestions?', 'i_search' ); ?></span>

                    <div id="isrc_extra_image_container">
                        <div class="isrc-meta-img-container">
							<?php if ( $you_have_img ) : ?>
                                <img src="<?php echo $isrc_img_src[0] ?>" alt="" style="max-width:100%;"/>
							<?php endif; ?>
                        </div>

                        <!-- isrc add & remove image links -->
                        <p class="hide-if-no-js">
                            <a class="upload-isrc-custom-img <?php if ( $you_have_img ) {
								echo 'hidden';
							} ?>"
                               href="<?php echo $upload_link ?>">
								<?php _e( 'Set custom image', 'i_search' ) ?>
                            </a>
                            <a class="delete-custom-img <?php if ( ! $you_have_img ) {
								echo 'hidden';
							} ?>"
                               href="#">
								<?php _e( 'Remove this image', 'i_search' ) ?>
                            </a>
                        </p>

                        <!-- A hidden input to set and post the chosen image id -->
                        <input class="isrc_img_id" name="isrc_img_id" type="hidden"
                               value="<?php echo isset( $isrc_img_id ) ? esc_attr( $isrc_img_id ) : ''; ?>"/>
                    </div>

                </div>
			<?php endif; ?>

        </div>

        <!-- JS snippets comes here. We are in the admin. We are allowed to put our JS in the html directly. Because we handle also with php in JS-->
        <script>
            jQuery(function () {
                $("#new-tag-isrc_terms").on("autocompletecreate", function () {
                    $('#new-tag-isrc_terms').autocomplete("destroy");
                });
                jQuery('#isrc_terms').isrc_tagBox({});

                $(".isrc-hide-if-disabled").each(
                    function () {
                        let toHide = $(this).attr('data-hide');

                        if ($(this).val() === '1') {
                            $('.' + toHide).show();
                        } else {
                            $('.' + toHide).hide();
                        }

                    }
                );

                $('body').on('change', '.isrc-hide-if-disabled', function () {
                    let toHide = $(this).attr('data-hide');

                    if ($(this).val() === '1') {
                        $('.' + toHide).show();
                    } else {
                        $('.' + toHide).hide();
                    }
                });


            });

			<?php if( $img_enabled ) : ?>
            jQuery(function ($) {

                // Set all variables to be used in scope
                let frame,
                    metaBox = $('#isrc_meta_box.postbox'),
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
                        imgContainer.append('<img src="' + attachment.url + '" alt="" style="max-width:100%;"/>');
                        // Send the attachment id to our hidden input
                        imgIdInput.val(attachment.id);
                        // Hide the add image link
                        addImgLink.addClass('hidden');
                        // Unhide the remove image link
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
        </script>
		<?php
	}

	/**
	 * Save the metabox data.
	 *
	 * @param int $post_id The Post ID.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function isrc_save_meta_box_data( $post_id ) {

		// verify meta box nonce
		if ( ! isset( $_POST['isrc_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['isrc_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return false;
		}

		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Check the user's permissions.
		if ( ! current_user_can( ISRC_CAPABILITIES, $post_id ) ) {
			return false;
		}

		$isrc_meta = array();

		// store custom fields values
		if ( isset( $_POST['isrc_sh'] ) ) {
			$isrc_meta['isrc_sh'] = (int)$_POST['isrc_sh'];
		}

		if ( isset( $_POST['isrc_terms'] ) ) {
			$isrc_meta['isrc_terms'] = trim( $_POST['isrc_terms'] );
		}

		if ( isset( $_POST['isrc_no_filter'] ) ) {
			$isrc_meta['isrc_no_filter'] = (int)$_POST['isrc_no_filter'];
		}

		if ( isset( $_POST['isrc_img_id'] ) ) {
			$isrc_meta['isrc_img_id'] = (int) $_POST['isrc_img_id'];
		}

		update_post_meta( $post_id, '_isrc', $isrc_meta );

		return true;
	}

}

new isrc_meta_box();