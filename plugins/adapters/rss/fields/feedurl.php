<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */


/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldFeedurl extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Feedurl';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		if (empty($this->element['label']) && empty($this->element['description']))
		{
			return '';
		}

		$title = $this->element['label'] ? (string) $this->element['label'] : ($this->element['title'] ? (string) $this->element['title'] : '');
		$heading = $this->element['heading'] ? (string) $this->element['heading'] : 'h4';
		$description = (string) $this->element['description'];
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$close = (string) $this->element['close'];

		$html = array();

		if ($close)
		{
			$close = $close == 'true' ? 'alert' : $close;
			$html[] = '<button type="button" class="close" data-dismiss="' . $close . '">&times;</button>';
		}

		$html[] = !empty($title) ? '<' . $heading . '>' . JText::_($title) . '</' . $heading . '>' : '';
		$html[] = !empty($description) ? JText::_($description) : '';

		return '</div><div ' . $class . '>' . implode('', $html);
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
//		$html = "
//			<script>
//			var url = jQuery('#adapter_params_folder').value + '/' + jQuery('#adapter_params_filename').value;
//			jQuery('#feed_url_desc').innerHTML = url;
//			</script>
//
//			<div id='feed_url_desc'></div>
//		";
		$html = '<a target="_blank" href="'.get_site_url( '' ).'/[FOLDER]/[FILENAME]">'.get_site_url( '' ).'/[FOLDER]/[FILENAME]</a>';
		$html .= '<span class="help-block">'.__('Replace [FOLDER] and [FILENAME] with your above inputted values to have the RSS Feed URL.').'</span>';
		return $html;
	}
}
