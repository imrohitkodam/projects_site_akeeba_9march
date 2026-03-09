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
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$doc = Factory::getDocument();
HTMLHelper::_('script',Uri::root(true) . '/libraries/techjoomla/assets/js/tjvalidator.js');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'taxprofile.cancel')
		{
			Joomla.submitform(task, document.getElementById('taxprofile-form'));
		}
		else
		{
			if (task != 'taxprofile.cancel' && document.formvalidator.isValid(document.getElementById('taxprofile-form')))
			{
				Joomla.submitform(task, document.getElementById('taxprofile-form'));
			}
			else
			{
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}

	function qtcDeleteProfileRule(ruleId,delBtn)
	{
		var data = {
			jform : {
				taxrule_id : ruleId,
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=taxprofile&task=taxprofile.deleteProfileRule",
			data : data,
			success : function(response)
			{
				if (response.error!=1)
				{
					techjoomla.jQuery(delBtn).closest('tr').remove();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});
	}

    function qtc_addTaxRule()
    {
		var qtc_taxrate_id = document.getElementById('jformtaxrate_id').value;
		var qtc_address = document.getElementById('jformaddress').value;

		if(qtc_taxrate_id == '' || qtc_address == '')
		{
			Joomla.renderMessages({ 'error': ["<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_INVALID_SELECTION'); ?>"] });
			return false;
		}

		var data = {
			jform : {
				taxprofile_id : document.getElementById('jform_taxprofile_id').value,
				taxrate_id : qtc_taxrate_id,
				address : qtc_address,
			}
		};

		var taxrate = techjoomla.jQuery("#jformtaxrate_id").children("option").filter(":selected").text() ;
		var address = techjoomla.jQuery("#jformaddress").children("option").filter(":selected").text() ;
		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=taxprofile&task=taxprofile.addTaxRule",
			data : data,
			dataType: "json",
			success : function(response)
			{
				if (response.error != 1)
				{
					// Remove Error dive content
					techjoomla.jQuery('#qtcErrorContentDiv').html('');
					techjoomla.jQuery('.qtcError').fadeOut();

					var taxrule_id= response.taxrule_id;
					var q="'";

					window.parent.location.reload();

				} else {
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});

		return false;
	}
	function toggleSetTaxProfileModal(id)
	{
		jQuery('#setTaxProfileModal_'+id).attr('data-width' , (window.innerWidth)/2);
		jQuery('#setTaxProfileModal_'+id).attr('data-height' , window.innerHeight);
		jQuery('#setTaxProfileModal_'+id).modal('show');
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="taxprofile-form" class="form-validate">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<fieldset class="adminform">
						<legend><?php echo Text::_('COM_QUICK2CART_TAXPROFILE'); ?></legend>
						<input type="hidden" name="jform[id]" id="jform_taxprofile_id" value="<?php echo $this->item->id; ?>" />
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>
						<?php
						if (empty($this->item->id))
						{
							?>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('store_id'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('store_id'); ?>
									<span class="help-block">
										<i class="icon-hand-right"></i>
										<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_CAN_NOT_CAHGNE_STORE_MSG'); ?>
									</span>
								</div>
							</div>
							<?php
						}
						else
						{
							?>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('store_id'); ?></div>
								<div class="controls">
									<input type="text" class="form-control" readonly value="<?php echo ucfirst($this->storeDetails['title']); ?>">
									<span style="display:none;">
									<?php echo $this->form->getInput('store_id'); ?>
									</span>
								</div>
							</div>
							<?php
						}?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>
						<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
						<div class="alert alert-info">
							<?php echo Text::_('COM_QUICK2CART_TAXPROFILES_HELP_TEXT');?>
						</div>
					</fieldset>
					<fieldset>
						<?php
						if (!empty($this->item->id))
						{
						?>
							<legend>
								<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADD_TAXRATES'); ?>
								<small><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_TAXRATE_MAP_HELP'); ?></small>
							</legend>

							<!-- For Error Display-->
							<div class="error alert alert-danger qtcError" style="display: none;">
								<i class="icon-cancel pull-right" style="align: right;"
									onclick="techjoomla.jQuery(this).parent().fadeOut();"> </i>
								<div id="qtcErrorContentDiv"></div>
							</div>

							<!-- Map the tax rule aginst tax profile -->
							<table class="row-fluid">
								<tr>
									<td class="span4">
										<?php echo $this->taxrate; ?>
									</td>
									<td class="span4">
										<span title="<?php echo Text::_("COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE_MSG");?>">
											<?php echo $this->address; ?>
										</span>
									</td>
									<td valign="top" class="span4"><input type="button" id="CreateTaxRule"
										value="<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE'); ?>"
										class="btn btn-success pull-right" onClick="qtc_addTaxRule()" />
									</td>
								</tr>
								<tr >
									<td colspan="3">
										<p class="text-info"><?php echo Text::_("COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE_MSG");?></p>
									</td>
								</tr>
							</table>

							<!-- Show the taxprofile rules -->
							<table class="adminlist table table-striped table-bordered">
								<thead>
									<tr>
										<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_TAXRATE'); ?> </th>
										<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADDRESS'); ?></th>
										<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></th>
									</tr>
								</thead>
								<tbody id="tableBody">
									<?php
									foreach($this->taxrules as $trule)
									{ ?>
										<tr>
											<td id="qtc_taxrate_<?php echo $trule->taxrule_id; ?>" >
												<?php echo $trule->name; ?>&nbsp;(<?php echo floatval($trule->percentage); ?>%)
											</td>
											<td id="qtc_address_<?php echo $trule->taxrule_id; ?>">
												<?php echo ucfirst($trule->address);?>
											</td>
											<td>
												<button class="btn btn-primary" type="button" onclick="toggleSetTaxProfileModal('<?php echo $trule->taxrule_id ?>');">
													<?php echo Text::_('COM_QUICK2CART_TAXPROFILEERULE_EDIT'); ?>
												</button>
												<?php
													$setTaxProfilelink = "index.php?option=com_quick2cart&view=taxprofile&layout=setrule_bs2&id=" . $trule->taxrule_id . "&tmpl=component";

													echo HTMLHelper::_(
														'bootstrap.renderModal',
														'setTaxProfileModal_' . $trule->taxrule_id,
														array(
															'title'      => Text::_('COM_QUICK2CART_TAXPROFILEERULE_EDIT'),
															'url'        => $setTaxProfilelink,
															'modalWidth' => '35',
															'bodyHeight' => '50',
														)
													)
												?>

												<input onclick="qtcDeleteProfileRule(<?php echo $trule->taxrule_id;?>,this);" class="btn btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_PROFILERULE_DELETE'); ?>">
											</td>
										</tr>
										<?php
									}?>
								</tbody>
							</table>
						<?php
						}
						?>
					</fieldset>
				</div>
			</div>
			<input type="hidden" name="task" value="" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div> <!--qyc_admin_taxprofile -->
