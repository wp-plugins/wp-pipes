<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: common.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class ogb_common {
	static function get_params_render( $dir, $file, $values, $xpath = '//config', $control = 'jform', $type = 'plugins', $show_group = false ) {
		$xml_dir = PIPES_PATH . DS . $dir;

		if ( ! is_file( $xml_dir . DS . $file . '.xml' ) ) {
			$xml_dir = OB_PATH_PLUGIN . $file;
		}
		if ( ! is_file( $xml_dir . DS . $file . '.xml' ) ) {
			return false;
		}
		jimport( 'includes.form.form' );
		jimport( 'includes.form.field' );
		jimport( 'includes.html.html' );
		jimport( 'includes.html.select' );
		jimport( 'includes.form.helper' );
		jimport( 'includes.registry.registry' );
		jimport( 'includes.string.string' );
		JForm::addFormPath( $xml_dir );

		JForm::addFieldPath( PIPES_PATH . DS . 'includes' . DS . 'form' . DS . 'fields' );
		JForm::addFieldPath( $dir . DS . 'fields' );

		$name    = 'com_wppipes.' . $type;
		$options = array(
			'control'   => $control,
			'load_data' => true
		);

		jimport( 'filesystem.folder' );
		if ( JFolder::exists( $xml_dir . DS . 'elements' ) ) {
			JForm::addFieldPath( $xml_dir . DS . 'elements' );
		}
		if ( JFolder::exists( $xml_dir . DS . 'fields' ) ) {
			JForm::addFieldPath( $xml_dir . DS . 'fields' );
		}
		$form = JForm::getInstance( $name, $file, $options, false, $xpath );

		$values = json_decode( $values );
		$values = array( 'params' => $values );
		$temp   = new JRegistry;
		$temp->loadArray( $values );

		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			$file = $xml_dir . DS . $file . '.xml';
			echo '<br />' . $file . ' [ XML file ]';
			$a = is_file( $file );
			echo "[ file exist: ";
			var_dump( $a );
			echo " ]";
			echo '<pre>';
			print_r( $temp );
			echo '</pre>';
		}

		$form->bind( $temp );

		$fieldSets = $form->getFieldsets();
		$li        = array();
//		foreach ($fieldSets as $name => $fieldSet) {

		/*$label = empty($fieldSet->label) ? 'COM_CONFIG_'.$name.'_FIELDSET_LABEL' : $fieldSet->label;
		$li	.= JHtml::_('tabs.panel', JText::_($label), 'publishing-details');
		if (isset($fieldSet->description) && !empty($fieldSet->description)) {
			$li	.= '<p class="tab-description">'.JText::_($fieldSet->description).'</p>';
		}*/
//			foreach ($form->getFieldset($name) as $field){
//				$li[]	= '<li>'.($field->hidden?'':$field->label).$field->input.'</li>';
//			}
//		}

		$element = '';
		// load basic fieldset
		$plugin_type = explode( ".", $type );
//		$i           = 1;
		foreach ( $form->getFieldset( 'basic' ) AS $field ) {
			if ( $field->class == 'fullwidth' ) {
				$li_class = 'col-md-12';
//				$i ++;
			} else {
				$li_class = 'col-md-6';
			}

//			if ( $i % 2 == 1 ) {
//				$li_class .= ' pipes-left';
//			} else {
//				$li_class .= ' pipes-right';
//			}
			if ( $field->hidden ) {
				$li_class .= ' hidden';
			}
			$li[] = '<li class="' . $li_class . '"><div class="form-group">' . ( $field->hidden ? '' : $field->label ) . $field->input . '</div></li>';
		}

		$element .= '
			<div class="tab-pane active" id="' . $plugin_type[0] . '-basic">
				<ul class="unstyled config-option-list">
		';
		foreach ( $li AS $key => $el ) {
			$element .= $el;
		}
		$element .= '
				</ul>
			</div>
		';

		$li = array();
		// load advanced fieldset
		$element .= '
			<div class="tab-pane" id="' . $plugin_type[0] . '-advanced">
				<ul class="unstyled config-option-list">
		';
//		$i = 1;
		foreach ( $form->getFieldset( 'advanced' ) AS $field ) {
			if ( $field->class == 'fullwidth' ) {
				$li_class = 'col-md-12';
//				$i++;
			} else {
				$li_class = 'col-md-6';
			}
//			if ( $i % 2 == 1 ) {
//				$li_class .= ' pipes-left';
//			} else {
//				$li_class .= ' pipes-right';
//			}
//			$i ++;
			$li[] = '<li class="' . $li_class . '"><div class="form-group">' . ( $field->hidden ? '' : $field->label ) . $field->input . '</div></li>';
		}

		foreach ( $li as $key => $el ) {
			$element .= $el;
		}
		$element .= '
				</ul>
			</div>
		';

		// load help tab
		$element .= '
			<div class="tab-pane" id="' . $plugin_type[0] . '-help">
		';

		$help_file_path = OBGRAB_SITE . "/plugins/{$plugin_type[0]}s/{$file}/language/en-GB/en-GB.plg_wppipes-{$plugin_type[0]}_{$file}.html";

		if ( JFile::exists( $help_file_path ) ) {
			ob_start();
			include( $help_file_path );
			$element .= ob_get_contents();
			ob_end_clean();
		} else {
			$element .= '
				' . __( 'No guide available!' ) . '
			';
		}
		$element .= '
			</div>
		';

		//$li = str_replace('class="hasTooltip"','class="hasTip hasTooltip"', $li);
		$element = str_replace( '<label', '<label data-toggle="tooltip"', $element );
		$element = str_replace( 'title=', 'data-original-title=', $element );
		//$html = '<div class="ogb-params"><ul class="unstyled config-option-list">'.$li.'</ul><div class="clr"></div></div>';
		$html = '<div class="ogb-params"><div class="tab-content">' . $element . '</div><div class="clr"></div></div>';

		return $html;
	}

	public static function get_param_pipe( $item_id, $code ) {
		/*get params of processors  */
		global $wpdb;
		$qry   = "SELECT `params` FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`={$item_id} AND `code`='{$code}' ORDER BY `ordering`";
		$pipes = $wpdb->get_results( $qry );

		return $pipes;
	}

	public static function get_default_data( $type = '', $id ) {
		$id   = filter_input( INPUT_GET, 'id' );
		$path = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		if ( ! is_file( $path ) ) {
			return null;
		}
		$default = file_get_contents( $path );
		$default = unserialize( $default );
		if ( $type == '' ) {
			return $default;
		} else {
			return $default->$type;
		}
	}

	public static function empty_folder( $path ) {
		if ( substr( $path, 0, 1 ) == '/' ) {
			$path = substr( $path, 1 );
		}
		$url_path  = JURI::root() . $path;
		$url_path  = JPath::clean( $url_path );
		$to        = array( 'host' => str_replace( "\\", "/", $url_path ), 'path' => $path );
		$dest_path = isset ( $to['path'] ) ? JPATH_ROOT . DS . $to['path'] : '';

		$folders = JFolder::folders( $dest_path, '.', false, true, array(), array() );
		foreach ( $folders as $folder ) {
			if ( is_link( $folder ) ) {
				// Don't descend into linked directories, just delete the link.
				jimport( 'joomla.filesystem.file' );
				if ( JFile::delete( $folder ) !== true ) {
					// JFile::delete throws an error
					return false;
				}
			} elseif ( JFolder::delete( $folder ) !== true ) {
				// JFolder::delete throws an error
				return false;
			}
		}
	}


	public static function renderIWantBtn() {
		global $isJ25;
		$bar = JToolBar::getInstance( 'toolbar' );
		if ( $isJ25 ) {
			$label = '<span class="fa fa-heart-o fa-3x"></span>';
		} else {
			$label = '<span class="fa fa-heart-o"></span>';
		}
		$iwant_button = "
			<div id=\"foobla\">
				<a type=\"button\" class=\"btn btn-link btn-small dropdown-toggle\" onclick=\"display_form();\" style=\"text-decoration: none;\">
					{$label}
				</a>
				<div class=\"dropdown-iwant\" id=\"dropdown-iwant\" style=\"display:none;width:400px\">
					<a id=\"iwant-close\" class=\"btn btn-link btn-micro\" href=\"javascript:void()\" onclick=\"document.getElementById('dropdown-iwant').style.display='none'\"><i class=\"fa fa-times-circle\"></i></a>
					<h4 style=\"text-align: left;\">
						" . JText::_( 'COM_OBGRABBER_IWANT_INFO' ) . "
					</h4>
					<div class=\"form-group\">
						<textarea rows=\"5\" name=\"iwant\" id=\"iwantto\" class=\"input-block-level\"></textarea>
					</div>
					<p id=\"iwanto_thanks\" class=\"alert alert-info\" style=\"font-size:11px;text-align:left;margin-bottom:10px\">
					</p>
					<div class=\"form-group pull-right\">
						<a id=\"iw_btn\" class=\"button btn btn-primary\" href=\"#\">" . JText::_( 'COM_OBGRABBER_SEND' ) . "</a>
					</div>
				</div>
			</div>
		";
		$bar->appendButton( 'Custom', $iwant_button, 'iwant' );
	}

	public static function get_templates() {
		$upload_dir = wp_upload_dir();
		$path       = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates';
		$pipes      = array();
		if ( ! is_dir( $path ) ) {
			return $pipes;
		}
		$files = PIPES_Helper_FileSystem::files( $path );
		if ( ! is_array( $files ) ) {
			return $pipes;
		}

		foreach ( $files as $file ) {
			$item      = new stdClass();
			$extension = ogbFile::getExten( $file );
			if ( $extension == 'pipe' ) {
				$content        = file_get_contents( $path . DS . $file );
				$item           = json_decode( $content );
				$item->filename = $file;

				$pipes[] = $item;
			}
		}

		return $pipes;
	}
}

