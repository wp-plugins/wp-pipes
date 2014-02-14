<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: alias.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_alias {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pal'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r( $params );
			echo 'Data: ';
			print_r( $data );
			echo '</pre>';
		}
		$res        = new stdClass();
		$res->alias = '';
		if ( $data->text == '' ) {
			return $res;
		}

		$alias = $data->text;
		$alias = self::replace_chars( $alias, $params );

		if ( isset( $_GET['palias'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'Alias: ' . $alias . '<br />';
		}
		$alias = wp_strip_all_tags($alias);
		$alias = preg_replace( '/[^a-zA-Z0-9\-\s]/', '', $alias );
		$alias = preg_replace( '/\s+/', '-', $alias );
		$alias = preg_replace( '/\-+/', '-', trim( $alias ) );
		$alias = strtolower( $alias );

		if ( isset( $_GET['palias'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'Alias: ' . $alias . '<br />';
		}

		$res->alias = $alias;

		return $res;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'text' );
		$data->output = array( 'alias' );

		return $data;
	}

	public static function replace_chars( $alias, $params ) {
		$chars = self::getChars( $params );

		if ( is_array( $chars ) ) {
			foreach ( $chars as $key => $vals ) {
				$alias = str_replace( $vals, $key, $alias );
			}
		}
		if ( isset( $_GET['pal1'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Chars: ';
			print_r( $chars );
			echo '</pre>';
		}

		return $alias;
	}

	public static function getChars( $params ) {
		global $ogb_chars;
		if ( ! $ogb_chars ) {
			$ogb_chars = array();
			if ( @$params->replace_chars != '' ) {
				$chars = $params->replace_chars;
			} else {
				$file  = dirname( __FILE__ ) . DS . 'chars.txt';
				$chars = file_get_contents( $file );
			}
			str_replace( "\n", '', $chars );

			$chars = explode( ";", $chars );
			for ( $i = 0; $i < count( $chars ); $i ++ ) {
				$char = explode( ":", $chars[$i] );
				if ( ! isset( $char[1] ) ) {
					continue;
				}
				$vals = explode( ",", $char[1] );
				if ( ! isset( $ogb_chars[$char[0]] ) ) {
					$ogb_chars[$char[0]] = $vals;
				} else {
					$ogb_chars[$char[0]] = array_merge( $ogb_chars[$char[0]], $vals );
				}
			}
		}

		return $ogb_chars;
	}
}