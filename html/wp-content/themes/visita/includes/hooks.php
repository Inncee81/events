<?php
/**
* Visita - Theme Hooks
*
* @file hooks.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/includes/hooks.php
* @since available since 0.1.0
*/

/**
* Add metag data per content if data is available
*/
function visita_add_head_metatags( ) {
  echo '<link rel="dns-prefetch" href="//cdn.apixu.com" />' . "\n";
  echo '<link rel="dns-prefetch" href="//s.visita.vegas" />' . "\n";
  echo '<link rel="dns-prefetch" href="//cdn.onesignal.com" />' . "\n";
  echo '<link rel="dns-prefetch" href="//fonts.gstatic.com" />' . "\n";
  echo '<link rel="dns-prefetch" href="//fonts.googleapis.com" />' . "\n";
  echo '<link rel="dns-prefetch" href="//pagead2.googlesyndication.com" />' . "\n";
}
add_action( 'wp_head', 'visita_add_head_metatags', 2 );

/**
*
*/
function visita_theme_setup( ) {
  global $content_width, $visita_options;

  /**
  * Global content width.
  * @see http://codex.wordpress.org/Content_Width
  */
  if ( ! isset( $content_width ) )
    $content_width = 640;

  /**
  * Global time date format
  */
  $visita_options['date_time_format'] = get_option('date_format') . ' ' . get_option('time_format');

  /**
  * Add your files into /languages/ directory.
  * @see http://codex.wordpress.org/Function_Reference/load_theme_textdomain
  */
  load_theme_textdomain( 'visita', get_template_directory() . '/languages' );

  /**
  * Add custom TinyMCE editor stylesheets. (editor-style.css)
  * @see https://developer.wordpress.org/reference/functions/add_editor_style/
  */
  add_editor_style( 'editor-style.css' );

  /**
  * Enables post and comment RSS feed links to head.
  * @see https://developer.wordpress.org/reference/functions/add_theme_support/#feed-links
  */
  add_theme_support( 'automatic-feed-links' );

  /**
  * Enables dynamic page title tag
  * @see https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
  */
  add_theme_support( 'title-tag' );

  /**
  * Switches default core markup for search form to output valid HTML5.
  * @see https://developer.wordpress.org/reference/functions/add_theme_support/#html5
  */
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

  /*
  * This theme supports all available post formats by default.
  * See http://codex.wordpress.org/Post_Formats
  */
  add_theme_support( 'post-formats', array(
    'aside', 'audio', 'image', 'video'
  ) );

  /**
  * Enables post-thumbnail support for a theme.
  * @see https://developer.wordpress.org/reference/functions/add_theme_support/#Post_Thumbnails
  */
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 800, 320, true );
  add_image_size( 'featured-mobile', 600,  240, true );

  // This theme uses its own gallery styles.
  add_filter( 'use_default_gallery_style', '__return_false' );

  /**
  * This feature enables custom-menus support for a theme.
  * @see http://codex.wordpress.org/Function_Reference/register_nav_menus
  */
  register_nav_menus( array(
      'top'         => __( 'Top Menu', 'visita' ),
      'social'      => __( 'Social Menu', 'visita' ),
      'primary'     => __( 'Header Menu', 'visita' ),
      'footer'      => __( 'Footer Menu', 'visita' ),
    )
  );
}
add_action( 'after_setup_theme', 'visita_theme_setup' );

/**
* Register theme's widget areas
*
* @see http://codex.wordpress.org/Function_Reference/register_sidebar
* @return void
*/
function visita_widgets_init( ) {
  register_sidebar( array(
		'name'          	=> __( 'Main Widget Area', 'visita' ),
		'id'            	=> 'sidebar',
		'description'   	=> __( 'Main sidebar: displays on pages and posts.', 'visita' ),
		'before_widget' 	=> '<div id="%1$s" class="widget %2$s">',
		'after_widget'  	=> '</div>',
		'before_title'  	=> '<h3 class="widget-title">',
		'after_title'   	=> '</h3>',
	) );
}
add_action( 'widgets_init', 'visita_widgets_init' );