class ogbLib {
	public static function call_method( $className, $method, $args = array() ) {
		$res = call_user_func_array( array( $className, $method ), $args );

		return $res;
	}
}

//ogbFile::get_curl($url);
class ogbFile {
	public static function write( $path, $txt = '' ) {
		$path   = self::clean( $path );
		$folder = dirname( $path );
		if ( ! is_dir( $folder ) ) {
			ogbFolder::create( $folder );
		}
		$ret = is_int( file_put_contents( $path, $txt ) );

		return $ret;
	}

	public static function getExten( $file ) {
		$dot = strrpos( $file, '.' ) + 1;

		return substr( $file, $dot );
	}

	public static function read( $file ) {
		return file_get_contents( $file );
	}

	public static function get_content( $file ) {
		return file_get_contents( $file );
	}

	public static function clean( $path, $ds = DIRECTORY_SEPARATOR ) {
		if ( ! is_string( $path ) ) {
			throw new UnexpectedValueException( 'obFile::clean: $path is not a string.' );
		}

		$path = trim( $path );

		if ( empty( $path ) ) {
			$path = JPATH_ROOT;
		}
		// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
		// If dealing with a UNC path don't forget to prepend the path with a backslash.
		elseif ( ( $ds == '\\' ) && ( $path[0] == '\\' ) && ( $path[1] == '\\' ) ) {
			$path = "\\" . preg_replace( '#[/\\\\]+#', $ds, $path );
		} else {
			$path = preg_replace( '#[/\\\\]+#', $ds, $path );
		}

		return $path;
	}

