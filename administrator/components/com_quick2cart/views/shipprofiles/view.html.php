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
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;

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
	 * Display the view
	 *
	 * @param   String  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$zoneHelper = new zoneHelper;
		$app        = Factory::getApplication();

		$this->state             = $this->get('State');
		$this->items             = $this->get('Items');
		$this->pagination        = $this->get('Pagination');
		$this->params            = ComponentHelper::getParams('com_quick2cart');
		$this->isShippingEnabled = $this->params->get('shipping', 0);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Publish states
		$this->publish_states = array(
			'' => Text::_('JOPTION_SELECT_PUBLISHED'),
			'1' => Text::_('JPUBLISHED'),
			'0' => Text::_('JUNPUBLISHED')
		);

		$comquick2cartHelper = new comquick2cartHelper;

		// Get all stores.
		$user        = Factory::getUser();
		$storeHelper = new storeHelper;

		$this->storeFilters[] = array(
			'id'    => 0,
			'title' => Text::_('COM_QUICK2CART_COUPONFORM_STORE_SELECT')
		);

		$userStores   = $storeHelper->getUserStore($user->id);
		$this->stores = array_merge($this->storeFilters, $userStores);

		// Setup toolbar
		$this->addToolbar();
		$this->_prepareDocument();

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;
		$canDo = Quick2CartHelper::getActions();

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_QUICK2CART_SHIPPROFILE'));
		}

		$title = $this->params->get('page_title', Text::_('COM_QUICK2CART_SHIPPROFILE'));

		// @TODO - hack * remove line below -when correct itemid is passed for this view
		$title = Text::_('COM_QUICK2CART_SHIPPROFILE');
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

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';

		$canDo = Quick2cartHelper::getActions();
		$bar = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_SHIPPROFILE'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/shipprofile';

		// @TODO use JForm for shipprofile creation
		if (file_exists($formPath) && ($canDo->get('core.create') && ($this->isShippingEnabled == 1)))
		{
			ToolBarHelper::addNew('shipprofile.add', 'JTOOLBAR_NEW');
		}

		if (JVERSION >= '4.0.0')
		{
			$dropdown = $bar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
		}

		if (JVERSION < '4.0.0')
		{
			if ($canDo->get('core.edit.state') && ($this->isShippingEnabled == 1))
			{
				ToolBarHelper::divider();
				ToolBarHelper::custom('shipprofile.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolBarHelper::custom('shipprofile.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}

			if (isset($this->items[0]))
			{
				if ($canDo->get('core.delete') && ($this->isShippingEnabled == 1))
				{
					ToolBarHelper::deleteList('', 'shipprofile.delete', 'JTOOLBAR_DELETE');
				}
			}
		}
		else
		{
			if ($canDo->get('core.edit.state') && ($this->isShippingEnabled == 1))
			{
				$childBar->publish('shipprofile.publish')->listCheck(true);
				$childBar->unpublish('shipprofile.unpublish')->listCheck(true);
			}

			if (isset($this->items[0]))
			{
				if ($canDo->get('core.delete') && ($this->isShippingEnabled == 1))
				{
					$childBar->delete('shipprofile.delete')->listCheck(true);
				}
			}
		}

		$this->extra_sidebar = '';
		HTMLHelper::_('bootstrap.modal', 'collapseModal');
	}
}
