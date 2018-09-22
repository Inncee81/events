<?php
/**
* Visita - Events Class
*
* @file events.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/events.php
* @since available since 0.1.0
*/

class VisitaBase {

  /**
  *
  */
  protected $name;

  /**
  *
  */
  protected $slug;

  /**
  *
  */
  protected $position;

  /**
  *
  */
  protected $singular;

  /**
  *
  */
  protected $taxonomy;

  /**
  *
  */
  protected $post_type;

  /**
  *
  */
  protected $taxonomy_slug;

  /**
  *
  */
  protected $taxonomy_label;

  /**
  *
  */
  protected $default_fields;

  /**
  *
  */
  protected $is_home = false;

  /**
  *
  */
  protected $tabs = array();

  /**
  *
  */
  protected $description = '';

  /**
  *
  */
  protected $supports = array();

  /**
  *
  */
  protected $archive_term_id = array();

  /**
  *
  */
  protected $mute_terms_ids = array();

  /**
  *
  */
  protected $lang = 'es';

  /**
  *
  */
  protected $fields = array(
    'key' => '',
    'title' => '',
    'menu_order' => 1,
    'fields' => array(),
    'options' => array(
      'layout' => 'default',
      'position' => 'normal',
      'hide_on_screen' => array(),
    ),
    'location' => array (
      array (
        array (
          'param' => 'post_type',
          'operator' => '==',
          'value' => '',
        ),
      ),
    ),
  );

  /**
  *
  */
  protected $default_data = array(
    'ID'                  => false,
    'image'               => false,
    'post_title'          => '',
    'post_author'         => 1,
    'post_status'         => 'draft',
    'post_content'        => '',
    'post_type'           => '',
    'tax_input'           => array(),
    'meta_input'          => array(
      '_keywords'         => '',
      '_description'      => '',
      '_starts'           => '', //start date
      '_ends'             => '', //start time
      '_location'         => '',
      '_street'           => '',
      '_city'             => 'Las Vegas',
      '_state'            => 'NV',
      '_zip'              => '',
      '_phone'            => '',
      '_link'             => '',
      '_price'            => '',
      '_price_max'        => '',
      '_event_id'         => false,
      '_event_type'       => '',
      '_duration'         => 120, // minutes
      '_currency'         => 'USD',
      '_times'            => array( array(
        '_date'           => '',
        '_time'           => '',
        '_date_link'      => '',
        '_availability'   => 'InStock',
      )),
      '_performers'       => array( array(
        '_name'           => '',
        '_type'           => '',
      ) ),
    )
  );

  /**
  *
  * @return array | string
  * @since 1.0.0
  */
  public function __get( $field ) {
    if ( isset($this->$field[$this->lang]) )
      return $this->$field[$this->lang];
    else return $this->$field;
  }

  /**
  *
  * @return void
  * @since 1.0.0
  */
  function redirect_404( ) {
    if ( is_404() && isset( $_SERVER['REQUEST_URI'] ) && ! empty( $this->slug ) ) {
      if ( preg_match('/\/(' . esc_attr( $this->slug ) . ')/i', $_SERVER['REQUEST_URI'] ) ) {
        wp_safe_redirect( "/{$this->taxonomy_slug}", 302 ); die();
      }
    }
  }

  /**
  *
  * @param $title_parts array
  * @return array
  * @since 1.0.0
  */
  function title_parts( $title_parts ) {
    if ( ! is_singular( $this->post_type ) ) {
      return $title_parts;
    }

    if ( $starts = get_post_meta( get_the_ID(), '_starts', true ) ) {
      if ( get_post_meta( get_the_ID(), '_days', true ) ) {
        $starts = strtotime( 'this week 0:00' ) + $starts;
      }
      array_splice( $title_parts, 1, 0, date_i18n( __( 'F j, Y', 'visita' ), $starts ) );
    }

    return $title_parts;
  }