	public static function get_curl( $url ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		if ( ini_get( 'open_basedir' ) != '' || ini_get( 'safe_mode' ) == 1 ) {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		} else {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		}

		ob_start();
		curl_exec( $ch );
		if ( isset( $_GET['x11'] ) ) {
			$info = curl_getinfo( $ch );
			echo '<br /><i><b>File:</b>' . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br /> \n";
			echo '<pre>';
			print_r( $info );
			echo '</pre>';
		}
		curl_close( $ch );

		return ob_get_clean();
	}

	/**
	 * function get_curl1
	 * var url link as http://foobla.com
	 * return html;
	 */
	public static function get_curl1( $url, $loop = 10 ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		if ( ini_get( 'open_basedir' ) != '' || ini_get( 'safe_mode' ) == 1 ) {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		} else {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		}
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
				return self::get_curl1( $url, $loop );
			}
			$content = "<p>[ http_code: {$response['http_code']} ][ redirect_url:{$url} ]</p>";

			return array( $response['http_code'], $content );
		}

		return array( $response['http_code'], $content );
	}

	/**
	 * Function get_curl2
	 * var url link as http://foobla.com
	 * @return mixed
	 */
	public static function get_curl2( $url, $useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1', $loop = 10, $javascript_loop = 0, $timeout = 5 ) {
		//$cookie = tempnam("/tmp", "CURLCOOKIE");
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_USERAGENT, $useragent );
		curl_setopt( $ch, CURLOPT_URL, $url );
		//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		if ( ini_get( 'open_basedir' ) != '' || ini_get( 'safe_mode' ) == 1 ) {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		} else {
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		}
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
					return self::get_curl2( $redirectaddr, $javascript_loop + 1 );
				}
			}
		}

		return array( $response['http_code'], $content );
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

	/**
	 * @param $url link as http://foobla.com
	 *
	 * @return html
	 */
	public static function get_curl3( $url ) {
		$header = array(
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36"
		);
		$ch     = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$html = curl_exec( $ch );
		curl_close( $ch );

		return array( 200, $html );
	}

    public static function get_curl5( $url, $custom_ck ) {
        $httpheader = array("Accept-Encoding: gzip,deflate,lzma,sdch" ,
            "Accept-Language: en-US,en;q=0.8" ,
            "User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.94 Safari/537.36 OPR/24.0.1558.53" ,
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" ,
            "Cache-Control: max-age=0" ,
            "Cookie: {$custom_ck}" ,
            "Connection: keep-alive");
        $options = array(
            CURLOPT_RETURNTRANSFER => false, // return web page
            CURLOPT_HEADER         => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => false, // follow redirects
            CURLOPT_ENCODING       => "utf-8", // handle all encodings
            CURLOPT_USERAGENT      => "spider", // who am i
            CURLOPT_AUTOREFERER    => false, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS      => 0, // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $httpheader
        );
        ob_start();
        $ch = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $html = ob_get_contents();
        ob_clean();

        return $html;
    }

	/**
	 * @param $url link as foobla.com
	 *
	 * @return html
	 */
	public static function get_curl4( $url ) {
		/*$link = "http://kha.wppipes.com/curl/?m=3&url=" . base64_encode( $url );
		$html = self::get_curl( $link );
		if ( isset( $_GET['php2'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'URL: ' . $link;
			echo 'http_code: ' . $html[0];
			echo $html[1];
			exit();
		}*/
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
		$html    = array();
		$html[0] = 200;
		$ch      = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$html[1] = curl_exec( $ch );
		/*$err     = curl_errno( $ch );
		$errmsg  = curl_error( $ch );
		$header  = curl_getinfo( $ch );*/
		curl_close( $ch );

		return $html;
	}

	public static function getHost() {
		global $ogb_host;
		if ( ! $ogb_host ) {
			$ogb_host = site_url();
		}

		return $ogb_host;
	}

	public static function getName( $file ) {
		// Convert back slashes to forward slashes
		$file  = str_replace( '\\', '/', $file );
		$slash = strrpos( $file, '/' );

		if ( $slash !== false ) {
			return substr( $file, $slash + 1 );
		} else {
			return $file;
		}
	}
}

