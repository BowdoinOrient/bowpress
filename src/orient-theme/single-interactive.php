<?php
/*
 * Template Name: Interactive Article
 * Template Post Type: post
 */

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="single__article-header">

        <?php
        $chevronRight = '<svg version="1.1" id="Chevron_right" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve" style="width: 1.5em;"><path fill="currentcolor" d="M9.163,4.516c0.418,0.408,4.502,4.695,4.502,4.695C13.888,9.43,14,9.715,14,10s-0.112,0.57-0.335,0.787 c0,0-4.084,4.289-4.502,4.695c-0.418,0.408-1.17,0.436-1.615,0c-0.446-0.434-0.481-1.041,0-1.574L11.295,10L7.548,6.092 c-0.481-0.533-0.446-1.141,0-1.576C7.993,4.08,8.745,4.107,9.163,4.516z"/></svg>';
        $chevronLeft = '<svg version="1.1" id="Chevron_left" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve" style="width: 1.5em;"><path fill="currentcolor" d="M12.452,4.516c0.446,0.436,0.481,1.043,0,1.576L8.705,10l3.747,3.908c0.481,0.533,0.446,1.141,0,1.574 c-0.445,0.436-1.197,0.408-1.615,0c-0.418-0.406-4.502-4.695-4.502-4.695C6.112,10.57,6,10.285,6,10s0.112-0.57,0.335-0.789 c0,0,4.084-4.287,4.502-4.695C11.255,4.107,12.007,4.08,12.452,4.516z"/></svg>';
        ?>

        <!-- Taxonomy box: category and series -->
        <div class="single__taxonomy">
            <p class="single__taxonomy__section">
                <?php the_category(' ', 'single'); ?>
            </p>
            <?php if (get_the_terms($post->ID, 'series')): ?>
                <p class="single__taxonomy__series">
                    <span class="previous">
                        <?php previous_post_link('%link', $chevronLeft, true, ' ', 'series'); ?>
                    </span>
                    <span class="link">
                        <?php the_terms($post->ID, 'series', '', ' / '); ?>
                    </span>
                    <span class="next">
                        <?php next_post_link('%link', $chevronRight, true, ' ', 'series'); ?>
                    </span>
                </p>
            <?php endif; ?>
        </div>

        <!-- Article title -->
        <h1 class="single__article-title">
            <?php the_title(); ?>
        </h1>

        <!-- Article subtitle -->
        <?php if (the_subtitle('', '', false)): ?>
            <h2 class="single__article-subtitle">
                <?php the_subtitle() ?>
            </h2>
        <?php endif; ?>

        <!-- Article byline -->
        <div class="single__byline">
            <!-- <ul class="byline-box"> -->
            <p class="byline__authors">By
                <?php authorList() ?>
            </p>
            <p class="byline__roles">
                <?php authorRole() ?>
                <!-- </ul> -->
        </div>

        <!-- Date box -->
        <p class="single__pubdate">
            <?php the_date(); ?>
        </p>

    </header>

    <div class="interactive-content">

        <?php the_content() ?>

    </div>

    <h1 class="single__sidebar__heading">Most Popular</h1>

    <div class="single__sidebar__popular">
        <?php wpp_get_mostpopular(wpp_args()) ?>
    </div>

    <div class="article-comments">
        <h1>Comments</h1>
        <p>Before submitting a comment, please review our <a href="/policies/">comment policy</a>. Some key points from
            the policy:</p>

        <ul>
            <li>No hate speech, profanity, disrespectful or threatening comments.</li>
            <li>No personal attacks on reporters.</li>
            <li>Comments must be under 200 words.</li>
            <li>You are strongly encouraged to use a real name or identifier ("Class of '92").</li>
            <li>Any comments made with an email address that does not belong to you will get removed.</li>
        </ul>

        <?php comments_template(); ?>
    </div>

    <?php get_footer(); ?>