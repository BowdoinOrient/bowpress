<?php get_header(); ?>

<script>
function loadDoc() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var response = this.responseText.substring(0, 24);
			if(response == "<!-- @orient-archive -->") {
				window.location = "http://" + window.location.host + "/bonus" + window.location.pathname;
			}
		}
	};
	xhttp.open("GET", "http://" + window.location.host + "/bonus" + window.location.pathname, true);
	xhttp.send();
}

loadDoc();
</script>

<div class="error-content">
	<h1>404. <span></span></h1>
	<p>Unfortunately, we couldn't find the page you're looking for. Maybe try searching for it instead.</p>
	<p>If this seems like a mistake, email <a href="mailto:orientwebmaster@gmail.com">orientwebmaster@gmail.com</a>.</p>
</div>

<script>
var emoji = ["😒","🤔","😳","😞","😟","😔","😥","👻"];
var randomNumber = Math.floor(Math.random()*emoji.length);

console.log(randomNumber);

document.querySelector('.error-content h1 span').innerHTML = emoji[randomNumber];
</script>

<?php get_footer(); ?>
