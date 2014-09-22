<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: image.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );

class WPPipesPro_image {
	public static function check_params_df( $params ) {
		$df              = new stdclass();
		$df->get_image   = 1;
		$df->clear_tiny  = '64x64';
		$df->image_local = 'images/wppipes';
		$df->origin_url  = '';
		$df->makelist    = 1;
		$df->number_imgs = 1;
		$df->remove      = 0;

		foreach ( $df as $key => $val ) {
			if ( ! isset( $params->$key ) ) {
				@$params->$key = $val;
			}
		}

		return $params;
	}

	public static function process( $data, $params ) {
		global $x;
		$params = self::check_params_df( $params );
		// Some debug variables
		$x  = isset( $_GET['pim'] );
		$x1 = isset( $_GET['pim1'] );

		// Debug
		if ( $x1 ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			ogb_pr( $params, 'Params: ' );
			ogb_pr( $data, 'Data: ' );
		}
		if ( isset( $data->enclosure ) && is_array( $data->enclosure ) ) {
			$html = '';
			foreach ( $data->enclosure as $obj ) {
				$enclosure = (array) $obj;
				if ( isset( $params->limit_width ) ) {
					$limit_period = explode( ';', $params->limit_width );
					$width_img = (isset($enclosure['width']) && $enclosure['width'] > 0 )? $enclosure['width'] : 0;
					if ( count( $limit_period ) > 1 && ( ( (int) $limit_period[0] > $width_img ) || ( $width_img > (int) $limit_period[1] ) ) ) {
						continue;
					}
				}
				if ( strpos( $enclosure['type'], 'image' ) !== false ) {
					$html .= '<img src="' . $enclosure['link'] . '" title="' . $enclosure['title'] . '">';
				}
			}
		} else {
			$html = $data->html;
		}

		$res         = new stdClass();
		$res->html   = $html;
		$res->images = array();

		// Debug
		if ( $x ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			//ogb_show($data->html,'data->html: ');
		}

		// if there is nothing set for the image path, store all images to /images/wppipes/ folder
		if ( $params->get_image ) {
			if ( $params->image_local == '' ) {
				$params->image_local = 'images/wppipes';
			}
			$html      = self::copyImage( $html, $data->url, $params );
			$res->html = $html;
		} else {
			return $res;
		}
		if ( $params->makelist == 1 ) {
			$res = self::make_list( $html, $params );
			// Debug
			if ( $x ) {
				echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
				ogb_pr( $res, 'Imgs: ' );
			}
		}
		if ( empty( $res->images ) && ( $params->stop_if_empty == 1 ) ) {
			$stop        = new stdClass();
			$stop->state = true;
			$stop->msg   = 'no images found';
			$res->stop   = $stop;

			return $res;
		}
		if ( isset( $params->default_img ) && $params->default_img != '' ) {
			$default_img       = new stdClass();
			$default_img->html = "<img src=\"{$params->default_img}\" alt=\"The default image\">";
			$default_img->src  = $params->default_img;
			if ( empty( $res->images ) ) {
				$res->images[] = $default_img;
			}
		}

		return $res;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->input  = array( 'url', 'html', 'enclosure' );
		$data->output = array( 'images', 'html' );

		return $data;
	}

	public static function make_list( $html, $params ) {
		$res         = new stdClass();
		$res->html   = $html;
		$res->images = array();

		//$html = preg_replace("#<img*[^\>]*>#i", '', $html);
		preg_match_all( "#<img*[^\>]*>#i", $html, $imgs );
		if ( ! isset( $imgs[0][0] ) ) {
			return $res;
		}
		$images      = array();
		$host        = get_site_url();
		$number_imgs = (int) $params->number_imgs;
		$fimgs       = count( $imgs[0] );
		if ( $number_imgs > $fimgs ) {
			$number_imgs = $fimgs;
		}
		$wp_root_path = str_replace( '/wp-content/themes', '', get_theme_root() );
		for ( $i = 0; $i < $number_imgs; $i ++ ) {
			$img = $imgs[0][$i];
			preg_match( '#src\s*=\s*"(.*?)"#i', $img, $src );
			$image = new stdClass();
			//$image->html	= '';
			$image->html = $img;
			$image->src  = $src[1];
			$image->path = '';
			if ( $params->get_image == 1 ) {
				$src  = str_replace( $host, '', $image->src );
				$src  = str_replace( '/', DS, $src );
				$path = $wp_root_path . $src;
				if ( is_file( $path ) ) {
					$image->path = $path;
				}
			}
			if ( $params->remove == 1 ) {
				$html = str_replace( $img, '', $html );
			}
			$images[] = $image;
		}
		$res->html   = $html;
		$res->images = $images;

		return $res;
	}