/**
* Load theme's stylessheets
*
* @see http://codex.wordpress.org/Function_Reference/wp_enqueue_style
* @return void
*/
function visita_style_enqueues( ) {

  if ( is_admin() ) {
    return;
  }

  $theme 	= wp_get_theme();

  wp_register_style( 'font-open-sans', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500' );
  wp_register_style( 'visita', get_template_directory_uri() . "/style.css", false, $theme->version, 'all' );
}
add_action( 'wp_enqueue_scripts', 'visita_style_enqueues' );

/**
*
*/
function visita_inline_styles( ) {

  if ( $content = file_get_contents( get_template_directory() . "/inline.css") ) {
    echo "<style>{$content}</style>";
  }

  echo "<noscript>\n"; wp_print_styles( 'font-open-sans' ); wp_print_styles( 'visita' ); echo "</noscript>\n";
}
add_action( 'wp_head', 'visita_inline_styles', 100 );


/**
* Load theme's JavaScript
*
* @see http://codex.wordpress.org/Function_Reference/wp_enqueue_script
* @return void
*/
function visita_scripts_enqueues( ) {

  if ( is_admin() ) {
    return;
  }

  $theme = wp_get_theme();

  global $wp_scripts;

  // Loads the Internet Explorer specific stylesheet.
  // see: http://codex.wordpress.org/Conditional_Comment_CSS
  // see: http://wordpress.stackexchange.com/questions/54327/register-and-enqueue-conditional-browser-specific-javascript-files
  wp_enqueue_script( 'visita-html5', get_template_directory_uri() . '/js/html5.js', false, $theme->version );
  $wp_scripts->add_data( 'visita-html5', 'conditional', ' lt IE 9' );

  // Loads JavaScript file.
  wp_enqueue_script( 'visita', get_template_directory_uri() . '/js/visita.js', array( 'jquery' ), $theme->version, true );

  wp_localize_script( 'visita', 'visita', array(
    'C' => __( 'celsius', 'visita' ),
    'F' => __( 'freiheit', 'visita' ),
    'fonts' => "https://fonts.googleapis.com/css?family=Roboto:300,400,500",
    'styles' => get_template_directory_uri() . "/style.css?ver=" . $theme->version,
    'tablet' => get_template_directory_uri() . "/tablet.css?ver=" . $theme->version,
  ) );
}
add_action( 'wp_enqueue_scripts', 'visita_scripts_enqueues' );

/**
*
*/
function visita_jquery_footer( &$wp_scripts ) {
  if ( is_admin() ) {
    return;
  }
  $wp_scripts->add_data( 'jquery', 'group', 1 );
  $wp_scripts->add_data( 'jquery-core', 'group', 1 );
  $wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
}
add_action( 'wp_default_scripts', 'visita_jquery_footer', 100 );


/**
* Add front-end body classes
*
* @param array $atts default body classes.
* @return array with body classes.
*/
function visita_body_class( $classes ){

  if ( is_admin() ) {
    return;
  }

  if ( $them_name = get_option( 'template' ) ) {
    $classes[] = $them_name;
  }

  if ( is_active_sidebar( 'sidebar' ) ) {
    $classes[] = 'sidebar';
  }

  if ( is_front_page() || is_post_type_archive() ){
    $classes[] = 'archive';
  }

  return array_unique( $classes );
}
add_filter( 'body_class', 'visita_body_class' );

/**
* Add scroll up link at the bottom of the content area #main
*
* @return void.
*/
function visita_add_scroll_up_button( ) {
  printf(
    '<a href="#" class="window-scroll-up" title="%1$s" rel="nofollow">%1$s</a>',
    esc_attr__( 'Move Up', 'visita' )
  );
}
add_filter( 'visita_after_main', 'visita_add_scroll_up_button' );

/**
* Dynamic loading images
*/
function visita_attachment_image_attributes( $attr, $attachment, $size ){

  if ( get_query_var( 'amp' ) ) {
    return $attr;
  }

  if ( $size == 'post-thumbnail' ) {
    $attr['src'] = get_stylesheet_directory_uri() . '/img/1x1.trans.gif';

    if ( isset( $attr['srcset'] ) && $mobile = wp_get_attachment_image_src( $attachment->ID, 'featured-mobile' ) ) {
      $attr['data-srcset'] = $attr['srcset'];
    } else if ( empty( $attr['srcset'] ) ) {
      if ( $image_src = wp_get_attachment_image_src( $attachment->ID, 'featured-mobile' ) ) {
        $attr['data-srcset'] = $image_src[0] . ' ' . $image_src[1] . 'w';
      }
    }

    foreach ( array( 'sizes', 'srcset' ) as $attribute ) {
      if ( isset( $attr[$attribute] ) ) unset( $attr[$attribute] );
    }
  }

  return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'visita_attachment_image_attributes', 50, 3 );

/**
* Add schema.org data
*
* @return void.
*/
function visita_organization_schema( ) {

  // add it only to the home page
  if ( ! is_front_page() ) {
    return;
  }

  printf(
    '<script type="application/ld+json">%1$s</script>',
    json_encode(
      array(
        '@context'        => 'http://schema.org',
        '@type'           => 'Organization',
        'url'             => 'https://visita.vegas/',
        'name'            => 'Visita Las Vegas;',
        'description'     => 'Eventos, antros, clubs, discotecas en Las Vegas en español, música, banda, salsa, bachata y rock.',
        'logo'            => array(
          '@context'      => 'http://schema.org',
          '@type'         => 'ImageObject',
          'url'           => get_stylesheet_directory_uri() .'/icon/icon-144.png',
          'width'         => 144,
          'height'        => 144
        ),
        'aggregateRating' => array(
          '@type'         => 'AggregateRating',
          'ratingValue'   => '4.3',
          'reviewCount'   => '1357'
        ),
        'sameAs' => array(
          'https://twitter.com/visita_vegas',
          'https://plus.google.com/106513602005672773042',
          'https://www.facebook.com/VisitaVegas-622985901191676/',
          'https://www.youtube.com/channel/UCjvmES5xtmea20CQnw2-m7A'
        )
      )
    )
  );
}
add_action( 'wp_footer', 'visita_organization_schema', 5 );

/**
*
*
* @return void.
*/
function visita_get_the_archive_title( $title ) {
  if ( is_post_type_archive() ) {
    return post_type_archive_title( '', false ) ;
  }
  return $title;
}
add_filter( 'get_the_archive_title', 'visita_get_the_archive_title' );

/**
* Add metag data per content if data is available
*/
function visita_head_metatags( ) {
  echo '<meta name="twitter:site" content="@visita_vegas">' . "\n";

  if ( is_front_page() ){
    echo '<meta name="description" content="Visita Las Vegas (Guía de turismo) Eventos, Shows, Atracciones, Conciertos, Clubs, Hoteles" />' . "\n";
    echo '<meta name="twitter:description" content="Visita Las Vegas (Guía de turismo) Eventos, Shows, Atracciones, Conciertos, Clubs, Hoteles" />' . "\n";
  }

  if ( is_tax() && $description = term_description() ) {
    echo '<meta name="description" content="' . esc_attr( trim( strip_tags( $description ) ) ) . '"  />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( trim( strip_tags( $description ) ) ) . '"  />' . "\n";
  }

  if ( is_singular() && $description = get_post_meta( get_the_ID(), '_description', true ) ){
    echo '<meta name="twitter:url" content="' . get_permalink() . '"/>' . "\n";
    echo '<meta name="twitter:title" content="'. esc_attr( get_the_title() ) .'">' . "\n";
    echo '<meta name="description" content="' . esc_attr( strip_tags( $description ) ) . '"  />' . "\n";
    echo '<meta name="twitter:description" content="'.  esc_attr( strip_tags( $description ) ) .'">' . "\n";
    echo '<meta name="og:description" content="'.  esc_attr( strip_tags( $description ) ) .'">' . "\n";
  }

	if ( is_front_page() || is_tax() ){
		echo '<meta property="og:image" content="'. site_url( '/wp-content/themes/visita/img/visita.jpg' )  .'" />'. "\n";
		echo '<meta name="twitter:image:src" content="'. site_url( '/wp-content/themes/visita/img/visita.jpg' )  .'">'. "\n";
    echo '<link rel="image_src" href="' . site_url( '/wp-content/themes/visita/img/visita.jpg' )  . '"  />' . "\n";
	}

  if ( has_post_thumbnail( get_the_ID() ) && is_singular() ) {
    if( $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),  'large' ) ) {
      echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
      echo '<meta name="twitter:image:src" content="'. esc_url( $image[0] )  .'">'. "\n";
      echo '<meta property="og:image" content="'. esc_url( $image[0] )  .'" />'. "\n";
      echo '<meta property="og:image:width" content="'. esc_attr( $image[1] )  .'" />'. "\n";
      echo '<meta property="og:image:height" content="'. esc_attr( $image[2] )  .'" />'. "\n";
      echo '<link rel="image_src" href="' . esc_url( $image[0] ) . '"  />' . "\n";
    }
  }
}
add_action( 'visita_site_metatags', 'visita_head_metatags', 20 );
