<?php
/**
 * @package              WP Pipes plugin - PIPES
 * @version              $Id: default.php 170 2014-01-26 06:34:40Z thongta $
 * @author               wppipes.com
 * @copyright        2014 wppipes.com. All rights reserved.
 * @license              GNU/GPL v3, see LICENSE
 */
defined( 'PIPES_CORE' ) or die( 'Restricted access' );

wp_enqueue_style( 'pipes-font-awesome-css' );
wp_enqueue_style( 'pipes-bootstrap-min' );
wp_enqueue_style( 'pipes-bootstrap-extended' );
wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'pipes-bootstrap-min' );
require_once( OBGRAB_HELPERS . 'requirements.php' );
$requirements = new AppRequirements;
$requirements->checkRequirements();
?>

<h2><?php echo __( 'WP Pipes plugin Settings' ); ?></h2>

<?php
echo PIPES::show_message( false );
?>
<div id="col-container">
	<div class="welcome-panel">
		<form method="post" action="">
			<table class="form-table">
				<?php
				foreach ( $this->configs->items as $setting ) {
					echo '<tr valign="top"';
					// Start at settings seems to be not necessary
					if ($setting->option_name == 'pipes_start_at') {
						echo ' class="hidden"';
					}
					echo '>';

					switch ( $setting->option_name ) {
						case 'pipes_cronjob_active':
							echo '<th scope="row">' . __( 'Cronjob Active' ) . '</th>';
							echo '<td>';

							echo '<fieldset><legend class="screen-reader-text"><span>Cronjob Active</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want to run my Pipes automatically when someone access my Wordpress site.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, I will create a cronjob task myself to run the script <a href="'.get_site_url().'/?pipes=cron&task=callaio" target="_blank">'.get_site_url().'/?pipes=cron&task=callaio</a>. More instruction can be found at <a href="http://wpbriz.com/settings-up-cronjob-schedule-for-pipes/" target="_blank">this cronjob guideline</a></span>.</label><br />
									</fieldset>';
							echo '</td>';
							break;
						case 'pipes_active':
		//						echo '<div class="alert alert-info">There are two methods to execute WPPipes Pipes automatically.
		//								<ol>
		//									<li>Activating "Auto Run" below to execute Pipes over your Joomla site. By using this method, your Pipes will be executed every time your Joomla site get accessed over Site or Admin area.</li>
		//									<li>Create a cronjob task to the URL: http://yourjoomlasite.com/wp-admin/pipes.xyz&amp;task=callaio<br>Details instruction can be found <a href="http://wppipes.com/kb/wppipes/4983-setup-server-side-cronjob-for-wppipes" target="_blank">here</a></li>
		//								</ol>
		//							</div>';
							echo '<th scope="row">' . __( 'Allow Auto Run' ) . '</th>';
							echo '<td>';

							echo '<fieldset><legend class="screen-reader-text"><span>Auto Run</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want to run my Pipes in both manually and automatically methods.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, I want to run my Pipes manually.</span></label><br>
									</fieldset>';
							echo '</td>';
							break;
						case 'pipes_schedule':
							echo '<th scope="row"><label for="' . $setting->option_name . '">Run every</label></th>';
							echo '<td>';
							echo '<select name="' . $setting->option_name . '" id="' . $setting->option_name . '">
										<option ' . ( ( $setting->option_value == 'i5' ) ? 'selected="selected"' : '' ) . ' value="i5">5 minutes</option>
										<option ' . ( ( $setting->option_value == 'i10' ) ? 'selected="selected"' : '' ) . ' value="i10">10 minutes</option>
										<option ' . ( ( $setting->option_value == 'i15' ) ? 'selected="selected"' : '' ) . ' value="i15">15 minutes</option>
										<option ' . ( ( $setting->option_value == 'i20' ) ? 'selected="selected"' : '' ) . ' value="i20">20 minutes</option>
										<option ' . ( ( $setting->option_value == 'i25' ) ? 'selected="selected"' : '' ) . ' value="i25">25 minutes</option>
										<option ' . ( ( $setting->option_value == 'i30' ) ? 'selected="selected"' : '' ) . ' value="i30">30 minutes</option>
										<option ' . ( ( $setting->option_value == 'h1' ) ? 'selected="selected"' : '' ) . ' value="h1">1 hour</option>
										<option ' . ( ( $setting->option_value == 'h2' ) ? 'selected="selected"' : '' ) . ' value="h2">2 hours</option>
										<option ' . ( ( $setting->option_value == 'h3' ) ? 'selected="selected"' : '' ) . ' value="h3">3 hours</option>
										<option ' . ( ( $setting->option_value == 'h4' ) ? 'selected="selected"' : '' ) . ' value="h4">4 hours</option>
										<option ' . ( ( $setting->option_value == 'h6' ) ? 'selected="selected"' : '' ) . ' value="h6">6 hours</option>
										<option ' . ( ( $setting->option_value == 'h8' ) ? 'selected="selected"' : '' ) . ' value="h8">8 hours</option>
										<option ' . ( ( $setting->option_value == 'h12' ) ? 'selected="selected"' : '' ) . ' value="h12">12 hours</option>
										<option ' . ( ( $setting->option_value == 'h24' ) ? 'selected="selected"' : '' ) . ' value="h24">24 hours</option>
									</select>';
							echo '</td>';
							break;
						case 'pipes_start_at':
							if ( '' == $setting->option_value ) {
								$setting->option_value = '0';
							}
							$date   = date( 'Y-m-d');
							$hour   = 0;
							$minute = 0;
							echo '<th scope="row"><label for="' . $setting->option_name . '">' . __( 'Start from' ) . '</label></th>';
							echo '<td>';
							echo '<input style="max-width: 250px" name="' . $setting->option_name . '" type="text" id="' . $setting->option_name . '" value="' . $date . '" class="regular-text">';
							echo ' at <input type="number" min="0" max="23" id="pipes_hh" name="pipes_hh" value="' . $hour . '" size="2" maxlength="2" autocomplete="off">
										 : <input type="number" min="0" max="59" id="pipes_mn" name="pipes_mn" value="' . $minute . '" size="2" maxlength="2" autocomplete="off"></td>';
							echo "<script>
								jQuery(document).ready(function() {
									jQuery('#" . $setting->option_name . "').datepicker({
									dateFormat : 'yy-mm-dd'
									});
								});
								</script>";
						case 'pipes_not_use_cache':
							echo '<th scope="row">' . __( 'Not Use Cache' ) . '</th>';
							echo '<td>';

							echo '<fieldset><legend class="screen-reader-text"><span>Not Use Cache</span></legend>
									<label title="Yes"><input type="radio" name="' . $setting->option_name . '" value="1" ' . ( ( $setting->option_value == 1 ) ? 'checked="checked"' : '' ) . '>
									<span>Yes, I want the cronjob will be executed getting data directly from the source, not from Cache.</span></label><br>
									<label title="No"><input type="radio" name="' . $setting->option_name . '" value="0" ' . ( ( $setting->option_value == 0 ) ? 'checked="checked"' : '' ) . '>
									<span>No, cronjob will get data from the cache if the cache is not expired</span>.</label><br />
									</fieldset>';
							echo '</td>';
							break;
						default:
							break;
					}

					echo '</tr>';
				}
				?>
			</table>
			<input type="hidden" name="task" value="save" />

			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
			</p>
		</form>
	</div>
	<div class="welcome-panel">
		<div class="col-wrap">
			<?php $requirements->displayResults(); ?>
		</div>
	</div>
</div>