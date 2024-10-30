<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * Preview file for the shortcode builder
 * This file is a standalone file.
 */

/*
* hash for security check
*/
$hash = get_option( 'isrc_hash' );
$preview_data = get_option( 'isrc_previewdata' );

if ( ! isset( $_GET['hash'] ) || $_GET['hash'] != $hash ) {
	wp_die( 'Wrong hash key', 403 );
}

check_ajax_referer( 'isrc_settings', 'nonce' );

$body_bg = ( isset( $preview_data['isrc_sc_opt']['preview']['bg_color'] ) && ! empty( $preview_data['isrc_sc_opt']['preview']['bg_color'] ) ) ? $preview_data['isrc_sc_opt']['preview']['bg_color'] : '#f3f3f2';
?>
<!DOCTYPE html>
<html>
<head>
    <!-- First add the elements you need in <head>; then last, add: -->
	<?php
	wp_head();
	if ( isset( $preview_data['isrc_sc_opt']['input_open_style'] ) && $preview_data['isrc_sc_opt']['input_open_style'] == 'body' ||
	     isset( $preview_data['isrc_sc_opt']['input_disp_style'] ) && $preview_data['isrc_sc_opt']['input_disp_style'] == 'theme_sw_full'  ||
	     isset( $preview_data['isrc_sc_opt']['inp_sc_p'] ) && $preview_data['isrc_sc_opt']['inp_sc_p'] == 'fix' ) {
		?>
        <style>
            body {
                min-height: 600px;
            }
        </style>
		<?php
	}
	?>

</head>
<body style="padding: 10px;background: <?php echo $body_bg ?>">
<?php
/*
 * We are in preview mode for the shortcode builder
 * All data is coming from $preview_data
 * extract it and build a shortcode
 * This is a shortcode builder. Use REAL shortcode string instead of array to test the functionality.
 */
if ( isset( $preview_data['isrc_sc_opt'] ) && is_array( $preview_data['isrc_sc_opt'] ) ) {
	/* build the shortcode string */
	$sc_atts          = $preview_data['isrc_sc_opt'];
	$shortcode_string = '[isrc_ajax_search is_preview=1 ';
	/* unset before */
	unset( $sc_atts['title'] );
	foreach ( $sc_atts as $key => $val ) {

		/* change array to string */
		if ( $key == 'ph_adverts' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'exc_max_words' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'colors' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( $key == 'cb_flds' ) {
			$val = base64_encode( json_encode( $val ) );
		}

		if ( is_array( $val ) ) {
			/* change array in to acceptable shortcode array */
			$temp = array();
			foreach ( $val as $key2 => $val2 ) {
				if ( filter_var( $val2, FILTER_VALIDATE_BOOLEAN ) ) {
					$temp[] = $key2;
				}
			}

			if ( ! empty( $temp ) ) {
				$val = implode( ',', $temp );
			}
		}

		if ( is_array( $val ) ) {
			/* something went wrong we have still array as $val. Skip this value to avoid errors */
			continue;
		}

		/* convert false string to 0/1 */
		if ( $val === 'false' ) {
			$val = '0';
		} elseif ( $val === 'true' ) {
			$val = '1';
		}

		$shortcode_string .= "{$key}='{$val}' ";
	}
	$shortcode_string .= ']';
} else {
	/*
	* no variables send from url.
	* use default shortcode.
	 */
	$shortcode_string = "[isrc_ajax_search is_preview=1]";
}
echo do_shortcode( $shortcode_string ); ?>
<script>
    jQuery(function ($) {
        $('.isrc-ajaxsearchform,.search-form').submit(function () {
            if ('parentIFrame' in window) {
                window.parentIFrame.sendMessage('msg_01');
            }
            return false;
        });
    });
    var iFrameResizer = {
        messageCallback: function (message) {
            if (message.func === 'sh_preloader') {
                jQuery(window).trigger('sh_preloader');
            }
            if (message.func === 'btn_bg_color') {
                let color = message.color;
                jQuery('.isrc-searchsubmit').css('background-color', color);
            }
            if (message.func === 'btn_icon_bg_color') {
                let color = message.color;
                jQuery('.isrc-sbm-svg').css('background-color', color);
            }
            if (message.func === 'body_bg_color') {
                let color = message.color;
                if(!color) {
                    color = '#f3f3f2';
                }
                    jQuery('body').css('background', color);
            }
        }
    }
</script>
<?php wp_footer(); ?>

</body>
</html>
