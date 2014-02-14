<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesPro_cut_introtext {
	public static function process( $data, $params ) {
		if ( isset( $_GET['p'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r( $params );
			echo 'Data: ';
			print_r( $data );
			echo '</pre>';
		}
		$html = $data->html;
		if ( isset( $_GET['pci'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo $html;
			exit();
		}
		$finish_sentence = isset( $params->finish_sentence ) ? $params->finish_sentence : 0;
		//$data->html	= self::clear_space($data->html);
		$data->html = str_replace( '&nbsp;', '', $data->html );
		$res        = self::cutHTML2( $data->html, $params->words, $finish_sentence );
		//$res->fulltext	= str_replace('<p/>','',$res->fulltext);
		$res->introtext = self::fix_iframe( $res->introtext );
		$res->fulltext  = self::fix_iframe( $res->fulltext );
		$res->fulltext  = self::remove_err_tags( $res->fulltext );
		if ( isset( $_GET['pci1'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'introtext:<br />' . $res->introtext . '<hr />';
			echo 'fulltext:<br />' . $res->fulltext;
			exit();
		}

		return $res;
	}

	public static function remove_err_tags( $html, $tags = array( 'div', 'p' ) ) {
		for ( $i = 0; $i < count( $tags ); $i ++ ) {
			$html = self::remove_err_tag( $html, $tags[$i] );
		}

		return $html;
	}

	public static function remove_err_tag( $html, $tag ) {
		$regex = '/<' . $tag . '[^<]*\/>/';
		preg_match_all( $regex, $html, $tags );
		if ( ! isset( $tags[0][0] ) ) {
			return $html;
		}
		foreach ( $tags[0] as $tag ) {
			$html = str_replace( $tag, '', $html );
		}

		return $html;
	}

	public static function clear_space( $html ) {
		$html = mb_convert_encoding( $html, 'HTML-ENTITIES', "UTF-8" );
		$html = str_replace( "\n", '', $html );
		$html = preg_replace( "/\s+/", " ", $html );
		$html = str_replace( "<div", "\n<div", $html );
		$html = str_replace( "<p", "\n<p", $html );
		$html = str_replace( "<br", "\n<br", $html );
		$html = str_replace( "<h", "\n<h", $html );

		return $html;
	}

	//$res->introtext	= self::fix_iframe($res->introtext);
	public static function fix_iframe( $html ) {
		$regex = '/<iframe [^<]*\/>/';
		preg_match_all( $regex, $html, $iframes );
		if ( ! isset( $iframes[0][0] ) ) {
			return $html;
		}
		foreach ( $iframes[0] as $iframe ) {
			$a    = str_replace( '/>', '></iframe>', $iframe );
			$html = str_replace( $iframe, $a, $html );
		}

		return $html;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->input  = array( 'html' );
		$data->output = array( 'introtext', 'fulltext' );

		return $data;
	}

	public static function isEndCentence( $symbol ) {
		if ( preg_match( '/[a-zA-Z0-9]/', $symbol ) ) {
			return false;
		}

		return true;
	}

	public static function cutHTML2( $text, $max_length = 0, $finish_sentence = 0 ) {
		$max_length = (int) $max_length;
		//$text = mb_convert_encoding($text,'HTML-ENTITIES', "UTF-8");
		if ( ! $text || $max_length < 1 ) {
			$obj            = new stdClass();
			$obj->introtext = $text;
			$obj->fulltext  = null;

			return $obj;
		}
		$tags        = array();
		$result      = "";
		$break_lines = array(
			'p',
			'br',
			'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5'
		);
		$is_open     = false;
		$grab_open   = false;
		$is_close    = false;
		$tag         = "";
		$pos         = 0;
		$i           = 0;
		$stripped    = 0;

		$stripped_text = strip_tags( $text );
		$end_sentence  = false;
		while ( $i < strlen( $text ) ) {
			$symbol = $text{$i};
			$result .= $symbol;
			$pos ++;

			if ( $stripped > $max_length ) {
				if ( $finish_sentence == 0 && in_array( $symbol, array( ' ' ) ) ) {
					break;
				} elseif ( in_array( $symbol, array( '.', "\n" ) ) && self::isEndCentence( $text{$i + 1} ) ) {
					break;
				}
			}

			switch ( $symbol ) {
				case '<' :
					$is_open   = true;
					$grab_open = true;
					break;
				case '/' :
					if ( $is_open ) {
						$is_close  = true;
						$is_open   = false;
						$grab_open = false;
					}
					break;
				case "\n" :
				case ' ' :
					if ( $is_open ) {
						$grab_open = false;
					} else {
						$stripped ++;
					}
					break;
				case '>' :
					if ( $is_open ) {
						array_push( $tags, $tag );
						$is_open   = false;
						$grab_open = false;
						$tag       = "";
					} elseif ( $is_close ) {
						array_pop( $tags );
						$is_close = false;
						$tag      = "";
					}
					break;
				default :
					if ( $grab_open || $is_close ) {
						$tag .= $symbol;
					}
					if ( ! $is_open && ! $is_close ) {
						if ( $symbol == ' ' ) {
							$stripped ++;
						}
					}
			}
			$i ++;
		}
		$tag      = '';
		$symbol   = '';
		$is_close = 0;
		$is_open  = 0;
		$tmp      = 0;
		$intro    = substr( $text, 0, $pos );

		$obj = new stdClass();
		/*if (strlen(strip_tags($intro)) > strlen(strip_tags($text)) / 2) {
			$obj->introtext = $text;
			$obj->fulltext = '';
			return $obj;
		}*/

		$dom = new DOMDocument();
		@ $dom->loadHTML( mb_convert_encoding( $intro, 'HTML-ENTITIES', "UTF-8" ) );
		$xpath = new DOMXPath( $dom );
		$xpath = $xpath->query( '/html/body' )->item( 0 );
		$intro = '';

		if ( $xpath ) {
			$body = self::cleanEmptyLink( $xpath );
			$body = $body->childNodes;
			for ( $i = $body->length - 1; $i >= 0; $i -- ) {
				$intro = ( $dom->saveXML( $body->item( $i ) ) ) . $intro;
			}
		}
		$fulltext = substr( $text, $pos );
		if ( isset( $_GET['pci2'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo '<br />max_length: ';;
			var_dump( $max_length );
			$b = strip_tags( $text );
			$d = strip_tags( $intro );
			$f = explode( ' ', $d );
			$e = strlen( $d );
			$a = strlen( $b );
			$c = strlen( $text );

			echo "\n<br />leng full: ";
			var_dump( $c );
			echo "\n<br />leng strip: ";
			var_dump( $a );
			echo "\n<br />pos cut: ";
			var_dump( $pos );
			echo "\n<br />leng intro: ";
			var_dump( $e );
			echo "\n<hr />Text strip: <br />" . $b;
			echo "\n<hr />Text result: <br />" . $result;
			echo "\n<hr />Text full: <br />" . $text;
			echo "\n<hr />intro: <br />" . $intro;
			echo "\n<hr />fulltext: <br />" . $fulltext;

			echo "\n<hr />intro strip: <br />" . $d;
			echo "\n<hr /><pre>Chars intro:";
			print_r( $f );
			echo '</pre>';
			exit();
		}
		$open_tags = array();
		preg_match_all( '#(</[a-zA-Z0-9]+>)#', $fulltext, $matches );
		if ( isset ( $matches[0][0] ) ) {
			$start       = 0;
			$single_tags = array();
			foreach ( $matches[0] as $tag ) {
				$pos_end = strpos( $fulltext, $tag );
				if ( strpos( substr( $fulltext, 0, $pos_end ), str_replace( array(
						"/",
						">"
					), '', $tag ), 0 ) === false
				) {
					$tttt          = $start;
					$start         = $pos_end + strlen( $tag );
					$single_tags[] = str_replace( array(
						"/"
					), '', $tag );
				}
			}
			$first = substr( $fulltext, 0, $start );
			preg_match_all( '#(</[a-zA-Z0-9]+>)#', $first, $matches );
			if ( isset ( $matches[0] ) ) {
				$matches = array_reverse( $matches[0] );
				foreach ( $matches as $key => $val ) {
					$open_tag = str_replace( array( "/", ">" ), "", $val );
					if ( strpos( $first, $open_tag ) === false ) {
						$open_tags[] = $open_tag . ">";
					}
				}
			}
		}
		if ( count( $open_tags ) ) {
			$fulltext = implode( "", ( $open_tags ) ) . $fulltext;
		}

		@ $dom->loadHTML( mb_convert_encoding( $fulltext, 'HTML-ENTITIES', "UTF-8" ) );
		$xpath    = new DOMXPath( $dom );
		$xpath    = $xpath->query( '/html/body' )->item( 0 );
		$fulltext = '';
		if ( $xpath ) {
			$body = self::cleanEmptyLink( $xpath );
			$body = $body->childNodes;
			for ( $i = $body->length - 1; $i >= 0; $i -- ) {
				$fulltext = ( $dom->saveXML( $body->item( $i ) ) ) . $fulltext;
			}
		}
		$obj->introtext = $intro;
		$obj->fulltext  = $fulltext;

		return $obj;
	}

	public static function cleanEmptyLink( $node ) {
		//return $node;
		$hrefs = $node->getElementsByTagName( 'a' );
		$len   = $hrefs->length;
		for ( $i = $len - 1; $i >= 0; $i -- ) {
			$elems = $hrefs->item( $i )->getElementsByTagName( "*" );
			if ( $elems->length == 0 && $hrefs->item( $i )->nodeValue == "" ) {
				$hrefs->item( $i )->parentNode->removeChild( $hrefs->item( $i ) );
				//echo "=0";
			} else {
				if ( $elems->length != 0 ) {

					if ( $elems->item( 0 )->nodeName == '#text' && trim( $elems->item( 0 )->textContent, " " ) == "" ) {
						$hrefs->item( $i )->parentNode->removeChild( $hrefs->item( $i ) );
					}
					//echo "=1";echo ",name=".$elems->item(0)->nodeName,",[",$elems->item(0)->textContent,"]";
				}
			}
			//echo "a";
		}

		return $node;
	}
}