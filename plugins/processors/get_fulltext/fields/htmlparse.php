<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined( 'JPATH_PLATFORM' ) or die;
require_once dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) . DS . 'includes' . DS . 'form' . DS . 'fields' . DS . 'textarea.php';

/**
 * Field to select a user id from a modal list.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6.0
 */
class JFormFieldHtmlparse extends JFormFieldTextarea {
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6.0
	 */
	public $type = 'Htmlparse';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6.0
	 */

	protected function getInput() {
		$html   = parent::getInput();
		$html   = str_replace( '</span>', '<a href="#" class="btn btn-default" onclick="checkitout(this);" style="float:right;"><i class="fa fa-cogs"></i>Check out</a></span>', $html );
		$html   = str_replace( "fullwidth", "fullwidth linecodehere", $html );
		$script = '<script type="text/javascript">

					function checkitout(el){
						var auto_fulltext = 1;
						var textarea = el.parentNode.parentNode.getElementsByTagName("textarea")[1].value;
						var input_url = el.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByClassName("obkey")[0].innerText;
						if(input_url == "Click me"){
						 	alert ("Please choose the first input field!");
						 	return false;
						}else{
						 	var list_oe = document.getElementById("ob-oe");
						 	var list_li = list_oe.getElementsByTagName("li");
						 	if("" != textarea){
						 		auto_fulltext = 0;
						 	}
						 	var value_input;
						 	for(var i=0;i<list_li.length;i++){
						 		var innerhtm = list_li[i].innerHTML;
						 		var first_innertext = innerhtm.split("</text><br>")[0];
						 		if (input_url==first_innertext.split(">")[1]){
						 			value_input = list_li[i].getElementsByTagName("p")[0].innerText;
						 		}
							}
							if(!checkValidurl(value_input)){
								alert ("Please input the valid url!");
								return false;
							}
							var url = encodeURI("http://demo.foobla.com/html_parser/index.php?url="+value_input+"&code="+textarea+"&auto_fulltext="+auto_fulltext);
							window.open(url);
							//document.write(\'<iframe height="450"  allowTransparency="true" frameborder="0" scrolling="yes" style="width:100%;" src="\'+value_input+\'" type= "text/javascript"></iframe>\');

						}

						/*jQuery(\'#myModal\').on(\'shown.bs.modal\',function (e){
								jQuery(\'#modal_iframe\').attr("src", url);
							}).on("show.bs.modal", function () {
							jQuery(this).find(".modal-dialog").css("height", \'800px\').css("width", \'1000px\').css(\'margin-top\', \'100px\');
							});

							jQuery(\'#myModal\').modal({show: true});*/
					}
					function checkValidurl(url){
						url_validate = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
						if(!url_validate.test(url)){
						   return false;
						}
						else{
						   return true;
						}
					}
					function change_auto_fulltext(el){
						var list_li = el.parentNode.parentNode.parentNode.getElementsByClassName("col-md-6")[1];
						var select = list_li.getElementsByTagName("select")[0];
						if(select.value == 1 && el.value != ""){
							select.value = 0;
						}
					}
					</script>';

		$line_script = '<script type="text/javascript">
			(function($){
			$.fn.linenumbers = function(in_opts){
				// Settings and Defaults
				var opt = $.extend({
					col_width: \'25px\',
					start: 1,
					digits: 4.
				},in_opts);
				return this.each(function(){
					// Get some numbers sorted out for the CSS changes
					var new_textarea_width = (parseInt($(this).css(\'width\'))-parseInt(opt.col_width))+\'px\';
					var new_textarea_height = parseInt($(this).css(\'height\'))+\'px\';
					var new_textarea_lineheight = parseInt($(this).css(\'line-height\'))+\'px\';
					var padding_top = parseInt($(this).css(\'padding-top\'))+\'px\';
					// Create the div and the new textarea and style it
					$(this).before(\'<div style="width:\'+$(this).css(\'width\')+\';"><textarea style="width:\'+new_textarea_width+\';height:\'+new_textarea_height+\';padding-top:\'+padding_top+\';line-height:\'+new_textarea_lineheight+\';float:left;margin-right:\'+\'-\'+new_textarea_width+\';white-space:pre;overflow:hidden;" disabled="disabled"></textarea>\');
					$(this).after(\'<div style="clear:both;"></div></div>\');
					// Edit the existing textarea\'s styles
					$(this).css({\'font-family\':\'monospace\',\'resize\':\'none\',\'height\':new_textarea_height,\'width\':new_textarea_width,\'float\':\'right\'});
					// Define a simple variable for the line-numbers box
					var lnbox = $(this).parent().find(\'textarea[disabled="disabled"]\');
					// Bind some actions to all sorts of events that may change it\'s contents
					$(this).bind(\'blur focus change keyup keydown\',function(){
						// Break apart and regex the lines, everything to spaces sans linebreaks
						var lines = "\n"+$(this).val();
						lines = lines.match(/[^\n]*\n[^\n]*/gi);
						// declare output var
						var line_number_output=\'\';
						// declare spacers and max_spacers vars, and set defaults
						var max_spacers = \'\'; var spacers = \'\';
						for(var i=0;i<opt.digits;i++){
							max_spacers += \' \';
						}
						// Loop through and process each line
						$.each(lines,function(k,v){
							// Add a line if not blank
							if(k!=0){
								line_number_output += "\n";
							}
							// Determine the appropriate number of leading spaces
							lencheck = k+opt.start+\'!\';
							spacers = max_spacers.substr(lencheck.length-1);
							// Add the line, trimmed and with out line number, to the output variable
							line_number_output += spacers+(k+opt.start)+\':\'+v.replace(/\n/gi,\'\').replace(/./gi,\' \').substr(opt.digits+1);
						});
						// Give the text area out modified content.
						$(lnbox).val(line_number_output);
						// Change scroll position as they type, makes sure they stay in sync
						$(lnbox).scrollTop($(this).scrollTop());
					})
					// Lock scrolling together, for mouse-wheel scrolling
					$(this).scroll(function(){
						$(lnbox).scrollTop($(this).scrollTop());
					});
					// Fire it off once to get things started
					$(this).trigger(\'keyup\');
				});
			};
		})(jQuery);
		jQuery(\'document\').ready(function(){
			jQuery(\'.linecodehere\').linenumbers({col_width:\'35px\'});
		})
		</script>';

		return $html . $script . $line_script;
	}
}
