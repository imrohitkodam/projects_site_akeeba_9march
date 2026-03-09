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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Zone form controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerZone extends FormController
{
	/**
	 * Class constructor.
	 *
	 * @since   1.6
	 */
	public function __construct()
	{
		$this->view_list = 'zones';
		parent::__construct();
	}

	/**
	 * This function give state/region select box.
	 *
	 * @return  null
	 *
	 * @since	2.2
	 */
	public function getStateSelectList()
	{
		$app            = Factory::getApplication();
		$data           = $app->input->post->get('jform', array(), 'array');
		$country_id     = isset($data['country_id']) ? $data['country_id'] : 0;
		$default_option = $data['default_option'];
		$field_name     = $data['field_name'];
		$field_id       = $data['field_id'];

		// Based on the country, get state and generate a select box
		if (!empty($country_id))
		{
			$model     = $this->getModel();
			$stateList = $model->getRegionList($country_id);
			$options   = array();
			$options[] = HTMLHelper::_('select.option', 0, Text::_('COM_QUICK2CART_ZONE_ALL_STATES'));

			if ($stateList)
			{
				foreach ($stateList as $state)
				{
					// This is only to generate the <option> tag inside select tag
					$options[] = HTMLHelper::_('select.option', $state['id'], $state['region']);
				}
			}

			// Now generate the select list and echo that
			$stateList = HTMLHelper::_(
			'select.genericlist', $options, $field_name, ' class="form-select col-sm-6 qtc_regionListTopMargin"', 'value', 'text', $default_option, $field_id
			);
			echo $stateList;
		}

		$app->close();
	}

	/**
	 * This function add country/region in perticular zone.
	 *
	 * @return  null
	 *
	 * @since	2.2
	 */
	public function addZoneRule()
	{
		$app   = Factory::getApplication();
		$data  = $app->input->post->get('jform', array(), 'array');
		$model = $this->getModel();

		$response          = array();
		$response['error'] = 0;

		if (!$model->saveZoneRule())
		{
			$response['error'] = 1;
			$response['errorMessage'] = $model->getError();
		}

		if ($response['error'] == 0)
		{
			$response['zonerule_id'] = $app->input->get('zonerule_id');
		}

		echo json_encode($response);

		$app->close();
	}

	/**
	 * This function Update country/region in perticular zone.
	 *
	 * @return  null
	 *
	 * @since	2.2
	 */
	public function updateZoneRule()
	{
		$app               = Factory::getApplication();
		$data              = $app->input->post->get('jform', array(), 'array');
		$model             = $this->getModel();
		$response          = array();
		$response['error'] = 0;

		if (!$model->saveZoneRule(1))
		{
			$response['error'] = 1;
			$response['errorMessage'] = $model->getError();
		}

		if ($response['error'] == 0)
		{
			$response['zonerule_id'] = $app->input->get('zonerule_id');
		}

		echo json_encode($response);
		$app->close();
	}

	/**
	 * This function deletes the rule form perticular zone.
	 *
	 * @return  null
	 *
	 * @since	2.2
	 */
	public function deleteZoneRule()
	{
		$app               = Factory::getApplication();
		$data              = $app->input->post->get('jform', array(), 'array');
		$model             = $this->getModel();
		$zoneRuleTable     = $model->getTable('Zonerule');
		$response          = array();
		$response['error'] = 0;

		if (!$zoneRuleTable->delete(array($data['zonerule_id'])))
		{
			$response['error']        = 1;
			$response['errorMessage'] = $zoneRuleTable->getError();
		}

		echo json_encode($response);
		$app->close();
	}
}
