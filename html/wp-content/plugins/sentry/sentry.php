<?php
/*
  Plugin Name: Sentry
  Author: Hafid R. Trujillo Huizar
  Version: 1.0.0
  Author URI:http://www.xparkmedia.com
  Requires at least: 3.1.0
	Min WP Version: 3.1.0
	Max WP Version: 4.9.4
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

define( 'SENTRY_FILE_NAME', plugin_basename( __FILE__ ) );
define( 'SENTRY_ABSPATH', str_replace( "\\", "/", dirname( __FILE__ ) ) );

include_once( SENTRY_ABSPATH . '/autoload.php');

if ( class_exists( 'Raven_Autoloader' ) ){

  Raven_Autoloader::register();

  $error_handler = new Raven_ErrorHandler(new Raven_Client(
    'https://86bddba08770486eb99e30f7f7a87a32:fc8ba8d201d14e11bc9556ff36236f9f@sentry.io/285640'
  ));

  $error_handler->registerErrorHandler();
  $error_handler->registerExceptionHandler();
  $error_handler->registerShutdownFunction();
}
