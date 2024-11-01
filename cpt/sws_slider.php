<?php 

// slider cpt
function sws_register_sws_slider() {

	/**
	 * Post Type: Sliders.
	 */

	$labels = [
		"name" => __( "Sliders", "twentytwenty" ),
		"singular_name" => __( "Slider", "twentytwenty" ),
	];

	$args = [
		"label" => __( "Sliders", "twentytwenty" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => false,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "sws_slider", "with_front" => false ],
		"query_var" => true,
		"supports" => [ "title" ],
	];

	register_post_type( "sws_slider", $args );
}

add_action( 'init', 'sws_register_sws_slider' );


// slider acf
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	// start slider repeater fields
	'key' => 'group_5fce7f27ca586',
	'title' => 'Slider Image',
	'fields' => array(
		array(
			'key' => 'field_5fce80411f0ee',
			'label' => 'Slide Repeater',
			'name' => 'sws_slide_repeater',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'table',
			'button_label' => '',
			'sub_fields' => array(
				array(
					'key' => 'field_5fce7f2c5e182',
					'label' => 'Image',
					'name' => 'sws_image',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'id',
					'preview_size' => 'medium',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
				//array(
				//	'key' => 'field_5fce861da2015',
				//	'label' => 'Caption',
				//	'name' => 'sws_caption',
				//	'type' => 'text',
				//	'instructions' => '',
				//	'required' => 0,
				//	'conditional_logic' => 0,
				//	'wrapper' => array(
				//		'width' => '',
				//		'class' => '',
				//		'id' => '',
				//	),
				//	'default_value' => '',
				//	'placeholder' => '',
				//	'prepend' => '',
				//	'append' => '',
				//	'maxlength' => '',
				//),
			),
		),
		array(
			'key' => 'field_5fde84d9f40e0',
			'label' => 'Show Arrows',
			'name' => 'sws_show_arrows',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
		array(
			'key' => 'field_5fde84faf40e1',
			'label' => 'Show Dots',
			'name' => 'sws_show_dots',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'message' => '',
			'default_value' => 0,
			'ui' => 0,
			'ui_on_text' => '',
			'ui_off_text' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'sws_slider',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',

));

endif;




