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

if ( isset( $id ) ) {
	$sr_form_data = apply_filters('sr_prepare_data', $sr_form_data, $context);
}

wp_enqueue_script( 'jquery-ui-accordion' );

?>

<div id="asset_payments" class="postbox closed">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'Payments', 'solidres' ); ?></span></h3>

	<div class="inside">
		<div class="solidres-accordion">
			<?php
			foreach ($solidres_payment_gateways->payment_gateways as $gateway) :
				echo $gateway->prepare_form_html( $sr_form_data );
			endforeach
			?>
		</div>
	</div>
</div>