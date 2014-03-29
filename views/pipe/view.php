<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: view.php 141 2014-01-24 10:36:21Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

//require_once dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'view.php';

class PIPESViewPipe extends View {
	public $pipes;
	public $template = null;
	public $item = null;
	public $form = null;
	public $other_pipes = null;

	public function __construct() {
		parent::__construct();
	}

	public function display() {
		$id = filter_input( INPUT_GET, 'id' );

		$model = $this->getModel();

//		$item	= $this->get('EditData');
		$this->item = $model->getEditData();
		if ( $this->item->id ) {
			$this->other_pipes = $model->get_other_pipes( $this->item->id );
		}
		$this->templates = ogb_common::get_templates();

//		echo '<pre>'.print_r( $this->item, true ).'</pre>';
//		exit();
//		$this->form = $model->getForm();

//		$this->pipes = $model->getPipes($id);
		$this->_layout = 'form';


		parent::display();
	}

	/**
	 * Add help tab to list pipes page
	 */
	public function on_load_page() {
		$screen = get_current_screen();

		// Help Tabs
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab',
			'title'   => __( 'What is a Pipe?' ),
			'content' => '<p>' . __( 'Yahoo Pipes &amp; Zapier are powerful online services for making pipeline of data, WP Pipes comes available to the Wordpress community to bring such of powerful abilities to Wordpress site, works right inside your Wordpress site. You can create many Pipes, give your Pipes input and get output as your needs.' ) .
				'<p>' . __( '<strong>A Pipe</strong> - is a pipeline stream of data to get content for your Wordpress site.' ) .
				'<p>' . __( 'You can create as much Pipe as you want to get data from many SOURCES and store into many DESTINATION,...' ) . '</p>'
		) );

		$screen->add_help_tab( array(
			'id'      => 'my_help_tab0',
			'title'   => __( 'Title and Pipe Status' ),
			'content' => '<p>' . __( '<strong>Pipe Title</strong> - is just simply a name to identify a pipe to others.' ) . '</p>' .
				'<p>' . __( '<strong>Pipe#ID</strong> - is for refering to the ID of the Pipe which is useful information when you request support to the plugin author.' ) . '</p>' .
				'<p>' . __( '<strong>Pipe Status</strong> - give it a check if you want to enable the Pipe, it will be executed over Cronjob / Schedule.' ) . '</p>'
		) );

		$screen->add_help_tab( array(
			'id'      => 'my_help_tab1',
			'title'   => __( 'Source' ),
			'content' => '<p>' . __( '<strong>Source</strong> - is where you get the data from.' ) .
				'<p>' . __( 'There is a built-in one by default, it is RSSReader which is for dealing with RSS Feed sources.' ) . '</p>' .
				'<p>' . __( '<strong>Select a Source</strong> - to get specific Source Fields / Columns in the "Source Output" area on the bottom left area (the same heading background color as this area as).' ) . '</p>' .
				'<p>' . __( '<strong>To install more Source</strong> - please go to Extends menu' ) . '</p>'
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab2',
			'title'   => __( 'Destination' ),
			'content' => '<p>' . __( '<strong>Destination</strong> - is where the data from Source will be stored. It can be Post, WooCommerce Products or anything.' ) .
				'<p>' . __( 'The built-in one is Post which is for storing Posts from Source.' ) . '</p>' .
				'<p>' . __( '<strong>Select a Destination</strong> - to get specific Destination Fields / Columns in the "Destination Input" area on the bottom right area (the same heading background color as this area as).' ) . '</p>' .
				'<p>' . __( '<strong>To install more Destination</strong> - please go to Extends menu' ) . '</p>'
		) );
		$screen->add_help_tab( array(
			'id'      => 'my_help_tab3',
			'title'   => __( 'Fields Mapping & Processor' ),
			'content' => '<p>' . __( 'Let\'s imagine Source Output is a grabbed item from Source (for example: a Feed Item) AND Destination Input is an item from Destination (for example: a Post).' ) .
				'<p>' . __( 'In this area, you will be able to map fields from Source Output to particular fields in Destination Input.' ) . '</p>' .
				'<p>' . __( 'Basically, that\'s enough for a Pipe if your Destination Input has enough fields to map from Source Input.' ) . '</p>' .
				'<p>' . __( '<strong>Processor</strong> - is a program to cook fields from Source Output to new fields which you will need these fields for Destination Input Fields.' ) . '</p>' .
				'<p>' . __( '<strong>Click me</strong> - button will allow you to select a field to cook or map.' ) . '</p>'
		) );

		// Help Sidebar
		$screen->set_help_sidebar(
			'<p>' . __( '<strong>For more information:</strong>' ) . '</p>' .
			'<p>' . __( '<a href="http://foobla.com/wordpress/pipes">Documentation on Creating a Pipe</a>' ) . '</p>' .
			'<p>' . __( '<a href="http://foobla.com/forums">Support Forums</a>' ) . '</p>'
//			. '<p>' . __( '<a href="http://www.youtube.com/v/TO3g-_wErEI?autoplay=1&vq=hd1080" class="button button-primary"><span class="fa fa-youtube-play" title=""></span> Video Tutorial</a>' ) . '</p>'
		);
	}
}