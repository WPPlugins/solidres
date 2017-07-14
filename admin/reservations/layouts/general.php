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
$options_plugin = get_option( 'solidres_plugin' );
$isDiscountPreTax = @$options_plugin[ 'discount_pre_tax' ];

$badges = array(
	0 => 'pending_code',
	1 => 'checkin_code',
	2 => 'checkout_code',
	3 => 'closed_code',
	4 => 'canceled_code',
	5 => 'confirmed_code',
	-2 => 'trashed_code'
);

$statuses = array(
	0 => __( 'Pending arrival', 'solidres' ),
	1 => __( 'Checked-in', 'solidres' ),
	2 => __( 'Checked-out', 'solidres' ),
	3 => __( 'Closed', 'solidres' ),
	4 => __( 'Canceled', 'solidres' ),
	5 => __( 'Confirmed', 'solidres' ),
	-2 => __( 'Trashed', 'solidres' )
);

$paymentStatues = array(
	0 => __( 'Unpaid', 'solidres' ),
	1 => __( 'Completed', 'solidres' ),
	2 => __( 'Cancelled', 'solidres' ),
	3 => __( 'Pending', 'solidres' )
);

$totalExtraPriceTaxIncl = $sr_form_data->total_extra_price_tax_incl;
$totalExtraPriceTaxExcl = $sr_form_data->total_extra_price_tax_excl;
$totalExtraTaxAmount = $totalExtraPriceTaxIncl - $totalExtraPriceTaxExcl;
$totalPaid = $sr_form_data->total_paid;
$deposit = $sr_form_data->deposit_amount;

$subTotal = clone $baseCurrency;
$subTotal->set_value( $sr_form_data->total_price_tax_excl - $sr_form_data->total_single_supplement );

$totalSingleSupplement = clone $baseCurrency;
$totalSingleSupplement->set_value($sr_form_data->total_single_supplement);

$totalDiscount = clone $baseCurrency;
$totalDiscount->set_value($sr_form_data->total_discount);

$tax = clone $baseCurrency;
$tax->set_value($sr_form_data->tax_amount);
$totalExtraPriceTaxExclDisplay = clone $baseCurrency;
$totalExtraPriceTaxExclDisplay->set_value($totalExtraPriceTaxExcl);
$totalExtraTaxAmountDisplay = clone $baseCurrency;
$totalExtraTaxAmountDisplay->set_value($totalExtraTaxAmount);
$grandTotal = clone $baseCurrency;

if ($isDiscountPreTax) :
	$grandTotal->set_value($sr_form_data->total_price_tax_excl - $sr_form_data->total_discount + $sr_form_data->tax_amount + $totalExtraPriceTaxIncl);
else :
	$grandTotal->set_value($sr_form_data->total_price_tax_excl + $sr_form_data->tax_amount - $sr_form_data->total_discount + $totalExtraPriceTaxIncl);
endif;


$depositAmount = clone $baseCurrency;
$depositAmount->set_value(isset($deposit) ? $deposit : 0);
$totalPaidAmount = clone $baseCurrency;
$totalPaidAmount->set_value(isset($totalPaid) ? $totalPaid : 0);

$couponCode = $sr_form_data->coupon_code;
$reservationId = $sr_form_data->id;
$reservationState = $sr_form_data->state;
$paymentStatus = $sr_form_data->payment_status;
$bookingType = $sr_form_data->booking_type;

$lengthOfStay = (int)SR_Utilities::calculate_date_diff($sr_form_data->checkin, $sr_form_data->checkout);
$paymentMethodTxnId = $sr_form_data->payment_method_txn_id;
$origin = $sr_form_data->origin;
?>

