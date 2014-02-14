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
		$itemsListTable   = new Lo_Items_List_Table();
		$user             = get_current_user_id();
		$current_per_page = get_user_meta( $user, 'pipes_per_page', true );
		if ( isset( $current_per_page ) && $current_per_page > 0 ) {
			$value = $current_per_page;
		}
		//Fetch, prepare, sort, and filter our data...
		if ( isset( $_POST['wp_screen_options']['option'] ) && $_POST['wp_screen_options']['option'] == 'pipes_per_page' ) {
// get the current admin screen
			$option = $_POST['wp_screen_options']['option'];
			$value  = $_POST['wp_screen_options']['value'];

			update_user_meta( $user, $option, $value );
		}
		if ( isset( $value ) ) {
			$itemsListTable->per_page = $value;
		}

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
			$sql = "DELETE FROM " . $wpdb->prefix . "wppipes_pipes
					WHERE `item_id` = " . $id;
			$wpdb->query( $sql );
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
			}
			$new_temp_id = $wpdb->insert_id;
			$qry         = "SELECT * FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`={$id} ORDER BY `ordering` ASC";
			$pipes       = $wpdb->get_results( $qry );
			for ( $i = 0; $i < count( $pipes ); $i ++ ) {
				$item = $pipes[$i];
				$qry  = "INSERT INTO `{$wpdb->prefix}wppipes_pipes` (`id`,`code`,`name`,`item_id`,`params`,`ordering`)";
				$qry .= "\n VALUES (NULL, '{$item->code}', '{$item->name}', {$new_temp_id}, '" . addslashes( $item->params ) . "', '{$item->ordering}')";

				if ( ! $wpdb->query( $qry ) ) {
					echo '<br />Error: ' . $wpdb->last_error;
					$error = true;
				} elseif ( isset( $_GET['x'] ) ) {
					echo '<br /><br />' . $qry;
				}
			}
		}
		$message = ( $count > 0 ) ? $count . ' pipe(s) copied successful!' : '';

		return $message;
	}
}