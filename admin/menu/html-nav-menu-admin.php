<?php
/*
 * Admin WP menu page html
 */
$shortcodes = get_the_shortcodes();
?>

<div id="posttype-isearch-endpoints" class="posttypediv">
    <div id="tabs-panel-isearch-endpoints" class="tabs-panel tabs-panel-active">
        <ul id="isearch-endpoints-checklist" class="categorychecklist form-no-clear">
			<?php
			$i = - 1;
			foreach ( $shortcodes as $key => $value ) :
				?>
                <li>
                    <label class="menu-item-title">
                        <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]"
                               value="<?php echo esc_attr( $i ); ?>"/> <?php echo esc_html( $value['title'] ); ?>
                    </label>
                    <input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="isearch"/>
                    <input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="i-Search: <?php echo esc_html( $value['title'] ); ?>"/>
                    <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" value="isrc-sc-menu_<?php echo esc_html( $value['id'] ); ?>"/>
                    <input type="hidden" class="menu-item-xfn" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-xfn]" value="<?php echo esc_html( $value['id'] ); ?>"/>
                </li>
				<?php
				$i --;
			endforeach;
			?>
        </ul>
    </div>
    <p class="button-controls">
		<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'i_search' ); ?>" name="add-post-type-menu-item"
                            id="submit-posttype-isearch-endpoints"><?php esc_html_e( 'Add to menu', 'i_search' ); ?></button>
					<span class="spinner"></span>
		</span>
    </p>
</div>