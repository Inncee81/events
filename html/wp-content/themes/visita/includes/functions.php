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
*
*/
function visita_before_loop(){
  do_action( 'visita_before_loop' );
}

/**
*
*/
function visita_after_loop(){
  do_action( 'visita_after_loop' );
}

/**
*
*/
function visita_after_post_content( ) {
  do_action( 'visita_after_post_content' );
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
      '<a href="%1$s" title="%2$s" class="%3$s" target="_blank" rel="nofollow"></a>',
      esc_attr( sprintf( $data['url'], urlencode( get_permalink() ), urlencode( get_the_title() ) ) ),
      esc_attr( $data['name'] ),
      esc_attr( 'sh-' . $share )
    );
  }

  echo '<span class="share_button" tabindex="0"><span class="sh-links">' . $links . '</span></span>';
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
    <h6 class="screen-reader-text"><?php esc_html_e( 'Navigation', 'visita' ); ?></h6>
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
* Display single post navigation
*
* @param string $class class attribute to identify menu location
* @return void
*/
function visita_post_nav( $class = '' ){

 // Don't print empty markup if there's nowhere to navigate.
$previous 	= ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
$next     	= get_adjacent_post( false, '', false);

  if ( ! $next && ! $previous )
  return;
?>
<nav class="navigation post-navigation <?php echo $class ?>">
  <h6 class="screen-reader-text"><?php esc_html_e( 'Post navigation', 'xclusive' ); ?></h6>
  <div class="nav-links">

    <div class="nav-previous"><?php previous_post_link( '%link', _x( '%title', 'Previous post link', 'xclusive' ) ); ?></div>
    <div class="nav-next"><?php next_post_link( '%link', _x( '%title', 'Next post link', 'xclusive' ) ); ?></div>

  </div><!-- .nav-links -->
</nav><!-- .navigation -->
<?php
}


/**
*
*
* @return void
*/
function visita_post_schema( $type ) {
  if ( $type = get_post_meta( get_the_ID(), $type, true ) ) {
    printf(
      'itemscope itemtype="https://schema.org/%1$s"',
      esc_attr( $type )
    );
  }
}

/**
*
*
* @return string
*/
function visita_get_external_link( ) {
  $link = '#';

  if ( $_link = get_post_meta( get_the_ID(), '_link', true ) ) {
    $link = ( strrpos( $_link, '?' ) === false ? "$_link?" : "$_link&" );
    if ( ! get_post_meta( get_the_ID(), '_disable_source', true ) ) {
      $link .= 'utm_source=visita.vegas&utm_medium=refer&utm_campaign=visita_vegas';
    }
  }

  return $link;
}

/**
*
*
* @return string
*/
function visita_format_price( $price, $symbol = '$' ) {
  return ( is_numeric( $price ) ? $symbol . number_format( $price ) : $price );
}

/**
*
*
* @return void
*/
function visita_entry_tax( $taxonomy ) {
  if ( $taxonomy_list = get_the_term_list( get_the_ID(), $taxonomy, '', '' ) ) {
    if ( ! is_wp_error( $taxonomy_list ) ) {
      echo $taxonomy_list = '<div class="taxonomy-links">' . $taxonomy_list . '</div>';
    }
  }
}

/**
*
*
* @return void
*/
function visita_get_description( ) {
  if ( $description = get_post_meta( get_the_ID(), '_description', true ) ) {
    printf(
      '<div class="hidden">%1$s</div>',
      esc_html( $description )
    );
  }
}

/**
*
*
* @return void
*/
function visita_get_performers( ) {
  if ( $performers = get_post_meta( get_the_ID(), '_performers', true ) ) {
    foreach ( $performers as $performer ) {
      printf(
        '<div itemprop="performer" itemscope itemtype="http://schema.org/%1$s">
          <meta itemprop="name" content="%2$s" />
        </div>',
        esc_attr( $performer['_type'] ),
        esc_attr( empty( $performer['_name'] ) ? get_the_title() : $performer['_name'] )
      );
    }
  }
}

/**
*
*
* @return void
*/
function visita_price_range( $itemprop = false ) {
  $price_max = get_post_meta( get_the_ID(), '_price_max', true );
  printf(
    '<div %3$s class="price">%1$s %2$s</div>',
    esc_html( visita_format_price( get_post_meta( get_the_ID(), '_price', true ) ) ),
    esc_html( ( $price_max ) ? "- " . visita_format_price( $price_max ) : '' ),
    ( ( $itemprop ) ? 'itemprop="priceRange"' : '' )
  );
}

