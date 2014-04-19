<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: form_bs3.php 148 2014-01-25 04:47:00Z thongta $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

#$task	= filter_input(INPUT_GET, 'task');#JRequest::getCmd('task','edit');
//global $option;
$item = $this->item;
//echo $item->eParams.'<hr />';
//echo $item->aParams;


$inputs = $item->inputs;
$outputs = $item->outputs;
$pipes = @$item->pipes;
$templates = $this->templates;
$cpp = count( $pipes );
$selected = 'selected="selected"';
if ( ! $pipes ) {
	$pipes = array();
}
$ops_js = json_encode( $outputs );
$ips_js = json_encode( $inputs );
$pipes_js = json_encode( $pipes );
if ( ! $pipes_js ) {
	$pipes_js = 'Array()';
}
?>
<script type="text/javascript">
	<!--
	var ogb_ops = <?php echo $ops_js;?>;
	var ogb_ips = <?php echo $ips_js;?>;
	var ogb_pipes = <?php echo $pipes_js;?>;
	var ogb_id;
	var ogb_change_field = null;
	var ogb_order = 'a';
	var max_order = ogb_pipes.length;
	var ogb_loade = 1;
	var ogb_loada = 1;
	var pload = [];
	var phload = [];
	jQuery(document).ready(function () {
		ogb_id = obgid('ogb_id').value;
//		ogb_loadAdapter(ogb_id);
//		ogb_loadEngine(ogb_id);
//		ogb_load_process_params();
		ogb_update_ips();
	}, true);
	/* @TODO: to Tung, please check to see if we can remove below code blog */
	/*window.onbeforeunload = function(){
	 var task = obgid('task').value;
	 if(ogb_need_save){
	 if(task==''){
	 alert("WPPipes need save the changed, please click Ok and wait ...!");
	 Joomla.submitbutton('save');
	 return false;
	 }else if(task=='cancel'){
	 alert("WPPipes need save the changed, please click Ok then click Save!");
	 obgid('task').value = '';
	 return false;
	 }
	 }
	 }*/
	//-->
	function submitbutton(form, task) {
		form.task.value = task;
		form.submit();
		return false;
	}
	function switch_pipe(value) {
		var answer = confirm('Are you sure? Please review and save all yours changes before switching to other pipe!');
		if (answer) {
			location = value;
		}
	}
	function set_template(el) {
		var href = el.getAttribute('data-href');
		var answer = confirm('Warning: After you allow this action, all configures you set before will be overwritten! Do you want to continue?');
		if (!answer) {
			return;
		} else {
			location = href;
		}
	}
	function delete_template(el, filename) {
		var answer = confirm('Are you sure?');
		if (!answer) {
			return false;
		} else {
			var li_el = el.parentNode;
			var url = ogb_be_url + 'delete_template';
			jQuery.ajax({
				url    : url,
				type   : 'POST',
				data   : {filename: filename},
				success: function (resp) {
					if (resp != 'false') {
						li_el.parentNode.removeChild(li_el);
						alert(resp);
					}
				}
			});
		}
	}
	jQuery(document).ready(function () {
		var config = {
			".chosen-select": {}
		};
		for (var selector in config) {
			jQuery(selector).chosen();
		}
		;
		jQuery('.text-muted').tooltip();
	})
</script>

<h2>
	<?php
	if ( ! $item->id ) { // New Pipe
		echo __( 'Add New Pipe', 'pipes' );
	} else { // Edit a Pipe
		echo __( 'Edit Pipe', 'pipes' );
		$other_pipes = $this->other_pipes;
		?>
		<select name="surf"
		        onchange="switch_pipe(document.adminForm.surf.options[document.adminForm.surf.selectedIndex].value);"
		        value="GO" size="1" class="" style="margin-bottom:4px;">
			<option value="1">- switch to other pipe -</option>
			<?php foreach ( $other_pipes as $other ): ?>
				<option
					value="admin.php?page=pipes.pipe&id=<?php echo $other['id']; ?>"><?php echo $other['id'] . ' - ' . $other['name']; ?></option>
			<?php endforeach; ?>
		</select>
		<?php echo __( '- or -' ); ?>
		<?php
		echo ' <a class="add-new-h2" href="admin.php?page=pipes.pipe">' . __( 'Add New', 'pipes' ) . '</a>';
	}
	?>
