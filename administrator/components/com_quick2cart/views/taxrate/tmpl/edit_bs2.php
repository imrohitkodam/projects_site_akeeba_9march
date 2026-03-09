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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$doc = Factory::getDocument();
HTMLHelper::_('script',Uri::root(true) . '/libraries/techjoomla/assets/js/tjvalidator.js');

Text::script('QTC_ENTER_POSITIVE_PERCENTAGE', true);

// Import CSS
$document = Factory::getDocument();
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function() {
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'taxrate.cancel') {
			Joomla.submitform(task, document.getElementById('taxrate-form'));
		}
		else {

			if (task != 'taxrate.cancel' && document.formvalidator.isValid(document.getElementById('taxrate-form'))) {

				Joomla.submitform(task, document.getElementById('taxrate-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}

	function qtc_ispositive(ele)
	{
		var val=ele.value;
		if (val==0 || val < 0)
		{
			ele.value='';
			alert(Joomla.JText._('QTC_ENTER_POSITIVE_PERCENTAGE'));
			return false;
		}
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="taxrate-form" class="form-validate">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<input type="hidden" name="jform[taxrate_id]" value="<?php echo $this->item->id; ?>" />
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('percentage'); ?></div>
							<div class="controls">
								<div class="input-append ">
									<?php echo $this->form->getInput('percentage'); ?>
									<span class="add-on">%</span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('zone_id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('zone_id'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>
						<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" id="id" value="<?php echo $this->item->get('id')?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
