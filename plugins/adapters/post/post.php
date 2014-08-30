<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id: post.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright    (c) 2007-2013 wppipes.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// test address: http://localhost/joomla7/index.php?option=com_wppipes&task=runaddon&type=adapter&addon=k2
class WPPipesAdapter_post {
	/**
	 * Check if an item is duplicated
	 *
	 * @param array $fields
	 *
	 * @return bool|int
	 */
	public static function checkDuplicate( $fields = array() ) {
		global $wpdb;
		if ( ! count( $fields ) ) {
			return false;
		}

		$res = 0;
		$qry = "SELECT `ID` FROM " . $wpdb->prefix . "posts WHERE `post_name`='" . addslashes( $fields['title'] ) . "' AND `post_type` = 'post'";
		$res = (int) $wpdb->get_var( $qry );

		return $res;
	}

	/**
	 * Logging
	 *
	 * @param $id
	 * @param $action
	 * @param $msg
	 *
	 * @return stdclass
	 */
	public static function makeLog( $id, $action, $msg ) {
		$res         = new stdclass();
		$res->name   = 'Post';
		$res->action = $action;
		$res->msg    = $msg;
		$res->id     = $id;

		if ( (int)$id > 0 ) {
			$res->viewLink = '?p=' . $id;
			$res->editLink = 'post.php?post=' . $id . '&action=edit';
		} else {
			$res->viewLink = '';
			$res->editLink = '';
		}

		return $res;
	}

	/**
	 * Storing item
	 *
	 * @param $data
	 * @param $params
	 *
	 * @return stdclass
	 */
	static function store( $data, $params ) {

		if ( isset( $_GET['a'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_show( $data->excerpt, 'Excerpt: ' );
			ogb_show( $data->content, 'Content: ' );
		}
		if ( isset( $_GET['a1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_pr( $params, 'Params: ' );
			ogb_pr( $data, 'Data: ' );
		}
		if ( '' == $data->slug ) {
			$data->slug = sanitize_title( $data->title );
		}
		$dup_id = self::checkDuplicate( array( 'title' => $data->slug ) );
		$action = '';
		$msg    = '';
		if ( $dup_id > 0 ) {
			if ( isset( $_GET['u'] ) ) {
				$action = 'Update';
				$msg    = 'Update - id:' . $dup_id;
			} else {
				$res = self::makeLog( $dup_id, 'Ignore', 'Duplicate - id:' . $dup_id );

				return $res;
			}
		}
		$id   = $dup_id > 0 ? $dup_id : 0;
		$save = self::storeContent( $data, $params, $id );
		$id   = $save->id;

		if ( isset( $_GET['a1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			echo 'Saved result: ';
			print_r( $save );
			echo '</pre>';
		}

		if ( (int)$id > 0 ) {
			if ( $dup_id < 1 ) {
				$action = 'Saved';
				$msg    = 'Saved - id:' . $id;
			}
		} else {
			$action = 'Saved Error';
			$msg    = $save->msg;
		}

		$res = self::makeLog( $id, $action, $msg );

		return $res;
	}

	/**
	 * @param     $data
	 * @param     $params
	 * @param int $uid
	 *
	 * @return stdClass
	 */
	public static function storeContent( $data, $params, $uid = 0 ) {
		$res      = new stdClass();
		$res->id  = $uid;
		$res->msg = '';

		if ( ! isset( $data->date ) || $data->date == '' ) {
			$lastDay = time() - 3600 * 24;
			$created = date( 'Y-m-d H:i:s', $lastDay );
		} else {
			$created = $data->date;
		}
		$metakey  = isset( $data->metakey ) ? $data->metakey : '';
		if ( ! is_array( $data->images ) && $data->images != '' ) {
			$images  = self::get_img_from_html( $data->images );
			$matches = array();
			preg_match_all( '/src="(.+?)"/i', $images, $matches );
			$img_url = $matches[1][0];
		} elseif ( isset($data->images[0]->src) && $data->images[0]->src != '' ) {
			$img_url = $data->images[0]->src;
		}

		$post = array();
		if ( $uid > 0 ) {
			$post['ID'] = $uid;
		}

		$post['post_title'] = wp_strip_all_tags( $data->title );
		if ( '' != $data->slug ) {
			$post['post_name'] = $data->slug;
		} else {
			$post['post_name'] = sanitize_title( $post['post_title'] );
		}
		$post['post_excerpt'] = $data->excerpt;
		$post['post_content'] = $data->content;

		$post['post_status']   = $params->public;
		$post_cate             = is_array( $params->category ) ? $params->category : array( $params->category );
		$post['post_category'] = $post_cate;
		$post['post_date']     = $created;
		$post['post_date_gmt'] = $created;

		$post['post_author'] = $params->author;

		$post['post_type'] = 'post';

		$post['tags_input'] = $metakey;
		$custom_fields      = self::get_all_post_custom();
		$post_id            = wp_insert_post( $post, true );
		foreach ( $custom_fields as $cf ) {
			$convert_cf = str_replace('-', '__', $cf);
			if ( isset( $data->$convert_cf ) ) {
				update_post_meta( $post_id, $cf, $data->$convert_cf );
			}
		}
		if ( isset( $img_url ) && '' != $img_url ) {
			self::set_feature_image( $img_url, $post_id );
		}

		$res->id  = $post_id;
		$res->msg = 'Success';

		return $res;
	}

	public static function set_feature_image( $image_url, $post_id ) {
		$upload_dir = wp_upload_dir(); // Set upload folder
		$image_data = @file_get_contents( $image_url, true ); // Get image data

		$filename = basename( $image_url ); // Create image file name
		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		if ( false === $image_data ) {
			echo '<pre>';
			print_r( 'invalid url of image, could not get image from ' );
		} else {
			file_put_contents( $file, $image_data );
		}
		$wp_filetype = wp_check_filetype( $filename, null );
		$attachment  = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		set_post_thumbnail( $post_id, $attach_id );

	}

	/**
	 * @param bool $param
	 *
	 * @return stdClass
	 */
	public static function getDataFields( $param = false ) {
		if(isset($_GET['arg2']) && $_GET['arg2'] == 1){
			$custom_fields = self::get_all_post_custom();
			$custom_fields = str_replace('-', '__', $custom_fields);
		}else{
			$custom_fields = array();
		}
		$data          = new stdClass();
		$inputs        = 'title,slug,excerpt,content,date,images,metakey';
		$data->input   = explode( ',', $inputs );
		$data->input   = array_unique( array_merge ( $data->input, $custom_fields ) );

		return $data;
	}

	public static function get_all_post_custom() {
		global $wpdb;
		$sql          = "SELECT `meta_key` FROM `{$wpdb->prefix}postmeta` GROUP BY `meta_key`";
		$customfields = $wpdb->get_results( $sql, ARRAY_A );
		$cust_f       = array();
		foreach ( $customfields as $cf ) {
			$meta_key = $cf['meta_key'];
			if ( substr( $meta_key, 0, 1 ) != '_' ) {
				$cust_f[] = $meta_key;
			}
		}

		return $cust_f;
	}

	public static function get_img_from_html( $contents ) {
		$matches = array();
		preg_match_all( "#<img*[^\>]*>#i", $contents, $matches );
		if ( ! isset( $matches[0][0] ) ) {
			return $contents;
		}

		return $matches[0][0];
	}

}