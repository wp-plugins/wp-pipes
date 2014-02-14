<?php
/**
 * @package          WP Pipes plugin - PIPES
 * @version          $Id: default.php 121 2014-01-20 10:14:24Z phonglq $
 * @author           wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license          GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );
?>
<script>
	/* Collapse Left Menu to save more room */
	jQuery(document).ready(function(){jQuery("body").addClass("folded")})
</script>
<!-- toolbar icon -->
<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br></div>
<!-- toolbar -->
<h2>
<!-- toolbar title -->
Item</h2>

<form id="item" name="item" method="post" action="admin.php">
	<table width="100%" cellspacing="0">
		<tr>
			<td width="45%" valign="top">
				<div id="poststuff">
					<div class="postbox">
						
						<h3><b>Connection Info</b></h3>
						<div class="inside">
							<p><b>Title: </b><br>
							<input type="text" name="title" size="60" value="<?php echo $this->item->title; ?>"></p>
							<p><b>Description: </b><br>
							<textarea name="description" rows="5" cols="52"><?php echo $this->item->description; ?></textarea></p>
							<p><b>Published:</b> 
							<select name="published">
								<option value="0">No</option>
								<option value="1">Yes</option>
							</select></p>
						</div>
					</div>
				</div>
			</td>
			<td valign="top">
				<div id="poststuff">
					<div class="postbox">
						<h3><b>Params</b></h3>
						<div class="inside">
							<?php 
// 							print_r($this);
							$fieldsets = $this->form->getFieldsets('params');
							foreach ($fieldsets as $name => $fieldset) :
								echo '<div class="tab-pane" id="options-'.$name.'">';
								$label = !empty($fieldset->label) ? $fieldset->label : 'COM_MODULES_'.$name.'_FIELDSET_LABEL';
								if (isset($fieldset->description) && trim($fieldset->description)) :
									echo '<p class="tip">'.JText::_($fieldset->description).'</p>';
								endif;
								?>
											<?php $hidden_fields = ''; ?>
											<?php foreach ($this->form->getFieldset($name) as $field) : ?>
												<?php if (!$field->hidden) : ?>
												<div class="control-group">
													<div class="control-label">
														<?php echo $field->label; ?>
													</div>
													<div class="controls">
														<?php echo $field->input; ?>
													</div>
												</div>
												<?php else :?>
												<?php $hidden_fields .= $field->input; ?>
												<?php endif; ?>
											<?php endforeach; ?>
											<?php echo $hidden_fields; ?>
										
											<?php echo '</div>'; // .tab-pane div ?>
										<?php 
							endforeach;
						?>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="button" class="button-primary" value="Save"/>
	<input type="button" class="button-secondary" value="Save & Close"/>
	<input type="button" class="button-secondary" value="Cancel"/>
	
</form>