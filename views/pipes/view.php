<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: view.php 139 2014-01-23 10:09:44Z phonglq $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
require_once dirname( dirname( dirname( __FILE__ ) ) ) . DS . 'includes' . DS . 'view.php';

class PIPESViewPipes extends View {
	public $items = array();
	public $itemsTable = null;

	public function __construct() {
		parent::__construct();
	}

	public function display() {
		$model            = $this->getModel();
		$this->itemsTable = $model->getTable();
		//$this->_layout= 'bs3';
		parent::display();
	}

	public function on_load_page() {
		$screen = get_current_screen();

		// add meta box
		add_meta_box( 'pipes-items-helpbox-1',
			'Sidebox 1 Title',
			array( $this, 'metabox_help' ),
			$screen,
			'items_top' );

		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	function metabox_help( $data = '' ) {
		?>
		<div class="welcome-panel" id="welcome-panel">
			<input type="hidden" value="d497f3bcac" name="welcomepanelnonce" id="welcomepanelnonce">
			<a id="dismiss_help_btn" href="#" class="welcome-panel-close">Dismiss</a>

			<div class="welcome-panel-content">
				<h3>Welcome to WordPress!</h3>

				<p class="about-description">We’ve assembled some links to get you started:</p>

				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4>Get Started</h4>
						<a href="http://localhost/wordpress/wp-admin/customize.php" class="button button-primary button-hero load-customize hide-if-no-customize">Customize Your Site</a>
						<a href="http://localhost/wordpress/wp-admin/themes.php" class="button button-primary button-hero hide-if-customize">Customize Your Site</a>

						<p class="hide-if-no-customize">or,
							<a href="http://localhost/wordpress/wp-admin/themes.php">change your theme completely</a>
						</p>
					</div>
					<div class="welcome-panel-column">
						<h4>Next Steps</h4>
						<ul>
							<li>
								<a class="welcome-icon welcome-write-blog" href="http://localhost/wordpress/wp-admin/post-new.php">Write your first blog post</a>
							</li>
							<li>
								<a class="welcome-icon welcome-add-page" href="http://localhost/wordpress/wp-admin/post-new.php?post_type=page">Add an About page</a>
							</li>
							<li>
								<a class="welcome-icon welcome-view-site" href="http://localhost/wordpress/">View your site</a>
							</li>
						</ul>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4>More Actions</h4>
						<ul>
							<li>
								<div class="welcome-icon welcome-widgets-menus">Manage
									<a href="http://localhost/wordpress/wp-admin/widgets.php">widgets</a> or
									<a href="http://localhost/wordpress/wp-admin/nav-menus.php">menus</a></div>
							</li>
							<li>
								<a class="welcome-icon welcome-comments" href="http://localhost/wordpress/wp-admin/options-discussion.php">Turn comments on or off</a>
							</li>
							<li>
								<a class="welcome-icon welcome-learn-more" href="http://codex.wordpress.org/First_Steps_With_WordPress">Learn more about getting started</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

}