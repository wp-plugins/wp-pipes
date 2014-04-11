<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: strip_tags.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_strip_tags {

	public static function check_params_df( $params ) {
		$df           = new stdclass();
		$df->strip_tag     = '*';
		$df->ignore  = 'div,p,a,img';

		foreach ( $df as $key => $val ) {
			if ( ! isset( $params->$key ) ) {
				$params->$key = $val;
			}
		}

		return $params;
	}

	static function process( $data, $params ) {
		if ( ! is_object( $params ) ) {
			$params = new stdClass();
		}
		$a = explode( '*', @$params->strip_tag );
		if ( isset( $a[1] ) ) {
			$ignore     = isset( $params->ignore ) ? $params->ignore : '';
			$ignore     = explode( ',', $ignore );
			$ignore     = '<' . implode( '><', $ignore ) . '>';
			$data->html = strip_tags( $data->html, $ignore );
		} else {
			$data->html = self::clear_tags( $data->html, @$params->strip_tag );
		}

		return $data;
	}

	/*public static function ogb_strip_tags( $html, $tags, $params ) {
		$data->html = strip_tags( $data->html );
	}*/

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'html' );
		$data->output = array( 'html' );

		return $data;
	}

	static function clear_tags( $html, $tags ) {
		$tags = explode( ',', $tags );
		for ( $i = 0; $i < count( $tags ); $i ++ ) {
			$tag = trim( $tags[$i] );
			if ( $tag == '' ) {
				continue;
			}
			$html = self::clear_tag( $html, $tag );
		}

		return $html;
	}

	public static function clear_tag( $html, $tag ) {
		$a = explode( "<{$tag}", $html );
		if ( ! isset( $a[1] ) ) {
			return $html;
		}
		$c   = array();
		$c[] = $a[0];
		for ( $i = 1; $i < count( $a ); $i ++ ) {
			$b = explode( "</{$tag}>", $a[$i] );
			if ( isset( $b[1] ) ) {
				$c[] = $b[1];
				continue;
			}
			$d = explode( '>', $a[$i] );
			unset( $d[0] );
			$c[] = implode( '>', $d );
		}
		$html = implode( '', $c );

		return $html;
	}
}