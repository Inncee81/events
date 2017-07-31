<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('acf_taxonomy_field_walker') ) :

class acf_taxonomy_field_walker extends Walker {

	var $field = null,
		$tree_type = 'category',
		$db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );

	function __construct( $field ) {

		$this->field = $field;

	}

	function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0) {

		// vars
		$selected = in_array( $term->term_id, $this->field['value'] );


		// append
		$output .= '<li data-id="' . esc_attr($term->term_id) . '"><label><input type="' . esc_attr($this->field['field_type']) . '" name="' . esc_attr($this->field['name']) . '" value="' . esc_attr($term->term_id ) . '" ' . ($selected ? 'checked="checked"' : '') . ' /> <span>' . esc_attr($term->name) . '</span></label>';

	}

	function end_el( &$output, $term, $depth = 0, $args = array() ) {

		// append
		$output .= '</li>' .  "\n";

	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {

		// append
		$output .= '<ul class="children acf-bl">' . "\n";

	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {

		// append
		$output .= '</ul>' . "\n";

	}

}

endif;
