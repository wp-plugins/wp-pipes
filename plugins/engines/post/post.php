<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: post.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    2014 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WPPipesEngine_post {
	public static function getData( $params ) {
		if ( isset( $_GET['e'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_pr( $params, 'Params: ' );
		}
		$limit = 0;
		$data = self::getItemsPost( $params );
		$datas = array();
		foreach ( $data as $key=>$value ) {
			if($limit >= (int)$params->limit_items){
				break;
			}
			if (has_post_thumbnail( $value->ID ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $value->ID ), 'single-post-thumbnail' );
			}
			$data[$key]->featured_image = @$image[0];
			$data[$key]->author_name = get_the_author_meta( 'display_name' , $value->post_author );
			$data[$key]->link = $value->guid;
			$datas[] = $data[$key];
			$limit++;
		}
		if ( isset( $_GET['e1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo 'Total: ' . count( $datas );
			ogb_pr( $datas, 'Data: ' );
		}//var_dump($data);die;

		return $datas;
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
}