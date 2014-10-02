<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: cronjob.php 160 2013-12-31 08:05:48Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once 'define.php';
define( 'OGRAB_CACHE_FEEDS', OGRAB_CACHE . 'feeds' . DS );
define( 'OGRAB_CACHE_DATA', OGRAB_CACHE . 'data' . DS );
define( 'OGRAB_CACHE_LOG', OGRAB_CACHE . 'log' . DS );
define( 'OGRAB_CACHE_DONE', OGRAB_CACHE . 'done' );

define( 'OGRAB_CACHE_LOG_INFO', OGRAB_CACHE . 'cronlog.info' );
define( 'OGRAB_CACHE_LOG_ITEM_INFO', OGRAB_CACHE . 'cronlogitem.info' );
define( 'OGRAB_CACHE_DATA_INFO', OGRAB_CACHE_DATA . 'data.info' );
define( 'OGRAB_CACHE_DATA_RUNING', OGRAB_CACHE_DATA . 'runing.info' );
define( 'OGRAB_CACHE_DATA_START', OGRAB_CACHE_DATA . 'idstart' );
define( 'OGRAB_CACHE_DATA_RUN', OGRAB_CACHE . 'run' );

require_once OBGRAB_HELPERS . 'common.php';
require_once OBGRAB_SITE . 'grab.php';

class ogbDebug {
	public static function out( $msg = '', $line = 0 ) {
		if ( filter_input( INPUT_GET, "obDebug" ) == 1 ) {
			echo "\n\n<br /><b>obDebug </b>";
			if ( $line != 0 ) {
				echo " <b>Line:</b>{$line} ";
			}
			echo '<b>Time: </b>' . date( "Y-m-d H:i:s" ) . "\n";
			if ( $msg != '' ) {
				echo "<br /><b>Msg: </b>{$msg}";
			}
			echo "<hr />\n";
		}
	}

	public static function getHMS( $t ) {
		$p   = $t % 3600;
		$h   = ( $t - $p ) / 3600;
		$s   = $p % 60;
		$m   = ( $p - $s ) / 60;
		$str = array( ( $h < 10 ? "0" . $h : $h ), ( $m < 10 ? "0" . $m : $m ), ( $s < 10 ? "0" . $s : $s ) );

		return implode( ':', $str );
	}

	public static function write( $path, $txt = '' ) {
		$a = ogbFile::write( $path, $txt ); #JFile::write( $path, $txt );
	}
}

class ogbCronCallAIO {
	public static function run() {
		$x = isset( $_GET['x'] );
		if ( $x ) {
			$mstart = microtime();
		}
		$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;
		$ip   = self::getRealIpAddr();
		self::add_log_ip( $ip );
		$url   = get_site_url() . '/?pipes=cron&task=cronjob&ip=' . $ip;
		$start = time();
		$res   = self::call( $url, $step );
		//sleep(1);
		$stop    = time();
		$runTime = $stop - $start;
		$time    = ogbDebug::getHMS( $runTime );

		echo $res . "\n<hr />";
		if ( $stop - $start > 1 ) {
			$msg = self::getMsgRun( $stop, $start );
			echo $msg;
		} else {
			echo "{$start}: " . date( 'Y-m-d H:i:s', $start );
		}
		if ( $x ) {
			$mstop = microtime();
			$a     = explode( ' ', $mstart );
			$b     = explode( ' ', $mstop );
			$f     = $b[1] - $a[1];
			if ( $f == 0 ) {
				$g = $b[0] - $a[0];
			} else {
				$g = $f + $b[0] - $a[0];
			}
			echo "\n\n<hr /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n";
			echo $mstart . "<br />\n" . $g . "<br />\n" . $mstop;
			echo "\n<br />url-cron: " . $url;
		}
	}

	public static function add_log_ip( $ip ) {
		$dir_ip = OGRAB_CACHE . 'ips' . DS;
		$file   = $dir_ip . date( 'Y-m-d' ) . '.txt';
		$info   = date( 'H:i:s' ) . " - " . $ip . "\n";
		if ( is_file( $file ) ) {
			$a = file_put_contents( $file, $info, FILE_APPEND );
		} else {
			$a = ogbDebug::write( $file, $info );
		}
	}

