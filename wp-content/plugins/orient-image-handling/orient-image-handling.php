<?php

/*
 * Plugin Name: Bowdoin Orient — Image Handling
 * Plugin URI: http://bowdoinorient.com/wordpress
 * Description: Adds shortcodes and other functionality to improve image handling within the WordPress editor.
 * Author: James Little
 * Author URI: http://jameslittle.me
 * Version: 1.0.0
 */

defined( 'ABSPATH' ) or die;


/**
 * Returns photographer objects and their associated author URL when given a
 * media ID.
 * @param  integer $id The ID of the image
 * @return [[photographer object, url string] ... ]
 */
function get_photographers($id) {
	$photographerPostObjects = get_field('photographer', $id);

	if(sizeof($photographerPostObjects) > 0) {
		$photographerArrays = array_map(function($object) {
			global $coauthors_plus;
			$id = $object->ID;
			$object =  $coauthors_plus->get_coauthor_by('id', $id);

			return array(
				'object' => $object,
				'url' => get_author_posts_url($object->ID, $object->user_nicename)
			);
		}, is_array($photographerPostObjects) ? $photographerPostObjects : array($photographerPostObjects));

		return $photographerArrays;
	} else {
		return null;
	}
}

function get_photo_credit($id) {
	if(get_field('custom_credit', $id)) {
    	// custom credit exists, use that instead of photographers
		$credit = get_field('custom_credit', $id);

		if(get_field('credit_url', $id)) {
			$url = get_field('credit_url', $id);
			return '<a href="' . $url . '">' . $credit . '</a>';
		} else {
			return $credit;
		}
	} else {
		$photographers = get_photographers($id);
		$citations = array();

		foreach ($photographers as $photographer) {
			$citations[] = '<a href="' . $photographer['url'] . '">' . $photographer['object']->display_name . '</a>';
		}

		return implode(', ', $citations);
	}
}

/**
 * Builds up a shortcode string like "[gallery ids=1,2,3,4,5]" but all of the
 * ids are the ids of the photos taken by the currently queried for user.
 * 
 * This should be used when the currently queried object is a Co-Authors Plus
 * user who has taken photos.
 */
function get_photos_by_author($should_echo = true) {
	global $wp_query;
	$curauth = $wp_query->get_queried_object();

	$posts = get_posts(array(
		'numberposts'	=> -1,
		'post_type'		=> 'attachment',
		'meta_query'	=> array(
			'relation'		=> 'AND',
			array(
				'key'	 	=> 'photographer',
				'value' => '"' . $curauth->ID . '"',
				'compare' => 'LIKE'
			),
			array(
				'key'	=> 'custom_credit',
				'value' => ''
			)
		),
	));

	wp_reset_postdata();

	$code = "[gallery ids=";

	$id_array = array_map(function($value) {
		return $value->ID;
	}, $posts);

	$ids = implode(',', $id_array);

	$code .= $ids;

	$code .= "]";

	if($should_echo) {
		echo do_shortcode($code);
	}

	return sizeof($posts);
}

/**
 * Completely replaces the thing that the WordPress editor places in the
 * text box when an image is inserted.
 * 
 * Instead of a nice little preview of the image, this makes it so a shortcode
 * gets inserted instead.
 */
function html5_insert_image($html, $id, $caption, $title, $align, $url) {
	return "[image id=$id align=normal]";
}

add_filter( 'image_send_to_editor', 'html5_insert_image', 10, 9 );

/**
 * Builds up the HTML outputted when an image shortcode is invoked.
 */
function image_shortcode($atts, $content = null) {
	$id = $atts['id'];
	$src  = wp_get_attachment_image_src( $id, 'full', false );
	$caption = get_post($id)->post_excerpt;
	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
	$html5 = "";

	if($atts['align'] == 'wide') {
		$html5 .= "[wide]";
	} else if ($atts['align'] == 'right') {
		$html5 .= "[right]";
	}

	$html5 .= "<figure id='post-$id media-$id'>";
	$html5 .= "<img src='$src[0]' alt='$alt' />";

	$credit = get_photo_credit($id);
	if($credit) {
		$html5 .= '<cite class="photo-credit">' . $credit . '</cite>';
	}

	$kicker = '';
	if(get_field('kicker', $id)) {
		$kicker = '<strong>' . get_field('kicker', $id) . '</strong> ';
	}

	if ($caption) {
		$html5 .= "<figcaption>" . $kicker . $caption . "</figcaption>";
	}
	$html5 .= "</figure>";


	if($atts['align'] == 'wide') {
		$html5 .= "[/wide]";
	} else if ($atts['align'] == 'right') {
		$html5 .= "[/right]";
	}

	return do_shortcode($html5);
}

add_shortcode( 'image', 'image_shortcode' );

/**
 * Same as above, but for gallery shortcodes.
 */
remove_shortcode('gallery');
add_shortcode('gallery', 'gallery_code');

function gallery_code($atts, $content = null) {
	$html5 = '';
	$html5 .= '<div class="carousel">';
	$ids = explode(',', $atts['ids']);
	foreach($ids as $id) {
		$html5 .= "<div class=\"slide\">[image id=$id]</div>";
	}
	$html5 .= "</div>";
	return do_shortcode($html5);
}



/**
 * Adds the shortcodes for wide article content, right-pulled article content,
 * and citations for blockquotes.
 */
function wide_shortcode( $atts, $content = null ) {
  return '<div class="single-wide-block">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'wide', 'wide_shortcode' );

function right_shortcode( $atts, $content = null ) {
  return '<div class="single-right-block">' . do_shortcode($content) . '</div>';
}
add_shortcode( 'right', 'right_shortcode' );

function cite_shortcode( $atts, $content = null ) {
  return '<cite>' . do_shortcode($content) . '</cite>';
}
add_shortcode( 'cite', 'cite_shortcode' );



