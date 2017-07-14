<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking extension for Wordpress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2015 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/
if (!defined('ABSPATH'))
{
	exit;
}
$column_class = has_action('sr_form_postbox_side') || has_action('sr_form_postbox_normal') ? ' columns-2' : '';
?>
<form name="sr_form_edit" action="" method="post" id="srform">
	<div class="wrap srform_wrapper">
		<h2><?php echo $this->title; ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder<?php echo $column_class; ?>">
				<div id="post-body-content" class="postbox-container" style="position: relative;">
					<div id="sr_general_infomation" class="postbox">
						<div class="meta-box-sortables ui-sortable">
							<h2 class="hndle ui-sortable-handle">
								<span><?php _e('General infomartion', 'solidres'); ?></span>
							</h2>

							<div class="inside">
								<table class="form-table">
									<tbody>
										<?php echo join("\n", $items); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<?php if (has_action('sr_form_postbox_side')): ?>
					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">
							<?php do_action('sr_form_postbox_side', $this->context); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php if (has_action('sr_form_postbox_normal')): ?>
					<div id="postbox-container-2" class="postbox-container">
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">
							<?php do_action('sr_form_postbox_normal', $this->context); ?>
						</div>
					</div>
				<?php endif; ?>
				<input type="submit" value="<?php echo __('Save'); ?>"
				       class="srform_button button button-primary button-large"/>
			</div>
		</div>
	</div>

</form>