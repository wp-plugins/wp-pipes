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
	public $item = null;
	public $form = null;
	public $other_pipes = null;
	public function __construct(){
		parent::__construct();
	}
	
	public function display(){
		$id = filter_input(INPUT_GET, 'id');
		
		$model 		= $this->getModel();
		
//		$item	= $this->get('EditData');
		$this->item = $model->getEditData();
		if ( $this->item->id ) {
			$this->other_pipes = $model->get_other_pipes( $this->item->id );
		}
		
//		echo '<pre>'.print_r( $this->item, true ).'</pre>';
//		exit();
//		$this->form = $model->getForm();
		
//		$this->pipes = $model->getPipes($id);
		$this->_layout= 'form';
		
		
		
		parent::display();
	}
}