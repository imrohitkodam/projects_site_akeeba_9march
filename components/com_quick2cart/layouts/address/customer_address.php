<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$data   = $displayData;
$params = ComponentHelper::getParams('com_quick2cart');

$user = Factory::getUser();
HTMLHelper::_('bootstrap.renderModal');

if (!empty($data))
{
	$bs_classes = ($params->get('bootstrap_version') == "bs2") ? "span4 qtc_address_pin_wrapper " : "col-xs-12 col-md-6 col-lg-4 ";
?>
	<div class="<?php echo $bs_classes;?>qtc-address<?php echo $data->id;?>">
		<div class="qtc_address_pin">
			<div class="qtc_address_pin_margin">
				<div class="q2c_address_header">
					<div class="q2c_address_name">
						<b>
							<?php echo htmlspecialchars(ucfirst($data->firstname) . " " . ucfirst($data->lastname), ENT_COMPAT, 'UTF-8');?>
						</b>
					</div>
					<div class="pull-right float-end">
						<?php
						if (JVERSION < '4.0.0')
						{
							?>
							<a
								class=""
								title="<?php echo Text::_('QTC_EDIT');?>"
								onclick="editAddress('editAddressModal_', <?php echo $data->id?>)"
								data-target="#editAddressModal_<?php echo $data->id;?>"
								data-toggle="modal">
								<i class="fa fa-pencil-square-o " aria-hidden="true"></i>
							</a>

							<div class="modal fade" id="editAddressModal_<?php echo $data->id;?>" tabindex="-1" aria-labelledby="editAddressModal<?php echo $data->id;?>" aria-hidden="true">
							  <div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
										<h5 class="modal-title"><?php echo Text::_('COM_QUICK2CART_EDIT_CUSTOMER_ADDRESS'); ?></h5>
									</div>
									<div class="modal-body"></div>
								</div>
							  </div>
							</div>
							
							<?php
						}
						else
						{
							?>
							<a
								class=""
								title="<?php echo Text::_('QTC_EDIT');?>"
								onclick="editAddress('editAddressModal_', <?php echo $data->id?>)"
								data-bs-target="#editAddressModal_<?php echo $data->id;?>"
								data-bs-toggle="modal">
								<i class="fa fa-pencil-square-o " aria-hidden="true"></i>
							</a>

							<div class="modal fade" id="editAddressModal_<?php echo $data->id;?>" tabindex="-1" aria-labelledby="editAddressModal<?php echo $data->id;?>" aria-hidden="true">
							  <div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title"><?php echo Text::_('COM_QUICK2CART_EDIT_CUSTOMER_ADDRESS'); ?></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body"></div>
								</div>
							  </div>
							</div>
							<?php
						};?>
						
						<span class="" onclick="deleteAddress('<?php echo $data->id;?>', '<?php echo Text::_('COM_QUICK2CART_CUSTOMER_ADDRESS_DELETE_MSG');?>')" title="<?php echo Text::_('QTC_DEL');?>">
							<i class="fa fa-trash-o " aria-hidden="true"></i>
						</span>
					</div>
					<div class="clearfix"></div>
				</div>
				<hr class="hr-condensed"/>
				<address>
					<div class="qtc_address_div">
						<?php
							if (!empty($data->address))
							{
								echo "<div>". htmlspecialchars($data->address, ENT_COMPAT, 'UTF-8') . "</div>";
							}

							if (!empty($data->land_mark))
							{
								echo "<div>". htmlspecialchars($data->land_mark, ENT_COMPAT, 'UTF-8') . "</div>";
							}

							echo "<div>". htmlspecialchars($data->city, ENT_COMPAT, 'UTF-8') . "</div>";
							echo "<div>";

							if (!empty($data->state_name))
							{
								echo $data->state_name . ", " ;
							}

							echo $data->country_name . "</div>";
							echo "<div>". htmlspecialchars($data->zipcode, ENT_COMPAT, 'UTF-8') . "</div>";
						?>
					</div>
				</address>
				<hr class="hr-condensed" />
				<div>
					<b><?php echo Text::_('COM_QUICK2CART_USE_ADDRESS_AS');?></b>
				</div>
				<div>
					<?php
					if ($params->get('shipping'))
					{
						$ship_checked = (!empty($data->last_used_for_shipping))?'checked="true"':'';
						?>
						<span class="form-inline">
							<input
								type="checkbox"
								class="addressship qtcHandPointer" <?php echo $ship_checked;?>
								onclick="selectShip('<?php echo $data->id;?>')" id="shipping_address<?php echo $data->id;?>"
								name="shipping_address"
								value="<?php echo $data->id;?>">
							<label class="qtcHandPointer" for="shipping_address<?php echo $data->id;?>">
								<?php echo Text::_('COM_QUICK2CART_CUSTOMER_SHIPPING_ADDRESS');?>
							</label>
						</span>
						<?php
					}
						$bill_checked = (!empty($data->last_used_for_billing))?'checked="true"':'';
					?>
					<span class="form-inline">
						<input
							type="checkbox"
							class="qtcHandPointer addressbill" <?php echo $bill_checked;?>
							onclick="selectBill('<?php echo $data->id;?>')"
							id="billing_address<?php echo $data->id;?>"
							name="billing_address"
							value="<?php echo $data->id;?>">
						<label class="qtcHandPointer" for="billing_address<?php echo $data->id;?>">
							<?php echo Text::_('COM_QUICK2CART_CUSTOMER_BILLING_ADDRESS');?>
						</label>
					</span>
				</div>
				<br>
			</div>
		</div>
	</div>
<?php
}
?>
