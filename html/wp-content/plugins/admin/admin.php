<?php
/*
	Plugin Name: Admin Area
	Plugin URI: http://xparkmedia.com
	Description: Default Settings for all xpark media sites. Custom login, remove some categories form RSS, delay RSS, add ID colum, cache, gz compression, set email information.
	Version: 3.0.0
	Author: Xpark Media
	Author URI: http://xparkmedia.com/
	Copyright (C) 2015-2018 Hafid R. Trujillo - Xpark Media
	Text Domain: admin

  Copyright 2010-2018 by Hafid Trujillo http://www.xparkmedia.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Pusblic License as published by
  the Free Software Foundation; either version 2 of the License,or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not,write to the Free Software
  Foundation,Inc.,51 Franklin St,Fifth Floor,Boston,MA 02110-1301 USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access file directly
};


if ( ! class_exists( 'XparkMedia' ) ) {
	class XparkMedia {

		/**
		* Variables
		*
		* @param $domain plugin Gallery IDentifier
		* Make sure that new language(.mo ) files have 'ims-' as base name
		*/
		protected $styles 	= array();
		protected $version 	= '3.0.0';
		protected $domain 	= 'xparkmedia';


		/**
		* Constructor
		*
		* @return void
		* @since 0.5.0
		*/
		function __construct( ) {

			//basics
			add_action( 'init', array( &$this, 'add_hooks' ) );
			add_action( 'init', array( &$this, 'redirect_page_admin' ), -10 );
			add_action( 'init', array( &$this, 'force_gzip_compresion' ), 1 );

			//register hooks
			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
		}

		/**
		* create cache folders
		*/
		function activate( ) {
			wp_schedule_event( strtotime( "tomorrow 1 hour" ), 'daily', 'block_users' );
		}

		/**
		* create cache folders
		*/
		function deactivate( ) {
			wp_clear_scheduled_hook( 'block_users' );
		}

		/**
		* Load what is needed where is needed
		*
		* @return void
		* @since 0.5.0
		*/
		function add_hooks( ) {

			//Security
			add_filter( 'login_message', array( &$this, 'display_message' ) );
			add_action( 'wp_login', array( &$this, 'user_last_access'), 1, 2 );
			add_action( 'admin_init', array( &$this, 'update_user_status' ) );
			add_action( 'block_users', array( &$this, 'change_user_status' ) );

			//url modifications
			add_filter( 'site_url', array( &$this, 'change_access_urls' ), 100, 2 );

			if ( is_admin( ) ) {

			}
		}

		/**
		 * Force GZip compression
		 */
		function force_gzip_compresion( ) {
			if ( ! defined('WP_CACHE') || ! WP_CACHE ) {
				return;
			}

			global
			$compress_css,
			$compress_scripts,
			$concatenate_scripts;

			$compress_css 				= 1;
			$compress_scripts 		= 1;
			$concatenate_scripts 	= 1;

			// Dont use on /wp-admin/post ( tinymce )
			if ( preg_match( '(/post.php|/post-new.php)i', $_SERVER['REQUEST_URI'] ) ) {
				$concatenate_scripts = 0;
			}

			define( 'ENFORCE_GZIP', true );
			define( 'COMPRESS_CSS', true );
			define( 'COMPRESS_SCRIPTS', true );
			define( 'CONCATENATE_SCRIPTS', true );
		}

		/*
		*
		*/
		function redirect_page_admin( ) {

			add_rewrite_rule( '/access/?', 'wp-login.php', 'top' );
			add_rewrite_rule( '/register/?', 'wp-signup.php', 'top' );
			add_rewrite_rule( '/loggedout/?', 'wp-login.php?loggedout=true', 'top' );

			//login in page redirect
			if ( preg_match( '/wp-login.php/i', $_SERVER['REQUEST_URI'] ) && empty( $_POST['user_login'])  ) {
				wp_redirect( site_url( str_replace( array( 'wp-login.php' ), 'access', $_SERVER['REQUEST_URI'] ) ) );
				die();
			}
		}

		/*
		*
		*/
		function change_access_urls( $original_url, $path ) {

			if ( preg_match( '/wp-signup.php/i', $original_url ) )
				return site_url( 'signup/' );

			if ( ! preg_match( '/wp-login.php/i', $original_url ) )
				return $original_url;

			if ( preg_match( '/action=register/i', $original_url ) )
				return site_url( 'signup/' );

			if ( preg_match( '/loggedout=true/i', $original_url ) )
				return site_url( 'signedout/', 'login'  );

			return  site_url( str_replace( array('wp-login.php' ), 'access', $path ));
		}

		/**
		* Change user status for users not using their account
		* 1 = locked
		* 2 = verified
		*/
		function change_user_status( ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare(
				"UPDATE $wpdb->users u
				LEFT JOIN $wpdb->usermeta on user_id = ID AND meta_key = 'user_last_access'
				SET user_status = 1 WHERE user_status NOT IN ( 1, 2 ) AND ( meta_value IS NULL OR meta_value = %d )
				AND ( SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = u.id ) <= 0 AND user_registered <  %s "
			,
				strtotime( '-1 year' ),
				date( 'Y-m-d', strtotime( '-1 month' ) )
			) );
		}

		/**
		* Add last user log in time
		*/
		function user_last_access( $user_login, $user ) {
			update_user_meta( $user->ID, 'user_last_access', current_time( 'timestamp' ) );
		}

		/**
		 * Check user status
		 */
		function check_user_status( ) {
			global $current_user;

			if ( $current_user->user_status == 1 ) {
				wp_logout( );
				wp_redirect( site_url(  '/wp-login.php?disabled=true' ) );
				die( );
			}
		}

		/**
		 * Display disabled message
		 */
		function display_message( $message ){
			if ( empty( $_GET['disabled'] ) )
				return $message;

			return sprintf(
				'<div id="login_error"><strong>%1$s</strong> <a href="mailto:%2$s">%3$s</a></div>',
				__( 'You account has been disabled, please contact:' ),
				esc_attr( get_bloginfo( 'admin_email' ) ),
				get_bloginfo( 'admin_email' )
			);
		}

		/**
		 * Update user status
		 */
		function update_user_status( ) {
			if ( isset( $_GET['action'] ) && isset( $_GET['user'] ) && current_user_can( 'remove_user' ) ) {

				switch ( $_GET['action'] ) {
					case 'submitblock':
						$status = 1;
						break;
					case 'submitverified':
						$status = 2;
						break;
					case 'submitactivate':
					default:
						$status = 0;
				}

				$wpdb->update( $wpdb->users,
					array( 'user_status' => $status ),
					array( 'ID' => $_GET['user'] ),
					array( '%d' ), array( '%d' )
				);

				wp_redirect( admin_url( 'users.php' ) );
				die( );
			}
		}
	}

	//do that thing that you do
	$XparkMedia = new XparkMedia( );
}
