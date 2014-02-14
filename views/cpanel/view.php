<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: view.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'view.php';

class PIPESViewCpanel extends View {
	public function __construct(){
		parent::__construct();
	}
	
	public function on_load_page(){
		$screen = get_current_screen();
		// Add my_help_tab if current screen is My Admin Page
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab',
			'title'   => __( 'My Help Tab' ),
			'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
		) );

		$screen->add_help_tab( array(
			'id'      => 'my_help_tab1',
			'title'   => __( 'My Help Tab' ),
			'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab2',
			'title'   => __( 'My Help Tab' ),
			'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab3',
			'title'   => __( 'My Help Tab' ),
			'content' => '<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
		) );
		
		
	// add meta box
		add_meta_box('pipes-metaboxes-sidebox-1',
				'Sidebox 1 Title', 
				array($this, 'on_sidebox_1_content'), 
				$screen, 
				'side', 'core');
		add_meta_box('pipes-metaboxes-contentbox-1',
				'Contentbox 1 Title', 
				array($this, 'on_contentbox_1_content'), 
				$screen, 
				'normal', 'core');
		add_meta_box('pipes-metaboxes-contentbox-2',
				'Contentbox 2 Title', 
				array($this, 'on_contentbox_2_content'), 
				$screen, 
				'normal', 'core');
		add_meta_box('pipes-metaboxes-contentbox-additional-1',
				'Contentbox Additional 1 Title', 
				array($this, 'on_contentbox_additional_1_content'), 
				$screen, 
				'additional', 'core');
		add_meta_box('pipes-metaboxes-contentbox-additional-2',
				'Contentbox Additional 2 Title', 
				array($this, 'on_contentbox_additional_2_content'), 
				$screen, 
				'additional', 'core');
		
		//ensure, that the needed javascripts been loaded to allow drag/drop, expand/collapse and hide/show of boxes
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');
	}

	public function display(){
//		PIPES::$__dashboard_screen = WP_Screen::get(PIPES::$__pagehook);
		parent::display();
	}
	
	public function on_load_page_cpanel(){
		

		//add several metaboxes now, all metaboxes registered during load page can be switched off/on at "Screen Options" automatically, nothing special to do therefore
	}
	
	function on_sidebox_1_content($data) {
		?>
		<ul style="list-style-type:disc;margin-left:20px;">
			<?php foreach($data as $item) { echo "<li>$item</li>"; } ?>
		</ul>
		<?php
	}
	
	
	
	function on_contentbox_1_content($data) {
		sort($data);
		?>
			<p>The given parameter at <b>sorted</b> order are: <em><?php echo implode(' | ', $data); ?></em></p>
		<?php
	}
	
	function on_contentbox_2_content($data) {
		sort($data);
		?>
		<p>The given parameter at <b>reverse sorted</b> order are: <em><?php echo implode(' | ', array_reverse($data)); ?></em></p>
		<?php
	}
	
	
	
	function on_contentbox_additional_1_content($data) {
		?>
		<p>This and the 2nd <em>additional</em> box will be addressed by an other group identifier to render it by calling with this dedicated name.</p>
		<p>You can have as much as needed box groups.</p>
		<?php
	}
	
	function on_contentbox_additional_2_content($data) {
		?>
			<p>metabox showcase - copyright &copy; 2009 Heiko Rabe (<a target="_blank" href="http://www.code-styling.de">www.code-styling.de</a>)</p>
			<p>requires at least WordPress 2.7 version, supports new box management of WordPress 2.8</p>
		<?php
	}
}