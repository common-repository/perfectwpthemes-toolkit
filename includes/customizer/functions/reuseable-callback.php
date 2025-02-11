<?php


/**
 *	Function to register new customizer panel
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_panel' ) ) {
	
	function perfectwpthemes_toolkit_add_panel( $id, $title, $desc, $priority ) {

		global $wp_customize;

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$panel_id = $theme_prefix. '_panel_' . $id;

		if( $priority == ''  ) {

			$priority = 10;
		}

		$wp_customize->add_panel( $panel_id,
			array(
				'title' => $title,
				'description' => $desc,
				'priority' => $priority,
			)
		);
	}	
}


/**
 *	Function to register new customizer section
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_section' ) ) {

	function perfectwpthemes_toolkit_add_section( $id, $title, $desc, $panel, $priority ) {

		global $wp_customize;

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$section_id = $theme_prefix . '_section_' . $id;

		$panel_id = $theme_prefix . '_panel_' . $panel;

		$section_args = array(
			'title'	=> $title,
			'desciption' => $desc,
		);

		if( !empty( $panel ) ) {
			$section_args['panel'] = $panel_id;
		}

		if( !empty( $priority ) ) {
			$section_args['priority'] = $priority;
		}

		$wp_customize->add_section( $section_id, $section_args );
	}
}


/**
 *	Function to register new customizer text field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_text_field' ) ) {

	function perfectwpthemes_toolkit_add_text_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type' => 'text',
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'sanitize_text_field',
				'default'				=> $defaults[$id],
			) 
		);	

		$wp_customize->add_control( $field_id, $control_args );
	}
}



/**
 *	Function to register new customizer number field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_number_field' ) ) {

	function perfectwpthemes_toolkit_add_number_field( $id, $label, $desc, $active_callback, $section, $max, $min, $step ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type' => 'number',
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		if( !empty( $max ) && !empty( $min ) && !empty( $step ) ) {

			$control_args['input_attrs'] = array(
				'min' => $min,
				'max' => $max,
				'step' => $step
			);

			$wp_customize->add_setting( $field_id, 
				array(
				'default' => $defaults[$id],
				'sanitize_callback' => 'perfectwpthemes_toolkit_sanitize_range',
				'capability'        => 'edit_theme_options',
				)
			);	

		} else {

			$wp_customize->add_setting( $field_id, 
				array(
					'default' => $defaults[$id],
					'sanitize_callback' => 'perfectwpthemes_toolkit_sanitize_number',
					'capability'        => 'edit_theme_options',
				) 
			);	
		}		

		$wp_customize->add_control( $field_id, $control_args );
	}	
}



/**
 *	Function to register new customizer url field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_url_field' ) ) {

	function perfectwpthemes_toolkit_add_url_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type' => 'url',
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'esc_url_raw',
				'default'				=> $defaults[$id],
			) 
		);	

		$wp_customize->add_control( $field_id, $control_args );
	}
}



/**
 *	Function to register new customizer email field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_email_field' ) ) {

	function perfectwpthemes_toolkit_add_email_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type' => 'email',
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'sanitize_email',
				'default'				=> $defaults[$id],
			) 
		);	

		$wp_customize->add_control( $field_id, $control_args );
	}
}


/**
 *	Function to register new customizer alpha color picker field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_alpha_color_field' ) ) {

	function perfectwpthemes_toolkit_add_alpha_color_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'desciption' => $desc,
			'show_opacity' => true,
			'palette' => false,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'sanitize_text_field',
				'default'				=> $defaults[$id],
			)
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Alpha_Color_Control( $wp_customize, $field_id, $control_args ) );
	}
}




/**
 *	Function to register new customizer image field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_image_field' ) ) {

	function perfectwpthemes_toolkit_add_image_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'desciption' => $desc,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'esc_url_raw',
				'default'				=> $defaults[$id],
			)
		);

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $field_id, $control_args ) );
	}
}



/**
 *	Function to register new customizer slider field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_slider_field' ) ) {

	function perfectwpthemes_toolkit_add_slider_field( $id, $label, $desc, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'desciption' => $desc,
			'section' => $section_id,
			'input_attrs' => array(
				'min' => 10,
				'max' => 100,
				'step' => 1,
			),
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}


		$wp_customize->add_setting( $field_id,
			array(
				'default' => $defaults[$id],
				'sanitize_callback' => 'perfectwpthemes_toolkit_sanitize_range'
			)
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Slider_Custom_Control( $wp_customize, $field_id, $control_args ) );
	}
}



/**
 *	Function to register new customizer image field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_radio_image_field' ) ) {

	function perfectwpthemes_toolkit_add_radio_image_field( $id, $label, $desc, $choices, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type'	=> 'select',
			'choices' => $choices,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'perfectwpthemes_toolkit_sanitize_select',
				'default'				=> $defaults[$id],
			)
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Radio_Image_Control( $wp_customize, $field_id, $control_args ) );
	}
}



/**
 *	Function to register new customizer toggle field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_toggle_field' ) ) {

	function perfectwpthemes_toolkit_add_toggle_field( $id, $label, $description, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label'				=> $label,
			'description'		=> $description,
			'type'				=> 'ios', // ios, light, flat
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback'		=> 'wp_validate_boolean',
				'default'				=> $defaults[$id],
			)
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Customizer_Toggle_Control( $wp_customize, $field_id, $control_args ) );
	}
}



/**
 *	Function to register new customizer repeatable group field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_repeatable_group_field' ) ) {

	function perfectwpthemes_toolkit_add_repeatable_group_field( $id, $label, $description, $defaults, $active_callback, $section, $groupname, $btnname, $fields ) {

		global $wp_customize;

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label'   => $label,
			'repeater_field_box_label' => $groupname,
			'repeater_field_box_add_control' => $btnname,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id, 
			array(
				'sanitize_callback' => 'perfectwpthemes_toolkit_repeater_sanitize',
				'default' => json_encode( $defaults ),
			) 
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Repeater_Control( $wp_customize, $field_id, $control_args, $fields ) );
	}
}


/**
 *	Function to register new customizer select field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_select_field' ) ) {

	function perfectwpthemes_toolkit_add_select_field( $id, $label, $desc, $choices, $active_callback, $section ) {

		global $wp_customize;

		$defaults = perfectwpthemes_toolkit_get_default_theme_options();

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'type' => 'select',
			'choices' => $choices,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id,
			array(
				'sanitize_callback'		=> 'perfectwpthemes_toolkit_sanitize_select',
				'default'				=> $defaults[$id],
			)
		);

		$wp_customize->add_control( $field_id, $control_args );
	}
}


/**
 *	Function to register new customizer multiple select field
 */
if( ! function_exists( 'perfectwpthemes_toolkit_add_multiple_select_field' ) ) {

	function perfectwpthemes_toolkit_add_multiple_select_field( $id, $label, $desc, $choices, $active_callback, $section ) {

		global $wp_customize;

		$theme_prefix = perfectwpthemes_toolkit_theme_prefix();

		$field_id = $theme_prefix . '_field_'. $id;

		$section_id = $theme_prefix . '_section_'. $section;

		$control_args = array(
			'label' => $label,
			'description' => $desc,
			'choices' => $choices,
			'section' => $section_id,
		);

		if( !empty( $active_callback ) ) {

			$control_args['active_callback'] = $active_callback;
		}

		$wp_customize->add_setting( $field_id,
			array(
				'sanitize_callback'		=> 'perfectwpthemes_toolkit_sanitize_multiple_select',
			)
		);

		$wp_customize->add_control( new Perfectwpthemes_Toolkit_Multiple_Select_Control ( 
				$wp_customize, $field_id, $control_args ) );
	}
}