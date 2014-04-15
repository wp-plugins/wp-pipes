<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: application.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

defined( 'JPATH_PLATFORM' ) or define( 'JPATH_PLATFORM', dirname( __FILE__ ) );
defined( 'JPATH_SITE' ) or define( 'JPATH_SITE', dirname( __FILE__ ) );

class JText {
	public static function _( $text ) {
		return __( $text, $text );
	}

	public static function alt( $text, $text ) {
		return __( $text );
	}
}

function jimport( $key = '' ) {

	$key = str_replace( 'joomla', 'includes', $key );
	$dir = str_replace( '.', DS, $key ) . '.php';
	if ( count( explode( '.', $key ) ) == 2 ) {
		$dir = 'includes' . DS . $dir;
	}
	$dir_path = dirname( dirname( __FILE__ ) ) . DS . $dir;

	if ( is_file( $dir_path ) ) {
		require_once $dir_path;
	}
}

abstract class Application {

	public $_page_prefix = '';
	public $_prefix = '';

	public function __construct( $prefix = 'Lo', $page_prefix = 'lomvc' ) {
		$this->_prefix      = $prefix;
		$this->_page_prefix = $page_prefix;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function display() {
		require_once dirname( __FILE__ ) . DS . 'view.php';
		$page = $_REQUEST['page'];
		list( $page_prefix, $type ) = explode( '.', $page );
		$view = View::getInstance( $type, $this->_prefix . 'View' );
		$view->display();
	}

	public function on_load_page() {
		require_once dirname( __FILE__ ) . DS . 'view.php';
		$page = $_REQUEST['page'];
		list( $page_prefix, $type ) = explode( '.', $page );
		$view = View::getInstance( $type, $this->_prefix . 'View' );
		if ( method_exists( $view, 'on_load_page' ) ) {
			$view->on_load_page();
		}
	}

	abstract function admin_menu();

	public function admin_init() {
		$this->execute();
	}

	public function execute() {
		require_once dirname( __FILE__ ) . DS . 'controller.php';
		$page    = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
		$task    = isset( $_REQUEST['task'] ) ? $_REQUEST['task'] : '';
		$action  = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		$action2 = isset( $_REQUEST['action2'] ) ? $_REQUEST['action2'] : '';
		if ( $action && $action != - 1 ) {
			$task = $action;
		}
		if ( $action2 && $action2 != - 1 ) {
			$task = $action2;
		}
		if ( ! $task ) {
			$task = 'display';
		}
        
		@list( $page_prefix, $type ) = count(array_filter(explode( '.', $page )))>0 ? explode( '.', $page ) : array('','') ;

		if ( $page_prefix != $this->_page_prefix 
				&& $page_prefix != 'toplevel_page_'.$this->_page_prefix ) {
			return;
		}

		$controller = Controller::getInstance( $type, $this->_prefix . 'Controller' );
		if(!$controller->existsTask($task)){
			return;
		}
		$controller->exec($task);
	}
}