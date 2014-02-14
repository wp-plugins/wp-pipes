<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: filesystem.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Cannot direct access' );

class PIPES_Helper_FileSystem {
	public static function files( $path, $filter = '.', $recurse = false, $full = false, $exclude = array( '.svn', 'CVS', '.DS_Store', '__MACOSX' ), $excludefilter = array( '^\..*', '.*~' ), $findfiles = true ) {
		@set_time_limit( ini_get( 'max_execution_time' ) );

		// Initialise variables.
		$arr = array();

		// Read the source directory
		$handle = opendir( $path );
		while ( ( $file = readdir( $handle ) ) !== false ) {
			if ( $file != '.' && $file != '..' && ! in_array( $file, $exclude ) && ( empty( $excludefilter_string ) || ! preg_match( $excludefilter_string, $file ) ) ) {
				// Compute the fullpath
				$fullpath = $path . '/' . $file;
				// Compute the isDir flag
				$isDir = is_dir( $fullpath );
				if ( ( $isDir xor $findfiles ) && preg_match( "/$filter/", $file ) ) {
					// (fullpath is dir and folders are searched or fullpath is not dir and files are searched) and file matches the filter
					if ( $full ) {
						// Full path is requested
						$arr[] = $fullpath;
					} else {
						// Filename is requested
						$arr[] = $file;
					}
				}
				if ( $isDir && $recurse ) {
					// Search recursively
					if ( is_integer( $recurse ) ) {
						// Until depth 0 is reached
						$arr = array_merge( $arr, self::files( $fullpath, $filter, $recurse - 1, $full, $exclude, $excludefilter_string, $findfiles ) );
					} else {
						$arr = array_merge( $arr, self::files( $fullpath, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles ) );
					}
				}
			}
		}
		closedir( $handle );

		return $arr;
	}

	public static function dirs( $path, $filter = '.', $recurse = false, $full = false, $exclude = array( '.svn', 'CVS', '.DS_Store', '__MACOSX' ), $excludefilter = array( '^\..*', '.*~' ) ) {
		return self::files( $path, $filter, $recurse, $full, $exclude, $excludefilter, false );
	}
}