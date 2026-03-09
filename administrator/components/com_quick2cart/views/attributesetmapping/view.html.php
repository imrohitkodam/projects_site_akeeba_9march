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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  2.5
 */
class Quick2cartViewAttributeSetMapping extends HtmlView
{
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
		$this->model = $this->getModel('attributesetmapping');
		$this->attributeSetsList = $this->model->getAttributeSets();
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla('', 0, 'prod_cat', ' required ');
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  null
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolBarHelper::title(Text::_('COM_QUICK2CART_ATTRIBUTESET_CATEGORY_MAPPING'), 'pencil-2');
		ToolBarHelper::apply('attributesetmapping.apply', 'JTOOLBAR_APPLY');
		ToolBarHelper::save('attributesetmapping.save', 'JTOOLBAR_SAVE');
		ToolBarHelper::cancel('attributesetmapping.cancel', 'JTOOLBAR_CLOSE');
		ToolBarHelper::preferences('com_quick2cart');
	}
}
