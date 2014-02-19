<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 139 2014-01-23 10:09:44Z phonglq $
 * @author               wppipes.com
 * @copyright            2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined('PIPES_CORE') or die('Restricted access');
$user = get_current_user_id();
$pipe_help_box = get_user_meta($user, 'pipes_help_box', true);
$check = 0;
if ($pipe_help_box == 1) {
    $check = 1;
}
?>
<style>
    @media screen and ( max-width: 782px ) {
        .column-hits, .column-created_time, .column-published, .column-id, .column-adapter, .column-engine {
            display: none;
        }
</style>

<!-- toolbar icon -->
<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br/></div>
<!-- toolbar -->
<h2>
    <!-- toolbar title -->
    <?php echo __('Pipes', 'pipes'); ?>
    <!-- buttons -->
    <a class="add-new-h2" href="admin.php?page=pipes.pipe">Add New</a>
	- or -
	<a class="add-new-h2" id="import-btn" href="#" onclick="jQuery('#import-panel').toggle();">Import</a>
</h2>

<div class="import-panel" id="import-panel" style="display: none;">
	<div class="import-content">
		<div class="postbox-container">
			<form method="post" enctype="multipart/form-data" class="wp-upload-form" action="">
				<strong><?php echo __( 'Import single or multiple Pipe(s) in .pipe format' ); ?></strong>

				<p class="install-help"><?php echo __(' If you have a pipe or multiple pipes in a .pipe format, you may install it by uploading it here.' ); ?></p>
				<input type="file" id="file_import" name="file_import" />
				<input type="hidden" name="task" value="import_from_file" />
				<input type="submit" name="install-plugin-submit" id="install-plugin-submit" class="button"
					   value="<?php echo __( 'Import' ); ?>" disabled="" />
			</form>
		</div>
		<div class="postbox-container">
			<div class="pipe-infobox">
				<?php echo __( '.pipe file is exported pipe(s). You can get .pipe file from:' ); ?><br />
				* <?php echo __( 'Export single or multiple pipes by yourself. <a href="#" onclick="jQuery(\'#contextual-help-link\').click();jQuery(\'#tab-link-my_help_tab1 a\').click();">Find out how</a>.' ); ?><br />
				* <?php echo __( 'Get it from <a href="http://foob.la/pipestore" target="_blank">Pipes Marketplace</a> (both free & paid available).' ); ?>
			</div>
		</div>
	</div>

</div>

<?php
echo PIPES::show_message();
?>

<div class="clear">
	<?php
	$screen = get_current_screen();
	$data = array();
	do_meta_boxes($screen, 'items_top', $data);
	?>
	<?php // $this->metabox_help();?>
</div>

<?php
$this->itemsTable->views();
?>

<form id="items-filter" method="get">
    <?php $this->itemsTable->search_box('Search', 'post'); ?>

    <!--<p class="search-box">
        <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
        <input type="search" id="post-search-input" name="s" value="">
        <input type="submit" name="" id="search-submit" class="button" value="Search Posts">
    </p>-->
    <?php
    $this->itemsTable->display();
    ?>
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
</form>
<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function ($) {
        var pipe_help = <?php echo $check; ?>;
        if (!pipe_help) {
            $('#pipes-items-helpbox-1').css('display', 'none');
            $('#pipes-items-helpbox-1-hide').prop("checked", false);
        } else {
            $('#pipes-items-helpbox-1-hide').prop("checked", true);
        }
        $('#pipes-items-helpbox-1-hide').click(function (event) {

            var selected = 0;
            if ($('#pipes-items-helpbox-1-hide').is(':checked')) {
                selected = 1;
            }

            ajax_update_meta(selected);

        })
        function ajax_update_meta(selected) {
            var user_id = <?php echo $user;?>;
            var url = '<?php echo admin_url() . 'admin.php?page=' . PIPES::$__page_prefix . '.pipes&task=update_meta';?>';
            $.ajax({
                url: url,
                type: 'POST',
                data: {select: selected, uid: user_id},
                success: function (resp) {
                }
            });
        }

        $('#dismiss_help_btn').click(function (event) {
            //alert('abc');
            event.preventDefault();
            var selected = 1;
            if ($('#pipes-items-helpbox-1-hide').is(':checked')) {
                $('#pipes-items-helpbox-1-hide').prop("checked", false);
                $('#pipes-items-helpbox-1').css('display', 'none');
                selected = 0;
            }
            ajax_update_meta(selected);
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
                    <iframe id="modal_iframe" src="" style="zoom:0.60" width="99.6%" height="600"
                            frameborder="0"></iframe>
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