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
		<div class="tab-pane" id="tabId<?php echo str_replace(' ', '', $this->escape($fieldSetName));?>">
		<?php
			foreach ($this->form_extra->getFieldsets() as $fieldsets => $fieldset)
			{
				if ($fieldset->name == $fieldSetName)
				{
					$fieldsArray = array();

					foreach ($this->form_extra->getFieldset($fieldset->name) as $field)
					{
						$fieldsArray[] = $field;
					}

					foreach ($fieldsArray as $field)
					{
						// If the field is hidden, only use the input.
						if ($field->hidden)
						{
							echo $field->input;
						}
						else
						{
						?>
							<div class="control-group">
								<div class="control-label">
									<label for="<?php echo $field->id;?>" title="<?php echo $field->title;?>">
										<?php echo $this->escape($field->getAttribute('label'));

										if ($field->getAttribute('required') == true)
										{
										?>
											<span class="star">&#160;*</span>
										<?php
										}
										?>
									</label>
								</div>
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php
						}
						?>
						<div class="clearfix">&nbsp;</div>
					<?php
					}
				}
			}
		?>
		</div>
		<?php
	}
}
