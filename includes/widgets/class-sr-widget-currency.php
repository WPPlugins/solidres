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
 * Class SR_Widget_Currency
 */
class SR_Widget_Currency extends WP_Widget {
	function __construct() {
		parent::__construct(
			'SR_Widget_Currency',
			__('Solidres - Widget currency', 'solidres'),
			array( 'description' => __( 'Solidres - Widget currency', 'solidres' ), )
		);
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		$currencies = new SR_Currency();
		$currency_list = $currencies->load_by_state( 1 );

		solidres_get_template( 'widgets/currency.php' , array( 'currency_list' => $currency_list ));

		echo $args['after_widget'];
	}

	/**
	 * Widget Backend
	 *
	 * @param array $instance
	 * @return mixed
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Solidres - Widget currency', 'solidres' );
		} ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'solidres' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
	<?php
	}
}

/**
 * Register and load the widget
 */
function register_widget_currency() {
	register_widget( 'SR_Widget_Currency' );
}
add_action( 'widgets_init', 'register_widget_currency' );
