<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Supports an HTML select list of gateways
 *
 * @since  1.6
 */
class JFormFieldBssetup extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Bssetup';

	/**
	 * Function to fetch elements
	 *
	 * @return  STRING  html
	 */
	public function getInput()
	{
		$control = (isset($this->options['control'])) ? $this->options['control'] : '';
		return $this->fetchElement($this->name, $this->value, $this->element, $control);
	}

	/**
	 * Function to fetch elements
	 *
	 * @param   STRING  $name          name
	 * @param   STRING  $value         value
	 * @param   STRING  $node          node
	 * @param   STRING  $control_name  control_name
	 *
	 * @return  STRING  html
	 */
	public function fetchElement($name, $value, $node, $control_name)
	{
		$actionLink = Uri::base() . "index.php?option=com_quick2cart&view=dashboard&layout=setup";

		// Show link for payment plugins.
		$html = '<a
			href="' . $actionLink . '" target="_blank"
			class="btn btn-small btn-primary ">'
				. Text::_('COM_QUICK2CART_CLICK_BS_SETUP_INSTRUCTION') .
			'</a>';

		return $html;
	}
}
