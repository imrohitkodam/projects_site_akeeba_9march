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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Form\Field\ListField;

if (JVERSION < '4.0')
{
	/**
	 * Class for custom gateway element
	 *
	 * @since  1.0.0
	 */
	class JFormFieldGatewayplg extends FormField
	{
		protected $type = 'Gatewayplg';

		protected $name = 'Gatewayplg';

		/**
		 * Method to get the field label markup.
		 *
		 * @return  string  The field label markup.
		 *
		 * @since   11.1
		 */
		public function getLabel()
		{
			if ($this->hidden)
			{
				return '';
			}

			// Get the label text from the XML element, defaulting to the element name.
			$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$text = $this->translateLabel ? Text::_($text) : $text;

			// Forcing the Alias field to display the tip below
			$position = $this->element['name'] == 'alias' ? ' data-placement="bottom" ' : '';

			$description = ($this->translateDescription && !empty($this->description)) ? Text::_($this->description) : $this->description;

			$displayData = array(
					'text'        => $text,
					'description' => $description,
					'for'         => 'jformgateways',
					'required'    => (bool) $this->required,
					'classes'     => explode(' ', $this->labelclass),
					'position'    => $position
				);

			return LayoutHelper::render($this->renderLabelLayout, $displayData);
		}

		/**
		 * Function to genarate html of custom element
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function getInput()
		{
			$control = (isset($this->options['control'])) ? $this->options['control'] : '';
			return $this->fetchElement($this->name, $this->value, $this->element, $control);
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $name          name of field
		 * @param   string  $value         value of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function fetchElement($name, $value, &$node, $control_name)
		{
			$db = Factory::getDBO();
			$condtion = array(0 => '\'payment\'');
			$condtionatype = join(',', $condtion);

			$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1";
			$db->setQuery($query);
			$gatewayplugin = $db->loadobjectList();
			$options = array();

			foreach ($gatewayplugin as $gateway)
			{
				$gatewayname = ucfirst(str_replace('plugpayment', '', $gateway->element));
				$options[] = HTMLHelper::_('select.option', $gateway->element, $gatewayname);
			}

			$fieldName = $name;
			$selectClass  = (JVERSION < '4.0.0') ? ' inputbox ' : ' form-select ';

			$html = HTMLHelper::_('select.genericlist', $options, $fieldName,
			'class="' . $selectClass . ' required"  multiple="multiple" size="5"', 'value', 'text', $value, $control_name . $name
			);

			$class = "";

			// Show link for payment plugins.
			$html .= '<a
				href="index.php?option=com_plugins&view=plugins&filter_folder=payment&filter_enabled="
				target="_blank"
				class="btn btn-small btn-primary ' . $class . '">'
					. Text::_('COM_QUICK2CART_SETTINGS_SETUP_PAYMENT_PLUGINS') .
				'</a>';

			return $html;
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $label         label of field
		 * @param   string  $description   description of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 * @param   string  $name          name of field
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function fetchTooltip($label, $description, &$node, $control_name, $name)
		{
			return null;
		}
	}
}
else
{
	/**
	 * Class for custom gateway element
	 *
	 * @since  1.0.0
	 */
	class JFormFieldGatewayplg extends ListField
	{
		/**
		 * Function to genarate html of custom element
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function getInput()
		{
			$html  = parent::getInput();
			$html .= '<br><a href="index.php?option=com_plugins&view=plugins&filter_folder=payment&filter_enabled="
				target="_blank" class="btn btn-small btn-primary">'. Text::_('COM_QUICK2CART_SETTINGS_SETUP_PAYMENT_PLUGINS') . '</a>';
			return $html;
		}

		/**
		 * Function to fetch a tooltip
		 *
		 * @param   string  $name          name of field
		 * @param   string  $value         value of field
		 * @param   string  &$node         node of field
		 * @param   string  $control_name  control_name of field
		 *
		 * @return  HTML
		 *
		 * @since  1.0.0
		 */
		public function getPaymentPlgData()
		{
			$db            = Factory::getDBO();
			$condtion      = array(0 => '\'payment\'');
			$condtionatype = join(',', $condtion);
			$query         = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1";
			$db->setQuery($query);

			return $gatewayplugin = $db->loadobjectList();
		}

		public function getOptions()
		{
			$options               = array();
			$payment_plg_installed = $this->getPaymentPlgData();

			foreach ($payment_plg_installed as $paymentPlg)
			{
				$options[] = HTMLHelper::_('select.option', $paymentPlg->element, $paymentPlg->element);
			}

			return $options;
		}
	}
}
