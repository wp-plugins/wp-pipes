=== Plugin Name ===
Contributors: thongta
Donate link: http://wppipes.com/
Tags: yahoo pipes, pipes, rss, xml sitemap, posts, itunes, podcast, itunes podcast, cronjob, schedule, auto, automatic, pipeline
Requires at least: 3.8
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Pipes plugin works the same way as Yahoo Pipes or Zapier does, give your Pipes input and get output as your needs.

== Description ==

Yahoo Pipes & Zapier are powerful online services for making pipeline of data, WP Pipes comes available to the Wordpress community to bring such of powerful abilities to Wordpress site, works right inside your Wordpress site.
You can create many Pipes, give your Pipes input and get output as your needs.

See:
http://pipes.yahoo.com/

http://zapier.com/

Here are things you can do with WP Pipes (just like Yahoo Pipes):

* Get Posts from Wordpress Posts > Export as RSS Feed
* Get Posts from Wordpress Posts > Export as iTunes Podcast
* Get Posts from Wordpress Posts > Export as Google XML Sitemap
* Get Documents from Wordpress > Export to Google Drive
* Get WooCommerce Products > Export as RSS Feed or Google XML Sitemap
... It's your plugin, freebie, we provide you a tools to make pipelines, just like Yahoo Pipes, do what ever you needs. This Yahoo Pipes style plugin will empower the Wordpress CMS to a new high.

# FEATURES

* Create unlimited pipes.
* Each Pipe will start by a SOURCE and finish by a DESTINATION.
* There is PROCESSOR between SOURCE and DESTINATION to process your Pipe.
* Builtin SOURCE: RSS, Post
* Builtin DESTINATION: Post, RSS, Sitemap
* Extra SOURCEs and DESTINATIONs will come up later.
* There are number of PROCESSORS: slug, text cutter, keywords filter, metadata
* Unlimited usage, there is no FREE or PRO version.
* Smart schedule to execute pipes using cronjob.

# ROADMAP

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

= 1.1 =
~ Fixed RSS Destination to force create RSS in any file extension.

= 1.0 =
* The first version

== Upgrade Notice ==

= 1.0 =

