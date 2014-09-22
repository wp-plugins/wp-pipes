<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: psc.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once 'gcurl.php';

class ogb_parser_code extends ogb_get_CURL {
	public static function setStop( &$data, $msg = 'unknow', $state = true ) {
		$stop        = new stdClass();
		$stop->state = $state;
		$stop->msg   = $msg;
		$data->stop  = $stop;
	}

	public static function run_parser_code( &$res, $html, $params ) {
		$codes = preg_replace( '/^[\n\r]*|[\n\r]*$/i', '', $params->code );
		$funcs = explode( "\n", $codes );
		if ( isset( $_GET['php'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo $html;
			exit();
		}
		$rows   = array();
		$rows[] = $html;
		$pod    = isset( $_GET['pod'] ) ? $_GET['pod'] : 0;
		for ( $i = 0; $i < count( $funcs ); $i ++ ) {
			$resf = self::runFuncs( $funcs[$i], $rows );
			if ( isset( $_GET['php3'] ) && $pod == $i ) {
				echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
				echo isset( $resf[1] ) ? $resf[1] : '[ no error ]';
				echo $resf[0];
				exit();
			}
			if ( ! isset( $resf[1] ) ) {
				$rows[] = $resf[0];
			} else {
				self::setStop( $res, "Error row-{$i}: " . $resf[1] );

				return $res;
			}
		}
		if ( isset( $_GET['php5'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			$ig = isset( $_GET['ig'] ) ? $_GET['ig'] : '0,1';
			$ig = explode( ',', $ig );
			for ( $i = 0; $i < count( $rows ); $i ++ ) {
				if ( in_array( $i, $ig ) ) {
					$rows[0] = '';
				}
			}
			echo '<pre>';
			print_r( $rows );
			echo '</pre>';
			exit();
		}
		$max           = count( $rows );
		$html          = $rows[$max - 1];
		$res->fulltext = $html;

		return $res;
	}

	public static function clear_tags( $html, $tags ) {
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

		if ( in_array( $tag, array( "br", "img", "hr", "input", "link", "meta" ) ) ) {
			$mix = '/<' . $tag . '[\s|\S]*?>/iu';
			$old_html = $html;
			$html     = preg_replace( $mix, "", $old_html );
			if ( ! $html ) {
				$mix  = str_replace( '/iu', '/i', $mix );
				$html = preg_replace( $mix, "", $old_html );
			}

			return $html;
		}

		$a       = explode( "</{$tag}>", $html );
		$count_a = count( $a );
		if ( ! isset( $a[1] ) ) {
			return $html;
		}
		$c = array();
		for ( $i = 0; $i < $count_a; $i ++ ) {
			$b = explode( "<{$tag}", $a[$i] );
			if ( isset( $b[0] ) ) {
				$c[] = $b[0];
				continue;
			}
			//$c[] = $b[0];
		}
		$html = implode( '', $c );

		return $html;
	}

	public static function stripTags( $html, $tags ) {
		$tags = explode( ',', $tags );
		for ( $i = 0; $i < count( $tags ); $i ++ ) {
			$html = self::strip_tag( $html, $tags[$i] );
		}

		return array( $html );
	}

	public static function strip_tag( $html, $tag ) {
		$html = preg_replace( '/<' . strtoupper( $tag ) . '\s+[^>]*>|<' . strtoupper( $tag ) . '\s*>|<' . strtolower( $tag ) . '\s+[^>]*>|<' . strtolower( $tag ) . '\s*>/', '', $html );
		$html = str_replace( '</' . strtoupper( $tag ) . '>', '', $html );
		$html = str_replace( '</' . strtolower( $tag ) . '>', '', $html );

		return $html;
	}

	public static function get_input( $code, $rows ) {
		$code   = explode( ',', $code );
		$inputs = array();
		for ( $i = 0; $i < count( $code ); $i ++ ) {
			$k = (int) $code[$i];
			if ( ! isset( $rows[$k] ) ) {
				$inputs[100] = $k;
				break;
			}
			$inputs[] = $rows[$k];
		}

		return $inputs;
	}

	public static function runFuncs( $funcs, $rows ) {
		$funcs = str_replace( '\"', '"', $funcs );
		$funcs = explode( '|', trim( $funcs ) );
		$funcs = str_replace( '::OR::', '|', $funcs );
		if ( ! isset( $funcs[1] ) ) {
			if ( ! isset( $rows[1] ) ) {
				$res = $rows;
			} else {
				$res = array( '', 'none row' );
			}

			return $res;
		}

		$inputs = self::get_input( $funcs[1], $rows );
		if ( isset( $inputs[100] ) ) {
			$res = array( '', 'Input 100' );

			return $res;
		}
		switch ( $funcs[0] ) {
			case 'ginner':
				//ginner|0|div|id="abc"|0|1|1
				$op   = isset( $funcs[4] ) ? $funcs[4] : 0;
				$err  = isset( $funcs[5] ) ? $funcs[5] : 0;
				$ctag = isset( $funcs[6] ) ? $funcs[6] : 0;
				$res  = self::ginner( $inputs[0], $funcs[2], $funcs[3], $op, $err, $ctag );
				break;
			case 'remove':
				//remove|0|div|id="abc"|1
				$ctag = isset( $funcs[4] ) ? $funcs[4] : 0;
				$res  = self::remove_tag( $inputs[0], $funcs[2], $funcs[3], $ctag );
				break;
			case 'split':
				$err = isset( $funcs[4] ) ? $funcs[4] : 0;
				$res = self::split( $inputs, $funcs[2], $funcs[3], $err );
				break;
			case 'wrap':
				$err = isset( $funcs[3] ) ? $funcs[3] : 0;
				$res = self::wrap( $inputs, $funcs[2], $err );
				break;
			case 'replace':
				$param_arr = array_splice( $funcs, 2 );
				$param_arr = array_merge( array( $inputs ), $param_arr );
				$res       = call_user_func_array( array( 'ogb_parser_code', 'replace' ), $param_arr );
				//self::replace($inputs,$funcs[2],$funcs[3]);
				break;
			case 'stribtags':
				//stribtags|0|span,a|
				$res = self::stripTags( $inputs[0], $funcs[2] );
				break;
			case 'clntags':
				//clntags|1|iframe,span|
				$ignore = isset( $funcs[2] ) ? $funcs[2] : 'iframe,textarea';
				$res    = self::clear_none_tags( $inputs[0], $ignore );
				break;
			default:
				$res = array( '', "function not exist ({$funcs[0]})" );
		}

		return $res;
	}

	public static function split( $inputs, $code, $el, $err = 0 ) {
		$a  = explode( $code, $inputs[0] );
		$ca = count( $a );
		if ( $el == 'L' ) {
			$el = $ca - 1;
		} else {
			$el = (int) $el;
		}
		$b   = isset( $a[1] );
		$res = array();
		if ( $b && isset( $a[$el] ) ) {
			$res[] = $a[$el];
		} else {
			$res[] = '';
			if ( $err == 1 ) {
				if ( $b ) {
					$res[] = 'Element not found';
				} else {
					$res[] = 'Code not found';
				}
			} elseif ( $err == 2 ) {
				$res[0] = $a[0];
			}
		}

		return $res;
	}

	public static function wrap( $inputs, $code, $err = 0 ) {
		if ( ! isset( $inputs[1] ) && $inputs[0] == '' ) {
			$res = array( '' );
			if ( $err == 1 ) {
				$res[] = 'input none';
			}

			return $res;
		}
		for ( $i = 0; $i < count( $inputs ); $i ++ ) {
			$code = str_replace( "{ogb-{$i}}", $inputs[$i], $code );
		}

		return array( $code );
	}

	public static function replace( $inputs, $search, $replace, $reg = 0, $reg_limit = 0, $reg_pattern = '' ) {
		$arg = func_get_args();
		if ( $reg ) {
			if ( $reg_limit ) {
				$html = preg_replace( $search, $replace, trim( $inputs[0] ), $reg_limit );
			} else {
				$html = preg_replace( $search, $replace, $inputs[0] );
			}
		} else {
			$html = str_replace( $search, $replace, $inputs[0] );
		}

		return array( $html );
	}

	//remove|div|id="abc"
	public static function remove_tag( $html, $tag, $code, $ctag = 0 ) {
		if ( $code == '' ) {
			$html = self::clear_tag( $html, $tag );

			return array( $html );
		}
		$rtag = self::ginner( $html, $tag, $code, 0, 0, $ctag );
		if ( $rtag[0] != '' ) {
			$html = str_replace( $rtag[0], '', $html );
			$html = self::remove_tag( $html, $tag, $code, $ctag );

			return $html;
		}

		return array( $html );
	}

	//ginner|div|id="abc"|0|1
	public static function ginner( $html, $tag, $code, $el = 0, $err = 0, $ctag = 0, $bor = 1 ) {
		if ( $code == '' ) {
			$tags = array( "<{$tag}>" );
		} else {
			$regex = "~<{$tag} [^>]*{$code}.*?>~";
			preg_match_all( $regex, $html, $tags );
			$tags = $tags[0];
		}
		if ( isset( $_GET['php6'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo '<pre>';
			print_r( $tags );
			echo '</pre>';
			//exit("<h4>Stop ".__LINE__."</h4>");
		}
		if ( ! isset( $tags[0] ) ) {
			$res = array( '' );
			if ( $err == 1 ) {
				$res[1] = 'Tag not found';
			}

			return $res;
		}
		$ftag = $tags[0];
		if ( $el = 'L' ) {
			$m    = count( $tags ) - 1;
			$ftag = $tags[$m];
		} elseif ( $el != 0 ) {
			if ( ! isset( $tags[$el] ) ) {
				if ( $err = 2 ) {
					$ftag = $tags[0];
				} elseif ( $err = 1 ) {
					return array( '', 'Element not found' );
				}
			} else {
				$ftag = $tags[$m];
			}
		}
		if ( $ctag == 0 ) {
			$ftag = self::getTagInner( $html, $tag, $ftag );
		}

		return array( $ftag );
	}

	public static function getTagInner( $html, $tag, $ftag, $n = 0 ) {
		$a    = explode( $ftag, $html );
		$ctag = "</{$tag}>";
		$b    = explode( $ctag, $a[1] );
		$k    = $b[0] . $ctag;
		if ( $n > 0 ) {
			for ( $i = 1; $i <= $n; $i ++ ) {
				$k .= $b[$i] . $ctag;
			}
		}
		$c  = explode( "<{$tag}", $k );
		$cc = count( $c ) - 1;
		if ( $cc > $n ) {
			$n += $cc - $n;
			$res = self::getTagInner( $html, $tag, $ftag, $n );
		} else {
			$res = $ftag . $k;
		}

		return $res;
	}

	//clntags|1|iframe,span|
	public static function clear_none_tags( $html, $ignore = 'iframe,textarea' ) {
		//$regex	= "~<\w+[^>]*>\s*</\w+>~";
		$regex = "~<\w+[^>]*></\w+>~";
		preg_match_all( $regex, $html, $tags );

		if ( isset( $_GET['php8'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo '<pre>';
			print_r( $tags );
			echo '</pre>';
			//exit("<h4>Stop ".__LINE__."</h4>");
		}

		if ( empty( $tags[0] ) ) {
			return array( $html );
		}
		$ignore = explode( ',', $ignore );
		for ( $i = 0; $i < count( $tags[0] ); $i ++ ) {
			$str = $tags[0][$i];
			if ( self::is_none_tag( $str, $ignore ) ) {
				$html = str_replace( $str, '', $html );
			}
		}

		return array( $html );
	}

	public static function is_none_tag( $html, $ignore ) {
		$a    = explode( '></', $html );
		$ctag = substr( $a[1], 0, - 1 );
		if ( in_array( $ctag, $ignore ) ) {
			return false;
		}
		$a    = explode( ' ', $a[0] );
		$otag = substr( $a[0], 1 );
		$res  = $ctag == $otag;
		if ( isset( $_GET['php8'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo "|{$ctag}| == |{$otag}|";
			var_dump( $res );
		}

		return $res;
	}

	//clattr|class,id|*,div,p|
	public static function clear_attribs( $html, $attr, $tags = '*' ) {
		$attr = explode( ',', $attr );
		for ( $i = 0; $i < count( $attr ); $i ++ ) {
			$html = self::clear_attrib( $html, $attr[$i] );
		}

		return array( $html );
	}

	public static function clear_attrib( $html, $attr, $tags = '*' ) {
		$html = preg_replace( '/ ' . $attr . '\s*=\s*"[^"]*"?/', '', $html );

		return $html;
	}
}
