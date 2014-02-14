<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: post.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

define( 'OGRAB_ECACHE', OGRAB_CACHE . 'ecache' . DS );

class WPPipesEngine_post {
	public static function getData( $params ) {
		if ( isset( $_GET['e'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_pr( $params, 'Params: ' );
		}
		$data = self::getItemsPost( $params );
		foreach ( $data as $key=>$value ) {
			if (has_post_thumbnail( $value->ID ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $value->ID ), 'single-post-thumbnail' );
			}
			$data[$key]->featured_image = @$image[0];
			$data[$key]->author_name = get_the_author_meta( 'display_name' , $value->post_author );
			$data[$key]->link = $value->guid;
		}
		if ( isset( $_GET['e1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo 'Total: ' . count( $data );
			ogb_pr( $data, 'Data: ' );
		}//var_dump($data);die;

		return $data;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->output = array( 'post_title', 'post_name', 'post_content', 'link', 'author_name', 'post_date', 'featured_image' );

		return $data;
	}


	public static function getItemsPost( $params ) {
		global $wpdb;
		$just_seven = new WP_Query(
			array(
				'category__in' => $params->categories,
				'author' => $params->author
			)
		);
		//$tl = count( $rows );
//		echo "\n<p>URL: " . '<a href="' . $url . '" target="_blank">' . $url . '</a>';
//		echo "\n<br />[ Found: " . $c_items . ' ][ +' . $tl . ' ][ ' . $obtl . '/' . $limit . ' ]' . "</p>\n";

		return $just_seven->posts;
	}

	public static function fix_date( $time, $fix_time = -86400 ) {
		$time = (int) strtotime( $time ) + $fix_time;

		return date( 'Y-m-d H:i:s', $time );
		//3600.24	= 86400
	}
	
	public static function update_cache( $path, $rows ) {
		$data  = serialize( $rows );
		$cache = self::get_cache( $path );
		$a     = ogbFile::write( $path, $cache );

		return $a;
	}

	public static function get_cache( $path ) {
		$rows = file_get_contents( $path );
		$rows = unserialize( $rows );

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
}