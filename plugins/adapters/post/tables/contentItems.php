<?php
/**
 * @package		WP Pipes plugin
 * @version		$Id: contentItems.php 170 2014-01-26 06:34:40Z thongta $
 * @author		wppipes.com
 * @copyright	2014 wppipes.com. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die( 'Restricted access' );

class WPPipesTableContentitems extends JTable
{
	public $id						= null;
	public $asset_id				= null;
	public $title					= null;
	public $alias 					= null;
	public $title_alias				= null;
	public $introtext 				= null;
	public $fulltext				= null;
	public $state					= null;
	public $sectionid				= null;
	public $mask 					= null;
	public $catid					= null;
	public $created 				= null;
	public $created_by 				= null;
	public $created_by_alias 		= null;
	public $modified 				= null; //default =0000-00-00 00:00:00
	public $modified_by 			= null; //default= null
	public $checked_out 			= null; //default =0
	public $checked_out_time 		= null; //default =0000-00-00 00:00:00
	public $publish_up 				= null;
	public $publish_down 			= null; //default =0000-00-00 00:00:00
	public $images					= null;
	public $urls					= null;
	public $ordering 				= null;
	public $metakey 				= null;
	public $metadesc 				= null; // default = null
	public $access 					= null;
	public $hits 					= null; //default =0
	public $metadata 				= null;	// default = null
	public $featured 				= null;
	public $language 				= null; // default = *
	public $xreference 				= null; //default =0
	
	function __construct( &$db ) {
		parent::__construct('#__content','id',$db);
	}	
}