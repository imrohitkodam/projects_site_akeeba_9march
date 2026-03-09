<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.renderModal');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

Text::script('QTC_SKU_EXIST', true);

$params = ComponentHelper::getParams('com_quick2cart');
$input=Factory::getApplication()->input;
$qtc_product_name = $input->get('qtc_article_name','','RAW');
$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

//added by aniket
$entered_numerics= Text::_('QTC_ENTER_NUMERICS', true);
$isAdmin = Factory::getApplication()->isClient('administrator');
$searchTmpPath = $useViewpath = "SITE";
$currentBSViews = $params->get('bootstrap_version', "bs3");

if ($isAdmin)
{
	$searchTmpPath = "JPATH_ADMINISTRATOR";
	$currentBSViews = "bs2";
}

if (!class_exists('comquick2cartHelper'))  // load helper file if not exist
{
  //require_once $path;
 	$path = JPATH_SITE. '/components/com_quick2cart/helper.php';
   JLoader::register('comquick2cartHelper', $path );
   JLoader::load('comquick2cartHelper');
}

$comquick2cartHelper = new comquick2cartHelper; // @have to remove this
$result = $comquick2cartHelper->checkExtraFieldIsExist();
$extra_field = (empty($result)) ? '0' : '1';

$this->comquick2cartHelper = $comquick2cartHelper;

// Load assets
comquick2cartHelper::loadQuicartAssetFiles();
comquick2cartHelper::defineIcons('SITE');

if (!class_exists('qtcshiphelper'))  // load helper file if not exist
{
  //require_once $path;
 	$path = JPATH_SITE . '/components/com_quick2cart/helpers/qtcshiphelper.php';
   JLoader::register('qtcshiphelper', $path );
   JLoader::load('qtcshiphelper');
}
$qtcshiphelper = new qtcshiphelper;


$qtc_shipping_opt_status = $params->get('shipping', 0);
$isTaxationEnabled = $params->get('enableTaxtion', 0);
$eProdSupport = $params->get('eProdSupport',0);
$prodAdmin_approval = $params->get('admin_approval', 0);
$document = Factory::getDocument();

$admin_base_url = Uri::base();
$js_currency="
	techjoomla.jQuery(document).ready(function()
	{

	  var width = techjoomla.jQuery(window).width();

	   if (width < 321 )
	   {
			techjoomla.jQuery('div.techjoomla-bootstrap').removeClass('form-horizontal');
			techjoomla.jQuery('div.q2c-wrapper').removeClass('form-horizontal');
		}
	});

	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++)
		{
		   if (el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) { alert('Numerics Not Allowed'); el.value = el.value.substring(0,i); break;}
		}
	}
";

	JLoader::import('attributes', JPATH_SITE. '/components/com_quick2cart/models');
	$quick2cartModelAttributes =  new quick2cartModelAttributes();
	$change_storeto = 0;

	if (!empty($pid))
	{
		$this->itemDetail = $itemDetail = $quick2cartModelAttributes->getItemDetail($pid,$client);

		// Get store id of product
		$change_storeto = $this->itemDetail['store_id'];

		// Get owner of store
		$storeInfo = $this->comquick2cartHelper->getSoreInfo($change_storeto);
		$owner = $storeInfo['owner'];
		$itemvalue = $itemDetail['name'];
	}

	$currencies=$params->get('addcurrency');
	$curr=explode(',',$currencies);

	JLoader::import('store', JPATH_SITE. '/components/com_quick2cart/models');
	$store_model =  new Quick2cartModelstore();
	$owner = isset($owner) ? $owner : 0 ;

	//$store_id = $store_model->getStoreId($owner);
	$store_role_list = $storeList = $this->comquick2cartHelper->getStoreIds($owner);

	if (!empty($store_role_list))
	{
		$this->store_id = $store_id = (!empty($change_storeto))?$change_storeto:$store_role_list[0]['store_id'];
	}

	$app = Factory::getApplication();
	$path = JPATH_SITE. '/components/com_quick2cart/helpers/storeHelper.php';

	if (!class_exists('storeHelper'))
	{
		//require_once $path;
		 JLoader::register('storeHelper', $path );
		 JLoader::load('storeHelper');
	}

	$storeHelper=new storeHelper;

	if ($app->isClient('administrator') && empty($change_storeto))
	{
		$this->store_id = $store_id = $storeHelper->getAdminDefaultStoreId();
	}

	if (empty($store_id))
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
			<div class="alert alert-error">
				<button type="button" class="close" data-dismiss="alert"></button>
				<?php
				$create_store_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
				$createstore="<a href=\"" . Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&Itemid=' . $create_store_itemid) . "\"> Click Here</a>"; ?>
				<strong><?php echo "<br>" . Text::sprintf('QTC_NO_STORE', $createstore); ?></strong>
			</div>
		</div>

		<?php
		return;
	}

	$qtc_icon_edit = QTC_ICON_EDIT;
