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

if (!empty($this->form_extra))
{
	$fieldSetNames = array();

	foreach ($this->form_extra->getFieldsets() as $fieldsets => $fieldset)
	{
		if (!in_array($fieldset->name, $fieldSetNames))
		{
			$fieldSetNames[] = $fieldset->name;
		}
	}

	foreach ($fieldSetNames as $fieldSetName)
	{
		?>
		<li id="<?php echo str_replace(' ', '', $this->escape($fieldSetName)) . 'id';?>" >
			<a href="#tabId<?php echo str_replace(' ', '', $this->escape($fieldSetName));?>" data-toggle="tab">
				<?php echo str_replace(' ', '', $this->escape($fieldSetName));?>
			</a>
		</li>
		<?php
	}
}