	public static function copyImage( $contents = '', $itemLink = '', $params ) {
		$matches    = array();
		$upload_dir = wp_upload_dir();
		preg_match_all( "#<img*[^\>]*>#i", $contents, $matches );
		if ( ! isset( $matches[0][0] ) ) {
			return $contents;
		}
		$searches = $matches[0];

		$local_dir = $params->image_local;
		if ( substr( $local_dir, 0, 1 ) == '/' ) {
			$local_dir = substr( $local_dir, 1 );
		}
		$upload_path = $upload_dir['basedir'];
		$url_path    = $upload_dir['baseurl'] . DS . $local_dir;

		$to = array( 'host' => str_replace( "\\", "/", $url_path ), 'path' => $local_dir );

		if ( isset( $params->origin_url ) && $params->origin_url != '' ) {
			$origin_url = $params->origin_url;
		} else {
			$url_parts  = parse_url( $itemLink );
			$origin_url = @$url_parts['scheme'] . "://" . @$url_parts['host'];
		}

		$dest_host = isset ( $to['host'] ) ? $to['host'] : '';

		$more_path = date( 'Y-m' );
		$dest_path = isset ( $to['path'] ) ? $upload_path . DS . $to['path'] . DS . $more_path : '';

		/*if ( ! preg_match( '!^https?://.+!i', $dest_host ) ) {
			$dest_host = str_replace( "administrator/", "", JURI :: base() ) . $dest_host;
		}*/
		$dest_parts  = parse_url( $dest_host );
		$source_urls = array();
		$replaces    = $searches;
		$b           = explode( "'", $searches[0] );
		$repl        = isset( $b[1] );

		$iMin = array( 64, 64 );
		if ( isset( $params->clear_tiny ) ) {
			$clear_tiny = explode( 'x', $params->clear_tiny );
			$iMin[0]    = (int) $clear_tiny[0];
			if ( isset( $clear_tiny[1] ) ) {
				$iMin[1] = (int) $clear_tiny[1];
			}
		}

		for ( $i = 0; $i < count( $searches ); $i ++ ) {
			$remove = false;
			$img    = $searches[$i];
			if ( $repl ) {
				$img          = str_replace( "'", '"', $searches[$i] );
				$contents     = str_replace( $searches[$i], $img, $contents );
				$searches[$i] = $replaces[$i] = $img;
			}
			preg_match_all( '#\ssrc=\"*[^\"]*\"#', $img, $src );

			$src = preg_replace( "#src\s*=\s*\"|\"#", "", @$src[0][0] );

			if ( ! preg_match( '!https?://.+!i', $src ) ) {
				$source_urls[$i] = $origin_url . trim( $src );
			} else {
				$source_urls[$i] = $src;
			}

			if ( @$params->special_img_url ) {
				$source_urls[$i] = self::img_url_encode( $source_urls[$i] );
			}

			$info_file = @get_headers( trim( $source_urls[$i] ), 1 );
			if ( ! $info_file ) {
				$curl                      = self::get_web_page( trim( $source_urls[$i] ) );
				$info_file['Content-Type'] = $curl['content_type'];
				$mime                      = $info_file['Content-Type'];
			} else {
				$mime = $info_file['Content-Type'];
			}

			if ( count( $mime ) > 1 ) {
				$mime = implode( "/", $mime );
			}
			$temp_arr = explode( "/", $mime );

			if ( ! in_array( 'image', $temp_arr ) ) { //check is image or not
				continue;
			}

			$filename = substr( md5( $source_urls[$i] ), - 10 ) . '.' . end( $temp_arr );

			$success = false;

			if ( $dest_host && $dest_path ) {
				$s = $source_urls[$i];
				$d = $dest_path . DS . $filename;

				$success = file_exists( $d );
				$unlink  = true;
				//$remove		= true;

				// Debug
				if ( isset( $_GET['i'] ) ) {
					$img_info = array();
					echo '<hr /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
					echo '[ url_path ]: ';
					var_dump( $url_path );
					echo '<br />[ <a href="' . $s . '" target="_blank">source_urls</a> ]: ' . $s;
					echo '<br />[ <a href="' . $url_path . '/' . $filename . '" target="_blank">dest_path</a> ]: ' . $d;
					if ( isset( $_GET['y'] ) ) {
						echo '<br /><br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
						if ( $success ) {
							$success = false;
							$k       = unlink( $d );
							echo 'Unlink: ';
							var_dump( $k );
						} else {
							echo 'File not exist';
						}
						echo 'dest_path:';
						var_dump( $k );
					}
				}

				if ( ! $success ) {
					$aa = ogbFolder::create( $dest_path );
					if ( $aa ) {

						$img_c = ogbFile::get_curl( trim( $s ) );

						$a = ogbFile::write( $d, $img_c );

						//$a = copy($s, $d);
						if ( is_file( $d ) ) {
							$size = filesize( $d );
							if ( $size > 0 ) {
								$img_info = getimagesize( trim( $source_urls[$i] ) );
								$width    = isset( $img_info[0] ) ? $img_info[0] : 0;
								$height   = isset( $img_info[1] ) ? $img_info[1] : 0;

								$remove = $iMin[0] > 0 && $width > 0 && $width < $iMin[0];
								if ( ! $remove ) {
									$remove = $iMin[1] > 0 && $height > 0 && $height < $iMin[1];
								}
								if ( ! $remove ) {
									$success = true;
									$unlink  = false;
								}
							}
							if ( $unlink && ! isset( $_GET['nodel'] ) ) {
								unlink( $d );
							}
						} else {
							$size = 0;
						}

						// Debug
						if ( isset( $_GET['i'] ) ) {
							echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
							echo 'copy image Success: ';
							var_dump( $a );
							echo '<br />file image exist: ';
							var_dump( $success );
							echo '<br />Size: ';
							var_dump( $size );
							echo '<br />Unlink:';
							var_dump( $unlink );
							echo '<pre>';
							print_r( $iMin );
							print_r( $img_info );
							echo '</pre>';
						}
					}
				}
			}

			if ( $remove ) {
				$replaces[$i] = '';
			} else {
				if ( $success ) {
					$replace = "src=\"" . ( $dest_host . '/' . $more_path . '/' . $filename ) . "\"";
				} else {
					if ( ! preg_match( '!^https?://.+!i', $src ) ) { // if src is not contain host=>add host to src
						$source_path = $origin_url . '/' . $src;
					} else {
						$source_path = $src;
					}
					$replace = "src=\"" . $source_urls[$i] . "\"";
				}
				$replaces[$i] = preg_replace( "#src\s*=\s*\"*[^\"]*\"#", $replace, $replaces[$i] );
			}
		}

		$contents = str_replace( $searches, $replaces, $contents );

		// Debug
		if ( isset( $_GET['i2'] ) ) {
			echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
			echo '$itemLink: ';
			var_dump( $itemLink );
			echo '<pre>';
			echo 'searches:';
			print_r( $searches );
			echo 'replaces:';
			print_r( $replaces );
			echo '</pre>';
			echo '<hr />' . $contents;
			exit();
		}

		return $contents;
	}

	public static function img_url_encode( $url ) {
		$url        = html_entity_decode( $url, 0, "UTF-8" );
		$split_url  = explode( "/", $url );
		$result_str = array();
		foreach ( $split_url as $value ) {
			$result_str[] = urlencode( $value );
		}
		$result = implode( "/", $result_str );
		$result = str_replace( "%3A//", "://", $result );

		return $result;
	}

	public static function get_web_page( $url ) {
		$options = array(
			CURLOPT_RETURNTRANSFER => true, // return web page
			CURLOPT_HEADER         => false, // don't return headers
			CURLOPT_FOLLOWLOCATION => true, // follow redirects
			CURLOPT_ENCODING       => "utf-8", // handle all encodings
			CURLOPT_USERAGENT      => "spider", // who am i
			CURLOPT_AUTOREFERER    => true, // set referer on redirect
			CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
			CURLOPT_TIMEOUT        => 120, // timeout on response
			CURLOPT_MAXREDIRS      => 10, // stop after 10 redirects
		);

		$ch = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		$header['errno']  = $err;
		$header['errmsg'] = $errmsg;

		return $header;
	}
}