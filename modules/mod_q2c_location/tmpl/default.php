<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2020 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('stylesheet','modules/mod_q2c_location/assets/css/q2clocation.css');
HTMLHelper::_('script','modules/mod_q2c_location/assets/js/q2clocation.js');

$country        = $params->get('country', '', 'STRING');
$defaultAddress = $location . ', ' . $city . ', ' . $zipCode;

if (isset($_COOKIE['q2cModLocationAddress']) && !empty($_COOKIE['q2cModLocationAddress']))
{
	$defaultAddress = $_COOKIE['q2cModLocationAddress'];
}

$defaultdisplay = (!empty($addresses)) ? 'display: none;' : 'display: flex;';
?>
<style>
.pac-container {
    z-index: 1050;
}
</style>
<div class="modq2c_location_<?php echo $moduleclass_sfx;?>">
	<div class="modq2c_location row">
		<div data-toggle="modal" data-target="#myLocationModal">
			<div id="location-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
			<div>
				<div class="city-location">
					<p id="city">
						<?php echo $defaultAddress;?>
					</p>
				</div>
			</div>
		</div>
		<div class="modal center fade" id="myLocationModal" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h1>
							<?php echo (!empty($addresses)) ? Text::_('MOD_Q2C_LOCATION_USER_SAVED_ADDRESSES') : Text::_('MOD_Q2C_LOCATION_CHOOSE_SHIPPING_LOCATION');?>
						</h1>
					</div>
					<div class="modal-body">
						<?php
						if (!empty($addresses))
						{?>
							<div class="list-group" id="previousAddress">
								<?php
									foreach ($addresses as $key => $address)
									{
									?>
										<div class="center">
											<a href="javascript:void(0);" onclick="q2c.q2cModLocation.updateUserSelectedAddress(this)" id="userAddress_<?php echo $key?>">
												<div class="af-w-100 justify-content-between previousEachAddress">
													<p class="mb-1">
														<i class="fa fa-dot-circle-o" aria-hidden="true"></i>
														<?php
														if (!empty($address->address))
														{
															?>
															<span><?php echo trim($address->address);?>, </span>
															<?php
														}

														if (!empty($address->city))
														{
															?>
															<span><?php echo trim($address->city);?>, </span>
															<?php
														}

														if (!empty($address->state_name))
														{
															?>
															<span><?php echo trim($address->state_name);?>, </span>
															<?php
														}

														if (!empty($address->country_name))
														{
															?>
															<span><?php echo trim($address->country_name);?>, </span>
															<?php
														}

														if (!empty($address->zipcode))
														{
															?>
															<span><?php echo trim($address->zipcode);?></span>
															<?php
														}
														?>
													</p>
												</div>
											</a>
										</div>
										<hr />
									<?php
									}
								?>
							</div>
							<div>
								<h3>
									<a href="javascript:void(0);" onclick="q2c.q2cModLocation.toggleLocatorButton()">
										<?php echo Text::_('MOD_Q2C_LOCATION_USER_TAKE_DIFFERENT_ADDRESSE');?>
									</a>
								</h3>
							</div>
						<?php
						}?>
						<section class="modq2c_location_popover_container" id="modq2c_location_popover_container" style="<?php echo $defaultdisplay?>">
							<div class="col-sm-6" id="locator-input-section">
								<input
									class="col-xs-5"
									type="text"
									placeholder="<?php echo Text::_('MOD_Q2C_LOCATION_ENTER_YOUR_ADDRESS')?>"
									id="autocomplete"
									/>
								<i class="fa fa-location-arrow" id="locator-button" aria-hidden="true"></i>
							</div>
						</section>
						<div>
							<em>
								<p class="alert alert-error col-sm-12" id="q2cModLocationErrorMsg" style="display: none;">
								</p>
							</em>
						</div>
						<script src="<?php echo 'https://maps.googleapis.com/maps/api/js?key=' . $googleMapsApiKey . '&libraries=places'?>"></script>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$isPinwiseShippingPlgEnable = false;

if (!empty($q2cPinwiseShippingPlugin))
{
	$isPinwiseShippingPlgEnable = true;
}
?>
<script>
var googleMapLocationKey = '<?php echo $googleMapsApiKey;?>';
var countryISOCode = '<?php echo $country;?>';
var defaultAddress = '<?php echo $defaultAddress;?>';
var isPinwiseShippingPlgEnable = '<?php echo $isPinwiseShippingPlgEnable?>';
var shippingPluginpincodeArray = <?php echo "'" . implode(",", $serviceablePincodes) . "'"?>;
q2c.q2cModLocation.init();
</script>