</h2>


<!-- Welcome -->
<div class="foobla" style="padding-top:15px;">
	<div class="alert alert-info alert-dismissable hidden">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h3><?php echo __( 'The first time here?', 'pipes' ); ?></h3>

		<div class="row">
			<div class="col-md-4">
				<h4>What is a Pipe?</h4>

				<p>Each Pipe is for collecting data/posts from SOURCE >>> process by PROCESSORS (if needed) >>> pass to
					DESTINATION.<br />
					You can create as much Pipe as you want.</p>
			</div>
			<div class="col-md-4">
				<h4>Step by step to configure a Pipe</h4>
				<a href="//www.youtube.com/v/TO3g-_wErEI?autoplay=1&amp;vq=hd1080" class="btn btn-default btn-lg">
					<span class="fa fa-youtube-play" title=""></span> 5-min Video Tutorial
				</a>
			</div>
			<div class="col-md-4">
				<h4>Learn more</h4>
				<a href="//www.youtube.com/v/TO3g-_wErEI?autoplay=1&amp;vq=hd1080" class="btn btn-default btn-lg">
					<span class="fa fa-youtube-play" title=""></span> User Manual
				</a>
				or
				<a href="//www.youtube.com/v/TO3g-_wErEI?autoplay=1&amp;vq=hd1080" class="btn btn-default btn-lg">
					<span class="fa fa-youtube-play" title=""></span> Knowledge Base
				</a>
			</div>
		</div>
	</div>
</div>

<div class="foobla">
<?php
// Show arrow instruction for [Test this Pipe] button
if ( isset( $_SESSION['PIPES']['messages'] ) && count( $_SESSION['PIPES']['messages'] ) ) {
	echo '<img src="' . plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/images/test-pipe.png" width="300" height="83" style="position:absolute;right:50px;top:45px;" />';
}
?>

<?php echo PIPES::show_message(); ?>

<div id="container-collapse" class="container-collapse"></div>

