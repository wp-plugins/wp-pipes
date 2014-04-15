<?php

/**
 * @package   wppipes
 * @author    wpbriz.com
 */
class AppRequirements {

	var $_required_results = array();
	var $_recommended_results = array();

	var $_required_extensions = array(
		array( 'name' => 'JSON', 'extension' => 'json', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/book.json.php">book.json.php</a>' ),
		array( 'name' => 'cURL', 'extension' => 'curl', 'info' => 'cURL is required for Facebook Connect and Twitter Authenticate to work.' ),
		array( 'name' => 'Multibyte String', 'extension' => 'mbstring', 'info' => '<a target="_blank" href="http://www.php.net/manual/en/book.mbstring.php">book.mbstring.php</a>' )
	);

	var $_recommended_extensions = array(
		array( 'name' => 'Multibyte String', 'extension' => 'mbstring', 'info' => 'mbstring is designed to handle Unicode-based encodings such as UTF-8. Check <a target="_blank" href="http://www.php.net/manual/en/book.mbstring.php">book.mbstring.php</a>' )
	);

	var $_required_functions = array(
		array( 'function' => 'file_get_contents', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/function.file_get_contents.php">function.file_get_contents.php</a>' ),
		array( 'function' => 'simplexml_load_string', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/function.simplexml-load-file.php">function.simplexml-load-file.php</a>' ),
		array( 'function' => 'simplexml_load_file', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/function.simplexml-load-string.php">function.simplexml-load-string.php</a>' ),
		array( 'function' => 'dom_import_simplexml', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/dom.setup.php">dom.setup.php</a>' )
	);

	var $_recommended_functions = array();

	var $_required_classes = array(
		array( 'class' => 'SimpleXMLElement', 'info' => 'Check <a target="_blank" href="http://de.php.net/manual/en/book.simplexml.php">book.simplexml.php</a>' ),
		array( 'class' => 'ArrayObject', 'info' => 'Check <a target="_blank" href="http://www.php.net/manual/en/class.arrayobject.php">class.arrayobject.php</a>' )
	);

	var $_required_writable = array(
		array( 'path' => OGRAB_CACHE, 'info' => 'Please change the permission to allow writable the folder plugins/wp-pipes/cache/' ),
		array( 'path' => SITE_UPLOAD_DIR, 'info' => 'Please change the permission to allow writable your upload folder' )
	);

	var $_recommended_classes = array();

	function checkPHP() {
		return ! version_compare( PHP_VERSION, '5.3', '<' );
	}

	function checkWP() {
		global $wp_version;

		return ! version_compare( $wp_version, '3.8', '<' );
	}

	function checkSafeMode() {
		return ! ini_get( 'safe_mode' );
	}

	function checkAllow_url_fopen() {
		return ini_get( 'allow_url_fopen' );
	}

	function checkMemoryLimit() {
		$memory_limit = ini_get( 'memory_limit' );

		return $memory_limit == '-1' ? true : $this->_return_bytes( $memory_limit ) >= 33554432;
	}

	function checkRealpathCache() {

		if ( $this->_return_bytes( (string) ini_get( 'realpath_cache_size' ) ) / 1024 < 512 ) {
			return false;
		}

		return true;
	}

	function checkAPC() {
		return extension_loaded( 'apc' ) && class_exists( 'APCIterator' );
	}

	function _return_bytes( $size_str ) {
		switch ( substr( $size_str, - 1 ) ) {
			case 'M':
			case 'm':
				return (int) $size_str * 1048576;
			case 'K':
			case 'k':
				return (int) $size_str * 1024;
			case 'G':
			case 'g':
				return (int) $size_str * 1073741824;
			default:
				return $size_str;
		}
	}

	function checkRequirements() {
		$this->_required_results    = array();
		$this->_recommended_results = array();

		$result = $this->_checkRequired();
		$this->_checkRecommended();

		return $result;
	}

	function _checkRequired() {

		// check php
		$status = $this->checkPHP();
		$info   = __( 'WPPipes requires PHP 5.3+. Please upgrade your PHP version (<a href="http://www.php.net" target="_blank">php.net</a>).' );
		$this->_addRequiredResult( 'PHP 5.3+', $status, $info );

		$status = $this->checkWP();
		$info   = __( 'WPPipes requires WP 3.8+. Please upgrade your Wordpress version (<a href="http://www.wordpress.org" target="_blank">wordpress.org</a>).' );
		$this->_addRequiredResult( 'WP 3.8+', $status, $info );

		$status = $this->checkAllow_url_fopen();
		$info   = 'It is required to turn on PHP allow_url_fopen.';
		$this->_addRequiredResult( 'PHP allow_url_fopen', $status, $info );

		foreach ( $this->_required_extensions as $extension ) {
			$status = extension_loaded( $extension['extension'] );
			$this->_addRequiredResult( 'Extension: ' . $extension['name'], $status, $extension['info'] );
		}

		foreach ( $this->_required_functions as $function ) {
			$status = function_exists( $function['function'] );
			$this->_addRequiredResult( 'Function: ' . $function['function'], $status, $function['info'] );
		}

		foreach ( $this->_required_classes as $class ) {
			$status = class_exists( $class['class'] );
			$this->_addRequiredResult( 'Class: ' . $class['class'], $status, $class['info'] );
		}

		foreach ( $this->_required_writable as $path ) {
			$status = is_writable( $path['path'] );
			$this->_addRequiredResult( 'Path: ' . $path['path'], $status, $path['info'] );
		}

		foreach ( $this->_required_results as $return ) {
			if ( ! $return['status'] ) {
				return $return;
			}
		}

		return true;
	}

	function _checkRecommended() {

		foreach ( $this->_recommended_extensions as $extension ) {
			$status = extension_loaded( $extension['extension'] );
			$this->_addRecommendedResult( 'Extension: ' . $extension['name'], $status, $extension['info'] );
		}

		foreach ( $this->_recommended_functions as $function ) {
			$status = function_exists( $function['function'] );
			$this->_addRecommendedResult( 'Function: ' . $function['function'], $status, $function['info'] );
		}

		foreach ( $this->_recommended_classes as $class ) {
			$status = class_exists( $class['class'] );
			$this->_addRecommendedResult( 'Class: ' . $class['class'], $status, $class['info'] );
		}

		// check safe mode
		$status = $this->checkSafeMode();
		$info   = 'It is recommended to turn off PHP safe mode.';
		$this->_addRecommendedResult( 'PHP Safe Mode', $status, $info );

		$status = $this->checkMemoryLimit();
		$info   = 'It is recommended to set the php setting memory_limit to 32M or higher.';
		$this->_addRecommendedResult( 'PHP Memory Limit', $status, $info );

		$status = $this->checkRealpathCache();
		$info   = 'It is recommended to set the php <a target="_blank" href="http://www.php.net/manual/en/ini.core.php#ini.realpath-cache-size">realpath cache setting</a> realpath_cache_size to 512K or higher.';
		$this->_addRecommendedResult( 'PHP Realpath Cache', $status, $info );

		if ( extension_loaded( 'apc' ) ) {
			$status = $this->checkAPC();
			$info   = 'It is recommended to turn on APC (version 3.1.2+).';
			$this->_addRecommendedResult( 'Alternative PHP Cache (APC) enabled', $status, $info );
		}

		foreach ( $this->_recommended_results as $return ) {
			if ( ! $return['status'] ) {
				return $return['info'];
			}
		}

		return false;
	}

	function _addRequiredResult( $name, $status, $info = '' ) {
		$this->_required_results[] = compact( 'name', 'status', 'info' );
	}

	function _addRecommendedResult( $name, $status, $info = '' ) {
		$this->_recommended_results[] = compact( 'name', 'status', 'info' );
	}

	function displayResults() {
		?>

		<h3><?php echo __( 'WPPipes Requirements' ); ?></h3>
		<div><?php echo __( 'If any of the items below are highlighted in red, you should try to correct them. Failure to do so could lead to your WPPipes operation not functioning correctly.' ); ?></div>
		<table class="adminlist table table-bordered table-striped" width="100%">
			<thead>
			<tr>
				<th class="title"><?php echo __( 'Requirement' ); ?></th>
				<th width="20%"><?php echo __( 'Status' ); ?></th>
				<th width="60%"><?php echo __( 'Info' ); ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="3">Please visit
					<a href="http://wpbriz.com/wp-pipes-system-requirements/" target="_blank">here</a> to get more help
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			foreach ( $this->_required_results as $i => $req ) : ?>
				<tr <?php echo ( $i ++ % 2 ) ? 'class="alternate"' : ''; ?>>
					<td class="key"><?php echo $req['name']; ?></td>
					<td style="text-align: center;color:#0f0">
						<?php $style = $req['status'] ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>

						<?php if ( $req['status'] ): ?>
							<span class="badge badge-success"><i class="fa fa-check-circle"></i></span>
						<?php else: ?>
							<span class="badge badge-important"><i class="fa fa-times-circle"></i></span>
						<?php endif; ?>
					</td>
					<td>
						<span><?php echo $req['status'] ? '' : __( $req['info'] ); ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

	<?php
	}

}