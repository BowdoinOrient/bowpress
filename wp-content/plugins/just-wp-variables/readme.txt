=== Just Variables ===
Contributors: aprokopenko
Plugin Name: Just Variables for Wordpress
Plugin URI: http://justcoded.com/just-labs/just-wordpress-theme-variables-plugin/
Author: JustCoded / Alex Prokopenko
Author URI: http://justcoded.com/
Tags: theme, variables, template, text data
Requires at least: 3.4
Tested up to: 4.5.2
Donate link: http://justcoded.com/just-labs/just-wordpress-theme-variables-plugin/#donate
License: GNU General Public License v2
Stable tag: trunk

This plugin allow you to create simple text variables (single/multi-line) to use them in your theme templates after that.

== Description ==

This plugin allow you to create simple text variables (single/multi-line) to use them in your theme templates after that.
Once you added at least one variable, a new page called Theme Variables will appear under the Appearance menu.
You can move all your text data (like copyright text, phone numbers, address etc.) to variables.
So if the final client wants to change this text, he can do this easily as admin without editing the template.

This project is also available on github.com:
https://github.com/aprokopenko/justvariables.

You can post issues, bugs and your suggestion in Issues tracker there.

== Installation ==

1. Download, unzip and upload to your WordPress plugins directory.
2. Activate the plugin within your WordPress Administration Backend.
3. Go to Settings > Just Variables.
4. Add few variables that you want to use in your theme templates. After 	that, a new page called Theme Variables will be created under the 	Appearance menu.
5. Go to the Theme Variables pages and add values for variables.
6. Insert template codes.

== Upgrade Notice ==

To upgrade remove the old plugin folder. After than follow the installation steps 1-2. All settings will be saved.

== Screenshots ==

1. Plugin settings page where you can add new variables
2. Theme Variables page under the Appearance menu

== Frequently Asked Questions ==

= Q: Will my theme continue to work if I deactivate or remove this plugin? =
A: If you have inserted a template function from this plugin to your templates, then you probably get error about missing functions. You will need to clean up your template files from function calls.

== Changelog ==
* Version 1.2.2:
	* Bug fix: Updated deprecated function get_current_theme()
* Version 1.2.1:
	* Bug fix: Duplicated slashes before quotes on settings pages
* Version 1.2:
	* New: added info how to get value without printing it
	* New: added HTML5 placeholder for inputs. This will help clients to see how to enter data correctly
	* Bug fix: Broken "delete" icon in admin page
* Version 1.1 :
	* Bug fix: emergency fix for WP 3.4.1 (https://github.com/aprokopenko/justvariables/issues/3)
* Version 1.0 :
	* Tested and added language support, added Russian language
* Version 0.1 :
	* First version beta