/**
* Display entry metadata information
*
* @return void
*/
function visita_entry_meta( ) {

  printf(
    '<a href="%7$s" rel="external" target="_blank" class="venue"><span itemprop="name">%1$s</span></a>
      <address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress" class="address">
        <a href="%7$s" rel="external" target="_blank">
          <span itemprop="streetAddress" class="street">%2$s</span>
          <span itemprop="addressLocality" class="city">%3$s</span>
          <span itemprop="addressRegion" class="state">%4$s</span>
          <span itemprop="postalCode" class="zip hidden">%5$s</span>
        </a>
      </address>' . '' .'
    <span itemprop="telephone" class="phone tel hidden">%6$s</span>',
    esc_html( get_post_meta( get_the_ID(), '_location', true ) ),
    esc_html( get_post_meta( get_the_ID(), '_street', true ) ),
    esc_html( get_post_meta( get_the_ID(), '_city', true ) ),
    esc_html( get_post_meta( get_the_ID(), '_state', true ) ),
    esc_html( get_post_meta( get_the_ID(), '_zip', true ) ),
    esc_html( get_post_meta( get_the_ID(), '_phone', true ) ),
    esc_url( 'https://www.google.com/maps/search/' .
      implode( '+',
        explode(' ',
          get_post_meta( get_the_ID(), '_location', true ) . '+' .
          get_post_meta( get_the_ID(), '_street', true ) . ' ' .
          get_post_meta( get_the_ID(), '_city', true ) . ' ' .
          get_post_meta( get_the_ID(), '_state', true ) . ' ' .
          get_post_meta( get_the_ID(), '_zip', true )
        )
      )
    )
  );
}

/**
*
*
* @return void
*/
function visita_get_start_time( ) {

  global $visita_options;

  $starts = get_post_meta( get_the_ID(), '_starts', true );
  if ( ! $ends = get_post_meta( get_the_ID(), '_ends', true ) ) {
    $ends = $starts + 120; // 2 more hours
  }

  printf(
    '<div class="date">
      <time class="updated hidden" datetime="%7$s">%6$s</time>
      <time class="entry-date published hidden" datetime="%3$s">%3$s</time>
      <time itemprop="startDate" class="dtstart" datetime="%4$s">%1$s</time>
      <time itemprop="endDate" class="dtend hidden" datetime="%5$s">%2$s</time>
    </div>',
    esc_html( date_i18n( $visita_options['date_time_format'], $starts ) ),
    esc_html( date_i18n( $visita_options['date_time_format'], $ends ) ),
    esc_attr( get_the_date( 'c' ) ),
    esc_attr( date_i18n( 'c', $starts ) ),
    esc_attr( date_i18n( 'c', $ends ) ),
    esc_attr( get_the_modified_date( ) ),
    esc_attr( get_the_modified_date( 'c' ) )
  );
}

/**
*
*
* @return void
*/
function visita_get_post_date( ) {
  printf(
    '<div class="date">
      <time itemprop="dateModified" class="updated hidden" datetime="%4$s">%3$s</time>
      <time itemprop="dateCreated" class="entry-date published" datetime="%2$s">%1$s</time>
    </div>',
    esc_html( get_the_date( ) ),
    esc_attr( get_the_date( 'c' ) ),
    esc_attr( get_the_modified_date( ) ),
    esc_attr( get_the_modified_date( 'c' ) )
  );
}

/**
*
*
* @return void
*/
function visita_event_dates( ) {
  if ( $price = get_post_meta( get_the_ID(), '_price', true ) ) {

    $starts = get_post_meta( get_the_ID(), '_starts', true );
    $max_price = get_post_meta( get_the_ID(), '_price_max', true );
    $times = (array) get_post_meta( get_the_ID(), '_times', true );

    foreach( $times as $time ) {

      $time = wp_parse_args( $time, array(
        '_availability' => 'InStock',
        '_time' => date_i18n('H:i', $starts),
        '_date' => date_i18n('Y-m-d', $starts),
      ));

      $today = strtotime( 'today' );
      $date = strtotime( $time['_date'] );

      printf(
        '<div itemprop="price" class="price" content="%4$s">
          <a class="price-action %8$s" href="%5$s" itemprop="url" rel="external">%2$s - %1$s</a>
          <link itemprop="availability" href="http://schema.org/%6$s" />
          <meta itemprop="priceCurrency" content="USD" />
          <meta itemprop="validFrom" content="%7$s" />
        </div>',

        esc_attr( date_i18n( get_option( 'time_format' ), strtotime( $time['_time'] ) ) ),
        esc_attr( date_i18n( 'j \d\e F Y', $date ) ),
        esc_html( get_post_meta( get_the_ID(), '_currency', true ) ), //
        esc_attr( is_numeric( $price ) ? $price : 0 ),
        esc_url( visita_get_external_link() ),
        esc_attr( $time['_availability'] ),
        esc_attr( get_the_date('c') ),
        esc_attr( $date < $today ? 'inactive' : '' )
      );
    }
  }
}

