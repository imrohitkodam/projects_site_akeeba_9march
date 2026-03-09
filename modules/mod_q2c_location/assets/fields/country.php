<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2021 - 2021 Techjoomla. All rights reserved.
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
 * Supports an HTML select list of country with ISO code
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldCountry extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var   string
	 * @since __DEPLOY_VERSION__
	 */
	protected $type = 'country';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return array An array of HTMLHelper options.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options = array();
		$db      = Factory::getDbo();
		$query   = $db->getQuery(true);
		$query->select($db->qn(array('country','country_code')));
		$query->from($db->qn('#__tj_country'));
		$query->where($db->qn('com_quick2cart') . ' = ' . $db->quote('1'));
		$db->setQuery($query);
		$countryList = $db->loadAssocList();

		if (!empty($countryList))
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_('MOD_Q2C_LOCATION_COUNTRY'));

			foreach ($countryList as $key => $country)
			{
				$options[] = HTMLHelper::_('select.option', htmlspecialchars($country['country_code']), htmlspecialchars($country['country']));
			}
		}

		return $options;
	}
}
