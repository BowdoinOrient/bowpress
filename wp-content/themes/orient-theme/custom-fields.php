<?php

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_ad-fields',
		'title' => 'Ad Fields',
		'fields' => array (
			array (
				'key' => 'field_5983ae39aa90a',
				'label' => 'Ad Image',
				'name' => 'ad_image',
				'type' => 'image',
				'save_format' => 'object',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_5983ae4daa90b',
				'label' => 'Ad Size',
				'name' => 'ad_size',
				'type' => 'select',
				'choices' => array (
					'Square' => 'Square',
					'Banner' => 'Banner',
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_5983aed221eea',
				'label' => 'URL',
				'name' => 'url',
				'type' => 'text',
				'instructions' => 'Enter nothing if there\'s no URL involved with the ad (but that\'d be weird).
	Ensure that the whole URL is there, with the http:// part and everything.',
				'required' => 1,
				'default_value' => '',
				'placeholder' => 'Enter \'#\' if there\'s no URL',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ad',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'permalink',
				1 => 'the_content',
				2 => 'excerpt',
				3 => 'custom_fields',
				4 => 'discussion',
				5 => 'comments',
				6 => 'revisions',
				7 => 'slug',
				8 => 'author',
				9 => 'format',
				10 => 'featured_image',
				11 => 'categories',
				12 => 'tags',
				13 => 'send-trackbacks',
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_alerts-fields',
		'title' => 'Alerts fields',
		'fields' => array (
			array (
				'key' => 'field_584b63a7f495c',
				'label' => 'Color',
				'name' => 'color',
				'type' => 'select',
				'instructions' => 'Color determines the severity of the message, which also determines the icon that appears next to the icon text.',
				'choices' => array (
					'white' => 'White',
					'green' => 'Green',
					'yellow' => 'Yellow',
					'red' => 'Red',
				),
				'default_value' => 'white',
				'allow_null' => 0,
				'multiple' => 0,
			),
/*
			array (
				'key' => 'field_584b63f3f495d',
				'label' => 'Start Date',
				'name' => 'start_date',
				'type' => 'date_picker',
				'required' => 1,
				'date_format' => 'yymmdd',
				'display_format' => 'dd/mm/yy',
				'first_day' => 0,
			),
			array (
				'key' => 'field_584b6419f495e',
				'label' => 'End Date',
				'name' => 'end_date',
				'type' => 'date_picker',
				'required' => 1,
				'date_format' => 'yymmdd',
				'display_format' => 'dd/mm/yy',
				'first_day' => 0,
			),
*/
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'alert',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'excerpt',
				1 => 'discussion',
				2 => 'comments',
				3 => 'revisions',
				4 => 'slug',
				5 => 'author',
				6 => 'format',
				7 => 'featured_image',
				8 => 'categories',
				9 => 'tags',
				10 => 'send-trackbacks',
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_media-library-fields',
		'title' => 'Media Library Fields',
		'fields' => array (
			array (
				'key' => 'field_589277fbe3e20',
				'label' => 'Kicker',
				'name' => 'kicker',
				'type' => 'text',
				'instructions' => 'The bold part of the caption. Leave blank for no kicker.',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58927818e3e21',
				'label' => 'Photographer',
				'name' => 'photographer',
				'type' => 'relationship',
				'choices' => '',
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'vertical',
				'return_format' => 'object',
				'post_type' => array (
					0 => 'guest-author',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'filters' => array (
					0 => 'search',
				),
				'result_elements' => array (
					0 => 'post_title',
				),
				'max' => '',
			),
			array (
				'key' => 'field_58927846e3e22',
				'label' => 'Custom Credit',
				'name' => 'custom_credit',
				'type' => 'text',
				'instructions' => 'A custom media credit. Overrides any set photographers.',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_58927868e3e23',
				'label' => 'Credit URL',
				'name' => 'credit_url',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'ef_media',
					'operator' => '==',
					'value' => 'all',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_page-fields',
		'title' => 'Page Fields',
		'fields' => array (
			array (
				'key' => 'field_59b09cd596947',
				'label' => 'Sidebar',
				'name' => 'sidebar',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'page',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_post-fields',
		'title' => 'Post Fields',
		'fields' => array (
			array (
				'key' => 'field_582e6decd6052',
				'label' => 'Opinion piece?',
				'name' => 'opinion',
				'type' => 'true_false',
				'instructions' => 'Check the box if this post represents the opinion of the author',
				'message' => '',
				'default_value' => 0,
			),
			array (
				'key' => 'field_58aba1a49c247',
				'label' => 'Editorial piece?',
				'name' => 'editorial',
				'type' => 'true_false',
				'instructions' => 'Check the box if this post represents the opinion of the editorial board.',
				'message' => '',
				'default_value' => 0,
			),
			array (
				'key' => 'field_58409d4834ea4',
				'label' => 'Article Style',
				'name' => 'article_style',
				'type' => 'select',
				'choices' => array (
					'regular' => 'Regular',
					'big-art' => 'Big Art',
				),
				'default_value' => 'regular',
				'allow_null' => 0,
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));

register_field_group(array (
		'id' => 'acf_packaging',
		'title' => 'Packaging',
		'fields' => array (
			array (
				'key' => 'field_59f9e2ff498ff',
				'label' => 'Articles',
				'name' => 'articles',
				'type' => 'post_object',
				'post_type' => array (
					0 => 'post',
				),
				'taxonomy' => array (
					0 => 'all',
				),
				'allow_null' => 0,
				'multiple' => 1,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'packaging',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

