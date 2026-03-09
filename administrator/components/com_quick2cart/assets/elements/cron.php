<?php
/**
 * @package     JTicketing
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2024 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die();
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Class to display cron
 *
 * @package     JTicketing
 * @subpackage  component
 * @since       2.2
 */
class JFormFieldCron extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.0
	 */
	public $type = 'cron';

	/**
	 * Method to get the field input markup. @TODO: Add access check.
	 *
	 * @since  2.2.1
	 *
	 * @return   string  The field input markup
	 */
	protected function getInput()
	{
		switch ($this->name)
		{
			case 'jform[pkey_for_feedback_email]':
			case 'jform[pkey_for_coupon_email]':
				return $this->getCronKey(
					$this->name,
					$this->value,
					$this->element,
					isset($this->options['control']) ? $this->options['control'] : ''
				);

				break;

			case 'jform[cronjoburl_feedback_emails]':
			case 'jform[cronjoburl_coupon_emails]':

				return $this->getCronUrl(
					$this->name,
					$this->value,
					$this->element,
					isset($this->options['control']) ? $this->options['control'] : ''
				);

				break;
		}
	}

	/**
	 * Return cron key
	 *
	 * @param   string  $name          name of field
	 * @param   mixed   $value         value of field
	 * @param   string  $node          node of field
	 * @param   string  $control_name  controller name
	 *
	 * @since  2.2.1
	 *
	 * @return  string                 return html
	 */
	protected function getCronKey($name, $value, $node, $control_name)
	{
		// Generate randome string

		if (empty($value))
		{
			$length       = 10;
			$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';

			for ($i = 0; $i < $length; $i++)
			{
				$randomString .= $characters[rand(0, strlen($characters) - 1)];
			}

			return "<input type='text' class='form-control' name='$name' value=" . $randomString . ">";
		}

		return "<input type='text' class='form-control' name='$name' value=" . $value . "></label>";
	}

	/**
	 * Return cron url
	 *
	 * @param   string  $name          name of field
	 * @param   mixed   $value         value of field
	 * @param   string  $node          node of field
	 * @param   string  $control_name  controller name
	 *
	 * @since  2.2.1
	 *
	 * @return  string                 return html
	 */
	protected function getCronUrl($name, $value, $node, $control_name)
	{
		$params = ComponentHelper::getParams('com_quick2cart');

		switch ($name)
		{
			case 'jform[cronjoburl_feedback_emails]':
				$private_key_cronjob = $params->get('pkey_for_feedback_email');

				$cron_masspayment = Route::_(
								Uri::root() . 'index.php?option=com_quick2cart&task=orders.sendFeedbackReminderEmail&pkey='
								. $private_key_cronjob
							);
				$return	= '<label class="text-break">' . $cron_masspayment . '</label>';

				break;

			case 'jform[cronjoburl_coupon_emails]':
				$private_key_cronjob = $params->get('pkey_for_coupon_email');

				$cron_url = Route::_(
					Uri::root() . 'index.php?option=com_quick2cart&task=promotions.runPromotionEligibilityCheck&pkey='
					. $private_key_cronjob
				);
				$return = '<label class="text-break">' . $cron_url . '</label>';
				break;
		}

		return $return;
	}
}
