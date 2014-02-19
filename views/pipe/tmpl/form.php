<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: form.php 121 2014-01-20 10:14:24Z phonglq $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

global $option;


$item = $this->item;

$pipes = @$item->pipes;
$name = filter_input( INPUT_GET, 'n' );
if ( ! $name ) {
//	$name = 'processors';
	$name = 'bs3';
}
//engine processor adapter
$action = 'admin.php?page=' . PIPES::$__page_prefix . '.pipe';
?>
<style type="text/css">
	.ob-cl {
		border: 1px solid #ff3300;
		width: 400px;
		float: left;
		margin: 2px;
	}

	.ob-box {
		background-color: #F8F8F8;
		border: 1px solid #cccccc;
		margin: 2px;
		padding: 10px;
	}

	.ob-box3 {
		border: 1px solid #cccccc;
		margin: 2px;
		padding: 10px;
	}

	.ob-box2 {
		border: 1px solid #cccccc;
		margin: 5 2px;
		padding: 2px;
		float: left;
	}

	.ob-box1 {
		border: 1px solid #cccccc;
		margin: 2px;
		padding: 2px;
	}

	.ob-title {
		border-bottom: 1px solid #dddddd;
		padding: 2px;
		background-color: #f0f0f0;
		color: #666666;
	}

	.ob-box1 div {
		margin: 2px;
		background-color: #fcfcfc;
	}

	.ob-pcl1 {
		width: 120px;
	}

	.ob-pcl2 {
		width: 700px;
	}

	.ob-pcl3 {
		width: 200px;
	}

	.ob-box-0 li {
		list-style: none;
		padding: 5px;
	}

	.ob-box-0 ul {
		margin: 2px;
	}

	.ob-box-0 table {
		border-spacing: 1px;
		border-collapse: separate;
	/ / font-size : 12 px;
	}

	.ob-box-0 label {
		float: left;
		min-width: 135px;
	}
</style>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate( $name ); ?>
	<input name="jform[id]" type="hidden" id="ogb_id" value="<?php echo $item->id; ?>" />
	<input name="task" id="task" type="hidden" value="" />
</form>