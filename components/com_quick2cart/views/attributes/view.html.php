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
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;
/**
 * This Class supports attribute process.
 *
 * @package     Joomla.Site
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartViewattributes extends HtmlView
{
	/**
	 * Render view.
	 *
	 * @param   array  $tpl  An optional associative array of configuration settings.
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function display($tpl = null)
	{
		$input = Factory::getApplication()->input;
		$this->params = ComponentHelper::getParams('com_quick2cart');
		$layout = $input->get('layout', '');

		if ($layout == 'attribute' || $layout == 'attribute_bs3' || $layout == 'attribute_bs5')
		{
			$id = $input->get('attr_id', 0, 'INT');
			$this->itemattribute_id = $id;
			$attribute = $this->get('Attribute');

			if ($attribute)
			{
				$this->itemattribute_name = $attribute->itemattribute_name;
				$this->attribute_compulsary = $attribute->attribute_compulsary;
				$this->attributeFieldType = $attribute->attributeFieldType;
			}

			$attribute_options = $this->get('Attributeoption');
			$this->attribute_opt = $attribute_options;
		}

		parent::display($tpl);
	}

	/**
	 * Method Allow to set toolbar.
	 *
	 * @return  ''
	 */
	private function _setToolBar()
	{
		$document = Factory::getDocument();
		$bar = ToolBar::getInstance('toolbar');
		ToolBarHelper::title(Text::_('QTC_SETT'), 'icon-48-quick2cart.png');
		ToolBarHelper::save('save', Text::_('QTC_SAVE'));
		ToolBarHelper::cancel('cancel', Text::_('QTC_CLOSE'));
	}
}
