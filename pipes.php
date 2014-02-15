<?php
/**
 * @package              WP Pipes plugin
 * @version              $Id: wpap.php 167 2014-01-26 03:05:32Z thongta $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
/*
$Id: wpap.php 167 2014-01-26 03:05:32Z thongta $
Plugin Name: WP Pipes
Plugin URI: http://wppipes.com
Description: WP Pipes plugin works the same way as Yahoo Pipes or Zapier does, give your Pipes input and get output as your needs.
Version: 1.3
Author: WPPipes
Author URI: http://wppipes.com
*/
define( '_JEXEC', 1 );
@session_start();
define( "PIPES_CORE", 1 );
define( "PIPES_PATH", dirname( __FILE__ ) );
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
require_once 'define.php';
require_once dirname( __FILE__ ) . DS . 'includes' . DS . 'application.php';
require_once dirname( __FILE__ ) . DS . 'helpers' . DS . 'common.php';

class PIPES extends Application {
	public static $__page_prefix = '';
	public static $__prefix = '';
	public static $__pagehook = '';
	public static $__dashboard_screen = '';

	public function __construct( $prefix = '', $page_prefix ) {
		self::$__page_prefix = $page_prefix;
		self::$__prefix      = $prefix;
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'init', array( $this, 'init' ) );
		parent::__construct( $prefix, $page_prefix );
	}

	public function admin_init() {
		wp_register_style( 'pipes-obstyle', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/obstyle.css' );
		wp_register_style( 'pipes-bootstrap-min', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/bootstrap.min.css' );
		wp_register_style( 'pipes-font-awesome-css', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
		wp_register_style( 'pipes-process-css', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/process.css' );
		wp_register_style( 'pipes-chosen-css', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/chosen.css' );
		wp_register_script( 'pipes-bootstrap-min', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/bootstrap.min.js' );
		wp_register_script( 'pipes-process', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/process.js' );
		wp_register_script( 'pipes-ogb-lib-admin', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/ogb-lib-admin.js' );
		wp_register_script( 'pipes-chosen', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/chosen.jquery.js' );
		parent::admin_init();
	}

	public function init() {
		require_once dirname( __FILE__ ) . DS . 'plugin.php';
		pipes_system::cronjob();
	}

	public function pipes_default_options() {
		global $pipes_settings;
		include_once( dirname( __FILE__ ) . DS . 'settings-init.php' );

		foreach ( $pipes_settings as $section ) {
			foreach ( $section as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					add_option( $value['id'], $value['default'] );
				}
			}
		}
	}

	public function admin_menu() {
		# add main menu
		if ( function_exists( "add_menu_page" ) ) {
			$controllers_dir = dirname( __FILE__ ) . DS . 'controllers';
			#TODO: change way to get icon url
//			$icon_url  = plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/images/menu_icon_core.png';
			$icon_url   = 'dashicons-editor-justify';
			$position   = 6;
			$pipes_page = add_menu_page( __( "Pipes", "pipes" ), __( "Pipes", "pipes" ), "manage_options", $this->_page_prefix . ".pipes", array( $this, 'display' ), $icon_url, $position );
			if ( function_exists( "add_submenu_page" ) ) {
//				add_submenu_page( $this->_page_prefix . '.pipes', __( 'Dashboard', 'cpanel' ), __( 'Dashboard', 'cpanel' ), "manage_options", $this->_page_prefix . ".cpanel", array( $this, 'display' ) );
				$items_page = add_submenu_page( $this->_page_prefix . '.pipes', __( 'All Pipes', 'pipes' ), __( 'All Pipes', 'pipes' ), "manage_options", $this->_page_prefix . ".pipes", array( $this, 'display' ) );
				$item_page  = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Add New Pipe', 'add_new' ), __( 'Add New', 'add_new' ), "manage_options", $this->_page_prefix . ".pipe", array( $this, 'display' ) );
				$addon_page = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Addons', 'plugins' ), __( 'Addons', 'plugins' ), "manage_options", $this->_page_prefix . ".plugins", array( $this, 'display' ) );
				add_submenu_page( $this->_page_prefix . '.pipes', __( 'Settings', 'settings' ), __( 'Settings', 'settings' ), "manage_options", $this->_page_prefix . ".settings", array( $this, 'display' ) );
				add_action( 'admin_print_styles-' . $item_page, array( $this, 'admin_style_item' ) );
				add_action( 'admin_print_styles-' . $items_page, array( $this, 'admin_style_item' ) );
				add_action( 'load-' . $item_page, array( $this, 'add_pipe_help_tab' ) );
			}
//			add_action('load-'.$pipes_page, array( $this, 'add_pipes_help_tab') );
//			add_action('load-'.$pipes_page, array( $this, 'add_pipes_screen_options') );
			self::$__pagehook = $pipes_page;
//			add_action('load-'.$pipes_page, array( $this, 'on_load_page_cpanel') );
			add_action( 'load-' . $pipes_page, array( $this, 'on_load_page' ) );
			add_action( 'load-' . $items_page, array( $this, 'on_load_page' ) );
			add_action( 'load-' . $addon_page, array( $this, 'on_load_page' ) );

		}
	}

	public function install() {
		$filename = pathinfo( __FILE__, PATHINFO_BASENAME );
		$filepath = dirname( __FILE__ ) . DS . 'install.' . $filename;
		$this->pipes_default_options();
		require_once $filepath;
	}

	public function uninstall() {
		$filename = pathinfo( __FILE__, PATHINFO_BASENAME );
		$filepath = dirname( __FILE__ ) . DS . 'uninstall.' . $filename;
		require_once $filepath;
	}

	public function admin_style_item() {
		wp_enqueue_style( 'pipes-obstyle' );
		wp_enqueue_style( 'pipes-bootstrap-min' );
		wp_enqueue_style( 'pipes-bootstrap-extended' );
		wp_enqueue_style( 'pipes-font-awesome-css' );
		wp_enqueue_style( 'pipes-process-css' );
		wp_enqueue_style( 'pipes-chosen-css' );
		wp_enqueue_script( 'pipes-bootstrap-min' );
		wp_enqueue_script( 'pipes-process' );
		wp_enqueue_script( 'pipes-ogb-lib-admin' );
		wp_enqueue_script( 'pipes-chosen' );
	}

	public static function add_message( $msg, $type = 'message' ) {
		if ( ! isset( $_SESSION['PIPES']['messages'] ) || empty( $_SESSION['PIPES']['messages'] ) ) {
			$_SESSION['PIPES']['messages'][]    = array( 'msg' => $msg, 'type' => $type );
			$_SESSION['PIPES']['messages_show'] = 0;
		}
	}

	public static function show_message( $bootstrap = false ) {
		if ( isset( $_SESSION['PIPES']['messages'] ) && count( $_SESSION['PIPES']['messages'] ) ) {
			$msgs    = $_SESSION['PIPES']['messages'];
			$classes = array( 'message' => 'info',
							  'error'   => 'danger',
							  'warning' => 'warning' );
			$out     = array();
			foreach ( $msgs as $msg ) {
				$msg_class = isset( $classes[$msg['type']] ) ? $classes[$msg['type']] : $classes['message'];
				if ( $bootstrap ) {
					$out[] = '<div class="alert alert-' . $msg_class . ' fade in">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<div>' . $msg['msg'] . '</div>
							</div>';
				} else {
					$out[] = '<div id="message" class="updated"><p>' . $msg['msg'] . '</p></div>';
				}

			}
			$contens                       = implode( '', $out );
			$_SESSION['PIPES']['messages'] = array();

			return $contens;
		}
	}

	public function add_pipe_help_tab() {
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
			'<p>' . __( '<a href="http://wppipes.com">Documentation on Creating a Pipe</a>' ) . '</p>' .
			'<p>' . __( '<a href="http://wppipes.com/forums">Support Forums</a>' ) . '</p>'
//			. '<p>' . __( '<a href="http://www.youtube.com/v/TO3g-_wErEI?autoplay=1&vq=hd1080" class="button button-primary"><span class="fa fa-youtube-play" title=""></span> Video Tutorial</a>' ) . '</p>'
		);
	}
}

$wplo_mvc = new PIPES( 'PIPES', 'pipes' );
