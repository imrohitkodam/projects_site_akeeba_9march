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
 * This Class supports checkout process.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class JFormFieldZonestorelist extends FormField
{
	protected	$type = 'zonestorelist';

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
		$comquick2cartHelper = new comquick2cartHelper;

		// Getting user accessible store ids
		$storeList = $comquick2cartHelper->getStoreIds();
		$options  = array();

		$app              = Factory::getApplication();
		$jinput           = $app->input;
		$zone_id          = $jinput->get('id');
		$defaultSstore_id = 0;

		if ($zone_id)
		{
			// Load Zone helper.
			$path = JPATH_SITE . "/components/com_quick2cart/helpers/zoneHelper.php";

			if (!class_exists('zoneHelper'))
			{
				JLoader::register('zoneHelper', $path);
				JLoader::load('zoneHelper');
			}

			$zoneHelper       = new zoneHelper;
			$defaultSstore_id = $zoneHelper->getZoneStoreId($zone_id);
		}

		foreach ($storeList as $store)
		{
			$storename = ucfirst($store['title']);
			$options[] = HTMLHelper::_('select.option', $store['store_id'], $storename);
		}

		return HTMLHelper::_('select.genericlist', $options, $name, 'class="inputbox required form-select"  size="1"', 'value', 'text', $defaultSstore_id, $control_name);
	}
}
