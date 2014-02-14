<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: plugin.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class pipes_system
 * To execute background tasks such as: Cronjob
 */
class pipes_system {

	/**
	 * Trigger/Hook cronjob task - detect when the cronjob will be executed
	 */
	static function cronjob() {
		$option = filter_input( INPUT_GET, 'pipes' );
		if ( $option != 'cron' ) {
			return;
		}
		$task = filter_input( INPUT_GET, 'task' );
		if ( ! in_array( $task, array( 'callaio', 'cronjob' ) ) ) {
			return;
		}
		$path = dirname( __FILE__ ) . DS . 'cronjob.php';
		if ( ! file_exists( $path ) ) {
			return;
		}
		require_once $path;

		if ( $task == 'callaio' ) { // Execute all enabled pipes
			ogbCronCallAIO::run();
		} elseif ( $task == 'cronjob' ) { // ???
			global $ogbConStop;
			$ogbConStop = true;
			ogbPlugCron::checkRun();
			echo '{ogb-res:' . ( $ogbConStop ? '1' : '0' ) . '}';
		}
		exit();
	}
}