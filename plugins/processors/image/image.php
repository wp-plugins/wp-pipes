<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: image.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );
class WPPipesPro_image {
	public static function process( $data, $params ) {
		global $x;

		// Some debug variables
		$x  = isset( $_GET['pim'] );
		$x1 = isset( $_GET['pim1'] );

		// Debug
		if ( $x1 ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			ogb_pr( $params, 'Params: ' );
			ogb_pr( $data, 'Data: ' );
		}
		$html = $data->html;
		

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

		return $res;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->input  = array( 'url', 'html' );
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
		$host        = JURI::root();
		$number_imgs = (int) $params->number_imgs;
		$fimgs       = count( $imgs[0] );
		if ( $number_imgs > $fimgs ) {
			$number_imgs = $fimgs;
		}
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
				$path = JPATH_SITE . DS . $src;
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
		$matches = array();
		preg_match_all( "#<img*[^\>]*>#i", $contents, $matches );
		if ( ! isset( $matches[0][0] ) ) {
			return $contents;
		}
		$searches = $matches[0];

		$local_dir = $params->image_local;
		if ( substr( $local_dir, 0, 1 ) == '/' ) {
			$local_dir = substr( $local_dir, 1 );
		}
		$url_path = JURI::root() . $local_dir;
		$to       = array( 'host' => str_replace( "\\", "/", $url_path ), 'path' => $local_dir );

		if ( isset( $params->origin_url ) && $params->origin_url != '' ) {
			$origin_url = $params->origin_url;
		} else {
			$url_parts  = parse_url( $itemLink );
			$origin_url = @$url_parts['scheme'] . "://" . @$url_parts['host'];
		}
		$dest_host = isset ( $to['host'] ) ? $to['host'] : '';
		$more_path = date( 'Y-m' );
		$dest_path = isset ( $to['path'] ) ? JPATH_ROOT . DS . $to['path'] . DS . $more_path : '';

		if ( ! preg_match( '!^https?://.+!i', $dest_host ) ) {
			$dest_host = str_replace( "administrator/", "", JURI :: base() ) . $dest_host;
		}
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
			preg_match_all( '#src=\"*[^\"]*\"#', $img, $src );
			$src = preg_replace( "#src\s*=\s*\"|\"#", "", @$src[0][0] );
			if ( ! preg_match( '!https?://.+!i', $src ) ) {
				$source_urls[$i] = $origin_url . $src;
			} else {
				$source_urls[$i] = $src;
			}
			//$filename	= JFile :: getName(preg_replace("#/#", DS, $src));
			$filename = substr( md5( $src ), - 10 ) . '.jpg';

			$success = false;
			if ( $dest_host && $dest_path ) {
				$s       = $source_urls[$i];
				$d       = $dest_path . DS . $filename;
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
					$aa = JFolder::create( $dest_path );
					if ( $aa ) {
						$img_c = ogbFile::get_curl( $s );
						$a     = JFile::write( $d, $img_c );
						//$a = copy($s, $d);
						if ( is_file( $d ) ) {
							$size = filesize( $d );
							if ( $size > 0 ) {
								$img_info = getimagesize( $d );
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
}