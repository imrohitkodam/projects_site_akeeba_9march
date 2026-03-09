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
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View class for a list of Shipping profiles.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewShipprofiles extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Function dispaly
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	public function display($tpl = null)
	{
		$zoneHelper = new zoneHelper;

		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}

		$app = Factory::getApplication();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params = ComponentHelper::getParams('com_quick2cart');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Publish states
		$this->publish_states = array(
			'' => Text::_('JOPTION_SELECT_PUBLISHED'),
			'1'  => Text::_('JPUBLISHED'),
			'0'  => Text::_('JUNPUBLISHED')
		);

		// Get all stores.
		$user = Factory::getUser();
		$storeHelper = new storeHelper;

		$this->storeFilters[] = array('id' => 0, 'title' => Text::_('COM_QUICK2CART_COUPONFORM_STORE_SELECT'));
		$userStores = $storeHelper->getUserStore($user->id);
		$this->stores = array_merge($this->storeFilters, $userStores);

		// Get toolbar path
		$comquick2cartHelper = new comquick2cartHelper;
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		// Setup TJ toolbar
		$this->addTJtoolbar();

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Function To Prepare Document
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	protected function _prepareDocument()
	{
		$app = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_QUICK2CART_SHIPPROFILE_S_MANAGE_LIST_LEGEND'));
		}

		$title = $this->params->get('page_title', Text::_('COM_QUICK2CART_SHIPPROFILE_S_MANAGE_LIST_LEGEND'));

		// @TODO - hack * remove line below -when correct itemid is passed for this view
		$title = Text::_('COM_QUICK2CART_SHIPPROFILE_S_MANAGE_LIST_LEGEND');
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	/**
	 * Setup ACL based tjtoolbar
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	protected function addTJtoolbar ()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/helpers/quick2cart.php';
		$canDo = Quick2cartHelper::getActions();

		// Add toolbar buttons
		jimport('techjoomla.tjtoolbar.toolbar');
		$tjbar = TJToolbar::getInstance('tjtoolbar', 'pull-right float-end');

		if ($canDo->get('core.create'))
		{
			$tjbar->appendButton('shipprofileform.add', 'TJTOOLBAR_NEW', QTC_ICON_PLUS, 'class="btn btn-sm btn-success"');
		}

		/*if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			$tjbar->appendButton('shipprofileform.edit', 'TJTOOLBAR_EDIT', '', 'btn btn-sm btn-success');
		}*/

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				$tjbar->appendButton('shipprofiles.publish', 'TJTOOLBAR_PUBLISH', QTC_ICON_PUBLISH, 'class="btn btn-sm btn-success"');
				$tjbar->appendButton('shipprofiles.unpublish', 'TJTOOLBAR_UNPUBLISH', QTC_ICON_UNPUBLISH, 'class="btn btn-sm btn-warning"');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]))
			{
				$tjbar->appendButton('shipprofiles.delete', 'TJTOOLBAR_DELETE', Q2C_ICON_TRASH, 'class="btn btn-sm btn-danger"');
			}
		}

		$this->toolbarHTML = $tjbar->render();
	}
}
