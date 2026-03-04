<?php

namespace DIFL\Handler;

class Divi_Compat {
	public static function is_displayable( $output, $render_method, $element_instance ) {
		if ( ! class_exists( 'ET_Builder_Display_Conditions' ) ) {
			return $output;
		}

		$conditions = \ET_Builder_Display_Conditions::get_instance();
		$output     = $conditions->process_display_conditions( $output, $render_method, $element_instance );

		return empty( $output ) ? [] : $output;
	}
}