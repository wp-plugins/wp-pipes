<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: controller.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class Controller {
	private $model;
	private $view;
	public $task = '';
	public $tasks = array();

	public function __construct( $config ) {
//		$this->model = $model;
	}
	
	public function registTask( $key, $function ){
		$this->tasks[$key]=$function;
	}

	public function existsTask($task){
		if(array_key_exists($task, $this->tasks)){
			return true;
		}elseif (method_exists($this, $task)) {
			return true;
		}else{
			return false;
		}
	}
	
	public function exec($task){
		if (method_exists($this, $task)) {
			return call_user_func( array( $this, $task ) );
		}elseif(array_key_exists($task, $this->tasks) && method_exists($this, $this->tasks[$task])){
			return call_user_func( array( $this, $this->tasks[$task] ) );
		}
	}
	/*
		public function display(){
			$page = $_REQUEST['page'];
			$t = explode('.', $page);
			$prefix 	= $t[0];
			$viewName 	= $t[1];
			$className = 'LoView'.$viewName;
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$viewName.DIRECTORY_SEPARATOR.'view.php';
			$view = new $className;
			$view->display();
		}
	*/
	public static function getInstance( $type, $prefix = '', $config = array() ) {
		$type  = preg_replace( '/[^A-Z0-9_\.-]/i', '', $type );
		$class = $prefix . ucfirst( $type );
		if ( ! class_exists( $class ) ) {
			$path = dirname( dirname( __FILE__ ) ) . DS . 'controllers' . DS . $type . '.php';
			if ( is_file( $path ) ) {
				require_once $path;
				if ( ! class_exists( $class ) ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return new $class( $config );
	}

	/**
	 *
	 */
	public function getView( $type, $prefix = '', $config = '' ) {
		require_once 'view.php';
		if ( ! $prefix ) {
			$class  = get_class( $this );
			$t      = explode( 'Controller', $class );
			$prefix = $t[0] . 'View';
		}
		$view = View::getInstance( $type, $prefix, $config );

		return $view;
	}
	
	public function getModel( $type, $prefix = '', $config = '' ) {
		require_once 'model.php';
		if ( ! $prefix ) {
			$class  = get_class( $this );
			$t      = explode( 'Controller', $class );
			$prefix = $t[0] . 'Model';
		}
		$model = Model::getInstance( $type, $prefix, $config );

		return $model;
	}
}