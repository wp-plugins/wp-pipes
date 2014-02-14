<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: settings.php 170 2014-01-26 06:34:40Z thongta $
 * @author               wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
// require_once dirname(dirname(dirname(__FILE__))).DS.'includes'.DS.'model.php';
// require_once '';
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class PIPESModelSettings extends Model {

	public function getTable() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'tables' . DS . 'settings.php';
		$itemsListTable = new PIPES_Settings_List_Table();
		//Fetch, prepare, sort, and filter our data...

		$itemsListTable->prepare_items();

		return $itemsListTable;
	}

	public function save() {
		global $wpdb;
		$res          = new stdClass();
		$res->id      = 0;
		$res->msg     = '';
		$res->typemsg = 'message';

		if ( isset( $_POST ) ) {
			foreach ( $_POST as $key => $value ) {
				if ( $key != 'task' && $key != 'submit' ) {
					if ( 'pipes_start_at' == $key ) {
						$value .= ' ' . ( ( $_POST['pipes_hh'] != '' ) ? $_POST['pipes_hh'] : '00' ) . ':' . ( ( $_POST['pipes_mn'] != '' ) ? $_POST['pipes_mn'] : '00' );
						$value = strtotime( $value );
					}
					$sql = 'UPDATE ' . $wpdb->prefix . 'options SET `option_value` = "' . $value . "\" WHERE `option_name` = '" . $key . "'";
					$wpdb->query( $sql );
				}
			}

			$res->msg = __( 'Settings saved.' );

			return $res;
		}

	}
}