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
use Joomla\CMS\HTML\HTMLHelper;

/**
 * This Class provide store list which is require while creating tax profile etc (store must have created tax rate ).
 *
 * @package     Quick2Cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class JFormFieldTaxprofilestorelist extends FormField
{
	public	$type = 'taxprofilestorelist';

	/**
	 * Fetch Element view.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getInput()
	{
		$control = (isset($this->options['control'])) ? $this->options['control'] : '';
		return $this->fetchElement($this->name, $this->value, $this->element, $control);
	}

	/**
	 * Fetch custom Element view.
	 *
	 * @param   string  $name          Field Name.
	 * @param   mixed   $value         Field value.
	 * @param   mixed   $node          Field node.
	 * @param   mixed   $control_name  Field control_name/Id.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function fetchElement($name, $value, $node, $control_name)
	{
		$db   = Factory::getDBO();
		$user = Factory::getUser();

		// While edit: get profile store id
		$app             = Factory::getApplication();
		$jinput          = $app->input;
		$id              = $jinput->get('id', 0, 'INT');
		$defaultStore_id = 0;

		// Load tax helper.
		$path = JPATH_SITE . "/components/com_quick2cart/helpers/taxHelper.php";

		if (!class_exists('taxHelper'))
		{
			JLoader::register('taxHelper', $path);
			JLoader::load('taxHelper');
		}

		$taxHelper = new taxHelper;

		if ($id)
		{
			$defaultStore_id = $taxHelper->getTaxProfileStoreId($id);
		}

		$storeList = $taxHelper->getStoreListForTaxprofile();

		$options = array();

		foreach ($storeList as $store)
		{
			$storename = ucfirst($store['title']);
			$options[] = HTMLHelper::_('select.option', $store['store_id'], $storename);
		}

		return HTMLHelper::_(
		'select.genericlist', $options, $name, 'class="form-select inputbox required" id="jform_store_id" ', 'value', 'text', $defaultStore_id, $control_name
		);
	}
}