	public static function get_contents( $url ) {
		$text = file_get_contents( $url );
		if ( $text ) {
			return $text;
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		ob_start();
		curl_exec( $ch );
		curl_close( $ch );

		return ob_get_clean();
	}

	public static function ob_get_curl( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		//curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 100 );
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

		ob_start();
		curl_exec( $ch );

		if ( isset( $_GET['x2'] ) ) {
			echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
			$info = curl_getinfo( $ch );
			echo '<pre>';
			print_r( $info );
			echo '</pre>';
		}
		curl_close( $ch );

		return ob_get_clean();
	}

	public static function call( $url = '', $limit = 1000 ) {
		if ( ! $url ) {
			return '';
		}
		$res = self::ob_get_curl( $url );
		set_time_limit( 0 );
		if ( isset( $_GET['x'] ) ) {
			echo '<hr />' . date( 'Y-m-d H:i:s' ) . ' - ' . microtime() . ' - limit: ' . $limit;
			echo '<br />' . $res;
		}
		if ( $limit < 1 ) {
			return $res;
		}
		if ( self::isNextCall( $res ) ) {
			$limit --;
			$res = self::call( $url, $limit );
		}

		return $res;
	}

	public static function getMsgRun( $rtime, $now ) {
		$nextt1 = $rtime - $now;
		$nextt  = ogbDebug::getHMS( $nextt1 );
		$nextt1 = "{$nextt1}";
		$a      = strlen( $nextt1 );
		for ( $a; $a < 10; $a ++ ) {
			$nextt1 = "0" . $nextt1;
		}
		$msg = date( 'Y-m-d H:i:s', $now ) . " [ {$now} - Start ]<br />\n";
		$msg .= "0000-00-00 {$nextt} [ {$nextt1} - Run &nbsp;]<br />\n";
		$msg .= date( 'Y-m-d H:i:s', $rtime ) . " [ {$rtime} - Stop ]\n";

		return $msg;
	}

	public static function isNextCall( $text ) {
		$step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;
		if ( isset( $_GET['x5'] ) && $step < 20 ) {
			echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";

			return true;
		}
		$a = explode( '{ogb-res:', $text );
		if ( ! isset( $a[1] ) ) {
			return false;
		}
		$a = explode( '}', $a[1] );
		if ( ! isset( $a[1] ) ) {
			return false;
		}
		if ( $a[0] == '0' ) {
			return true;
		}

		return false;
	}

	public static function getRealIpAddr() {
		if ( isset( $_GET['x12'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo '<pre>$_SERVER:';
			print_r( $_SERVER );
			echo '</pre>';
			exit();
		}
		global $ogb_ip;
		$a = explode( '.', $ogb_ip );
		if ( isset( $a[1] ) ) {
			return $ogb_ip;
		}
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = '0.0.0.0';
		}
		$ogb_ip = $ip;

		return $ip;
	}
}

class ogbPlugCron {
	public static function getGbParams() {
		global $gbParams;
		if ( is_object( $gbParams ) ) {
			return $gbParams;
		}
		$gbParams                 = new stdClass();
		$gbParams->cronjob_active = get_option( 'pipes_cronjob_active' );
		$gbParams->active         = get_option( 'pipes_active' );
		$gbParams->schedule       = get_option( 'pipes_schedule' );
		$gbParams->start_at       = get_option( 'pipes_start_at' );
		$gbParams->not_use_cache  = get_option( 'pipes_not_use_cache' );
		self::udParams( $gbParams );

		return $gbParams;
	}

	/**
	 * Update Params
	 * @global type $wpdb
	 *
	 * @param type  $params
	 *
	 * @return type
	 */
	public static function udParams( &$params ) {
		global $wpdb;
		$a = explode( ':', $params->start_at );
		if ( $a[count( $a ) - 1] == '00' ) {
			return;
		}
		$params->start_at = date( 'Y-m-d' ) . ' 01:00:00';
		update_option( 'pipes_start_at', $params->start_at );
		$udparams = json_encode( $params );
	}

	public static function setRunning( $info ) {
		ogbDebug::write( OGRAB_CACHE_DATA_RUN, $info );
	}

	public static function setCanRun() {
		ogbDebug::write( OGRAB_CACHE_DATA_RUN, '123' );
	}

	public static function isRuning() {
		$now = time();
		if ( ! is_file( OGRAB_CACHE_DATA_RUN ) ) {
			self::setRunning( $now );

			return false;
		}
		$run = self::get_content( OGRAB_CACHE_DATA_RUN );
		if ( $run == '123' ) {
			self::setRunning( $now );

			return false;
		}
		$maxtime = 300;
		if ( isset( $_GET['mt'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo 'setCanRun';
			self::setCanRun();

			return true;
		}

		$delay = $now - (int) $run;
		if ( $delay > $maxtime ) {
			self::setRunning( $now );

			return false;
		}
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo "isRunning: start: " . date( 'Y-m-d H:i:s' ) . " [ {$run} ] - Delay:{$delay}";
		}

		return true;
	}

	public static function checkDone() {
		if ( ! is_file( OGRAB_CACHE_DONE ) ) {
			self::setDone( '1' );

			return true;
		}
		$done = self::get_content( OGRAB_CACHE_DONE );
		if ( intval( $done ) > 0 ) {
			return true;
		}

		return false;
	}

	public static function setDone( $value ) {
		if ( $value == 1 ) {
			global $ogbConStop;
			$ogbConStop = true;
		}
		ogbDebug::write( OGRAB_CACHE_DONE, $value );
	}

	public static function getMsgLeft( $rtime, $now ) {
		$nextt1 = $rtime - $now;
		$nextt  = ogbDebug::getHMS( $nextt1 );
		$nextt1 = "{$nextt1}";
		$a      = strlen( $nextt1 );
		for ( $a; $a < 10; $a ++ ) {
			$nextt1 = "0" . $nextt1;
		}
		$msg = "<br />\n";
		$msg .= date( 'Y-m-d H:i:s', $now ) . " [ {$now} - Now ]<br />\n";
		$msg .= "0000-00-00 {$nextt} [ {$nextt1} - Left &nbsp;]<br />\n";
		$msg .= date( 'Y-m-d H:i:s', $rtime ) . " [ {$rtime} - Next ]\n";

		return $msg;
	}

	public static function checkRun() {
		/*Get global config*/
		$gbParams = self::getGbParams();
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $gbParams );
			echo '</pre>';
		}
		$active   = $gbParams->active;
		$start    = $gbParams->start_at;
		$schedule = $gbParams->schedule;
		/*Check auto run*/
		if ( ! $active ) {
			if ( ! self::checkDone() ) {
				self::setDone( '1' );
			}
			ogbDebug::out( "The Cronjob is not active.", __LINE__ );
			echo "The Cronjob is not active.";

			return;
		}
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
		}
		/*Check delay*/
		if ( self::isRuning() ) {
			return;
		}

		global $ogbConStop;
		/*Check pie complete. If not continued*/
		if ( ! self::checkDone() ) {
			ogbDebug::out( "Start get fulltext", __LINE__ );
			$ogbConStop = false;
			$logInfo    = self::runCron();
			self::setCanRun();
			self::addLog( $logInfo, false, false );

			return;
		}


		$startAt = date( 'Y-m-d H:i:s' );
		$rtime   = self::getTime( $start );
		$now     = time();
		if ( $now < $rtime ) {
			$msg = self::getMsgLeft( $rtime, $now );
			ogbDebug::out( $msg, __LINE__ );

			return;
		}

		/*Get global next time, schedule from cache. File name: cronlog.info*/
		if ( is_file( OGRAB_CACHE_LOG_INFO ) ) {
			$info = self::get_content( OGRAB_CACHE_LOG_INFO );
			$info = json_decode( $info );
		} else {
			//self::createFolder(OGRAB_CACHE_LOG_INFO);
			$info = new stdClass();
		}

		/*Get Pie's next time', schedule from cache. File name: cronlogitem.info*/
		$items = self::getPiesItems();
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $gbParams );
			echo '</pre>';
			echo $info->nextRun;


		}

		if ( is_file( OGRAB_CACHE_LOG_ITEM_INFO ) ) {
			$info_items = self::get_content( OGRAB_CACHE_LOG_ITEM_INFO );
			$info_items = json_decode( $info_items );

		} else {
			$info_items = array();

			if ( count( $items ) > 0 ) {
				foreach ( $items as $k => $item ) {
					$item->schedule = json_decode( $item->adapter_params )->schedule;
					if ( $item->schedule ) {
						//@$info_items[$k]->nextRun = self::getNextTimeItem($item->schedule);
						@$info_items[$k]->schedule = $item->schedule;
					} else {
						//@$info_items[$k]->nextRun = $info->nextRun;
						@$info_items[$k]->schedule = $gbParams->schedule;
					}
					@$info_items[$k]->id = $item->id;

				}
			}

		}

		/*Check if new items or when change item's schedule*/
		if ( ( count( $items ) != count( $info_items ) ) || ( count( $items > 0 ) ) ) {

			foreach ( $items as $k => $item ) {
				$item->schedule = json_decode( $item->adapter_params )->schedule;
				if ( ! ( ( $item->schedule == $info_items[$k]->schedule ) && ( $item->id == $info_items[$k]->id ) ) ) {
					//@$info_items[$k]->nextRun = self::getNextTimeItem($item->schedule);
					if ( ! $item->schedule ) {
						//@$info_items[$k]->nextRun = self::getNextTimeItem($gbParams->schedule);
						@$info_items[$k]->schedule = $gbParams->schedule;
					} else {
						//@$info_items[$k]->nextRun = self::getNextTimeItem($item->schedule);
						@$info_items[$k]->schedule = $item->schedule;
					}
					@$info_items[$k]->id = $item->id;
				}
			}
		}
		/*Write items's config to cache. File name: cronlogitem.info */
		ogbDebug::write( OGRAB_CACHE_LOG_ITEM_INFO, json_encode( $info_items ) );

		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $info_items );
			echo '</pre>';
			echo date( 'Y-m-d H:i:s' ) . ' [ Now ]<br />';

			foreach ( $info_items as $k => $info_item ) {
				echo date( 'Y-m-d H:i:s', $info_item->nextRun ) . ' [ nextRun' . $k . ' ]<br />';
			}
		}
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $info );
			echo '</pre>';
			echo date( 'Y-m-d H:i:s' ) . ' [ Now ]<br />';
			echo date( 'Y-m-d H:i:s', $info->nextRun ) . ' [ nextRun ]';
		}

		/*Check next time of global*/
		$validInfo = isset( $info->start_at ) && isset( $info->nextRun ) && isset( $info->schedule );
		if ( $validInfo && $info->start_at == $start && $info->schedule == $schedule ) {
			/*Remove cause. Get time global next time*/
			//$rtime = $info->nextRun;
		} else {
			$info->start_at = $start;
			$info->schedule = $schedule;
		}
		if ( $now < $rtime ) {
			//if(0){
			$msg = self::getMsgLeft( $rtime, $now );
			ogbDebug::out( $msg, __LINE__ );
			self::setCanRun();

			return;
		}

		/*Calculate global's next time'*/
		$uType = substr( $schedule, 0, 1 );
		switch ( $uType ) {
			case 'i':
				$unit = 60;
				break;
			case 'h':
				$unit = 3600;
				break;
			case 'm':
				$unit = 'month';
				break;
			case 'y':
				$unit = 'years';
				break;
			default :
				$unit = 'days';
		}
		$uVal    = (int) substr( $schedule, 1 );
		$nextRun = $rtime;
		if ( in_array( $uType, array( 'i', 'h' ) ) ) {
			$unit = $uVal * $unit;
			while ( $nextRun < $now ) {
				$nextRun += $unit;
			}
		} else {
			$rdate   = getdate( $rtime );
			$rdstr   = $rdate['mday'] . ' ' . $rdate['month'] . ' ' . $rdate['year'];
			$nextRun = $rtime;
			$k       = 0;
			while ( $nextRun < $now ) {
				$nextRun = strtotime( $rdstr . ' + ' . ( $k ++ ) . ' ' . $unit );
			}
			$nextRun += $rdate['seconds'] + ( $rdate['minutes'] * 60 ) + ( $rdate['hours'] * 3600 );
		}
		$info->nextRun = $nextRun;

		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $info );
			echo '</pre>';
			echo 'nextRun: ' . $info->nextRun . ' : ' . date( 'Y-m-d H:i:s', $info->nextRun );
		}

		$logName       = date( 'Y.m.d_H.i.s', $rtime );
		$info->logName = $logName;
		$info          = json_encode( $info );

		/*Write global's config to cache. Include: start_time, schedule, nexttime what will run in future.*/
		ogbDebug::write( OGRAB_CACHE_LOG_INFO, $info );

		self::setDone( '0' );
		ogbDebug::out( 'Run Cronjob', __LINE__ );


		/*Call cron*/
		$initInfo = self::runCron();

		self::setCanRun();
		$ogbConStop = false;
		$logInfo    = '<h3 style="color:#555555;">' . $startAt . ' - Start</h3><hr>' . $initInfo;
		self::addLog( $logInfo );

		return;
	}

	/**
	 * Get all items of OBGraper
	 * return list object
	 */
	public static function getPiesItems() {
		global $wpdb;
		$query = "SELECT * FROM `{$wpdb->prefix}wppipes_items` WHERE published=1 ORDER BY id ASC";

		return $wpdb->get_results( $query );
	}

	public static function getTime( $str ) {
		return strtotime( $str );
	}

	public static function createFolder( $file ) {
		$dir = dirname( $file );
		if ( ! is_dir( $dir ) ) {
			$file = $dir . DS . 'index.html';

			return ogbDebug::write( $file, '<html><body bgcolor="#FFFFFF">&nbsp;</body></html><html>' );
		}

		return true;
	}

	public static function getNextRunId( $id ) {
		global $wpdb;
		$qry = "SELECT `id` FROM `{$wpdb->prefix}wppipes_items` "
			. "\nWHERE `published` =1 AND `id` > {$id} ORDER BY `id` ASC LIMIT 1";
//		$db->setQuery( $qry );
		$id = (int) $wpdb->get_var( $qry );
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo $qry;
			echo '<br />' . $wpdb->print_error();
		}

		return $id;
	}

	/**
	 * Get next time for each item
	 * $time str schedule of item
	 */
	public static function getNextTimeItem( $time ) {

		$gbParams = self::getGbParams();
		$start    = $gbParams->start_at;
		$rtime    = self::getTime( $start );
		$now      = time();
		$uType    = substr( $time, 0, 1 );
		switch ( $uType ) {
			case 'i':
				$unit = 60;
				break;
			case 'h':
				$unit = 3600;
				break;
			case 'm':
				$unit = 'month';
				break;
			case 'y':
				$unit = 'years';
				break;
			default :
				$unit = 'days';
		}
		$uVal    = (int) substr( $time, 1 );
		$nextRun = $rtime;
		if ( in_array( $uType, array( 'i', 'h' ) ) ) {
			$unit = $uVal * $unit;
			while ( $nextRun < $now ) {
				$nextRun += $unit;
			}
		} else {
			$rdate   = getdate( $rtime );
			$rdstr   = $rdate['mday'] . ' ' . $rdate['month'] . ' ' . $rdate['year'];
			$nextRun = $rtime;
			$k       = 0;
			while ( $nextRun < $now ) {
				$nextRun = strtotime( $rdstr . ' + ' . ( $k ++ ) . ' ' . $unit );
			}
			$nextRun += $rdate['seconds'] + ( $rdate['minutes'] * 60 ) + ( $rdate['hours'] * 3600 );
		}

		return $nextRun;
	}

	public static function get_content( $file ) {
		return file_get_contents( $file );
	}

	public static function set_data_run( $info ) {
		ogbDebug::write( OGRAB_CACHE_DATA_RUNING, $info );
	}

	/**
	 * Function get time Schedule
	 * $id int ID of Item
	 * result next time of item
	 */
	public static function getTimeSchedule( $id ) {
		$now = time();
		if ( is_file( OGRAB_CACHE_LOG_ITEM_INFO ) ) {
			$info_items = self::get_content( OGRAB_CACHE_LOG_ITEM_INFO );
			$info_items = json_decode( $info_items );

		} else {
			return false;
		}
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			var_dump( $info_items );


		}
		if ( count( $info_items ) > 0 ) {
			$return_time = 0;
			foreach ( $info_items as $info_item ) {
				if ( $info_item->id == $id ) {
					$return_time = $info_item->nextRun;
					if ( $now > $info_item->nextRun ) {
						$info_item->nextRun = self::getNextTimeItem( $info_item->schedule );
						ogbDebug::write( OGRAB_CACHE_LOG_ITEM_INFO, json_encode( $info_items ) );
					}

					return $return_time;
					break;
				}
			}
		} else {
			return false;
		}


	}

	public static function runCron() {
		$ip        = isset( $_GET['ip'] ) ? $_GET['ip'] : '1.1.1.1';
		$ip        = "[ ip: {$ip} ]";
		$new_start = true;
		/*Check pie from cache. If isset pieID, It will run this pieID.
		If not It will stop: Default data: 0:0:0
		{0 position 1:pieID}{{0 position 2:Number data what is imported}{{0 position 3: Total datas what be got by engine }
		File name: runing.info
		*/
		if ( ! is_file( OGRAB_CACHE_DATA_RUNING ) ) {
			$run_id = 0;
			$crun   = $mrun = 0;
		} else {
			$run_info = self::get_content( OGRAB_CACHE_DATA_RUNING );
			if ( isset( $_GET['x'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
				echo '$run_info: ';
				var_dump( $run_info );


			}
			$run_info = explode( ':', $run_info );
			$run_id   = (int) $run_info[0];
			$crun     = (int) $run_info[1];
			$mrun     = (int) $run_info[2];
			if ( $crun < $mrun ) {
				$new_start = false;
			}
		}

		if ( $new_start ) {
			$run_id        = self::getNextRunId( $run_id );
			$run_item_time = self::getTimeSchedule( $run_id );
			if ( isset( $_GET['x'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
				echo 'run time: ' . $run_item_time;
			}
			if ( $run_id == 0 ) {
				self::set_data_run( "0:0:0" );
				self::setDone( '1' );
				$log = "\n\n<hr /><b style=\"color:#555555;\">" . date( 'Y-m-d H:i:s' ) . ' - Done</b> ' . $ip . '<hr />';
				self::addLog( $log, true );

				return $log;
			}
		}

		$start = time();
		$log   = "<hr/>[ " . date( 'Y-m-d H:i:s', $start ) . '  - Start ]' . $ip;
		/*Include obGrab class. What will call engine, adapter, store plugin content*/
		require_once OBGRAB_SITE . 'grab.php';
		$grab = new obGrab;
		$info = '';
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<br />New start:' . $new_start;
		}
		if ( $new_start ) {
			/*Check time for each pie. If pie's nextime less now. Pie will run'*/
			if ( $run_item_time < $start ) {
				if ( isset( $_GET['x'] ) ) {
					echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";

				}
				$res = $grab->start( $run_id );
			}
			$crun = 0;
			$mrun = $res->total;
			//$info	= $res->info;
			self::set_data_run( "{$run_id}:{$crun}:{$mrun}" );

			if ( isset( $_GET['x'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
				echo '$new_start: ';
				var_dump( $new_start );
				echo '<pre>';
				print_r( $res );
				echo '</pre>';
				echo "{$run_id}:{$crun}:{$mrun}";
			}

			$edit_url = "admin.php?page=pipes.pipe&id={$run_id}";
			$a_edit   = "<a href=\"{$edit_url}\" target=\"_blank\">{$run_id} - {$res->pipe}</a>";
			$log2     = "<hr /><b>Pipe: {$a_edit}</b><br/ >" . '[ ' . date( 'Y-m-d H:i:s' ) . ' ]';
			$log2 .= "[ Engine: {$res->name} ][ Found: {$mrun} articles ]<br/ >\n";

			self::addLog( $log2, true );
			self::addLog( '' );

			$log2 .= $mrun > 0 ? '<ol>' : '<br/>' . date( 'Y-m-d H:i:s' ) . " Stop: [{$run_id}:{$crun}/{$mrun}]";
			$log .= $log2;
			if ( $mrun < 1 || self::outTime( $start ) ) {
				return $log;
			}
		}
		$savedInfo = '';
		/*Storedata what is got by engine into database*/
		for ( $i = $crun; $i < $mrun; $i ++ ) {
			$info = $grab->storeItems( $run_id, $i );
			$savedInfo .= self::makeLogSave( $info );
			$log .= self::makeLogCron( $info );
			if ( isset( $_GET['x'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
				echo 'crun: ' . $i;
			}
			if ( self::outTime( $start ) || isset( $_GET['st'] ) ) {
				$i ++;
				break;
			}
		}
		self::addSavedLog( $savedInfo );
		/*Write cache next ID runing.info. If not write, It will not run next time*/
		self::set_data_run( "{$run_id}:{$i}:{$mrun}" );
		$log .= ( $i == $mrun ? '</ol>' : '' ) . '[ ' . date( 'Y-m-d H:i:s' ) . ' - Stop ]';
		$log .= '[ Pipe id: ' . $run_id . ' - (' . $crun . '->' . $i . ')/' . $mrun . ' ]';

		return $log;
	}

	public static function makeLogCron( $info ) {
		$source = '<a target="_blank" href="' . $info->src_url . '">' . $info->src_name . '</a>';
		$log    = '<li><b>Source: </b>' . $source . '<br />[ ' . date( 'Y-m-d H:i:s' ) . ' ]';
		$log .= '[ ' . $info->action . ' ][ ' . $info->msg . ' ]<hr /></li>';

		return $log;
	}

	public static function makeLogSave( $save ) {
		if ( $save->action != 'Save' ) {
			return '';
		}
		$host      = ogbFile::getHost();
		$source    = '<a target="_blank" href="' . $save->src_url . '">' . $save->src_name . '</a>';
		$view      = '[ id: ' . $save->id . ' ][ <a target="_blank" href="' . $host . $save->viewLink . '">View</a> ]';
		$edit      = '[ <a target="_blank" href="' . $host . 'administrator/' . $save->editLink . '">Edit</a> ]';
		$savedInfo = '<li><b>Source: </b>' . $source . '<br />[ Saved at: ' . date( 'Y-m-d H:i:s' ) . ' ]';
		$savedInfo .= $view . $edit . '[ ID: ' . $save->item_id . ' ]<hr /></li>';

		return $savedInfo;
	}

	public static function getPathSave() {
		$path = OGRAB_CACHE_SAVED . date( 'Y.m.d' );
		if ( ! is_file( $path . '-00' ) ) {
			return $path . '-00';
		}
		$h = (int) date( 'H' );
		for ( $i = 0; $i < $h; $i ++ ) {
			$k = $i < 10 ? "0" . $i : $i;
			$p = $path . '-' . $k;
			if ( is_file( $p ) && filesize( $p ) < 102400 ) {
				return $p;
			}
		}

		return $p;
	}

	public static function addSavedLog( $info ) {
		if ( $info == '' ) {
			return;
		}
		$path = self::getPathSave();
		self::addLog2( $path, $info, false );
	}

	public static function addLog( $info, $ra = false, $new = true ) {
		$path = self::getlogPath( $ra, $new );
		self::addLog2( $path, $info );
	}

	public static function addLog2( $path, $info, $a = true ) {
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo $info;
			echo '<hr />logPath: ' . $path;
		}
		if ( is_file( $path ) ) {
			$old = self::get_content( $path );
			if ( $a ) {
				$log = $old . "\n" . $info;
			} else {
				$log = $info . "\n" . $old;
			}
		} else {
			$log = $info;
		}
		ogbDebug::write( $path, $log );
	}

	public static function getlogPath( $ra = false, $new = true, $maxsize = 102400 ) {
		if ( $ra ) {
			return OGRAB_CACHE_LOG . date( 'Y.m.d' ) . ".24.00.00";
		}
		$logName = date( 'Y.m.d.H' );
		$logPath = OGRAB_CACHE_LOG . $logName;

		$file = $logPath . ".00.00";
		if ( ! is_file( $file ) ) {
			if ( ! $new ) {
				self::addLog2( $file, "<ol>\n" );
			}

			return $file;
		}
		$m         = date( 'i' );
		$k         = (int) $m;
		$last_file = '';
		for ( $i = 0; $i < $k; $i ++ ) {
			$n    = $i < 10 ? '0' . $i : $i;
			$file = $logPath . ".{$n}.00";
			if ( is_file( $file ) ) {
				$last_file = $file;
				if ( $new && filesize( $file ) > $maxsize ) {
					return $file;
				}
			}
		}
		if ( $last_file == '' ) {
			$last_file = $logPath . ".{$m}.00";
			if ( ! $new && ! is_file( $last_file ) ) {
				self::addLog2( $last_file, "<ol>\n" );
			}
		}

		return $last_file;
	}

	public static function outTime( $start, $time = 3 ) {
		$now = time();
		if ( $now - $start > $time ) {
			return true;
		}

		return false;
	}
}