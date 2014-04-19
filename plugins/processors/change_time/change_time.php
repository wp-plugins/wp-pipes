<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: change_time.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_change_time {
	/* publish_up publish_down */
	public static function check_params_df( $params ) {
		$df               = new stdclass();
		$df->publish_up   = 0;
		$df->publish_down = 0;

		foreach ( $df as $key => $val ) {
			if ( ! isset( $params->$key ) ) {
				@$params->$key = $val;
			}
		}

		return $params;
	}

	public static function process( $data, $params ) {
		$params = self::check_params_df( $params );
		if ( isset( $_GET['pct'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r( $params );
			echo 'Data: ';
			print_r( $data );
			echo '</pre>'; //exit();
		}
		if ( $data->time == '' ) {
			$data->time = date( 'Y-m-d H:i:s', time() );
		}
		if ( $params->publish_up != 0 ) {
			$fix_time   = (int) $params->publish_up;
			$fix_time   = $fix_time * 3600;
			$publish_up = self::fix_date( $data->time, $fix_time );
		} else {
			$publish_up = $data->time;
		}

		if ( $params->publish_down != 0 ) {
			$fix_time     = (int) $params->publish_down;
			$fix_time     = $fix_time * 3600 * 24;
			$publish_down = self::fix_date( $publish_up, $fix_time );
		} else {
			$publish_down = '';
		}
		$res             = new stdClass();
		$res->publish_up = $publish_up;
		$res->pub_down   = $publish_down;
		if ( isset( $_GET['pct_after'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Data: ';
			print_r( $data );
			echo '</pre>';
			die;
		}

		return $res;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'time' );
		$data->output = array( 'publish_up', 'pub_down' );

		return $data;
	}

	public static function fix_date( $time, $fix_time = -86400 ) {
		$time = (int) strtotime( $time ) + $fix_time;

		return date( 'Y-m-d H:i:s', $time );
		//3600.24	= 86400
	}
}