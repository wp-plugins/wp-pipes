<?php
defined('_JEXEC') or die( 'Restricted access' );

class ogb_pro{
	public static function addslashes($str){
		return addslashes($str);
	}
	public static function makedf($row,$list_p){
		global $wpdb;
		self::remove_pipes($row['id']);
		require_once dirname(__FILE__).DS.'plugins.php';
		$list_processors = PIPES_Helper_Plugins::getProcessors();
		$proc_array	= explode('-',$list_p);
		
		$qry	= "INSERT INTO `{$wpdb->prefix}wppipes_pipes` (`id`,`code`,`name`,`item_id`,`params`,`ordering`) VALUES \n";
		foreach($proc_array as $key=>$proc){
			if ( isset ( $list_processors[$proc] ) ) {
				$name_proc = $list_processors[$proc]['name'];
				$pr_proc = '';
			}
			/*$proc_obj	= self::get_dfparams($proc);
			$pr_proc	= $proc_obj->params;
			$name_proc	= $proc_obj->name;*/
			if(in_array($row['adapter'],array('zoo','post')) && $proc=='image'){
				$pr_proc			= json_decode($pr_proc);
				$pr_proc->makelist	= 1;
				$pr_proc->remove	= 1;
				$pr_proc			= json_encode($pr_proc);
			}
			$qry	.= "\n(NULL, '{$proc}', '{$name_proc}', {$row['id']}, '".addslashes($pr_proc)."', {$key}),";
		}
		$qry		= substr($qry,0,-1);
		$wpdb->query($qry);
		//if(!$db->query()){
//			echo $db->getQuery();
//			echo '<br />Error: '.$db->getErrorMsg();
//			exit();
//		}elseif(isset($_GET['x'])) {
//			echo '<br /><br />'.$qry;
//			exit();
//		}		
		$inputs		= self::get_inputs($row['adapter']);
		$outputs	= self::get_outputs();
		 //, outputs 
		$qry = "UPDATE `{$wpdb->prefix}wppipes_items`\n".
				"SET `inputs` = '{$inputs}',\n".
				"`outputs` = '{$outputs}'\n".
				"WHERE `id` = '{$row['id']}' LIMIT 1";
		$wpdb->query($qry);
		//if(!$db->query()){
//			echo $db->getQuery();
//			echo '<br />ERROR: '.$db->getErrorMsg();exit();
//		}
	}
	public static function get_inputs($adapter){
		switch($adapter){
			case 'zoo':
				$ia	= '{"st":"e","of":"title","if":"name"},{"st":"0","of":"slug","if":"slug"},{"st":"3","of":"introtext","if":"introtext"},{"st":"3","of":"fulltext","if":"fulltext"},{"st":"2","of":"images","if":"images"},{"st":"","of":"","if":"created"},{"st":"","of":"","if":"publish_up"},{"st":"","of":"","if":"publish_down"},{"st":"","of":"","if":"keywords"}';
				break;
			case 'post':
				$ia	= '{"st":"e","of":"title","if":"title"},{"st":"0","of":"slug","if":"slug"},{"st":"","of":"","if":"excerpt"},{"st":"4","of":"fulltext","if":"content"},{"st":"e","of":"date","if":"date"},{"st":"3","of":"images","if":"images"},{"st":"","of":"","if":"metakey"}';
				break;
			default:
				$ia	= '{"st":"e","of":"title","if":"title"},{"st":"0","of":"slug","if":"slug"},{"st":"4","of":"introtext","if":"introtext"},{"st":"4","of":"fulltext","if":"fulltext"},{"st":"e","of":"date","if":"created"},{"st":"e","of":"date","if":"publish_up"},{"st":"","of":"","if":"publish_down"},{"st":"3","of":"images","if":"images"},{"st":"5","of":"metakey","if":"metakey"},{"st":"5","of":"metadesc","if":"metadesc"}';
		}
		$inputs	= '{"ip":[[{"st":"e","of":"title","if":"text"}],[{"st":"0","of":"slug","if":"input"}],[{"st":"e","of":"link","if":"url"},{"st":"","of":"","if":"html"}],[{"st":"","of":"","if":"url"},{"st":"2","of":"fulltext","if":"html"}],[{"st":"3","of":"html","if":"html"}],[{"st":"2","of":"full_html","if":"html"}]],"ia":['.$ia.']}';
		return addslashes($inputs);
	}
	public static function get_outputs(){
		$outputs	= '{"oe":["title","link","description","author","date","enclosures"],"op":[["slug"],["result"],["full_html","fulltext"],["images","html"],["introtext","fulltext"],["metakey","metadesc"]]}';
		return addslashes($outputs);
	}	
	public static function get_dfparams($pro){
		global $wpdb;
		$qry	= "SELECT `params`, `name` FROM `{$wpdb->prefix}extensions` WHERE `folder`= 'wppipes-processor' AND `element`='{$pro}'";
		$wpdb->query($qry);
		$params	= $db->LoadObject();
		return $params;
	}
	
	public static function remove_pipes($id){
		global $wpdb;
		$qry		= "DELETE FROM `{$wpdb->prefix}wppipes_pipes` WHERE `item_id`={$id}";
		$wpdb->query($qry);
		return;
	}
}