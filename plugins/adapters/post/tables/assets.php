<?php
/**
 * @package		WP Pipes plugin
 * @version		$Id: assets.php 170 2014-01-26 06:34:40Z thongta $
 * @author		wppipes.com
 * @copyright	2014 wppipes.com. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die( 'Restricted access' );

class WPPipesTableAssets extends JTable
{
	public $id						= null;
	public $parent_id				= null;
	public $lft						= null;
	public $rgt	 					= null;	
	public $level					= null;
	public $name					= null;
	public $title					= null;
	public $rules					= null;
	
	
	function __construct( &$db ) {
		parent::__construct('#__assets','id',$db);
	}

}
?>