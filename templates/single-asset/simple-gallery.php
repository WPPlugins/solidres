<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

?>

<div class="<?php echo SR_UI_GRID_CONTAINER ?>">
	<?php if ( ! empty( $media ) ):
		$first_media_attr = wp_get_attachment_image_src( $media[0]->media_id, 'full' );
		?>
		<div class="main-photo <?php echo SR_UI_GRID_COL_5 ?>">
			<a class="sr-photo" href="<?php echo $first_media_attr[0]; ?>">
				<?php echo wp_get_attachment_image( $media[0]->media_id, array( 300, 250) ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="other-photos clearfix <?php echo SR_UI_GRID_COL_7 ?>">
		<?php foreach ( $media as $media ) :
			$media_attr = wp_get_attachment_image_src( $media->media_id, 'full' )
			?>
			<a class="sr-photo" href="<?php echo $media_attr[0]; ?>">
				<?php echo wp_get_attachment_image( $media->media_id, array( 75, 75) ); ?>
			</a>
		<?php endforeach ?>
	</div>
</div>