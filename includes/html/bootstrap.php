<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Bootstrap elements.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
abstract class JHtmlBootstrap
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript support for the Bootstrap affix plugin
	 *
	 * @param   string  $selector  Unique selector for the element to be affixed.
	 * @param   array   $params    An array of options.
	 *                             Options for the affix plugin can be:
	 *                             - offset  number|function|object  Pixels to offset from screen when calculating position of scroll.
	 *                                                               If a single number is provided, the offset will be applied in both top
	 *                                                               and left directions. To listen for a single direction, or multiple
	 *                                                               unique offsets, just provide an object offset: { x: 10 }.
	 *                                                               Use a function when you need to dynamically provide an offset
	 *                                                               (useful for some responsive designs).
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public static function affix($selector = 'affix', $params = array())
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap alerts
	 *
	 * @param   string  $selector  Common class for the alerts
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function alert($selector = 'alert')
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap buttons
	 *
	 * @param   string  $selector  Common class for the buttons
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public static function button($selector = 'button')
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap carousels
	 *
	 * @param   string  $selector  Common class for the carousels.
	 * @param   array   $params    An array of options for the modal.
	 *                             Options for the modal can be:
	 *                             - interval  number  The amount of time to delay between automatically cycling an item.
	 *                                                 If false, carousel will not automatically cycle.
	 *                             - pause     string  Pauses the cycling of the carousel on mouseenter and resumes the cycling
	 *                                                 of the carousel on mouseleave.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function carousel($selector = 'carousel', $params = array())
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap dropdowns
	 *
	 * @param   string  $selector  Common class for the dropdowns
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function dropdown($selector = 'dropdown-toggle')
	{
		return;
	}

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null)
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap modals
	 *
	 * @param   string  $selector  The ID selector for the modal.
	 * @param   array   $params    An array of options for the modal.
	 *                             Options for the modal can be:
	 *                             - backdrop  boolean  Includes a modal-backdrop element.
	 *                             - keyboard  boolean  Closes the modal when escape key is pressed.
	 *                             - show      boolean  Shows the modal when initialized.
	 *                             - remote    string   An optional remote URL to load
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function modal($selector = 'modal', $params = array())
	{
		return;
	}

	/**
	 * Method to render a Bootstrap modal
	 *
	 * @param   string  $selector  The ID selector for the modal.
	 * @param   array   $params    An array of options for the modal.
	 * @param   string  $footer    Optional markup for the modal footer
	 *
	 * @return  string  HTML markup for a modal
	 *
	 * @since   3.0
	 */
	public static function renderModal($selector = 'modal', $params = array(), $footer = '')
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap popovers
	 *
	 * Use element's Title as popover content
	 *
	 * @param   string  $selector  Selector for the tooltip
	 * @param   array   $params    An array of options for the tooltip.
	 *                  Options for the tooltip can be:
	 *                      animation  boolean          apply a css fade transition to the tooltip
	 *                      html       boolean          Insert HTML into the tooltip. If false, jQuery's text method will be used to insert
	 *                                                  content into the dom.
	 *                      placement  string|function  how to position the tooltip - top | bottom | left | right
	 *                      selector   string           If a selector is provided, tooltip objects will be delegated to the specified targets.
	 *                      trigger    string           how tooltip is triggered - hover | focus | manual
	 *                      title      string|function  default title value if `title` tag isn't present
	 *                      content    string|function  default content value if `data-content` attribute isn't present
	 *                      delay      number|object    delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
	 *                                                  If a number is supplied, delay is applied to both hide/show
	 *                                                  Object structure is: delay: { show: 500, hide: 100 }
	 *                      container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function popover($selector = '.hasPopover', $params = array())
	{
		return;
	}

	/**
	 * Add javascript support for Bootstrap ScrollSpy
	 *
	 * @param   string  $selector  The ID selector for the ScrollSpy element.
	 * @param   array   $params    An array of options for the ScrollSpy.
	 *                             Options for the modal can be:
	 *                             - offset  number  Pixels to offset from top when calculating position of scroll.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function scrollspy($selector = 'navbar', $params = array())
	{
		return;
	}

	public static function tooltip($selector = '.hasTooltip', $params = array())
	{
		return;
	}
	
	public static function typeahead($selector = '.typeahead', $params = array())
	{
		if (!isset(self::$loaded[__METHOD__][$selector]))
		{
			// Include Bootstrap framework
			self::framework();

			// Setup options object
			$opt['source']      = isset($params['source']) ? $params['source'] : '[]';
			$opt['items']       = isset($params['items']) ? (int) $params['items'] : 8;
			$opt['minLength']   = isset($params['minLength']) ? (int) $params['minLength'] : 1;
			$opt['matcher']     = isset($params['matcher']) ? (string) $params['matcher'] : null;
			$opt['sorter']      = isset($params['sorter']) ? (string) $params['sorter'] : null;
			$opt['updater']     = isset($params['updater']) ? (string) $params['updater'] : null;
			$opt['highlighter'] = isset($params['highlighter']) ? (int) $params['highlighter'] : null;

			$options = JHtml::getJSObject($opt);

			// Attach tooltips to document
			JFactory::getDocument()->addScriptDeclaration(
				"jQuery(document).ready(function()
				{
					jQuery('" . $selector . "').typeahead(" . $options . ");
				});"
			);

			// Set static array
			self::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}

	/**
	 * Add javascript support for Bootstrap accordians and insert the accordian
	 *
	 * @param   string  $selector  The ID selector for the tooltip.
	 * @param   array   $params    An array of options for the tooltip.
	 *                             Options for the tooltip can be:
	 *                             - parent  selector  If selector then all collapsible elements under the specified parent will be closed when this
	 *                                                 collapsible item is shown. (similar to traditional accordion behavior)
	 *                             - toggle  boolean   Toggles the collapsible element on invocation
	 *                             - active  string    Sets the active slide during load
	 *
	 * @return  string  HTML for the accordian
	 *
	 * @since   3.0
	 */
	public static function startAccordion($selector = 'myAccordian', $params = array())
	{
		$sig = md5(serialize(array($selector, $params)));

		if (!isset(self::$loaded[__METHOD__][$sig]))
		{
			// Include Bootstrap framework
			self::framework();

			// Setup options object
			$opt['parent'] = isset($params['parent']) ? (boolean) $params['parent'] : false;
			$opt['toggle'] = isset($params['toggle']) ? (boolean) $params['toggle'] : true;
			$opt['active'] = isset($params['active']) ? (string) $params['active'] : '';

			$options = JHtml::getJSObject($opt);

			// Attach accordion to document
			JFactory::getDocument()->addScriptDeclaration(
				"(function($){
					$('#$selector').collapse($options);
				})(jQuery);"
			);

			// Set static array
			self::$loaded[__METHOD__][$sig] = true;
			self::$loaded[__METHOD__]['active'] = $opt['active'];
		}

		return '<div id="' . $selector . '" class="accordion">';
	}

	/**
	 * Close the current accordion
	 *
	 * @return  string  HTML to close the accordian
	 *
	 * @since   3.0
	 */
	public static function endAccordion()
	{
		return '</div>';
	}

	/**
	 * Begins the display of a new accordion slide.
	 *
	 * @param   string  $selector  Identifier of the accordion group.
	 * @param   string  $text      Text to display.
	 * @param   string  $id        Identifier of the slide.
	 * @param   string  $class     Class of the accordion group.
	 *
	 * @return  string  HTML to add the slide
	 *
	 * @since   3.0
	 */
	public static function addSlide($selector, $text, $id, $class = '')
	{
		$in = (self::$loaded['JHtmlBootstrap::startAccordion']['active'] == $id) ? ' in' : '';
		$class = (!empty($class)) ? ' ' . $class : '';

		$html = '<div class="accordion-group' . $class . '">'
			. '<div class="accordion-heading">'
			. '<strong><a href="#' . $id . '" data-parent="#' . $selector . '" data-toggle="collapse" class="accordion-toggle">'
			. $text
			. '</a></strong>'
			. '</div>'
			. '<div class="accordion-body collapse' . $in . '" id="' . $id . '">'
			. '<div class="accordion-inner">';

		return $html;
	}

	/**
	 * Close the current slide
	 *
	 * @return  string  HTML to close the slide
	 *
	 * @since   3.0
	 */
	public static function endSlide()
	{
		return '</div></div></div>';
	}

	/**
	 * Creates a tab pane
	 *
	 * @param   string  $selector  The pane identifier.
	 * @param   array   $params    The parameters for the pane
	 *
	 * @return  string
	 *
	 * @since   3.1
	 */
	public static function startTabSet($selector = 'myTab', $params = array())
	{
		$sig = md5(serialize(array($selector, $params)));

		if (!isset(self::$loaded[__METHOD__][$sig]))
		{
			// Include Bootstrap framework
			self::framework();

			// Setup options object
			$opt['active'] = (isset($params['active']) && ($params['active'])) ? (string) $params['active'] : '';

			// Attach tabs to document
			JFactory::getDocument()
				->addScriptDeclaration(JLayoutHelper::render('libraries.cms.html.bootstrap.starttabsetscript', array('selector' => $selector)));

			// Set static array
			self::$loaded[__METHOD__][$sig] = true;
			self::$loaded[__METHOD__][$selector]['active'] = $opt['active'];
		}

		$html = JLayoutHelper::render('libraries.cms.html.bootstrap.starttabset', array('selector' => $selector));

		return $html;
	}

	/**
	 * Close the current tab pane
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   3.1
	 */
	public static function endTabSet()
	{
		$html = JLayoutHelper::render('libraries.cms.html.bootstrap.endtabset');

		return $html;
	}

	/**
	 * Begins the display of a new tab content panel.
	 *
	 * @param   string  $selector  Identifier of the panel.
	 * @param   string  $id        The ID of the div element
	 * @param   string  $title     The title text for the new UL tab
	 *
	 * @return  string  HTML to start a new panel
	 *
	 * @since   3.1
	 */
	public static function addTab($selector, $id, $title)
	{
		static $tabScriptLayout = null;
		static $tabLayout = null;

		$tabScriptLayout = is_null($tabScriptLayout) ? new JLayoutFile('libraries.cms.html.bootstrap.addtabscript') : $tabScriptLayout;
		$tabLayout = is_null($tabLayout) ? new JLayoutFile('libraries.cms.html.bootstrap.addtab') : $tabLayout;

		$active = (self::$loaded['JHtmlBootstrap::startTabSet'][$selector]['active'] == $id) ? ' active' : '';

		// Inject tab into UL
		JFactory::getDocument()
		->addScriptDeclaration($tabScriptLayout->render(array('selector' => $selector,'id' => $id, 'active' => $active, 'title' => $title)));

		$html = $tabLayout->render(array('id' => $id, 'active' => $active));

		return $html;
	}

	/**
	 * Close the current tab content panel
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   3.1
	 */
	public static function endTab()
	{
		$html = JLayoutHelper::render('libraries.cms.html.bootstrap.endtab');

		return $html;
	}

	/**
	 * Creates a tab pane
	 *
	 * @param   string  $selector  The pane identifier.
	 * @param   array   $params    The parameters for the pane
	 *
	 * @return  string
	 *
	 * @since   3.0
	 * @deprecated  4.0	Use JHtml::_('bootstrap.startTabSet') instead.
	 */
	public static function startPane($selector = 'myTab', $params = array())
	{
		$sig = md5(serialize(array($selector, $params)));
		if (!isset(self::$loaded['JHtmlBootstrap::startTabSet'][$sig]))
		{
			// Include Bootstrap framework
			self::framework();

			// Setup options object
			$opt['active'] = isset($params['active']) ? (string) $params['active'] : '';

			// Attach tooltips to document
			JFactory::getDocument()->addScriptDeclaration(
				"(function($){
					$('#$selector a').click(function (e) {
						e.preventDefault();
						$(this).tab('show');
					});
				})(jQuery);"
			);

			// Set static array
			self::$loaded['JHtmlBootstrap::startTabSet'][$sig] = true;
			self::$loaded['JHtmlBootstrap::startTabSet'][$selector]['active'] = $opt['active'];
		}

		return '<div class="tab-content" id="' . $selector . 'Content">';
	}

	/**
	 * Close the current tab pane
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   3.0
	 * @deprecated  4.0	Use JHtml::_('bootstrap.endTabSet') instead.
	 */
	public static function endPane()
	{
		return '</div>';
	}

	/**
	 * Begins the display of a new tab content panel.
	 *
	 * @param   string  $selector  Identifier of the panel.
	 * @param   string  $id        The ID of the div element
	 *
	 * @return  string  HTML to start a new panel
	 *
	 * @since   3.0
	 * @deprecated  4.0 Use JHtml::_('bootstrap.addTab') instead.
	 */
	public static function addPanel($selector, $id)
	{
		$active = (self::$loaded['JHtmlBootstrap::startTabSet'][$selector]['active'] == $id) ? ' active' : '';

		return '<div id="' . $id . '" class="tab-pane' . $active . '">';
	}

	/**
	 * Close the current tab content panel
	 *
	 * @return  string  HTML to close the pane
	 *
	 * @since   3.0
	 * @deprecated  4.0 Use JHtml::_('bootstrap.endTab') instead.
	 */
	public static function endPanel()
	{
		return '</div>';
	}

	/**
	 * Loads CSS files needed by Bootstrap
	 *
	 * @param   boolean  $includeMainCss  If true, main bootstrap.css files are loaded
	 * @param   string   $direction       rtl or ltr direction. If empty, ltr is assumed
	 * @param   array    $attribs         Optional array of attributes to be passed to JHtml::_('stylesheet')
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function loadCss($includeMainCss = true, $direction = 'ltr', $attribs = array())
	{
		// Load Bootstrap main CSS
		if ($includeMainCss)
		{
			JHtml::_('stylesheet', 'jui/bootstrap.min.css', $attribs, true);
			JHtml::_('stylesheet', 'jui/bootstrap-responsive.min.css', $attribs, true);
			JHtml::_('stylesheet', 'jui/bootstrap-extended.css', $attribs, true);
		}

		// Load Bootstrap RTL CSS
		if ($direction === 'rtl')
		{
			JHtml::_('stylesheet', 'jui/bootstrap-rtl.css', $attribs, true);
		}
	}
}
