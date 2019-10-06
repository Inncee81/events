<?php
/**
* Visita - Users Class
*
* @file users.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2020 Xpark Media
* @filesource  wp-content/plugins/visita/includes/users.php
* @since available since 2.1.6
*/

class Visita_Users {

  /**
   * Constructor
   *
   * @return void
   * @since 0.5.0
   */
  function __construct( ) {

    //
    add_filter( 'manage_users_columns' , array( $this, 'add_extra_user_column' ) );
    add_filter( 'manage_users_custom_column' , array( $this, 'add_user_ip_column' ), 100, 3 );

    add_filter( 'pre_user_login', array( $this, 'pre_user_login' ) );
    add_filter( 'user_register', array( $this, 'register_user_ip' ) );
    add_filter( 'illegal_user_logins', array( $this, 'illegal_user_logins' ) );

  }

  /**
  *
  *
  * @since  2.1.6
  */
  function register_user_ip( $user_id ) {
    update_user_meta( $user_id, '_ip',
      trim(isset($_SERVER['HTTP_X_FORWARDED_FOR']) ?
        $_SERVER['HTTP_X_FORWARDED_FOR'] :
        $_SERVER['REMOTE_ADDR']
      )
    );
  }

  /**
  *
  *
  * @since  2.1.6
  */
  function illegal_user_logins( $blacklisted ) {
    return array_merge( $blacklisted,
      array( 'spam-user' )
    );
  }

  /**
  * Block spam users from russia
  *
  * @since  2.1.6
  */
  function pre_user_login( $user_login ) {
    if ( preg_match( '/(has left (.*) message|^(date|chat|meet) )/', $user_login ) ) {
      return 'spam-user';
    }
    return $user_login;
  }

  /**
  * add additional columns to users page
  *
  * @since  2.1.6
  */
  function add_extra_user_column( $columns ) {
    return array_merge( $columns,
      array( 'IP' => __( 'IP', 'visita' ) )
    );
  }

  /**
  * add IP columns to users page
  *
  * @since  2.1.6
  */
  function add_user_ip_column( $output, $column_name, $user_id ) {
    if ( $column_name == 'IP' ) return get_user_meta( $user_id, '_ip', true );
  }
}

//
new Visita_Users();
