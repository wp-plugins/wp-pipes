<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: keywords_filter.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_keywords_filter {
	public static function process( $data, $params ) {
		if ( isset( $_GET['pkey'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r( $params );
			echo 'Data: ';
			print_r( $data );
			echo '</pre>';
		}
		$valid = self::check( $data->html, $params->keywords );
		$stop  = new stdClass();
		if ( $valid ) {
			$stop->state = false;
			$stop->msg   = '';
		} else {
			$stop->state = true;
			$stop->msg   = 'keywords invalid';
		}
		$data->stop = $stop;

		return $data;
	}

	public static function getDataFields( $params = false ) {
		$data         = new stdClass();
		$data->input  = array( 'html' );
		$data->output = array( 'html' );

		return $data;
	}

	public static function parseKeyword( $keywords ) {
		$sentences1 = null;
		$sentences  = null;
		$keywords   = str_replace( "\\", "", $keywords );
		// match with +"[word]" or -"[word]" or "[word]"
		preg_match_all( '#(\+?|\-?)"(.*)"#iU', $keywords, $sentences1 );
		// match with +'[word]' or -'[word]' or '[word]'
		preg_match_all( '#(\+?|\-?)\'(.*)\'#iU', $keywords, $sentences );

		// replace +"[word]" or -"[word]" or "[word]" by ""
		$tmp = preg_replace( '#(\+?|\-?)"(.*)"#iU', '', $keywords );
		// replace +'[word]' or -'[word]' or '[word]' by ''
		$tmp = preg_replace( '#(\+?|\-?)\'(.*)\'#iU', '', $tmp );

		// merge all +"[word]" or -"[word]" or "[word] with all +'[word]' or -'[word]' or '[word]'
		foreach ( $sentences1 as $key => $sen ) {
			$sentences[$key] = array_merge( $sentences[$key], $sen );
		}

		$tmp   = str_replace( "+", " +", $tmp );
		$tmp   = str_replace( "-", " -", $tmp );
		$words = explode( ' ', $tmp );
		foreach ( $words as $key => $word ) {
			if ( ! $word ) {
				continue;
			} // skip word is null
			$sign = substr( $word, 0, 1 );
			$pos  = 1;
			if ( $sign != '+' && $sign != '-' ) {
				$sign = '';
				$pos  = 0;
			}
			$sentences[0][] = $word; // word
			$sentences[1][] = $sign; // + or - or none
			$sentences[2][] = substr( $word, $pos ); // word with prefix + or - or none
		}

		return $sentences;
	}

	public static function trimSpace( $string ) {
		$string = preg_replace( '/\s+/', ' ', $string );

		return $string;
	}

	public static function check( $content, $keywords ) {
		//$keywords	= 'Ontario "Skills Required"';
		$aaa      = $keywords;
		$keywords = self::parseKeyword( $keywords );
		if ( count( $keywords[0] ) < 1 ) {
			return true;
		}
		$content = strip_tags( $content );
		$must    = 0;
		$tmust   = 0;
		$while   = 0;
		$twhile  = 0;
		$black   = 0;
		$tblack  = 0;
		$kExists = array();
		foreach ( $keywords[1] as $key => $value ) {
			if ( $value == '-' ) {
				$tblack ++;
			} elseif ( $value == '+' ) {
				$tmust ++;
			} else {
				$twhile ++;
			}

			$word   = self::trimSpace( $keywords[2][$key] );
			$kExist = preg_match( "#{$word}#iU", $content );
			if ( $kExist == 0 ) {
				$word   = htmlentities( $word );
				$kExist = preg_match( "#{$word}#iU", $content );
			}

			if ( $kExist ) {
				$kExists[] = $word;
				if ( $value == '-' ) {
					$black ++;
				} elseif ( $value == '+' ) {
					$must ++;
				} else {
					$while ++;
				}
			}
		}

		$res = true;
		if ( $tblack > 0 && $black > 0 ) {
			$res = false;
		}
		if ( $tmust > 0 && $must < $tmust ) {
			$res = false;
		}
		if ( $twhile > 0 && $while == 0 ) {
			$res = false;
		}

		if ( isset( $_GET['k'] ) ) {
			echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
			echo 'Keywords: ' . $aaa;
			echo '<hr />' . $content;
			echo '<pre>';
			print_r( $keywords );

			echo '<br />kExists: <br />';
			print_r( $kExists );

			echo '</pre>';
			echo '<br />tmust: ';
			var_dump( $tmust );
			echo '<br />must: ';
			var_dump( $must );
			echo '<br />twhile: ';
			var_dump( $twhile );
			echo '<br />while: ';
			var_dump( $while );
			echo '<br />tblack: ';
			var_dump( $tblack );
			echo '<br />black: ';
			var_dump( $black );
			echo '<br />-----<br />Result: ';
			var_dump( $res );
			echo '<br />';
		}

		return $res;
	}
}