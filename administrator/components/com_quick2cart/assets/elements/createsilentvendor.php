<?php
/**
 * @package     Quick2Cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

FormHelper::loadFieldClass('list');

/**
 * Custom Legend field for component params.
 *
 * @package  Quick2Cart
 *
 * @since    3.0.0
 */
class JFormFieldCreateSilentVendor extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	3.0.0
	 */
	protected $type = 'createsilentvendor';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of HTMLHelper options.
	 *
	 * @since  3.0.0
	 */
	protected function getInput()
	{
		$params = ComponentHelper::getParams('com_tjvendors');
		$vendorApproval = $params->get('vendor_approval');
		$options = array();

		if ($vendorApproval)
		{
			$options[] = HTMLHelper::_('select.option', '0', Text::_('JNO'));
			$html = HTMLHelper::_('select.genericlist', $options, $this->name, '', 'value', 'text', '', $this->id);
			$html = '
				<div class="span8">
					<div class="span5">
						<div>'
							. HTMLHelper::_('select.genericlist', $options, $this->name, '', 'value', 'text', '', $this->id) .
						'</div>
						<br>
						<div class="pull-left alert alert-info control-label">'
							. Text::_("COM_QUICK2CART_VENDOR_APPROVAL_ENABLED") .
							' <a href="' . Uri::root() . 'administrator/index.php?option=com_config&view=component&component=com_tjvendors" target="_blank">' .
							Text::_("COM_QUICK2CART_VENDOR_APPROVAL_ENABLED_HERE") . '</a> ' . Text::_("COM_QUICK2CART_VENDOR_APPROVAL_ENABLED_2") . '
						</div>
					</div>
				</div>';
		}
		else
		{
			$options[] = HTMLHelper::_('select.option', '1', Text::_('JYES'));
			$options[] = HTMLHelper::_('select.option', '0', Text::_('JNO'));
			$html = HTMLHelper::_('select.genericlist', $options, $this->name, 'class="inputbox form-select"  ', 'value', 'text', $this->value, $this->name);
		}

		return  $html;
	}
}
