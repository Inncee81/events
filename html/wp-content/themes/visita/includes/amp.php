<?php
/**
* Visita - AMP support file
*
* @file amp.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.3
* @filesource  wp-content/themes/visita/includes/amp.php
* @since available since 0.1.9
*/

/**
* Add AMP support scripts
*
* @return void
*/
function visita_amp_post_template_data( $data ) {

  $data['amp_component_scripts'] = array(
    'amp-ad' => 'https://cdn.ampproject.org/v0/amp-ad-0.1.js',
    'amp-sidebar' => 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js',
    'amp-carouse' => 'https://cdn.ampproject.org/v0/amp-carousel-0.1.js',
    'amp-auto-ads' => 'https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js',
    'amp-analytics' => 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js',
    'amp-youtube' => 'https://cdn.ampproject.org/v0/amp-youtube-0.1.js',
  );

  $data['body_class'] .= 'amp-' . get_post_type();

  return $data;
}
add_action( 'amp_post_template_data', 'visita_amp_post_template_data' );

/**
* Add visita font
* @return void
*/
function visita_amp_post_template_add_fonts() {
  remove_action('amp_post_template_head', 'amp_post_template_add_fonts');
  echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,500,700">';
}
add_action( 'amp_post_template_head', 'visita_amp_post_template_add_fonts' );

/**
* add AMP Templates
*
* @return void
*/
function visita_amp_post_template_metadata( $metadata, $post ) {

  if ( $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID), 'original' ) ) {
    $metadata['image'] =  array_combine(
      array('@type', 'url', 'width', 'height'), array_slice( array_merge( array( 'ImageObject' ), $image ), 0, 4 )
    );
  }

  $metadata['publisher']['logo'] = array(
    '@context'  => 'http://schema.org',
    '@type'     => 'ImageObject',
    'url'       => get_stylesheet_directory_uri() . '/icons/icon-144.png',
    'width'     => '144',
    'height'    => '144'
  );

  return $metadata;
}
add_filter( 'amp_post_template_metadata', 'visita_amp_post_template_metadata', 10, 2 );

/**
* add AMP Templates
*
* @return void
*/
function visita_amp_set_custom_template( $file, $type, $post ) {

  if ( $type == 'single' && $template = locate_template(array(
    'amp/single-' .  get_post_type() . '.php',
    'amp/single.php'
  ), false ) ) {
    return $template;
  }

  return $file;
}
add_filter( 'amp_post_template_file', 'visita_amp_set_custom_template', 10, 3 );
