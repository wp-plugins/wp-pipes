<?php
/**
 * @package		WP Pipes plugin
 * @version		$Id: postcategories.php 121 2014-01-20 10:14:24Z phonglq $
 * @author		wppipes.com
 * @copyright	2014 wppipes.com. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die( 'Restricted access' );

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).DS.'includes'.DS.'form'.DS.'fields'.DS.'list.php';

class JFormFieldPosttags extends JFormFieldList
{
	public $type = 'Posttags';
	protected function getOptions()
	{
		/*return $rows = JHTML::_('category.options', 'com_content');
		//JOPTION_SELECT_CATEGORY
		$db = JFactory::getDBO();
		$query = "SELECT u.id as value, u.title as text FROM #__categories as u ORDER BY u.title LIMIT 100";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;*/
		$tags = $this->getTagList();
		
		return $tags;
	}
	
	public static function getTagList() {
		global $wpdb;
		$tags = get_tags();
		if (!$tags) {
			return;
		}
		$tagArr = array();
		foreach ($tags as $c) {
			$tag = new stdClass();
			$tag->value 			= $c->slug;
			$tag->text				= $c->name;
			$tagArr[] = $tag;
		}
		return $tagArr;
	}
}