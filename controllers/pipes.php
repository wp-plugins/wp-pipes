<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: pipes.php 141 2014-01-24 10:36:21Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

class PIPESControllerPipes extends Controller {

	public function __construct() {

	}

	function display( $cachable = false, $urlparams = false ) {
		return;
	}

	public function edit() {
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
		header( 'Location: ' . $url );
	}

	public function delete() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->delete( $id );
		}
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
		//$this->display();
	}

	public function copy() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->copy( $id );
		}
		PIPES::add_message( $res );

		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}

	public function publish() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->change_status( $id, 1 );
		}
		PIPES::add_message( $res );
		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
		//$this->display();
	}

	public function move_to_draft() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		if ( $id == '' ) {
			$res = "Please pick up at least 1 pipe first!";
		} else {
			$res = $mod->change_status( $id, 0 );
		}
		PIPES::add_message( $res );

		$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
		header( 'Location: ' . $url );
		exit();
	}

	public function update_meta() {
		if ( isset( $_POST['uid'] ) ) {
			$user  = $_POST['uid'];
			$value = $_POST['select'];
			update_user_meta( $user, 'pipes_help_box', $value );

			return 'Success!';
		}
	}

	public function export_to_share() {
		$mod = $this->getModel( 'pipes' );
		$id  = isset( $_GET['id'] ) ? $_GET['id'] : '';
		if ( $id == '' ) {
			PIPES::add_message( "Please pick up at least 1 pipe first!" );
			$url = remove_query_arg( array( 'id', 'action', 'action2' ), $_SERVER['HTTP_REFERER'] );
			header( 'Location: ' . $url );
			exit();
		}
		$set_template = isset( $_GET['set_template'] ) ? $_GET['set_template'] : 0;
		$res          = $mod->export_to_share( $id );
		//PIPES::add_message($res->msg);
		if ( count( $res->result ) == 1 ) {
			$file_name = sanitize_title( $res->result[0]->name ) . '.pipe';
		} else {
			$file_name = 'pipes-' . date( 'd-m-Y', time() ) . '.pipe';
		}
		$upload_dir = wp_upload_dir();
		if ( $set_template ) {
			$file_name = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name;
			if ( ! is_file( $file_name ) ) {
				ogbFolder::create( $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' );
			}
		}
		$fp = fopen( $file_name, 'w' );
		foreach ( $res->result as $result ) {
			fwrite( $fp, json_encode( $result ) . "\n" );
		}
//var_dump(filesize("$file_name"));die;
		fclose( $fp );
		if ( $set_template ) {
			PIPES::add_message( $res->msg );
			$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
			header( 'Location: ' . $url );
		}
		header( "Cache-Control: public" );
		header( "Content-Description: File Transfer" );
		header( "Content-Length: " . filesize( "$file_name" ) . ";" );
		header( "Content-Disposition: attachment; filename=$file_name" );
		header( "Content-Transfer-Encoding: binary" );

		readfile( $file_name );
		/*$url = remove_query_arg(array('id', 'task', 'action', 'action2'), $_SERVER['HTTP_REFERER']);
		header('Location: ' . $url);*/
		exit();
	}

	public function import_from_file() {
		$upload_dir = wp_upload_dir();
		$mod        = $this->getModel( 'pipes' );
		$id         = isset( $_GET['id'] ) ? $_GET['id'] : 0;
		$file_name  = isset( $_GET['file_name'] ) ? $_GET['file_name'] : '';
		if ( isset ( $_FILES["file_import"]["name"] ) ) {
			$filename = $_FILES["file_import"]["tmp_name"];
		} elseif ( isset( $_GET['url'] ) ) {
			$filename = $_GET['url'];
		} elseif ( is_file( $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name ) ) {
			$filename = $upload_dir['basedir'] . DS . 'wppipes' . DS . 'templates' . DS . $file_name;
		}
		$file_content = file_get_contents( $filename );
		$items        = explode( "\n", $file_content );
		$new_pipes    = array();
		if ( $file_content == '' ) {
			$new_pipes[] = "The file has not content!";
		}
		foreach ( $items as $value ) {
			if ( $value != '' ) {
				if ( substr( $value, 0, 1 ) == '{' ) {
					$item = json_decode( $value );
				} else {
					$item = json_decode( substr( $value, 3 ) );
				}
				if ( ! is_object( $item ) ) {
					$new_pipes[] = "There is something wrong with the structure of file's content!";
					continue;
				}
				$item->current_id = $id;
				$new_pipes[]      = $mod->import_from_file( $item );
			}
		}
		$message = implode( "</br>", $new_pipes );
		PIPES::add_message( $message );
		if ( isset( $_GET['url'] ) ) {
			$url = remove_query_arg( array( 'task', 'url' ), $_SERVER['HTTP_REFERER'] );
			header( 'Location: ' . $url );
			exit();
		} elseif ( $id > 0 ) {
			$url = admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipe&id=' . $id;
			header( 'Location: ' . $url );
			exit();
		}
	}
}