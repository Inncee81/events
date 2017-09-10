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
*
*/
function visita_after_main( ) {
  do_action( 'visita_after_main' );
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

/**
* Display default social navigation
*
* uses visita_render_menu()
* @param array $args menu argumens see: http://codex.wordpress.org/Function_Reference/wp_nav_menu#Usage
* @return string HTML
*/
function visita_default_social_menu( $args = array() ){
  if( $args['theme_location'] != 'social' || empty( $args ) ) {
    return $args;
  }

  $links 			  = '';
  $social_links = array(
    'Email'     => 'mailto:support@visita.vegas',
    'Facebook'  => '//www.facebook.com/VisitaVegas/',
    'Twitter'   => '//twitter.com/visita_vegas',
    'RSS'       => '/feed/',
  );

  foreach( $social_links as $name => $link ){
    $links .= sprintf(
      '<li class="social-%2$s">
        <a class="fa fa-%2$s" href="%1$s" title="%2$s" rel="nofollow" target="_blank"></a>
      </li>',
      esc_url( $link ),
      esc_attr( strtolower( $name ) ),
      esc_html( $name )
    );
  }

  printf(
    '<ul class="%1$s">%2$s</ul>',
    esc_attr( $args['menu_class'] ),
    $links
  );
}

/**
* Display default social navigation
*
* uses paginate_links()
* @param string $css additional class
* @return string HTML
*/
function visita_content_nav( $css ) { ?>
  <nav class="<?php echo esc_attr("navigation paging-navigation $css")?>">
    <h3 class="screen-reader-text"><?php esc_html_e( 'Navigation', 'visita' ); ?></h3>
    <div class="nav-links">

      <?php echo paginate_links( array(
        'mid_size' => 1,
        'prev_text'=> '',
        'next_text' => '',
      )) ?>

    </div><!-- .nav-links -->
  </nav><!-- .navigation --><?php
}

/**
* Display entry metadata information
*
* @return void
*/
function visita_entry_meta(){
  printf(
    '<span class="author vcard hidden">
      <a class="url fn" href="%1$s" title="%2$s" rel="author">%3$s</a>
    </span>',
    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
    esc_attr( sprintf( __( 'View all posts by %s', 'visita' ), get_the_author() ) ),
    get_the_author()
  );

  visita_get_start_time();
}

/**
*
*
* @return void
*/
function visita_post_schema( ) {
  printf(
    'itemscope itemtype="https://schema.org/%1$s"',
    esc_attr(get_post_meta( get_the_ID(), '_event_type', true ))
  );
}

/**
*
*
* @return void
*/
function visita_get_start_time( ) {

  global $visita_options;
  $ends = strtotime( get_post_meta( get_the_ID(), '_ends', true ) );
  $starts = strtotime( get_post_meta( get_the_ID(), '_starts', true ) );

  printf(
    '<div class="date">
      <time class="entry-date updated hidden" datetime="%3$s">%3$s</time>
      <time itemprop="startDate" class="dtstart" datetime="%4$s">%1$s</time>
      <time itemprop="endDate" class="dtend hidden" datetime="%5$s">%2$s</time>
    </div>',
    esc_html( date_i18n( $visita_options['date_time_format'], $starts ) ),
    esc_html( date_i18n( $visita_options['date_time_format'], $ends ) ),
    esc_attr( get_the_date('c') ),
    esc_attr( $starts ),
    esc_attr( $ends )
  );

}

/**
*
*
* @return void
*/
function visita_entry_tax( ) {

}
