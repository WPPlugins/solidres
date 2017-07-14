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
 * Breadcrumb class, extends from WC Breadcrumb
 */
class Solidres_Breadcrumb {

	/**
	 * Breadcrumb trail
	 *
	 * @var array
	 */
	private $crumbs = array();

	/**
	 * Add a crumb so we don't get lost
	 *
	 * @param string $name
	 * @param string $link
	 */
	public function add_crumb( $name, $link = '' ) {
		$this->crumbs[] = array(
			$name,
			$link
		);
	}

	/**
	 * Reset crumbs
	 */
	public function reset() {
		$this->crumbs = array();
	}

	/**
	 * Get the breadcrumb
	 *
	 * @return array
	 */
	public function get_breadcrumb() {
		return $this->crumbs;
	}

	/**
	 * Generate breadcrumb trail
	 *
	 * @return array of breadcrumbs
	 */
	public function generate() {
		$conditionals = array(
			'is_home',
			'is_404',
			'is_attachment',
			'is_single',
			'is_page',
			'is_post_type_archive',
			'is_category',
			'is_tag',
			'is_author',
			'is_date'
		);

		if ( ! is_front_page() || is_paged() ) {
			foreach ( $conditionals as $conditional ) {
				if ( call_user_func( $conditional ) ) {
					call_user_func( array( $this, 'add_crumbs_' . substr( $conditional, 3 ) ) );
					break;
				}
			}

			$this->search_trail();
			$this->paged_trail();

			return $this->get_breadcrumb();
		}

		return array();
	}

	/**
	 * is home trail
	 */
	private function add_crumbs_home() {
		$this->add_crumb( single_post_title( '', false ) );
	}

	/**
	 * 404 trail
	 */
	private function add_crumbs_404() {
		$this->add_crumb( __( 'Error 404', 'solidres' ) );
	}

	/**
	 * attachment trail
	 */
	private function add_crumbs_attachment() {
		global $post;

		$this->add_crumbs_single( $post->post_parent, get_permalink( $post->post_parent ) );
		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Single post trail
	 *
	 * @param int    $post_id
	 * @param string $permalink
	 */
	private function add_crumbs_single( $post_id = 0, $permalink = '' ) {
		if ( ! $post_id ) {
			global $post;
		} else {
			$post = get_post( $post_id );
		}

		$this->add_crumb( get_the_title( $post ), $permalink );
	}

	/**
	 * Page trail
	 */
	private function add_crumbs_page() {
		global $post;

		if ( $post->post_parent ) {
			$parent_crumbs = array();
			$parent_id     = $post->post_parent;

			while ( $parent_id ) {
				$page          = get_post( $parent_id );
				$parent_id     = $page->post_parent;
				$parent_crumbs[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
			}

			$parent_crumbs = array_reverse( $parent_crumbs );

			foreach ( $parent_crumbs as $crumb ) {
				$this->add_crumb( $crumb[0], $crumb[1] );
			}
		}

		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Post type archive trail
	 */
	private function add_crumbs_post_type_archive() {
		$post_type = get_post_type_object( get_post_type() );

		if ( $post_type ) {
			$this->add_crumb( $post_type->labels->singular_name, get_post_type_archive_link( get_post_type() ) );
		}
	}

	/**
	 * Category trail
	 */
	private function add_crumbs_category() {
		$this_category = get_category( $GLOBALS['wp_query']->get_queried_object() );

		if ( 0 != $this_category->parent ) {
			$this->term_ancestors( $this_category->parent, 'post_category' );
			$this->add_crumb( $this_category->name, get_category_link( $this_category->term_id ) );
		}

		$this->add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
	}

	/**
	 * Tag trail
	 */
	private function add_crumbs_tag() {
		$queried_object = $GLOBALS['wp_query']->get_queried_object();
		$this->add_crumb( sprintf( __( 'Posts tagged &ldquo;%s&rdquo;', 'solidres' ), single_tag_title( '', false ) ), get_tag_link( $queried_object->term_id ) );
	}

	/**
	 * Add crumbs for date based archives
	 */
	private function add_crumbs_date() {
		if ( is_year() || is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		}
		if ( is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
		}
		if ( is_day() ) {
			$this->add_crumb( get_the_time( 'd' ) );
		}
	}

	/**
	 * Add a breadcrumb for author archives
	 */
	private function add_crumbs_author() {
		global $author;

		$userdata = get_userdata( $author );
		$this->add_crumb( sprintf( __( 'Author: %s', 'solidres' ), $userdata->display_name ) );
	}

	/**
	 * Add crumbs for a term
	 * @param string $taxonomy
	 */
	private function term_ancestors( $term_id, $taxonomy ) {
		$ancestors = get_ancestors( $term_id, $taxonomy );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, $taxonomy );

			if ( ! is_wp_error( $ancestor ) && $ancestor ) {
				$this->add_crumb( $ancestor->name, get_term_link( $ancestor ) );
			}
		}
	}

	/**
	 * Add a breadcrumb for search results
	 */
	private function search_trail() {
		if ( is_search() ) {
			$this->add_crumb( sprintf( __( 'Search results for &ldquo;%s&rdquo;', 'solidres' ), get_search_query() ), remove_query_arg( 'paged' ) );
		}
	}

	/**
	 * Add a breadcrumb for pagination
	 */
	private function paged_trail() {
		if ( get_query_var( 'paged' ) ) {
			$this->add_crumb( sprintf( __( 'Page %d', 'solidres' ), get_query_var( 'paged' ) ) );
		}
	}
}
