<?php
/**
 * @package		obGrabber - Joomla! Anything Grabber
 * @version		$Id: original_source.php 61 2013-12-14 01:19:15Z thongta $
 * @author		Kha Nguyen - foobla.com
 * @copyright	(c) 2007-2012 foobla.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE
 */
defined('_JEXEC') or die( 'Restricted access' );
class WPPipesPro_combine {
	public static function process($data, $params) {
		if (isset($_GET['combine'])){
			echo '<br /><br /><i><b>File</b> '.__FILE__.' <b>Line</b> '.__LINE__."</i><br />\n";
			echo '<pre>';
			echo 'Params: ';
			print_r($params);
			echo 'Data: ';
			print_r($data);
			echo '</pre>';	//exit();		
		}
		$res	= new stdClass();
		if($params->combine!=''){
			preg_match_all('/(?<={).*?(?=})/i',$params->combine,$matches);
			$inputs = array();
			if(is_array($matches[0]) && count($matches[0])> 0){
				foreach($matches[0] as $key=>$value){
					$value = str_replace('[so]', '[oe]', $value);
					$seperate_array = explode(' ',$value);
					if($seperate_array[0] == '[oe]'){
						$inputs[$matches[0][$key]] = $data->no_need['oe']->$seperate_array[1];
					}else{
						preg_match('/(?<=\[).*?(?=\])/i', $seperate_array[0], $result);
						if(isset($data->no_need['op'][$result[0]]->$seperate_array[1])){
							$inputs[$matches[0][$key]] = $data->no_need['op'][$result[0]]->$seperate_array[1];
						}
					}
				}
			}

			foreach($inputs as $key_ip=>$new_value){
				$params->combine = str_replace('{'.$key_ip.'}',$new_value,$params->combine);
			}
			//$static_value	= '<p>'.$params->static_value.'</p>';
			//echo '<pre>';print_r($params->combine);die;
			$html	= $params->combine;
		}else{
			$html	= '';
		}
		
		$res->html	= $html;
		return $res;
	}
	public static function getDataFields($params=false){
		$data	= new stdClass();
		$data->input	= array('');
		$data->output	= array('html');
		return $data;
	}
}