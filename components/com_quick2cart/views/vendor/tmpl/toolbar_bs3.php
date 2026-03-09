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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$isShippingEnabled = $this->params->get('shipping', 0);
$isTaxationEnabled = $this->params->get('enableTaxtion', 0);
$user              = Factory::getUser();
$active            = isset($active) ? $active : '';
?>

<script type="text/javascript">
	function submitAction_store(action)
	{
		if (action=="change_store")
		{
			var store_id=document.getElementById("current_store_id").value;
			document.adminForm.change_store.value=store_id;
			document.adminForm.submit();
		}
	}
</script>

<?php
$multivendor_enable = 1;

if (empty($multivendor_enable))
{
	return;
}

$app     = Factory::getApplication();
$jinput  = $app->input;
$preview = $jinput->get("preview");

if (!empty($preview))
{
	return;
}
?>

<div class="qtc_toolbarDiv">
	<?php
	$comquick2cartHelper = new Comquick2cartHelper;

	if (!$user->guest)
	{
		$this->store_cp_itemid        = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$this->create_store_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
		$this->my_stores_itemid       = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=stores&layout=my');
		$this->my_payouts_itemid      = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=payouts&layout=my');
		$this->store_customers_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout=mycustomer');
		$this->store_orders_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout=storeorder');
		$this->view_products_itemid   = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');
		$this->add_product_itemid     = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=product');
		$this->my_products_itemid     = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
		$this->promotions_itemid      = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=promotions');

		$this->store_id     = (!empty($this->store_id)) ? $this->store_id : 0 ;
		$storeLimitPerUser  = $this->params->get('storeLimitPerUser');
		$storeHelper        = new storeHelper();
		$allowToCreateStore = $storeHelper->isAllowedToCreateNewStore();
		?>

		<div class="row shadow-sm">
			<div class="col-sm-12 col-xs-12 qtc_toolbar">
				<div class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
					<div class="navbar-inner">
						<div class="container">
							<div class="">
								<ul class="navbar-nav me-auto mb-2 mb-lg-0 d-flex align-items-center">
									<?php
									$dash_hometitle = Text::_('QTC_STORE_DASHBOARD_DEFAULT');
									$setup_array    = array ('zones', 'taxrates', 'taxprofiles', 'shipping', 'shipprofiles');

									if (!empty($this->store_role_list))
									{
										$storehelp      = new storeHelper();
										$index          = $storehelp->array_search2d($this->store_id, $this->store_role_list);
										$store_name     = (is_numeric($index)) ? $this->store_role_list[$index]['title'] : "";
										$dash_hometitle = Text::sprintf('QTC_STORE_DASHBOARD', $store_name);
									}
									?>
									<li class="nav-item<?php echo ($active == 'cp') ? 'active': '' ?>" >
										<a class="nav-link"
											href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=vendor&layout=cp');?>"
											title="<?php echo $dash_hometitle; ?>">
											<i class="<?php echo Q2C_TOOLBAR_ICON_HOME;?>"></i>
										</a>
									</li>
									
									<li class="nav-item dropdown<?php echo (in_array($active, $setup_array)) ? 'active': '' ?> ">
										<a
											href="#"
											class="nav-link dropdown-toggle"
											data-toggle="dropdown"
											id="dropdownMenuLink"
											data-bs-toggle="dropdown"
											aria-expanded="false">
											<i class="<?php echo Q2C_TOOLBAR_ICON_SETTINGS;?>"></i>
											<?php echo Text::_('COM_QUICK2CART_STORE_SETUP'); ?>
											<b class="caret"></b>
										</a>
										<ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
											<li>
												<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=zones');?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
													<?php echo Text::_('COM_QUICK2CART_SETUP_ZONES'); ?>
												</a>
											</li>

											<?php
											// Dont show taxation related links if taxation is diabled msg
											if ($isTaxationEnabled == 1)
											{
												?>
												<li>
													<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=taxrates');?>" class="dropdown-item">
														<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
														<?php echo Text::_('COM_QUICK2CART_SETUP_TAXRATES'); ?>
													</a>
												</li>
												<li>
													<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=taxprofiles');?>" class="dropdown-item">
														<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
														<?php echo Text::_('COM_QUICK2CART_SETUP_TAXPROFILE'); ?>
													</a>
												</li>
												<?php
											}
											?>

											<?php
											// Dont show shipping related links if shipping is diabled msg
											if ($isShippingEnabled == 1)
											{
											?>
												<li>
													<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=shipping');?>" class="dropdown-item">
														<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
														<?php echo Text::_('COM_QUICK2CART_SETUP_SHIPPING'); ?>
													</a>
												</li>
												<li>
													<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=shipprofiles');?>" class="dropdown-item">
														<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
														<?php echo Text::_('COM_QUICK2CART_SETUP_SHIPPROFILE'); ?>
													</a>
												</li>
											<?php
											}
											?>
										</ul>
									</li>

									<li class="<?php echo ($active == 'create_store' || $active == 'my_stores') ? 'active': '' ?> dropdown">
										<a
											href="#"
											class="nav-link dropdown-toggle"
											data-toggle="dropdown"
											id="storedropdownMenuLink"
											data-bs-toggle="dropdown"
											aria-expanded="false">
											<?php echo Text::_('QTC_MANAGE_STORE'); ?>
										</a>
										<ul class="dropdown-menu" aria-labelledby="storedropdownMenuLink">
											<?php
											if (!empty($allowToCreateStore))
											{
											?>
											<li>
												<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=vendor&layout=createstore');?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_PLUS;?>"></i>
													<?php echo Text::_('QTC_NEW_STORE'); ?>
												</a>
											</li>
											<?php
											}
											?>
											<li>
												<a href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=stores&layout=my');?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
													<?php echo Text::_('QTC_MANAGE_MY_STORE'); ?>
												</a>
											</li>
										</ul>
									</li>

									<li class="<?php echo ($active == 'add_product' || $active == 'my_products') ? 'active': '' ?> dropdown">
										<a
											href="#"
											class="nav-link dropdown-toggle<?php echo ($active == 'products') ? 'active': '' ?> dropdown-toggle"
											data-toggle="dropdown"
											id="productdropdownMenuLink"
											data-bs-toggle="dropdown"
											aria-expanded="false">
											<?php echo Text::_('QTC_MANAGE_STORE_PROD'); ?>
										</a>
										<ul class="dropdown-menu" aria-labelledby="productdropdownMenuLink">
											<li>
												<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=product&layout=default' . '&Itemid=' . $this->add_product_itemid);?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_PLUS;?>"></i>
													<?php echo Text::_('QTC_MANAGE_STORE_ADD_PROD'); ?>
												</a>
											</li>
											<li>
												<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=category&qtcStoreOwner=1&layout=my&Itemid=' . $this->my_products_itemid);?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
													<?php echo Text::_('COM_QUICK2CART_MY_PRODUCTS'); ?>
												</a>
											</li>
											<li>
												<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=category&qtcStoreOwner=1&layout=default&Itemid=' . $this->view_products_itemid . '&current_store=' . $this->store_id);?>" class="dropdown-item">
													<i class="<?php echo Q2C_TOOLBAR_ICON_LIST;?>"></i>
													<?php echo Text::_('QTC_MANAGE_STORE_VIEW_PROD'); ?>
												</a>
											</li>
										</ul>
									</li>
									<li class="nav-link <?php echo ($active == 'storeorders' || $active == 'storeorder') ? 'active': '' ?> ">
										<a class="nav-link dropdown-toggle" id="storeOrdersDropdownMenuLink" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false" href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=orders&layout=storeorder');?>">
											<i class="<?php echo Q2C_TOOLBAR_ICON_CART;?>"></i>
											<?php echo Text::_('QTC_MANAGE_STORE_ORDERS'); ?>
										</a>
									</li>
									
									<li class="nav-link <?php echo ($active == 'storecustomers' || $active == 'customerdetails') ? 'active': '' ?> dropdown">
										<a class="nav-link dropdown-toggle" id="storeOrdersDropdownMenuLink" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false" href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=orders&layout=mycustomer');?>">
											<i class="<?php echo Q2C_TOOLBAR_ICON_USERS;?>"></i>
											<?php echo Text::_('QTC_MANAGE_STORE_CUSTOMER'); ?>
										</a>
									</li>

									<li class="nav-link<?php echo ($active == 'promotions') ? 'active': '' ?> dropdown">
										<a class="nav-link dropdown-toggle" id="storeOrdersDropdownMenuLink" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false" href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=promotions&layout=default');?>">
											<i class="<?php echo Q2C_TOOLBAR_ICON_COUPONS;?>"></i>
											<?php echo Text::_('COM_QUICK2CART_PROMOTIONS'); ?>
										</a>
									</li>

									<li class="nav-link<?php echo ($active == 'payouts') ? 'active': '' ?> dropdown">
										<a class="nav-link dropdown-toggle" id="storeOrdersDropdownMenuLink" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false" href="<?php echo $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=payouts&layout=my');?>">
											<i class="<?php echo Q2C_TOOLBAR_ICON_PAYOUTS;?>"></i>
											<?php echo Text::_('QTC_MANAGE_STORE_PAYOUTS');?>
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php
	}
