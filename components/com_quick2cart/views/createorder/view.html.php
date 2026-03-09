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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

require_once JPATH_SITE . '/components/com_quick2cart/models/store.php';
/**
 * View class for create order view.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.5.1
 */
class Quick2cartViewCreateOrder extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->params = ComponentHelper::getParams('com_quick2cart');
		$user = Factory::getUser();
		$app = Factory::getApplication();
		$input     = $app->input;

		$model = $this->getModel('createorder');
		$storeModel = new Quick2cartModelstore;
		$authorisedToViewThisView = $storeModel->getStoreId($user->id);
		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
		$quick2cartUsers = FormHelper::loadFieldType('quick2cartusers', false);

		$this->comquick2cartHelper = new Comquick2cartHelper;

		$defaultStore = array();
		$defaultStore['store_id'] = '';
		$defaultStore['title'] = Text::_('COM_QUICK2CART_SELET_STORE');

		$this->stores = array();
		$this->stores[] = $defaultStore;

		$storeList[] = $this->stores;

		$this->stores = array_merge($this->stores, $this->comquick2cartHelper->getStoreIds($user->id));

		// Get users list
		$this->users = $quick2cartUsers->getInput();

		if (!empty($authorisedToViewThisView))
		{
			parent::display($tpl);
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_CREATE_ORDER_AUTHORIZATION_ERROR'), 'Warning');
		}
	}
}
