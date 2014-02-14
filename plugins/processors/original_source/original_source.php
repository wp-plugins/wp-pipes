<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: original_source.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_original_source {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pos'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r( $params );
			echo 'Data: ';
			print_r( $data );
			echo '</pre>'; //exit();
		}
		$original_source = '<p><a href="' . $data->url . '" target="_blank">' . $params->text . '</a></p>';
		$res             = new stdClass();
		$res->html       = $data->html . "\n" . $original_source;

		return $res;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'url', 'html' );
		$data->output = array( 'html' );

		return $data;
	}
}