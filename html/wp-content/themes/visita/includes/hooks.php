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

  wp_enqueue_style( 'Karla', 'https://fonts.googleapis.com/css?family=Karla:400,700' );
  wp_enqueue_style( 'font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,700' );
  wp_enqueue_style( 'visita', get_template_directory_uri() . "/style.css", false, $theme->version, 'all' );

  wp_localize_script( 'visita', 'visita', array(
    'C' => __( 'celsius', 'visita' ),
    'F' => __( 'freiheit', 'visita' ),
  ) );

}
add_action( 'wp_enqueue_scripts', 'visita_style_enqueues' );

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

  /*
  * Adds JavaScript to pages with the comment form to support sites with
  * threaded comments (when in use).
  */
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  global $wp_scripts;

  // Loads the Internet Explorer specific stylesheet.
  // see: http://codex.wordpress.org/Conditional_Comment_CSS
  // see: http://wordpress.stackexchange.com/questions/54327/register-and-enqueue-conditional-browser-specific-javascript-files
  wp_enqueue_script( 'visita-html5', get_template_directory_uri() . '/js/html5.js', false, $theme->version );
  $wp_scripts->add_data( 'visita-html5', 'conditional', ' lt IE 9' );

  // Loads JavaScript file with functionality specific to Twenty Thirteen.
  wp_enqueue_script( 'visita-script', get_template_directory_uri() . '/js/visita.js', array( 'jquery' ), $theme->version, true );
}
add_action( 'wp_enqueue_scripts', 'visita_scripts_enqueues' );


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
add_filter( 'visita_after_content', 'visita_add_scroll_up_button' );


/**
* Add schema.org data
*
* @return void.
*/
function organization_schema( ) {

  // add it only to the home page
  if ( ! is_front_page() ) {
    return;
  }

  printf(
    '<script type="application/ld+json">%1$s</script>',
    json_encode(
      array(
        '@context'    => 'http://schema.org',
        '@type'       => 'Organization',
        'url'         => 'https://visita.vegas/',
        'name'        => 'Visita Las Vegas;',
        'description' => 'Eventos, antros, clubs, discotecas en Las Vegas en español, música, banda, salsa, bachata y rock.',
        'logo'        => array(
          '@context'    => 'http://schema.org',
          '@type'       => 'ImageObject',
          'url'         => get_stylesheet_directory_uri() .'/icon/icon-144.png',
          'width'       => 144,
          'height'      => 144
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
add_action ( 'wp_footer',  'organization_schema', 5 );
