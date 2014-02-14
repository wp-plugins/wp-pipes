<?php
/**
 * @version        $Id: postformat.php 170 2014-01-26 06:34:40Z thongta $
 * @package        foobla RSS Feed Creator for Joomla.
 * @copyright      Copyright (C) 2007-2012 wppipes.com. All right reserved.
 * @author         wppipes.com.
 * @license        http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die();


class JFormFieldPostformat extends JFormField {
	public $type = 'Postformat';

	protected function getInput() {
		$html = array();

		// Initialize some field attributes.
		$class     = ! empty( $this->class ) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<div id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

		$options      = array();
		$options[]    = 0;
		$post_formats = get_theme_support( 'post-formats' );
		if ( $post_formats == false ) {
			return null;
		}
		foreach ( $post_formats[0] as $format ) {
			$options[] = $format;
		}


		// Build the radio field output.
		foreach ( $options as $i => $option ) {
			// Initialize some option attributes.
			$checked = ( (string) $option == (string) $this->value ) ? ' checked="checked"' : '';
			$class   = ! empty( $option->class ) ? ' class="' . $option->class . '"' : '';

			if ( 0 === $option ) {
				$class_label = 'class="post-format-icon post-format-standard"';
			} else {
				$class_label = 'class = "post-format-icon post-format-' . esc_attr( $option ) . '"';
			}
			$disabled = ! empty( $option->disable ) || ( $readonly && ! $checked );

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick  = ! empty( $option->onclick ) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = ! empty( $option->onchange ) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<label style="display:block;">';
			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				. htmlspecialchars( $option, ENT_COMPAT, 'UTF-8' ) . '"' . $checked . $class . $required . $onclick
				. $onchange . $disabled . ' />';

//			$html[] = '<label for="' . $this->id . $i . '"' . $class_label . ' >'
//				. esc_html( get_post_format_string( $option ) ) . '</label>';
			$html[]   = '<i ' . $class_label . '></i>';
			$html[]   = esc_html( get_post_format_string( $option ) );
			$html[]   = '</label>';
			$required = '';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode( $html ) . '</div><div><span class="help-block">' . $this->description . '</span>';
	}

}