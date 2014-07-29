<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: item.php 156 2014-01-25 08:55:27Z tung $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
require_once dirname( dirname( __FILE__ ) ) . DS . 'helpers' . DS . 'plugins.php';

// require_once '';
class PIPESModelPipe extends Model {

	public function getTable() {
		require_once dirname( dirname( __FILE__ ) ) . DS . 'tables' . DS . 'pipes.php';
		$itemsListTable = new Lo_Items_List_Table();
		//Fetch, prepare, sort, and filter our data...
		$itemsListTable->prepare_items();

		return $itemsListTable;
	}

	public function getItem() {
		global $wpdb;

		$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
		if ( $id ) {
			$sql  = 'SELECT * FROM `' . $wpdb->prefix . 'wppipes_items` WHERE `id`=' . $id;
			$item = $wpdb->get_row( $sql );
		} else {
			$item              = new stdClass();
			$item->id          = '';
			$item->title       = '';
			$item->description = '';
			$item->published   = 0;
			$item->ordering    = 0;
			$item->created     = '';
			$item->modified    = '';
			$item->params      = '';
		}

		return $item;
	}

	## Empty data variabele
	var $_data = null;
	var $_item = null;
	var $_total = null;
	var $_pagination = null;

	function __construct() {
//		parent::__construct();
		$this->id = filter_input( INPUT_GET, 'cid' );
		if ( ! $this->id ) {
			$this->id = 0;
		}
	}

