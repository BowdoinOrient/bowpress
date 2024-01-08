<?php
/*
 * Template Name: Home Page
 * Template Post Type: post
 */

$homepages_dir = get_stylesheet_directory() . '/homepages/';
$file = $homepages_dir . get_field('template');

get_header(); ?>

<?php include($file); ?>

<?php get_footer(); ?>