class ogbFolder {
	public static function create( $path, $mode = 0755 ) {
		$path = ogbFile::clean( $path );
		if ( is_dir( $path ) ) {
			return true;
		}
		$i = 0;

		$parent = dirname( $path );
//		JFolder::create();
		if ( is_dir( $parent ) ) {
			// First set umask
			$origmask = @umask( 0 );
			// Create the path
			if ( ! $ret = @mkdir( $path, $mode ) ) {
				@umask( $origmask );

				return false;
			}
			// Reset umask
			@umask( $origmask );
		} else {
			self::create( $parent, $mode );
			self::create( $path, $mode );
		}
	}

	/*public static function files() {
		JFolder::files();
	}*/
}

class ogbDb {
	public static function query( $sql ) {
		global $wpdb;
		$wpdb->query( $sql );
	}
}

//echo obg_sbug(__FILE__,__LINE__,true);
function obg_sbug( $file, $line, $stime = false, $msg = '', $microtime = false ) {
	$smtime = $microtime ? '[' . microtime() . ']' : '';
	$time   = $stime ? "[" . date( 'Y-m-d H:i:s' ) . "]" : '';
	echo "\n\n<br />" . $time . $smtime . "<i>[ <b>File:</b>" . $file . ' ][ <b>Line:</b>' . $line . "]</i><br />\n\n";
	if ( $msg != '' ) {
		echo '<br />' . $msg . "<br />\n";
	}
}

function ogb_show( $text, $desc = '', $width = 900, $mheight = 600 ) {
	$style = "margin:5px auto;background:#f8f8f8;border: 2px solid #009900;max-height: {$mheight}px;overflow: auto;padding: 5px;width: {$width}px;";
	echo '<div style="' . $style . '">';
	echo "<b><i>{$desc}</i></b><hr />" . $text;
	echo '</div>';

}

function ogb_pr( $arr, $desc = '', $width = 1200, $mheight = 600 ) {
	$style = "background:#f8f8f8;border: 2px solid #009900;max-height: {$mheight}px;overflow: auto;padding: 5px;width: {$width}px;";
	echo '<pre style="' . $style . '">';
	echo $desc;
	print_r( $arr );
	echo '</pre>';
}
