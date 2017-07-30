<?php
/**
* Visita - Theme Functions
*
* @file functions.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/includes/functions.php
* @since available since 0.1.0
*/

/**
* Display single post navigation
*
* @param $uri basename
* @return void
*/
if ( ! function_exists( 'stylesheet_directory_uri' ) ) {
  function stylesheet_directory_uri( $uri = '' ) {
    echo get_stylesheet_directory_uri() . $uri;
  }
}

/**
* Just before opening <div id="page">
*
* @param $string string
* @see hooks.php
* @return string
*/
if ( ! function_exists( 'visita_get_css_units' ) ) {
  function visita_get_css_units( $string ) {
    return intval( trim( $string ) ) . ( ( strpos( $string , '%' ) === false ) ? 'px' : '%' ) ;
  }
}

/**
* Within the <head> tag
*
* @see header.php
* @return void
*/
if ( ! function_exists( 'visita_site_metatags' ) ) {
  function visita_site_metatags( $uri = '' ) {
    do_action( 'visita_site_metatags' );
  }
}

/**
* After the <body> tag
*
* @see header.php
* @return void
*/
if ( ! function_exists( 'visita_before_page' ) ) {
  function visita_before_page( $uri = '' ) {
    do_action( 'visita_before_page' );
  }
}

/**
* Display share toolbar
*
* @return void
*/
function visita_share_botton( ) {

  $links = '';
  $sharelinks = array(
    'em' => array( 'name' => __( 'eMail', 'visita' ), 'url' => 'mailto:?body=%s&subject=%s' ),
    'fb' => array( 'name' => __( 'Facebook', 'visita' ), 'url' => 'https://www.facebook.com/sharer/sharer.php?u=%s' ),
    'gp' => array( 'name' => __( 'Google+', 'visita' ), 'url' => 'https://plus.google.com/share?url=%s' ),
    'tw' => array( 'name' => __( 'Twitter', 'visita' ), 'url' => 'http://twitter.com/share?url=%s&text=%s' ),
    'ri' => array( 'name' => __( 'Reddit', 'visita' ), 'url' => 'http://www.reddit.com/submit/?url=%s&title=%s' ),
    'su' => array( 'name' => __( 'StumbleUpon', 'visita' ), 'url' => 'http://www.stumbleupon.com/submit?url=%s&title=%s' ),
  );

  foreach ( $sharelinks as $share => $data ) {
    $links .= sprintf(
      '<a href="%1$s" title="%2$s" class="%3$s" target="_blank" rel="nofollow">%2$s</a>',
      esc_attr( sprintf( $data['url'], urlencode( get_permalink() ), urlencode( get_the_title() ) ) ),
      esc_attr( $data['name'] ),
      esc_attr( 'sh-' . $share )
    );
  }

  return '<span class="share_button" tabindex="0"><span class="sh-links">' . $links . '</span></span>';
}
