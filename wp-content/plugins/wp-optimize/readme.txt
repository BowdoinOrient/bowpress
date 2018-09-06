=== WP-Optimize ===
Contributors: DavidAnderson, ruhanirabin, DNutbourne, aporter, snightingale
Donate link: https://david.dw-perspective.org.uk/donate
Tags: comments, spam, optimize, database, revisions, users, posts, trash, schedule, automatic, clean, phpmyadmin, meta, postmeta, responsive, mobile
Requires at least: 3.8
Tested up to: 4.9
Stable tag: 2.2.4
License: GPLv2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WP-Optimize is WordPress's most-installed optimization plugin. With it, you can clean up your database easily and safely, without manual queries.

== Description ==

WP-Optimize is an effective tool for automatically cleaning your WordPress database so that it runs at maximum efficiency.

= Major Features =

* Removes all unnecessary data (e.g. trashed/unapproved/spam comments, stale data) plus pingbacks, trackbacks and expired transient options
* Compact/de-fragment MySQL tables with a button-press
* Detailed control of which optimizations you wish to carry out
* Carries out automatic weekly (or otherwise) clean-ups
* Retains a set number of weeks' data during clean-ups
* Performs optimizations without the need for running manual queries
* Automatically trigger a pre-optimize backup via <a href="https://updraftplus.com">UpdraftPlus</a>
* Show database statistics and potential savings
* Mobile friendly and easy-to-use
* Translated into several languages
* More planned!

= WP-Optimize helps you to: =

* <strong>Make space:</strong> When you edit a post or page on your website, WordPress automatically saves the new revision to the database. If you edit things a few times (and particularly if the post is long), your database soon gets clogged up with old revisions that just sit there, taking up valuable space. WP-Optimize removes these unnecessary post revisions, freeing up valuable Megabytes of data and increasing speed and efficiency. It also cleans up your comments table, removing all the spam and un-approved comments that have built up with a single click.

* <strong>Take control:</strong> WP-Optimize reports on exactly which of your database tables have overhead and wasted space, giving you the insight, control and power to keep your website neat, fast and efficient.

* <strong>Keep it clean:</strong> Once enabled, WP-Optimize can run an automatic clean-up on a schedule, keeping a selected number of weeks' data, according to your specification. 

When you use this plugin for the first time or just updated to major version, make a backup of your database (we recommend <a href="https://wordpress.org/plugins/updraftplus">UpdraftPlus</a>). Though none of the queries used are dangerous, it is always the best practice to make a database backup before altering your database.

= How this could help you? =

* The tables in MySQL (the database that WordPress uses) will, over time, become inefficient as data is added, removed, moved around. Asking MySQL to optimize its tables every now and again will keep your site running as fast as possible. It won't happen by itself.

* Every-time you save a new post or pages, WordPress creates a revision of that post or page. If you edit a post 6 times you might have 5 copy of that post as revisions. This quickly adds lots of rarely-used data to your database tables, making them unnecessarily bloated, and slower to access.

* Similar to the scenario described above, there might be thousands of spam and un-approved comments in your comments table, WP-Optimize can clean and remove those in a single click.

* WP-Optimize reports which database tables have overhead and wasted spaces also it allows you to shrink and get rid of those wasted spaces.

* Automatically cleans database every week and respects the "Keeps selected number of weeks data" option.

= WP-Optimize Premium =

<strong>Our free version of WP-Optimize is great, but we also have a more powerful Premium version with extra features that offer the ultimate in freedom and flexibility:</strong>

<strong>Multisite Support:</strong> extends database optimisations so they function for multiple WordPress sites at a time. If you manage more than one website, you will need WP-Optimize Premium.

<strong>Flexibility and Control:</strong> gives you the power to optimize select individual tables or a particular combination of tables on one or more WordPress websites, rather than having to optimize all database tables.

<strong>Image Optimization:</strong> removes orphaned images from your WordPress site, plus images of a certain, pre-defined size.

