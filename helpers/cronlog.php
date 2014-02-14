<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: cronlog.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );
//require_once JPATH_COMPONENT.DS.'cronjob.php';
//require_once JPATH_ROOT.DS.'components'.DS.'com_wppipes'.DS.'define.php';
define( 'OGRAB_CACHE_LOG', OGRAB_CACHE . 'log' . DS );
$CronLog = new CronLog;
$CronLog->view();
class CronLog {
	function getFiles( $dir ) {
		$res        = new stdClass();
		$res->items = array();
		$res->total = 0;
		if ( ! is_dir( $dir ) ) {
			return $res;
		}

		$items = JFolder::files( $dir, '.', true, true );
		$names = array();
		foreach ( $items as $item ) {
			$item    = str_replace( DS . '/', DS . DS, $item );
			$names[] = str_replace( $dir . DS, '', $item );
		}
		asort( $names );
		$res->items = $names;
		$res->total = count( $names );

		return $res;
	}

	function getCByte( $val ) {
		if ( $val < 1024 ) {
			$mem = $val . " bytes";
		} elseif ( $val < 1048576 ) {
			$mem = round( $val / 1024, 2 ) . " KB";
		} else {
			$mem = round( $val / 1048576, 2 ) . " MB";
		}

		return $mem;
	}

	function view() {
		global $option;
		JHTML::stylesheet( 'obstyle.css', 'administrator/components/' . $option . '/assets/css/' );
		JHtml::_( 'script', 'system/core.js', false, true );
		JToolBarHelper::title( JText::_( 'View Logs' ), 'cronlog.png' );
//		JToolBarHelper::preferences('com_wppipes', '500', '700');
		JToolBarHelper::divider();
//		JToolBarHelper::back();
//		JToolBarHelper::back('Dashboard','index.php?option=com_wppipes');
//		$bar = JToolBar::getInstance('toolbar');
//		$iwant_button = "
//			<div class=\"btn-group\">
//				<button type=\"button\" class=\"btn btn-small btn-info dropdown-toggle\" onclick=\"display_form();\">
//					".JText::_('COM_OBGRABBER_I_WANT')."
//				</button>
//				<div class=\"dropdown-iwant\" id=\"dropdown-iwant\" style=\"display:none;\">
//					<form role=\"form\">
//						<div class=\"form-group\">
//							<textarea rows=\"10\" name=\"iwant\" id=\"iwantto\" class=\"input-xlarge\"></textarea>
//						</div>
//						<div class=\"form-group\">
//							<span class=\"input-group-btn\">
//								<a id=\"iw_btn\" class=\"btn btn-default btn-primary\" href=\"#\">".JText::_('COM_OBGRABBER_SEND')."</a>
//							</span>
//						</div>
//					</form>
//				</div>
//			</div>
//			";
//		$bar->appendButton('Custom', $iwant_button, 'iwant');
		ogb_common::renderIWantBtn();
		//JToolBarHelper::custom('back','back.png', 'back.png', JText::_('COM_OBGRABBER_BACK'), false,false);
		$logName = array();
		if ( is_dir( OGRAB_CACHE_LOG ) ) {
			$logs = JFolder::files( OGRAB_CACHE_LOG, '.', true, true );

			foreach ( $logs as $log ) {
				$log       = str_replace( DS . '/', DS . DS, $log );
				$logName[] = str_replace( OGRAB_CACHE_LOG . DS, '', $log );
			}
			asort( $logName );
			$lastestlog = count( $logName ) - 1;
		} else {
			$lastestlog = - 1;
		}

		$saveds = CronLog::getFiles( OGRAB_CACHE_SAVED );

		$items    = $saveds->items;
		$lastSave = $saveds->total > 0 ? $items[( $saveds->total - 1 )] : '';
		if ( $lastestlog == - 1 ) {
			$nDefault = $lastSave;
		} else {
			$nDefault = $lastSave != '' ? $lastSave : $logName[$lastestlog];
		}

		$logN     = JRequest::getVar( 'name', $nDefault );
		$showsize = JRequest::getVar( 'showsize', 0 );
		//$style	= "<style>"
		//."li {color:#333333;}"
		//."li hr {margin:5px;border:1px dashed #dddddd;}"
		//."row-fluid .span10 { width: 95%;}"
		//."</style>";
		//echo $style;
		//$st	='background:#f8f8f8;border:1px solid #ddd;float:left;height:400px;overflow:auto;padding:5px;margin-left:5px;';
		$com_url = 'index.php?option=com_wppipes&controller=items&task=viewlog';
		echo '
			<div class="foobla">
				<div class="row">
		';
		echo '
				<div class="col-md-3 well well-small">
					<h3 class="text-error">Cron-Job work</h3>
		';
		if ( $lastestlog == - 1 ) {
			echo '<span style="color:#666666;"><i>No Logs available: To enable Cronjob you need to open Options > Cronjob > Enable: Yes</i></span>';
			//return ;
		} else {
			echo "<ol>";
			$k = 1;
			for ( $i = $lastestlog; $i > - 1; $i -- ) {
				if ( $k < 101 ) {
					if ( $showsize ) {
						$fileZise = CronLog::getCByte( filesize( OGRAB_CACHE_LOG . DS . $logName[$i] ) );
						$size     = " <i>({$fileZise})</i>";
					} else {
						$size = '';
					}
					if ( $logN == $logName[$i] ) {
						$fileZise = CronLog::getCByte( filesize( OGRAB_CACHE_LOG . DS . $logName[$i] ) );
						$size     = " <i>({$fileZise})</i>";
						echo "<li><b style=\"color:#444444;\">{$logName[$i]}</b>{$size}</li>";
					} else {
						echo "<li><a href=\"{$com_url}&name={$logName[$i]}&type=log\">{$logName[$i]}</a>{$size}</li>";
					}
				} else {
					JFile::delete( OGRAB_CACHE_LOG . DS . $logName[$i] );
				}
				$k ++;
			}
			echo "</ol>";
		}
		echo "</div>";
		$logSave = JRequest::getVar( 'type', 'saved' ) == 'saved';
		if ( $logSave ) {
			$logF = JPath::clean( OGRAB_CACHE_SAVED . $logN );
		} else {
			$logF = JPath::clean( OGRAB_CACHE_LOG . $logN );
		}

		if ( is_file( $logF ) ) {
			$logC = JFile::read( $logF );
			if ( $logSave ) {
				$logC = "<ol>{$logC}</ol>";
			}
		} else {
			$logC = '<i>No exist Log!</i>';
		}
		echo "<div class=\"col-md-6 well well-small\"><h3 style=\"color:#009900\">Logs Details</h3>$logC</div>";
		echo "<div class=\"col-md-3 well well-small\"><h3 style=\"color:#ff6600;\">Saved items</h3>";
		if ( $saveds->total > 0 ) {
			echo '<ol>';
			$k = 1;
			for ( $i = ( $saveds->total - 1 ); $i > - 1; $i -- ) {
				if ( $k < 101 ) {
					if ( $logN == $items[$i] ) {
						$fileZise = CronLog::getCByte( filesize( OGRAB_CACHE_SAVED . $items[$i] ) );
						echo "<li><b>{$items[$i]}</b> <i>({$fileZise})</i></li>";
					} else {
						echo "<li><a href=\"{$com_url}&name={$items[$i]}&type=saved\">{$items[$i]}</a></li>";
					}
				} else {
					JFile::delete( OGRAB_CACHE_SAVED . $items[$i] );
				}
				$k ++;
			}
			echo '</ol>';
		} else {
			echo '<i>None saved</i>';
		}
		echo '</div></div></div>';

		return;
	}
}