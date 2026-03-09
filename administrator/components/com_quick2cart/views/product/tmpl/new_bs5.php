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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

$jinput           = Factory::getApplication()->input;
$qtc_product_name = $jinput->get('qtc_article_name', '', 'RAW');

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

// Load helper file if not exist
if (!class_exists('comquick2cartHelper'))
{
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

$comquick2cartHelper = new comquick2cartHelper;
$qtcshiphelper       = new qtcshiphelper;
$productHelper       = new productHelper;
$params              = ComponentHelper::getParams('com_quick2cart');
$currencies          = $params->get('addcurrency');

$qtc_shipping_opt_status = $this->params->get('shipping', 0);
$isTaxationEnabled       = $this->params->get('enableTaxtion', 0);

HTMLHelper::_('script', 'administrator/components/com_tjfields/assets/js/tjfields.js');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css');

JLoader::import('attributes', JPATH_SITE . '/components/com_quick2cart/models');
$quick2cartModelAttributes = new quick2cartModelAttributes();
$currencies                = $params->get('addcurrency');
$curr                      = explode(',', $currencies);
$currencies_sym            = $params->get('addcurrency_sym');

if (!empty($currencies_sym))
{
	$curr_syms = explode(',', $currencies_sym);
}

Text::script('COM_QUICK2CART_ADD_PROD_SEL_ATTRIBUTE_OPTION', true);
?>
<script type="text/javascript">
	function myValidate(f)
	{
		var title = document.getElementById('item_name').value;

		if(title.trim() == '')
		{
			 return false;
		}

		f.check.value='<?php echo Session::getFormToken(); ?>';

		return true;
	}

	Joomla.submitbutton = function(task)
	{

		var submit_status=myValidate(document.adminForm);

		if (task=="product.cancel")
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else if (task != 'product.cancel' && submit_status && document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else
		{
			alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			return false;
		}

		if (task=='product.save'  || task=="product.saveAndClose")
		{
			// Check for slab condition
			var slabvalue=techjoomla.jQuery('#item_slab').val();
			if(slabvalue!=1 && slabvalue!=0)
			{
				var minval=techjoomla.jQuery('#min_item').val();

				if(minval!='' && minval!=0)
				{
					var minvaluecheck=minval%slabvalue;
					if(minvaluecheck!=0)
					{
						alert("<?php echo Text::_('QTC_SLAB_MIN_QTY')?>");
						return false;
					}
				}
				else{
					alert("<?php echo Text::_('QTC_SLAB_MIN_QTY')?>");
						return false;
				}
				if((minval.trim)<(slabvalue.trim))
				{
					alert("<?php echo Text::_('QTC_SLAB_MIN_QTY')?>");
					return false;
				}
			}

			if (task=='product.save')
			{
				document.adminForm.task.value='product.save';
			}
			else
			{
				document.adminForm.task.value='product.saveAndClose';
			}
		}
	}
</script>

<?php
// If catagories are not presnt then show appropriate msg
if (empty($this->cats))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
		<div class="well well-small" >
			<div class="alert alert-error">
				<span><?php echo Text::_('QTC_NO_FOUND_CONTACT_TO_ADMIN'); ?></span>
			</div>
		</div>
	</div>
	<!-- eoc techjoomla-bootstrap -->
<?php
	return;
}
?>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> add-product">
	<form name="adminForm" id="adminForm" class="form-validate form-horizontal" method="post" enctype="multipart/form-data">
		<?php
		if (!$this->store_id)
		{
			?>
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert"></button>
				<?php echo Text::_('QTC_NO_STORE'); ?>
			</div>
		</div>
		<!-- eoc techjoomla-bootstrap -->
		<?php
			return false;
		}
		?>
		<legend>
			<?php echo (!empty($this->itemDetail)) ? Text::_("QTC_EDIT_PRODUCT") : Text::_("QTC_ADD_PRODUCT");?>
		</legend>

		<?php
		echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'qtctab1', 'recall' => true, 'breakpoint' => 768]);
			echo HTMLHelper::_('uitab.addTab', 'myTab', 'qtctab1', Text::_('QTC_PRODUCTS_BASIC_DETAIL'));
				$att_list_path = $comquick2cartHelper->getViewpath('product', 'options_bs5', "ADMINISTRATOR", "ADMINISTRATOR");
				ob_start();
				include($att_list_path);
				$item_options = ob_get_contents();
				ob_end_clean();
				echo $item_options;
			echo HTMLHelper::_('uitab.endTab');

			echo HTMLHelper::_('uitab.addTab', 'myTab', 'qtctab2', Text::_('QTC_PROD_ATTRI_INFO'));
				$canDisplayAttriContent = empty($this->item_id) ? 0 : 1; ?>
				<div id="qtcAttributeTabContent" style="<?php echo ($canDisplayAttriContent == 0) ? 'display:none;' : ''; ?>">
					<?php
					$att_list_path = $comquick2cartHelper->getViewpath('product', 'attribute_bs5', "ADMINISTRATOR", "ADMINISTRATOR");
					ob_start();
					include($att_list_path);
					$html_attri = ob_get_contents();
					ob_end_clean();
					echo $html_attri;
					?>
				</div>
				<div id="qtcAttributeTabContentHideMsg" style="<?php echo ($canDisplayAttriContent == 1) ? 'display:none;' : ''; ?>">
					<div class="alert alert-info">
						<?php echo Text::_('COM_QUICK2CART_PRODUCT_SAVE_PROD_TO_ADD_ATTRI_MSG');?>
					</div>
				</div>
				<?php
			echo HTMLHelper::_('uitab.endTab');

			$eProdSupport = $params->get('eProdSupport', 0);

			if ($eProdSupport)
			{
				echo HTMLHelper::_('uitab.addTab', 'myTab', 'qtcMediatab', Text::_('QTC_PROD_MEDIA_DETAILS'));

					// Check for view override
					$mediaDetail = $comquick2cartHelper->getViewpath('product', 'medialist_bs5', "ADMINISTRATOR", "ADMINISTRATOR");
					ob_start();
					include($mediaDetail);
					$mediaDetail = ob_get_contents();
					ob_end_clean();
					echo $mediaDetail;
				echo HTMLHelper::_('uitab.endTab');
			}

			if ($isTaxationEnabled  || $qtc_shipping_opt_status)
			{
				echo HTMLHelper::_('uitab.addTab', 'myTab', 'taxshipTab', Text::_('COM_QUICK2CART_TAX_ND_SHIPPING_TAB'));

					// Check for view override
					$taxshipPath = $comquick2cartHelper->getViewpath('product', 'taxship_bs5', "ADMINISTRATOR", "ADMINISTRATOR");
					ob_start();
					include($taxshipPath);
					$taxshipDetail = ob_get_contents();
					ob_end_clean();
					echo $taxshipDetail;
				echo HTMLHelper::_('uitab.endTab');
			}

			if (!empty($this->form_extra) || empty($this->item_id))
			{
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
						echo HTMLHelper::_('uitab.addTab', 'myTab', 'taxshipTab', str_replace(' ', '', $this->escape($fieldSetName)));

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
									if ($field->hidden)
									{
										echo $field->input;
									}
									else
									{
										?>
										<div class="form-group row">
											<div class="form-label col-md-4">
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
											<div class="col-md-8">
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
						echo HTMLHelper::_('uitab.endTab');
					}
				}
			}

		echo HTMLHelper::_('uitab.endTabSet');?>
		<div class="clearfix">&nbsp;</div>
		<hr/>
		<div>
			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" name="task" value="product.save" />
			<input type="hidden" name="view" value="product" />
			<input type="hidden" name="pid" value="<?php echo $this->item_id;?>" />
			<input type="hidden" name="client" value="com_quick2cart" />
			<input type="hidden" name="check" value="post"/>
		</div>
	</form>
</div>
