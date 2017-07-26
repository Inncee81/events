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
  echo '<link rel="dns-prefetch" href="//s.visita.vegas" />' . "\n";
  echo '<link rel="dns-prefetch" href="//cdn.onesignal.com" />' . "\n";
  echo '<link rel="dns-prefetch" href="//fonts.googleapis.com" />' . "\n";
}
add_action( 'wp_head', 'visita_add_head_metatags', 2 );
