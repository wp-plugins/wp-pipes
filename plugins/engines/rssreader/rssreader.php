<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: rssreader.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
# Call SimplePie library
require_once dirname( __FILE__ ) . DS . 'helpers' . DS . 'autoloader.php';

class WPPipesEngine_rssreader {
	public static function getData( $params ) {
		if ( isset( $_GET['e'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_pr( $params, 'Params: ' );
		}
		$urls  = explode( "\nhttp", $params->feed_url );
		$limit = (int) $params->limit_items;
		$a     = 0;
		$data  = array();
		for ( $i = 0; $i < count( $urls ); $i ++ ) {
			$url = trim( $urls[$i] );
			if ( $i > 0 ) {
				$url = 'http' . $url;
			}
			$items = self::getItemsFeed( $url, $limit, $params );
			if ( count( $items ) > 0 ) {
				$data = array_merge( $items, $data );
			}
		}
		if ( isset( $_GET['e1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo 'Total: ' . count( $data );
			ogb_pr( $data, 'Data: ' );
		}

		return $data;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->output = array( 'title', 'link', 'description', 'author', 'date', 'enclosures' );
		$id           = filter_input( INPUT_GET, 'id' );
		$path         = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		if ( ! is_file( $path ) ) {
			return $data;
		}
		$default = file_get_contents( $path );
		$default = unserialize( $default );

		$default_oe = $default->so;
		foreach ( $data->output as $key => $value ) {
			if ( is_array( $default_oe->$value ) ) {
				$data->output[$key] = $value . '<br /><p class="text-muted small">Array</p>';
			} else {
				$default_oe->$value = str_replace( "'", "", $default_oe->$value );
				$default_oe->$value = str_replace( '"', '', $default_oe->$value );
				$data->output[$key] = $value . '<br /><p data-toggle="tooltip" data-original-title="' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) : 'null' ) . '" class="text-muted small">' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) . '</p>' : 'null</p>' );
			}
		}

		return $data;
	}

	//--- Begin Get feed item ---
	public static function update_cache( $path, $rows ) {
		$data = serialize( $rows );
		//$cache = self::get_cache( $path );
		$a = ogbFile::write( $path, $data );

		return $a;
	}

	public static function get_cache( $path ) {
		if ( ! is_file( $path ) ) {
			return array();
		}
		$cache_conten = file_get_contents( $path );
		$rows         = unserialize( $cache_conten );

		return $rows;
	}

	public static function getPath( $url ) {
		return OGRAB_ECACHE . md5( $url );
	}

