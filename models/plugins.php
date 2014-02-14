<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: plugins.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
// require_once dirname(dirname(dirname(__FILE__))).DS.'includes'.DS.'model.php';
// require_once '';
class PIPESModelPlugins extends Model {
	public function getTable() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'tables' . DS . 'plugins.php';
		$itemsListTable = new PIPES_Plugins_List_Table();
		//Fetch, prepare, sort, and filter our data...
		$itemsListTable->prepare_items();

		return $itemsListTable;
	}

	public function uninstall( $addons ) {
		$addons   = ( is_array( $addons ) ) ? $addons : array( $addons );
		$message = array();
		foreach ($addons as $addon) {
			$name_and_type = explode("-", $addon);
			$name = $name_and_type[1];
			$type = $name_and_type[0];
			$path = OBGRAB_ADDONS . $type . 's' . DS . $name;
			if (! is_dir($path)) {
				$message[] = "$path must be a directory";
				continue;
			}
			if( self::check_addon_in_used($name) ){
				$message[] = "$name was in used, can not remove it!";
				continue;
			}
			self::deleteDir($path);
			$message[] = $name . ' uninstalled successful!';
		}
		$message = implode("</br>", $message);

		return $message;
	}

	public function check_addon_in_used($code){
		global $wpdb;
		$sql    = "SELECT *
					FROM " . $wpdb->prefix . "wppipes_pipes
					WHERE `code` = '" . $code."'";
		$result = $wpdb->get_row( $sql, ARRAY_A );
		return $result;
	}

	public function deleteDir($path){
		if (substr($path, strlen($path) - 1, 1) != '/') {
			$path .= '/';
		}
		$files = glob($path . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($path);
	}
}