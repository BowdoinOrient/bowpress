<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$tip_success = false;

if($_POST) {
	// validate post array
	if(sizeof($_POST) > 2) {
		return;
	} 

	if(!$_POST['message'] || !$_POST['g-recaptcha-response']) {
		return;
	}

	// validate recaptcha

	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array('secret' => $recaptcha_secret, 
				  'response' => $_POST['g-recaptcha-response'], 
				  'remoteip' => $_SERVER['REMOTE_ADDR']);

	// use key 'http' even if you send the request to https://...
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	if(json_decode($result, true)["success"]) {
		// send slack message
		$url = $slack_tipline_url;
		$data = array('payload' => "{\"text\": \"" . $_POST["message"] . "\"}");

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context  = stream_context_create($options);
		file_get_contents($url, false, $context);
		$tip_success = true;
	}
}

get_header();
if (have_posts()) :
	while (have_posts()) :
		the_post();
	?>

	<?php
		// If this isn't the home page, do things a little differently
		if(!is_in_front_page_tree()):
	?>

		<header class="page-header">
			<h1><?php the_title() ?></h1>
		</header>

	<?php endif; ?>

	<div class="content">
		<aside>
			<?php if(get_field("sidebar")) {
				the_field("sidebar");
			} ?>
		</aside>

		<article>
			<?php if($tip_success): ?>
				<p class="tip-success-message"><strong>Your tip has been successfully submitted.</strong></p>
			<?php endif; ?>

			<p>To anonymously submit a tip or request an investigation, fill out the form below.</p>
            <p>Submissions are anonymous. Leave contact information if willing, or email <a href="mailto:orient@bowdoin.edu">orient@bowdoin.edu</a>.</p>

            <form method="POST" class="tipline-form">
                <label for="message">This will send a message to our editors.</label>
                <textarea name="message" id="message"></textarea>

                <div class="g-recaptcha" data-sitekey="6LdcpnQUAAAAAIzf4gt7ETf-_lCKShN8MKwwAj70"></div>

                <button type="submit" class="button">Submit</button>
            </form>
		</article>
	</div>

    <script src='https://www.google.com/recaptcha/api.js'></script>

<?php
	endwhile;
endif;
get_footer();