<div class="row" style="padding-bottom: 20px;">
	<!-- Pipe Title & Publish Status -->
	<div class="col-md-6">
		<a href="#" style="display:none;"><?php echo __( 'none' ); ?></a>

		<div class="input-group input-group-lg col-xs-12">
			<span class="input-group-addon"><?php echo sprintf( __( 'Pipe#%s' ), $item->id ); ?></span>
			<input placeholder="<?php echo __( 'Give this Pipe a name, whatever, just a name' ); ?>"
			       onfocus="this.select();" type="text" class="form-control" id="pipe_title" required="true"
			       value="<?php echo $item->name; ?>" id="jform_title" name="jform[name]" />
				<span class="input-group-addon">
					<input type="checkbox" name="jform[published]" value="1" <?php if ( $item->published == 1 ) {
						echo 'checked="checked"';
					} ?>/>
				</span>
		</div>

	</div>

	<!-- Toolbar / Functional Buttons -->
	<div class="col-md-6" style="position: inherit">
		<div class="btn-toolbar pull-right" id="toolbar" data-spy="affix1" data-offset-top="60"
		     data-offset-bottom="200">
			<div id="toolbar-schedule" class="btn-wrapper">
				<div class="btn-group">
					<?php
					if ( $item->adapter_params ) {
						$adapter_params = json_decode( $item->adapter_params );
						$schedule = $adapter_params->schedule;
					} else{
						$schedule = "0";
					}
					echo '<select name="adapter[params][schedule]" class="chosen-select" id="adapter_params_schedule">
                    	<option value="">--Set schedule this pipe--</option>
                        <option ' . ( ( !$schedule ) ? 'selected="selected"' : '' ) . ' value="0">Global</option>
                        <option ' . ( ( $schedule === 'i1' ) ? 'selected="selected"' : '' ) . ' value="i1">1 minutes</option>
    					<option ' . ( ( $schedule == 'i5' ) ? 'selected="selected"' : '' ) . ' value="i5">5 minutes</option>
    					<option ' . ( ( $schedule == 'i10' ) ? 'selected="selected"' : '' ) . ' value="i10">10 minutes</option>
    					<option ' . ( ( $schedule == 'i15' ) ? 'selected="selected"' : '' ) . ' value="i15">15 minutes</option>
    					<option ' . ( ( $schedule == 'i20' ) ? 'selected="selected"' : '' ) . ' value="i20">20 minutes</option>
    					<option ' . ( ( $schedule == 'i25' ) ? 'selected="selected"' : '' ) . ' value="i25">25 minutes</option>
    					<option ' . ( ( $schedule == 'i30' ) ? 'selected="selected"' : '' ) . ' value="i30">30 minutes</option>
    					<option ' . ( ( $schedule == 'h1' ) ? 'selected="selected"' : '' ) . ' value="h1">1 hour</option>
    					<option ' . ( ( $schedule == 'h2' ) ? 'selected="selected"' : '' ) . ' value="h2">2 hours</option>
    					<option ' . ( ( $schedule == 'h3' ) ? 'selected="selected"' : '' ) . ' value="h3">3 hours</option>
    					<option ' . ( ( $schedule == 'h4' ) ? 'selected="selected"' : '' ) . ' value="h4">4 hours</option>
    					<option ' . ( ( $schedule == 'h6' ) ? 'selected="selected"' : '' ) . ' value="h6">6 hours</option>
    					<option ' . ( ( $schedule == 'h8' ) ? 'selected="selected"' : '' ) . ' value="h8">8 hours</option>
    					<option ' . ( ( $schedule == 'h12' ) ? 'selected="selected"' : '' ) . ' value="h12">12 hours</option>
    					<option ' . ( ( $schedule == 'h24' ) ? 'selected="selected"' : '' ) . ' value="h24">24 hours</option>
    					</select>';
					?>
				</div>
			</div>
			<div class="btn-wrapper" id="toolbar-apply">
				<div class="btn-group">
					<button onclick="submitbutton(this.form,'apply')" class="btn btn-default btn-lg">
						<span class="fa fa-save"></span>
						<?php echo __( 'Save' ); ?>
					</button>
					<button type="button" class="btn btn-default  btn-lg dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="admin.php?page=<?php echo PIPES::$__page_prefix ?>.pipes&task=export_to_share&id=<?php echo $item->id; ?>"
							   class="btn-pipes-export">
								<span class="fa fa-download"></span>
								<?php echo __( 'Export to .pipe file' ); ?>
							</a>
						</li>
						<li>
							<a href="admin.php?page=<?php echo PIPES::$__page_prefix ?>.pipes&task=export_to_share&set_template=1&id=<?php echo $item->id; ?>"
							   class="btn-pipes-export">
								<span class="fa fa-save"></span>
								<?php echo __( 'Export as a template' ); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="btn-wrapper" id="toolbar-postu">
				<div class="btn-group">
					<button id="openBtn"
					        href="admin.php?page=<?php echo PIPES::$__page_prefix ?>.pipe&task=post&id=<?php echo $item->id; ?>"
					        class="btn btn-default btn-lg btn-pipes-post">
						<i class="fa fa-flask" title=""></i> <?php echo __( 'Test this Pipe' ); ?>
					</button>
					<button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="admin.php?page=<?php echo PIPES::$__page_prefix ?>.pipe&task=post&id=<?php echo $item->id; ?>&u=1"
							   class="btn-pipes-post">
								<span class="fa fa-flask" title=""></span> <?php echo __( 'Test in Update mode' ); ?>
							</a>
						</li>
						<!--							<li>-->
						<!--								<p class="alert alert-info" style="margin: 0 10px;">-->
						<!--									--><?php //echo __( 'These buttons are for testing purpose only. The cronjob/schedule will handle this action automatically for you.' ); ?>
						<!--									<br />-->
						<!--									--><?php //echo __( 'Both Test this Pipe & Test in Update mode will run the Pipe. The Test in Update mode will overwrite existing data (if any).' ); ?>
						<!--									<br />-->
						<!--									--><?php //echo __( 'The most usage button will take the top position.' ); ?>
						<!--								</p>-->
						<!--							</li>-->
					</ul>
				</div>
			</div>

			<?php /* we will turn this back later ?>
				<div class="btn-wrapper" id="toolbar-iwant">
					<a type="button" class="btn btn-info btn-xs dropdown-toggle" onclick="display_form();" style="text-decoration: none;">
						<span class="fa fa-heart-o"></span>&nbsp;<?php echo __( 'I want ...' ); ?>
					</a>

					<div class="dropdown-iwant" id="dropdown-iwant" style="display:none;width:400px">
						<a id="iwant-close" class="btn btn-link btn-micro" href="javascript:void()" onclick="document.getElementById('dropdown-iwant').style.display='none'"><i class="fa fa-times-circle"></i></a>
						<h4 style="text-align: left;">
							<?php echo __( 'Tell foobla what you want' ); ?>
						</h4>

						<div class="form-group">
							<textarea rows="5" class="form-control"></textarea>
						</div>
						<p id="iwanto_thanks" class="alert alert-info" style="font-size:11px;text-align:left;margin-bottom:10px">
						</p>

						<div class="form-group pull-right">
							<a id="iw_btn" class="btn btn-primary" href="#"><i class="fa fa-envelope-o"></i> <?php echo __( 'Send to foobla' ); ?>
							</a>
						</div>
					</div>
				</div>
 				<?php */
			?>
		</div>
	</div>
