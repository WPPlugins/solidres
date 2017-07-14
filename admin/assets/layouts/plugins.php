<?php
/*------------------------------------------------------------------------
Solidres - Hotel booking plugin for WordPress
------------------------------------------------------------------------
@Author    Solidres Team
@Website   http://www.solidres.com
@Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
@License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if (!defined('ABSPATH'))
{
	exit;
}
$plugins = solidres()->plugins;

?>
<?php if (count($plugins)): ?>
	<div id="asset_plugins" class="postbox closed">
		<div class="handlediv"><br></div>
		<h3 class="hndle"><span><?php _e('Plugins', 'solidres'); ?></span></h3>

		<div class="inside">
			<div class="solidres-accordion">
				<?php foreach ($plugins as $plugin): ?>
					<?php echo $plugin; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

<?php endif; ?>