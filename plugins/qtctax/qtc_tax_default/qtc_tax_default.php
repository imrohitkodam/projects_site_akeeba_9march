<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

$lang = Factory::getLanguage();
$lang->load('plug_qtc_tax_default', JPATH_ADMINISTRATOR);

/**
 * PlgQtctaxqtc_tax_default
 *
 * @package     Com_Quick2cart
 * @subpackage  site
 * @since       2.2
 */
class PlgQtctaxqtc_Tax_Default extends CMSPlugin
{
	/**
	 * Gives applicable tax charges.
	 *
	 * @param   integer  $amt   cart subtotal (after discounted amount )
	 * @param   object   $vars  object with cartdetail,billing and shipping details.
	 *
	 * @since   2.2
	 * @return   it should return array that contain [charges]=>charges [DetailMsg]=>Detail message
	 * 				or return empty array
	 */
	public function onAddTax($amt, $vars='')
	{
		$tax_per = $this->params->get('tax_per');
		$tax_value = ($tax_per * $amt) / 100;

		$return["DetailMsg"] = $tax_per . "%";
		$return["charges"] = $tax_value;

		return $return;
	}
}
