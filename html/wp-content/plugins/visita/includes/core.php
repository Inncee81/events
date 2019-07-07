<?php
/**
* Visita - Core Class
*
* @file core.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/core.php
* @since available since 0.1.0
*/

class Visita_Core {

  public $lang = 'es';
  public $weather_data = false;

  /**
   * Constructor
   *
   * @return void
   * @since 0.5.0
   */
  function __construct( ) {

    //
    add_action( 'wp', array( $this, 'display_widgets' ), 100 );
    add_action( 'init', array( $this, 'add_rewrite_rules' ), 100 );
    add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );
    add_action( 'widgets_init', array( $this, 'widgets_init' ), 100 );
    add_action( 'rest_api_init', array( $this, 'rest_api_register_routes') );
    add_action( 'visita_get_weather', array( $this, 'visita_get_weather' ) );
    add_action( 'after_setup_theme', array( $this, 'register_post_types'), 0 );
    add_filter( 'wpsc_protected_directories', array( $this, 'protect_json' ) );
    add_filter( 'pre_get_posts', array( $this, 'fix_polylang_searches' ), 100 );

    //disable acf save hook
    add_action( 'acf/init', array( $this, 'register_acf_fields' ) );
    add_action( 'acf/init', array( $this, 'disable_save_action' ) );
    add_filter( 'cron_schedules', array( $this, 'add_cron_schedule') );
    add_action( 'acf/save_post', array( $this, 'save_acf_data' ), 5 );

    //short code
    add_shortcode( 'clima', array( $this, 'shortcode_clima' ) );
    add_shortcode( 'lista-eventos', array( $this, 'shortcode_event_list' ) );

    //speed up wordpress
    if ( defined( 'DOING_AJAX' ) || defined( 'DOING_AUTOSAVE' ) ) {
      return;
    }

    if ( ! is_admin() ) {
      add_action( 'wp', array( $this, 'remove_code' ), 100 );
      add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
      add_filter( 'locale', array( $this, 'change_language'), 500 );
      add_action( 'parse_request', array( $this, 'parse_request_vars' ) );
      add_action( 'wp_footer', array( $this, 'schemaorg_breadcrumbs' ), 0 );
      return;
    }

    //
    add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
    add_filter( 'srm_max_redirects', array( $this, 'srm_max_redirects' ) );

