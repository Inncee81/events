<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('acf_admin_settings_updates') ) :

class acf_admin_settings_updates {

	// vars
	var $view = array();


	/*
	*  __construct
	*
	*  Initialize filters, action, variables and includes
	*
	*  @type	function
	*  @date	23/06/12
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct() {

		// actions
		add_action('admin_menu', array($this, 'admin_menu'), 20 );

	}


	/*
	*  show_notice
	*
	*  This function will show a notice (only once)
	*
	*  @type	function
	*  @date	11/4/17
	*  @since	5.5.10
	*
	*  @param	$message (string)
	*  @param	class (string)
	*  @return	n/a
	*/

	function show_notice( $message = '', $class = '' ){

		// only show one notice
		if( acf_has_done('acf_admin_settings_updates_notice') ) return false;


		// add notice
    	acf_add_admin_notice( $message, $class );

	}


	/*
	*  show_error
	*
	*  This function will show an error notice (only once)
	*
	*  @type	function
	*  @date	11/4/17
	*  @since	5.5.10
	*
	*  @param	$error (mixed)
	*  @return	n/a
	*/

	function show_error( $error = '' ){
	}

	/*
	*  get_changelog_section
	*
	*  This function will find and return a section of content from a plugin changelog
	*
	*  @type	function
	*  @date	11/4/17
	*  @since	5.5.10
	*
	*  @param	$changelog (string)
	*  @param	$h4 (string)
	*  @return	(string)
	*/

	function get_changelog_section( $changelog, $h4 = '' ) {
	}


	/*
	*  admin_menu
	*
	*  This function will add the ACF menu item to the WP admin
	*
	*  @type	action (admin_menu)
	*  @date	28/09/13
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function admin_menu() {

		// bail early if no show_admin
		if( !acf_get_setting('show_admin') ) return;


		// bail early if no show_updates
		if( !acf_get_setting('show_updates') ) return;


		// bail early if not a plugin (included in theme)
		if( !acf_is_plugin_active() ) return;
		
	}


	/*
	*  load
	*
	*  description
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function load() {
	}


	/*
	*  activate_pro_licence
	*
	*  description
	*
	*  @type	function
	*  @date	16/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function activate_pro_licence() {
	}


	/*
	*  deactivate_pro_licence
	*
	*  description
	*
	*  @type	function
	*  @date	16/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function deactivate_pro_licence() {

	}


	/*
	*  html
	*
	*  description
	*
	*  @type	function
	*  @date	7/01/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function html() {

		// load view
		acf_get_view( dirname(__FILE__) . '/views/html-settings-updates.php', $this->view);

	}

}


// initialize
new acf_admin_settings_updates();

endif; // class_exists check
