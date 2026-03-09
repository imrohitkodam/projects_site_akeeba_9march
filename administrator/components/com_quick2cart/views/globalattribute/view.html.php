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
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  2.5
 */
class Quick2cartViewGlobalAttribute extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   STRING  $tpl  template name
	 *
	 * @return  null
	 *
	 * @since  2.5
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$model = $this->getModel();

		if ($this->item->id)
		{
			$this->optionList = $model->getOptionList($this->item->id);
		}

		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
		$renderer = FormHelper::loadFieldType('renderer', false);

		$layoutsList = $renderer->getOptions();

		foreach ($layoutsList as $layouts)
		{
			$layoutInfo = new stdclass;

			$layoutInfo->text = str_replace(".php", "", $layouts->text);
			$layoutInfo->value = str_replace(".php", "", $layouts->value);
			$this->layoutsList[] = $layoutInfo;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  null
	 *
	 * @since  2.5
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = Quick2cartHelper::getActions();

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_ATTRIBUTE'), 'pencil-2');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('globalattribute.apply', 'JTOOLBAR_APPLY');

			if (!empty($this->item->id))
			{
				ToolBarHelper::save('globalattribute.save', 'JTOOLBAR_SAVE');
			}
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			if (!empty($this->item->id))
			{
				ToolBarHelper::custom('globalattribute.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}

		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('globalattribute.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('globalattribute.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
