<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Field to select a user id from a modal list.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6.0
 */
class JFormFieldGRUser extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6.0
	 */
	public $type = 'User';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6.0
	 */
	
	protected function getInput(){
		$html = parent::getInput();
		$user_default =  $this->getDefaultUser();
		$html = str_replace("value=\"" . $user_default . "\"", "value=\"" . $user_default . "\" select='selected' ", $html );
		
		return $html;
	}
	
	protected function getOptions() {
		global $wpdb;
		$qr = "
			SELECT `ID` AS `value`, `display_name` AS `text` 
			FROM " . $wpdb->prefix . "users 
			WHERE `user_status` = 0
		";
		$users = $wpdb->get_results( $qr );
		
		return $users;
	}

	protected function getDefaultUser(){
		global $wpdb;
		$qry	= "SELECT `user_id` FROM " . $wpdb->prefix. "usermeta 
					WHERE `meta_key` = '" . $wpdb->prefix. "user_level' 
					AND `meta_value` = 10 ORDER BY `user_id` LIMIT 1";
		$UserId = $wpdb->get_var($qry);
		return $UserId;
	}
	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   1.6.0
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6.0
	 */
	protected function getExcluded()
	{
		return null;
	}
}
