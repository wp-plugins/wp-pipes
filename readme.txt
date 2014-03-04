=== Plugin Name ===
Contributors: thongta
Donate link: http://wpbriz.com/
Tags: pipes, csv, woocommerce, rss, syndicate, syndication, posts, auto, aggregation, aggregate, aggregator, aggregation, autoblog, autoblogging, autoblogged, import, export, migrate, migrator, migration, wordpress, blogger, blogspot, feed to post, rss to post
Requires at least: 3.8
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Powerful Data Migration Wordpress plugin: CSV importing for Posts/WooCommerce, RSS Feed Creator, AutoBlogging, auto post to Twitter/Facebook/LinkedIn

== Description ==

Yahoo Pipes & Zapier are powerful online services for making pipeline of data, WP Pipes comes available to the Wordpress community to bring such of powerful abilities to Wordpress site, works right inside your Wordpress site.
You can create many Pipes, give your Pipes input and get output as your needs.

Powerful Data Migration Wordpress plugin: csv importing for Posts/WooCommerce, RSS Feed Creator, AutoBlogging, auto post to Twitter/Facebook/LinkedIn.

<h4>Support Forum</h4>
Please visit: http://wpbriz.com/forums/

<h4>Here are things you can do with WP Pipes (just like Yahoo Pipes):</h4>

* **Feed to post**: get Posts from Wordpress Posts > Export as RSS Feed, .
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
* RSS Reader: to read RSS Feed.
* Post: to read Posts from Wordpress.
* CSV: to read CSV files (coming soon)
* WooCommerce: to deal with WooCommerce products (coming soon)
* bbPress: to deal with bbPress topics (coming soon)
* Email: to read mailbox (coming soon)
* Facebook: get (coming soon)
* Wordpress: deal with xmlrpc from Wordpress (hosted or wordpress.com) (coming soon)

<h4>AVAILABLE DESTINATIONS (more will come up later):</h4>
* RSS Creator: to create RSS Feed for Wordpress.
* Post: to create Posts for Wordpress.
* WooCommerce: to create products in WooCommerce plugin. (coming soon)
* bbPress: to create topics in bbPress plugin. (coming soon)
* Email: to send a new email to a mailbox. (coming soon)
* Wordpress.com: to create post on Wordpress.com blog. (coming soon)
* Blogger: to create blog post on Blogger. (coming soon)
* Twitter: to create Twitter tweets. (coming soon)
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

1. Upload `wppipes` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Is there any limitation when using the WP Pipes plugin? =

NO, there is no limitation when using WP Pipes plugin. It's GNU/GPL, you can download & use it for free forever.

= How do I get support if I have problems with the WP Pipes plugin? =

You can raise your support request at http://wppipes.com/forums

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

= 1.9 =
* Moved extra addons to Wordpress plugin structure, keepin the core addons at the current place.
* Added Test button to HTML Parser feature inside Get Fulltext processor.
* Added "Display the first output from Source to Source Output and Processors Output Fields".
* Removed description from Processors drop down list.

= 1.8 =
* Updated condition to check type of output data in default-item file.

= 1.7 =
* Fixed missing slug processor.
* Fixed missing combine processor.
* Fixed minor issues with UI/UX.

= 1.6 =
* Fixed error with Import feature.
* Set "excerpt" as default view mode for the Pipes listing.

= 1.5 =
* Improved slug processor.
* Cleaned source code.
* Added combine processor to combine fields together.
* Added sample source output for rssreader source.
* Wordpress.com & Blogger destination addons will come up to public soon in the next release.

= 1.4 =
* Added Import/Export feature in the "All Pipes" page.
* Added Export feature in the "Add/Edit Pipe" page as a sub-menu of Save button.
* Added Help box in the "All Pipes" page.
* Added "Welcome" box in the "All Pipes" page as the Guideline for the first time usage.

= 1.3 =
* Fixed error when choose only 1 category in Post destination.
* Only display pipes with both source and destination selected.
* Added missing jQuery in cronjob pages.

= 1.2 =
* Improved PHP code to work with PHP 5.3. It used to requires PHP 5.4 or higher.
* Allowed user to choose the number of pipes to be displayed on the Pipes Listing.
* Fixed minor bugs on Pipe form.
* Improved RSS Creator Destination addon.

= 1.1 =
* Fixed RSS Destination to force create RSS in any file extension.

= 1.0 =
* The first version

== Upgrade Notice ==

= 1.7 =
* Upgrade to this version is required to use Import feature.

= 1.6 =
* Fixed error with Import feature. Highly recommended to upgrade to this version.

= 1.0 =