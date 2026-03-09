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
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Language\Text;

/**
 * Supports an HTML select list of categories
 *
 * @package  Quick2cart
 *
 * @since    2.7
 */
class JFormFieldquick2cartusers extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 *
	 * @since	1.6
	 */
	protected $type = 'quick2cartusers';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since  1.6
	 */
	public function getInput()
	{
		// Initialize variables.
		$db	   = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('id, name, username');
		$query->from('#__users AS ac');

		// Get the options.
		$db->setQuery($query);

		$users  = array();
		$user[] = array(
				'id' => '0',
				'name' => Text::_('COM_QUICK2CART_SELECT_CUSTOMER'),
				'username' => Text::_('COM_QUICK2CART_SELECT_CUSTOMER')
			);
		$users  = array_merge($user, $db->loadAssocList());

		return $users;
	}
}
