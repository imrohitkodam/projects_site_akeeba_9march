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
class Quick2cartViewTaxprofiles extends HtmlView
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
			'' => Text::_('JOPTION_SELECT_PUBLISHED'),
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
		$canDo = Quick2CartHelper::getActions($state->get('filter.category_id'));
		$bar = ToolBar::getInstance('toolbar');

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_TAXPROFILES'), 'list');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/taxprofile';

		if (file_exists($formPath) && ($canDo->get('core.create') && ($this->isTaxationEnabled == 1)))
		{
			ToolBarHelper::addNew('taxprofile.add', 'JTOOLBAR_NEW');
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
			if (isset($this->items[0]->state) && ($this->isTaxationEnabled == 1))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::divider();
					ToolBarHelper::custom('taxprofiles.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
					ToolBarHelper::custom('taxprofiles.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
				}
				else
				{
					$childBar->publish('taxprofiles.publish')->listCheck(true);
					$childBar->unpublish('taxprofiles.unpublish')->listCheck(true);
				}
			}
		}

		if (isset($this->items[0]))
		{
			if ($canDo->get('core.delete') && ($this->isTaxationEnabled == 1))
			{
				if (JVERSION < '4.0.0')
				{
					ToolBarHelper::deleteList('', 'taxprofiles.delete', 'JTOOLBAR_DELETE');
				}
				else
				{
					$childBar->delete('taxprofiles.delete')->listCheck(true);
				}
			}
		}

		HTMLHelper::_('bootstrap.modal', 'collapseModal');

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		$this->extra_sidebar = '';

		if (JVERSION >= '3.0')
		{
			// Set sidebar action - New in 3.0
			JHtmlSidebar::setAction('index.php?option=com_quick2cart&view=taxprofiles');
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
			'a.name' => Text::_('COM_QUICK2CART_TAXPROFILES_TAXPROFILE_NAME'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
