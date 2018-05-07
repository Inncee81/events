<?php

function vidalatina_settings() {
  global $visita_options;

  $visita_options['domain'] = 'vidalatina.us';
  remove_action( 'wp_footer', 'visita_organization_schema', 5 );
}
add_action( 'after_setup_theme', 'vidalatina_settings', 100 );
