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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * View class for a list of Quick2cart.
 *
 * @since  1.6
 */
class Quick2cartViewweights extends HtmlView
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
		$this->items		 = $this->get('Items');
		$this->pagination	 = $this->get('Pagination');
		$this->state		 = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->publish_states = array(
			''   => Text::_('JOPTION_SELECT_PUBLISHED'),
			'1'  => Text::_('JPUBLISHED'),
			'0'  => Text::_('JUNPUBLISHED'),
			'-2' =>	Text::_('JTRASHED'),
			'2'  => Text::_('JARCHIVED'),
			'*'  => Text::_('JALL')
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
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';

		$state = $this->get('State');
		$canDo = Quick2CartHelper::getActions();
		$bar   = ToolBar::getInstance('toolbar');
		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_WEIGHTS'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/weight';

		if (file_exists($formPath) && $canDo->get('core.create'))
		{
			ToolBarHelper::addNew('weight.add', 'JTOOLBAR_NEW');
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
					ToolBarHelper::custom('weights.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					ToolBarHelper::custom('weights.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
					ToolBarHelper::archiveList('weights.archive', 'JTOOLBAR_ARCHIVE');

					if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
					{
						ToolBarHelper::deleteList('', 'weights.delete', 'JTOOLBAR_EMPTY_TRASH');
						ToolBarHelper::divider();
					}
					elseif ($canDo->get('core.edit.state'))
					{
						ToolBarHelper::trash('weights.trash', 'JTOOLBAR_TRASH');
						ToolBarHelper::divider();
					}
				}
				else
				{
					$childBar->publish('weights.publish')->listCheck(true);
					$childBar->unpublish('weights.unpublish')->listCheck(true);
					$childBar->archive('weights.archive')->listCheck(true);

					if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
					{
						$childBar->delete('weights.delete')->listCheck(true);
					}
					elseif ($canDo->get('core.edit.state'))
					{
						$childBar->trash('weights.trash')->listCheck(true);
					}
				}
			}
			elseif (isset($this->items[0]))
			{
				if (JVERSION < '4.0.0')
				{
					// If this component does not use state then show a direct delete button as we can not trash
					ToolBarHelper::deleteList('', 'weights.delete', 'JTOOLBAR_DELETE');
				}
				else
				{
					$childBar->delete('weights.delete')->listCheck(true);
				}
			}

			if (isset($this->items[0]->checked_out))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::custom('weights.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
				}
				else
				{
					$childBar->checkin('weights.checkin')->listCheck(true);
				}
			}
		}

		HTMLHelper::_('bootstrap.modal', 'collapseModal');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_quick2cart&view=weights');
		$this->extra_sidebar = '';
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
			'a.title' => Text::_('COM_QUICK2CART_WEIGHTS_WEIGHT_TITLE'),
			'a.unit' => Text::_('COM_QUICK2CART_WEIGHTS_WEIGHT_UNIT'),
			'a.value' => Text::_('COM_QUICK2CART_WEIGHTS_WEIGHT_VALUE'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
