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

HTMLHelper::_('bootstrap.tooltip');

$user   = Factory::getUser();
$result = $this->user_info;

// check empty of result
$Itemid            = (isset($this->Itemid)) ? $this->Itemid : 0;
$vendor_order_view = (!empty($this->store_id)) ? 1 : 0;
$document          = Factory::getDocument();
$totalamount       = 0;

$document->addScriptDeclaration('
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery(".q2c-wrapper #limit").removeAttr("size");
	});
');
?>

<style type="text/css">
.pagination a{
	text-decoration:none;
}
</style>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
	<form action="" name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		$helperobj      = new comquick2cartHelper;
		$active         = 'storecustomers';
		$order_currency = $helperobj->getCurrencySession();
		$view           = $helperobj->getViewpath('vendor','toolbar_bs3');
		ob_start();
		include ($view);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;

		$orders_site = (isset($this->orders_site)) ? $this->orders_site : 0;

		if ($orders_site)
		{
			?>
			<h1>
				<strong>
				<?php
				if (!empty($this->store_role_list))
				{
					$storehelp = new storeHelper();
					$index     = $storehelp->array_search2d($this->store_id, $this->store_role_list);

					if (is_numeric($index))
					{
						$store_name = $this->store_role_list[$index]['title'];
					}

					echo Text::sprintf('QTC_STORE_CUSTOMER', $store_name);
				}
				else
				{
					echo Text::_('QTC_STORE_CUSTOMER');
				}
				?>
				</strong>
			</h1>
			<?php
		}

		// ***STEP 1: check for user login or not
		if (!$user->id)
		{
			$app    = Factory::getApplication();
			$return = base64_encode(Uri::getInstance());
			$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
			$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
			$app->redirect($login_url_with_return, 403);
		}

		// ***step 3:: CHECK where user is autorized or not. Dont allow to display is user info if not autorize
		if (empty($this->store_authorize))
		{
			?>
				<div class="well" >
					<div class="alert alert-danger">
						<span><?php echo Text::_('QTC_NOT_AUTHORIZED_USER');?></span>
					</div>
				</div>
			</div>

			<?php
			return false;
		}
		?>

		<div class="clearfix">&nbsp; </div>
		<div id="qtc-filter-bar" class="qtc-btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_CUSTOMERS'); ?>"
				value="<?php echo $this->escape($this->lists['filter_search']); ?>"
				class="qtc-hasTooltip input-medium"
				title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_CUSTOMERS'); ?>" />
			</div>

			<div class="btn-group pull-left">
				<button type="submit" class="btn btn-default qtc-hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
				</button>
				<button type="button" class="btn btn-default qtc-hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
				onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
				</button>
			</div>

			<div class=" pull-right hidden-xs ">
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
		<div class="clearfix"> &nbsp;</div><br/>
		<div class="">
			<?php
			if (empty($result))
			{ ?>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			}
			else
			{?>
				<table class="table table-striped table-bordered table-responsive" id="productList">
					<thead>
						<tr>
							<?php
							if (!$orders_site)
							{
								?>
								<th width="2%" align="center" class="title">
									<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($result)+1; ?>);" />
								</th>
							<?php
							}
							?>

							<th width="25%">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_CUST_NAME','firstname', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="23%">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_CUST_EMAIL', 'user_email', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="20%">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_CUST_MOB_NO', 'phone', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="20%">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_CUST_CITY', 'city', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<th width="20%">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_CUST_COUNTRY', 'country_code', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$id=1;
						foreach($result as $orders)
						{
							?>
							<tr class="row0">
								<?php
								if (!$orders_site)
								{
									?>
									<td align="center">
										<?php echo HTMLHelper::_('grid.id', $id, $orders->id ); ?>
									</td>
									<?php
								}
								?>
								<td>
									<a href="<?php echo  Uri::base().substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=customerdetails&orderid='.$orders->order_id.'&store_id='.$this->store_id),strlen(Uri::base(true))+1); ?>"><?php echo HTMLHelper::tooltip(Text::_('QTC_TOOLTIP_VIEW_CUST_INFO'), Text::_('QTC_TOOLTIP_VIEW_CINFO'), '', $orders->firstname." ".$orders->lastname ) ;?></a>
								</td>
								<td>
									<?php echo $orders->user_email; ?>
								</td>
								<td class="qtc_pending_action" >
									<?php echo $orders->phone; ?>
								</td>
								<td>
									<?php echo $orders->city; ?>
								</td>
								<td>
									<?php echo $orders->countryName; ?>
								</td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
				<?php echo $this->pagination->getListFooter();
			} ?>
		</div>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
