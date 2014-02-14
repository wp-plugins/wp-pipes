<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: pipes.php 141 2014-01-24 10:36:21Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class PIPESControllerPlugins extends Controller {

	public function __construct() {

	}

	function display( $cachable = false, $urlparams = false ) {
		return;
	}

	public function uninstall(){
		$mod = $this->getModel( 'plugins' );
		$addon  = isset( $_GET['addon'] ) ? $_GET['addon'] : '';
		$res = $mod->uninstall($addon);
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'element', 'addon', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}


}