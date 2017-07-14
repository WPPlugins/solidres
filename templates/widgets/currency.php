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
?>

<form class="solidres-module-currency" action="">
	<ul>
		<?php
		if ( $currency_list ) :
			foreach ( $currency_list as $c ) :
				echo '<li><a href="javascript:Solidres.setCurrency(' . $c->id . ')">'.$c->currency_code.'</a></li>';
			endforeach;
		endif;
		?>
	</ul>
</form>