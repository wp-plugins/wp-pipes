<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: settings.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class PIPESControllerSettings extends Controller {

	public function __construct() {
		
	}

	public function display() {

	}

	public function edit() {
		
	}
	
	public function save(){
		$model = $this->getModel('settings');
		$res = $model->save();
		$msg = $res->msg;
		PIPES::add_message( $msg );
		$this->display();
	}
	
}
