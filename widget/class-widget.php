<?php

/**
 * Adds i-Search widget.
 */
class i_search_widged extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'i_search_widged',
			esc_html__( 'i-Search', 'i_search' ),
			array( 'description' => esc_html__( 'Displays a i-Search instance. ', 'i_search' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( empty( $instance['shortcode_id'] ) ) {
			return false;
		}

		$shortcode_string = "[isrc_ajax_search shortcode_id={$instance['shortcode_id']}]";
		echo do_shortcode( $shortcode_string );
		echo $args['after_widget'];

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $wpdb;
		$title        = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'i_search' );
		$shortcode_id = ! empty( $instance['shortcode_id'] ) ? $instance['shortcode_id'] : 0;
		$lang         = isrc_get_lang_admin();
		$sql          = "SELECT * FROM {$wpdb->prefix}isearch_shortcodes WHERE lang = '{$lang}' ORDER BY id DESC";
		$results      = $wpdb->get_results( $sql, 'ARRAY_A' );
		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'i_search' ); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
		<?php
		if ( empty( $results ) ) {
			?>
            <p>
				<?php _e( 'Please setup a search instance first.', 'i_search' ); ?>
            </p>
		<?php } else { ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>"><?php esc_attr_e( 'Select search instance:', 'i_search' ); ?></label>
                <select style="width:100%" id="<?php echo esc_attr( $this->get_field_id( 'shortcode_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'shortcode_id' ) ); ?>">
					<?php foreach ( $results as $key => $val ) { ?>
                        <option <?php echo ( $val['id'] == $shortcode_id ) ? 'selected="selected"' : '' ?> value="<?php echo $val['id']; ?>"><?php echo $val['title']; ?></option>
					<?php } ?>
                </select>
            </p>
			<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public
	function update(
		$new_instance, $old_instance
	) {
		$instance                 = array();
		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['shortcode_id'] = ( ! empty( $new_instance['shortcode_id'] ) ) ? sanitize_text_field( $new_instance['shortcode_id'] ) : 0;

		return $instance;
	}

}