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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * View class for list view of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewattributes extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$input  = Factory::getApplication()->input;
		$layout = $input->get('layout', '');
		$this->params = ComponentHelper::getParams('com_quick2cart');

		if ($layout == 'attribute' || $layout == 'attribute_bs2' || $layout == 'attribute_bs5')
		{
			$id                     = $input->get('attr_id', 0, 'INT');
			$this->itemattribute_id = $id;
			$attribute              = $this->get('Attribute');

			if ($attribute)
			{
				$this->itemattribute_name   = $attribute->itemattribute_name;
				$this->attributeFieldType = $attribute->attributeFieldType;
				$this->attribute_compulsary = $attribute->attribute_compulsary;
			}

			$attribute_options   = $this->get('Attributeoption');
			$this->attribute_opt = $attribute_options;
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function _setToolBar()
	{
		ToolBarHelper::title(Text::_('QTC_SETT'), 'icon-48-quick2cart.png');
		ToolBarHelper::save('save', Text::_('QTC_SAVE'));
		ToolBarHelper::cancel('cancel', Text::_('QTC_CLOSE'));
	}
}
