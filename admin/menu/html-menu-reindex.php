<?php
/*
 * Regenerate (Re-index) screen.
 *
 * This file is as an ajax call.
 * We can not use WP's enqueue scripts/styles function. It is not available in ajax calls.
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

if ( ! i_src_security_check() ) {
	wp_die( 'You need a higher level of permission.', 403 );
}

$total_status = 'not_finished';

global $limit;

if ( isset( $_GET['limit'] ) ) {
	$limit = esc_attr( wp_unslash( $_GET['limit'] ) );
} else {
	$limit = 50;
}

if ( isset( $_GET['offset'] ) ) {
	$offset      = esc_attr( wp_unslash( $_GET['offset'] ) );
	$isfirst_log = false;
} else {
	$offset      = '0';
	$isfirst_log = false;
}

if ( isset( $_GET['action_type'] ) ) {
	$action_type = esc_attr( wp_unslash( $_GET['action_type'] ) );
} else {
	$action_type = 'first_start';
}

if ( isset( $_GET['source'] ) ) {
	$source = esc_attr( wp_unslash( $_GET['source'] ) );
} else {
	$source = 'all';
}

/* get all post types from settings */
$available_opt_langs = isrc_get_lang_codes();
$post_types          = array();

if ( $source == 'all' ) {
	foreach ( $available_opt_langs as $lang_code ) {
		$options_general = get_option( 'isrc_opt_' . $lang_code );
		if ( isset( $options_general['include_in_suggestions'] ) ) {
			$post_types_temp = $options_general['include_in_suggestions'];
			$post_types      = array_merge_recursive( $post_types, $post_types_temp );
		}
	}
}

if ( $source == 'cb' ) {

	if ( isset( $_GET['lang'] ) ) {
		$lang = esc_attr( wp_unslash( $_GET['lang'] ) );
	} else {
		$lang = isrc_get_lang_admin();
	}
	$cb_data_all = get_option( 'isrc_opt_content_' . $lang );
	if ( isset( $cb_data_all['builder_data'] ) ) {
		foreach ( $cb_data_all['builder_data'] as $pt => $ptval ) {
			$post_types['post_types'][] = $pt;
		}
	}

}

/* unique */
if ( isset( $post_types['post_types'] ) ) {
	$post_types['post_types'] = array_values( array_unique( $post_types['post_types'] ) );
}

if ( isset( $post_types['taxonomies'] ) ) {
	$post_types['taxonomies'] = array_values( array_unique( $post_types['taxonomies'] ) );
}

if ( empty( $post_types ) ) {
	_e( 'No post types are selected in the settings', 'i_search' );
	exit;
}

/* fill temp with post ids */
if ( $action_type == 'first_start' ) {
	fill_temp_ids( $post_types, $source );
}

$total = isrc_get_total_count();
if ( $total > 0 ) {
	$percent = calc_percent( $total );
} else {
	$percent = 0;
}

$pids = isrc_get_post_ids( $limit );

$check_type = 'reindex';

if ( empty( $pids ) && $check_type == 'reindex' && $source == 'all' ) {
	$check_type = 'logs';
	/* update index hash so that the index status is up to date */
	foreach ( $available_opt_langs as $lang_code ) {
		$att_hash = get_option( 'isrc_att_hash_set_' . $lang_code );
		update_option( 'isrc_att_hash_ind_' . $lang_code, $att_hash );
		/* also update cb hash if ull reindexed */
		$cb_hash = get_option( 'isrc_cb_att_hash_set_' . $lang );
		update_option( 'isrc_cb_att_hash_ind_' . $this->lang, $cb_hash );

	}

}

if ( empty( $pids ) && $check_type == 'reindex' && $source == 'cb' ) {
	$check_type   = 'logs';
	$total_status = 'finished';

	/* update cb hash so that the index status is up to date */

	if ( isset( $_GET['lang'] ) ) {
		$lang = esc_attr( wp_unslash( $_GET['lang'] ) );
	} else {
		$lang = isrc_get_lang_admin();
	}
	/* update cb hash only if source is cb */
	$cb_hash = get_option( 'isrc_cb_att_hash_set_' . $lang );
	update_option( 'isrc_cb_att_hash_ind_' . $lang, $cb_hash );

}

$generated_ids = array();

