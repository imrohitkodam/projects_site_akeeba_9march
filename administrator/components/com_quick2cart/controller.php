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
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Zones list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartController extends BaseController
{
	/**
	 * dispaly function of backend controller
	 *
	 * @param   string  $cachable   cachable
	 * @param   string  $urlparams  urlparams
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app          = Factory::getApplication();
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';

		$vName = $app->input->getCmd('view', 'dashboard');
		$app->input->set('view', $vName);

		$layout  = $app->input->getCmd('layout', 'default');
		$jinput  = $app->input;
		$vLayout = '';

		switch ($vName)
		{
			case 'dashboard':
				$mName = 'Dashboard';
				$vLayout = $jinput->get('layout', 'dashboard');
			break;

			case 'stores':
				$mName = 'stores';
				$vLayout = $jinput->get('layout', 'default');
			break;

			case "vendor" :
				$mName = 'vendor';
				$vLayout = $jinput->get('layout', 'default');
			break;

			case 'products':
				$mName = 'products';
				$vLayout = $jinput->get('layout', 'default');
			break;

			case 'orders':
				$mName = 'orders';
				$vLayout = $jinput->get('layout', $layout);
			break;

			case 'salesreport':
				$mName = 'salesreport';
				$vLayout = $jinput->get('layout', 'default');
			break;

			case 'delaysreport':
				$mName = 'delaysreport';
				$vLayout = $jinput->get('layout', 'default');
			break;
			case 'attributes':
				$mName = 'attributes';
				$vLayout = $jinput->get('layout', 'default');
			break;

			/*case 'attributes':
				$mName = 'attributes';
				$vLayout = $jinput->get('layout', 'default');
			break;*/

			case 'managecoupon':
				$mName = 'Managecoupon';
				$vLayout = $jinput->get('layout', 'default');
			break;

			default:
				$mName = 'reports';
				$vLayout = $jinput->get('layout', 'payouts');
			break;

			case 'countries':
				$mName = 'countries';
				$vLayout = $jinput->get('layout', 'default');
			break;
			case 'shipping':
				$mName = 'shipping';
				$vLayout = $jinput->get('layout', 'default');
			break;
			case 'shipprofiles':
				$mName = 'shipprofiles';
				$vLayout = $jinput->get('layout', 'default');
			break;
			case 'shipprofile':
				$mName = 'shipprofile';
				$vLayout = $jinput->get('layout', 'edit');
			break;
		}

		if ($vLayout)
		{
			$app->input->set('layout', $vLayout);
		}

		Quick2CartHelper::addSubmenu($vName, $vLayout);

		parent::display();

		return $this;
	}
}
