<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: items.php 121 2014-01-20 10:14:24Z phonglq $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
// require_once dirname(dirname(dirname(__FILE__))).DS.'includes'.DS.'model.php';
// require_once '';
class PIPESModelPipes extends Model {
	public function getTable() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'tables' . DS . 'pipes.php';
		$itemsListTable = new Lo_Items_List_Table();
		//Fetch, prepare, sort, and filter our data...

		$itemsListTable->prepare_items();

		return $itemsListTable;
	}

	public function delete( $ids ) {
		global $wpdb;
		$ids   = ( is_array( $ids ) ) ? $ids : array( $ids );
		$count = 0;
		foreach ( $ids as $id ) {
			$sql = "DELETE FROM " . $wpdb->prefix . "wppipes_items
					WHERE `id` = " . $id;
			if ( $wpdb->query( $sql ) ) {
				$count ++;
			};
		}
		$message = ( $count > 0 ) ? $count . ' pipe(s) deleted' : '';

		return $message;
	}

	public function copy( $ids ) {
		global $wpdb;
		$count = 0;
		foreach ( $ids as $id ) {
			$sql    = "SELECT *
					FROM " . $wpdb->prefix . "wppipes_items
					WHERE `id` = " . $id;
			$insert = $wpdb->get_row( $sql, ARRAY_A );
			unset( $insert['id'] );
			$insert['name'] = $insert['name'] . ' ( copy )';
			$insert_str     = '';
			foreach ( $insert as $ins ) {
				$insert_str .= ',' . $wpdb->prepare( " %s ", $ins );
			}
			$insert_str = str_replace( '\"', '"', $insert_str );
			$sql_ins    = "INSERT INTO " . $wpdb->prefix . "wppipes_items (`id`, `name`, `published`, `engine`, `engine_params`, `adapter`, `adapter_params`, `inherit`, `inputs`, `outputs` )
			 VALUES ( NULL" . $insert_str . " )";
			if ( $wpdb->query( $sql_ins ) ) {
				$count ++;
			};
		}
		$message = ( $count > 0 ) ? $count . ' pipe(s) copied successful!' : '';

		return $message;
	}
}