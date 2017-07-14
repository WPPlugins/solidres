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

add_filter( 'template_include', 'solidres_call_template' );
function solidres_call_template( $template ) {
	$find_paths = array();
	$file = '';
	$solidres_asset = new SR_Asset();
	$is_asset = false;
	if ( isset( $GLOBALS['post'] ) ) {
		$is_asset = $solidres_asset->count_by_alias( $GLOBALS['post']->post_name );
	}

	if ( $is_asset ) {
		$file = 'single-asset.php';
		$find_paths[] = $file;
		$find_paths[] = solidres()->template_path() . $file;
	}

	if ( ! empty( $file ) ) {
		$template = locate_template( array_unique( $find_paths ) );

		if ( ! $template ) {
			$template = solidres()->plugin_path() . '/templates/' . $file;
		}
	}

	return $template;
}

if ( ! function_exists( 'solidres_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function solidres_output_content_wrapper() {
		solidres_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'solidres_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function solidres_output_content_wrapper_end() {
		solidres_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'solidres_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 *
	 */
	function solidres_get_sidebar() {
		solidres_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'solidres_breadcrumb' ) ) {

	/**
	 * Output the Solidres Breadcrumb
	 */
	function solidres_breadcrumb( $args = array() ) {
		$args = wp_parse_args( $args, apply_filters( 'solidres_breadcrumb_defaults', array(
			'delimiter'   => ' &#47; ',
			'wrap_before' => '<nav class="solidres-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
			'wrap_after'  => '</nav>',
			'before'      => '',
			'after'       => '',
			'home'        => _x( 'Home', 'breadcrumb', 'solidres' )
		) ) );

		$breadcrumbs = new Solidres_Breadcrumb();

		if ( $args['home'] ) {
			$breadcrumbs->add_crumb( $args['home'], home_url() );
		}

		$args['breadcrumb'] = $breadcrumbs->generate();

		solidres_get_template( 'global/breadcrumb.php', $args );
	}
}

function solidres_metadata() {
	global $post;
	$solidres_asset = new SR_Asset();
	$is_asset = $solidres_asset->count_by_alias( $post->post_name );
	if ( $is_asset ) {
		$asset = $solidres_asset->load_by_alias( $post->post_name );
		$solidres_country = new SR_Country;
		$country = $solidres_country->load( $asset->country_id, OBJECT );
		$medias = new SR_Media();
		$media = $medias->load_by_asset_id( $asset->id );
		$fbStars = '';
		for ( $i = 1; $i <= $asset->rating; $i++) :
			$fbStars .= '&#x2605;';
		endfor; ?>
		<meta property="og:title" content="<?php echo $fbStars . ' ' . apply_filters( 'solidres_asset_name', $asset->name ) . ', ' . $asset->city . ', '.$country->name; ?>"/>
		<meta property="og:type" content="place"/>
		<meta property="og:url" content="<?php echo esc_url( home_url( $post->post_name ) ); ?>"/>
		<?php if (!empty($media[0]->img_url)) : ?>
		<meta property="og:image" content="<?php echo $media[0]->img_url; ?>"/>
		<?php endif ?>
		<?php if (!empty($media[1]->img_url)) : ?>
		<meta property="og:image" content="<?php echo $media[1]->img_url; ?>"/>
		<?php endif ?>
		<?php if (!empty($media[2]->img_url)) : ?>
		<meta property="og:image" content="<?php echo $media[2]->img_url; ?>"/>
		<?php endif ?>
		<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
		<meta property="og:description" content="<?php echo apply_filters( 'solidres_asset_desc', strip_tags( $asset->description) ); ?>"/>
		<meta property="place:location:latitude" content="<?php echo $asset->lat; ?>"/>
		<meta property="place:location:longitude" content="<?php echo $asset->lng; ?>"/>
		<meta name="keywords" content="<?php echo $asset->metakey ?>" />
		<meta name="author" content="<?php echo $asset->metakey ?>" />
		<meta name="description" content="<?php echo $asset->metadesc ?>" />
		<?php
	}
}