<?php
/**
* Visita - review functions
*
* @file reviews.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2020 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/plugins/visita/includes/reviews.php
* @since available since 0.1.0
*/

/**
*
*/
function review_link() {
  echo Visita_Reviews::get_review_link();
}

/**
*
*/
function review_add_link() {
  echo Visita_Reviews::get_review_add_link();
}

/**
*
*/
function get_user_post_comment( $post_id = 0 ) {
  return Visita_Reviews::get_user_post_comment( $post_id );
}

/**
*
*/
function get_user_post_comment_ID( $post_id = 0 ) {
  return Visita_Reviews::get_user_post_comment_ID( $post_id );
}

/**
*
*/
function get_post_rating() {
  if ( $rating = get_post_meta( get_the_ID(), '_rating', true ) )
    return $rating;
  return 0;
}

/**
*
*/
function post_rating_formatted() {
  echo number_format( get_post_rating(), 1 );
}

/**
*
*/
function post_rating_stars() {
  printf( '<span class="rating"><span class="rating-value" style="width:%s%%"></span></span>',
    (get_post_rating() * 100) / 5
  );
}

/**
*
*/
function user_rating_stars( $comment_ID ) {
  printf( '<span class="rating"><span class="rating-value" style="width:%s%%"></span></span>',
    (get_comment_meta( $comment_ID, '_rating', true ) * 100) / 5
  );
}

/**
*
*/
function post_review_count() {
  echo get_post_meta( get_the_ID(), '_review_count', true );
}

/**
*
*/
function post_review_count_label() {
  $review_count = get_post_meta( get_the_ID(), '_review_count', true );
  printf(
    _n( '%d Review', '%d Reviews', $review_count, 'visita' ), $review_count
  );
}

/**
*
*/
class Visita_Reviews {

  /**
   * Constructor
   *
   * @return void
   * @since 0.5.0
   */
  function __construct( ) {

    add_action( 'comment_form_top',  array( $this, 'comment_form_top' ), 500 );
    add_action( 'deleted_comment',  array( $this, 'deleted_comment' ), 100, 2 );
    add_action( 'pre_comment_on_post',  array( $this, 'pre_comment_on_post' ), 500 );

    add_filter( 'single_template_hierarchy', array( $this, 'post_templates' ) );
    add_filter( 'duplicate_comment_id', array( $this, 'duplicate_comment_id' ), 200, 2 );
    add_filter( 'registered_post_type', array( $this, 'registered_post_type' ), 300, 2 );

    if ( ! is_admin() ) {
      add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
      add_action( 'init',  array( $this, 'remove_fastvelocity_login' ), 500 );
      return;
    }
  }

  /**
  *
  */
  function remove_fastvelocity_login() {
    global $pagenow;
    if ( in_array( $pagenow, array( 'wp-login.php', 'wp-register.php' ) ) ) {
      remove_filter( 'script_loader_tag', 'fastvelocity_min_defer_js', 10, 3 );
      remove_action( 'wp_print_scripts', 'fastvelocity_min_merge_header_scripts', PHP_INT_MAX );
      remove_action( 'wp_print_footer_scripts', 'fastvelocity_min_merge_footer_scripts', 9.999999 );
    }
  }

  /**
  *
  */
  function allow_empty_comments() {
    if ( isset( $_POST['comment'] ) && ! empty( $_POST['rating'] ) ) {
      if ( '' == trim( $_POST['comment'] ) ) {
        $_POST['comment'] = "&nbsp;";
      }
    }
  }

  /**
  *
  */
  function duplicate_comment_id( $dupe_id , $commentdata) {
    if ( get_user_post_comment_ID() == $dupe_id ) {
      return false;
    }
    return $dupe_id;
  }

  /**
  *
  */
  function pre_comment_on_post( $comment_post_ID ) {

    if ( ! isset( $_POST['post_review'] )  ) {
      if ( ! wp_verify_nonce( $_POST['post_review'], 'review_action' ) ) {
        wp_die(
          '<p>' . __( 'Security check failed', 'visita' ) . '</p>',
          __( 'Sorry, the security checke failed', 'visita' ), array( 'response' => 500, 'back_link' => true )
        );
      }
    }

    $user = wp_get_current_user();

    if ( $user->exists() ) {
  		if ( empty( $user->display_name ) ) {
  			$user->display_name=$user->user_login;
  		}
  		$comment_author       = $user->display_name;
  		$comment_author_email = $user->user_email;
  		$comment_author_url   = $user->user_url;
  		$user_ID              = $user->ID;
  	} else {
  		if ( get_option( 'comment_registration' ) ) {
  			return new WP_Error( 'not_logged_in', __( 'Sorry, you must be logged in to comment.' ), 403 );
  		}
  	}

    $comment_parent = 0;
    $comment_ID = get_user_post_comment_ID( $comment_post_ID );

    // do we want to delete the comment?
    if ( isset( $_POST['delete'] ) && $comment_ID ) {
      if ( current_user_can( 'read' ) ) {
        wp_delete_comment( $comment_ID, true );
        wp_safe_redirect( get_permalink( $comment_post_ID ) );
        exit();
      }
    }

    if ( isset( $_POST['comment'] ) && is_string( $_POST['comment'] ) ) {
      $comment_content = trim( $_POST['comment'] );
    }

    if ( isset( $_POST['rating'] ) ) {
      $comment_rating = trim( (int) $_POST['rating'] );
    }

    if ( $comment_rating == 0 && $comment_content == '' ) {
      wp_safe_redirect( get_permalink( $comment_post_ID ) );
      exit();
    }

    $commentdata = wp_slash( compact(
      'comment_ID',
      'comment_post_ID',
      'comment_author',
      'comment_author_email',
      'comment_author_url',
      'comment_content',
      'user_ID'
    ) );

    $check_max_lengths = wp_check_comment_data_max_lengths( $commentdata );
    if ( is_wp_error( $check_max_lengths ) ) {
      return $check_max_lengths;
    }

    ( $comment_ID ) ? wp_update_comment( $commentdata ) : $comment_ID = wp_new_comment( $commentdata, true );

    if ( is_wp_error( $comment_ID ) ) {
      wp_die(
        '<p>' . $comment_ID->get_error_message() . '</p>',
        __( 'Review Submission Failure', 'visita' ), array( 'response' => 500, 'back_link' => true )
      );
    }

    update_comment_meta(
      $comment_ID, '_rating', $comment_rating,
      get_comment_meta( $comment_ID, '_rating', true )
    );

    $this->update_post_review_count( $comment_post_ID );
    wp_safe_redirect( get_comment_link( $comment_ID ) );
    exit();
  }

