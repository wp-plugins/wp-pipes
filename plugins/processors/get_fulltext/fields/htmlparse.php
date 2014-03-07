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
		$script = '<script type="text/javascript">
					function checkitout(el){
						var auto_fulltext = 1;
						var textarea = el.parentNode.parentNode.getElementsByTagName("textarea")[0].value;
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
						 		if (input_url==innerhtm.split("<br>")[0]){
						 			value_input = list_li[i].getElementsByTagName("p")[0].innerText;
						 		}
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
					function change_auto_fulltext(el){
						var list_li = el.parentNode.parentNode.parentNode.getElementsByClassName("col-md-6")[1];
						var select = list_li.getElementsByTagName("select")[0];
						if(select.value == 1 && el.value != ""){
							select.value = 0;
						}
					}
					</script>';

		return $html . $script;
	}
}