if ( ! empty( $pids ) ) {

	foreach ( $pids as $key => $val ) {

		$type_of_temp = $val['type'];

		if ( $type_of_temp == 'post_type' ) {
			/* update for POST TYPES only */
			$generated_ids[ $key ]['id']    = $val['post_id'];
			$generated_ids[ $key ]['title'] = get_the_title( $val['post_id'] );
			update_post_isrc( $val['post_id'] );
			isrc_delete_temp_id( $val['post_id'] );
		} else {
			/* update for taxonomies only */
			$taxonomy_id                    = (int) $val['post_id'];
			$term                           = get_term( $taxonomy_id );
			$generated_ids[ $key ]['id']    = $val['post_id'];
			$generated_ids[ $key ]['title'] = $term->name;
			isrc_update_taxonomy_meta( $val['post_id'] );
			isrc_delete_temp_id( $val['post_id'] );
		}

	}
}

if ( $check_type == 'logs' && $source != 'cb' ) {

	/* re-check log data */
	$log_ids = isrc_recheck_logs( $limit, $offset );

	if ( ! empty( $log_ids ) ) {
		foreach ( $log_ids as $key => $val ) {
			$generated_ids[ $key ]['id']    = $val['id'];
			$generated_ids[ $key ]['title'] = $val['src_query'];
			if ( isset( $val['status'] ) ) {
				$generated_ids[ $key ]['status'] = $val['status'];
			}
		}
	} else {
		$total_status = 'finished';
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="<?php echo ISRC_PLUGIN_URL ?>/admin/menu/css/images/favicon.png">
    <title>i-Search Reindex</title>

    <!-- Bootstrap core CSS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          crossorigin="anonymous">

    <!-- We can not use WP enqueue style function it is not available in ajax calls -->
    <style>
        .sidebar-nav li a, .sidebar-nav li a:active, .sidebar-nav li a:focus {
            text-decoration: none
        }

        body {
            overflow-x: hidden
        }

        #wrapper {
            padding-left: 0;
            -webkit-transition: all .5s ease;
            -moz-transition: all .5s ease;
            -o-transition: all .5s ease;
            transition: all .5s ease
        }

        #wrapper.toggled {
            padding-left: 250px
        }

        #sidebar-wrapper {
            z-index: 1000;
            position: fixed;
            left: 250px;
            width: 0;
            height: 100%;
            margin-left: -250px;
            overflow-y: auto;
            background: #000;
            -webkit-transition: all .5s ease;
            -moz-transition: all .5s ease;
            -o-transition: all .5s ease;
            transition: all .5s ease;
            padding: 10px
        }

        #wrapper.toggled #sidebar-wrapper {
            width: 250px
        }

        #page-content-wrapper {
            width: 100%;
            position: absolute;
            padding: 15px
        }

        #wrapper.toggled #page-content-wrapper {
            position: absolute;
            margin-right: -250px
        }

        .sidebar-nav {
            position: absolute;
            top: 0;
            width: 250px;
            margin: 0;
            padding: 0;
            list-style: none
        }

        .sidebar-nav li {
            text-indent: 20px;
            line-height: 40px
        }

        .sidebar-nav li a {
            display: block;
            color: #999
        }

        .sidebar-nav li a:hover {
            text-decoration: none;
            color: #fff;
            background: rgba(255, 255, 255, .2)
        }

        .sidebar-nav > .sidebar-brand a {
            color: #999
        }

        .sidebar-nav > .sidebar-brand a:hover {
            color: #fff;
            background: 0 0
        }

        @media (min-width: 768px) {
            #wrapper {
                padding-left: 0
            }

            #wrapper.toggled {
                padding-left: 250px
            }

            #sidebar-wrapper {
                width: 0
            }

            #wrapper.toggled #sidebar-wrapper {
                width: 250px
            }

            #page-content-wrapper {
                padding: 20px;
                position: relative
            }

            #wrapper.toggled #page-content-wrapper {
                position: relative;
                margin-right: 0
            }
        }

        .txtc {
            text-align: center
        }

        #sidebar-wrapper .totals {
            display: block;
            color: #fff;
            font-size: 1.5em;
            text-align: center;
            font-weight: 600
        }

        #sidebar-wrapper .totals-descr {
            display: block;
            color: #fff;
            font-size: 1em;
            text-align: center
        }

        .totals-widget {
            margin-top: 35px
        }

        .mt20, .sidebtns {
            margin-top: 20px
        }

        .hide {
            display: none
        }

        .overlapper {
            background: rgba(0, 0, 0, .5);
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 99
        }

        .footer.side {
            color: #fff;
            position: absolute;
            bottom: 5px;
            font-size: .9em
        }

        .info {
            color: #fff
        }

        .block1 {
            margin: auto;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .13);
            background: #fff;
            padding: 24px
        }

        body {
            background: #f1f1f1;
            font-family: Roboto, sans-serif
        }

        .page-header h1 {
            font-size: 1.4em
        }

        #log-viewer {
            background: #fff;
            border: 1px solid #e5e5e5;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            padding: 5px 20px;
            -webkit-font-smoothing: subpixel-antialiased
        }

        #log-viewer pre {
            overflow: hidden;
            overflow-y: auto;
            height: 500px
        }

        .mt20 {
            margin-top: 20px
        }

        .infoabbort {
            text-align: center;
            display: block;
            color: red
        }

        body.finished .block1 {
            display: none
        }

        .block3 {
            padding: 40px
        }

        body.finished .show-if-finished {
            display: block
        }

        body.finished .hide-if-finished {
            display: none
        }
    </style>

    <script>
        let offset = <?php echo (int) $offset; ?>,
            limit = <?php echo $limit; ?>,
            isfirst_log = <?php echo $isfirst_log ? 'true' : 'false'; ?>;

        function UpdateQueryString(key, value, url) {
            if (!url) url = window.location.href;
            let re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                hash;

            if (re.test(url)) {
                if (typeof value !== 'undefined' && value !== null)
                    return url.replace(re, '$1' + key + "=" + value + '$2$3');
                else {
                    hash = url.split('#');
                    url = hash[0].replace(re, '$1$3').replace(/([&])$/, '');
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                        url += '#' + hash[1];
                    return url;
                }
            } else {
                if (typeof value !== 'undefined' && value !== null) {
                    let separator = url.indexOf('?') !== -1 ? '&' : '?';
                    hash = url.split('#');
                    url = hash[0] + separator + key + '=' + value;
                    if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                        url += '#' + hash[1];
                    return url;
                } else
                    return url;
            }
        }

        function redirecttocontinue() {
            let url = window.location.href,
                offset_new,
                redirect = UpdateQueryString('action_type', 'continue', url),
                limit = $('#atonce').val();
            redirect = UpdateQueryString('check_type', check_type, redirect);
            if (limit) {
                redirect = UpdateQueryString('limit', limit, redirect);
            }

            if (check_type === 'logs') {
                if (!isfirst_log) {
                    offset_new = Math.round(parseInt(offset) + parseInt(limit));
                } else {
                    offset_new = '0';
                }
                redirect = UpdateQueryString('offset', offset_new, redirect);
            }
            window.location.href = redirect;
        }

        let generated_ids = <?php echo json_encode( $generated_ids ); ?>,
            check_type = '<?php echo $check_type; ?>',
            total_status = '<?php echo $total_status; ?>';

    </script>
