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
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal');

$doc = Factory::getDocument();
HTMLHelper::_('script', 'libraries/techjoomla/assets/js/tjvalidator.js');
?>
<script type="text/javascript">
	function qtcsubmitAction(action)
	{
		var form = document.taxprofileform;

		switch(action)
		{
			case 'save': form.task.value='taxprofileform.save';
			break

			case 'saveAndClose':
			form.task.value='taxprofileform.saveAndClose';
			break
		}

		form.submit();
		return;
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
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=taxprofileform&task=taxprofileform.deleteProfileRule&tmpl=component",
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
			Joomla.renderMessages({ 'error': ["<?php echo Text::_('COM_QUICK2CART_S_INVALID_SELECTION'); ?>"] });
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
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=taxprofileform&task=taxprofileform.addTaxRule&tmpl=component",
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
					var editbtn = '<input type="button" value="<?php echo Text::_('COM_QUICK2CART_TAXPROFILEERULE_EDIT'); ?>" class="btn btn-primary">';
					var editHref = 'index.php?option=com_quick2cart&view=taxprofileform&layout=setrule&id='+taxrule_id+'&tmpl=component';
					var editLink = '<a rel="{handler:\'iframe\',size:{x: window.innerWidth-450, y: window.innerHeight-250}}" href="'+editHref+'" class="modal qtc_modal">'+editbtn+'</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

					var delLink = '<input onclick="qtcDeleteProfileRule('+
								taxrule_id+',this);" class="btn btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_PROFILERULE_DELETE'); ?>">';
					//alert(links);
					var result='<tr><td id="qtc_taxrate_'+taxrule_id+'">'+taxrate+'</td><td id="qtc_address_'+taxrule_id+'">'+address+'</td><td>' + editLink + delLink + '</td></tr>';
					techjoomla.jQuery('#tableBody').append(result);
					window.parent.location.reload();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});

		return false;
	}

	function toggleSetTaxProfileRuleModal(id)
	{
		jQuery('#setTaxProfileRuleModal_'+id).attr('data-width' , (window.innerWidth)/2);
		jQuery('#setTaxProfileRuleModal_'+id).attr('data-height' , window.innerHeight);
		jQuery('#setTaxProfileRuleModal_'+id).modal('show');
	}
</script>
<div class=" <?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	$helperobj      = new comquick2cartHelper;
	$active         = 'zones';
	$order_currency = $helperobj->getCurrencySession();
	$view           = $helperobj->getViewpath('vendor','toolbar_bs5');
	ob_start();
		include $view;
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;
	?>
	<form id="taxprofileform" name="taxprofileform" method="post" class="form-validate container-fluid" enctype="multipart/form-data">
		<div class="row">
			<div class="form-horizontal">
				<fieldset class="adminform">
					<h1><strong><?php echo Text::_('COM_QUICK2CART_TAXPROFILE'); ?></strong></h1>
					<div class="form-group row">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<?php
					if (empty($this->item->id))
					{
						?>
						<div class="form-group row">
							<div class="col-sm-3 col-xs-12 form-label">
								<?php echo $this->form->getLabel('store_id'); ?>
							</div>
							<div class="col-sm-9 col-xs-12">
								<?php echo $this->form->getInput('store_id'); ?>
								<div class="text-warning">
									<p><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_CAN_NOT_CAHGNE_STORE_MSG'); ?></p>
								</div>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="form-group row">
							<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('store_id'); ?></div>
							<div class="col-sm-9 col-xs-12">
								<input type="text" class="form-control" readonly value="<?php echo ucfirst($this->storeDetails['title']); ?>">
								<span style="display:none;">
									<?php echo $this->form->getInput('store_id'); ?>
								</span>
							</div>
						</div>
						<?php
					}?>
					<div class="form-group row">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('state'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('state'); ?></div>
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
							<i class="<?php echo QTC_ICON_REMOVE; ?> float-end" style="align: right;" onclick="techjoomla.jQuery(this).parent().fadeOut();"> </i>
							<div id="qtcErrorContentDiv"></div>
						</div>

						<div class="text-info">
							<p><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE_MSG'); ?></p>
						</div>

						<!-- Map the tax rule aginst tax profile -->
						<br/>
						<div class="row">
							<div class="col-sm-4 col-xs-12">
								<?php echo $this->taxrate;?>
							</div>
							<div class="col-sm-4 col-xs-12">
								<span title="<?php echo Text::_("COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE_MSG");?>">
									<?php echo $this->address;?>
								</span>
							</div>
							<div class="col-sm-4 col-xs-12">
								<input type="button" id="CreateTaxRule"
									value="<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADD_TAXRATE'); ?>"
									class="btn btn-success float-end" onClick="qtc_addTaxRule()" />
							</div>
						</div>

						<div class="clearfix">&nbsp;</div>
						<!-- Show the taxprofile rules -->
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_TAXRATE'); ?></th>
									<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ADDRESS'); ?></th>
									<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></th>
								</tr>
							</thead>
							<tbody id="tableBody">
								<?php
								$i=1;

								foreach($this->taxrules as $trule)
								{
									?>
									<tr>
										<td id="qtc_taxrate_<?php echo $trule->taxrule_id; ?>" >
											<?php echo $trule->name; ?>&nbsp;(<?php echo floatval($trule->percentage); ?>%)
										</td>
										<td id="qtc_address_<?php echo $trule->taxrule_id; ?>">
											<?php echo ucfirst($trule->address);?>
										</td>
										<td>
											<button class="btn btn-sm btn-primary" type="button" onclick="toggleSetTaxProfileRuleModal('<?php echo $trule->taxrule_id;?>');">
												<?php echo Text::_('COM_QUICK2CART_TAXPROFILEERULE_EDIT'); ?>
											</button>
											<?php
												$setTaxProfileRulelink = Route::_('index.php?option=com_quick2cart&view=taxprofileform&layout=setrule_bs5&id=' . $trule->taxrule_id . '&tmpl=component');

												echo HTMLHelper::_(
													'bootstrap.renderModal',
													'setTaxProfileRuleModal_' . $trule->taxrule_id,
													array(
														'title'      => Text::_('COM_QUICK2CART_TAXPROFILEERULE_EDIT'),
														'url'        => $setTaxProfileRulelink,
														'modalWidth' => '80',
														'bodyHeight' => '30',
														'width' => '100%',
														'height' => '300px',
													)
												)
											?>

											<input onclick="qtcDeleteProfileRule(<?php echo $trule->taxrule_id;?>,this);" class="btn btn-sm btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_PROFILERULE_DELETE'); ?>">
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

				<!-- Action part -->
				<div>
					<button type="button" class="btn btn-success validate" title="<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>" onclick="qtcsubmitAction('save');">
						<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>
					</button>
					<?php if($this->item->get('id')):?>
						<button type="button" class="btn btn-primary validate" title="<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>" onclick="qtcsubmitAction('saveAndClose');">
							<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>
						</button>
					<?php endif;?>
					<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=taxprofileform.cancel&id='.$this->item->id); ?>" class="btn btn-default" title="<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>">
						<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>
					</a>
				</div>
			</div>
		</div>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="task" value="taxprofileform.save" />
		<?php echo HTMLHelper::_('form.token'); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $this->item->get('id')?>" />
		<input type="hidden" name="jform[id]" id="jform_taxprofile_id" value="<?php echo $this->item->id; ?>" />
	</form>
</div>