?>
</div>
<?php
$skip_array = array(
	'0'=>'add_product',
	'1'=>'customerdetails',
	'2'=>'storeorder',
	'3'=>'storecoupon',
	'4'=>'payouts'
	);

if (is_array($this->store_role_list) && !in_array($active, $skip_array) && (count($this->store_role_list)>1))
{
	?>
	<div class="row">
		<div class="col-sm-12 col-xs-12">
			<?php
			$options = array();
			$default = $app->getUserStateFromRequest('com_quick2cart' . '.current_store', 'current_store', 'int');
			$app->setUserState('com_quick2cart.current_store', $default);

			foreach ($this->store_role_list as $key=>$value)
			{
				$options[] = HTMLHelper::_('select.option', $value["store_id"], $value['title']);
			}
			?>

			<div class="form-horizontal">
				<div class="clearfix"></div>
				<div class="pull-right">
					<?php
					echo HTMLHelper::_('select.genericlist', $options, 'current_store', 'class="form-select" autocomplete="off"  onchange=\'submitAction_store("change_store");\' title="' . Text::_('COM_QUICK2CART_CURRENT_STORE') . '"', 'value', 'text', $default, 'current_store_id');
					?>
					<input type="hidden" name="change_store" id="qtc_change_store" value="<?php echo $default;?>" />
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<?php
}
