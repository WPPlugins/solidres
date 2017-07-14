<?php
/*------------------------------------------------------------------------
 Solidres - Hotel booking plugin for WordPress
 ------------------------------------------------------------------------
 @Author    Solidres Team
 @Website   http://www.solidres.com
 @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creating the widget
 *
 * Class SR_Widget_Check_Availability
 */
class SR_Widget_Check_Availability extends WP_Widget {
	function __construct() {
		parent::__construct(
			'SR_Widget_Check_Availability',
			__('Solidres - Widget check availability', 'solidres'),
			array( 'description' => __( 'Solidres - Widget check availability', 'solidres' ), )
		);
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo apply_filters( 'solidres_before_widget_check_availability', '' );

		solidres_get_template( 'widgets/check-availability-' . (isset($instance['widget_layout']) ? $instance['widget_layout'] : 'vertical' ) . '.php', array( 'args' => $args, 'instance' => $instance ) );

		echo apply_filters( 'solidres_after_widget_check_availability', '' );

		echo $args['after_widget'];
	}

	/**
	 * Widget Backend
	 *
	 * @param array $instance
	 * @return mixed
	 */

	public function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : __('Solidres - Widget check availability', 'solidres');
		$target_itemid = isset($instance['target_itemid']) ? $instance['target_itemid'] : '';
		$enable_room_quantity_option = isset($instance['enable_room_quantity_option']) ? $instance['enable_room_quantity_option'] : '';
		$max_room_number = isset($instance['max_room_number']) ? $instance['max_room_number'] : 10;
		$max_adult_number = isset($instance['max_adult_number']) ? $instance['max_adult_number'] : 10;
		$max_child_number = isset($instance['max_child_number']) ? $instance['max_child_number'] : 10;
		$layout = isset($instance['widget_layout']) ? $instance['widget_layout'] : 'vertical';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'solidres' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('widget_layout'); ?>"><?php _e( 'Widget Layout', 'solidres' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('widget_layout'); ?>"
			        name="<?php echo $this->get_field_name('widget_layout'); ?>">
				<option <?php echo $layout == 'vertical' ? 'selected' : '' ?> value="vertical"><?php _e( 'Vertical', 'solidres' ) ?></option>
				<option <?php echo $layout == 'horizontal' ? 'selected' : '' ?> value="horizontal"><?php _e( 'Horizontal', 'solidres' ) ?></option>
			</select>
		</p>
		<!--<p>
			<label for="<?php /*echo $this->get_field_id('target_itemid'); */?>"><?php /*_e( 'Target itemid', 'solidres' ); */?></label>
			<input class="widefat" id="<?php /*echo $this->get_field_id('target_itemid'); */?>" name="<?php /*echo $this->get_field_name('target_itemid'); */?>" type="text" value="<?php /*echo esc_attr($target_itemid); */?>">
		</p>-->
		<p>
			<label for="<?php echo $this->get_field_id('enable_room_quantity_option'); ?>"><?php _e( 'Enable room quantity', 'solidres' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('enable_room_quantity_option'); ?>"
			        name="<?php echo $this->get_field_name('enable_room_quantity_option'); ?>">
				<?php
				$options = array('0' => 'No', '1' => 'Yes');
				foreach ($options as $key => $value) {
					echo '<option value="' . $key . '"', $enable_room_quantity_option == $key ? ' selected="selected"' : '', '>' . $value . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_room_number'); ?>"><?php _e( 'Max room number', 'solidres' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_room_number'); ?>"
			       name="<?php echo $this->get_field_name('max_room_number'); ?>" type="text"
			       value="<?php echo esc_attr($max_room_number); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_adult_number'); ?>"><?php _e( 'Max adult number', 'solidres' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_adult_number'); ?>"
			       name="<?php echo $this->get_field_name('max_adult_number'); ?>" type="text"
			       value="<?php echo esc_attr($max_adult_number); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('max_child_number'); ?>"><?php _e( 'Max child number', 'solidres' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_child_number'); ?>"
			       name="<?php echo $this->get_field_name('max_child_number'); ?>" type="text"
			       value="<?php echo esc_attr($max_child_number); ?>">
		</p>
	<?php
	}
}

/**
 * Register and load the widget
 */
function register_widget_checkavailability() {
	register_widget( 'SR_Widget_Check_Availability' );
}
add_action( 'widgets_init', 'register_widget_checkavailability' );
