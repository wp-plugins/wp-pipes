<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: gcurl.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
class ogb_cache {
	// --- Get and Update data { ---
	public static function update_cache( $path, $data, $type = 'html' ) {
		switch ( $type ) {
			case 'arr':
				$ud = self::update_cache_arr( $path, $data );
				break;
			default:
				$ud = self::update_cache_html( $path, $data );
		}
		return $ud;
	}

	public static function get_cache( $path, $type = 'html' ) {
		switch ( $type ) {
			case 'arr':
				$data = self::get_cache_arr( $path );
				break;
			default:
				$data = self::get_cache_html( $path );
		}
		return $data;
	}

	public static function write_data( $path, $data ) {
		if ( class_exists( 'ogbFile' ) ) {
			$wr = ogbFile::write( $path, $data );
		} else {
			$wr = true;
			if ( ! is_file( $path ) ) {
				$wr = self::mkdir( $path );
			}
			if ( $wr ) {
				$wr = file_put_contents( $path, $data );
			}
		}

		return $wr;
	}

	public static function mkdir( $path ) {
		$dir = dirname( $path );
		if ( is_dir( $dir ) ) {
			return true;
		}

		return mkdir( $dir );
	}

	// --- Get and Update data } ---
	// --- Get and Update data - ARRAY { ---
	public static function update_cache_arr( $path, $rows ) {
		$data = serialize( $rows );
		$ud   = self::write_data( $path, $data );

		return $ud;
	}

	public static function get_cache_arr( $path ) {
		$rows = file_get_contents( $path );
		$rows = unserialize( $rows );
		if ( ! is_array( $rows ) ) {
			return array();
		}

		return $rows;
	}

	// --- Get and Update data - ARRAY } ---
	// --- Get and Update data - HTML { ---
	public static function update_cache_html( $path, $html ) {
		$ud = self::write_data( $path, $html );

		return $ud;
	}

	public static function get_cache_html( $path ) {
		$html = file_get_contents( $path );

		return $html;
	}

	// --- Get and Update data - HTML } ---
	public static function getCachePath( $url, $dir = 'urldata' ) {
		$path = OGRAB_CACHE . $dir . DS . date( 'Y-m' ) . DS . md5( $url ) . '.html';

		return $path;
	}