</head>
<body class="check_type_<?php echo $check_type; ?> <?php echo $total_status; ?>">
<div id="wrapper" class="toggled">

    <!-- Sidebar -->
    <div id="sidebar-wrapper" class="hide-if-finished">
        <div class="totals-widget"><span class="totals"><?php echo $total; ?></span> <span class="totals-descr">
      <?php _e( 'Posts waiting for reindexing.', 'i_search' ); ?>
      </span></div>
        <div class="mt20 input-group input-group-sm mb-3">
            <div class="input-group-prepend"> <span class="input-group-text">
        <?php _e( 'Index', 'i_search' ); ?>
        </span></div>
            <label for="atonce"></label>
            <input id="atonce" type="number" class="form-control" value="<?php echo $limit; ?>"
                   aria-label="At once index">
            <div class="input-group-append"> <span class="input-group-text">
        <?php _e( 'At Once', 'i_search' ); ?>
        </span></div>
        </div>
        <div class="info">
            <p>
				<?php _e( 'Based on your Server capacities, you can increase the value of indexing at once. If your value is too high you will get a server timeout error. Restart the reindexing process and decrease value.', 'i_search' ); ?>
            </p>
        </div>
        <div class="sidebtns">
            <button type="button" class="btn btn-warning btn-block" id="pausebtn">
				<?php _e( 'Pause', 'i_search' ); ?>
            </button>
            <button type="button" class="btn btn-info btn-block hide" id="resumebtn">
				<?php _e( 'Resume', 'i_search' ); ?>
            </button>
        </div>
        <div class="footer side"> i-Search <?php echo ISRC_VER; ?> </div>
    </div>
    <!-- /#sidebar-wrapper -->
    <div class="overlapper hide"></div>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 .bg-white block1">
            <div class="page-header">
                <h1>
					<?php if ( $check_type == 'reindex' ) : ?>
						<?php _e( 'Reindexing...', 'i_search' ); ?>
                        <small>
							<?php _e( 'Please do not close this window.', 'i_search' ); ?>
                        </small>
					<?php endif; ?>
					<?php if ( $check_type == 'logs' ) : ?>
						<?php _e( 'Checking Logs...', 'i_search' ); ?>
                        <small>
							<?php _e( 'Please do not close this window.', 'i_search' ); ?>
                        </small>
					<?php endif; ?>
                </h1>
            </div>

            <div class="progress-wrap">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                         aria-valuenow="<?php echo $percent; ?>" aria-valuemin="0" aria-valuemax="100"
                         style="width:<?php echo $percent; ?>%">
                    </div>
                </div>
            </div>

            <div id="log-viewer">
                <pre></pre>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 .bg-white block1 mt20 hide-if-finished">
            <h5 class="txtc"> <?php printf( __( 'This window will refresh in <span id="time" class="secs">%s</span> Seconds', 'i_search' ), 5 ); ?> </h5>
            <span class="infoabbort">
      <?php _e( 'Do not cancel this process. Let i-Search finish indexing.', 'i_search' ); ?>
      </span></div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 .bg-white block1 block3 mt20 block1 show-if-finished hide">
            <h5 class="txtc">
				<?php _e( 'Done. Please close this window. And reload the i-Search settings page.', 'i_search' ); ?>
            </h5>
        </div>
    </div>
    <!-- /#page-content-wrapper -->