<strong>Sophisticated Scheduling:</strong> offers a wide range of options for scheduling automatic optimization. Specify an exact time and run clean-ups daily, weekly, fortnightly or monthly and perform any number of aditional one off optimizations.

<strong>Seamless Graphical User Interface:</strong> for superb clarity in scheduling and managing of multi-site optimizations.

<strong>WP-CLI support:</strong> provides a way to manage optimizations from command-line interface.

= Translations =

Translators are welcome to contribute to the plugin.  Please use the [WordPress translation website](https://translate.wordpress.org/projects/wp-plugins/wp-optimize).

== Installation ==

There are 3 different ways to install WP-Optimize, as with any other wordpress.org plugin.

= Using the WordPress dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'WP-Optimize'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Download the latest version of this plugin from https://wordpress.org/plugins/wp-optimize/
2. Navigate to the 'Add New' in the plugins dashboard
3. Navigate to the 'Upload' area
4. Select the zip file (from step 1.) from your computer
5. Click 'Install Now'
6. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download the latest version of this plugin from https://wordpress.org/plugins/wp-optimize/
2. Unzip the zip file, which will extract the wp-optimize directory to your computer
3. Upload the wp-optimize directory to the /wp-content/plugins/ directory in your web space
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= Is optimizing my database safe? =
Yes; optimizing does not involve any "tricks" or poking around in dangerous ways. It involves running routine clean-up operations using well-defined, common MySQL commands. Nevertheless, we always recommend backups, which cover every possibility; not just database malfunctions, but hackers, human errors, etc. We recommend <a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>.

= What savings can I expect to make? =
This is a "how long is string?" sort of question. It depends completely on your site - how big it is, how many users interact on it, how long it has been running, and when you last optimised it. However, the savings and speed-ups can be substantial; it is always worth making sure that your database is optimized.

= How do I get support? =
In our support forum, here: https://wordpress.org/support/plugin/wp-optimize/

= WP-Optimize does not make large savings on my database =
This is rare; it’s probably because you’re with a shared web hosting company that doesn’t allow scripts to run an optimize command via SQL statements (SQL "OPTIMIZE" instruction). Please consult your web hosting company on this matter.

= Is the plugin tried and tested? =
Yes - WP-Optimize is WordPress's #1 most-installed optimization plugin, with over 500,000 users and a pedigree going back over 7 years.

= Does WP-Optimize support InnoDB tables? =
WP-Optimize will disable some features if it detects InnoDB tables. Optimization of the database tables on-disk is not available, but other clean up features would work.

= Can you help me restore my database? =
I'm afraid that this is not possible. Please ensure that you make a backup of your entire database before using WP-Optimize for the first time, and when you upgrade to a major new version of it. We recommend <a href="https://wordpress.org/plugins/updraftplus/">UpdraftPlus</a>.

= Are there any bugs in this plugin? =
The plugin is an ongoing work; that means that it is impossible to rule out unforeseen situations and bugs. So I would recommend you to test it out on your local system or make a backup of your database (just to be extra careful).

= How do I stop transient options from coming back automatically each time I clear them? =
When WordPress uses a particular transient, that specific transient is re-created automatically. So, it's normaly for ones which are in active use to gradually re-appear. The best way to keep things optimal is to clear the transient options on a scheduled basis. For example, WordPress may create 50 transient option in a week. If you schedule WP-Optimize to clear these options on a weekly basis, you’ll have far fewer building up.

= The table size shows wrong / Not optimizing  =
Please check your database for corrupted tables. That can happen, usually your web hosting company can run the repair command on the db.

== Screenshots ==

1. The main optimizer Screen
2. UpdraftPlus running a backup before an optimization proceeds
3. Table Report
4. Settings

== Changelog ==

= 2.2.4 - 07/May/2018 =

* TWEAK: Changed the term 'Automatic' to 'Scheduled'.
* TWEAK: Show correct table type for views
* TWEAK: Fixed string spelling and syntax errors
* TWEAK: Disabled Simple History logging option if plugin is not installed.
* TWEAK: Prevented PHP notices in repair tables functionality

= 2.2.3 - 04/Apr/2018 =

* FEATURE: Added the ability to repair corrupted database tables
* FIX: Fixed dismiss notices functionality
* FIX: When detecting potentially unused images, exclude those found mentioned in the options table(s)
* TWEAK: Load WPO translations (logger classes info included) when template is pulled for UpdraftCentral-WPO module
* TWEAK: Add get_js_translation command for the UpdraftCentral WPO module
* TWEAK: Added logging for fatal errors

= 2.2.2 - 28/Feb/2018 =

* TWEAK: Prevent possible PHP notice when parsing logger options

= 2.2.1 - 28/Feb/2018 =

* FIX: Fix number counting in Table information tab
* FIX: Fix InnoDB optimization UI
* TWEAK: Removed orphaned attachment optimization from optimizations UI list

= 2.2.0 - 19/Feb/2018 =

* FEATURE: Added the ability to export/import WP-Optimize settings
* FEATURE: Extended the logging class to include logging for Simple History Logger, Slack, Email, syslog and a simple 'ring' log
* FEATURE: Added the ability to optimize ARCHIVE and Aria (MariaDB) database tables.
* FEATURE: Added the ability to sort data in the "Table Information" tab
* FEATURE: Added the ability to search tables in "Table Information" tab
* FEATURE: Added the ability to optimize an individual table. (Premium)
* FEATURE: Added the ability to optimize multisites. (Premium)
* FEATURE: Allow the user to create arbitrarily complex automatic optimization schedules (Premium)
* FEATURE: Added ability to use WP CLI interface for run optimizations. (Premium)
* FIX: Auto clean-up settings not optimizing database tables
* FIX: Comments count after related to comments optimization
* FIX: Fix unapproved comments count
* TWEAK: Add functions to pull WP-Optimize templates for UpdraftCentral
* TWEAK: Allow user to tick the "Take a backup with UpdraftPlus before optimizing" option from UpdraftCentral
* TWEAK: Show last automatic optimization time using site's configured timezone and locale
* TWEAK: Extra MySQL and MariaDB checks for optimization, along with an over-ride functionality.
* TWEAK: updated previous optimizations and added optimizations for trash posts, trash comments and orphaned attachements
* TWEAK: Use higher-quality spinner image
* TWEAK: Adjusted notices about other products
* TWEAK: Added message for multisite users with proposal to upgrade to Premium.
* TWEAK: Optimize the check for whether both free and Premium are installed
* TWEAK: Added Premium / Plugin Family tab
* TWEAK: Added seasonal dashboard notices  

= 2.1.1 - 28/Feb/2017 =

* FEATURE: Added the ability to take a automatic backup with UpdraftPlus (https://updraftplus.com) before an optimization
* FEATURE: When optimizing from the dashboard, tables are now optimized one by one, to reduce the scope for timeouts
* FIX: Removal of sitemeta items from main site on multisite install was not proceeding
* TWEAK: Adding premium bootstrapping to WP-Optimize for adding premium features in the future
* TWEAK: A few wording tweaks, plus automatically refresh the page if the 'admin bar menu' option is changed
* TWEAK: Introduce internal logging API

= 2.1.0 - 28/Dec/2016 =

* FOCUS: This release concentrates upon the user-interface, and in particular upon improving the clarity of each part, and making optimizations, saves and other actions possible without page reloads.
* FEATURE: Any optimisation can now be run individually with a single button press
* TWEAK: All optimisations run via the dashboard page are now run via AJAX (no page refresh)
* TWEAK: Settings saving now takes place via AJAX (no page refresh)
* TWEAK: Navigation between different tabs now takes place without a page refresh
* TWEAK: The "trackback/comments" actions section now operates via AJAX (no page refresh needed)
* TWEAK: "Refresh" button in the "Status" widget now refreshes via AJAX (no page refresh needed)
* TWEAK: When saving settings or running an optimization, the "Status" widget now automatically refreshes
* TWEAK: Make the admin bar menu into a drop-down, making it quicker to access individual tabs
* TWEAK: Call set_time_limit to reduce the chances of PHP self-terminating via reaching max_execution_time
* TWEAK: Introduce dashboard notice infrastructure
* TWEAK: The lines showing information on how many spam/trashed posts and comments existed were incomplete

= 2.0.1 - 12/Dec/2016 =

* OWNERSHIP: WP-Optimize is now under the leadership of Team UpdraftPlus - https://updraftplus.com. A big thank you to Ruhani (who remains on-board) for his leadership of WP-Optimize until this point! Layout, branding and links have been altered to reflect this change.
* RE-FACTOR: Internal code completely re-factored, laying the foundations for future improvements
* TWEAK: Various filters introduced internally for easier customisation
* TWEAK: Marked form element labels, so that they can be clicked
* TWEAK: Various small UI improvements (more to come in future releases)
* FIX: Previous versions could potentially run OPTIMIZE commands on tables in the same MySQL database (if it was shared) belonging to other sites
* FIX: Previous versions were not deleting most delete-able transients. This is now fixed, with the modification that we now delete all *expired* transients.
* LANGUAGES: Removed language packs and screenshots that are already carried by wordpress.org, reducing the plugin download / install size

= 1.9.1 =
* Ability to clean up Unused Post Meta, Comment Meta and Broken Relationship Data
* Warning prompts for RED marked items and optimize button
* Better transient options cleaning.
* Language files update
* Various other fixes

= 1.9 =
* 27 Weeks retention option equivalent to 6 month
* Compatibility update.
* Language files update
* Removed email notifications, it doesn't work on many servers
* Various other fixes

= 1.8.9.10 =
* Security Patch provided by Dion at WordPress.org and Security report provided by http://planetzuda.com .
* Language files update

= 1.8.9.8 =
* Daily Schedule Option Added
* Email notification on automatic optimization, default email is admin email address. You can change this in settings

= 1.8.9.7 =
* BUGFIX for Settings screen
* Enable/Disable trackbacks / comments buttons removal and use select box instead. Extra button caused the Auto Scheduler to get into reset mode.

= 1.8.9.6 =
* There were few number formatting problem and detection of InnoDB table format. Charles Dee Rice solved the problems that I missed out. Thank you!
* Duplicate msg fixed
* Enable/Disable trackbacks for all published post
* Enable/Disable comments for all published post

= 1.8.9 =
* ONE MILLION+ Downloads. THANK YOU!!
* Language updates platform - see readme file for details.
* Mixed type tables optimization supported and in BETA
* Removal of akismet metadata from comments
* Removal of other stale metadata from comments
* InnoDB tables won't be optimized.
* Main screen user selection will be saved. Red items selection will not be saved
* Scheduled time display will be shown according to WordPress blog local time

= 1.8.6 =
* Language files update
* Fix issues with total gain number problem
* InnoDB tables detected and features disabled automatically, tables view will not show Overhead. Main view will not show space saved, or total gain.

= 1.8.5 =
* Version bump + modified translator names

= 1.8.4 =
* Problem with readme file changes

= 1.8.3 =
* Minor fixes

= 1.8.1 =
* A whole lot more code optimization
* Slick new interface
* Responsive mobile interface, supports running from iPhone/Android/Tablets
* Tables moved to independent tab
* Optimize faster
* GitHub updater support
* I do not monitor WP forums, support email at plugins(at)ruhanirabin.com

= 1.7.4 =
* More Translation compatibility.
* Added MYSQL and PHP versions beside the Optimizer tab.

= 1.7.3 =
* Fixed Problems with wpMail.
* Fixed Problems with wpAdmin menubar.
* Fixed Permission issues on some site.
* Language files update

= 1.7.2 =
* All MySQL statements re-factored into native WP database calls - necessary for future versions of MySQL and WordPress.
* Upgrade to match WordPress 3.9 changes.
* Language files update
* Now postmeta cleanup is disabled from code - it will be updated soon with native WordPress postmeta cleaning options.

= 1.6.2 =
* Language files update

= 1.6.1 =
* Fixed - trashed Comments was not clearing out.
* Language files update

= 1.5.7 =
* Language files update

= 1.5.6 =
* "Unused Tags cleanup" option made a problem on some WordPress sites that it deletes empty categories. Since I am unable to replicate this problem. I am making this option disabled.
* Language files update
* Minor maintenance and fixes.

= 1.5.5 =
* Safe clean up options are selected by default, defaults are not by user preference for now (Optimizer Page).
* All the potentially dangerous clean up options are MARKED RED.
* Language files update
* New features explained - http://j.mp/HBIoVT (read the blog post).

= 1.5.4 =
* More path related fixes for various warnings. Maintenance

= 1.5.2 =
* Fatar error fix, if it disabled your wp admin, please remove the wp-optimize directory and reinstall again.

= 1.5.1 =
* Option to add or remove link on wp admin bar (even enabled - it is visible to admin only).
* New admin interface.
* Settings to select items for automatic optimization.
* Removal of WordPress transient options
* Removal of orphaned post meta tags.
* Removal of unused tags.
* 3 different schedule times added (weekly, bi-weekly and monthly).
* Language files update
* Code optimization and translation strings updated.
* Integrated development log from TRAC

= 1.1.2 =
* removed persistent admin bar menu item
* Language files update

= 1.1.1 =
* Fix Fatal Error.

= 1.1.0 =
* Added WP-Optimize to admin menu bar on top. Always accessible.
* Added wp-optimize.pot file for translators (inside ./languages/ folder).
* Last auto optimization timestamp / display
* Fix possible scheduler bug as requested at support forum
* Fix some other codes regarding SQL query parameters
* Ability to keep last X weeks of data, any junk data before that period will be deleted - this option affects both Auto and Manual process. Appreciate time and help from Mikel King (http://mikelking.com/) about this matter.

= 1.0.1 =
* Removed auto cleanup of trackbacks or pingbacks.. it's better for people to do it manually.

= 0.9.8-beta =
* added beta tag

= 0.9.8 =
* Remove all trackbacks and pingbacks (can significantly reduce db size)
* Remove all Trash Comments and Posts
* Enable/Disable weekly schedules of optimization. This is an EXPERIMENTAL feature. It may or may not work on all servers.

= 0.9.4 =
* Non Initialized variables fixes as of https://wordpress.org/support/topic/plugin-wp-optimize-errors-in-debug-mode?replies=2

= 0.9.3 =
* Removed security tools.
* Full database size displayed

= 0.9.2 =
* Now the plugin is visible to site administrators only. Authors, Contributors, Editors won't be able to see it.

= 0.9.1 =
* Fixed problem with database names containing "-" .
* NEW Main Level Menu Item added for WP-Optimize, You might need to scroll down to see it
* Compatibilty with WordPress 3.1
* Language files update
* Added auto draft post removal feature

= 0.8.0 =
* Added Multilanguage capability
* Language files update

= 0.7.1 =
* POST META Table cleanup code removed cause it is making problems with many hosts

= 0.7 =
* Added cleanup of POST META Table along with the revisions
* Fixed some minor PHP tags which causes the total numbers to disappear
* Now requires MySQL 5.1.x and PHP 5.1.x

= 0.6.5.1 =
* Fix Interface

== Upgrade Notice ==
* 2.2.4 : 2.2 has lots of new features, tweaks and fixes; including the introduction of a Premium version with even more features. 2.2.4 makes a number of small, cosmetic fixes.
