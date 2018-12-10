<?php
/*
  Plugin Name: Visita
  Author: Hafid R. Trujillo Huizar
  Version: 2.1.3
  Author URI:http://www.xparkmedia.com
  Requires at least: 3.1.0
	Min WP Version: 2.7.0
	Max WP Version: 4.8.0
  Text Domain: visita

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

// Stop direct access to the file
if ( ! defined( 'ABSPATH' ) )
	die( );

define( 'VISITA_VERSION', '2.1.3' );
define( 'VISITA_FILE_NAME', plugin_basename( __FILE__ ) );
define( 'VISITA_ABSPATH', str_replace( "\\", "/", dirname( __FILE__ ) ) );
define( 'VISITA_INC', VISITA_ABSPATH . '/includes' );

if ( ! class_exists( 'Visita_Core' ) ){

	include_once( VISITA_INC . "/core.php" );
	include_once( VISITA_INC . "/base.php" );
	include_once( VISITA_INC . "/shows.php" );
	include_once( VISITA_INC . "/clubs.php" );
	include_once( VISITA_INC . "/events.php" );
	include_once( VISITA_INC . "/hotels.php" );
	include_once( VISITA_INC . "/reviews.php" );
	include_once( VISITA_INC . "/widget.php" );
	include_once( VISITA_INC . "/attractions.php" );

	$visita_core = new Visita_Core();
	$visita_reviews = new Visita_Reviews();
}
