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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View class for a list of stores.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewStores extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display ($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->params        = ComponentHelper::getParams('com_quick2cart');

		// Check for errors.
		$errors = $this->get('Errors');

		if (count($errors))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		// Creating status filter.
		$statuses = array();
		$statuses[] = HTMLHelper::_('select.option', '', Text::_('JOPTION_SELECT_PUBLISHED'));
		$statuses[] = HTMLHelper::_('select.option', 1, Text::_('JPUBLISHED'));
		$statuses[] = HTMLHelper::_('select.option', 0, Text::_('JUNPUBLISHED'));
		$this->statuses = $statuses;

		parent::display($tpl);
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

		$state = $this->get('State');
		$canDo = Quick2cartHelper::getActions();
		$bar   = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_STORES'), 'cart');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/store';

		// @TODO use JForm for store creation
		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('store.add', 'JTOOLBAR_NEW');
			}
		}
		else
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('vendor.addNew', 'JTOOLBAR_NEW');
			}
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
			if (isset($this->items[0]->published))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::divider();
					ToolBarHelper::custom('stores.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					ToolBarHelper::custom('stores.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				}
				else
				{
					$childBar->publish('stores.publish')->listCheck(true);
					$childBar->unpublish('stores.unpublish')->listCheck(true);
				}
			}

			if (isset($this->items[0]->checked_out))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::custom('stores.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
				}
				else
				{
					$childBar->checkin('stores.checkin')->listCheck(true);
				}
			}
		}

		if (isset($this->items[0]))
		{
			if ($canDo->get('core.delete'))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::deleteList('', 'stores.delete', 'JTOOLBAR_DELETE');
				}
				else
				{
					$childBar->delete('stores.delete')->listCheck(true);
				}
			}
		}

		ToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart&view=dashboard');

		HTMLHelper::_('bootstrap.modal', 'collapseModal');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		if (JVERSION >= '3.0')
		{
			// Set sidebar action
			JHtmlSidebar::setAction('index.php?option=com_quick2cart&view=stores');
		}

		$this->extra_sidebar = '';
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields ()
	{
		return array(
			'a.title' => Text::_('STORE_TITLE'),
			'published' => Text::_('JSTATUS'),
			'u.name' => Text::_('VENDOR_NAME'),
			'a.store_email' => Text::_('STORE_EMAIL'),
			'a.phone' => Text::_('STORE_PHONE'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
