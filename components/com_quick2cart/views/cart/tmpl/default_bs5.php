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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

$comquick2cartHelper = new comquick2cartHelper;
$q2cbaseUrl          = $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=category&layout=default');
$checkout            = Uri::root().substr($comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=cartcheckout',false),strlen(Uri::base(true))+1);

$data       = new stdclass;
$data->cart = $this->cart;

if(empty($data->cart))
{
?>
	<div class="well" >
		<div class="alert alert-danger">
			<span ><?php echo Text::_('QTC_EMPTY_CART'); ?> </span>
		</div>
	</div>
<?php
	return false;
}

$data->showoptioncol = 0;
$data->coupon        = $this->coupon;

foreach ($this->cart as $citem)
{
	if (!empty($citem['options']))
	{
		$data->showoptioncol = 1;
		break;
	}
}
?>
<div class="container-fluid">
	<div class=" <?php echo Q2C_WRAPPER_CLASS; ?> ">
		<div class="">
			<h1><strong><?php echo Text::_('QTC_CART')?></strong></h1>
		</div>
		<form method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal form-validate" onsubmit="return validateForm();">
			<?php
			$layoutName       = "cartcheckout." . QUICK2CART_LOAD_BOOTSTRAP_VERSION . ".cart_checkout";
			$layout           = new FileLayout($layoutName);
			$data->promotions = !empty($this->promotions) ? $this->promotions : array();
			$response         = $layout->render($data);
			echo $response;
			?>
			<?php
				$userId = Factory::getUser()->id;

				JLoader::register('Quick2cartModelPromotions', JPATH_SITE . '/components/com_quick2cart/models/promotions.php');
				$helper = new Quick2cartModelPromotions;

				$hasVisiblePromotion = false;

				// Get applicable promotions
				$applicablePromotions = $this->applicablePromotionsList ?? [];

				// Create map of eligible users per promotion
				$promoIds = is_array($applicablePromotions) ? array_map(function ($p) { return $p->id; }, $applicablePromotions) : [];
				$eligibleUsersMap = $helper->getEligibleUsersForPromotions($promoIds);

				// Check if any promotion is visible for the user
				foreach ($applicablePromotions as $promotion)
				{
					$isSpecific = (int) $promotion->allowspecificuserpromotion === 1;
					$isUserEligible = in_array($userId, $eligibleUsersMap[$promotion->id]['users'] ?? []);

					if (!$isSpecific || ($isSpecific && $isUserEligible))
					{
						$hasVisiblePromotion = true;
						break;
					}
				}

				// Check if there are any applicable promotions in the list
				if ($hasVisiblePromotion)
				{
					$model = $this->getModel();
					?>
					<h4 class="mt-5 mb-3"><?php echo Text::_("COM_QUICK2CART_AVAILABLE_OFFERS"); ?></h4>
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-hover border">
							<thead class="table-primary border table-bordered">
								<tr>
									<th class="fw-bold"><?php echo Text::_("QTC_COUPON_NAME"); ?></th>
									<th class="fw-bold"><?php echo Text::_("QTC_CUPCODE"); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php 
							foreach ($this->applicablePromotionsList as $promotion)
							{
								$quantity = $promotion->quantity;
								$operation = isset($promotion->operation) ? $promotion->operation : '';
							?>
								<tr>
									<td>
										<?php
										echo $promotion->name;
										if (!empty($promotion->description)) {
											echo " [ " . $promotion->description . " ] ";
										}
										?>
									</td>
									<td>
										<?php 
										if ($promotion->coupon_required == '1' && !empty($promotion->coupon_code))
										{
											echo '<h6><span class="text-warning font-weight-bold">' . $promotion->coupon_code . '</span></h6>';
										} 
										else
										{
											echo "-";
										}
										?>
									</td>
								</tr>
							<?php 
							}
							?>
							</tbody>
						</table>
					</div>
			<?php   
				} ?>
			<div class="row">
				<div class="form-actions col-md-12" id="qtc_formactions">
					<a class="float-end btn btn-success text-light" onclick="window.parent.document.location.href='<?php echo $checkout; ?>';" >
						<?php echo Text::_('QTC_CHKOUT'); ?>
					</a>
					<a class="float-end me-2 btn btn-primary text-light" onclick="qtcCartContinueBtn('<?php echo $q2cbaseUrl;?>')" >
						<?php echo Text::_('QTC_BACK'); ?>
					</a>
				</div>
			</div>
			<input type="hidden" name="task" id="task" value="cartcheckout.qtc_autoSave" />
		</form>
	</div>
</div>
<?php

// To change to Continue shipping URL to site specific URL.
$AllProductItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');
$allProdLink      = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&view=category&Itemid=' . $AllProductItemid, false), strlen(Uri::base(true)) + 1);
?>
<script>
	function qtcCartContinueBtn(q2cBaseUrl)
	{
		var popup = true;
		try
		{
			// IF popup.
			popup = (window.self === window.top);
		}
		catch (e)
		{
			popup = true;
		}

		if (popup == true)
		{
			/* qtc_base_url - Defined in asset loader plugin*/
			window.location.assign(q2cBaseUrl);

			/* To change to Continue shipping URL to site specific URL. */
			/*window.location.assign("<?php echo $allProdLink;?>"); */
		}
		else
		{
			window.parent.location = q2cBaseUrl;
		}
	}
</script>
