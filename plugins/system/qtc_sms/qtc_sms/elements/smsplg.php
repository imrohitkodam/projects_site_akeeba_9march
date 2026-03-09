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
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * JFormFieldSmsplg form custom element class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.6
 */
class JFormFieldSmsplg extends FormField
{
	protected $type = 'smsplg';

	protected $name = 'smsplg';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	public function getInput()
	{
		return $this->fetchElement($this->name, $this->value, $this->element, (isset($this->options['control']) ? $this->options['control'] : ''));
	}

	/**
	 * Get needed field data
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value of the field
	 * @param   string  &$node         Node of the field
	 * @param   string  $control_name  Field control name
	 *
	 * @return   string  Field HTML
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = Factory::getDBO();

		$condtion = array(0 => '\'tjsms\'');
		$condtionatype = join(',', $condtion);

		$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1";
		$db->setQuery($query);
		$smsplugin = $db->loadobjectList();

		$options = array();

		foreach ($smsplugin as $sms_opt)
		{
			$sms_opt_name = ucfirst(str_replace('plugsms', '', $sms_opt->element));
			$options[] = HTMLHelper::_('select.option', $sms_opt->element, $sms_opt_name);
		}

		$fieldName = $name;
		$html = HTMLHelper::_(
		'select.genericlist', $options, $fieldName, 'class="inputbox required"', 'value', 'text', $value,
		$control_name . $name
		);

		// Show link for payment plugins.
		$html .= '<a
			href="index.php?option=com_plugins&view=plugins&filter_folder=tjsms&filter_enabled="
			target="_blank"
			class="btn btn-small btn-primary">'
				. Text::_('PLG_SYSTEM_QTC_SMS_SETUP_SMS_PLUGINS') .
			'</a>';

		return $html;
	}

	/**
	 * Get field tooltip
	 *
	 * @param   string  $label         Label of the field
	 * @param   string  $description   Description of the field
	 * @param   string  &$node         Node of the field
	 * @param   string  $control_name  Field control name
	 * @param   string  $name          Field name
	 *
	 * @return   string  Field HTML
	 */
	public function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return null;
	}
}
