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
class Quick2cartViewAttributeset extends HtmlView
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
			$this->attributeLists = $model->getAttributeListInAttributeSet($this->item->id);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
		$attributeList = FormHelper::loadFieldType('attributelist', false);
		$this->attributeList = $attributeList->getInput();

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

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_ATTRIBUTESET'), 'pencil-2');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('attributeset.apply', 'JTOOLBAR_APPLY');

			if (!empty($this->item->id))
			{
				ToolBarHelper::save('attributeset.save', 'JTOOLBAR_SAVE');
			}
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			if (!empty($this->item->id))
			{
				ToolBarHelper::custom('attributeset.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}

		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('attributeset.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('attributeset.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