	function copyItem( $id ) {
		global $wpdb;
		$error = false;
		$qry   = "SELECT * FROM `{$wpdb->prefix}wppipes_items` WHERE `id`={$id}";
		$db->setQuery( $qry );
		$item = $db->loadObject();

		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $item );
			echo '</pre>';
		}

		$item->name .= ' ( copy )';
		unset( $item->id );
		$row = JTable::getInstance( 'Pipes', 'wppipesTable' );
		if ( ! $row->bind( $item ) ) {
			echo $row->getError();
		}
		if ( ! $row->store() ) {
			echo $row->getError();
		}
		$copy_id = $row->id;

		$qry = "SELECT * FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`={$id} ORDER BY `ordering` ASC";
		$db->setQuery( $qry );
		$pipes = $db->loadObjectList();

		echo '<br />count($pipes): ' . count( $pipes ) . '<br /><br />QRY: ' . $qry;

		//`id`,`code`,`name`,`item_id`,`input`,`params`,`output`,`ordering`
		for ( $i = 0; $i < count( $pipes ); $i ++ ) {
			$item = $pipes[$i];
			$qry  = "INSERT INTO `{$wpdb->prefix}wppipes_pipes` (`id`,`code`,`name`,`item_id`,`params`,`ordering`)";
			$qry .= "\n VALUES (NULL, '{$item->code}', '{$item->name}', {$copy_id}, '" . addslashes( $item->params ) . "', '{$item->ordering}')";

			$db->setQuery( $qry );
			if ( ! $db->query() ) {
				echo $db->getQuery();
				echo '<br />Error: ' . $db->getErrorMsg();
				$error = true;
			} elseif ( isset( $_GET['x'] ) ) {
				echo '<br /><br />' . $qry;
			}
		}
		if ( $error || isset( $_GET['x'] ) ) {
			exit();
		}

		return $copy_id;
	}

	function reOrderPipe( $id ) {
		global $wpdb;
		$qry   = "SELECT `id`,`ordering` FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`={$id} ORDER BY `ordering` ASC,`id` DESC";
		$pipes = $wpdb->get_results( $qry, OBJECT );
		for ( $i = 0; $i < count( $pipes ); $i ++ ) {
			$pipe = $pipes[$i];
			if ( $pipe->ordering != $i ) {
				$qry = "UPDATE `{$wpdb->prefix}wppipes_pipes` SET `ordering` = {$i} WHERE `id` ={$pipe->id}";
				if ( ! $wpdb->query( $qry ) ) {
					$wpdb->print_error();
				}
			}
		}
	}

	function removePipe( $pid, $itid ) {
		global $wpdb;
		$sql = "SELECT `ordering`, `item_id` FROM `{$wpdb->prefix}wppipes_pipes` WHERE `id` = {$pid}";

		$obj = $wpdb->get_results( $sql );
		$this->remove_io( $obj[0] );
		$qry = "DELETE FROM `{$wpdb->prefix}wppipes_pipes` WHERE `id` = {$pid}";
		if ( ! $wpdb->query( $qry ) ) {
			$msg = $wpdb->last_error;
		} else {
			$this->reOrderPipe( $itid );
			$msg = "Remove success pipe[{$pid}]";
		}

		return $msg;
	}

	function remove_io( $obj ) {
		global $wpdb;
		$sql = "SELECT `inputs`, `outputs` FROM `{$wpdb->prefix}wppipes_items` WHERE `id` = {$obj->item_id}";

		$io_obj = $wpdb->get_results( $sql );
		$inputs = json_decode( $io_obj[0]->inputs );
		unset( $inputs->ip[$obj->ordering] );
		if ( count( $inputs->ip ) > 0 ) {
			$inputs->ip = array_values( $inputs->ip );
		}
		$new_ip = json_encode( $inputs );

		$outputs = json_decode( $io_obj[0]->outputs );
		unset( $outputs->op[$obj->ordering] );
		if ( count( $outputs->op ) > 0 ) {
			$outputs->op = array_values( $outputs->op );
		}
		$new_op = json_encode( $outputs );
		$sql_up = "UPDATE `{$wpdb->prefix}wppipes_items` SET `inputs` = '{$new_ip}', `outputs` = '{$new_op}' WHERE `id` = {$obj->item_id}";

		$wpdb->query( $sql_up );
		if ( ! $wpdb->query( $sql_up ) ) {
			$error = $wpdb->last_error;

			return $error;
		}
	}

	function addProcess( $code, $id, $ordering ) {
		global $wpdb;
		$res        = new stdClass();
		$res->error = '';
		$qry        = "SELECT `name`,`params` FROM `{$wpdb->prefix}extensions` WHERE `folder`= 'wppipes-processor' AND `element`='{$code}'";

		$processor = PIPES_Helper_Plugins::getPlugins( true );
		if ( isset( $processor[$code] ) ) {
			$pipe         = new stdClass();
			$pipe->name   = $processor[$code]['name'];
			$pipe->params = '';
		}
		if ( ! isset( $pipe ) || ! $pipe ) {
			$res->error = "Processor not found: {$code}";

			return $res;
		}

		$qry = "INSERT INTO `{$wpdb->prefix}wppipes_pipes` (`id`,`code`,`name`,`item_id`,`params`,`ordering`)"
			. "\n VALUES (NULL, '{$code}', '{$pipe->name}', {$id}, '{$pipe->params}',$ordering)";
		if ( ! $wpdb->query( $qry ) ) {
			$res->error = $wpdb->print_error();

			return $res;
		}

		$pipe_id = $wpdb->insert_id;
		$this->reOrderPipe( $id );
		$res->pipe_id = $pipe_id;
		$res->code    = $code;
		$res->name    = $pipe->name;
		$res->order   = $ordering;

		return $res;
	}

	function getAddonParam( $type, $name = '', $id = 0, $render = true ) {
		global $wpdb;
		$default = true;
		if ( $id > 0 ) {
			switch ( $type ) {
				case 'processor':
				case 'processorhelp':
					$qry = "SELECT `code` AS name,`params` FROM `{$wpdb->prefix}wppipes_pipes` WHERE `id`={$id}";
					break;
				case 'engine':
				case 'adapter':
					$qry = "SELECT `{$type}` AS name,`{$type}_params` AS params FROM `{$wpdb->prefix}wppipes_items` WHERE `id`={$id}";
					break;
				default:
					$qry    = '';
					$values = false;
			}
			if ( $qry != '' ) {
				$addon = $wpdb->get_row( $qry );
				if ( ! $name ) {
					$name = $addon->name;
				}
				$values = $addon->params;
			}
			if ( $values ) {
				$default = false;
			}
		}
		if ( $default ) {
//			$qry	= "SELECT `params` FROM `{$wpdb->prefix}extensions` WHERE `folder`= 'wppipes-{$type}' AND `element`='{$name}'";
			$values = '';
		}
		if ( ! $render ) {
			if ( isset( $_GET['x'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
				echo $values;
			}
			$params = json_decode( $values );

			return $params;
		}

		$html = $this->addonRender( $type, $name, $values, $id );

		return $html;
	}

	function addonRender( $type, $name, $values, $id = 0 ) {
		$dir = 'plugins' . DS . $type . 's' . DS . $name;
		if ( 'processorhelp' == $type ) {
			$dir = str_replace( 'processorhelp', 'processor', $dir );
			ob_start();
			$helppath = OBGRAB_ADMIN . $dir . DS . 'help.html';
			if ( ! is_file( $helppath ) ) {
				return 'There is still not guide line';
			}
			require_once $helppath;
			$res = ob_get_contents();
			ob_end_clean();

			return $res;
		}
		$xpath   = '/extension/config';
		$control = $type . ( $type == 'processor' ? "[{$id}]" : '' );
		$html    = ogb_common::get_params_render( $dir, $name, $values, $xpath, $control, $type . '.' . $name );

		return $html;
	}

	function loadElementAddon( $type, $name ) {
		$path = JPATH_SITE . DS . 'plugins' . DS . 'wppipes-' . $type . DS . $name . DS . 'elements' . DS;
		if ( ! is_dir( $path ) ) {
			return;
		}
		$elements = JFolder::files( $path, '.php$' );
		if ( ! is_array( $elements ) && count( $elements ) < 1 ) {
			return;
		}
		foreach ( $elements as $el ) {
			include_once $path . $el;
		}
	}

	function saveProcessParam( $id, $param ) {
		global $wpdb;
		//jimport('registry.registry');
//		$registry = new JRegistry();
		//$registry->loadArray( $param );
		$paramStr = json_encode( $param );
		$qry      = "UPDATE `{$wpdb->prefix}wppipes_pipes` SET `params` = '" . addslashes( $paramStr ) . "' WHERE `id` ={$id}";
		if ( ! $wpdb->query( $qry ) ) {
			$wpdb->print_error();
			//exit( '' . __FILE__ . ' - ' . __LINE__ );
		}
	}

	function makeOPS( $post ) {
		$outputs = new stdClass();
		$oe      = array();
		$op      = array();

		if ( isset( $post['oe'] ) ) {
			for ( $i = 0; $i < count( $post['oe'] ); $i ++ ) {
				$oe[] = $post['oe'][$i];
			}
		}
		if ( isset( $post['op'] ) ) {
			$i = 0;
			foreach ( $post['op'] as $ops ) {
				for ( $j = 0; $j < count( $ops ); $j ++ ) {
					$op[$i][$j] = $ops[$j];
				}
				$i ++;
			}
		}
		$outputs->oe = $oe;
		$outputs->op = $op;

		return json_encode( $outputs );
	}

	function makeIPS( $post ) {
		$inputs = new stdClass();
		$ip     = array();
		$ia     = array();

		if ( isset( $post['ia'] ) ) {
			for ( $i = 0; $i < count( $post['ia'] ); $i ++ ) {
				$ia_str = $post['ia'][$i];
				$ia_arr = explode( ',', $ia_str );
				$t      = new stdClass();
				$t->st  = $ia_arr[0];
				$t->of  = $ia_arr[1];
				$t->if  = $ia_arr[2];
				$ia[$i] = $t;
			}
		}

		if ( isset( $post['ip'] ) ) {
			//for ($i=0;$i<count($post['ip']);$i++) {
			$i = 0;
			foreach ( $post['ip'] as $ips ) {
				for ( $j = 0; $j < count( $ips ); $j ++ ) {
					$ip_str = $ips[$j];

					$ip_arr     = explode( ',', $ip_str );
					$t          = new stdClass();
					$t->st      = $ip_arr[0];
					$t->of      = $ip_arr[1];
					$t->if      = $ip_arr[2];
					$ip[$i][$j] = $t;
				}
				$i ++;
			}
		}
		$inputs->ip = $ip;
		$inputs->ia = $ia;

		return json_encode( $inputs );
	}

	function arrToStr( $arr ) {
		jimport( 'registry.format' );
		jimport( 'registry.registry' );
		$registry = new JRegistry();
		$registry->loadArray( $arr );

		return $registry->toString();
	}

	function save_b4_post() {
		global $wpdb;
		$post      = $_POST;
		$res       = new stdClass();
		$jdata     = array();
		$temp_arr  = array();
		$processor = array();
		foreach ( $post['jform'] as $val_jf ) {
			$jf_ar            = explode( '||', $val_jf );
			$jdata[$jf_ar[0]] = $jf_ar[1];
		}
		foreach ( $post['engine_par'] as $val_ep ) {
			$ep_ar = explode( '||', $val_ep );
			if ( count( $ep_ar ) > 2 ) {
				for ( $i = 1; $i < count( $ep_ar ); $i ++ ) {
					$temp_arr[$ep_ar[0]][] = $ep_ar[$i];
				}
			} else {
				$temp_arr[$ep_ar[0]] = $ep_ar[1];
			}
		}
		$jdata['engine_params'] = $this->arrToStr( $temp_arr );
		unset( $temp_arr );
		foreach ( $post['adapter_par'] as $val_ap ) {
			$ap_ar = explode( '||', $val_ap );
			if ( count( $ap_ar ) > 2 ) {
				for ( $i = 1; $i < count( $ap_ar ); $i ++ ) {
					$temp_arr[$ap_ar[0]][] = $ap_ar[$i];
				}
			} else {
				$temp_arr[$ap_ar[0]] = $ap_ar[1];
			}

		}

		$jdata['adapter_params'] = $this->arrToStr( $temp_arr );
		unset( $temp_arr );
		if ( isset( $post['ip'] ) ) {
			foreach ( $post['ip'] as $val_ip ) {
				$ip_ar                                      = explode( '||', $val_ip );
				$index_ar                                   = explode( '_', $ip_ar[0] );
				$temp_arr['ip'][$index_ar[0]][$index_ar[1]] = $ip_ar[1];
			}
		}
		foreach ( $post['ia'] as $val_ia ) {
			$ia_ar                     = explode( '||', $val_ia );
			$temp_arr['ia'][$ia_ar[0]] = $ia_ar[1];
		}
		$jdata['inputs'] = $this->makeIPS( $temp_arr );
		unset( $temp_arr );
		if ( isset( $post['op'] ) ) {
			foreach ( $post['op'] as $val_op ) {
				$op_ar                                      = explode( '||', $val_op );
				$index_ar                                   = explode( '_', $op_ar[0] );
				$temp_arr['op'][$index_ar[0]][$index_ar[1]] = $op_ar[1];
			}
		}
		foreach ( $post['oe'] as $val_oe ) {
			$oe_ar                     = explode( '||', $val_oe );
			$temp_arr['oe'][$oe_ar[0]] = $oe_ar[1];
		}
		$jdata['outputs'] = $this->makeOPS( $temp_arr );
		unset( $temp_arr );
		if ( is_array( @$post['proc'] ) ) {
			foreach ( $post['proc'] as $val_proc ) {
				$proc_ar                                         = explode( '||', $val_proc );
				$index_ar                                        = explode( '-', $proc_ar[0] );
				$processor[$index_ar[0]]['params'][$index_ar[1]] = $proc_ar[1];
			}
		}
		if ( ! isset( $jdata['id'] ) || ! $jdata['id'] || $jdata['id'] == 0 ) {
			$wpdb->insert(
				$wpdb->prefix . 'wppipes_items',
				array(
					'name'           => $jdata['name'],
					'published'      => $jdata['published'],
					'engine'         => $jdata['engine'],
					'adapter'        => $jdata['adapter'],
					'inputs'         => $jdata['inputs'],
					'outputs'        => $jdata['outputs'],
					'adapter_params' => $jdata['adapter_params'],
					'engine_params'  => $jdata['engine_params'],
				)
			);
			$jdata['id'] = $wpdb->insert_id;
		} else {
			$wpdb->update(
				$wpdb->prefix . 'wppipes_items',
				array(
					'name'           => $jdata['name'],
					'published'      => $jdata['published'],
					'engine'         => $jdata['engine'],
					'adapter'        => $jdata['adapter'],
					'inputs'         => $jdata['inputs'],
					'outputs'        => $jdata['outputs'],
					'adapter_params' => $jdata['adapter_params'],
					'engine_params'  => $jdata['engine_params'],
				),
				array( 'id' => $jdata['id'] )
			);
		}
		if ( isset( $processor ) && count( $processor ) > 0 ) {
			foreach ( $processor as $id => $param ) {
				$this->saveProcessParam( $id, $param['params'] );
			}
		}
		$res->msg = 'Saved success';

		return $res;
	}

	function save() {
		global $wpdb;
		$res          = new stdClass();
		$res->id      = 0;
		$res->msg     = '';
		$res->typemsg = 'message';

		$post = $_POST;

		$jdata = $post['jform'];
		if ( $jdata['name'] == '' ) {
			$res->msg     = 'Please input Title field!';
			$res->typemsg = 'Warning';

			return $res;
		}
		$jdata['inputs']  = $this->makeIPS( $post );
		$jdata['outputs'] = $this->makeOPS( $post );

		if ( isset( $post['adapter']['params'] ) && is_array( $post['adapter']['params'] ) ) {
			$jdata['adapter_params'] = $this->arrToStr( $post['adapter']['params'] );
		}

		if ( isset( $post['engine']['params'] ) && is_array( $post['engine']['params'] ) ) {
			$jdata['engine_params'] = $this->arrToStr( $post['engine']['params'] );
		}

		if ( ! isset( $jdata['id'] ) || ! $jdata['id'] ) {
			$wpdb->insert(
				$wpdb->prefix . 'wppipes_items',
				array(
					'name'           => $jdata['name'],
					'published'      => $jdata['published'],
					'engine'         => $jdata['engine'],
					'adapter'        => $jdata['adapter'],
					'inputs'         => $jdata['inputs'],
					'outputs'        => $jdata['outputs'],
					'adapter_params' => $jdata['adapter_params'],
					'engine_params'  => $jdata['engine_params'],
				)
			);
			$jdata['id'] = $wpdb->insert_id;
		} else {
			$wpdb->update(
				$wpdb->prefix . 'wppipes_items',
				array(
					'name'           => $jdata['name'],
					'published'      => $jdata['published'],
					'engine'         => $jdata['engine'],
					'adapter'        => $jdata['adapter'],
					'inputs'         => $jdata['inputs'],
					'outputs'        => $jdata['outputs'],
					'adapter_params' => $jdata['adapter_params'],
					'engine_params'  => $jdata['engine_params'],
				),
				array( 'id' => $jdata['id'] )
			);
		}

		if ( @$jdata['input_default'] != '' ) {
			$file = OBGRAB_HELPERS . 'processors.php';
			if ( is_file( $file ) ) {
				require_once $file;
				ogb_pro::makedf( $jdata, $jdata['input_default'] );
			}
		} elseif ( isset( $post['processor'] ) && count( $post['processor'] ) > 0 ) {
			foreach ( $post['processor'] as $id => $param ) {
				$this->saveProcessParam( $id, $param['params'] );
			}
		}
		//exit();
		$res->id  = $jdata['id'];
		$res->msg = sprintf( 'Pipe#%s saved. You can click <span class="label label-primary"><i class="fa fa-flask" title=""></i> Test this Pipe</span> button to test the results.', $jdata['id'] );

		return $res;
	}

	function getIOaddon( $type, $name, $params = array() ) {
		$res         = new stdClass();
		$res->err    = '';
		$path        = PIPES_PATH . DS . 'plugins' . DS . $type . 's' . DS . $name . DS . $name . '.php';
		$path_plugin = OB_PATH_PLUGIN . $name . DS . $name . '.php';
		switch ( $type ) {
			case 'engine':
				$class = 'WPPipesEngine_';
				break;
			case 'processor':
				$class = 'WPPipesPro_';
				break;
			case 'adapter':
				$class = 'WPPipesAdapter_';
				break;
			default:
				$res->err = "Unknow addon type [{$type} {$name}]";

				return $res;
		}
		if ( is_file( $path ) ) {
			include_once $path;
		} elseif ( ! is_file( $path_plugin ) ) {
			$res->err = "File not found [{$type} {$name}]";

			return $res;
		} else {
			include_once $path_plugin;
			$addon_name = explode( '-', $name );
			$name       = end( $addon_name );
		}

		$class .= $name;

		if ( ! method_exists( $class, 'getDataFields' ) ) {
			$res->err = "not found method getDataFields  [{$type} {$name}]";

			return $res;
		}
		$data = ogbLib::call_method( $class, 'getDataFields', array( $params ) );
		//$data	= $class::getDataFields($params);
		if ( isset( $data->input ) ) {
			$res->input = $data->input;
		}
		if ( isset( $data->output ) ) {
			$res->output = $data->output;
		}

		return $res;
	}

	function get_other_pipes( $id ) {
		global $wpdb;
		$sql  = "SELECT `id`, `name` FROM " . $wpdb->prefix . "wppipes_items WHERE `adapter` <> '' AND `engine` <> '' AND `id` <>" . $id;
		$data = $wpdb->get_results( $sql, ARRAY_A );

		return $data;
	}

	function getEditData( $id = null ) {
		global $wpdb;
		//$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
		if ( ! $id ) {
			$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
		}
//		$id	= (int) $cid[0];
//		var_dump($id);exit();
		//$data	= JTable::getInstance('Items','wppipesTable');
		if ( ! $id ) {
			$id = 0;
		}
		$sql  = 'SELECT * FROM ' . $wpdb->prefix . 'wppipes_items WHERE `id`=' . $id;
		$data = $wpdb->get_row( $sql );
//		echo $sql;
//		var_dump($data);
//		exit();
//		$data->load( $id );
		if ( $data ) {
			$data->inputs  = json_decode( $data->inputs );
			$data->outputs = json_decode( $data->outputs );
			$data->pipes   = $this->getPipes( $id );
		} else {
			$data                 = new stdClass();
			$data->id             = 0;
			$data->name           = '';
			$data->published      = 0;
			$data->engine         = '';
			$data->engine_params  = '';
			$data->adapter        = '';
			$data->adapter_params = '';
			$data->inherit        = '';
			$data->inputs         = '';
			$data->outputs        = '';
		}
		$disabled = false;
		if ( $data->inherit > 0 ) {
			$data->processors = '';
			$disabled         = true;
		} else {
			$data->processors = $this->getListProcessors();
		}

		$data->engines  = $this->getListEngine( $data->engine, $disabled );
		$data->adapters = $this->getListAdapter( $data->adapter, $disabled );

		if ( $data->engine != '' ) {
			$data->eParams = $this->addonRender( 'engine', $data->engine, $data->engine_params, $data->id );
		} else {
			$data->eParams = '';
		}

		$data->eParams = str_replace( 'data-toggle=', 'rel=', $data->eParams );
		if ( $data->adapter != '' ) {
			$data->aParams = $this->addonRender( 'adapter', $data->adapter, $data->adapter_params, $data->id );
		} else {
			$data->aParams = '';
		}
		$data->aParams = str_replace( 'data-toggle=', 'rel=', $data->aParams );
		$default_oe    = ogb_common::get_default_data( 'so', $id );
		if ( ! $default_oe || ! isset( $data->outputs->oe ) ) {
			return $data;
		}
		foreach ( $data->outputs->oe as $key => $value ) {
			if ( is_array( $default_oe->$value ) || ! is_string( $default_oe->$value ) ) {
				$data->outputs->oe[$key] = $value . '<br /><p class="text-muted small">Array</p>';
			} else {
				$default_oe->$value      = str_replace( "'", "", $default_oe->$value );
				$default_oe->$value      = str_replace( '"', '', $default_oe->$value );
				$data->outputs->oe[$key] = $value . '<br /><p title="' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) : 'null' ) . '" class="text-muted small">' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) . '</p>' : 'null</p>' );
			}
		}
		$default_op = ogb_common::get_default_data( 'po', $id );
		if ( ! $default_op ) {
			return $data;
		}
		foreach ( $data->outputs->op as $key => $values ) {
			foreach ( $values as $index => $value ) {
				if ( ! isset( $default_op[$key]->$value ) ) {
					continue;
				}
				if ( is_array( $default_op[$key]->$value ) || ! is_string( $default_op[$key]->$value ) ) {
					$values[$index] = $value . '<br /><p class="text-muted small">Array</p>';
				} else {
					$default_op[$key]->$value = str_replace( "'", "", $default_op[$key]->$value );
					$default_op[$key]->$value = str_replace( '"', '', $default_op[$key]->$value );
					$stripped                 = preg_replace( array( '/\s{2,}/', '/[\t\n]/' ), ' ', $default_op[$key]->$value );
					$stripped                 = strlen( $stripped ) > 200 ? mb_substr( strip_tags( $stripped ), 0, 200, 'UTF-8' ) . '...' : strip_tags( $stripped );
					$values[$index]           = $value . '<br /><p data-placement="bottom" title="' . $stripped . '" class="text-muted small">' . $stripped . '</p>';
				}
			}
			$data->outputs->op[$key] = $values;
		}

		return $data;
	}

	/**
	 * Get the list of enabled processors
	 * @return string
	 */
	function getListProcessors() {
		$rows   = $this->getAddons( 'processor', true );
		$select = 'id="new_processor" data-placeholder="- Add a Processor -" name="new_processor" class="chosen-select form-control" onchange="addProcessor()"';
		$first  = __( '- Add a Processor -' );
		$select = $this->getHtmlList( $select, $rows, '', $first );

		return $select;
	}

	function getListEngine( $default, $disabled = false ) {
		$rows = $this->getAddons( 'engine' );
		// if there is only one engine, set it as default
		// @TODO: it doens't load params
//		if ( count( $rows ) == 1 ) {
//			$default = $rows[0]->value;
//		}
		$select = 'name="jform[engine]" class="chosen-select" onchange="jQuery(\'#engineTab a:first\').tab(\'show\');ogb_loadEngine(0)" id="ogb_engine"';
		if ( $disabled ) {
			$select .= ' disabled="disabled"';
		}
		$first  = __( '- Select a Source -' );
		$select = $this->getHtmlList( $select, $rows, $default, $first );

		return $select;
	}

	function getListAdapter( $default, $disabled = false ) {
		$rows = $this->getAddons( 'adapter' );
		// if there is only one adapter, set it as default
		// @TODO: it doens't load params
//		if ( count( $rows ) == 1 ) {
//			$default = $rows[0]->value;
//		}
		$select = 'name="jform[adapter]" class="chosen-select" onchange="jQuery(\'#adapterTab a:first\').tab(\'show\');ogb_loadAdapter(0)" id="ogb_adapter"';
		if ( $disabled ) {
			$select .= ' disabled="disabled"';
		}
		$first  = __( '- Select a Destination -' );
		$select = $this->getHtmlList( $select, $rows, $default, $first );

		return $select;
	}

	function  getHtmlList( $select, $rows, $default, $first = '' ) {

		$selected = 'selected="selected"';
		$select   = '<select ' . $select . '>';
		if ( $first != '' ) {
			$select .= '<option value = ""> ' . $first . ' </option>';
		}
		for ( $i = 0; $i < count( $rows ); $i ++ ) {
			$select .= '
				<option value="' . $rows[$i]->value . '" ' . ( $default == $rows[$i]->value ? $selected : '' ) . '>' . $rows[$i]->text . '</option>
			';
		}
		$select .= '</select>';

		return $select;
	}

	/**
	 * get Plugins listing, filter by $type: engine, adapter, processor
	 * */
	function getAddons( $type = '', $show_desc = false ) {
		global $wpdb;

		require_once PIPES_PATH . DS . 'helpers' . DS . 'plugins.php';

		$addons = PIPES_Helper_Plugins::getPlugins( true, $type );
		// Get Addon Description
		$ILove = array();
		foreach ( $addons AS $addon ) {
			$You        = new stdClass();
			$You->value = $addon['element'];
			/*if ( isset( $addon['description'] ) && $show_desc ) {
				$You->text = $addon['name'] . ' (' . $addon['description'] . ')';
			} else {
				$You->text = $addon['name'];
			}*/
			$You->text = $addon['name'];
			$ILove[]   = $You;
		}

		if ( isset( $_GET['x'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo '<pre>';
			print_r( $ILove );
			echo '</pre>';
		}

//		$ILove = $addons;
		return $ILove;
	}

	function getPipes( $id ) {
		global $wpdb;
		$qry   = 'SELECT `id`,`code`,`name`,`params` FROM `' . $wpdb->prefix . 'wppipes_pipes` WHERE `item_id`=' . $id . ' ORDER BY `ordering`';
		$pipes = $wpdb->get_results( $qry );
		if ( ! $pipes ) {
			return $pipes;
		}
		foreach ( $pipes as $key => $pipe ) {
			$paramobj = json_decode( $pipe->params );
			if ( isset( $paramobj->note ) ) {
				$note = $paramobj->note;
			} else {
				$note = '';
			}
			$pipes[$key]->note = $note;
		}

		return $pipes;
	}

	function _buildQuery() {
		global $wpdb;
		$where   = $this->_buildContentWhere();
		$orderby = $this->_buildContentOrderBy();

		$query = "
			SELECT
				SQL_CALC_FOUND_ROWS i.`id`,i.`name`,
				i.`published`,
				i.`engine_params`,
				e.`name` AS `engine`,
				a.`name` AS `adapter`,
				i.`inherit`
			FROM
				`{$wpdb->prefix}wppipes_items` AS i,
				`{$wpdb->prefix}extensions` AS e,
				`{$wpdb->prefix}extensions` AS a
			{$where}
			WHERE
				i.`engine` = e.`element` AND e.`folder` = 'wppipes-engine' AND
				i.`adapter` = a.`element` AND a.`folder` = 'wppipes-adapter'
			{$orderby}
		";

		return $query;
	}

	function get_limit() {
		global $mainframe, $option;
		$limit      = $mainframe->getUserStateFromRequest( $option . '.pipes.limit', 'limit', 20, 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option . '.pipes.limitstart', 'limitstart', 0, 'int' );

		return array( $limit, $limitstart );
	}

	function get_filter_order() {
		global $mainframe, $option;
		$order     = $mainframe->getUserStateFromRequest( $option . '.pipes.order', 'filter_order', 'inherit', 'cmd' );
		$order_Dir = $mainframe->getUserStateFromRequest( $option . '.pipes.order_Dir', 'filter_order_Dir', 'ASC', 'word' );

		return array( $order, $order_Dir );
	}

	function _buildContentOrderBy() {
		$filter  = $this->get_filter_order();
		$orderby = ' ORDER BY ' . $filter[0] . ' ' . $filter[1];

		return $orderby;
	}

	function _buildContentWhere() {
		global $mainframe, $option;
		$filter_state = $mainframe->getUserStateFromRequest( $option . '.feedposts.filter_state', 'filter_feedpost', '', 'word' );
		$search       = $mainframe->getUserStateFromRequest( $option . '.feedposts.search', 'search', '', 'string' );
		$search       = $this->_db->getEscaped( trim( JString::strtolower( $search ) ) );
		$where        = array();
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'published = 1';
			} else {
				if ( $filter_state == 'U' ) {
					$where[] = 'published = 0';
				}
			}
		}
		if ( $search ) {
			$where[] = " `name` LIKE '%{$search}%' ";
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function getTotal() {
		// Load the content if it doesn't already exist
		if ( empty( $this->_total ) ) {
			$query        = $this->_buildQuery();
			$this->_total = $this->_getListCount( $query );
		}

		return $this->_total;
	}

	function getPagination() {
		// Load the content if it doesn't already exist
		if ( empty( $this->_pagination ) ) {
			jimport( 'joomla.html.pagination' );
			$limit             = $this->get_limit();
			$this->_pagination = new JPagination( $this->getTotal(), $limit[1], $limit[0] );
		}

		return $this->_pagination;
	}

	function savenote( $id, $new_note = '' ) {
		global $wpdb;
		$qry = "SELECT `params` FROM `{$wpdb->prefix}wppipes_pipes` WHERE `id`={$id}";

		$pipes  = $wpdb->get_results( $qry, OBJECT );
		$params = json_decode( $pipes[0]->params );
		if ( ! $params ) {
			$params = new stdClass();
		}
		$params->note = $new_note;
		$this->saveProcessParam( $id, $params );

		return true;
	}

	function create_temp() {
		global $wpdb;
		$this->remove_if_no_ip();
		$qry     = "SELECT MAX(id) FROM `{$wpdb->prefix}wppipes_items`";
		$max_cur = $wpdb->get_var( $qry );
		$maxid   = $max_cur + 1;

		$name = 'Pipe#' . $maxid;

		$ins_q = "INSERT INTO `{$wpdb->prefix}wppipes_items` (`id`,`name`,`published`) "
			. "\n VALUES ({$maxid}, '{$name}', 1)";
		$wpdb->query( $ins_q );

		return $maxid;
	}

	function remove_if_no_ip() {
		global $wpdb;
		$sql       = "SELECT `id` FROM `{$wpdb->prefix}wppipes_items` WHERE `engine`='' OR `adapter`=''";
		$list_pipe = $wpdb->get_results( $sql );
		if ( count( $list_pipe ) > 0 ) {
			foreach ( $list_pipe as $pipe ) {
				$qry = "DELETE FROM `{$wpdb->prefix}wppipes_items` WHERE `id`= {$pipe->id}";
				if ( ! $wpdb->query( $qry ) ) {
					$error = $wpdb->last_error;

					return $error;
				}
				$qry = "DELETE FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`= {$pipe->id}";
				if ( ! $wpdb->query( $qry ) ) {
					$error = $wpdb->last_error;

					return $error;
				}
			}
		}
	}

	public function getForm() {
		jimport( 'includes.form.form' );
		jimport( 'includes.form.field' );
		jimport( 'includes.html.html' );
		jimport( 'includes.html.select' );
		jimport( 'includes.form.helper' );
		jimport( 'includes.registry.registry' );
		jimport( 'includes.string.string' );
// 		load_plugin_textdomain();
		JForm::addFormPath( dirname( __FILE__ ) . DS . 'forms' . DS );
		JForm::addFieldPath();
		$options = array( 'control' => '', 'load_data' => true );
		$form    = JForm::getInstance( 'my.form', 'item_params', $options, false, '//config' );
		$data    = $this->getFormData();

		$form->bind( $data );

		return $form;
	}

	public function getFormData() {
		$data = array( 'params' => array(
			'template'      => 'Oi Cuoc Song Men Thuong !!!',
			'desc_template' => 'Song trong doi song can co 1 dong tien'
		) );

		return $data;
	}

	public static function get_first_output_processor( $current_data, $ordering, $proc_id ) {
		global $wpdb;
		$sql  = "SELECT * FROM `{$wpdb->prefix}wppipes_pipes` WHERE `id`={$proc_id}";
		$pipe = $wpdb->get_row( $sql );

		$path        = PIPES_PATH . DS . 'plugins' . DS . 'processors' . DS . $pipe->code . DS . $pipe->code . '.php';
		$path_plugin = OB_PATH_PLUGIN . $pipe->code . DS . $pipe->code . '.php';
		if ( is_file( $path ) ) {
			include_once $path;
			$class = 'WPPipesPro_' . $pipe->code;
		} elseif ( ! is_file( $path_plugin ) ) {
			$res = new stdClass();
			$res->err = "File not found [processor {$pipe->code}]";

			return $res;
		} else {
			include_once $path_plugin;
			$real_name = explode( '-', $pipe->code );
			$class     = 'WPPipesPro_' . end( $real_name );
		}
		$pInput = new stdClass();
		foreach ( $current_data->pi[$ordering] as $key => $value ) {
			$val_array  = explode( ',', $value );
			$type_input = $val_array[0];
			$name_input = $val_array[1];
			$data_value = $current_data->$type_input;
			if ( ! isset( $data_value ) ) {
				continue;
			}
			if ( count( $val_array ) > 3 && $val_array[3] != '' ) {
				$pInput->$key = $data_value[$val_array[3]]->$name_input;
			} else {
				$pInput->$key = $data_value->$name_input;
			}
		}
		$pOutput = ogbLib::call_method( $class, 'process', array( $pInput, json_decode( $pipe->params ) ) );
		foreach ( $pOutput as $out_key => $out_value ) {
			if ( is_string( $out_value ) ) {
				$out_value         = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $out_value );
				$out_value         = str_replace( '"', '', $out_value );
				$out_value         = str_replace( "'", "", $out_value );
				$pOutput->$out_key = strip_tags( $out_value );
			}
		}

		if ( ! is_array( $current_data->po[$ordering] ) ) {
			$current_data->po[$ordering] = array();
		}
		if ( isset( $pOutput ) ) {
			$current_data->po[$ordering] = $pOutput;
		}

		return $current_data;
	}

	public function quick_edit_pipe() {
		global $wpdb;
		$res = new stdClass();
		$res->err = '';
		if ( isset( $_POST ) ) {
			$res->title = $_POST['title'];
			$id = $_POST['id'];
			$res->status = $_POST['status'];
			$sql = "UPDATE `{$wpdb->prefix}wppipes_items` SET `name` = '{$res->title}', `published` = '{$res->status}' WHERE `id` = {$id}";

			if ( ! $wpdb->query( $sql ) ) {
				$res->err = $wpdb->last_error;
				return $res;
			}
		}else{
			$res->err = 'There is error';
		}
		return $res;
	}
}