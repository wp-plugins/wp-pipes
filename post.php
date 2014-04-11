<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: post.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once 'define.php';
require_once OBGRAB_HELPERS . 'common.php';
require_once OBGRAB_SITE . 'grab.php';

class ogbPost {
	public static function Post() {
		require_once OBGRAB_SITE . 'post.html.php';
	}

	public static function getEngin() {
		//echo '{"error":"1","msg":"id=0","found":12}';exit();
//		$id = JRequest::getInt('id',0);
		$id = filter_input( INPUT_GET, 'id' );
		if ( $id < 1 ) {
			echo '{"error":"1","msg":"id=0","found":0}';
			exit();
		}
		$start = date( 'Y-m-d H:i:s' );
		$grab  = new obGrab;
		$res   = $grab->start( $id );

		echo '<p>[ Started: ' . $start . ' ][ Source found: ' . $res->total . ' items ]</p>';
		echo '<input type="hidden" id="ogb-' . $id . '-efound" name="ogb-' . $id . '-efound" value="' . $res->total . '"/>';

		if ( $res->total > 0 ) {
			echo '
				<div class="progress progress-striped" id="ogb-' . $id . '-res-bar">
					<div class="progress-bar bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%; height: 100%">
						<small>0% Complete (success)</small>
					</div>
				</div>
			';
			echo '<ol id="ogb-' . $id . '-get-rows">&nbsp;</ol>';
		} else {
			echo '
				<p class="alert alert-danger">No data found from the SOURCE.</p>
			';
		}
		//echo $res->json;	
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $res );
			echo '</pre>';
		}
		exit();
	}

	public static function saveAdapter() {
		sleep( 1 );
		$id  = filter_input( INPUT_GET, 'id' );
		$row = filter_input( INPUT_GET, 'row' );
		require_once PIPES_PATH . DS . 'grab.php';
		$grab = new obGrab;

		$res = $grab->storeItems( $id, $row );
		$log = self::makeInfo( $res );
		echo $log;
		exit();
	}

	public static function makeInfo( $info ) {
		$source = '<a target="_blank" href="' . $info->src_url . '">' . $info->src_name . '</a>';
		$log    = '<b>Source: </b>' . $source . '<br />[ ' . date( 'Y-m-d H:i:s' ) . ' ]';
		$log .= '[ ' . $info->action . ' ][ ' . $info->msg . ' ]';
		if ( $info->id > 0 ) {
			$host = ogbFile::getHost();
			$view = '[ id: ' . $info->id . ' ][ <a target="_blank" href="' . $host . $info->viewLink . '">View</a> ]';
			$edit = '[ <a target="_blank" href="' . $host . '/wp-admin/' . $info->editLink . '">Edit</a> ]';
			$log .= $view . $edit;
		}
		$logSave = '<li>' . $log . "[Post id:{$info->item_id}]<hr /></li>\n";
		//if($info->action!='Ignore'){
		self::addSavedLog( $logSave );

		//}
		return $log;
	}

	public static function addSavedLog( $info ) {
		if ( $info == '' ) {
			return;
		}
		$name = '';
		if ( is_file( OGRAB_CACHE . 'savedinfo' ) ) {
			$name = file_get_contents( OGRAB_CACHE . 'savedinfo' );
		}
		if ( is_file( OGRAB_CACHE_SAVED . $name ) && filesize( OGRAB_CACHE_SAVED . $name ) < 102400 ) {
			$old = file_get_contents( OGRAB_CACHE_SAVED . $name );
		} else {
			$name = date( 'Y.m.d-H.i.s' );
			ogbFile::write( OGRAB_CACHE . 'savedinfo', $name );
			$old = '';
		}
		ogbFile::write( OGRAB_CACHE_SAVED . $name, $info . "\n" . $old );
	}
}