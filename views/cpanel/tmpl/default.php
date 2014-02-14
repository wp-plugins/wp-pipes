<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: default.php 170 2014-01-26 06:34:40Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
$data	= array('My Data 1', 'My Data 2', 'Available Data 1');
$screen = get_current_screen();
?>
<div id="howto-metaboxes-general" class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Metabox Showcase Plugin Page</h2>
	<form action="admin-post.php" method="post">
		<?php wp_nonce_field('howto-metaboxes-general'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
		<input type="hidden" name="action" value="save_howto_metaboxes_general" />

		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div id="side-info-column" class="inner-sidebar">
				<?php do_meta_boxes($screen, 'side', $data); ?>
			</div>
			<div id="post-body" class="has-sidebar">
				<div id="post-body-content" class="has-sidebar-content">
					<?php do_meta_boxes($screen, 'normal', $data); ?>
					<?php do_meta_boxes($screen, 'additional', $data); ?>
					<p>
						<input type="submit" value="Save Changes" class="button-primary" name="Submit"/>	
					</p>
				</div>
			</div>
			<br class="clear"/>

		</div>	
	</form>
</div>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
		postboxes.add_postbox_toggles('<?php echo $screen->id;#echo PIPES::$__pagehook; ?>');
	});
	//]]>
</script>