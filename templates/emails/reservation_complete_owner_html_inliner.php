<!-- Inliner Build Version 4380b7741bb759d6cb997545f3add21ad48f010b -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php /*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

$editLink = get_admin_url() . 'admin.php?page=sr-reservations&action=edit&id='.$display_data['reservation']->id;
$checkin = new DateTime($display_data['reservation']->checkin, $display_data['timezone']);
$checkout = new DateTime($display_data['reservation']->checkout, $display_data['timezone']);
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<body style="width: 100% !important; min-width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"><style type="text/css">
a:hover {
	color: #2795b6 !important;
}
a:active {
	color: #2795b6 !important;
}
a:visited {
	color: #2ba6cb !important;
}
h1 a:active {
	color: #2ba6cb !important;
}
h2 a:active {
	color: #2ba6cb !important;
}
h3 a:active {
	color: #2ba6cb !important;
}
h4 a:active {
	color: #2ba6cb !important;
}
h5 a:active {
	color: #2ba6cb !important;
}
h6 a:active {
	color: #2ba6cb !important;
}
h1 a:visited {
	color: #2ba6cb !important;
}
h2 a:visited {
	color: #2ba6cb !important;
}
h3 a:visited {
	color: #2ba6cb !important;
}
h4 a:visited {
	color: #2ba6cb !important;
}
h5 a:visited {
	color: #2ba6cb !important;
}
h6 a:visited {
	color: #2ba6cb !important;
}
table.button:hover td {
	background: #2795b6 !important;
}
table.button:visited td {
	background: #2795b6 !important;
}
table.button:active td {
	background: #2795b6 !important;
}
table.button:hover td a {
	color: #fff !important;
}
table.button:visited td a {
	color: #fff !important;
}
table.button:active td a {
	color: #fff !important;
}
table.button:hover td {
	background: #2795b6 !important;
}
table.tiny-button:hover td {
	background: #2795b6 !important;
}
table.small-button:hover td {
	background: #2795b6 !important;
}
table.medium-button:hover td {
	background: #2795b6 !important;
}
table.large-button:hover td {
	background: #2795b6 !important;
}
table.button:hover td a {
	color: #ffffff !important;
}
table.button:active td a {
	color: #ffffff !important;
}
table.button td a:visited {
	color: #ffffff !important;
}
table.tiny-button:hover td a {
	color: #ffffff !important;
}
table.tiny-button:active td a {
	color: #ffffff !important;
}
table.tiny-button td a:visited {
	color: #ffffff !important;
}
table.small-button:hover td a {
	color: #ffffff !important;
}
table.small-button:active td a {
	color: #ffffff !important;
}
table.small-button td a:visited {
	color: #ffffff !important;
}
table.medium-button:hover td a {
	color: #ffffff !important;
}
table.medium-button:active td a {
	color: #ffffff !important;
}
table.medium-button td a:visited {
	color: #ffffff !important;
}
table.large-button:hover td a {
	color: #ffffff !important;
}
table.large-button:active td a {
	color: #ffffff !important;
}
table.large-button td a:visited {
	color: #ffffff !important;
}
table.secondary:hover td {
	background: #d0d0d0 !important; color: #555;
}
table.secondary:hover td a {
	color: #555 !important;
}
table.secondary td a:visited {
	color: #555 !important;
}
table.secondary:active td a {
	color: #555 !important;
}
table.success:hover td {
	background: #457a1a !important;
}
table.alert:hover td {
	background: #970b0e !important;
}
table.facebook:hover td {
	background: #2d4473 !important;
}
table.twitter:hover td {
	background: #0087bb !important;
}
table.google-plus:hover td {
	background: #CC0000 !important;
}
@media only screen and (max-width: 600px) {
	table[class="body"] img {
		width: auto !important; height: auto !important;
	}
	table[class="body"] center {
		min-width: 0 !important;
	}
	table[class="body"] .container {
		width: 95% !important;
	}
	table[class="body"] .row {
		width: 100% !important; display: block !important;
	}
	table[class="body"] .wrapper {
		display: block !important; padding-right: 0 !important;
	}
	table[class="body"] .columns {
		table-layout: fixed !important; float: none !important; width: 100% !important; padding-right: 0px !important; padding-left: 0px !important; display: block !important;
	}
	table[class="body"] .column {
		table-layout: fixed !important; float: none !important; width: 100% !important; padding-right: 0px !important; padding-left: 0px !important; display: block !important;
	}
	table[class="body"] .wrapper.first .columns {
		display: table !important;
	}
	table[class="body"] .wrapper.first .column {
		display: table !important;
	}
	table[class="body"] table.columns td {
		width: 100% !important;
	}
	table[class="body"] table.column td {
		width: 100% !important;
	}
	table[class="body"] .columns td.one {
		width: 8.333333% !important;
	}
	table[class="body"] .column td.one {
		width: 8.333333% !important;
	}
	table[class="body"] .columns td.two {
		width: 16.666666% !important;
	}
	table[class="body"] .column td.two {
		width: 16.666666% !important;
	}
	table[class="body"] .columns td.three {
		width: 25% !important;
	}
	table[class="body"] .column td.three {
		width: 25% !important;
	}
	table[class="body"] .columns td.four {
		width: 33.333333% !important;
	}
	table[class="body"] .column td.four {
		width: 33.333333% !important;
	}
	table[class="body"] .columns td.five {
		width: 41.666666% !important;
	}
	table[class="body"] .column td.five {
		width: 41.666666% !important;
	}
	table[class="body"] .columns td.six {
		width: 50% !important;
	}
	table[class="body"] .column td.six {
		width: 50% !important;
	}
	table[class="body"] .columns td.seven {
		width: 58.333333% !important;
	}
	table[class="body"] .column td.seven {
		width: 58.333333% !important;
	}
	table[class="body"] .columns td.eight {
		width: 66.666666% !important;
	}
	table[class="body"] .column td.eight {
		width: 66.666666% !important;
	}
	table[class="body"] .columns td.nine {
		width: 75% !important;
	}
	table[class="body"] .column td.nine {
		width: 75% !important;
	}
	table[class="body"] .columns td.ten {
		width: 83.333333% !important;
	}
	table[class="body"] .column td.ten {
		width: 83.333333% !important;
	}
	table[class="body"] .columns td.eleven {
		width: 91.666666% !important;
	}
	table[class="body"] .column td.eleven {
		width: 91.666666% !important;
	}
	table[class="body"] .columns td.twelve {
		width: 100% !important;
	}
	table[class="body"] .column td.twelve {
		width: 100% !important;
	}
	table[class="body"] td.offset-by-one {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-two {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-three {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-four {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-five {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-six {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-seven {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-eight {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-nine {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-ten {
		padding-left: 0 !important;
	}
	table[class="body"] td.offset-by-eleven {
		padding-left: 0 !important;
	}
	table[class="body"] table.columns td.expander {
		width: 1px !important;
	}
	table[class="body"] .right-text-pad {
		padding-left: 10px !important;
	}
	table[class="body"] .text-pad-right {
		padding-left: 10px !important;
	}
	table[class="body"] .left-text-pad {
		padding-right: 10px !important;
	}
	table[class="body"] .text-pad-left {
		padding-right: 10px !important;
	}
	table[class="body"] .hide-for-small {
		display: none !important;
	}
	table[class="body"] .show-for-desktop {
		display: none !important;
	}
	table[class="body"] .show-for-small {
		display: inherit !important;
	}
	table[class="body"] .hide-for-desktop {
		display: inherit !important;
	}
	table[class="body"] .right-text-pad {
		padding-left: 10px !important;
	}
	table[class="body"] .left-text-pad {
		padding-right: 10px !important;
	}
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width" /><title><?php echo apply_filters( 'solidres_asset_name', $display_data['asset']->name )?></title><table class="body" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; height: 100%; width: 100%; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="center" align="center" valign="top" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;">
			<center style="width: 100%; min-width: 580px;">

				<!-- Begin email header -->
				<table class="row header" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; background: #999999; padding: 0px;" bgcolor="#999999"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="center" align="center" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" valign="top">
							<center style="width: 100%; min-width: 580px;">

								<table class="container" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

											<table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="six sub-columns" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; min-width: 0px; width: 50%; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 10px 10px 0px;" align="left" valign="top">
														<?php if ( isset( $asset_params['logo'] ) ) : ?>
														<img src="<?php echo $asset_params['logo']; ?>" alt="logo" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: left; clear: both; display: block;" align="left" /><?php endif ?></td>
													<td class="six sub-columns last" style="text-align: right; vertical-align: middle; word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; min-width: 0px; width: 50%; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="right" valign="middle">
														<span class="template-label" style="color: #ffffff; font-weight: bold; font-size: 11px;"><?php _e( 'Reservation confirmation', 'solidres' ) ?></span><br /><span class="template-label" style="color: #ffffff; font-weight: bold; font-size: 11px;">
															<a href="<?php echo $editLink ?>" target="_blank" style="color: #2ba6cb; text-decoration: none;">
																<?php printf( __( 'Reference ID: %s', 'solidres' ), $display_data['reservation']->code ) ?>
															</a>
														</span>
													</td>
													<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
												</tr></table></td>
									</tr></table></center>
						</td>
					</tr></table><!-- End of email header --><!-- Begin of email body --><table class="container" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top">

							<table class="row callout" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 20px;" align="left" valign="top">

										<table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<h3 style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 32px; margin: 0; padding: 0;" align="left"><?php _e( 'Hello,', 'solidres' ) ?></h3>

													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"> </p>

													<p><?php printf( __( 'A new reservation has been made, please check details below or <a href="%s" target="_blank">click here</a> to view it:', 'solidres' ), $editLink) ?></p></td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><h5 class="email_heading" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; background: #f2f2f2; margin: 0; padding: 5px; border: 1px solid #d9d9d9;" align="left"><?php _e( 'General info', 'solidres' ) ?></h5>

							<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 20px 0px 0px;" align="left" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Checkin: ', 'solidres' ) . $checkin->format($display_data['date_format']) ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Checkout: ', 'solidres' ) . $checkout->format($display_data['date_format']) ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Payment method: ', 'solidres' ) . __( $display_data['reservation']->payment_method_id, 'solidres' ) ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Email: ', 'solidres') . $display_data['reservation']->customer_email ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Length of stay: ', 'solidres') ?>
														<?php
														if ( $display_data['asset']->booking_type == 0 ) :
															echo sprintf( _n( '%d night', '%d nights', $display_data['stay_length'], 'solidres' ), $display_data['stay_length']);
														else :
															echo sprintf( _n( '%d day', '%d days', $display_data['stay_length'] + 1, 'solidres' ), $display_data['stay_length'] + 1 );
														endif;
														?>
													</p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Note: ', 'solidres') . $display_data['reservation']->note ?></p>
												</td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
									<td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Room cost (excl tax): ', 'solidres' ) . $display_data['sub_total'] ?></p>
													<?php if ($display_data['discount_pre_tax'] && !is_null($display_data['total_discount'])) : ?><p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal;  line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Total discount: ', 'solidres' ) . '-' . $display_data['total_discount']?></p>
													<?php endif; ?><p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Room cost tax: ', 'solidres' ) . $display_data['tax'] ?></p>
													<?php if (!$display_data['discount_pre_tax'] && !is_null($display_data['total_discount'])) : ?><p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal;  line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Total discount: ', 'solidres' ) .  '-' . $display_data['total_discount']?></p>
													<?php endif; ?><p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Extra cost (exl tax): ', 'solidres' ) . $display_data['total_extra_price_tax_excl'] ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Extra tax: ', 'solidres' ) . $display_data['extra_tax'] ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Grand total: ', 'solidres' ) . $display_data['grand_total'] ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php echo __( 'Deposit Amount: ', 'solidres' ) . $display_data['deposit_amount'] ?></p>
												</td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><?php if (!empty($display_data['bankwire_instructions'])) : ?><h5 class="email_heading" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; background: #f2f2f2; margin: 0; padding: 5px; border: 1px solid #d9d9d9;" align="left"><?php _e( 'Bankwire info', 'solidres') ?></h5>

							<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

										<table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo $display_data['bankwire_instructions']['account_name'];
														?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo $display_data['bankwire_instructions']['account_details'];
														?></p>
												</td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><?php endif ?>

							<?php if ( ! empty( $display_data['payment_method_custom_email_content'] ) ) : ?><h5 class="email_heading" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; background: #f2f2f2; margin: 0; padding: 5px; border: 1px solid #d9d9d9;" align="left"><?php echo __( 'Payment information', 'solidres' ) ?></h5>

								<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

										<table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<?php echo $display_data['payment_method_custom_email_content']; ?></td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
							</tr></table><?php endif ?>

							<h5 class="email_heading" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; background: #f2f2f2; margin: 0; padding: 5px; border: 1px solid #d9d9d9;" align="left"><?php _e( 'Room/Extra info', 'solidres' ) ?></h5>

							<?php foreach($display_data['reserved_room_details'] as $room) : ?>
							<p class="email_roomtype_name" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: bold; text-align: left; line-height: 19px; font-size: 14px; border-bottom-style: solid; border-bottom-color: #CCC; border-bottom-width: 1px; margin: 10px 0 5px; padding: 0;" align="left">
								<?php echo apply_filters( 'solidres_roomtype_name' , $room->room_type_name ) ?>
							</p>

							<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 20px 0px 0px;" align="left" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo __( 'Guest fullname', 'solidres' ) . ': '. $room->guest_fullname ?>
													</p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php foreach ($room->other_info as $info) : if (substr($info->key, 0, 7) == 'smoking') : ?>
															<?php echo __( $info->key, 'solidres' ) . ': ' . ($info->value == '' ? __( 'No preferences', 'solidres' ) : ($info->value == 1 ? __( 'Yes', 'solidres' ): __( 'No', 'solidres' ) ) ) ; ?>
														<?php endif; endforeach; ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo __( 'Adult number', 'solidres' ) . ': '. $room->adults_number ?>
													</p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo __( 'Child number', 'solidres' ) . ': '. $room->children_number ?>
													</p>
													<?php foreach ($room->other_info as $info) : ?>
													<ul><?php if (substr($info->key, 0, 5) == 'child') : ?>
														<li>
															<?php echo __( $info->key, 'solidres' ) . ': ' . sprintf( _n( '%s year old', '%s years old', $info->value, 'solidres' ), $info->value ) ?>
														</li>
														<?php endif; ?></ul><?php endforeach; ?></td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
									<td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<?php if ( isset($room->extras) && is_array($room->extras)) : ?>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php _e( 'Extras items: ', 'solidres' ) ?></p>
													<?php foreach($room->extras as $extra) : ?>

													<dl><dt>
															<?php echo apply_filters( 'solidres_extra_name', $extra->extra_name ) ?>
														</dt>
														<dd>
															<?php echo __( 'Quantity: ', 'solidres' ) . $extra->extra_quantity ?>
														</dd>
														<dd>
															<?php
															$roomExtraCurrency = clone $display_data['base_currency'];
															$roomExtraCurrency->set_value($extra->extra_price);
															echo __( 'Price: ', 'solidres' ) . $roomExtraCurrency->format()
															?>
														</dd>
													</dl><?php endforeach; ?><?php endif; ?></td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><?php endforeach; ?><h5 class="email_heading" style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; background: #f2f2f2; margin: 0; padding: 5px; border: 1px solid #d9d9d9;" align="left"><?php _e( 'Other info', 'solidres' ) ?></h5>

							<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="top">

										<table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<dl><?php if (isset($display_data['reserved_extras']) && is_array($display_data['reserved_extras'])) :
														foreach($display_data['reserved_extras'] as $extra) : ?>
														<dt>
															<?php echo apply_filters( 'solidres_extra_name', $extra->extra_name )  ?>
														</dt>
														<dd>
															<?php echo __( 'Quantity: ', 'solidres' ) . $extra->extra_quantity ?>
														</dd>
														<dd>
															<?php
															$bookingExtraCurrency = clone $display_data['base_currency'];
															$bookingExtraCurrency->set_value($extra->extra_price);
															echo __( 'Price: ', 'solidres' ) . $bookingExtraCurrency->format()
															?>
														</dd>
														<?php endforeach;
														endif;
														?></dl></td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><table class="row footer" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #ebebeb; margin: 0; padding: 10px 20px 0px 0px;" align="left" bgcolor="#ebebeb" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="left-text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px 10px;" align="left" valign="top">

													<h5 style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; margin: 0; padding: 0 0 10px;" align="left"><?php _e( 'Connect With Us: ', 'solidres' ) ?></h5>

													<?php if ( ! empty( $display_data['social_network']['facebook_link'] ) ) : ?>
													<table class="tiny-button facebook" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #3b5998; margin: 0; padding: 5px 0 4px; border: 1px solid #2d4473;" align="center" bgcolor="#3b5998" valign="top">
															<a href="<?php echo $display_data['social_network']['facebook_link']; ?>" style="color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">Facebook</a>
														</td>
													</tr></table><?php endif; ?><br /><?php if (!empty( $display_data['social_network']['twitter_link'] ) ) : ?>
													<table class="tiny-button twitter" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #00acee; margin: 0; padding: 5px 0 4px; border: 1px solid #0087bb;" align="center" bgcolor="#00acee" valign="top">

															<a href="<?php echo $display_data['social_network']['twitter_link']; ?>" style="color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">Twitter</a>

														</td>
													</tr></table><?php endif; ?><br /><?php if (!empty( $display_data['social_network']['google_plus_link'] ) ) : ?>
													<table class="tiny-button google-plus" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #ffffff; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #DB4A39; margin: 0; padding: 5px 0 4px; border: 1px solid #cc0000;" align="center" bgcolor="#DB4A39" valign="top">

															<a href="<?php echo $display_data['social_network']['google_plus_link']; ?>" style="color: #ffffff; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">Google Plus</a>

														</td>
													</tr></table><?php endif; ?>

															</td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
									<td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #ebebeb; margin: 0; padding: 10px 0px 0px;" align="left" bgcolor="#ebebeb" valign="top">

										<table class="six columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="last right-text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="top">
													<h5 style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 24px; margin: 0; padding: 0 0 10px;" align="left"><?php _e( 'Contact Info: ', 'solidres' ) ?></h5>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
														<?php echo __( 'Address: ', 'solidres' ) . $display_data['asset']->address_1 . ', ' . $display_data['asset']->postcode . ', ' . $display_data['asset']->city . ', ' . (!empty($display_data['asset_state']) ? $display_data['asset_state']->code_2 . ' ' : '') . $display_data['asset']->postcode
														?>
													</p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php _e( 'Phone: ', 'solidres' ) ?><?php echo $display_data['asset']->phone ?></p>
													<p style="color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left"><?php _e( 'Email: ', 'solidres' ) ?><a href="mailto:<?php echo $display_data['asset']->email ?>"><?php echo $display_data['asset']->email ?></a></p>
												</td>
												<td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
											</tr></table></td>
								</tr></table><!-- container end below --></td>
					</tr></table><!-- End of email body --></center>
		</td>
	</tr></table></body>
</html>
