<?php

if (!defined('ABSPATH')) die('-1');

class Block10 {
	function __construct() {
		add_action('init', array($this, 'register'));
		add_shortcode('block10', array($this, 'render'));
	}

	public function register() {
		if (!defined('WPB_VC_VERSION')) {
			die;
		}

		// https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332

		vc_map(array(
			"name" => "Block 10",
			"description" => "Block 10 Description",
			"base" => "block10",
			"class" => "",
			"controls" => "full",
			"category" => 'Content',
			"params" => array(
				array(
					"type" => "textfield",
					"holder" => "div",
					"heading" => "Title",
					"param_name" => "title",
					"value" => "",
					"description" => "Block title"
				),
				array(
					"type" => "loop",
					"holder" => "div",
					"class" => "",
					"heading" => "Post Selection",
					"param_name" => "query",
					"description" => ""
				)
			) 
		));
	}
	
	public function render( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'query' => 'query_value',
			'title' => ''
		), $atts));

		$query = vc_build_loop_query($query);
		$query = $query[1];

		ob_start();

		$output = "";
		if ($title != "") {
			$output .= "<header><h1 class=\"block-title\">".$title."</h1></header>";
		}

		while($query->have_posts()) {
			$query->the_post(); 
			include('Module10.php');
			$output .= ob_get_contents();
			ob_clean();
		}

		wp_reset_postdata();
		return $output;
	}
}

new Block10();