</div>
<!-- /#wrapper -->
<!-- Bootstrap core JavaScript -->
<!-- This is a standalone html. WP script functions are not available here. Include external libraries. -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script>
    let timeToRedirect = 6,
        timersCount = 0,
        log_atonce = 0,
        pause = false; //is timer paused

    if (total_status !== 'finished') {
        countTimers(timeToRedirect);
    }

    function filllog() {
        if (check_type === 'reindex') {
            filllog_reindex();
        }

        if (check_type === 'logs') {
            filllog_logs();
        }
    }

    function filllog_logs() {
        let atonce;
        if (log_atonce < 1) {
            atonce = Math.round(generated_ids.length / timeToRedirect);
            if (atonce < 7) {
                atonce = 7;
            }
            log_atonce = atonce;
        } else {
            atonce = log_atonce;
        }
        let logs = generated_ids.slice(0, atonce);
        generated_ids = generated_ids.slice(atonce, generated_ids.length);
        $(logs).each(function (key, val) {
            let logtxt = 'ID: ' + val.id + ' - Searching for: ' + val.title + ' - ' + val.status + '\n';
            setTimeout(function () {
                $('#log-viewer pre').prepend(logtxt);
            }, 30 * (key + 1));
        })
    }

    function filllog_reindex() {
        let atonce;
        if (log_atonce < 1) {
            atonce = Math.round(generated_ids.length / timeToRedirect);
            if (atonce < 7) {
                atonce = 7;
            }
            log_atonce = atonce;
        } else {
            atonce = log_atonce;
        }
        let logs = generated_ids.slice(0, atonce);
        generated_ids = generated_ids.slice(atonce, generated_ids.length);
        $(logs).each(function (key, val) {
            let logtxt = 'ID: ' + val.id + ' Title: ' + val.title + '\n';
            setTimeout(function () {
                $('#log-viewer pre').prepend(logtxt);
            }, 30 * (key + 1));
        })
    }


    function countTimers(secsto) {
        timersCount++;

        let count = secsto,
            counter = setInterval(timer, 1000);

        function timer() {
            if (!pause) { //do something if not paused
                count = count - 1;
                filllog();
                if (count < 0) {
                    clearInterval(counter);
                    $('#atonce').attr('disabled', true);
                    redirecttocontinue();
                    return false;
                }
                document.getElementById("time").innerHTML = count;
            }
        }

    }

    function paused() {
        $("#pausebtn").addClass("hide");
        $(".progress-bar").removeClass("progress-bar-animated");
        $("#resumebtn").removeClass("hide");
        $(".overlapper").removeClass("hide");
        pause = true;
    }

    function resumed() {
        $("#resumebtn").addClass("hide");
        $(".progress-bar").addClass("progress-bar-animated");
        $("#pausebtn").removeClass("hide");
        $(".overlapper").addClass("hide");
        pause = false;
    }

    $("#pausebtn").on('click', function (e) {
        e.preventDefault();
        paused();
    });

    $("#resumebtn").on('click', function (e) {
        e.preventDefault();
        resumed();
    });
    $("#atonce").on('focus', function () {
        paused();
    });

</script>
</body>
</html>