?>

<script type="text/javascript">
	 techjoomla.jQuery(document).ready(function()
	 {
		var width = techjoomla.jQuery(window).width();

	   if (width < 480 )
	   {
			techjoomla.jQuery('div.techjoomla-bootstrap').removeClass('form-horizontal');
			techjoomla.jQuery('div.q2c-wrapper').removeClass('form-horizontal');
		}

		/* For front-end k2. pop-up is opening in new window*/
			window.SqueezeBox.initialize({});
			window.SqueezeBox.assign($$('a.modal'),
			{
				parse: 'rel'
			});
	});
	function checkForSku(sku)
	{
		//var editprod="<?php //echo $edit_task;?>";
		var formName = document.adminForm;
		var skuval=document.adminForm.sku.value;

		// if not a edit task and not empty sku value then only call ajax
		if (skuval)
		{
			var oldSku="<?php if (!empty($itemDetail['sku'])){  echo stripslashes($itemDetail['sku']); } ?>";
			/* while edit sku is not changed*/
			if (skuval != oldSku)
			{

				var actUrl = Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.checkSku&tmpl=component&sku='+sku;
				var skuele = formName.sku;
				/*qtcIsPresentSku(actUrl, skuele);*/
				techjoomla.jQuery.ajax({
					url: actUrl,
					cache: false,
					type: 'GET',
					success: function(data)
					{
						//*already exist*/
						if (data == '1')
						{
							alert(Joomla.Text._('QTC_SKU_EXIST'));
							skuele.value="";
						}
						else
						{
							var tem='';
						}
					}
				});
			}
		}
	}

</script>
<?php

