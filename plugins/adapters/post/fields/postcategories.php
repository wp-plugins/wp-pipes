<?php
/**
 * @package		WP Pipes plugin
 * @version		$Id: postcategories.php 170 2014-01-26 06:34:40Z thongta $
 * @author		wppipes.com
 * @copyright	2014 wppipes.com. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die( 'Restricted access' );

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).DS.'includes'.DS.'form'.DS.'fields'.DS.'list.php';

class JFormFieldPostcategories extends JFormFieldList
{
	public $type = 'Postcategories';
	protected function getOptions()
	{
		$cats = $this->getCatList(0, '', array());
		
		return $cats;
	}
	
	public static function getCatList($parent_id=0, $lv='', $catsList=array()) {
		global $wpdb;
		$qr = "
			SELECT tr.term_id AS `value`, tr.name AS `text`, tt.parent AS `parent_id` 
			FROM ".$wpdb->prefix."terms AS tr 
			LEFT JOIN ".$wpdb->prefix."term_taxonomy AS tt 
			   ON (tr.term_id = tt.term_id)
			WHERE tt.taxonomy = 'category' AND tt.parent = " .(int)$parent_id . " 
			GROUP BY tt.term_id
		";
		$cats	= $wpdb->get_results( $qr );
		if (!$cats) {
			return $catsList;
		}
		$nlv = ' - - '.($lv == '' ? '' : $lv);
		foreach ($cats as $c) {
			$cat = new stdClass();
			$cat->value 			= $c->value;
			$cat->text				= $lv.$c->text;
			$cat->parent_id			= $c->parent_id;
			$cArr	= array();
			$cArr[]	= $cat;
			$subCat	= JFormFieldPostcategories::getCatList($cat->value, $nlv, array());
			if ($subCat) {
				$cArr	= array_merge($cArr,$subCat);
			}
			if (is_array($catsList)) {
				$catsList	= array_merge($catsList,$cArr);
			} else {
				$catsList	= $cArr;
			}
		}
		return $catsList;
	}
}