<div id="reservation_general_info" class="postbox">
	<div class="handlediv"><br></div>
	<h3 class="hndle"><span><?php _e( 'General info', 'solidres' ); ?></span></h3>

	<div class="inside reservation-details">
		<!-- Start here -->
		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Code', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<span class="<?php echo $badges[ $sr_form_data->state ] ?>"><?php echo $sr_form_data->code; ?></span>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Payment status', 'solidres' ); ?></label>
					</div>
					<div class="eight columns align-right">
						<?php if ( current_user_can( 'solidres_user' ) ) { ?>
							<span><?php echo $paymentStatues[$sr_form_data->payment_status]; ?></span>
						<?php } else { ?>
							<span><?php echo '<a href="" id="payment_status" data-type="select" data-value="' . $sr_form_data->payment_status . '" data-pk="' . $id . '">' . $paymentStatues[$sr_form_data->payment_status] . '</a>'; ?></span>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Asset name', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php
						$assetLink = 'admin.php?page=sr-assets&action=edit&id=' . $sr_form_data->reservation_asset_id;
						echo isset( $sr_form_data->reservation_asset_name ) ? '<a href="' . $assetLink . '">' . apply_filters( 'solidres_asset_name', $sr_form_data->reservation_asset_name ) . '</a>' : '';
						?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Transaction Id', 'solidres' ); ?></label>
					</div>
					<div class="eight columns align-right">
						<a href="#"
						   id="payment_method_txn_id"
						   data-type="text"
						   data-pk="<?php echo $id ?>"
						   data-value="<?php echo $paymentMethodTxnId ?>"
						   data-original-title=""><?php echo isset($paymentMethodTxnId) ? $paymentMethodTxnId : '' ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Checkin', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo date( get_option( 'date_format' ), strtotime($sr_form_data->checkin) ); ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Room cost (excl tax)', 'solidres' ); ?></label>
					</div>
					<div class="eight columns align-right">
						<?php echo $subTotal->format(); ?>
					</div>
				</div>

			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Checkout', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo date( get_option( 'date_format' ), strtotime( $sr_form_data->checkout ) ); ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Room cost tax', 'solidres' ); ?></label>
					</div>
					<div class="eight columns align-right">
						<?php echo $totalExtraTaxAmountDisplay->format(); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Length of stay', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php
						if ($bookingType == 0) :
							printf( _n( '%d night', '%d nights', $lengthOfStay, 'solidres' ), $lengthOfStay );
						else :
							printf( _n( '%d day', '%d days', $lengthOfStay + 1, 'solidres' ), $lengthOfStay + 1 );
						endif;
						?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Total discount', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php echo $totalDiscount->format(); ?>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Status', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php if ( current_user_can( 'solidres_user' ) ) : ?>
								<?php
								echo '<span class="reservation_status_user">' . $status . '</span>';
								if ( $sr_form_data->state != 4 ) :
									$nonce = wp_create_nonce( 'cancel_reservation_nonce' );

									$current_user = wp_get_current_user();
									$author_id    = $current_user->ID;
									?>
									<form id="cancel_reservation_form" action="" method="POST">
										<input type="hidden" name="reservation_id" value="<?php echo $id; ?>"
										       id="reservation_id">
										<input type="hidden" name="customer_id" value="<?php echo $author_id; ?>"
										       id="customer_id">
										<button type="submit" name="cancel_reservation"
										        class="srform_button button button-primary button-large cancel_reservation_btn"
										        data-nonce="<?php echo $nonce; ?>"><?php _e( ' Cancel this reservation', 'solidres' ); ?></button>
									</form>
								<?php endif ?>
						<?php else : ?>
							<?php echo '<a href="" id="state" data-type="select" data-value="' . $sr_form_data->state . '" data-pk="' . $id . '">' . $statuses[$sr_form_data->state] . '</a>'; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Extra cost (excl tax)', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php echo $totalExtraPriceTaxExclDisplay->format(); ?>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Origin', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<a href="#" id="origin" data-type="text" data-pk="<?php echo $id ?>"
						   data-value="<?php echo isset($origin) ? $origin : '' ?>"
						   data-original-title=""><?php echo isset($origin) ? $origin : '' ?></a>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Extra tax', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php echo $totalExtraTaxAmountDisplay->format(); ?>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Created date', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo date( get_option( 'date_format' ), strtotime( $sr_form_data->created_date ) ); ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Grand total', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php echo $grandTotal->format(); ?>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Payment type', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php
						$solidres_payment_gateways = solidres()->payment_gateways();
						echo $solidres_payment_gateways->lookup[$sr_form_data->payment_method_id]->title; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Deposit amount', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php echo $depositAmount->format(); ?>
				</div>
			</div>
		</div>

		<div class="sr_row">
			<div class="six columns">
				<div class="sr_row">
					<div class="four columns">
						<label><?php _e( 'Coupon code', 'solidres' ); ?></label>
					</div>
					<div class="eight columns">
						<?php echo $sr_form_data->coupon_code == null ? 'N/A' : $sr_form_data->coupon_code; ?>
					</div>
				</div>
			</div>
			<div class="six columns">
				<div class="four columns">
					<label><?php _e( 'Total paid', 'solidres' ); ?></label>
				</div>
				<div class="eight columns align-right">
					<?php if ( current_user_can( 'solidres_user' ) ) { ?>
						<span class="align-right"><?php echo $totalPaidAmount->format(); ?></span>
					<?php } else { ?>
						<span class="align-right"><?php echo '<a href="" id="total_paid" data-type="text" data-value="' . $sr_form_data->total_paid . '" data-pk="' . $id . '">' . $totalPaidAmount->format() . '</a>'; ?></span>
					<?php } ?>
				</div>
			</div>
		</div>

		<!-- end here -->
	</div>
</div>

<?php
$paymentData = $sr_form_data->payment_data;
if ( !empty($paymentData) && $sr_form_data->payment_method_id == 'offline' ) :
	$paymentData = json_decode($paymentData);
	$paymentData->cardnumber = str_pad($paymentData->cardnumber, 16, 'X', STR_PAD_RIGHT);
	?>
	<div id="reservation_customer_payment_info" class="postbox">
		<div class="handlediv"><br></div>
		<h3 class="hndle"><span><?php _e( 'Customer payment info', 'solidres' ); ?></span></h3>

		<div class="inside">
			<table class="form-table">
				<tbody>
				<tr>
					<td>
						<p><?php echo __( 'Card number', 'solidres' ) . ': ' . $paymentData->cardnumber ?></p>
						<p><?php echo __( 'Card holder', 'solidres' ) . ': ' . $paymentData->cardholder ?></p>
						<p><?php echo __( 'Card CVV', 'solidres' ) . ': ' . $paymentData->cardcvv ?></p>
						<p><?php echo __( 'Expired month', 'solidres' ) . ': ' . $paymentData->cardexpmonth ?></p>
						<p><?php echo __( 'Expired year', 'solidres' ) . ': ' . $paymentData->cardexpyear ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php endif ?>