if (!empty($store_id))
{
		if (empty($itemDetail))
		{
			$itemDetail['taxprofile_id'] = '';
		}
		$user=Factory::getUser();
		$storeHelper=$comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		//$storeList=(array)$storeHelper->getUserStore($user->id);

		$js_currency .= "
		var firstStoreId = '".$storeList[0]['store_id']."';

		function getTaxprofile()
		{
			var isTaxationEnabled = " . $isTaxationEnabled . ";
			var qtc_shipping_opt_status = " . $qtc_shipping_opt_status . ";
			var store_id = techjoomla.jQuery('#current_store_id').val();
			if (store_id == null)
			{
				store_id = firstStoreId;
			}
			var selected_taxid = '" . $itemDetail['taxprofile_id'] . "';

			if (isTaxationEnabled == 1)
			{
				qtcLoadTaxprofileList(store_id, selected_taxid);
			}

			/* Now update ship profiles */

			if (qtc_shipping_opt_status == 1)
			{
				qtcUpdateShipProfileList(store_id);
			}
		}
		window.onload = function ()
		{
			var isTaxationEnabled = " . $isTaxationEnabled . ";
			var qtc_shipping_opt_status = " . $qtc_shipping_opt_status . ";
			var store_id = techjoomla.jQuery('#current_store_id').val();
			if (store_id == null)
			{
				store_id = firstStoreId;
			}
			var selected_taxid = '" . $itemDetail['taxprofile_id'] . "';
			// Get tax profile list

			if (isTaxationEnabled == 1)
			{
				qtcLoadTaxprofileList(store_id, selected_taxid);
			}
		}
		";


		$js_currency .="
	function savecurrency(pid,client)
	{
		var store_id = techjoomla.jQuery('#current_store_id').val();
		if (store_id == null)
		{
			store_id = firstStoreId;
		}

		var multicurr = techjoomla.jQuery('.currtext').serializeArray();
		console.log(multicurr);
		var item_name=techjoomla.jQuery('#item_name').val();
		var isCurrFilled=1;
		techjoomla.jQuery('.qtc_requiredoption').each(function()
		{
			var val=techjoomla.jQuery(this).val();
			if (val=='' || val==null)
			{
				isCurrFilled=0;
			}
		});

		if (isCurrFilled == 0 || item_name.length==0)
		{
			alert(\"".Text::_('QTC_OPTIONS_REQUIRED')."\");
			return( false );
		}

		// sku check
		var qtc_sku = techjoomla.jQuery('#sku').val();
		if (qtc_sku == '' || qtc_sku == null)
		{
			alert(\"".Text::_('QTC_OPTIONS_REQUIRED')."\");
			return( false );
		}
		//var attdata = techjoomla.jQuery('#qtcCCK_prodDetail').serialize();
		var attdata = techjoomla.jQuery('#qtcCCK_prodDetail :input').serialize();

		techjoomla.jQuery.ajax({
			url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&controller=attributes&task=attributes.addcurrency&tmpl=component',
			type: 'POST',
			data: attdata,
			dataType: 'json',
			success: function(msg)
			{
				if (msg[0]==0){
					alert(msg[1]);
				}
				else if (msg[1]==1)
				{
					alert(msg[1]);
					//window.location.reload();
				}

			}
		});
	}

	techjoomla.jQuery(document).ready(function() {
		qtcUpdateProdFieldClasses();
	});

	function qtcUpdateProdFieldClasses()
	{
		if (document.getElementById('qtcEnableQtcProd').checked)
		{
			/* Add required class*/
			techjoomla.jQuery('#qtcOptionsWraper').show();
			techjoomla.jQuery('#item_name').addClass('required');
			techjoomla.jQuery('#sku').addClass('required');

			/* Price related things */
			techjoomla.jQuery('#qtc_price_currencey_textbox').find('.currtext').each(function()
			{
				techjoomla.jQuery(this).addClass('required');
			});

		}
		else
		{
			techjoomla.jQuery('#qtcOptionsWraper').hide();
			/*Remove required classes*/
			techjoomla.jQuery('#item_name').removeClass('required');
			techjoomla.jQuery('#sku').removeClass('required');

			/* Price related things */
			techjoomla.jQuery('#qtc_price_currencey_textbox').find('.currtext').each(function()
			{
				techjoomla.jQuery(this).removeClass('required');
			});
		}
	}
	";
}

$document->addScriptDeclaration($js_currency);
?>

<div class="qtcClearBoth"></div>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" id="qtcCCK_prodDetail" >

	<!-- Required for ajax based q2c option saving -->
	<input type="hidden" name='client' value="<?php echo !empty($client) ? $client: ''?>" >
	<?php
	if (empty($store_id))
	{
		?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert"></button>
			<?php
			$create_store_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
			$createstore="<a href=\"" . Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&Itemid=' . $create_store_itemid) . "\"> " . Text::_('QTC_CLICK_HERE') . "</a>"; ?>
			<strong><?php echo "<br>" . Text::sprintf('QTC_NO_STORE', $createstore); ?></strong>
		</div>
		<?php
	}
	else
	{
		JLoader::import('cart', JPATH_SITE. '/components/com_quick2cart/models');
		$model =  new Quick2cartModelcart;

		if (!empty($pid))
		{
			$item_id=$model->getitemid($pid,$client);
		}

		/*fetch Minimum/ max /stock  item Quantity*/
		if (!empty($item_id))  // item_id present i.e  item is saved
		{
			$minmaxstock = $model->getItemRec($item_id);
		}

		$class_nm = 'form-horizontal';

		if ($client === 'com_zoo')
		{
			$class_nm = '';
		}

		// If article is QTC product then show qtc fields.
		$showQtcFields = 'display:none;';

		if (!empty($itemDetail['item_id']))
		{
			$showQtcFields = '';
		}
		else
		{ ?>

			<div class="control-group" style="<?php //echo $StoreIds_style;?>">
				<label for="qtcEnableQtcProd" class="control-label">
					<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ENABLE_QTC_PROD_FOR_THIS_ARTICLE_DESC'), Text::_('COM_QUICK2CART_ENABLE_QTC_PROD_FOR_THIS_ARTICLE'), '', ' ' . Text::_('COM_QUICK2CART_ENABLE_QTC_PROD_FOR_THIS_ARTICLE'));?>
				</label>

				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" id="qtcEnableQtcProd"   autocomplete="off" name="qtcEnableQtcProd" value=""  onchange="qtcUpdateProdFieldClasses()">
					</label>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

		<?php
		}

		?>

		<div id='qtcOptionsWraper' style="<?php echo $showQtcFields; ?>">
			<div id='' class=" <?php echo $class_nm;?>" >
				<?php
				if ($app->isClient('site')  && $prodAdmin_approval )
				{
					?>
					<div class="alert">
						<em>
							<i><?php echo Text::_('COM_QUICK2CART_PROD_ADMIN_APPROVAL_NEEDED_HELP'); ?></i>
						</em>
					</div>
					<?php
				} ?>


				<div class="control-group" style="<?php //echo $StoreIds_style;?>">
					<label for="item_name" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_TITLE_DES'), Text::_('QTC_ITEM_NAME'), '', '* ' . Text::_('QTC_ITEM_NAME'));?>
					</label>
						<?php
							$p_title ='';

							if (!empty($itemDetail['name']))
							{
								//$p_title = stripslashes($this->itemDetail['name']);
								$p_title = ($itemDetail['name']);
							}
						?>
					<div class="controls">
						<input type="text" class="input-medium inputbox requiredEE"  name="item_name"  id="item_name" value="<?php echo $comquick2cartHelper->escape($p_title); ?>" />

						<input type="hidden" class="inputbox"  name="pid"  id="pid" value="<?php echo $pid; ?>" />
						<?php
						/*if ($StoreIds_count > 1)
						{
						?>
						<input type="hidden" class="inputbox"  name="store_id" id="store_id" value="<?php echo $default_store_id; ?>" />
						<?php
						}
						*/
						?>
					</div>
				</div>

				<!-- SKU -->
				<div class='control-group' >
					<label for="sku" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('QTC_ITEM_CCK_SKU_DES'), Text::_('QTC_ITEM_CCK_SKU'), '', '* '.Text::_('QTC_ITEM_CCK_SKU'));?></label>
					<div class="controls">
						<input type="text" class="input-medium inputbox requiredEE"  name="sku"  id="sku" onBlur="checkForSku(this.value)" value="<?php echo (!empty($itemDetail['sku'])? $itemDetail['sku'] : ''); ?>" autocomplete='off'  aria-invalid="false"  />
					</div>
				</div>
				<!-- end SKU -->

				<?php

				$StoreIds_count = count($storeList);
				$StoreIds_style = ($StoreIds_count > 1) ? "display:block" : "display:none";
				?>
				<div class="control-group" style="<?php echo $StoreIds_style;?>">
					<!--
					<label class="control-label" for="storeid_lable"><strong><?php echo Text::_('COM_QUICK2CART_STORE_SELECT'); ?></strong></label>
					-->
					<label for="current_store_id" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_SELECT_STORE_DES'), '* ' . Text::_('COM_QUICK2CART_STORE_SELECT'), '', Text::_('COM_QUICK2CART_STORE_SELECT'));?></label>
					<div class="controls">
						<?php
						if (!empty($itemDetail['store_id']))
						{
							$default_store_id = $itemDetail['store_id'];
						}
						else
						{
						  $default_store_id = $storeList[0]['store_id'];
						}
							for($i=0;$i < count($storeList);$i++)
							{
								//echo $default_store_id = (!empty($StoreIds) ? $StoreIds[$i]->store_id : 1;
								$options_store[] = HTMLHelper::_('select.option',$storeList[$i]['store_id'], $storeList[$i]['title']);//submitAction('deletecoupon');
							}
							echo $this->dropdown = HTMLHelper::_('select.genericlist',$options_store,'store_id','class=" qtc_putmargintop10px qtc_requiredoption"   onchange="getTaxprofile();"','value','text',$default_store_id,'current_store_id');
						// if store count is 1 then save default store

						?>
					</div>
				</div>

				<!-- publish/unpublis -->
				<?php
				// Dont show if its front end and admin approval is set to NO.
				$showStateBtn = 1;
				if ($app->isClient('site') && $prodAdmin_approval == 1 )
				{
					$showStateBtn = 0;
				}

				if ($showStateBtn)
				{

					?>
					<div class='control-group' >
						<!--
						<label class="control-label "><strong><?php echo HTMLHelper::tooltip(Text::_('QTC_ITEM_STATUS'), Text::_('QTC_ITEM_STATUS_DES'), '', Text::_('QTC_ITEM_STATUS'));?></strong></label>
						-->
						<label for="qtc_item_state" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('QTC_ITEM_STATUS'), Text::_('QTC_ITEM_STATUS_DES'), '', Text::_('QTC_ITEM_STATUS'));?></label>

						<div class="controls">
						  <?php
								$default=(!empty($itemDetail['state'])? $itemDetail['state'] :0 );
								$options[] = HTMLHelper::_('select.option', 1, Text::_('QTC_ITEM_PUBLISH'));
								$options[] = HTMLHelper::_('select.option', 0, Text::_('QTC_ITEM_UNPUBLISH'));
								$stateFieldName = 'state';

								// #40581 = temporary fix
								if ($client == "com_zoo" || $client == "com_k2" || $client == "com_flexicontent")
								{
									$stateFieldName = 'qtcProdState';
								}
							echo	$dropdown = HTMLHelper::_('select.genericlist', $options, $stateFieldName,'class="qtc_itemstate " autocomplete="off"','value','text', $default,"qtc_item_state");
							 ?>
						</div>
					</div>
				<?php
				}

				?>

				<!-- PRICE PRICE -->
				<div class='control-group qtc_currencey_textbox' id="qtc_price_currencey_textbox" >
					<label for="jform_price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_PRICE_DESC'), Text::_('QTC_ITEM_PRICE'), '', "* " .Text::_('QTC_ITEM_PRICE'));?>
					</label>

					<div class="controls">
						<?php
						$storevalue = '';
						$currdata = array();
						$base_currency_id = "";
						// If all currency fields r filled
						$currfilled = 1;
						$multiCurrencies = 0;

						if (count($curr) > 1)
						{
							$multiCurrencies = 1;
						}

						// key contain 0,1,2... // value contain INR...
						foreach ($curr as $key=>$value)
						{
							//$name="jform[attribs][$value]";
							$currvalue="";

							if (!empty($pid))
							{
								$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($pid, $value, $client);
							}

							$storevalue = (isset($currvalue[0]['price'])) ? $currvalue[0]['price'] : '';

							if (empty($storevalue))
							{
								$currfilled=0;
							}

							if (!empty($curr_syms[$key]))
							{
								$currtext = $curr_syms[$key];
							}
							else
							{
								$currtext = $value;
							}
							?>

							<?php if ($multiCurrencies) : ?>
								<div>
									<div class="input-append curr_margin">
										<label for="jform_price_<?php echo trim($value);?>" class="control-label">
											<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_PRICE_DESC'), Text::_('QTC_ITEM_PRICE'), '', Text::_('COM_QUICK2CARET_PRICE') . ' ' . Text::_('COM_QUICK2CART_IN') . ' ' . trim($currtext));?>
										</label>
										<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
											class="input-mini currtext requiredEE qtc_requiredoption"
											style="align:right;"
											id="jform_price_<?php echo trim($value);?>"
											size="16"
											type="text"
											name="multi_cur[<?php echo trim($value);?>]"
											value="<?php echo $storevalue;?>"
											placeholder="<?php echo trim($value);?>">
										<span class="add-on"><?php echo $value;?></span>
									</div>
								</div>
							<?php else : ?>
								<div class="input-append curr_margin">
									<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
										class="input-mini currtext requiredEE qtc_requiredoption"
										style="align:right;"
										id="jform_price_<?php echo trim($value);?>"
										size="16"
										type="text"
										name="multi_cur[<?php echo trim($value);?>]"
										value="<?php echo $storevalue;?>"
										placeholder="<?php echo trim($value);?>">
									<span class="add-on"><?php echo $value;?></span>
								</div>
							<?php endif; ?>
						<?php
						}
						?>
					</div>
				</div>
				<!-- DISCOUNT PRICE -->
				<div class='control-group qtc_currencey_textbox' style="<?php echo (($params->get('usedisc') == '0') ? 'display:none;' :'display:block;'); ?>" >
					<label for="jform_disc_price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), Text::_('QTC_ITEM_DIS_PRICE'), '', Text::_('QTC_ITEM_DIS_PRICE'));?>
					</label>
					<div class="controls">
						<?php
						$currdata = array();
						$base_currency_id = "";

						// key contain 0,1,2... // value contain INR...
						foreach ($curr as $key=>$value)
						{
							//$name="jform[attribs][$value]";
							$currvalue = "";

							if (!empty($pid))
							{
								$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($pid, $value, $client);
							}

							$storevalue = (isset($currvalue[0]['discount_price'])) ? $currvalue[0]['discount_price'] : '';
							?>

							<?php if ($multiCurrencies) : ?>
								<div>
									<div class="input-append curr_margin">
										<label for="jform_disc_price_<?php echo trim($value);?>" class="control-label">
											<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), Text::_('QTC_ITEM_DIS_PRICE'), '', Text::_('COM_QUICK2CARET_PRICE') . ' ' . Text::_('COM_QUICK2CART_IN') . ' ' . trim($value));?>
										</label>

										<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
											class="input-mini currtext"
											style="align:right;"
											id="jform_disc_price_<?php echo trim($value);?>"
											size="16"
											type="text"
											name="multi_dis_cur[<?php echo trim($value);?>]"
											value="<?php echo $storevalue;?>"
											placeholder="<?php echo trim($value);?>">
										<span class="add-on"><?php echo trim($value);?></span>
									</div>
								</div>
							<?php else : ?>

								<div class="input-append curr_margin">
									<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
										class="input-mini currtext"
										style="align:right;"
										id="jform_disc_price_<?php echo trim($value);?>"
										size="16"
										type="text"
										name="multi_dis_cur[<?php echo trim($value);?>]"
										value="<?php echo $storevalue;?>"
										placeholder="<?php echo trim($value);?>">
									<span class="add-on"><?php echo trim($value);?></span>
								</div>
							<?php endif; ?>
						<?php
						}
						?>
					</div>
				</div>

				<?php
				/*fetch Minimum/ max /stock  item Quantity*/
				if (!empty($item_id))  // item_id present i.e  item is saved
				{
					$minmaxstock = $model->getItemRec($item_id);
				}

				$qtc_stock_style = ($params->get('usestock')==1)?"display:block":"display:none";
				?>
				<div class="control-group" style="<?php echo $qtc_stock_style;?>">
					<!--
					<label class="control-label"><strong class="" id="" ><?php echo Text::_('PLG_QTC_ITEM_STOCK'); ?></strong></label>
					-->
					<label for="stock" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_STOCK_DESC'), Text::_('PLG_QTC_ITEM_STOCK'), '', Text::_('PLG_QTC_ITEM_STOCK'));?>
					</label>
					<div class="controls">
						<input Onkeyup="checkforalpha(this,'45', '<?php echo addslashes($entered_numerics); ?>')"  type="text" name="stock" id="stock"  value="<?php if (isset($minmaxstock->stock)) echo $minmaxstock->stock;?>" class="input-mini inputbox validate-integer"  >
					</div>
				</div>

				<!-- for Minimum/ max item Quantity -->
				<?php
					$qtc_min_max_status=$params->get('minmax_quantity');
					$qtc_min_max_style=	($qtc_min_max_status==1)?"display:block":"display:none";
				?>
				<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
					<label class="control-label" for="item_slab">
					<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_SLAB_DESC'), Text::_('COM_QUICK2CART_ITEM_SLAB'), '', Text::_('COM_QUICK2CART_ITEM_SLAB'));?>
					</label>
					<div class="controls">
						<input Onkeyup="checkforalpha(this,'', '<?php echo addslashes($entered_numerics); ?>')"  Onchange="checkSlabValue();" type="text" name="item_slab" id="item_slab"  value="<?php echo isset($minmaxstock) ? $minmaxstock->slab: 1  ?>" class="input-mini inputbox validate-integer"  >
					</div>
				</div>

				<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
					<label for="min_item" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_MIN_QTY_DESC'), Text::_('QTC_ITEM_MIN_QTY'), '', Text::_('QTC_ITEM_MIN_QTY'));?>
					</label>
					<?php
					?>
					<div class="controls">
						<input
							onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')"
							type="text" name="min_item" id="min_item"  value="<?php if (isset($minmaxstock)) echo $minmaxstock->min_quantity;?>" class="input-mini inputbox validate-integer"  >
					</div>
				</div>
				<div class="control-group" style="<?php echo $qtc_min_max_style;?>">
					<label for="max_item" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_ITEM_MAX_QTY_DESC'); ?>">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_MAX_QTY_DESC'), Text::_('QTC_ITEM_MAX_QTY'), '', Text::_('QTC_ITEM_MAX_QTY'));?>
					</label>
					<div class="controls">
						<input
						onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')"
						type="text" name="max_item" id="max_item"  value="<?php if (isset($minmaxstock))  echo $minmaxstock->max_quantity;?>" class="input-mini inputbox validate-integer"  >
					</div>
				</div>


				<!-- END for Minimum/ max item Quantity -->

				<?php
				//---------------------------------
				/*	Enable= A 1.check $itemvalue valid 2.check currency is filled 3.outof_stock_ship=1 then show button
				 * 	B.	1. $itemvalue valid
				 * 		2.check currency is filled
				 * 		3.ck if outof_stock_ship=0  AND usestock=1 Stored stock is valid then  show button
				 * */
				$button_dis="disabled";
				if (!empty($itemvalue))// && $currfilled)
				{
					if ($currfilled)
					{
						 $usestock=$params->get('usestock');
						 $outof_stock_ship=$params->get('outofstock_allowship'); // checkig
						//$outof_stock_ship==1 then we are not checking for stock etc
						if ($usestock==0)  // dont have to use stock then show button
						{
							$button_dis="";
						}
						elseif ($outof_stock_ship==1)
						{
							$button_dis="";
						}
						else
						{
							if ($minmaxstock->stock || $minmaxstock->stock==NULL )//&& $outof_stock_ship==0 && $usestock==1 )  // not empty then u should fill stock
							{
							$button_dis="";
							}
						}
					}
				}

				// If taxation and shippping is enabled
				if ($isTaxationEnabled  || $qtc_shipping_opt_status)
				{
					// Required later
					$defaultStore = $default_store_id;
					$qtcshiphelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/qtcshiphelper.php","qtcshiphelper");

					// Check for view override
					$taxshipPath = $comquick2cartHelper->getViewpath('product', 'taxship', $searchTmpPath, $useViewpath);
					ob_start();
					include($taxshipPath);
					$taxshipDetail = ob_get_contents();
					ob_end_clean();
					echo $taxshipDetail;
				}
				?>
		</div>

			<div style="clear:both;"></div>

			<?php
			// Hide save button on Hideqtc varible
			$jinput=Factory::getApplication()->input;
			$showQtcSvbtn = $jinput->get('showQtcSvbtn', '', 'Int');
			if (!empty($pid) && $showQtcSvbtn == 1)
			{
				?>
				<div class="alert">
					<button type="button" class="close" data-dismiss="alert"></button>
					<strong><?php echo Text::_('QTC_NOTE'); ?></strong><?php echo Text::_('QTC_OPTIONS_REQUIRED_MSG'); ?>
				</div>

				<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert"></button>
					<strong><?php echo Text::_('QTC_NOTE'); ?></strong> <?php echo Text::_('QTC_SAVE_ITEM_PARAM_DESC'); ?>
				</div>

				<div class="form-actions">
					 <button  type="button" class="btn btn-success validate" onclick="savecurrency('<?php echo $pid; ?>','<?php echo $client; ?>');" >
					 <?php echo Text::_('QTC_ITEM_SAVE'); ?>
					 </button>
				</div>
			<?php
			}

			if (!empty($pid))
			{ ?>
			  <div >
					<label ><strong><?php echo Text::_('QTC_ITEM_OPTION'); ?></strong></label>

				<?php
				// CHECK for view override
					$comquick2cartHelper = new comquick2cartHelper;
					$att_list_path=$comquick2cartHelper->getViewpath('attributes','attribute_list', $searchTmpPath, $useViewpath);
					$html_attri = '';
					ob_start();
						require_once($att_list_path);
						$html_attri = ob_get_contents();
					ob_end_clean();
					echo $html_attri;

				?>
				</div>
				<hr class="hr-condensed"></hr>
				<?php
				if ($eProdSupport)
				{
					?>
				<div class="">
						<label ><strong><?php echo Text::_('QTC_CCK_MEDIA_LIST_DETAILS'); ?></strong></label>
				<?php
					// CHECK for medialist override
					$comquick2cartHelper = new comquick2cartHelper;
					$att_list_path=$comquick2cartHelper->getViewpath('attributes','media_list', $searchTmpPath, $useViewpath);
					$html_attri = '';
					ob_start();
						require_once($att_list_path);
						$html_attri = ob_get_contents();
					ob_end_clean();

				echo $html_attri;
				?>
				</div>
				<br />
				<?php
				}
				if($extra_field == '1')
				{?>
					<div class="">
						<label>
							<strong><?php echo Text::_('COM_QUICK2CART_CCK_TJ_FIELDS')?></strong>
						</label>
						<?php
						// CHECK for medialist override
						$comquick2cartHelper = new comquick2cartHelper;
						$att_list_path=$comquick2cartHelper->getViewpath('attributes','extrafield_list', $searchTmpPath, $useViewpath);

						$html_attri = '';

						ob_start();
						require_once($att_list_path);
						$html_attri = ob_get_contents();
						ob_end_clean();

						echo $html_attri;
						?>
					</div>
				<?php
				}
			}
		?>
		</div>
		<?php

	}

		?>
</div>
<div style="clear:both;"></div>
