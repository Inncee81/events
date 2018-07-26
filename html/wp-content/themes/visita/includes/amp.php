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

  $data['amp_component_scripts'] =
  array_merge( $data['amp_component_scripts'], array(
    'amp-ad' => 'https://cdn.ampproject.org/v0/amp-ad-0.1.js',
    'amp-sidebar' => 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js',
    'amp-auto-ads' => 'https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js',
    'amp-analytics' => 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js',
    'amp-youtube' => 'https://cdn.ampproject.org/v0/amp-youtube-0.1.js',
  ) );

  if (is_single()) {
    $data['body_class'] .= 'single';
  }

  $data['body_class'] .= ' amp-' . get_post_type();
  if ( ! empty( $data['amp_component_scripts']['amp-carousel'] ) ) {
    $data['body_class'] .= ' amp-carousel';
  }

  return $data;
}
add_action( 'amp_post_template_data', 'visita_amp_post_template_data', 500 );

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


/**
* Display single post navigation
*
* @param string $class class attribute to identify menu location
* @return void
*/
function visita_amp_post_nav( $class = '' ){

  // Don't print empty markup if there's nowhere to navigate.
  $previous 	= ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
  $next     	= get_adjacent_post( false, '', false);

  if ( ! $next && ! $previous ) return;
  ?>
  <nav class="navigation post-navigation <?php echo $class ?>">
    <h6 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'xclusive' ); ?></h6>
    <div class="nav-links">
      <div class="nav-previous">
        <?php if ($previous): ?>
          <a href="<?php echo esc_url(amp_get_permalink($previous->ID)) ?>" rel="previous">
            <?php echo wp_kses_data($previous->post_title) ?>
          </a>
        <?php endif ?>
      </div>
      <div class="nav-next">
        <?php if ($next): ?>
          <a href="<?php echo esc_url(amp_get_permalink($next->ID)) ?>" rel="next">
            <?php echo wp_kses_data($next->post_title) ?>
          </a>
        <?php endif ?>
      </div>
    </div><!-- .nav-links -->
  </nav><!-- .navigation -->
  <?php
}
