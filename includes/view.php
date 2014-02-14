<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: view.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class View {
	protected $_name = null;
	protected $_layout = 'default';
	protected $_models = array();
	protected $_messages = array();

	protected $_toolbar = array();

	public function __construct() {
		$className   = get_class( $this );
		$t           = explode( 'View', $className );
		$this->_name = strtolower( $t[1] );
	}
	
	public function loadTemplate($name){
		if(!$name) return;
		ob_start();
		$layoutpath = dirname( dirname( __FILE__ ) ) . DS . 'views' . DS . $this->_name . DS . 'tmpl' . DS . $this->_layout .'_'.$name.'.php';
		require_once $layoutpath;
		$res = ob_get_contents();
		ob_end_clean();
		return $res;
	}

	public function display() {
		ob_start();
		$layoutpath = dirname( dirname( __FILE__ ) ) . DS . 'views' . DS . $this->_name . DS . 'tmpl' . DS . $this->_layout . '.php';
		require_once $layoutpath;
		$res = ob_get_contents();
		ob_end_clean();

		echo '<div class="wrap">';
		echo $res;
		echo '</div>';
	}

	public function getModel( $name = '', $prefix = '', $config = array() ) {
		require_once 'model.php';
		if ( ! $name ) {
			$class = get_class( $this );
			list( $_prefix, $name ) = explode( 'View', $class );
			$prefix = $_prefix . 'Model';
		}
		$model = Model::getInstance( $name, $prefix, $config );

		return $model;
	}

	public static function getInstance( $type, $prefix = '', $config = array() ) {
		$type  = preg_replace( '/[^A-Z0-9_\.-]/i', '', $type );
		$class = $prefix . ucfirst( $type );
		if ( ! class_exists( $class ) ) {
			$path = dirname( dirname( __FILE__ ) ) . DS . 'views' . DS . strtolower( $type ) . DS . 'view.php';
			if ( $path ) {
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
	
	public function get($name){
		
		if(method_exists($this, 'get'.$name)){
			return call_user_func(array($this, 'get'.$name));
		}
	}
}