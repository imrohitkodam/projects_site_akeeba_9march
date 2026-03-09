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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

Text::script('QTC_ENTER_NUMERICS', true);
HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');
HTMLHelper::_('script', '/libraries/techjoomla/assets/js/tjvalidator.js');
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');
?>

<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function() {
		jQuery('#weight-form #jform_value').change(function() {
			checkforalpha(this, '46', Joomla.JText._('QTC_ENTER_NUMERICS'));
		});
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'weight.cancel') {
			Joomla.submitform(task, document.getElementById('weight-form'));
		}
		else {
			if (task != 'weight.cancel' && document.formvalidator.isValid(document.getElementById('weight-form'))) {
				Joomla.submitform(task, document.getElementById('weight-form'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>
<div class="qyc_admin_length">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="weight-form" class="form-validate">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<div class="control-group"></div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('unit'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('unit'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('value'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('value'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>
						<?php $creator  = (empty($this->item->created_by)) ? Factory::getUser()->id : $this->item->created_by; ?>
						<input type="hidden" name="jform[created_by]" value="<?php echo $creator; ?>" />
						<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
						<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
