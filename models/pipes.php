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
			$path = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
			if ( is_file( $path ) ) {
				$folder = dirname( $path );
				unlink( $path );
				rmdir( $folder );
			}
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

	public function change_status( $ids, $status = 1 ) {
		global $wpdb;
		$str_id  = implode( ",", $ids );
		$count   = count( $ids );
		$sql_ins = "UPDATE `{$wpdb->prefix}wppipes_items` SET `published` = {$status} WHERE `id` IN ( {$str_id} )";

		if ( ! $wpdb->query( $sql_ins ) ) {
			echo '<br />Error: ' . $wpdb->last_error;
			$error = true;
		} elseif ( isset( $_GET['x'] ) ) {
			echo '<br /><br />' . $sql_ins;
		}
		$message = ( $status ) ? $count . ' pipe(s) been published!' : $count . ' pipe(s) been drafted!';

		return $message;
	}

	public function export_to_share( $ids ) {
		global $wpdb;
		$res   = new stdClass();
		$msg   = array();
		$ids   = ( is_array( $ids ) ) ? $ids : array( $ids );
		$items = array();
		foreach ( $ids as $id ) {
			$sql  = "SELECT * from`{$wpdb->prefix}wppipes_items` WHERE `id` = $id";
			$item = $wpdb->get_row( $sql );
			if ( ! is_object( $item ) ) {
				$msg[] = "Can not find the pipe #$id";
			} else {
				if ( $item->adapter == '' ) {
					$msg[] = "The pipe #$id still not use any adapters";
					continue;
				}
				if ( $item->engine == '' ) {
					$msg[] = "The pipe #$id still not use any engines";
					continue;
				}
				if ( $item->adapter == 'post' ) {
					$adapter_params_array           = json_decode( $item->adapter_params );
					$adapter_params_array->category = 1;
					$item->adapter_params           = json_encode( $adapter_params_array );
				}
				unset( $item->id );
				$sql         = "SELECT `code`, `name`, `params`, `ordering` from `{$wpdb->prefix}wppipes_pipes` WHERE `item_id` = $id";
				$pipes       = $wpdb->get_results( $sql );
				$item->pipes = $pipes;
				$msg[]       = "Export the pipe #$id success!";
			}
			$items[] = $item;
		}
		$res->msg    = implode( "</br>", $msg ) . "</br>The template file could be found in uploads folder";
		$res->result = $items;

		return $res;
	}

	public function import_from_file( $item ) {
		global $wpdb;
		$insert_str = '';
		$temp_arr   = array();
		if ( ! self::check_exist_plugin( $item->adapter, 'adapter' ) ) {
			return "Please install $item->adapter destination first!";
		}
		if ( ! self::check_exist_plugin( $item->engine, 'engine' ) ) {
			return "Please install $item->engine source first!";
		}
		foreach ( $item->pipes as $pipe ) {
			if ( ! self::check_exist_plugin( $pipe->code, 'processor' ) ) {
				return "Please install $pipe->code processor first!";
			}
		}
		foreach ( $item as $key => $ins ) {
			if ( $key != 'pipes' && $key != 'current_id' ) {
				$insert_str .= ',' . $wpdb->prepare( " %s ", $ins );
				if ( $item->current_id > 0 ) {
					$temp_arr[] = "`" . $key . "` = '" . $ins . "'";
				}
			}
		}
		if ( count( $temp_arr ) > 0 ) {
			$insert_str = implode( ", ", $temp_arr );
		}

		$insert_str = str_replace( '\"', '"', $insert_str );
		$sql_ins    = "INSERT INTO " . $wpdb->prefix . "wppipes_items (`id`, `name`, `published`, `engine`, `engine_params`, `adapter`, `adapter_params`, `inherit`, `inputs`, `outputs` )
			 VALUES ( NULL" . $insert_str . " )";
		if ( $item->current_id > 0 ) {
			$id      = $item->current_id;
			$sql_ins = "UPDATE `{$wpdb->prefix}wppipes_items` SET {$insert_str} WHERE `id` = {$id}";
		}
		if ( ! $wpdb->query( $sql_ins ) ) {
			echo '<br />Error: ' . $wpdb->last_error;
			$error = true;
		} elseif ( isset( $_GET['x'] ) ) {
			echo '<br /><br />' . $sql_ins;
		}
		$new_temp_id = $wpdb->insert_id;
		if ( $item->current_id > 0 ) {
			$new_temp_id = $item->current_id;
			self::rmPipewith_item_id( $new_temp_id );
		}
		foreach ( $item->pipes as $pipe ) {
			$qry = "INSERT INTO `{$wpdb->prefix}wppipes_pipes` (`id`,`code`,`name`,`item_id`,`params`,`ordering`)";
			$qry .= "\n VALUES (NULL, '{$pipe->code}', '{$pipe->name}', {$new_temp_id}, '" . addslashes( $pipe->params ) . "', '{$pipe->ordering}')";
			if ( ! $wpdb->query( $qry ) ) {
				echo '<br />Error: ' . $wpdb->last_error;
				$error = true;
			} elseif ( isset( $_GET['x'] ) ) {
				echo '<br /><br />' . $qry;
			}
		}

		if ( $item->current_id > 0 ) {
			return "The template <strong>{$item->name}</strong> was set success!";
		}

		return "Pipe#$new_temp_id - {$item->name} imported success! <a href='admin.php?page=pipes.pipe&task=edit&id=$new_temp_id'>View pipe</a>";
	}

	public function check_exist_plugin( $key, $type ) {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'helpers' . DS . 'plugins.php';
		$plugins = PIPES_Helper_Plugins::getPlugins( true, $type );
		if ( array_key_exists( $key, $plugins ) ) {
			return true;
		} else {
			return false;
		}

	}

	public function rmPipewith_item_id( $item_id ) {
		global $wpdb;
		$qry = "DELETE FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id` = {$item_id}";
		if ( ! $wpdb->query( $qry ) ) {
			echo '<br />Error: ' . $wpdb->last_error;
		}
	}
}