	public static function need_update( $path, $time = 120 ) {
		$time = 3600 * $time;
		if ( isset( $_GET['uc'] ) ) {
			return true;
		}
		if ( ! is_file( $path ) ) {
			return true;
		}
		$cache_mtime = filemtime( $path );
		$diff        = time() - $cache_mtime;
		if ( isset( $_GET['gc'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'Path: ';
			var_dump( $path );
			echo '<br />Last Update  - s: ';
			var_dump( $diff );
			if ( $diff > 60 ) {
				$a = $diff / 60;
				echo ' - m: ';
				var_dump( $a );
			}
			if ( $a > 60 ) {
				$a = $a / 60;
				echo ' - h: ';
				var_dump( $a );
			}
		}

		return $diff > $time;
	}
}

class ogb_get_CURL extends ogb_cache {
	public static function get_foobla_url( $url ) {
		if ( ! isset( $_GET['ft1'] ) ) {
			return $url;
		}
		$md5 = $_GET['ft1'];
		$url = 'http://kha.wppipes.com/html_parser/cache/urldata/';
		$url .= date( 'Y-m' ) . "/{$md5}.html";

		echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
		echo "URL: <a href=\"{$url}\">{$url}</a>";

		return $url;
	}

	public static function getCURL( $url, $mode ) {
		$url        = self::get_foobla_url( $url );
		$typeData   = 'html';
		$cache_path = self::getCachePath( $url );
		$need_ud    = self::need_update( $cache_path );
		if ( ! $need_ud ) {
			$data = self::get_cache( $cache_path, $typeData );

			return array( 200, $data );
		}
		switch ( $mode ) {
			case 4:
				$html = self::get_curl4( $url );
				break;
			case 3:
				$html = self::get_curl3( $url );
				break;
			case 2:
				$html = self::get_curl2( $url );
				break;
			default:
				$html = self::get_curl( $url );
		}
		if ( $html[0] == 200 ) {
			//$clssCache->update_cache($cache_path,$html[1],$typeData);
			self::update_cache( $cache_path, $html[1], $typeData );
		}

		return $html;
	}

	public static function x11( $response, $loop, $html, $lv = 1 ) {
		if ( ! isset( $_GET['x11'] ) ) {
			return;
		}
		echo '<hr /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
		echo "LV {$lv} - Loop: {$loop}<br />";
		echo '<pre>';
		print_r( $response );
		echo "</pre>\n";
		if ( isset( $_GET['x12'] ) && $_GET['x12'] == $loop ) {
			$html = $html != '' ? "\n{$html}\n" : 'None';
			echo "--->|{$html}|<---\n";
			if ( ! isset( $_GET['x13'] ) ) {
				exit();
			}
		}
	}

	public static function get_curl4( $url ) {
		$link = "http://kha.wppipes.com/curl/?m=3&url=" . base64_encode( $url );
		$html = self::get_curl( $link );
		if ( isset( $_GET['php2'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'URL: ' . $link;
			echo 'http_code: ' . $html[0];
			echo $html[1];
			exit();
		}

		return $html;
	}

	public static function get_curl3( $url ) {
		$html = file_get_contents( $url );

		return array( 200, $html );
	}

	public static function check_curl_init() {
		if ( function_exists( 'curl_init' ) ) {
			return true;
		}

		return false;
	}

	public static function get_curl( $url, $loop = 10 ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		ob_start();
		curl_exec( $ch );
		$response = curl_getinfo( $ch );
		curl_close( $ch );
		$content = ob_get_clean();
		self::x11( $response, $loop, $content );
		if ( $response['http_code'] != 200 && $response['redirect_url'] != '' ) {
			$loop --;
			$url = $response['redirect_url'];
			if ( $loop > 0 ) {
				return self::get_curl( $url, $loop );
			}
			$content = "<p>[ http_code: {$response['http_code']} ][ redirect_url:{$url} ]</p>";

			return array( $response['http_code'], $content );
		}

		return array( $response['http_code'], $content );
	}

	public static function get_curl2( $url, $loop = 10, $javascript_loop = 0, $timeout = 5 ) {
		//$cookie = tempnam("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
		curl_setopt( $ch, CURLOPT_URL, $url );
		//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false ); # required for https urls
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		$content  = curl_exec( $ch );
		$response = curl_getinfo( $ch );
		curl_close( $ch );
		self::x11( $response, $loop, $content, 2 );
		if ( $response['http_code'] != 200 && $response['redirect_url'] != '' ) {
			$loop --;
			$url = $response['redirect_url'];
			if ( $loop > 0 ) {
				return self::get_curl2( $url, $loop );
			}
			//$info	= "[ http_code: {$response['http_code']} ][ redirect_url={$url} ]";
			//return array($response['http_code'],$info);
		}
		//-----
		if ( $response['http_code'] == 301 || $response['http_code'] == 302 ) {
			ini_set( "user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
			if ( $headers = get_headers( $response['url'] ) ) {
				foreach ( $headers as $value ) {
					if ( substr( strtolower( $value ), 0, 9 ) == "location:" ) {
						return self::get_curl2( trim( substr( $value, 9, strlen( $value ) ) ) );
					}
				}
			}
		}
		if ( ( preg_match( "/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value ) || preg_match( "/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value ) ) && $javascript_loop < 5 ) {
			return self::get_curl2( $value[1], $javascript_loop + 1 );
		}
		if ( preg_match( "'<meta[\s]*http-equiv[\s]*=[\s]*[\"\']?refresh'i", $content ) ) {
			$redirectaddr = "";
			preg_match( "'content[\s]*=[\s]*[\"\']?(\d+);[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i", $content, $match );
			if ( $match && $match[1] == 0 ) {
				if ( ! preg_match( "|\:\/\/|", $match[2] ) ) {
					// no host in the path, so prepend
					$parseurl     = parse_url( $url );
					$redirectaddr = $parseurl["scheme"] . "://" . $parseurl["host"];
					if ( $parseurl["port"] ) {
						$redirectaddr .= ":" . $parseurl["port"];
					}
					// eliminate double slash
					if ( ! preg_match( "|^/|", $match[2] ) ) {
						$redirectaddr .= "/" . $match[2];
					} else {
						$redirectaddr .= $match[2];
					}
				} else {
					$redirectaddr = $match[2];
				}
				if ( $redirectaddr ) {
					return self::get_curl2( $redirectaddr, $javascriptloop + 1 );
				}
			}
		}

		return array( $response['http_code'], $content );
	}
}