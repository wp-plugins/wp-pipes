<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: model.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class Model {
	public static function getInstance( $type, $prefix = '', $config = array() ) {
		$type       = preg_replace( '/[^A-Z0-9_\.-]/i', '', $type );
		$modelClass = $prefix . ucfirst( $type );
		if ( ! class_exists( $modelClass ) ) {
			$path = dirname( dirname( __FILE__ ) ) . DS . 'models' . DS . strtolower( $type ) . '.php';
			if ( is_file( $path ) ) {
				require_once $path;
				if ( ! class_exists( $modelClass ) ) {
					return false;
				}
			} else {
				return false;
			}
		}

		return new $modelClass( $config );
	}
}