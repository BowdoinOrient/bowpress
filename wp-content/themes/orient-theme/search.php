<?php get_header(); ?>

<style>

form h1 {
	display: flex;
	align-items: baseline;
}

@media screen and (max-width: 800px) {
	form h1 {
		display: block;
	}

	form h1 em {
		display: block;
		font-size: 1rem;
		font-family: Verlag;
		text-transform: uppercase;
		margin-bottom: 0.5em;
		font-style: normal;
	}

	.inline-search-bar-submit {
		width: 100%;
		font-size: 1rem;
		font-family: Verlag;
		text-transform: uppercase;
	}

	.inline-search-bar-submit:before {
		content: "Search Again ";
	}
}

form h1 em {
	min-width: 150px;
}

.inline-search {
width: 100%;
padding: 0px 10px;
overflow-x: visible;
}

.inline-search:focus {
	border: 1px solid black;
	outline: none;
}

.inline-search-bar-submit {
	padding: 4px 15px;
}

</style>

<form action="/" method="get">
<h1><em>You searched:</em> <input type="search" name="s" class="inline-search" value="<?php echo htmlspecialchars($_GET['s']) ?>"> <button type="submit" class="inline-search-bar-submit">&rarr;</button></h1>
</form>

<script>
  (function() {
    var cx = '010341517716756046834:aijy4qiq4xc';
    var gcse = document.createElement('script');
    gcse.type = 'text/javascript';
    gcse.async = true;
    gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(gcse, s);
  })();
</script>
<gcse:searchresults-only></gcse:searchresults-only>

<?php get_footer(); ?>
