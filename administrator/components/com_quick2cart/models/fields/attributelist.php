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
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * This Class supports checkout process.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class JFormFieldattributelist extends FormField
{
	public $type = 'attributelist';

	/**
	 * This function to get attribute list
	 *
	 * @return  null
	 *
	 * @since	2.2
	 */
	public function getInput()
	{
		// Initialize variables.
		$options = array();
		$db	     = Factory::getDbo();
		$query	 = $db->getQuery(true);

		$query->select('DISTINCT ga.id, ga.attribute_name');
		$query->from('#__kart_global_attribute AS ga');
		$query->join('INNER', "#__kart_global_attribute_option AS ao ON ao.attribute_id = ga.id");

		// Get the options.
		$db->setQuery($query);

		$globalattributes = $db->loadObjectList();
		$options          = array();
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('QTC_PRODUCT_ATTRIBUTE_SELECT'));

		foreach ($globalattributes as $attributes)
		{
			$options[] = HTMLHelper::_('select.option', $attributes->id, $attributes->attribute_name);
		}

		return $options;
	}
}
