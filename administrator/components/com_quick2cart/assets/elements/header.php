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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
 * Gives HTML element as header
 *
 * @since  1.6
 */
class JFormFieldHeader extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Header';

	/**
	 * Function to fetch header
	 *
	 * @return  HTML
	 *
	 * @since  1.0.0
	 */
	public function getInput()
	{
		HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');

		$return = '
		<div class="q2cHeaderOuterDiv">
			<div class="q2cHeaderInnerDiv">
				' . Text::_($this->value) . '
			</div>
		</div>';

		return $return;
	}
}