/**
*
*
* @return void
*/
function visita_opening_hours( ) {
  if ( $hours = get_post_meta( get_the_ID(), '_hours', true ) ) {
    foreach( $hours as $hour ){
      printf(
        '<div class="day">
          <meta itemprop="openingHours" content="%1$s %8$s-%7$s">
          <a class="action" href="%6$s" itemprop="url" rel="external">%2$s: %3$s %5$s %4$s</a>
        </div>',
        esc_attr( $hour['_day'] == 'all' ? __( 'Mo,Tu,We,Th,Fr,Sa,Su', 'visita' ) : date_i18n( 'D', strtotime( $hour['_day'] ) ) ),
        esc_attr( $hour['_day'] == 'all' ? __( 'Every Day', 'visita' ) : date_i18n( 'l', strtotime( $hour['_day'] ) ) ),
        esc_attr( $hour['_24h'] ? __( '24 Hours', 'visita' ) : '' ),
        esc_attr( $hour['_close'] ? date_i18n( get_option( 'time_format' ), strtotime( $hour['_close'] ) ) : '' ),
        esc_attr( $hour['_open'] ? date_i18n( get_option( 'time_format' ), strtotime( $hour['_open'] ) ) : '' ),
        esc_attr( visita_get_external_link() ),
        esc_attr( $hour['_close'] ? $hour['_close'] : '' ),
        esc_attr( $hour['_open'] ? $hour['_open'] : '' )
      );
    }
  } else {
    visita_legacy_time();
  }
}

/**
*
*
* @return void
*/
function visita_legacy_time( ) {
  if ( $days = get_post_meta( get_the_ID(), '_day', true ) ) {
    $close = get_post_meta( get_the_ID(), '_close', true );
    $open = get_post_meta( get_the_ID(), '_open', true );

    printf(
      '<div class="day">
        <meta itemprop="openingHours" content="%1$s %8$s-%7$s">
        <a class="action" href="%6$s" itemprop="url" rel="external">%2$s: %3$s %5$s %4$s</a>
      </div>',
      esc_attr( $days == 'All' ? __( 'Mo,Tu,We,Th,Fr,Sa,Su', 'visita' ) : date_i18n( 'D', strtotime( $days ) ) ),
      esc_attr( $days == 'All' ? __( 'Every Day', 'visita' ) : date_i18n( 'l', strtotime( $days ) ) ),
      esc_attr( get_post_meta( get_the_ID(), '_24h', true ) ? __( '- 24 Hours', 'visita' ) : '' ),
      esc_attr( $close ? date_i18n( get_option( 'time_format' ), strtotime( $close ) ) : '' ),
      esc_attr( $open ? date_i18n( get_option( 'time_format' ), strtotime( $open ) ) : '' ),
      esc_attr( visita_get_external_link() ),
      esc_attr( $close ? $close : '' ),
      esc_attr( $open ? $open : '' )
    );
  }
}

/**
*
*
* @return void
*/
function visita_get_time_range( ) {
  if ( $days = get_post_meta( get_the_ID(), '_days', true ) ) {

    $duration = get_post_meta( get_the_ID(), '_duration', true );

    foreach ($days as $day) {
      printf(
        '<div class="day">
           <meta itemprop="openingHours" content="%1$s %4$s - %5$s">
           <a class="action" href="" itemprop="url" rel="external">%2$s %3$s %4$s - %5$s</a>
        </div>',
        esc_attr( $day['_from'] == 'all' ? __( 'Mo,Tu,We,Th,Fr,Sa,Su', 'visita' ) : date_i18n( 'D', strtotime( $day['_from'] ) ) ),
        esc_attr( $day['_from'] == 'all' ? __( 'Every Day', 'visita' ) : date_i18n( 'l', strtotime( $day['_from'] ) ) ),
        esc_attr( $day['_to'] == 'all' ? '' : __(' to ', 'visita') . date_i18n( 'l', strtotime( $day['_to'] ) ) ),
        esc_attr( date_i18n( get_option( 'time_format' ), strtotime( $day['_time'] ) ) ),
        esc_attr( date_i18n( get_option( 'time_format' ), strtotime( $day['_time'] . " + $duration minutes" ) ) )

      );
    }
  }
}
