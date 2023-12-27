# Repository Overview: Bowdoin Orient Website

This is the repository that powers https://bowdoinorient.com/, the website for the Bowdoin Orient student newspaper.

## WordPress Overview

The website is built on WordPress. If you've never worked with WordPress before, you should read the [Intro to WordPress](Intro%20to%20WordPress) doc.

## WordPress Components

There are four custom WordPress components that together form the functionality of the Bowdoin Orient site. They are:

- [Orient Theme](Orient%20Theme.md), a WordPress theme
- [orient-home-pages](orient-home-pages), a WordPress plugin that provides a user interface for editing the home page,
- [orient-taxonomies](orient-taxonomies), a WordPress plugin that registers extra post types
- [orient-image-handling](orient-image-handling), a WordPress plugin that registers [Shortcodes](Shortcodes) that power attributed image embedding.

The source code for each of these is in the `/src/` directory in the repository.

Also in `/src/` is the `static/` directory. The contents of this directory get copied to the root of the web server. For more information, see [The Static Directory](The%20Static%20Directory).

## Writing, Submitting, and Publishing new code

You can run the Bowdoin Orient WordPress site directly on your computer. For more information, see [Local Development](Local%20Development.md).

Once you've made the changes you want locally, you can upload them to our **staging environment** to test that everything functions as expected. The staging environment is a separate server on WPEngine that mimics the environment that hosts bowdoinorient.com; if it works on the staging site, it's (almost) guaranteed to work on the production site. Read more at [[Deploying to Staging]].

Once things are looking good on staging, you'll probably want to get your code onto Github. The typical way to do this is by submitting a Pull Request: you can find instructions at [[Submitting a Pull Request]]. 

Once your pull request is approved and merged, you're safe to deploy your change to production. Read more at [[Deploying to Production]].

## Additional Resources

If you want to learn more about how to do specific things, look at one of these links:

* [[Exercise - Update the Volume and Issue in the Nameplate]]
* [[Embedding Static Content]]
* [[Bonus (Legacy Content)]]