<div class="isrc-css-builder-wrap isrc-field isrc-settings _closed">
    <div class="isrc-label toplabel">
        <label for="isrc-settings-css">
			<?php _e( 'Color/Style Builder', 'i_search' ); ?>
        </label>
        <div class="isrc-upd-prv"><span><?php _e( 'Click to update preview', 'i_search' ); ?></span></div>
        <span class="isrc-tab-arrow" style="top:-4px"></span>
    </div>

    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Input height in px', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][inp_h]',
				'isrc_inp_h',
				( isset( $settings_for_check['colors']['inp_h'] ) ) ? $settings_for_check['colors']['inp_h'] : '40',
				'',
				'',
				'',
				true,
				'number',
				1,
				1000,
				1
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Input outer border radius in px', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][border_r]',
				'isrc_border_r',
				( isset( $settings_for_check['colors']['border_r'] ) ) ? $settings_for_check['colors']['border_r'] : '0',
				'',
				'',
				'',
				true,
				'number',
				0,
				100,
				1
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Input wrapper min. width', 'i_search' ); ?>
                <p class="description">
					<?php _e( 'Min width of the input wrapper in px or %', 'i_search' ); ?>
                </p>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][wrap_min_w]',
				'isrc_wrap_min_w',
				( isset( $settings_for_check['colors']['wrap_min_w'] ) ) ? $settings_for_check['colors']['wrap_min_w'] : '',
				'',
				'',
				'',
				true,
				'text'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Input text/icon color', 'i_search' ); ?>
                <p class="description">
					<?php _e( 'If search input style is text or icon.', 'i_search' ); ?>
                </p>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][inp_ic_cl]',
				'inp_txt_ic_clr',
				( isset( $settings_for_check['colors']['inp_ic_cl'] ) ) ? $settings_for_check['colors']['inp_ic_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Overlay background color', 'i_search' ); ?>
                <p class="description">
					<?php _e( 'If search input is opening on click.', 'i_search' ); ?>
                </p>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][overl_bg]',
				'overl_bg',
				( isset( $settings_for_check['colors']['overl_bg'] ) ) ? $settings_for_check['colors']['overl_bg'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Overlay icon close color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][overl_ic_bg]',
				'overl_ic_bg',
				( isset( $settings_for_check['colors']['overl_ic_bg'] ) ) ? $settings_for_check['colors']['overl_ic_bg'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Trending searches label color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ts_lb]',
				'',
				( isset( $settings_for_check['colors']['ts_lb'] ) ) ? $settings_for_check['colors']['ts_lb'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Trending searches tag color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ts_tag]',
				'',
				( isset( $settings_for_check['colors']['ts_tag'] ) ) ? $settings_for_check['colors']['ts_tag'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Trending searches tag hover color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ts_tag_hv]',
				'',
				( isset( $settings_for_check['colors']['ts_tag_hv'] ) ) ? $settings_for_check['colors']['ts_tag_hv'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Trending searches distance in px', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ts_mt]',
				'isrc_ts_mt',
				( isset( $settings_for_check['colors']['ts_mt'] ) ) ? $settings_for_check['colors']['ts_mt'] : '0',
				'',
				'',
				'',
				true,
				'number',
				0,
				500,
				1
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Trending searches alignment', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
	        <?php
	        isrc_render_select_field(
		        'isrc_sc_opt[colors][ts_al]',
		        'isrc_ts_alignment',
		        '',
		        '',
		        '',
		        '',
		        array(
			        'l' => __( 'Left', 'i_search' ),
			        'r' => __( 'Right', 'i_search' ),
		        ),
		        $settings_for_check['colors']['ts_al']
	        );
	        ?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Search input background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][input_bg]',
				'',
				( isset( $settings_for_check['colors']['input_bg'] ) ) ? $settings_for_check['colors']['input_bg'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Search input border color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][input_bc]',
				'',
				( isset( $settings_for_check['colors']['input_bc'] ) ) ? $settings_for_check['colors']['input_bc'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Search input border color on focus', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][input_bc_f]',
				'',
				( isset( $settings_for_check['colors']['input_bc_f'] ) ) ? $settings_for_check['colors']['input_bc_f'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Search input left label color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][input_ll]',
				'',
				( isset( $settings_for_check['colors']['input_ll'] ) ) ? $settings_for_check['colors']['input_ll'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Placeholder text color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][plchldr_cl]',
				'',
				( isset( $settings_for_check['colors']['plchldr_cl'] ) ) ? $settings_for_check['colors']['plchldr_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>

        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Placeholder advertising text color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][plchldr_adv_cl]',
				'',
				( isset( $settings_for_check['colors']['plchldr_adv_cl'] ) ) ? $settings_for_check['colors']['plchldr_adv_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>

        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Search input text color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][inpt_cl]',
				'',
				( isset( $settings_for_check['colors']['inpt_cl'] ) ) ? $settings_for_check['colors']['inpt_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Spinner color', 'i_search' ); ?>
                <span class="sh_preloader"><?php _e( 'Show/Hide Spinner', 'i_search' ); ?></span>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][spin_cl]',
				'',
				( isset( $settings_for_check['colors']['spin_cl'] ) ) ? $settings_for_check['colors']['spin_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Clear button color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][xclose_cl]',
				'',
				( isset( $settings_for_check['colors']['xclose_cl'] ) ) ? $settings_for_check['colors']['xclose_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-lbl">
				<?php _e( 'Submit button background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[subm_color]',
				'isrc_submit_color',
				( isset( $settings_for_check['subm_color'] ) ) ? $settings_for_check['subm_color'] : '',
				'',
				'',
				'color-picker'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-lbl">
				<?php _e( 'Submit button icon color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[subm_icon_color]',
				'subm_icon_color',
				( isset( $settings_for_check['subm_icon_color'] ) ) ? $settings_for_check['subm_icon_color'] : '#ffffff',
				'',
				'',
				'color-picker'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Submit button text color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][subm_txt_cl]',
				'',
				( isset( $settings_for_check['colors']['subm_txt_cl'] ) ) ? $settings_for_check['colors']['subm_txt_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Underlined color', 'i_search' ); ?>
                <p class="description">
					<?php _e( 'If search input style is underlined.', 'i_search' ); ?>
                </p>
            </label>
        </div>
        <div class="isrc-input">
            <ul class="isrc-checkbox-list isrc-bl isrc-mtb5px">
				<?php
				isrc_render_fieldset_checkbox(
					'underl_ed',
					"isrc_sc_opt[colors][underl_ed]",
					'1',
					'',
					'',
					$settings_for_check,
					isset( $settings_for_check['colors']['underl_ed'] ),
					'',
					__( 'Disable underline', 'i_search' ),
					'',
					false
				);
				?>
            </ul>
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][underl_cl]',
				'',
				( isset( $settings_for_check['colors']['underl_cl'] ) ) ? $settings_for_check['colors']['underl_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Post type divider background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ptdivider_bg]',
				'',
				( isset( $settings_for_check['colors']['ptdivider_bg'] ) ) ? $settings_for_check['colors']['ptdivider_bg'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Matched word background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][match_bg]',
				'',
				( isset( $settings_for_check['colors']['match_bg'] ) ) ? $settings_for_check['colors']['match_bg'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Matched word color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][match_cl]',
				'',
				( isset( $settings_for_check['colors']['match_cl'] ) ) ? $settings_for_check['colors']['match_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Post type divider font color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][ptdivider_cl]',
				'',
				( isset( $settings_for_check['colors']['ptdivider_cl'] ) ) ? $settings_for_check['colors']['ptdivider_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Selected tab top border color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_sel_top_bc]',
				'',
				( isset( $settings_for_check['colors']['tab_sel_top_bc'] ) ) ? $settings_for_check['colors']['tab_sel_top_bc'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Selected tab background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_sel_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['tab_sel_bg_cl'] ) ) ? $settings_for_check['colors']['tab_sel_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Selected tab background (on hover) color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_sel_bg_hvr_cl]',
				'',
				( isset( $settings_for_check['colors']['tab_sel_bg_hvr_cl'] ) ) ? $settings_for_check['colors']['tab_sel_bg_hvr_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Not selected tab background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_nsel_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['tab_nsel_bg_cl'] ) ) ? $settings_for_check['colors']['tab_nsel_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Not selected tab background (on hover) color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_nsel_hvr_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['tab_nsel_hvr_bg_cl'] ) ) ? $settings_for_check['colors']['tab_nsel_hvr_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Tabs font color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][tab_cl]',
				'',
				( isset( $settings_for_check['colors']['tab_cl'] ) ) ? $settings_for_check['colors']['tab_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Suggestions container border radius in px', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][cont_r]',
				'isrc_cont_r',
				( isset( $settings_for_check['colors']['cont_r'] ) ) ? $settings_for_check['colors']['cont_r'] : '0',
				'',
				'',
				'',
				true,
				'number',
				0,
				100,
				1
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Suggestions container border color', 'i_search' ); ?>
                <p class="description">
					<?php _e( 'Suggestions container.', 'i_search' ); ?>
                </p>
            </label>
        </div>
        <div class="isrc-input">
            <ul class="isrc-checkbox-list isrc-bl isrc-mtb5px">
				<?php
				isrc_render_fieldset_checkbox(
					'cont_bc_ed',
					"isrc_sc_opt[colors][cont_bc_ed]",
					'1',
					'',
					'',
					$settings_for_check,
					isset( $settings_for_check['colors']['cont_bc_ed'] ),
					'',
					__( 'Disable border', 'i_search' ),
					'',
					false
				);
				?>
            </ul>
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][cont_bc]',
				'',
				( isset( $settings_for_check['colors']['cont_bc'] ) ) ? $settings_for_check['colors']['cont_bc'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Container background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][cont_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['cont_bg_cl'] ) ) ? $settings_for_check['colors']['cont_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Active suggestion background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][sug_act_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['sug_act_bg_cl'] ) ) ? $settings_for_check['colors']['sug_act_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Suggestion border color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][sug_bd_cl]',
				'',
				( isset( $settings_for_check['colors']['sug_bd_cl'] ) ) ? $settings_for_check['colors']['sug_bd_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Suggestion font color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][sug_fnt_cl]',
				'',
				( isset( $settings_for_check['colors']['sug_fnt_cl'] ) ) ? $settings_for_check['colors']['sug_fnt_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Content builder font color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][cb_fnt_cl]',
				'',
				( isset( $settings_for_check['colors']['cb_fnt_cl'] ) ) ? $settings_for_check['colors']['cb_fnt_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'Content builder clickable background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][cb_clc_cl]',
				'',
				( isset( $settings_for_check['colors']['cb_clc_cl'] ) ) ? $settings_for_check['colors']['cb_clc_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'More results button background color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][mr_bg_cl]',
				'',
				( isset( $settings_for_check['colors']['mr_bg_cl'] ) ) ? $settings_for_check['colors']['mr_bg_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'More results button background (on hover) color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][mr_bg_hvr_cl]',
				'',
				( isset( $settings_for_check['colors']['mr_bg_hvr_cl'] ) ) ? $settings_for_check['colors']['mr_bg_hvr_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'More results font color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][mr_fnt_cl]',
				'',
				( isset( $settings_for_check['colors']['mr_fnt_cl'] ) ) ? $settings_for_check['colors']['mr_fnt_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>

    <div class="isrc-field isrc-settings isrc-havecolorpicker">
        <div class="isrc-label">
            <label for="isrc-settings-css">
				<?php _e( 'More results icon color', 'i_search' ); ?>
            </label>
        </div>
        <div class="isrc-input">
			<?php
			isrc_render_text_field(
				'isrc_sc_opt[colors][mr_icn_cl]',
				'',
				( isset( $settings_for_check['colors']['mr_icn_cl'] ) ) ? $settings_for_check['colors']['mr_icn_cl'] : '',
				'',
				'',
				'color-picker isrc-colors'
			);
			?>
        </div>
        <div class="clear"></div>
    </div>
	<?php
	if ( defined( "ISRC_WOOCOMMERCE_INSTALLED" ) ) { ?>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: On Sale, background color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_bg]',
					'',
					( isset( $settings_for_check['colors']['badg_bg'] ) ) ? $settings_for_check['colors']['badg_bg'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: On Sale, Font color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_cl]',
					'',
					( isset( $settings_for_check['colors']['badg_cl'] ) ) ? $settings_for_check['colors']['badg_cl'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Out of stock, Background color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_oos_bg]',
					'',
					( isset( $settings_for_check['colors']['badg_oos_bg'] ) ) ? $settings_for_check['colors']['badg_oos_bg'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Out of stock, Font color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_oos_cl]',
					'',
					( isset( $settings_for_check['colors']['badg_oos_cl'] ) ) ? $settings_for_check['colors']['badg_oos_cl'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Featured, Background color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_ft_bg]',
					'',
					( isset( $settings_for_check['colors']['badg_ft_bg'] ) ) ? $settings_for_check['colors']['badg_ft_bg'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Featured, Font color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_ft_cl]',
					'',
					( isset( $settings_for_check['colors']['badg_ft_cl'] ) ) ? $settings_for_check['colors']['badg_ft_cl'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Backorder, Background color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_bo_bg]',
					'',
					( isset( $settings_for_check['colors']['badg_bo_bg'] ) ) ? $settings_for_check['colors']['badg_bo_bg'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Badge: Backorder, Font color', 'i_search' ); ?>
                </label>
            </div>
            <div class="isrc-input">
				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][badg_bo_cl]',
					'',
					( isset( $settings_for_check['colors']['badg_bo_cl'] ) ) ? $settings_for_check['colors']['badg_bo_cl'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="isrc-field isrc-settings isrc-havecolorpicker">
            <div class="isrc-label">
                <label for="isrc-settings-css">
					<?php _e( 'Add to cart frame background color', 'i_search' ); ?>
                    <p class="description">
						<?php _e( 'Clean template only.', 'i_search' ); ?>
                    </p>
                </label>
            </div>
            <div class="isrc-input">
                <ul class="isrc-checkbox-list isrc-bl isrc-mtb5px">
					<?php
					isrc_render_fieldset_checkbox(
						'atc_ed',
						"isrc_sc_opt[colors][atc_ed]",
						'1',
						'',
						'',
						$settings_for_check,
						isset( $settings_for_check['colors']['atc_ed'] ),
						'',
						__( 'Hide add to cart buttons', 'i_search' ),
						'',
						false
					);
					?>
                </ul>

				<?php
				isrc_render_text_field(
					'isrc_sc_opt[colors][atc_fr_bg]',
					'',
					( isset( $settings_for_check['colors']['atc_fr_bg'] ) ) ? $settings_for_check['colors']['atc_fr_bg'] : '',
					'',
					'',
					'color-picker isrc-colors'
				);
				?>
            </div>
            <div class="clear"></div>
        </div>

	<?php } ?>
</div>