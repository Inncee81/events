<?php
/**
* Visita - Theme Options
*
* @file theme-options.php
* @package visita
* @author Hafid Trujillo
* @copyright 2010-2018 Xpark Media
* @version release: 1.0.0
* @filesource  wp-content/themes/visita/includes/theme-options.php
* @since available since 0.1.0
*/

function visita_customize_register( $wp_customize ) {

  $wp_customize->add_control( 'visita_page_width', array(
    'section'  				=> 'visita_layout',
    'settings' 				=> 'visita_theme_options[page_width]',
    'label'    				=> __( 'Maximum page width', 'visita' ),
  ) );

  $wp_customize->add_setting( 'visita_theme_options[content_width]', array(
    'default'      			=> '',
    'type'          		=> 'option',
    'capability'  			=> 'edit_theme_options',
    'sanitize_callback'		=> 'visita_get_css_units',
  ) );
}
add_action( 'customize_register', 'visita_customize_register' );
