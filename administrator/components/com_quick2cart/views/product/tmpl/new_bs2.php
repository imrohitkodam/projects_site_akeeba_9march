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

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('formbehavior.chosen', 'select.q2c_product_category_select');
}

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
		}
		else
		{
			?>
			<legend>
				<?php echo (!empty($this->itemDetail)) ? Text::_("QTC_EDIT_PRODUCT") : Text::_("QTC_ADD_PRODUCT");?>
			</legend>

			<div class="tabbable">
				<ul class="nav nav-pills">
					<li id="tab1id" class="active">
						<a href="#qtctab1" data-toggle="tab"><?php echo JText::_("QTC_PRODUCTS_BASIC_DETAIL"); ?></a>
					</li>
					<li id="tab2id">
						<a href="#qtctab2" data-toggle="tab"><?php echo JText::_("QTC_PROD_ATTRI_INFO"); ?></a>
					</li>
					<?php
					$eProdSupport = $params->get('eProdSupport', 0);

					// If esupport
					if ($eProdSupport)
					{
						?>
						<li id="tab3id" class="">
							<a href="#qtcMediatab" data-toggle="tab"><?php echo JText::_("QTC_PROD_MEDIA_DETAILS"); ?></a>
						</li>
						<?php
					}

					// If taxation and shippping is enabled
					if ($isTaxationEnabled  || $qtc_shipping_opt_status)
					{
						?>
						<li id="taxshipTabId" class="">
							<a href="#taxshipTab" data-toggle="tab"><?php echo JText::_("COM_QUICK2CART_TAX_ND_SHIPPING_TAB"); ?></a>
						</li>
						<?php
					}

					if (!empty($this->form_extra) || empty($this->item_id))
					{
						echo $this->loadTemplate('extrafields_tab_header_bs2');
					}
					?>
				</ul>

				<div class="tab-content">
					<div class="tab-pane active" id="qtctab1">
						<?php
						// Check for view override
						$att_list_path = $comquick2cartHelper->getViewpath('product', 'options_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
						ob_start();
						include($att_list_path);
						$item_options = ob_get_contents();
						ob_end_clean();
						echo $item_options;
						?>
					</div>
					<!-- tab 1 end -->
					<div class="tab-pane" id="qtctab2">
						<?php $canDisplayAttriContent = empty($this->item_id) ? 0 : 1; ?>
						<div id="qtcAttributeTabContent" style="<?php echo ($canDisplayAttriContent == 0) ? 'display:none;' : ''; ?>">
							<?php
							// Check for view override
							$att_list_path = $comquick2cartHelper->getViewpath('product', 'attribute_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
							ob_start();
							include($att_list_path);
							$html_attri = ob_get_contents();
							ob_end_clean();
							echo $html_attri;
						?>
						</div>
						<div id="qtcAttributeTabContentHideMsg" style="<?php echo ($canDisplayAttriContent == 1) ? 'display:none;' : ''; ?>">
							<div class="alert alert-info">
								<?php echo JText::_('COM_QUICK2CART_PRODUCT_SAVE_PROD_TO_ADD_ATTRI_MSG');?>
							</div>
						</div>
					</div>
				<?php
				if ($eProdSupport)
				{
					?>
					<div class="tab-pane" id="qtcMediatab">
						<?php
						// Check for view override
						$mediaDetail = $comquick2cartHelper->getViewpath('product', 'medialist_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
						ob_start();
						include($mediaDetail);
						$mediaDetail = ob_get_contents();
						ob_end_clean();
						echo $mediaDetail;
						?>
					</div>
					<?php
				}
				// If taxation and shippping is enabled
				if ($isTaxationEnabled  || $qtc_shipping_opt_status)
				{
					?>
					<div class="tab-pane" id="taxshipTab">
						<?php
						// Check for view override
						$taxshipPath = $comquick2cartHelper->getViewpath('product', 'taxship_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
						ob_start();
						include($taxshipPath);
						$taxshipDetail = ob_get_contents();
						ob_end_clean();
						echo $taxshipDetail;
						?>
					</div>
					<?php
				}

				if (!empty($this->form_extra) || empty($this->item_id))
				{
					echo $this->loadTemplate('extrafields_tab_body_bs2');
				}
				?>
				</div>
			</div>
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
			<?php
		}
		?>
	</form>
</div>
