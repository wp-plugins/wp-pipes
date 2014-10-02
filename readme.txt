=== Plugin Name ===
Contributors: thongta,phamtungpth,coven-eye
Donate link: http://wpbriz.com/
Tags: pipes, csv, woocommerce, rss, syndicate, syndication, auto post, aggregation, aggregate, aggregator, aggregation, autoblog, autoblogging, autoblogged, autopost, posts, seo, automation, automatic, import, export, migrate, migrator, migration, wordpress, blogger, blogspot, feed to post, rss to post
Requires at least: 3.8
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

RSS Feed to Post/bbPress, AutoBlogging, auto post to Twitter/Facebook/LinkedIn, CSV importing for Posts/WooCommerce/bbPress, RSS Feed Creator.

== Description ==

Yahoo Pipes & Zapier are powerful online services for making pipeline of data, [WP Pipes](http://wpbriz.com/pipes "WP Pipes plugin") by [wpBriz](http://wpbriz.com "Time Saving Wordpress Plugins") comes available to the Wordpress community to bring such of powerful abilities to Wordpress site, works right inside your Wordpress site.
You can create many Pipes, give your Pipes input and get output as your needs.

Powerful Data Migration Wordpress plugin: csv importing for Posts/WooCommerce, RSS Feed Creator, AutoBlogging, auto post to Twitter/Facebook/LinkedIn.

Support: [wp.org Forum (this website)](http://wordpress.org/support/plugin/wp-pipes), [Twitter](https://twitter.com/wpbriz "wpBriz Twitter"), [Facebook](https://www.facebook.com/wpbriz "wpBriz Facebook"), [Google+](https://plus.google.com/+Wpbriz/posts "wpBriz Google+"), [Youtube](https://www.youtube.com/user/wpbriz "wpBriz Youtube")

[youtube https://www.youtube.com/watch?v=zLHFqAHQj2A&hd=1&&cc_load_policy=1]

<h4>Here are things you can do with WP Pipes (just like Yahoo Pipes):</h4>

* **RSS Feed to post**: a powerful RSS Feed to Post solution, get newsfeed from RSS Feed source and store into your Wordpress as posts.
* **RSS Feed Creator**: getting Posts from Wordpress Posts > Export as RSS Feed.
* **iTunes Podcast creator**: get Posts from Wordpress Posts > Export as iTunes Podcast, 
* **Google XML Sitemap generator**: get Posts from Wordpress Posts > Export as Google XML Sitemap
* **WooCommerce RSS Feed creator**: get WooCommerce Products > Export as RSS Feed or Google XML Sitemap
* **CSV Importer for WooCommerce**: upload CSV files and import to WooCommerce.
* **Auto Social Poster**: post from Posts, WooCommerce Products, bbPress topics / comments to Twitter, Facebook, LinkedIn, Google+ Moments, Pinterest, Vkontakle, ...
... It's your plugin, freebie, we provide you a tools to make pipelines, just like Yahoo Pipes, do what ever you needs. This Yahoo Pipes style plugin will empower the Wordpress CMS to a new high.

<h4>FEATURES</h4>

* Requires PHP 5.3 or higher with JSON, cURL to work properly.
* Create unlimited pipes.
* Each Pipe will start by a SOURCE and finish by a DESTINATION.
* There is PROCESSOR between SOURCE and DESTINATION to process your Pipe.
* Builtin SOURCE: RSS, Post
* Builtin DESTINATION: Post, RSS, Sitemap
* Extra SOURCEs and DESTINATIONs will come up later.
* There are number of PROCESSORS: slug, text cutter, keywords filter, metadata
* Unlimited usage, there is no FREE or PRO version.
* Smart schedule to execute pipes using cronjob.

<h4>AVAILABLE SOURCES (more will come up later):</h4>
* RSS Reader: to read RSS Feed, to offer RSS Feed to Post functionality.
* Post: to read Posts from Wordpress.
* [CSV: to read CSV files](http://wpbriz.com/shop/csv-source-pipes/ "Wordpress plugin import from CSV").
* WooCommerce: to deal with WooCommerce products (coming soon)
* bbPress: to deal with bbPress topics (coming soon)
* Email: to read mailbox (coming soon)
* Facebook (coming soon)
* Wordpress.com: using oauth to connect to your blogs on wordpress.com.
* Wordpress: deal with xmlrpc from Wordpress (hosted or wordpress.com) (coming soon)

<h4>AVAILABLE DESTINATIONS (more will come up later):</h4>
* RSS Creator: to create RSS Feed for Wordpress.
* Post: to create Posts for Wordpress. Can works with RSSReader source to offer RSS Feed to post functionality.
* WooCommerce: to create products in WooCommerce plugin. (coming soon)
* bbPress: to create topics in bbPress plugin. (coming soon)
* Email: to send a new email to a mailbox. (coming soon)
* [Wordpress.com: to create post on Wordpress.com blog](http://wpbriz.com/shop/wordpress-com-destination-pipes/ "Wordpress plugin auto post to Wordpress.com").
* [Blogger: to create blog post on Blogger](http://wpbriz.com/shop/blogger-blogspot-destination-for-pipes/ "Wordpress plugin Auto post to Blogger / Blogspot").
* [Twitter: to create Twitter tweets](http://wpbriz.com/shop/twitter-destination-pipes/ "Wordpress plugin Auto post to Twitter").
* Facebook: to create message on Facebook personal wall. (coming soon)
* Facebook Page: to create message on Facebook Page. (coming soon)
* Facebook Group: to create message on Facebook Group. (coming soon)
* LinkedIn: to create message on LinkedIn personal wall. (coming soon)
* LinkedIn Group: to create message on LinkedIn Group wall. (coming soon)
* LinkedIn Company: to create message on LinkedIn Company wall. (coming soon)
* Vkontakle: to create message on Vkontakle (VK). (coming soon)
* Google Plus (Google+ or G+): to create message on Google+ / G+ / Google Plus. (comming soon)

<h4>AVAILABLE PROCESSORS (more will come up later):</h4>
* Alias: create slug from text/title/subject.
* Combine: combine fields together into one output field using shortcode. (new)
* Duplicate: check and prevent duplicate data items from source, recommend to use right after alias.
* Cut Introtext: cutting text into two parts.
* Get Fulltext: getting fulltext from a link.
* Get Images: get images from a link or html.
* Keywords Filter: filter by keywords with AND, OR and NOT operators.
* Strip Tags: strip html tags out of input html or text.
* Change Time: adjust date/time.

<h4>ROADMAP</h4>

* Writing more Source Addons: WooCommerce Products, Easy Digital Downloads, bbPress; will add ability to WP Pipes to create RSS Feed for WooCommerce, Easy Digital Downloads or bbPress.
* Writing more Destination Addons: Google Drive (to store document as Google Drive Docs),iTunes Podcast (to generate iTunes Podcast), Google XML Sitemap (to generate Google XML Sitemap).
* Custom schedule for each Pipe instead of the whole Pipes.
* Adding Pre-made / Template Fields Matching sets.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `wp-pipes` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. A new menu item "Pipes" created right after Posts on the admin sidebar.

== Frequently Asked Questions ==

= Is there any limitation when using the WP Pipes plugin? =

NO, there is no limitation when using WP Pipes plugin. It's GNU/GPL, you can download & use it for free forever.

= What is a Pipe? =

Let's imagine it as a pipeline of data from one place (Source) to another place (Destination).
One Pipe will start with a Source.
On the way of each Pipe, it can pass over some or many Processors to transform its format to suit the Destination.
At the end, it will stop at a Destination.

= What is a WP Pipes Source? =

Pipe Source is where your Pipe begins. It can be: Posts, Pages, WooCommerce Products, RSS Feeds, Files/Folders,... the sky is unlimited :)

= What is a WP Pipes Destination? =

Pipe Destination is the end of your Pipes. It can be: RSS Feeds, Google XML Sitemap, Files/Folders, WooCommerce Products, Posts,... once again, the sky is unlimited :)

= What is a WP Pipes Processor? =

Pipe Processor is for doing some processes to get more fields for the Destination Input.
Pipe Processor will process some input fields (depends on Processor) and provide some output fields. After that, you can use those output fields for the Destination Input.

== Screenshots ==

1. All Pipes: list of all created Pipes.
2. Add New Pipe: create a data pipeline from SOURCE to DESTINATION
3. Addons: empower WP Pipes by adding more SOURCES, DESTINATIONS.
4. Settings: where to configure cronjob.

== Changelog ==

= 1.20 =
* Fix: solved issue "Parse error: syntax error, unexpected T_PAAMAYIM_NEKUDOTAYIM in /wp-content/plugins/wp-pipes/grab.php on line 163"
which appears with some version of PHP.

= 1.19 =
* New: added filter options "minimum words" and "maximum words" in Get Fulltext processor
* New: added more input field of Get Image processor, could get images from enclosures
* Fix: get free position of menu only after Post menu
* New: add 1 more method of using curl in both common file and psc file of Get Fulltext processor.
* New: add 1 more option allow choosing use cache or not when running cronjob

= 1.18 =
* New: checked free position of menu before activating
* New: added 1 more case for Keyword Filter processor: the keywords are Latin
* New: added option in Post Destination allow using custom fields or not
* New: added sort by date with RSSReader source

= 1.17 =
* Fix: Fixed some minor bugs in Get Fulltext, Get Image processor.
* Fix: Removed some unnecessary Joomla code
* Fix: Checked duplicate custom fields when creating the inputs of Post Destination.
* New: Use other way in using curl_level3 instead of the old way.
* New: Added 1 option allow choosing stop processing item without images or not

= 1.16 =
* Fix: Fixed notice, minor bug.
* Fix: Clear HTML comment.
* Fix: Clear tag and parse code cho hr, link, br, img, meta, input.
* Fix: Get image Processor.
* New: Use other way in using curl instead of the old way.
* New: Set input as the current time if leave blank in Change Time processor.

= 1.15 =
* Fix: Repaired get full title with rssreader source.
* Fix: Fixed bugs: pipes not run when turn off debug mode.
* Fix: active and cron active in setting
* New: Able to set separate schedule for each pipe.
* New: Added quick_edit mode for pipes.
* New: Check writable permission of "cache" folder and "upload" folder.
* Improve: Possible to remove unnecessary templates.

= 1.14 =
* Fix: Repaired write cache function in rssreader source.
* Fix: Fixed errors when click Test this pipe button. It was caused by define.
* Fix: Fixed bug when save image from redirect urls to server.
* Fix: Not use cache in cronjob.
* New: Able to set a Pipe as a Pipe template. Can be loaded later conveniently.
* New: Added one requirement: turn on allow_url_fopen.
* New: Auto fix html input data for Blogger Destination.
* New: Saving the export pipe as a template; in edit view, could load that template.
* Improve: Moved the templates folder to uploads > wppipes.
* Improve: Rewrote ajax in post.js.
* Improve: Added User Agent input for get fulltext processor.

= 1.13 =
* New: Added line number to the Parser Code area in the Get Fulltext processor.
* Improve: Pipes core tweaks to allow redirection to Pipes plugin after activating a Pipes add-ons plugin.

= 1.12 =
* Fix: Fixed minor bugs.
* Improve: Improved Pipes UI/UX.

= 1.11 =
* New: Added drag & drop feature for fields matching area.
* Improve: Improved fields matching UI/UX.
* Improve: Improved Get Fulltext processor UX.
* Improve: Improved Import feature UX.

= 1.10 =
* Fix: Emergency fix for wrong condition to get addons (sources, engines and processors) for Pipes.

= 1.9 =
* Improve: Moved extra addons to WordPress plugin structure, keepin the core addons at the current place.
* New: Added Test button to HTML Parser feature inside Get Fulltext processor.
* New: Added "Display the first output from Source to Source Output and Processors Output Fields".
* Improve: Removed description from Processors drop down list.

= 1.8 =
* Improve: Updated condition to check type of output data in default-item file.

= 1.7 =
* Fix: Fixed missing slug processor.
* Fix: Fixed missing combine processor.
* Fix: Fixed minor issues with UI/UX.

= 1.6 =
* Fix: Fixed error with Import feature.
* Improve: Set "excerpt" as default view mode for the Pipes listing.

= 1.5 =
* Improve: Improved slug processor.
* Improve: Cleaned source code.
* Improve: Added combine processor to combine fields together.
* New: Added sample source output for rssreader source.
* Msg: Wordpress.com & Blogger destination addons will come up to public soon in the next release.

= 1.4 =
* New: Added Import/Export feature in the "All Pipes" page.
* New: Added Export feature in the "Add/Edit Pipe" page as a sub-menu of Save button.
* New: Added Help box in the "All Pipes" page.
* New: Added "Welcome" box in the "All Pipes" page as the Guideline for the first time usage.

= 1.3 =
* Fix: Fixed error when choose only 1 category in Post destination.
* Fix: Only display pipes with both source and destination selected.
* Fix: Added missing jQuery in cronjob pages.

= 1.2 =
* Improve: Improved PHP code to work with PHP 5.3. It used to requires PHP 5.4 or higher.
* New: Allowed user to choose the number of pipes to be displayed on the Pipes Listing.
* Fix: Fixed minor bugs on Pipe form.
* Improve: Improved RSS Creator Destination addon.

= 1.1 =
* Fix: Fixed RSS Destination to force create RSS in any file extension.

= 1.0 =
* New: The first version

== Upgrade Notice ==

= 1.7 =
* Upgrade to this version is required to use Import feature.

= 1.6 =
* Fixed error with Import feature. Highly recommended to upgrade to this version.

= 1.0 =
* first release