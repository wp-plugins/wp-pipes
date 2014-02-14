<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: pipes.php 141 2014-01-24 10:36:21Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class PIPESControllerPipes extends Controller {

	public function __construct() {

	}

	function display( $cachable = false, $urlparams = false ) {
		return;
	}

	public function edit() {
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
		header( 'Location: ' . $url );
	}

	public function delete() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		$res = $mod->delete( $id );
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
		//$this->display();
	}

	public function copy() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		$res = $mod->copy( $id );
		PIPES::add_message( $res );
		//$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipes';
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}


}