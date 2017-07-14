<style>
	.content-wrap {
		max-width: 900px;
		font-size: 14px;
		line-height: 1.1;
	}

	.content-wrap p {
		font-size: 15px;
	}

	.content-wrap h2 {
		font-size: 22px;
	}

	.toc {
		float: right;
		background: white;
		border: 1px solid #888;
		padding: 15px;
		margin: 15px 15px;
		width: 250px;
	}

	.toc li {
		margin-bottom: 0;
	}

	ul {
		list-style-type: disc;
		margin: 0;
		margin-left: 1em;
	}

	li {
		margin: 0;
		margin-bottom: 0.5em;
	}
</style>

<div class="toc">
	<p><b>Table of Contents</b></p>
	<ul>
		<li>Usage
			<ul>
				<li>Adding an article</li>
				<li>Adding a photo</li>
				<li>Managing authors</li>
				<li>Changing the front page</li>
				<li>Changing other pages</li>
			</ul>
		</li>
		<li>Customization
			<ul>
				<li>Setting up a development environment</li>
				<li>Working with Visual Composer</li>
				<li>Adding new blocks and modules</li>
				<li>Creating custom article templates</li>
				<li>Embedding things in articles</li>
			</ul>
		</li>
	</ul>
</div>

<div class="content-wrap">

<h1>Bowdoin Orient WordPress Documentation</h1>

<p>The Bowdoin Orient website, bowdoinorient.com, runs on a <a href="https://wordpress.org/">WordPress</a>-based site with a custom theme originally made by James Little '19 in 2016-17. The site has been built with customization and future development in mind: the site has been built so people can make changes without interacting with the underlying code. These web pages are written to teach Bowdoin Orient publishers and web editors both how to use the site, but how to build on the WordPress structure and make significant changes. The documentation is divided into two parts: <i>usage</i>, for learning how to interact with the admin area of the site; and <i>customization</i>, for learning how the backend and theme is structured, and how to set up your computer for development.</p>

<h1>Usage</h1>

<p>If you've ever used WordPress before, you'll have a sense of how the site works. The site is divided into two parts: the public-facing side, known as the <i>frontend</i>, and the administration area, known as the <i>backend</i>. The backend is built and organized by the WordPress organization, while the frontend has been completely built by Orient developers.<p>

<p>To access the backend, press the star icon in the theme on a desktop computer, or look for the Admin Log In link in the mobile sidebar. The backend is password-protected, so you'll need to enter a username and password to enter. Once you've logged in, you'll see the Dashboard page, along with a list of links for navigating throughout the backend on the left side of the page. (If you're looking at this documentation, you've already figured all this out.)</p>

<p>The backend is divided into sections, listed in the sidebar on the left. The first sections after the Dashboard are meant for managing different types of content on the site: articles (which WordPress calls "posts"), media (such as images, documents, audio and video), static pages and alerts. Comments may appear alongside the different content types, but the Orient does away with the built-in WordPress commenting system, opting instead to use <a href="https://disqus.com/">Disqus</a>, a pre-built comment management system.</p>

<p>Underneath the different content types are some settings for managing the site. Many of these settings shouldn't be changed, as they would break existing functionality of the site. However, there are some settings editors and administrators will have to periodically adjust. This document will explain how to make these changes and in which situations it is appropriate to do so. Note that the administrator and editor accounts will have different functionality enabled, so the accounts' sidebars will look different.</p>

<h2>Adding an article</h2>

<p><em>Note: WordPress uses the term Post when referring to what the Orient calls an Article. For the purposes of this documentation, the two terms can be used interchangably.</em></p>

<p>Clicking on the "Post" link in the sidebar brings you to a list of every article, published or not, in the site's database. You can see identifying and organizational information about the articles from this list. When you've clicked on the Post link in the sidebar, a submenu will appear underneath. "Categories," "Tags," and "Series" are meant as organizational taxonomies for articles, and can be managed from each of those pages. However, we want to click on "Add New," which will bring us to the article creation page.</p>

<p>The article creation page is comprised of several panels, each panel encapsulating an option for that article's publication. Some panels can be moved around -- click and drag on the panel's title bar. Panels you might encounter are:</p>

<ul>
	<li><strong>Publish:</strong> A panel for managing the status of the article: if it's a draft, scheduled, or published.</li>
	<li><strong>Authors:</strong> A panel for choosing which author or authors have written the article.</li>
	<li><strong>Featured Image:</strong> A panel for choosing an image that might display alongside the article on the home page.</li>
	<li><strong>Post Options:</strong> A panel for setting Orient-specific options for the post.</li>
	<li><strong>Categories:</strong> A panel for setting the section in which the article is published.</li>
	<li><strong>Tags:</strong> A panel for tagging the article, so it can be grouped with articles about similar topics.</li>
	<li><strong>Series:</strong> A panel for organizing an article as part of a series, such as a column.</li>
	<li><strong>Excerpt:</strong> A panel for writing a blurb about the article that might be displayed on the front page.</li>
	<li><strong>Yoast SEO:</strong> A panel for improving the way the article's web page looks on Google.</li>
	<li><strong>Art Direction:</strong> A panel for inserting custom CSS or Javascript code into a single article to customize how an article appears on an individual basis.</li>
</ul>

<p>To add an article, fill out the title, subtitle (if applicable), and put the content in the content editor. If you're copying and pasting from another source, make sure the content doesn't have any extra space anywhere, such as too many line breaks between paragraphs or tabs at the beginning of the paragraph. If you want, you can save the article as a draft and preview it to ensure it looks the way it should.</p>

<p>Before you publish the article, make sure you've gone through each panel and set the options you want.</p>

<h3>Working with the Article Editor</h3>

<h3>Article Organization</h3>

<p>There are three different taxonomies used to categorize articles. Categories mimic the sections from the print edition. Series are used to group sequential articles or columns, such as the wine column, Talk of the Quad, or a multi-part enterprise story. Tags are a new organization method, used to group articles about the same thing, like a campus organization.</p>

<p>Each taxonomy has its own panel in the article editor. Please consider each taxonomy when creating an article. Categories should not be created or deleted. Tags should be created by editors according to a tagging policy. Series should, in general, only be created by administrators. Tags and Series can be created in the corresponding sections of the admin area, under the Posts link in the sidebar.</p>

 <p>When setting the post's category, make sure it matches up with the section in which the article was published. Do not select more than one section, even though the option is available. The <em>Uncategorized</em> category can be used for articles that are actually uncategorized in the newspaper.</p>

<p>Please make sure your article has been tagged properly. Tagging is more subjective than objective, but please try to put in a few terms that the article is about. Please use sentence case when creating tags, except when the Orient style guide says otherwise. Use discretion when creating tags.</p>

<h2>Adding a photo</h2>

<h2>Managing authors</h2>

<h2>Changing the Front Page</h2>

<h2>Changing other pages</h2>

<h1>Customization</h1>

<h2>Setting up a development environment</h2>

<h2>Working with Visual Composer</h2>

<h3>Adding new blocks and modules</h3>

<h2>Creating custom article templates</h2>

<h2>Embedding things in articles</h2>

</div>
