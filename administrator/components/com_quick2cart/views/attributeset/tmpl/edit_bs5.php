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

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('formbehavior.chosen', 'select');
}

HTMLHelper::_('script', 'libraries/techjoomla/assets/js/tjvalidator.js');
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');
?>
<script type="text/javascript">
	techjoomla.jquery = jQuery.noConflict();

	Joomla.submitbutton = function(task)
	{
		if (task == 'attributeset.cancel')
		{
			Joomla.submitform(task, document.getElementById('attributeset-form'));
		}
		else
		{
			if (task != 'attributeset.cancel' && document.formvalidator.isValid(document.getElementById('attributeset-form')))
			{
				Joomla.submitform(task, document.getElementById('attributeset-form'));
			}
			else
			{
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}

	function add_attribute_to_list()
	{
		var wrapper = techjoomla.jquery(".selectedattributes"); //Fields wrapper
		var e = document.getElementById("attributelist");
		var selectedOptiontext = e.options[e.selectedIndex].text;
		var selectedOptionvalue = e.options[e.selectedIndex].value;
		var attributesetid = <?php echo $this->item->id?$this->item->id:0;?>
		// note - take it from form itself
		var optionListCount = techjoomla.jquery('.qtcattributeclone').length;
		var optionListCountNew = optionListCount+1;
		if(!techjoomla.jquery("#qtcattribute"+selectedOptionvalue).length)
		{
			if (selectedOptionvalue != 0)
			{
				var url = "?option=com_quick2cart&task=attributeset.addattribute&attributeid="+selectedOptionvalue+"&attributesetid="+attributesetid;
				techjoomla.jQuery.ajax({
					type: "get",
					url:url,
					async:false,
					success: function(response)
					{
						if (!techjoomla.jquery("#qtcoptionheading").length)
						{
							techjoomla.jquery(wrapper).append('<div class="form-group row" id="qtcoptionheading"><div class="col-md-8 row"><span class="qtc-attributeset-name col-md-3"><b><?php echo Text::_('COM_QUICK2CART_GLOBALATTRIBUTES_ATTRIBUTE_NAME');?></b></span><span class="col-md-3"><b><?php echo Text::_('COM_QUICK2CART_FORM_LBL_ATTRIBUTE_ORDERING');?></b></span></div></div>');
						}
						//on add options click add an clone of option fields
						techjoomla.jquery(wrapper).append('<div class="form-group row"><div id="qtcoptionclone" class="col-md-8 row qtcattributeclone"><span class="col-md-3"><input type="text" class="input-medium disabled center" disabled="disabled" id="qtcattribute'+selectedOptionvalue+'" name="attributes['+optionListCount+'][attribute_name]" placeholder="attribute Name" value="'+selectedOptiontext+'"></span>	<span class="col-md-3"><input type="text" class="input-small" name="attributes['+optionListCount+'][attribute_option]" placeholder="Order" value="'+optionListCountNew+'"></span>	<span class="col-md-3"><a class="btn btn-sm btn-danger" onclick="removeclone('+selectedOptionvalue+')"><?php echo Text::_('COM_QUICK2CART_REMOVE_OPTION');?></a><span><input type="hidden" class="input-small" name="attributes['+optionListCount+'][id]" placeholder="attribute id" value="'+selectedOptionvalue+'"></span></span></div></div>');
						optionListCount++;
					},
					error: function(response)
					{
						alert("error");
						console.log(' ERROR!!' );
						return e.preventDefault();
					}
				});
			}
			else
			{
				alert("Please select attribute");
			}
		}
		else
		{
			alert("Attribute already selected");
		}
	}

	function removeclone(clone_id)
	{
		var confirmdelete = confirm("<?php echo Text::_('COM_QUICK2CART_REMOVE_ATTRIBUTE_MSG');?>");

		if( confirmdelete == false )
		{
			return false;
		}

		var attributesetid = <?php echo $this->item->id?$this->item->id:0;?>;
		var url = "?option=com_quick2cart&task=attributeset.removeattribute&attributeid="+clone_id+"&attributesetid="+attributesetid;
		techjoomla.jQuery.ajax({
			type: "get",
			url:url,
			async:false,
			success: function(response)
			{
				var message = JSON.parse(response);

				if(message[0].error)
				{
					alert(message[0].error);
				}
				else
				{
					techjoomla.jquery('#qtcattribute'+clone_id).parent().parent().parent().remove();

					if (!techjoomla.jquery("#qtcoptionclone").length)
					{
						techjoomla.jquery('#qtcoptionheading').remove();
					}
				}
			},
			error: function(response)
			{
				alert("error");
				console.log(' ERROR!!' );
				return e.preventDefault();
			}
		});
	}
</script>

<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="attributeset-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<?php if($this->item->id != null):?>
				<div class="alert alert-info">
					<?php echo Text::_("COM_QUICK2CART_GLOBAL_ATTRIBUTE_SET_INFO");?>
				</div>
			<?php endif;?>
				<div class="col-md-12 form-horizontal">
					<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
					<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
					<div class="form-group row">
						<div class="form-label col-md-4"><?php echo $this->form->getLabel('global_attribute_set_name'); ?></div>
						<div class="col-md-4"><?php echo $this->form->getInput('global_attribute_set_name'); ?></div>
					</div>
					<?php
					if (!isset($this->item->id))
					{?>
						<div class="alert alert-info"><?php echo Text::_('COM_QUICK2CART_ATTRIBUTESET_TOOLTIP');?></div>
					<?php 
					}
					else
					{?>
						<div class="form-group row">
							<label class="form-label col-md-4">
								<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_SELECT_ATTRIBUTE_DESC'), Text::_('COM_QUICK2CART_SELECT_ATTRIBUTE'), '', Text::_('COM_QUICK2CART_SELECT_ATTRIBUTE'));?>
							</label>
							<div class="col-md-4">
								<?php echo HTMLHelper::_('select.genericlist', $this->attributeList, "attributelist", 'class="form-select col-sm-4" size="1" name="attributeList"', "value", "text", $this->state->get('filter.campaignslist'));?>
							</div>
							<div class="col-md-4">
								<div class="btn btn btn-success mt-1" onclick="add_attribute_to_list()"><?php echo Text::_('COM_QUICK2CART_ADD_OPTION');?></div>
							</div>
						</div>
					<?php 
					}?>
				</div><br><br>
				<?php
				if (!empty($this->attributeLists)):?>
					<div class="form-group row" id="qtcoptionheading">
						<div class="col-md-8 row">
							<span class="qtc-attributeset-name col-md-3">
								<b><?php echo Text::_('COM_QUICK2CART_GLOBALATTRIBUTES_ATTRIBUTE_NAME');?></b>
							</span>
							<span class="col-md-3">
								<b><?php echo Text::_('COM_QUICK2CART_FORM_LBL_ATTRIBUTE_ORDERING');?></b>
							</span>
						</div>
					</div>
					<?php
					foreach($this->attributeLists as $key => $attributeDetail):?>
						<div class="form-group row">
							<div id="qtcoptionclone" class="col-md-8 row qtcattributeclone">
								<span class="col-md-3">
									<input type="text" class="input-medium disabled center" disabled="disabled" id="qtcattribute<?php echo $attributeDetail['id']?>" name="attributes[<?php echo $key;?>][attribute_name]" placeholder="attribute Name" value="<?php echo $attributeDetail['attribute_name']?>">
								</span>
								<span class="col-md-3">
									<input type="text" class="input-small" name="attributes[<?php echo $key;?>][attribute_option]" placeholder="Order" value="<?php echo $key+1;?>">
								</span>
								<span class="col-md-3">
									<a class="btn btn-sm btn-danger" onclick="removeclone(<?php echo $attributeDetail['id']?>)"><?php echo Text::_('COM_QUICK2CART_REMOVE_OPTION');?></a>
									<input type="hidden" class="input-small" name="attributes[<?php echo $key;?>][id]" placeholder="attribute id" value="<?php echo $attributeDetail['id']?>">
								</span>
							</div>
						</div>
					<?php
					endforeach;?>
				<?php
				endif;?>
				<div class="row-fluid">
					<div class="selectedattributes"></div>
				</div>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="attributeset" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
