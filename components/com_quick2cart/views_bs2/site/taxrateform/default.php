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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

?>

<script type="text/javascript">

function qtcsubmitAction(action)
{
	var form = document.taxrateForm;
	var valid =document.formvalidator.isValid(document.getElementById('taxrateForm'));
	if(valid == false)
	{
		alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_ZONEFORM_FILL_REQUIRED_FIELDS')); ?>");
		return false;
	}
	switch(action)
	{
		case 'save': form.task.value='taxrateForm.save';
		break

		case 'saveAndClose':
		form.task.value='taxrateForm.saveAndClose';
		break
	}

	form.submit();

	return;
 }

</script>

<div class="qtc_site_taxrate <?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	$helperobj = new comquick2cartHelper;
	$active = 'zones';
	$order_currency = $helperobj->getCurrencySession();
	$view = $helperobj->getViewpath('vendor', 'toolbar');
	ob_start();
		include $view;
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;
	?>

	<?php
	if (!empty($this->item->id))
	{ ?>
		<legend><?php echo Text::_('COM_QUICK2CART_S_EDIT_TAX_RATE');?></legend>
		<?php
	}
	else
	{ ?>
		<legend><?php echo Text::_('COM_QUICK2CART_S_ADD_TAX_RATE');?></legend>
		<?php
	}
	?>

    <form id="taxrateForm" name="taxrateForm"   method="post" class="form-validate" enctype="multipart/form-data">
		<!-- Form action part-->
		<div class="row-fluid">
			<div class="form-horizontal">
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

				<div class="control-group">
					<div class="form-actions ">

						<button type="button" class="btn btn-success validate" title="<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>" onclick="qtcsubmitAction('save');">
							<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>
						</button>
						<button type="button" class="btn btn-default validate" title="<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>" onclick="qtcsubmitAction('saveAndClose');">
							<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>
						</button>

						 <a href="<?php echo Route::_('index.php?option=com_quick2cart&task=taxrateform.cancel&id=' . $this->item->id); ?>" class="btn " title="<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>">
							<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>
						 </a>

					</div>
				</div>

			</div>
		</div>


		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="task" value="taxrateform.save" />
		<?php echo HTMLHelper::_('form.token'); ?>

		<input type="hidden" name="id" id="id" value="<?php echo $this->item->get('id')?>" />
		<?php echo HTMLHelper::_('form.token'); ?>

    </form>

</div>
