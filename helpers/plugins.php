<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: plugins.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );


//@session_start();

class PIPES_Helper_Plugins {
	public static function parseXMLInstallFile( $path ) {
		// Read the file to see if it's a valid component XML file
		$xml = simplexml_load_file( $path );

		if ( ! $xml ) {
			return false;
		}

		// Check for a valid XML root tag.

		// Extensions use 'extension' as the root tag.  Languages use 'metafile' instead

		if ( $xml->getName() != 'extension' && $xml->getName() != 'metafile' ) {
			unset( $xml );

			return false;
		}

		$data = array();

		$data['name']    = (string) $xml->name;
		$data['element'] = pathinfo( $path, PATHINFO_FILENAME );

		// Check if we're a language. If so use metafile.
		$data['type']  = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;
		$data['group'] = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->group;

		$group_arr            = explode( '-', $data['group'] );
		$data['addon']        = @$group_arr[1];
		$data['creationDate'] = ( (string) $xml->creationDate ) ? (string) $xml->creationDate : JText::_( 'Unknown' );
		$data['author']       = ( (string) $xml->author ) ? (string) $xml->author : JText::_( 'Unknown' );

		$data['copyright']   = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl']   = (string) $xml->authorUrl;
		$data['version']     = (string) $xml->version;
		$data['description'] = (string) $xml->description;


		return $data;
	}

	public static function getPlugins( $update = false, $type = '' ) {
		global $addon_type;
		if ( $type && method_exists( 'PIPES_Helper_Plugins', 'get' . $type . 's' ) ) {
			return call_user_func( array( 'PIPES_Helper_Plugins', 'get' . $type . 's' ), $update );
		}
		if ( ! isset( $_SESSION['PIPES']['plugins'] ) || $update || empty( $_SESSION['PIPES']['plugins'] ) ) {
			$engines    = self::getEngines( $update ); # get engines
			$adapters   = self::getAdapters( $update ); # get adapters
			$processors = self::getProcessors( $update ); #get processors
			switch ( $addon_type ) {
				case 'adapters':
					$plugins = $adapters;
					break;
				case 'engines':
					$plugins = $engines;
					break;
				case 'processors':
					$plugins = $processors;
					break;
				default:
					$plugins = array_merge( $engines, $adapters, $processors );
					break;
			}

			$_SESSION['PIPES']['plugins'] = $plugins;
		}

		return $_SESSION['PIPES']['plugins'];
	}


	public static function getEngines( $update = false ) {
		if ( ! isset( $_SESSION['PIPES']['engines'] ) || $update || empty( $_SESSION['PIPES']['engines'] ) ) {
			$path                         = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'engines';
			$path_to_wp_plugin            = OB_PATH_PLUGIN;
			$datas_in_wp_plgin            = self::getPluginsInFolder( $path_to_wp_plugin, 'engine' );
			$datas                        = self::getPluginsInFolder( $path, 'engine' );
			$datas                        = array_merge( $datas_in_wp_plgin, $datas );
			$_SESSION['PIPES']['engines'] = $datas;
		}

		return $_SESSION['PIPES']['engines'];
	}

	public static function getAdapters( $update = false ) {
		if ( ! isset( $_SESSION['PIPES']['adapters'] ) || $update || empty( $_SESSION['PIPES']['adapters'] ) ) {
			$path                          = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'adapters';
			$path_to_wp_plugin             = OB_PATH_PLUGIN;
			$datas_in_wp_plgin             = self::getPluginsInFolder( $path_to_wp_plugin, 'adapter' );

			$datas                         = self::getPluginsInFolder( $path, 'adapter' );
			$datas                         = array_merge( $datas_in_wp_plgin, $datas );
			$_SESSION['PIPES']['adapters'] = $datas;
		}

		return $_SESSION['PIPES']['adapters'];
	}

	public static function getProcessors( $update = false ) {
		if ( ! isset( $_SESSION['PIPES']['processors'] ) || $update || empty( $_SESSION['PIPES']['processors'] ) ) {
			$path                            = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'processors';
			$path_to_wp_plugin               = OB_PATH_PLUGIN;
			$datas_in_wp_plgin               = self::getPluginsInFolder( $path_to_wp_plugin, 'processor' );
			$datas                           = self::getPluginsInFolder( $path, 'processor' );
			$datas                           = array_merge( $datas_in_wp_plgin, $datas );
			$_SESSION['PIPES']['processors'] = $datas;
		}

		return $_SESSION['PIPES']['processors'];
	}

	public static function getPluginsInFolder( $path, $type) {
		require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'filesystem.php';
		$folders = PIPES_Helper_FileSystem::dirs( $path );
		$path_elements = explode(DIRECTORY_SEPARATOR, $path);
		$datas   = array();
		foreach ( $folders as $folder ) {
			$path_xml = $path . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.xml';

			if ( ! is_file( $path_xml ) ) {
				$data = null;
			} elseif( end( $path_elements ) == '' && ! is_plugin_active($folder . '/' . $folder . '.php')){
				$data = null;
			}else {
				$data = self::parseXMLInstallFile( $path_xml );
			}

			if ( isset( $data['element'] ) && ( isset( $data['addon'] ) && $data['addon'] == $type ) ) {
				$datas[$data['element']] = $data;
			}
		}

		return $datas;
	}
}