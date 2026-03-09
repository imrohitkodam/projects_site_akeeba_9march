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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View class for a list of Quick2cart.
 *
 * @since  1.6
 */
class Quick2cartViewPromotions extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->comquick2cartHelper = new comquick2cartHelper;
		$storeHelper               = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		$this->comquick2cartHelper->loadqtcClass(JPATH_ADMINISTRATOR . "/components/com_quick2cart/models/products.php", 'Quick2cartModelProducts');
		$this->Quick2cartModelProducts = new Quick2cartModelProducts;

		$user = Factory::getUser();
		$userId = $user->id;
		
		// Get all stores.
		$this->storeList = $this->comquick2cartHelper->getAllStoreDetails();

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Setup toolbar
		$this->addToolbar();

		if (JVERSION < '4.0.0')
		{
		    $this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Method to order fields
	 *
	 * @return void
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`state`' => Text::_('COM_QUICK2CART_PROMOTIONS_PUBLISHED'),
			'a.`name`' => Text::_('COM_QUICK2CART_PROMOTIONS_NAME'),
			'a.`description`' => Text::_('COM_QUICK2CART_PROMOTIONS_DESCRIPTION'),
			'a.`from_date`' => Text::_('COM_QUICK2CART_PROMOTIONS_FROM_DATE'),
			'a.`exp_date`' => Text::_('COM_QUICK2CART_PROMOTIONS_EXP_DATE'),
			'a.`store_id`' => Text::_('COM_QUICK2CART_PROMOTIONS_STORE_ID'),
		);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Quick2cartHelper::getActions();
		$bar   = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_PROMOTIONS'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/promotion';

		if (file_exists($formPath) && ($canDo->get('core.create')))
		{
			ToolBarHelper::addNew('promotion.add', 'JTOOLBAR_NEW');
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

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::divider();
					ToolBarHelper::custom('promotions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					ToolBarHelper::custom('promotions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				}
				else
				{
					$childBar->publish('promotions.publish')->listCheck(true);
					$childBar->unpublish('promotions.unpublish')->listCheck(true);
				}

				if ($canDo->get('core.delete'))
				{
					if (JVERSION < '4.0.0')
					{
						ToolBarHelper::deleteList('', 'promotions.delete', 'JTOOLBAR_DELETE');
						ToolBarHelper::divider();
					}
					else
					{
						$childBar->delete('promotions.delete')->listCheck(true);
					}
				}
			}
			elseif (isset($this->items[0]))
			{
				if (JVERSION < '4.0.0')
				{
					// If this component does not use state then show a direct delete button as we can not trash
					ToolBarHelper::deleteList('', 'promotions.delete', 'JTOOLBAR_DELETE');
				}
				else
				{
					$childBar->delete('promotions.delete')->listCheck(true);
				}
			}
		}

		HTMLHelper::_('bootstrap.modal', 'collapseModal');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		if (JVERSION < '4.0.0')
		{
			// Set sidebar action - New in 3.0
			JHtmlSidebar::setAction('index.php?option=com_quick2cart&view=promotions');
		}

		$this->extra_sidebar = '';
	}
}
