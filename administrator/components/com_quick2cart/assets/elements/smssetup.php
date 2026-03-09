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

/**
 * JFormFieldSmssetup form custom element class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.6
 */
class JFormFieldSmssetup extends FormField
{
	protected $type = 'smssetup';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.6
	 */
	public function getInput()
	{
		$control = (isset($this->options['control'])) ? $this->options['control'] : '';
		return $this->fetchElement($this->name, $this->value, $this->element, $control);
	}

	/**
	 * Get needed field data
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value of the field
	 * @param   string  $node          Node of the field
	 * @param   string  $control_name  Field control name
	 *
	 * @return   string  Field HTML
	 */
	public function fetchElement($name, $value, $node, $control_name)
	{
		$html = '<a
			href="index.php?option=com_plugins&view=plugins&filter_folder=system"
			target="_blank"
			class="btn btn-small btn-primary">'
				. Text::_('COM_QUICK2CART_SETUP_SMS_PLUGINS') .
			'</a>';

		return $html;
	}
}