	public static function need_update( $path ) {
		if ( isset( $_GET['u'] ) ) {
			return true;
		}
		if ( ! is_file( $path ) ) {
			return true;
		}
		$cache_mtime = filemtime( $path );
		$diff        = time() - $cache_mtime;
		if ( isset( $_GET['x'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'last Update  - s: ';
			var_dump( $diff );
			if ( $diff > 600 ) {
				$a = $diff / 60;
				echo ' - m: ';
				var_dump( $a );
			}
			if ( $a > 60 ) {
				$a = $diff / 60;
				echo ' - h: ';
				var_dump( $a );
			}
		}
		$time = 3600;

		return $diff > $time;
	}

	public static function get_items_feed( $url, $params ) {
		$cache_path = self::getPath( $url );
		if ( ! self::need_update( $cache_path ) ) {
			$rows = self::get_cache( $cache_path );

			return $rows;
		}
//		jimport('simplepie.simplepie');
		$feed = new SimplePie();
		$mode = isset( $params->mode ) ? $params->mode : 0;
		if ( $mode == 0 ) {
			$feed->set_feed_url( $url );
		} else {
			$html = ogbFile::get_curl( $url );
			$feed->set_raw_data( $html );
		}
		$feed->set_autodiscovery_level( SIMPLEPIE_LOCATOR_NONE );
		$feed->set_timeout( 20 );
		$feed->enable_cache( false );
		$feed->set_stupidly_fast( true );
		$feed->enable_order_by_date( $params->order_by_date ); // we don't want to do anything to the feed
		$feed->set_url_replacements( array() );
		$result = $feed->init();
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo "<p>URL: [{$url}]</p>";
			echo "<p>Error: [{$feed->error}]</p>";
		}
		$items   = $feed->get_items();
		$c_items = count( $items );
		if ( $c_items == 0 ) {
			echo "<p>Error: [{$feed->error}]</p>";

			return array();
		}

		for ( $i = 0; $i < count( $items ); $i ++ ) {
			$row              = new stdclass();
			$row->title       = html_entity_decode( $items[$i]->get_title(), ENT_QUOTES, 'UTF-8' ); # the title for the post
			$row->link        = $items[$i]->get_link(); # a single link for the post
			$row->description = $items[$i]->get_description(); # the content of the post (prefers summaries)
			$row->author      = $items[$i]->get_author(); # a single author for the post
			$row->date        = $items[$i]->get_date( 'Y-m-d H:i:s' );
			$row->enclosures  = $items[$i]->get_enclosures();
			$rows[]           = $row;
		}
//		var_dump($rows);
		if ( $params->order_by_date && $params->order_by_date_follow ) {
			$newrows = array();
			if ( count( array_filter( $rows ) ) > 0 ) {
				for ( $i = count( $rows ) - 1; $i >= 0;$i -- ) {
					$newrows[] = $rows[$i];
				}
				$rows = $newrows;
			}
		}
		self::update_cache( $cache_path, $rows );

		return $rows;
	}

	//--- End Get feed item ---

	public static function getItemsFeed( $url, $limit = 5, $params ) {
		global $obtl;
		$obtl     = (int) $obtl;
		$url      = trim( $url );
		$items    = self::get_items_feed( $url, $params );
		$c_items  = count( $items );
		$fix_time = isset( $params->fix_time ) ? (int) $params->fix_time : - 24;
		$fix_time = $fix_time * 3600;
		$rows     = array();
		for ( $i = 0; $i < $c_items; $i ++ ) {
			if ( $obtl >= $limit ) {
				break;
			}
			$row           = $items[$i];
			$row->date     = self::fix_date( $row->date, $fix_time );
			$row->src_url  = $row->link;
			$row->title    = trim( $row->title );
			$row->src_name = $row->title != '' ? $row->title : $row->link;
			if ( strlen( $row->src_name ) > 50 ) {
				$row->src_name = substr( $row->src_name, 0, 50 ) . ' ...';
			}
			$row->title = html_entity_decode( $row->title );
			$rows[]     = $row;
			$obtl ++;
		}
		$tl = count( $rows );
		echo "\n<p>URL: " . '<a href="' . $url . '" target="_blank">' . $url . '</a>';
		echo "\n<br />[ Found: " . $c_items . ' ][ +' . $tl . ' ][ ' . $obtl . '/' . $limit . ' ]' . "</p>\n";

		return $rows;
	}

	public static function fix_date( $time, $fix_time = -86400 ) {
		$time = (int) strtotime( $time ) + $fix_time;

		return date( 'Y-m-d H:i:s', $time );
		//3600.24	= 86400
	}

	public static function get_default_item() {
		$id            = filter_input( INPUT_POST, 'id' );
		$value_default = filter_input( INPUT_POST, 'val_default' );
		if ( $value_default == '' ) {
			return 'Do nothing!';
		}
		$feed = new SimplePie();
		$path = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		$feed->set_feed_url( $value_default );
		$feed->set_autodiscovery_level( SIMPLEPIE_LOCATOR_NONE );
		$feed->set_timeout( 20 );
		$feed->enable_cache( false );
		$feed->set_stupidly_fast( true );
		$feed->enable_order_by_date( false ); // we don't want to do anything to the feed
		$feed->set_url_replacements( array() );
		$result = $feed->init();
		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo "<p>URL: [{$value_default}]</p>";
			echo "<p>Error: [{$feed->error}]</p>";
		}
		$items   = $feed->get_items();
		$c_items = count( $items );
		if ( $c_items == 0 ) {
			echo "<p>Error: [{$feed->error}]</p>";

			return array();
		}
		$row              = new stdclass();
		$row->title       = html_entity_decode( $items[0]->get_title(), ENT_QUOTES, 'UTF-8' ); # the title for the post
		$row->link        = $items[0]->get_link(); # a single link for the post
		$row->description = $items[0]->get_description(); # the content of the post (prefers summaries)
		$row->author      = $items[0]->get_author(); # a single author for the post
		$row->date        = $items[0]->get_date( 'Y-m-d H:i:s' );
		$row->enclosures  = $items[0]->get_enclosures();
		if ( ! is_file( $path ) ) {
			$source = new stdClass();
		} else {
			$source = ogb_common::get_default_data( '', $id );
		}
		$source->so = $row;
		$cache      = serialize( $source );

		if ( isset( $_GET['x2'] ) ) {
			//echo "\n\n<br /><i><b>File:</b>".__FILE__.' <b>Line:</b>'.__LINE__."</i><br />\n\n";
			echo '<br>Path: ' . $path;
		}
		ogbFile::write( $path, $cache );
		exit();
	}
}