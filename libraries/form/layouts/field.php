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
?>
<tr>
	<td class="first" width="20%">
		<label<?php echo !empty($id) ? ' for="' . $id . '"' : ''; ?>>
			<?php echo $title; ?>
			<?php if ($required): ?>
				<span class="required" aria-required="true">*</span>
			<?php endif; ?>
		</label>
	</td>
	<td>
		<?php echo $input; ?>
	</td>
</tr>
