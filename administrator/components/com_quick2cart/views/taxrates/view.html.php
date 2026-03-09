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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * View class for a list of Quick2cart.
 *
 * @since  1.6
 */
class Quick2cartViewTaxrates extends HtmlView
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
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$this->params            = ComponentHelper::getParams('com_quick2cart');
		$this->items		     = $this->get('Items');
		$this->pagination	     = $this->get('Pagination');
		$this->state		     = $this->get('State');
		$this->filterForm        = $this->get('FilterForm');
		$this->activeFilters     = $this->get('ActiveFilters');
		$this->isTaxationEnabled = $this->params->get('enableTaxtion', 0);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->publish_states = array(
			''   => Text::_('JOPTION_SELECT_PUBLISHED'),
			'1'  => Text::_('JPUBLISHED'),
			'0'  => Text::_('JUNPUBLISHED')
		);

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since	1.6
	 *
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';

		$state = $this->get('State');
		$canDo = Quick2CartHelper::getActions();
		$bar   = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_TAXTRATES'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/taxrate';

		if (file_exists($formPath) && ($this->isTaxationEnabled == 1))
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('taxrate.add', 'JTOOLBAR_NEW');
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

		if ($canDo->get('core.edit.state') && ($this->isTaxationEnabled == 1))
		{
			if (isset($this->items[0]->state))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::divider();
					ToolBarHelper::custom('taxrates.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					ToolBarHelper::custom('taxrates.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				}
				else
				{
					$childBar->publish('taxrates.publish')->listCheck(true);
					$childBar->unpublish('taxrates.unpublish')->listCheck(true);
				}
			}

			if (isset($this->items[0]->checked_out))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::custom('taxrates.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
				}
				else
				{
					$childBar->checkin('taxrates.checkin')->listCheck(true);
				}
			}
		}

		if (isset($this->items[0]) && ($this->isTaxationEnabled == 1))
		{
			if ($canDo->get('core.delete'))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::deleteList('', 'taxrates.delete', 'JTOOLBAR_DELETE');
				}
				else
				{
					$childBar->delete('taxrates.delete')->listCheck(true);
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
			JHtmlSidebar::setAction('index.php?option=com_quick2cart&view=taxrates');
			$this->extra_sidebar = '';
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since	1.6
	 */
	protected function getSortFields()
	{
		return array(
			'a.state' => Text::_('JSTATUS'),
			'a.name' => Text::_('COM_QUICK2CART_TAXTRATES_TAXRATE_NAME'),
			'a.percentage' => Text::_('COM_QUICK2CART_TAXTRATES_TAX_PERCENT'),
			'a.zone_id' => Text::_('COM_QUICK2CART_TAXTRATES_ZONE_ID'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