  /**
  *
  * @return array
  * @since 2.0.2
  */
  function title_tax_parts( $title_parts ) {
    if ( is_tax( $this->taxonomy ) ) {
      $title_parts['title'] = $this->taxonomy_label . ": " . $title_parts['title'];
    }
    return $title_parts;
  }

  /**
  * Format event description for SEO
  *
  * @return string
  * @since 1.0.0
  */
  function get_description( $title, $date, $time, $city = 'Las Vegas' ) {
    $format = ( $date && $time ) ? 'l j %\s F Y %\s g:i a.' : 'Y %\s g:i a.';
    return ucwords( str_ireplace(
        array('of las vegas', 'las vegas'), '', strtolower( $title )
      ) )
      . " de $city, "
      . sprintf(
          lcfirst( date_i18n( $format, strtotime( "$date $time" ) ) ),
          __( 'from', 'visita' ),
          __( 'to', 'visita' )
      )
      . " #VisitaVegas #Vegas";
  }

  /**
   * Add admin styles and scripts
   *
   * @return void
   * @since 1.0.0
   */
  function admin_scripts( ) {
    if ( get_current_screen()->id === $this->post_type ) {
      wp_enqueue_style( 'visita-fields', plugins_url( 'css/fields.css', VISITA_FILE_NAME ), NULL, VISITA_VERSION );
      wp_enqueue_script( 'visita-admin', plugins_url( 'js/admin.js', VISITA_FILE_NAME ), array( 'jquery' ), VISITA_VERSION, true );
    }
  }

  /**
  *
  * @return void
  * @since 1.0.0
  */
  function register_post_type( ) {

    register_taxonomy( $this->taxonomy, $this->post_type, array(
      'show_in_rest'      => true,
      'show_admin_column' => true,
      'label'             => $this->taxonomy_label,
      'rewrite'           => array( 'slug' => trim( $this->taxonomy_slug ) ),
      'hierarchical'      => true,
    ) );

    register_post_type( $this->post_type, array(
      'labels'            => array(
          'name'          => $this->name,
          'singular_name' => $this->singular,
        ),
        'show_ui'         => true,
        'public'          => true,
        'hierarchical'    => false,
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'menu_position'   => $this->position,
        'has_archive'     => $this->taxonomy_slug,
        'taxonomies'      => array( $this->taxonomy ),
        'rewrite'         => array( 'slug' => $this->slug, 'with_front' => false ),
        'supports'        => array_merge(array(
          'title',
          'editor',
          'comments',
          'revisions',
          'thumbnail',
        ), $this->supports )
    ) );
  }

  /**
  *
  *
  * @return void
  * @since 1.0.0
  */
  function head_metatags() {
    if ( is_post_type_archive( $this->post_type ) && $this->description ){
      echo '<meta name="description" content="'. esc_attr( $this->description ) . '" />'. "\n";
      echo '<meta name="twitter:description" content="' . esc_attr( $this->description ) . '"  />' . "\n";
    }
  }

