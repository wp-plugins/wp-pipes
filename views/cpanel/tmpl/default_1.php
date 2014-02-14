<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: default_1.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
?>
<!-- toolbar icon -->
<div class="icon32 icon32-posts-page" id="icon-index"><br></div>
<!-- toolbar -->
<h2>
	<!-- toolbar title -->
	WP Automatic Poster Dashboard
</h2>

<div id="welcome-panel" class="welcome-panel">
	<input type="hidden" id="welcomepanelnonce" name="welcomepanelnonce" value="158a022279">		<a class="welcome-panel-close" href="http://www.wppipes.com/wordpress/wp-admin/?welcome=0">Dismiss</a>
	<div class="welcome-panel-content">
		<h3>Welcome to WordPress!</h3>
		<p class="about-description">We’ve assembled some links to get you started:</p>
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h4>Get Started</h4>
				<a class="button button-primary button-hero load-customize hide-if-no-customize" href="http://www.wppipes.com/wordpress/wp-admin/customize.php">Customize Your Site</a>
				<a class="button button-primary button-hero hide-if-customize" href="http://www.wppipes.com/wordpress/wp-admin/themes.php">Customize Your Site</a>
				<p class="hide-if-no-customize">or, <a href="http://www.wppipes.com/wordpress/wp-admin/themes.php">change your theme completely</a></p>
			</div>
			<div class="welcome-panel-column">
				<h4>Next Steps</h4>
				<ul>
					<li><a href="http://www.wppipes.com/wordpress/wp-admin/post-new.php" class="welcome-icon welcome-write-blog">Write your first blog post</a></li>
					<li><a href="http://www.wppipes.com/wordpress/wp-admin/post-new.php?post_type=page" class="welcome-icon welcome-add-page">Add an About page</a></li>
					<li><a href="http://www.wppipes.com/wordpress/" class="welcome-icon welcome-view-site">View your site</a></li>
				</ul>
			</div>
			<div class="welcome-panel-column welcome-panel-last">
				<h4>More Actions</h4>
				<ul>
					<li><div class="welcome-icon welcome-widgets-menus">Manage <a href="http://www.wppipes.com/wordpress/wp-admin/widgets.php">widgets</a> or <a href="http://www.wppipes.com/wordpress/wp-admin/nav-menus.php">menus</a></div></li>
					<li><a href="http://www.wppipes.com/wordpress/wp-admin/options-discussion.php" class="welcome-icon welcome-comments">Turn comments on or off</a></li>
					<li><a href="http://codex.wordpress.org/First_Steps_With_WordPress" class="welcome-icon welcome-learn-more">Learn more about getting started</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<h3>Widgets on the Dashboard/Control Panel</h3>
<ul>
	<li>
		Step by Step instruction widget w/ Dismiss button
	</li>
	<li>
		Schedule/Cronjob Settings Widget
	</li>
	<li>
		Latest Created Pipes
	</li>
	<li>
		Version Information
	</li>
	<li>
		Support Box
	</li>
	<li>
		Graph statistic: served posts
	</li>
</ul>

<h3>Notes</h3>
<ul>
	<li>
		+ Add served posts feature on the Dashboard + Pipes Manager pages.
	</li>
	<li>
		+ Add Graph statistic widget on the Dashboard: show grabbed posts stats by daily, weekly, monthly, annually.
	</li>
	<li>
		+ Add Engines to get posts from popular sources: Articles Enzine, Clickbank, CJ, Yahoo Answers, Stackoverflow, stack*, ... find out more
	</li>
	<li>
		+ Add latest grabbed posts widget on Dashboard.
	</li>
	<li>
		! Focus on SEO features:
		<ul>
			<li>
				metadata
			</li>
			<li>
				spin title (synonym,... think)
			</li>
			<li>
				spin metadata
			</li>
			<li>
				rename image
			</li>
			<li>
				add before/after text to post body
			</li>
			<li>

			</li>
		</ul>
	</li>
	<li>
		+ Add different time cronjob for each pipe
	</li>
	<li>
		+ Add Environment widget on the dashboard.
	</li>
	<li>
		+ Pipes Listing > Add created date, updated date and served posts.
	</li>
	<li>
		+ Add Adapters: Post, Page, WooCommerce Products, bbPress
	</li>
	<li>
		~ Using WP Update Servers/Updaters lib to offer automatic updates feature. Considering to move from custom Extensions manager to the standard Wordpress Plugins Manager.
	</li>
</ul>