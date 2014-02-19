<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: post.html.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
$id = filter_input( INPUT_GET, 'id' );
$params_pipes = ogb_common::get_param_pipe( $id, 'image' );
foreach ( $params_pipes as $params_pipe ) {
	$param_array = json_decode( $params_pipe->params );
	if ( @$param_array->empty_folder ) {
		ogb_common::empty_folder( $param_array->image_local );
	}
}

$host = '';
?>
<html>
<head>
	<link rel='stylesheet' id='pipes-bootstrap-min-css' href='<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ); ?>/assets/css/bootstrap.min.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo get_site_url(); ?>/wp-includes/js/jquery/jquery.js?ver=1.10.2'></script>
	<script type='text/javascript' src='<?php echo get_site_url(); ?>/wp-includes/js/jquery/jquery-migrate.js?ver=1.2.1'></script>
	<script src="<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/ogb-lib.js'; ?>"></script>
	<script src="<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/js/post.js'; ?>"></script>
	<script type="text/javascript">
		<?php echo "ogbHost='{$host}';ogb_id={$id}".(isset($_GET['u'])?',ogb_ud=true':'').';';?>
		window.addEventListener('load', function () {
			ogbPost.onload();
		}, true);
	</script>
	<script type='text/javascript' src='<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ); ?>/assets/js/bootstrap.min.js'></script>
</head>
<body>
<div class="foobla">
	<div id="ogb_res">&nbsp;</div>
</div>
</body>
</html><?php exit(); ?>