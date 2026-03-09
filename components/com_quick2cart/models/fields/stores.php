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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.0
 */
class JFormFieldStores extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'stores';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getOptions()
	{
		if (!class_exists('storeHelper'))
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';
			JLoader::register('storeHelper', $path);
			JLoader::load('storeHelper');
		}

		$storeHelper = new storeHelper;

		// Get all stores.
		$user    = Factory::getUser();
		$stores  = $storeHelper->getUserStore($user->id);
		$options = array();

		foreach ($stores as $key => $value)
		{
			$options[] = HTMLHelper::_('select.option', $value['id'], $value['title']);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
