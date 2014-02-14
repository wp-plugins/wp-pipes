<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 139 2014-01-23 10:09:44Z phonglq $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
?>
<style>
	@media screen and ( max-width: 782px ) {
		.column-hits, .column-created_time, .column-published, .column-id, .column-adapter, .column-engine {
			display: none;
		}
</style>

<!-- toolbar icon -->
<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br /></div>
<!-- toolbar -->
<h2>
	<!-- toolbar title -->
	<?php echo __( 'Pipes', 'pipes' ); ?>
	<!-- buttons -->
	<a class="add-new-h2" href="admin.php?page=pipes.pipe">Add New</a>
</h2>
<?php
echo PIPES::show_message();
?>
<!--<ul class="subsubsub">
	<li class="all"><a href="default.php?post_type=post" class="current">All <span class="count">(1)</span></a> |</li>
	<li class="publish"><a href="default.php?post_status=publish&amp;post_type=post">Published <span class="count">(1)</span></a></li>
</ul>-->
<?php
$this->itemsTable->views();
?>

<form id="items-filter" method="get">
	<div class="clear">
		<?php
		$screen = get_current_screen();
		$data = array();
		//	do_meta_boxes($screen, 'items_top', $data);
		?>
		<?php // $this->metabox_help();?>
	</div>
	<?php $this->itemsTable->search_box( 'Search', 'post' ); ?>

	<!--<p class="search-box">
		<label class="screen-reader-text" for="post-search-input">Search Posts:</label>
		<input type="search" id="post-search-input" name="s" value="">
		<input type="submit" name="" id="search-submit" class="button" value="Search Posts">
	</p>-->
	<?php
	$this->itemsTable->display();
	?>
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
</form>
<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready(function ($) {
		$('#pipes-items-helpbox-1-hide').change(function (event) {
			if (this.checked) {
				$('#welcome-panel').removeClass('hidden');
			} else {
				$('#welcome-panel').addClass('hidden');
			}
		})


		$('#dismiss_help_btn').click(function (event) {
			alert('abc');
			event.preventDefault();
			console.log($('#pipes-items-helpbox-1-hide').is(':checked'));
			if ($('#pipes-items-helpbox-1-hide').is(':checked')) {
				$('#welcome-panel').removeClass('hidden');
			} else {
				$('#welcome-panel').addClass('hidden');
			}
		})
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
		postboxes.add_postbox_toggles('<?php echo $screen->id; ?>');

	});
	//]]>
</script>
<!-- Modal -->
<div class="foobla">
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">

					</h4>
				</div>
				<div class="modal-body">
					<iframe id="modal_iframe" src="" style="zoom:0.60" width="99.6%" height="600" frameborder="0"></iframe>
				</div>
				<!--      <div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save changes</button>
					  </div>-->
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!-- /.modal -->
</div>

<script type="text/javascript">
	/* modal buttons: Manual Post & update and Manual Post*/
	jQuery(document).ready(function ($) {
		$('.btn-pipes-post').click(function (event) {
			event.preventDefault();
			var ahref = $(this).attr('href');
			var id_item = $(this).data("id");
			$('#myModalLabel').text('Pipe#' + id_item + ': Manually Running Pipe Now...');
			$('#myModal').on('shown.bs.modal',function (e) {
				$('#modal_iframe').attr("src", ahref);
			}).on("show.bs.modal", function () {
				$(this).find(".modal-dialog").css("height", '600px').css("width", '700px').css('margin-top', '100px');
			});
			$('#myModal').modal({show: true});
		});
	});
</script>