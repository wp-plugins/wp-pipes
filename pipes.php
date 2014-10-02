<?php
/*
Plugin Name: WP Pipes
Plugin URI: http://wpbriz.com
Description: WP Pipes plugin works the same way as Yahoo Pipes or Zapier does, give your Pipes input and get output as your needs.
Version: 1.20
Author: wpBriz
Author URI: http://wpbriz.com
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
	public static $__dashboard_screen = '';

	public function __construct( $prefix = '', $page_prefix ) {
		self::$__page_prefix = $page_prefix;
		self::$__prefix      = $prefix;
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'pipes_plugin_redirect' ) );
		parent::__construct( $prefix, $page_prefix );
	}

	public function admin_init() {
		wp_register_style( 'pipes-obstyle', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/obstyle.css' );
		wp_register_style( 'pipes-bootstrap-min', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/bootstrap.min.css' );
		wp_register_style( 'pipes-font-awesome-css', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css' );
		wp_register_style( 'pipes-process-css', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/process.css' );
		wp_register_style( 'pipes-chosen-css', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/chosen.css' );
		//wp_register_style( 'pipes-inputtags-css', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/css/bootstrap-tagsinput.css' );
		wp_register_script( 'pipes-bootstrap-min', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/bootstrap.min.js' );
		wp_register_script( 'pipes-process', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/process.js' );
		wp_register_script( 'pipes-ogb-lib-admin', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/ogb-lib-admin.js' );
		wp_register_script( 'pipes-chosen', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/chosen.jquery.js' );
		//js for input tags
		//wp_register_script( 'pipes-bootstrap-tagsinput', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/bootstrap-tagsinput.js' );

		parent::admin_init();
	}

	public function pipes_plugin_redirect() {
		if ( get_option( 'pipes_plugin_do_activation_redirect', false ) ) {
			delete_option( 'pipes_plugin_do_activation_redirect' );
			wp_redirect( "admin.php?page=pipes.pipes" );
		}
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
//			$icon_url  = plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/images/menu_icon_core.png';
			$icon_url = 'dashicons-editor-justify';
			$position = $this->get_free_menu_position( 5 );
			add_menu_page( __( "Pipes", "pipes" ), __( "Pipes", "pipes" ), "manage_options", $this->_page_prefix . ".pipes", array( $this, 'display' ), $icon_url, $position );
			if ( function_exists( "add_submenu_page" ) ) {
//				add_submenu_page( $this->_page_prefix . '.pipes', __( 'Dashboard', 'cpanel' ), __( 'Dashboard', 'cpanel' ), "manage_options", $this->_page_prefix . ".cpanel", array( $this, 'display' ) );
				$items_page = add_submenu_page( $this->_page_prefix . '.pipes', __( 'All Pipes', 'pipes' ), __( 'All Pipes', 'pipes' ), "manage_options", $this->_page_prefix . ".pipes", array( $this, 'display' ) );
				$item_page  = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Add New Pipe', 'add_new' ), __( 'Add New', 'add_new' ), "manage_options", $this->_page_prefix . ".pipe", array( $this, 'display' ) );
				$addon_page = add_submenu_page( $this->_page_prefix . '.pipes', __( 'Addons', 'plugins' ), __( 'Addons', 'plugins' ), "manage_options", $this->_page_prefix . ".plugins", array( $this, 'display' ) );
				add_submenu_page( $this->_page_prefix . '.pipes', __( 'Settings', 'settings' ), __( 'Settings', 'settings' ), "manage_options", $this->_page_prefix . ".settings", array( $this, 'display' ) );
				add_action( 'admin_print_styles-' . $item_page, array( $this, 'admin_style_item' ) );
				add_action( 'admin_print_styles-' . $items_page, array( $this, 'admin_style_item' ) );
			}
			add_action( 'load-' . $item_page, array( $this, 'on_load_page' ) );
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
		//wp_enqueue_style( 'pipes-inputtags-css' );//css for input tags
		wp_enqueue_script( 'pipes-bootstrap-min' );
		wp_enqueue_script( 'pipes-process' );
		wp_enqueue_script( 'pipes-ogb-lib-admin' );
		wp_enqueue_script( 'pipes-chosen' );
		//js for input tags
		//wp_enqueue_script( 'pipes-bootstrap-tagsinput' );

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

	public function get_free_menu_position( $start, $increment = 1 ) {
		foreach ( $GLOBALS['menu'] as $key => $menu ) {
			$menus_positions[] = $key;
		}

		if ( ! in_array( $start, $menus_positions ) ) {
			return $start;
		}

		/* the position is already reserved find the closet one */
		while ( in_array( $start, $menus_positions ) ) {
			$start += $increment;
		}

		return $start;
	}
}


function my_enqueue( $hook ) {
	//wp_enqueue_script( 'my_custom_script', plugin_dir_url( __FILE__ ) . 'assets/js/ogb.js' );
	wp_register_script( 'my_custom_script', plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/call_pipe.js' );
	wp_enqueue_script( 'my_custom_script' );
}

function ts_js() {
	echo '<script type="text/javascript">
		var obHost ="' . get_site_url() . '/";
		</script>';
}

if ( ! is_admin() ) {
	error_reporting( E_ERROR );
	if ( get_option( 'pipes_cronjob_active' ) ) {
		add_action( 'wp_enqueue_scripts', 'my_enqueue' );
		add_action( 'wp_print_scripts', 'ts_js' );
	}
}

$wplo_mvc = new PIPES( 'PIPES', 'pipes' );