</div>

<div class="row" style="position: relative;">
	<div id="wppipes-connect-source-destination" style="position: absolute; left: 49.4%;top:100px;">
		<i class="fa fa-chevron-right fa-2x"></i></div>
	<!-- Engine / Source -->
	<div class="col-md-6">
		<img src="<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/images/pipe-step-2.png'; ?>"
		     width="145" height="83" />

		<div class="panel panel-success" style="position: relative;">
			<div class="panel-heading">
				<ul class="nav nav-pills pull-right" id="engineTab">
					<li class="active"><a href="#engine-basic" data-toggle="tab"><?php echo __( 'Options' ); ?></a></li>
					<li><a href="#engine-advanced" data-toggle="tab"><?php echo __( 'More Options' ); ?></a></li>
					<!--						<li><a href="#engine-help" data-toggle="tab"><i class="fa fa-question-circle"></i></a></li>-->
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="form-inline input-lg" style="float:none;position: absolute;left: 0px;top: 0px;">
					<?php echo $item->engines; ?>
				</div>

				<div id="ogb-engine-param">
					<?php
					if ( $item->engine != '' ) {
						echo $item->eParams;
					} else {
						echo __( 'Please select a Source. After that, specific fields will be displayed in the <span class="label label-success">Source Output</span> area.' );
					}
					?>
				</div>
			</div>
		</div>
	</div>

	<!-- Adapter / Destination -->
	<div class="col-md-6">
		<img src="<?php echo plugin_dir_url( '' ) . basename( PIPES_PATH ) . '/assets/images/pipe-step-3.png'; ?>"
		     height="83" width="145" />

		<div class="panel panel-warning" style="position: relative;">
			<div class="panel-heading">
				<ul class="nav nav-pills pull-right" id="adapterTab">
					<li class="active"><a href="#adapter-basic" data-toggle="tab"><?php echo __( 'Options' ); ?></a>
					</li>
					<li><a href="#adapter-advanced" data-toggle="tab"><?php echo __( 'More Options' ); ?></a></li>
					<!--						<li><a href="#adapter-help" data-toggle="tab"><i class="fa fa-question-circle"></i></a></li>-->
				</ul>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="form-inline input-lg" style="float:none;position: absolute;left: 0px;top: 0px;">
					<?php echo $item->adapters; ?>
				</div>

				<div id="ogb-adapter-param">
					<?php
					if ( $item->adapter != '' ) {
						echo $item->aParams;
					} else {
						echo __( 'Please <strong>select a Destination</strong>. After that, specific fields will be displayed in the <span class="label label-warning">Destination Input</span> area.' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Inherits -->
<?php
/* this feature will be turned on later
if ( $item->inherit > 0 ) {
	$edit  = "index.php?option=com_wppipes&controller=items&task=edit&cid[]={$item->inherit}";
	$aedit = '[ <a href="' . $edit . '">' . __( 'Edit' ) . '</a> ]';
	echo '<div align="center"><h4>' . sprintf( __( 'COM_OBGRABBER_INHERITS_PIPEFORM' ), $item->inherit ) . ' ' . $aedit . '</h4></div>';

	return;
}
*/
?>

<!-- Fields Matching -->
<div class="row">
<div id="fields_matching" class="col-md-12">
<div class="text-center">
	<h3 style="margin-top:0;">
		<?php echo __( 'Fields Matching' ); ?>
		<?php /* @TODO: Tung - because of this function is not really work for now, I have to hide it ?>
		 * <fieldset style="visibility:<?php echo ( $item->engine == 'rssreader' AND ( $item->adapter == 'post' OR $item->adapter == 'content' ) ) ? 'visible' : 'hidden'; ?>;display: inline-block;" rel="tooltip" data-original-title="Default set of processors: slug, duplicate, get_fulltext, image, cut_introtext, get_meta" id="jform_set_default_obg" class="radio btn-group btn-group-yesno">
		 * <input type="radio" id="jform_set_default_obg_<?php echo $item->id; ?>" name="jform[input_default]" value="slug-duplicate-get_fulltext-image-cut_introtext" />
		 * <label for="jform_set_default_obg_<?php echo $item->id; ?>" class="btn btn-info"><?php echo __( 'Pre-made SET#1' ); ?></label>
		 * </fieldset>
		 * <?php */
		?>
		<?php if ( count( $templates ) > 0 ): ?>
			<div class="btn-group dropup">
				<button type="button" class="btn btn-default"><?php echo __( 'Set template' ); ?></button>
				<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
					<span class="sr-only"><?php echo __( 'Libraries' ); ?></span>
				</button>
				<ul class="dropdown-menu pull-right">
					<?php foreach ( $templates as $template ): ?>
						<li>
							<a style="display: inline;" onclick="set_template(this);" class="use_this_template" href="#"
							   data-href="admin.php?page=pipes.pipes&task=import_from_file&file_name=<?php echo $template->filename; ?>&id=<?php echo $item->id; ?>">
								<?php echo '<strong>' . $template->filename . '</strong> <small>(' . __( 'Engine' ) . ': ' . $template->engine . ', ' . __( 'Adapter' ) . ': ' . $template->adapter . ')</small>'; ?>
							</a>
									<span style="cursor: pointer; text-indent: -20px;" onclick="delete_template(this,'<?php echo $template->filename; ?>');">
										<i class="fa fa-trash-o" style="color:#b94a48"></i>
									</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</h3>
	<a href="javascript:void(0);" onclick="ogb_config_as_default(this);" title="Config as Default"
	   style="display:none;">[ Get Default Config ]</a>
</div>

<div class="clearfix"></div>
<div class="row" style="position:relative;">
	<div style="position: absolute; left: 16%;top:18px;"><i class="fa fa-chevron-right fa-2x"></i></div>
	<div style="position: absolute; left: 74.3%;top:18px;"><i class="fa fa-chevron-right fa-2x"></i></div>

	<!-- Source Output Box -->
	<div class="col-md-2">
		<div class="panel panel-success">
			<div class="panel-heading">
				<h4><?php echo __( 'Source Output' ); ?></h4>
			</div>

			<div class="panel-body">
				<ul class="unstyled oblistfield" id="ob-oe">
					<li><?php echo __( 'Please <span class="label label-success">select a Source</span>. After that, specific fields from the selected Source will be displayed here.' ); ?></li>
				</ul>
			</div>

			<div class="panel-footer">
				<p>This area is the output of each item from Source.</p>

				<p>[so] is the short-form of the Source Output.</p>
			</div>
		</div>
	</div>

	<!-- Processors Box -->
	<div class="col-md-7">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>
					<?php echo __( 'Processors' ); ?>
					<small><?php echo __( 'Cooking fields with processors' ); ?>
						<a href="http://wppipes.com/understanding-processors/" target="_blank" rel="tooltip"
						   data-original-title="<?php echo __( 'Understanding processors' ); ?>"><i
								class="fa fa-question-circle"></i></a>
					</small>
					<p class="text-right pull-right">
						<a onclick="refresh_mapping();" class="btn btn-danger btn-xs">
							<span class="fa fa-refresh"></span>
							<?php echo __( 'Clear added processors' ); ?>
						</a>
					</p>
				</h4>
			</div>

			<ul id="ogb_list_processor" class="list-group">
				<!-- PROCESSORS LIST HEADER -->
				<li class="list-group-item">
					<div class="col-md-4"><span><strong><?php echo __( 'Input Fields' ); ?></strong></span>
					</div>
					<div class="col-md-5"><span><strong><?php echo __( 'Processor' ); ?></strong></span></div>
					<div class="col-md-3"><span><strong><?php echo __( 'Output Fields' ); ?></strong></span>
					</div>
					<div class="clearfix"></div>
				</li>

				<!-- ADDED PROCESSORS LIST -->
				<?php $t = 0;
				for ( $i = 0; $i < $cpp; $i ++ ) {
					$pipe = $pipes[$i];
					$t    = ( $t + 1 ) % 2;
					?>
					<li class="list-group-item" id="pipes_processor-<?php echo $pipe->id; ?>">
						<div class="col-md-4">
							<ul class="unstyled" id="ob-ip-<?php echo $i; ?>">
								<li><i><? echo __( 'Loading' ); ?></i></li>
							</ul>
						</div>
						<div class="col-md-5">
									<span style="float: left;"><a href="javascript:void(0);" title="Settings"
									                              onclick="showParams(this,<?php echo $pipe->id; ?>);"><i
												class="fa fa-expand"></i></a>
									&nbsp;</span>
							<strong style="color:#006600;"><?php echo $pipe->name; ?></strong>
                                    <span style="float: left;">&nbsp;<a href="javascript:void(0);" title="Help"
                                                                        onclick="showHelps(this,<?php echo $pipe->id; ?>);"><i
			                                    class="fa fa-question-circle"></i></a>
                                    &nbsp;</span>
									<span style="float:right">
									<a href="javascript:void(0);" title="Remove"
									   onclick="remove_pipe(<?php echo $pipe->id; ?>)"><i class="fa fa-trash-o"
									                                                      style="color:#b94a48"></i></a>
									</span>
							</br>
							<textarea class="form-control input-sm" name="note" rows="1"
							          id="note_<?php echo $pipe->id; ?>"
							          onblur="savenote(<?php echo $pipe->id; ?>)"
							          placeholder="Write note here!"><?php echo $pipe->note; ?></textarea>
						</div>
						<div class="col-md-3">
							<ul class="unstyled" id="ob-op-<?php echo $i; ?>">
								<li><i><? echo __( 'Loading' ); ?></i></li>
							</ul>
						</div>
						<div class="col-md-12 well well-small" style="display:none; margin-left:0;"
						     id="ob-param-<?php echo $pipe->id; ?>">
							<i><? echo __( 'Loading' ); ?></i>
						</div>
						<div class="col-md-12 well well-small" style="display:none; margin-left:0;"
						     id="ob-param-help-<?php echo $pipe->id; ?>">
							<i><? echo __( 'Loading' ); ?></i>
						</div>
						<div class="clearfix"></div>
					</li>
				<?php
				}
				?>

				<!-- NEW PROCESSOR DROPDOWN BOX -->
				<li class="list-group-item">
					<?php //echo $this->getPMode($item); ?>
					<?php echo $item->processors; ?>
					<span style="display:none;">
								<b>Order:</b> <input type="text" id="npp_order" name="npp_order" size="3"
								                     value="<?php echo( $cpp > 0 ? $cpp : 0 ); ?>" style="float:none;"
								                     onfocus="this.select();" />
								<a href="javascript:void(0);" title="Add new processor" onclick="addProcessor();">[ Add
									]</a>
							</span>

					<div class="clearfix"></div>
				</li>
			</ul>

			<div class="panel-footer">
				<p>This area is for cooking more fields for the Destination Input in the case Source Output
					doesn't provide enough fields or exact fields for it.</p>

				<p>[pi] is the short-form of Processor Input. [po] is the short-term of Processor Output.</p>

				<p>You can add as many Processors in many times for your cooking job.</p>

				<p>Click [Click me] button to select an available input field. Processor will generate several
					output fields.</p>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h4><?php echo __( 'Destination Input' ); ?></h4>
			</div>

			<div class="panel-body">
				<ul class="unstyled" id="ob-ia">
					<li><?php echo __( 'Please <span class="label label-warning">select a Destination</span>. After that, specific fields from the selected Destination will be displayed here.' ); ?></li>
				</ul>
			</div>

			<div class="panel-footer">
				<p>This area is the input for each item in Destination. [di] is the short-form of the
					Destination Input.</p>

				<p>Click [Click me] button to select a particular input field from Source Output Fields and
					Processors Output Fields.</p>
			</div>
		</div>
	</div>
</div>
<!-- Mapping Box -->
<div class="ob-list-output" id="ogb-list-output">
	<div class="panel panel-info">
		<div class="panel-heading">
                    <span class="ogb-close hasTip" title="close this box" onclick="ogb_closeListOp()"><i
		                    class="fa fa-times-circle fa-lg"></i></span>
			<strong>
                        <span id="change-field" onclick="ogb_update_field('','');">[ <?php echo __( 'Leave blank' ); ?>
	                        ]</span>
			</strong>

			&nbsp;- or <?php echo __( 'Select a field to map with this field.' ); ?>
		</div>
		<div class="panel-body">
			<div id="ob-oelist" style="">
				<i>None</i>
			</div>
			<div id="ob-oplist" style="">
				<i>&nbsp;&nbsp;None</i>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">
					<?php echo sprintf( __( 'Pipe#%s: Manually Running Pipe Now...' ), $item->id ); ?><br />
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
<div id="dvLoading" style="display: none;"><i class="fa fa-spinner fa-spin fa-5x"></i></div>

<script type="text/javascript">
	/* modal buttons: Manual Post & update and Manual Post*/
	jQuery(document).ready(function ($) {
		$('.btn-pipes-post').click(function (event) {
			event.preventDefault();
			save_b4_post();
			var ahref = $(this).attr('href');
			$('#myModal').on('shown.bs.modal',function (e) {
				$('#modal_iframe').attr("src", ahref);
			}).on("show.bs.modal", function () {
				$(this).find(".modal-dialog").css("height", '600px').css("width", '700px').css('margin-top', '100px');
			});
			$('#myModal').modal({show: true});
			$('#myModal').on('hidden.bs.modal', function () {
				$('#modal_iframe').attr("src", '');
			});
		});
	});
</script>