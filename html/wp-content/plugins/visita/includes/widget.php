<?php

/**
 * Image Store - image widget
 *
 * @file widget.php
 * @package Image Store
 * @author Hafid Trujillo
 * @copyright 2010-2015
 * @filesource  wp-content/plugins/image-store/_inc/widget.php
 * @since 0.5.3
 */

class Visita_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 * @since 0.5.3
	 */
	function __construct( ) {
		$widget_ops = array(
			'classname' => 'visita-widget',
			'description' => __( 'Display Random Content', 'admin' )
		);
		parent::__construct( 'visita-widget', __( 'Visita Widget', 'admin' ), $widget_ops);
	}

	/**
	 * Configuration form.
	 *
	 * @return void
	 * @since 0.5.3
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, array( 'title' => NULL ) );
	}

	/**
	 * Display widget.
	 *
	 * @return void
	 * @since 0.5.3
	 */
	function widget( $args, $instance ) {

		extract( $args );
		extract( $instance );
		$type = ( $type = get_post_type() ) ? $type : 'event';

		echo $before_widget . "\n";
		echo $before_title . $title . $after_title . "\n";

		$language = false;
		if ( function_exists( 'pll_current_language') ) {
			if ( $term_id = pll_current_language( 'term_id') ) {
				$language = array(
					'operator' => 'IN',
					'field'    => 'term_id',
					'taxonomy' => 'language',
					'terms'	=> array( $term_id )
				);
			}
		}

		$events = new WP_Query( array(
			'post_type' => $type,
			'orderby' => 'rand',
			'posts_per_page' => 4,
			'post__not_in' => array( get_the_ID() ),
			'tax_query' => array(	array(
				'taxonomy' => 'events',
				'field'    => 'term_id',
				'operator' => 'NOT IN',
				'terms'		 => array( 44 ),
			), $language ),
		) );

		global $wp_query;

		$wp_query->is_single = false;
		$wp_query->is_singular = false;

		while ( $events->have_posts() ) { $events->the_post();
			get_template_part( 'content', get_post_type() );
		} wp_reset_postdata();

		$wp_query->is_single = true;
		$wp_query->is_singular = true;

		echo $after_widget . "\n";
	}
}