  /**
  * Get Post ID by using meta_key and media_value
  *
  * @return bool|unit
  * @since 1.0.0
  */
  function get_id_by_metadata( $meta_key, $media_value ) {

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
      "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",
      $meta_key,
      $media_value
    ) );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_where( $direction ) {
    global $wpdb, $post;

    $language = '';
    if ( function_exists( 'pll_current_language') ) {
      if ( $term_id = pll_current_language( 'term_id') ) {
        $language = $wpdb->prepare(" AND p.ID IN ( SELECT tr.object_id FROM $wpdb->term_relationships tr LEFT JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) WHERE tt.term_id IN (%d) )", $term_id );
      }
    }

    return $wpdb->prepare(
      " WHERE p.post_title $direction '%s' AND p.post_type = '%s' AND p.post_status = 'publish'",
      $post->post_title,
      get_post_type()
    );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_sort( $direction ) {
    return esc_sql( " ORDER BY p.post_title $direction LIMIT 1 " );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_next_sort( ) {
    return $this->adjacent_post_sort( "ASC" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_next_where( ) {
    return $this->adjacent_post_where( ">" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_previous_sort( ) {
    return $this->adjacent_post_sort( "DESC" );
  }

  /**
  * Sort objects by title and date
  *
  * @return string
  * @since 1.0.0
  */
  function adjacent_post_previous_where( ) {
    return $this->adjacent_post_where( "<" );
  }

  /**
  *
  * @return string
  * @since 1.0.0
  */
  function get_name( ) {
    return $this->name;
  }

  /**
  *
  */
  function rewrite_rules_array( $rules ) {

    $new_rules = array();
    $find = array( '/pagina', "(en)/$this->taxonomy_slug", "(en)/$this->slug" );
    $replace =  array( '/page', "(en)/$this->taxonomy", "(en)/$this->post_type" );

    foreach ( $rules as $match => $rule ) {
      if ( stripos( $match, "(en)/$this->taxonomy_slug" ) === 0 ) {
        $new_rules[ str_replace( $find, $replace, $match) ] = $rule;
      } elseif ( stripos( $match, "(en)/$this->slug" ) === 0 )  {
        $new_rules[ str_replace( $find, $replace, $match) ] = $rule;
      } else {
        $new_rules[$match] = $rule;
      }
    }

    return $new_rules;
  }

  /**
  * Add additional rewrites
  *
  * @return bool|unit
  * @since 1.0.0
  */
  function add_rewrite_rules( ) {
    global $wp_rewrite;

    $base = $this->post_type == 'event' ? '' : "$this->taxonomy_slug/";

    add_rewrite_rule(
      "{$base}{$wp_rewrite->pagination_base}/([0-9]{1,})/?$",
      "index.php?post_type={$this->post_type}&paged=\$matches[1]",
      'top'
    );

    if ( defined( 'AMP_QUERY_VAR' ) ) {
      add_post_type_support( $this->post_type, AMP_QUERY_VAR );
    }
  }

  /**
  *
  * @return bool
  * @since 1.0.0
  */
  function register_acf_fields( ) {
    if ( $this->fields ) {
      register_field_group( $this->fields );
    }
  }

  /**
  * Save ACF fields
  *
  * @param $post_id int
  * @param $values array
  * @return void
  * @since 1.0.0
  */
  function save_acf_data( $post_id, $values ) {
    if ( get_current_screen()->post_type !== $this->post_type ) {
      return;
    }

    //save each field
    foreach ( $values as $meta_key => $meta_value ) {
      update_post_meta( $post_id, $meta_key, $meta_value );
    }
  }

  /**
  * Return _times field values
  *
  * @param $post_id mix
  * @param $post_id int
  * @param $field array acf filed array
  * @return array|mix
  * @since 1.0.0
  */
  function load_repeater_values( $value, $post_id, $field ) {

    if ( empty( $field['sub_fields'] ) ) return $value;
    if ( get_post_type( $post_id ) !== $this->post_type ) return $value;

    $rows = array();
    $data = get_post_meta( $post_id, $field['key'], true );

    foreach ( (array) $data as $index => $values ) {
      foreach ( $field['sub_fields'] as $sub_field ) {
        $key = $sub_field['key'];
        if ( isset( $values[ $key ] ) ) {
          $rows[$index][ $key ] = $values[ $key ];
        }
      }
    }

    return array_values( $rows );
  }

  /**
  *
  *
  *
  * @return void
  * @since 0.5.0
  */
  function pre_get_posts( $query ) {
    if ( ! $query->is_main_query() ) {
      return;
    }

    if ( is_home() && $this->is_home ) {
      $query->is_post_type_archive = true;
      $query->set( 'post_type', $this->post_type );
      if ( $this->archive_term_id ) {
        $query->query_vars['tax_query'][] = array(
          'taxonomy' => 'events',
          'field'    => 'term_id',
          'operator' => 'NOT IN',
          'terms'		 => array( $this->archive_term_id ),
        );
      }
    }

    if ( is_post_type_archive( $this->post_type )) {
      $query->set( 'posts_per_page', 14 );
    }
  }

  /**
  *
  * @return object $match
  */
  function relevanssi_adjust_weights( $match ) {
    if ( get_post_type( $match->doc ) == $this->post_type ) {
      if ( has_term( $this->archive_term_id , $this->taxonomy, $match->doc ) ) {
        $match->weight = $match->tf * .15;
      }
    }
    return $match;
  }

  /**
  *
  * @return void
  */
  function sort_tabs() {
    if ( ! is_post_type_archive( $this->post_type ) && ! is_tax( $this->taxonomy ) ) {
      return;
    }

    global $wp; $tabs = '';
    $order_var = get_query_var( 'orderby' );

    foreach ( $this->tabs as $tab => $label ) {
      $tabs .= '<li class="tabs-title tab-' . esc_attr( $tab ) . '">
        <a href="%1$s?%4$s=%2$s&%5$s=' . strtolower( esc_attr( $label ) ) . '">' . esc_html( $label ) . '</a>
      </li>';
    }

    printf(
      '<ul class="tabs tabs-sort active-%3$s order-%2$s" data-tabs>
        <li class="tabs-title"><span>' . esc_html__( 'Sort:', 'visita' ) . '</span></li>' . $tabs . '
      </ul>',
      home_url( $wp->request ),
      esc_attr( strtolower( get_query_var( 'order' ) ) == 'asc' ? 'desc' : 'asc' ),
      esc_attr( trim( is_array( $order_var ) ? key( $order_var ) : $order_var, '_' ) ),
      esc_attr__( 'order', 'visita' ),
      esc_attr__( 'orderby', 'visita'  )
    );
  }

  /**
  *
  * @return string
  * @since 1.0.0
  */
  function sort_tax( $query ) {
    if ( ! $query->is_main_query() || is_search() ) {
      return;
    }

    if ( is_post_type_archive( $this->post_type ) || is_tax( $this->taxonomy ) ) {

      $orderby = get_query_var( 'orderby' );
      $order = ( $order = get_query_var( 'order' ) ) ? $order : 'ASC';

      $query->set( 'order',  $order );
      if ( ! $orderby ) {
        $query->set( 'orderby',  'name' );
      }

      if ( $orderby == 'price' ) {
        $key = ( $order == 'ASC' ) ? '_price' : '_price_max';
        $query->set( 'orderby', array( $key => $order ) );
        $query->set( 'meta_query', array(
          $key => array(
            'key'     => $key,
            'compare' => 'EXISTS',
            'type'    => 'DECIMAL',
          )
        ) );
      }
    }
  }

  /**
  * add schem.org breadcrumbs
  *
  * @since 2.0.2
  */
  function schemaorg_breadcrumbs( ) {
    if (
      !is_tax( $this->taxonomy )
      && !is_singular( $this->post_type )
      && !is_post_type_archive( $this->post_type )) {
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
            '@id' => home_url(),
            'name' => __('Home'),
          )
        )
      )
    );

    if ( is_singular( $this->post_type ) ) {
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => home_url($this->taxonomy_slug),
          'name' => $this->taxonomy_label,
        )
      );
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => get_permalink(),
          'name' => get_the_title(),
        )
      );
    }

    if ( is_tax( $this->taxonomy ) ) {
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => home_url($this->taxonomy_slug),
          'name' => $this->taxonomy_label,
        )
      );
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => get_term_link(get_queried_object()->ID),
          'name' => get_queried_object()->name,
        )
      );
    }

    if ( is_post_type_archive( $this->post_type ) ) {
      $breadcrumbs['itemListElement'][] = array(
        '@type' => 'ListItem',
        'position' =>	$count++,
        'item' => array(
          '@id' => home_url($this->taxonomy_slug),
          'name' => $this->taxonomy_label,
        )
      );
    }

    printf('<script type="application/ld+json">%s</script>', wp_json_encode( $breadcrumbs ));
  }
}
