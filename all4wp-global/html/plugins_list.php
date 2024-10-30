<?php
/*
 * all4wp plugins listing
 *
 * This file is loaded only in admin.
 *
 *
 * LICENSE:
 *
 * @author     all4wp.net <all4wp.net@gmail.com>
 * @copyright  2018 by all4wp
 * @since      2.0.0
 */

$url    = "https://all4wp.net/plugins";
$locale = get_locale();
$url    = add_query_arg( array( 'type' => 'json', 'lang' => $locale ), $url );


$response = wp_remote_get( $url );
if ( is_array( $response ) ) {
	$header = $response['headers']; // array of http header lines
	$body   = $response['body']; // use the content
	$json   = json_decode( $body, true );

	if ( isset( $json['plugins'] ) && ! empty( $json['plugins'] ) ) {
		$plugins_remote = $json['plugins'];
	} else {
		$plugins_remote = false;
	}

	if ( $plugins_remote ) {

		?>

        <div class="all-4-wp-plugins-list wp-filter"><span>all4wp Plugins</span></div>
        <div class="list-for-plugins">
			<?php

			/* check if plugin is intalled */
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			foreach ( $plugins_remote as $plugins ) {
				$plugin_check    = $plugins['install_check'];
				$url             = $plugins['url'];
				$slug            = $plugins['slug'];
				$name            = $plugins['name'];
				$icon            = $plugins['icon'];
				$description     = $plugins['description'];
				$is_insalled_txt = ' <small class="is_installed"><i>(already installed)</i></small>';
				$menu_slug       = $plugins['menu_slug'];
				$is_installed    = false;

				if ( is_plugin_active( $plugin_check ) ) {
					//plugin is activated
					$activecss    = 'activated';
					$is_installed = true;
				} else {
					$activecss       = 'not_activated';
					$is_insalled_txt = '';
				}

				?>
                <div class="all4wp_plugin_list plugin-card plugin-card-<?php echo $slug; ?> <?php echo $activecss; ?>">
                    <div data-url="<?php echo $url; ?>" class="plugin-card-top">
                        <div class="name column-name">
                            <h3><a href="<?php echo $url; ?>" target="_blank"> <?php echo $name; ?> <img
                                            src="<?php echo $icon; ?>" class="plugin-icon" alt="">
                                </a> <?php echo $is_insalled_txt; ?> </h3>
                        </div>
                        <div class="action-links"></div>
                        <div class="desc column-description">
                            <p><?php echo $description; ?></p>
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        <div class="configure_url">
							<?php if ( $is_installed ) : ?>
                                <a class="install-now button"
                                   href="<?php menu_page_url( $menu_slug, true ); ?>">Settings</a>
							<?php endif; ?>
                            <a class="install-now button" target="_blank" href="<?php echo $url; ?>">More Info</a></div>
                    </div>
                </div>
				<?php
			}
			?>
        </div>
	<?php }

}

?>
