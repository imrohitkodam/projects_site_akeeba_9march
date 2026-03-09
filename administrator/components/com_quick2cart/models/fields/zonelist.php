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
 * This help to fetch users available zones across all store.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class JFormFieldZonelist extends FormField
{
	protected $type = 'zonelist';

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

		// Load Zone helper.
		$path = JPATH_SITE . "/components/com_quick2cart/helpers/zoneHelper.php";
		JLoader::register('zoneHelper', $path);
		JLoader::load('zoneHelper');
		$zoneHelper = new zoneHelper;

		// Get user's accessible zone list
		$zoneList      = $zoneHelper->getUserZoneList('', array(1));
		$options       = array();
		$app           = Factory::getApplication();
		$jinput        = $app->input;
		$taxrate_id    = $jinput->get('id', 0, 'INT');
		$defaultZoneid = "";

		if ($taxrate_id)
		{
			$defaultZoneid = $zoneHelper->getZoneFromTaxRateId($taxrate_id);
		}

		foreach ($zoneList as $zone)
		{
			$zoneName  = ucfirst($zone['name']);
			$options[] = HTMLHelper::_('select.option', $zone['id'], $zoneName);
		}

		return HTMLHelper::_('select.genericlist', $options, $name, 'class="inputbox form-select required" ', 'value', 'text', $defaultZoneid, $control_name);
	}
}