  /**
  *
  */
  function deleted_comment( $comment_ID, $deleted ) {
    $this->update_post_review_count( $deleted->comment_post_ID );
  }

  /**
  *
  */
  function comment_form_top() {
    global $user_identity, $current_user;

    wp_nonce_field( 'review_action', 'post_review' );
    $rating = get_comment_meta( get_user_post_comment_ID(), '_rating', true );

    $stars = ''; for ( $i = 5; $i >= 0; $i-- ) {
      $stars .= sprintf( '
        <input type="radio" name="rating" id="rating-%3$d" value="%3$d" %2$s />
        <label for="rating-%3$d">%1$s</label>',
        sprintf( esc_html__( 'Rating %d' ), $i ),
        checked( $i, $rating, false ),
        $i
      );
    }

    printf( '
      <p class="logged-in-as"><a href="%2$s">%1$s</a></p>
      <div class="stars">%3$s</div>',
      get_avatar( $current_user->user_email, 38 ) . $user_identity,
      admin_url( 'profile.php' ),
      $stars
    );
  }

  /**
  *
  */
  function registered_post_type( $post_type, $post_type_object ) {
    if ( post_type_supports( $post_type, 'reviews' ) ) {
      $this->add_reviews_endpoint( $post_type, $post_type_object->rewrite['slug'] );
    }
  }

  /**
  *
  */
  function add_query_vars( $vars ) {
    array_push( $vars, 'reviews' );
    return $vars;
  }

  /**
  * add reviews end point
  *
  * @since 2.0.3
  */
  function add_reviews_endpoint( $post_type, $slug ) {
    add_rewrite_rule(
      "$slug/([^/]+)/reviews/add/?$",
      "index.php?post_type={$post_type}&name=\$matches[1]&reviews=add",
      'top'
    );
    add_rewrite_rule(
      "$slug/([^/]+)/reviews/?$",
      "index.php?post_type={$post_type}&name=\$matches[1]&reviews=1",
      'top'
    );
  }

  /**
  * provide other default templates for reviews
  *
  * @param $templates array
  * @since 2.0.3
  */
  function post_templates( $templates ) {
    if ( ! post_type_supports( get_post_type(), 'reviews' ) ) {
      return $templates;
    }

    if ( get_query_var('reviews') === 'add' ) {
      array_unshift(
        $templates,
        'reviews-form.php'
      );
    } else if ( get_query_var('reviews') ) {
      array_unshift(
        $templates,
        'reviews.php'
      );
    }

    return $templates;
  }

  /**
  *
  */
  function comment_form_delete( $submit ) {
    return sprintf(
      '%s <input name="submit" type="submit" id="submit" class="submit button" value="Delete Review">',
      $submit
    );
  }

  /**
  *
  */
  static function get_review_link() {
    return trim( get_permalink() , '/' ) . '/reviews/';
  }

  /**
  *
  */
  static function get_review_add_link() {
    return trim( get_permalink() , '/' ) . '/reviews/add/';
  }

  /**
  *
  */
  static function get_user_post_comment( $post_id  = 0 ) {
    if ( $posts = get_comments( array(
        'post_id' => ( $post_id ) ? $post_id : get_the_ID(),
        'author_email' => wp_get_current_user()->user_email,
       )
    ) ) return $posts[0];
    return (object) array(
      'comment_ID' => null,
      'comment_content' => null,
    );
  }

  /**
  *
  */
  static function get_user_post_comment_ID( $post_id = 0 ) {
    return get_user_post_comment( $post_id )->comment_ID;
  }

  /**
  *
  */
  function update_post_review_count( $comment_post_ID ) {
    global $wpdb;

    if ( ! $comment_post_ID = (int) $comment_post_ID )
      return false;

    $values = $wpdb->get_row( $wpdb->prepare("
      SELECT COUNT(*) count, SUM(cm.meta_value) sum FROM $wpdb->comments c
      JOIN visit_commentmeta cm
        ON c.comment_ID = cm.comment_id
        AND cm.meta_key = '_rating'
        AND cm.meta_value > 0
      WHERE c.comment_post_ID = %d"
    , $comment_post_ID ) );

    update_post_meta( $comment_post_ID, '_review_count', $values->count );
    update_post_meta( $comment_post_ID, '_rating', (($values->count) ? $values->sum / $values->count : false) );
  }
}
