<?php

// don't load directly
if (!defined('ABSPATH')) die('-1');

class Block3 {
	function __construct() {
		// We safely integrate with VC with this hook
		add_action('init', array($this, 'register'));

		// Use this when creating a shortcode addon
		add_shortcode('block3', array($this, 'render'));

		// Register CSS and JS
		add_action('wp_enqueue_scripts', array($this, 'loadCssAndJs'));
	}

	public function register() {
		// Check if Visual Composer is installed
		if (!defined('WPB_VC_VERSION')) {
			die;
		}

		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/
		vc_map(array(
			"name" => "Block 3",
			"description" => "Picture, Title, Author",
			"base" => "block3",
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
					"type" => "checkbox",
					"holder" => "div",
					"heading" => "Should include excerpt?",
					"param_name" => "excerpt",
					"value" => "",
					"description" => "Should the modules within this block include the excerpt of the article"
				),
				array(
					"type" => "loop",
					"holder" => "div",
					"class" => "",
					"heading" => "Filter",
					"param_name" => "foo",
					"description" => "Description for foo param."
				)
			) 
		));
	}
	
	/*
	Shortcode logic how it should be rendered
	*/
	public function render( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'foo' => 'something',
			'excerpt' => '',
			'title' => '',
		), $atts));

		$query = vc_build_loop_query($foo);
		$query = $query[1];

		ob_start();

		$output = "";
		if ($title != "") {
			$output .= "<header><h1 class=\"block-title\">".$title."</h1></header>";
		}

		while($query->have_posts()) {
			$query->the_post(); 
			include('Module3.php');
			$output .= ob_get_contents();
			ob_clean();
		}

		wp_reset_postdata();
		return $output;
	}

	/*
	Load plugin css and javascript files which you may need on front end of your site
	*/
	public function loadCssAndJs() {
		//
	}
}

new Block3();
