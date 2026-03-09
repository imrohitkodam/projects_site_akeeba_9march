<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Help by @manoj
 * How to use this?
 * See the code below that needs to be added in form xml
 * Make sure, you pass a unique id for each field
 * Also pass a hint field as Help text
 *
 * <field menu="hide" type="legend" id="q2c-product-display"
 * name="q2c-product-display" default="COM_QUICK2CART_DISPLAY_SETTINGS" hint="COM_QUICK2CART_DISPLAY_SETTINGS_HINT" label="" />
 *
 */

/**
 * Custom Legend field for component params.
 *
 * @package  Quick2cart
 *
 * @since    2.2
 */
class JFormFieldLegend extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Legend';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 *
	 * @since	1.6
	 */
	public function getInput()
	{
		$document = Factory::getDocument();
		HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');

		$legendClass = 'q2c-elements-legend';
		$hintClass = "q2c-elements-legend-hint";

		$hint = $this->hint;

		// Tada...

		// Let's remove controls class from parent

		// And, remove control-group class from grandparent
		$script = 'jQuery(document).ready(function(){
			jQuery("#' . $this->id . '").parent().removeClass("controls");
			jQuery("#' . $this->id . '").parent().parent().removeClass("control-group");
		});';

		$document->addScriptDeclaration($script);

		// Show them a legend.
		$return = '<legend class="clearfix ' . $legendClass . '" id="' . $this->id . '">' . Text::_($this->value) . '</legend>';

		// Show them a hint below the legend.
		// Let them go - GaGa about the legend.
		if (!empty($hint))
		{
			$return .= '<span class="disabled ' . $hintClass . '">' . Text::_($hint) . '</span>';
			$return .= '<br/><br/>';
		}

		return $return;
	}
}