    //actions
    add_action( 'admin_menu', array( $this, 'remove_unwanted_menus'), 100 );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 100 );

    //register hooks
    register_activation_hook( VISITA_FILE_NAME, array( $this, 'activate' ) );
    register_deactivation_hook( VISITA_FILE_NAME, array( $this, 'deactivate' ) );
  }

  /**
   *
   * @return void
   * @since 0.5.0
   */
  function register_post_types( ) {
    $this->show       = new VisitaShows();
    $this->club       = new VisitaClubs();
    $this->event      = new VisitaEvents();
    $this->hotel      = new VisitaHotels();
    $this->attraction = new VisitaAttractions();
  }

  /**
   *
   * @return void
   * @since 0.5.0
   */
  function activate( ) {
    wp_schedule_event( time(), 'every_two_half_hours', 'visita_get_weather', array('lang' => 'es') );
    wp_schedule_event( time(), 'every_two_half_hours', 'visita_get_weather', array('lang' => 'en') );
    do_action( 'visita_activate' );
  }

  /**
   *
   * @return void
   * @since 0.5.0
   */
  function deactivate( ) {
    do_action( 'visita_deactivate' );
    wp_clear_scheduled_hook( 'visita_get_weather', array('lang' => 'en') );
    wp_clear_scheduled_hook( 'visita_get_weather', array('lang' => 'es') );
  }

  /**
  * Add new cron scheule interval
  */
  function add_cron_schedule( $schedules ) {
    $schedules['every_two_half_hours'] = array(
      'interval'  => 9000,
      'display'   => __( 'Every 2 Hours', 'textdomain' )
    );
    return $schedules;
  }

  /**
  *
  */
  function load_plugin_textdomain() {
    load_plugin_textdomain( 'visita', false, plugin_basename( AMP__DIR__ ) . '/languages' );
  }

  /**
  *
  */
  function change_language( $locale ) {
    if ( isset( $_SERVER['REQUEST_URI'] ) && stripos( $_SERVER['REQUEST_URI'], 'en' ) == 1 ) {
      return "en_US";
    }
    return $locale;
  }

  /**
  *
  */
  function add_query_vars( $vars ) {
    array_push( $vars, 'orden', 'por' );
    return $vars;
  }

  /**
  *
  */
  function parse_request_vars( $query ) {

    if ( isset( $query->query_vars['orden'] ) ) {
      $query->query_vars['order'] = $query->query_vars['orden'];
    }

    if ( isset( $query->query_vars['s'] ) ) {
      if ( isset( $query->query_vars['post_type'] ) ) {
        unset( $query->query_vars['post_type'] );
      }
    }

    if ( isset( $query->query_vars['por'] ) ) {
      $por = $query->query_vars['por'];
      $map = array(
        'fecha' => 'starts',
        'precio' => 'price',
        'nombre' => 'name'
      );
      $query->query_vars['orderby'] = ( isset( $map[ $por ] ) ) ? $map[ $por ] : $por;
    }

    return $query;
  }

  /**
  *
  */
  function fix_polylang_searches( $query ) {
    global $polylang;
    if ( !$polylang || !$query->is_search() || is_admin() ) {
      return;
    }

    if ( isset( $_GET['post_type'] ) ) {
      $query->query_vars['post_type'] = $_GET['post_type'];
    }

    if ( ! empty( $_GET['_starts'] ) ) {
      $query->query_vars['meta_query'] = array();
      array_push( $query->query_vars['meta_query'], array(
          'compare' => '<=',
          'key' => '_starts',
          'value' => trim($_GET['_starts']),
        )
      );

      if ( ! empty( $_GET['_ends'] ) ) {
        array_push($query->query_vars['meta_query'], array(
            'compare' => '>=',
            'key' => '_ends',
            'value' => trim($_GET['_ends']),
          )
        );
      }
    }

    if ( ! empty( $query->query_vars['tax_query'] ) ) {
      foreach( $query->query_vars['tax_query'] as $key => $tax_query ) {
        if ($tax_query['taxonomy'] == 'language') {
          unset( $query->query_vars['tax_query'][$key] );
        }
      }
    }

    if ( $term = PLL()->model->get_language( $this->lang == 'es' ? 'en' : 'es' )->term_id ){
      $query->query_vars['tax_query'][] = array(
        'taxonomy'  => 'language',
        'field'    	=> 'term_id',
        'terms'    	=> array( $term ),
        'operator' 	=> 'NOT IN',
      );
    };
  }

  /**
  *
  */
  function add_rewrite_rules( ) {
    global $wp_rewrite;

    $this->lang = get_locale() == 'es_MX' ? 'es' : 'en';

    $wp_rewrite->pagination_base = __( 'page', 'visita' );
    add_rewrite_rule(
      "(en)/page/([0-9]{1,})/?$",
      "index.php?lang=\$matches[1]&post_type=event&paged=\$matches[2]",
      'top'
    );
  }

  /**
  *
  */
  function widgets_init( ) {
    register_widget( 'Visita_Widget' );
  }

  /**
  *
  */
  function remove_code() {
    remove_action('wp_head', 'NextendSocialLogin::styles', 100);
    remove_action('wp_print_footer_scripts', 'NextendSocialLogin::scripts', 100);
  }

  /**
  *
  */
  function display_widgets( ) {
    if ( ( is_singular() && ! is_singular( 'post' ) && ! is_page() ) || is_404() ) {
      add_filter( 'visita_after_loop', array( $this, 'display_widget' ), 0 );
    }
  }

  /**
  * increase number or redirects
  *
  * @since 1.0.1
  */
  function srm_max_redirects( ) {
    return 380;
  }

  /**
  *
  * @param $protected_directories array
  * @return array
  * @since 2.2.1
  */
  function protect_json( $protected_directories ) {
    $protected_directories[] = WP_CONTENT_DIR . '/cache/_json/';
    $protected_directories[] = WP_CONTENT_DIR . '/cache/_json/en.json';
    $protected_directories[] = WP_CONTENT_DIR . '/cache/_json/es.json';
    return $protected_directories;
  }

  /**
  * Activite and save default options
  * Activite the expire cron
  *
  * @return void
  * @since 1.0.1
  */
  function visita_get_weather( $lang ) {

    $responds = wp_remote_get(
      "https://api.apixu.com/v1/forecast.json?key=d5c0c8ccdd194cf4b0003734172201&days=5&q=89109&lang=${lang}"
    );

    if (is_wp_error($responds)) {
      return false;
    }

    if ( ! file_exists( WP_CONTENT_DIR . "/cache/_json/" ) ) {
      @mkdir( WP_CONTENT_DIR . "/cache/_json/", 0755, true );
    }

    if ( $responds['body'] ) {
      if ( $fh = @fopen(WP_CONTENT_DIR . "/cache/_json/${lang}.json", "w" ) ) {
        fwrite( $fh, $responds['body']);
        fclose( $fh );

        if ( function_exists('wpsc_delete_url_cache') ) {
          wpsc_delete_url_cache(
            get_permalink( ($lang == 'es' ? 7057 : 8041) )
          );
        }

      }
    }
  }

  /**
  *
  * @return object
  * @since 2.2.1
  */
  function get_weather_data() {

    if ( $this->weather_data ) {
      return $this->weather_data;
    }

    $file = WP_CONTENT_DIR . "/cache/_json/{$this->lang}.json";

    if (file_exists($file) && $weather_data = file_get_contents($file) ) {

      $weather_json = json_decode($weather_data);
      $weather_json->unit = 'F';
      $weather_json->lang = $this->lang;
      $weather_json->now = round($weather_json->current->temp_f);
      $weather_json->now_feelslike = round($weather_json->current->feelslike_f);

      if ($this->lang == 'es') {
        $weather_json->unit = "C";
        $weather_json->now = round($weather_json->current->temp_c);
        $weather_json->now_feelslike = round($weather_json->current->feelslike_c);
      }

      $weather_json->now_icon = str_replace(
        '//cdn.apixu.com/weather', plugins_url( 'visita/img' ),
        $weather_json->current->condition->icon
      );

      foreach ( $weather_json->forecast->forecastday as $key => $forecast ) {
        $weather_json->forecast->forecastday[$key]->unit = 'F';
        $weather_json->forecast->forecastday[$key]->avgtemp = $temp = round($forecast->day->avgtemp_f);

        if ($this->lang == 'es') {
          $weather_json->forecast->forecastday[$key]->unit = "C";
          $weather_json->forecast->forecastday[$key]->avgtemp = $temp = round($forecast->day->avgtemp_c);
        }

        $weather_json->forecast->forecastday[$key]->icon = str_replace(
          '//cdn.apixu.com/weather', plugins_url( 'visita/img' ),
          $forecast->day->condition->icon
        );
      }

      return $this->weather_data = $weather_json;
    }

    return false;
  }

  /**
  *
  * @return void
  * @since 2.0.1
  */
  function shortcode_clima( ) {

    if ( $data = $this->get_weather_data() ) {

      $forecast_days = '';
      $weather_icon = sprintf(
        '<img src="%1$s" alt="%2$s" title="%2$s">',
        esc_url( $data->now_icon ),
        esc_attr( $data->current->condition->text )
      );

      foreach ($data->forecast->forecastday as $forecast) {
        $forecast_days .= sprintf(
          '<div class="column column-block">
            <div class="text-center">
              <div>%3$s</div>
              <img src="%5$s" alt="%2$s" title="%2$s">
              <div class="show-for-medium">%2$s</div>
              <div>%1$s&deg;%4$s</div>
            </div>
          </div>',
          esc_html( $forecast->avgtemp ),
          esc_attr( $forecast->day->condition->text ),
          esc_attr( date_i18n( 'D', $forecast->date_epoch ) ),
          esc_html( $forecast->unit ),
          esc_url( $forecast->icon )
        );
      }

      return sprintf(
        '<div class="row weather-current">
          <div class="small-12 medium-4 columns text-center">
            <div class="weather-image">%13$s</div>
            <div class="weather-text">%4$s</div>
            <div>%1$s, %2$s</div>
          </div>
          <div class="small-12 medium-8 columns text-center medium-text-left">
            <div class="weather-deg">%3$s&deg;%12$s</div>
            <div class="row weather-detail">
              <div class="small-12 columns">%10$s %5$s%%</div>
              <div class="small-12 columns">%9$s %6$s KH / %7$s</div>
              <div class="small-12 columns">%11$s %8$s&deg;%12$s</div>
            </div>
          </div>
        </div>
        <div class="row small-up-5 weather-forecast">%14$s</div>',
        esc_html( $data->location->name ),
        esc_html( $data->location->region ),
        esc_html( $data->now ),
        esc_html( $data->current->condition->text ),

        esc_html( $data->current->humidity ),
        esc_html( $data->current->wind_kph ),
        esc_html( $data->current->wind_dir ),
        esc_html( $data->now_feelslike ),

        esc_html__( 'Wind:', 'visita'),
        esc_html__( 'Humidity:', 'visita'),
        esc_html__( 'Feels like:', 'visita'),
        esc_attr($data->unit),

        $weather_icon,
        $forecast_days
      );
    }
  }

  /**
  * Disable ACF save hook to improve dabase performance
  *
  * @return void
  * @since 3.0.0
  */
  function disable_save_action( ) {
    remove_action( 'acf/save_post', '_acf_do_save_post' );
  }

  /**
  *
  * @return void
  * @since 3.0.0
  */
  function register_acf_fields( ) {
    register_field_group( array(
      'key' => 'seo',
      'title' => __( 'SEO', 'visita' ),
      'menu_order' => 2,
      'fields' => array(
        array(
          'key' => '_description',
          'name' => '_description',
          'label' => __( 'Description', 'visita' ),
          'rows' => 5,
          'maxlength' => 155,
          'type' => 'textarea',
        ),
        array(
          'key' => '_keywords',
          'name' => '_keywords',
          'label' => __( 'Keywords', 'visita' ),
          'type' => 'text',
        ),
      ),
      'options' => array(
        'layout' => 'default',
        'position' => 'side',
        'hide_on_screen' => array(),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '!=',
            'value' => '',
          ),
        ),
      ),
    ) );
  }

  /**
  * Save ACF fields
  *
  * @param $post_id int
  *
  * @return void
  * @since 3.0.0
  */
  function save_acf_data( $post_id ) {
    if ( ! in_array( get_current_screen()->post_type, array('page', 'post') ) ) {
      return;
    }

    //save each field
    foreach ( (array) $_POST['acf'] as $meta_key => $meta_value ) {
      update_post_meta( $post_id, $meta_key, $meta_value );
    }
  }

  /**
   * Add admin styles and scripts
   *
   * @return void
   * @since 3.0.0
   */
  function admin_scripts( ) {
    if ( ! is_admin() ) return;
    wp_dequeue_style( 'select2' );
    wp_enqueue_style( 'visita-admin', plugins_url( 'css/admin.css', VISITA_FILE_NAME ), NULL, VISITA_VERSION );
  }

  /**
   * Remove admin menus
   *
   * @return void
   * @since 3.0.0
   */
  function remove_unwanted_menus() {
    remove_menu_page( 'amp-options' );

    remove_submenu_page( 'caldera-forms', 'cf-pro' );
    remove_submenu_page( 'caldera-forms', 'cf-pro' );
    remove_submenu_page( 'caldera-forms', 'caldera-form-support' );
    remove_submenu_page( 'caldera-forms', 'caldera-forms-extend' );
  }

  /**
  * Add Import admin page
  *
  * @return void
  * @since 3.0.0
  */
  function add_admin_pages( ) {
    add_management_page(
      __( 'Visita Import', 'visita' ),
      __( 'Visita Import', 'visita' ),
      'manage_options',
      'visita-import',
      array( $this, 'import_page' )
    );
  }

  /**
  * Load Import page
  *
  * @return void
  * @since 3.0.0
  */
  function import_page( ) {
    include_once( VISITA_INC . "/import.php");
  }

  /**
   * WP REST API register custom search endpoints
   *
   * @since 2.2.9
   */
  function rest_api_register_routes() {
    register_rest_route( 'vv/v1', '/s', array(
      'methods'  => 'GET',
      'callback' => array( $this, 'rest_api_search' ),
    ) );
  }

  /**
   * Return search results
   *
   * @return object WP_REST_Response
   * @since 2.2.9
   */
  function rest_api_search( $query ) {

    $data = array();
    $response = new WP_REST_Response( $data );
    $response->header(
      'Cache-Control',
      'max-age=1800, must-revalidate, public'
    );

    if ( empty( $term = $query->get_param( 'term' ) ) ) {
      return $response;
    }

    add_filter( 'the_posts', 'relevanssi_query', 99, 2 );
    add_filter( 'relevanssi_search_ok', '__return_true' );

    $result = new WP_Query( array(
      'posts_per_page'  => 12,
      'post_status'     => 'publish',
      's'               => $term,
    ) );

    foreach ( $result->posts as $post ) {
      $data[] = array(
        'label' => $post->post_title,
        'link' => get_permalink( $post->ID ),
      );
    }

    $response->set_data($data);
    return $response;
  }

  /**
  *
  */
  function display_widget( ) {
    $type = ( $type = get_post_type() ) ? $type : 'event';
    if ( isset( $this->{ $type } ) ) {
      the_widget(
        'Visita_Widget', array(
          'title' => sprintf( __( 'More %s', 'visita' ), $this->{ $type }->get_name() ),
          'before_title'  		=> '<h3 class="widget-title">',
          'after_title'   		=> '</h3>',
        )
      );
    }
  }

  /**
  *
  * @return void
  * @since 0.5.0
  */
  function shortcode_event_list( $atts ) {

    extract( shortcode_atts( array(
        'mes' => false,
        'fecha' => false,
        'lista' => false,
        'semana' => false,
        'proximos' => false,
        'categoria' => false,
        'titulo' => __( 'All Events', 'admin' ),
      ), $atts, 'lista-eventos' )
    );

    // we need a date
    if ( ! $fecha && ! $semana && ! $mes && ! $lista && ! $proximos ) {
      return;
    }

    $tax_query = ( $categoria ) ?
      array( array(
        'field' => 'name',
        'terms' => $categoria,
        'taxonomy' => 'events',
      ) )
    : array( );

    // legacy attributes to date
    if ( $mes ) {
      $end = strtotime( 'first day of next month 23:59' );
      $start = strtotime( 'first day of this month 0:00' );
    }

    // legacy attributes to date
    if ( $semana ) {
      $end = strtotime( 'monday next week 23:59' );
      $start = strtotime( 'monday this week 0:00');
    }

    // legacy attributes to date
    if ( $fecha ) {
      $end = strtotime( "last day of $fecha 23:59" );
      $start = strtotime( "first day of $fecha 0:00" );
    }

    // 30 dias
    if ( $proximos ) {
      $end = strtotime( "today +20 days" );
      $start = strtotime( "today" );
    }

    $meta_query = ($start && $end) ? array(
      array(
        array(
          'value'    => $end,
          'key'      => '_starts',
          'compare'  => '<='
        ),
        array(
          'value'    => $start,
          'key'      => '_ends',
          'compare'  => '>='
        ),
      ),
    ) : array();

    // list of post
    $post_ids = ($lista) ?
      explode( ',', $lista )
    : array();

    $query = new WP_Query(array(
			'post_type'      => array( 'event' ),
			'posts_per_page' => 40,
      'meta_key'       => '_starts',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
      'tax_query'      => $tax_query,
      'meta_query'     => $meta_query,
      'post__in'       => $post_ids,
    ) );


    $list = '';
    foreach( $query->posts as $post ) {
      $starts = (int) get_post_meta( $post->ID, '_starts', true );
      $list .= sprintf(
      '<li itemscope itemtype="http://schema.org/Event">
        <a itemprop="url" class="url" href="%1$s" title="%2$s" rel="bookmark">
          <meta class="image" itemprop="image" content="%8$s" />
          <time itemprop="startDate" datetime="%4$s">%5$s</time> <strong itemprop="name">%3$s</strong>
          <span class="%9$s" itemprop="location" itemscope itemtype="http://schema.org/Place">' . __( 'at', 'visita' ) . '
            <span itemprop="name">%6$s</span>
            <span class="hidden" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
              <span itemprop="streetAddress">%7$s</span>
            </span>
          </span>
        </a>
      </li>',
        get_permalink( $post->ID ),
        esc_attr( $post->post_title ),
        esc_html( $post->post_title ),
        esc_attr( date_i18n( 'c', $starts ) ),
        esc_html( date_i18n( 'j M', $starts ) ),
        esc_html( $location = get_post_meta( $post->ID, '_location', true ) ),
        esc_html( get_post_meta( $post->ID, '_street', true ) ),
        get_the_post_thumbnail_url( $post ),
        esc_html( ! $location ? 'em empty' : 'em' ),
        esc_attr( date_i18n( 'c', $end ) )
      );
    }

    return sprintf(
      '<h2><a href="/" title="%1$s" rel="bookmark">%2$s</a></h2>
      <ul class="event-list">%3$s</ul>',
      esc_attr($titulo),
      __( 'Events', 'visita'),
      $list
    );
  }

  /**
  * add schem.org breadcrumbs
  *
  * @since 2.0.2
  */
  function schemaorg_breadcrumbs( ) {

    if (
      !is_category()
      && !is_singular( 'post' )) {
      return;
    }

    $count = 1;
    $breadcrumbs = array(
      '@context'  => 'http://schema.org',
      '@type' => 'BreadcrumbList',
      'itemListElement' => array(
        array(
          '@type' => 'ListItem',
          'position' =>	$count,
          'item' => array(
            '@id' => home_url(''),
            'name' => __('Home'),
          )
        )
      )
    );

    $breadcrumbs['itemListElement'][] = array(
      '@type' => 'ListItem',
      'position' =>	$count++,
      'item' => array(
        '@id' => home_url('blog'),
        'name' => __('Blog'),
      )
    );

    if ( is_singular( 'post' ) ) {
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => get_permalink(),
          'name' => get_the_title(),
        )
      );
    }
    printf('<script type="application/ld+json">%s</script>', wp_json_encode( $breadcrumbs ));
  }
}
