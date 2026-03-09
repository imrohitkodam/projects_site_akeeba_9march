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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
$input               = Factory::getApplication()->input;
// $post                = $input->post();
$params              = ComponentHelper::getParams('com_quick2cart');
$comquick2cartHelper = new comquick2cartHelper;
$catpage_Itemid      = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');

// Show shipping tab on checkout page, if product has shipping methods
$input->set('showShipTab', 0);

$shippingMode                = $params->get('shippingMode', 'itemLevel', 'String');
$restrictItemLevelShipping   = $params->get('restrictItemLevelShipping', 0, 'Integer');
$itemlLevelshppingrestricted = '0';
?>
<div class="qtcAddBorderToWrapper"  id="qtcShippingMethTab">
	<?php
	if (!empty($itemWiseShipDetail))
	{
	?>
		<strong><?php echo Text::_('COM_QUICK2CART_CHOOSE_YOUR_DELIVARY_OPTION') ?>&nbsp;</strong>
		<table class="table table-striped ">
			<tbody>
				<tr>
					<th class="" width="20%" align="left"><?php echo Text::_('COM_QUICK2CART_PROD_NAME');?></th>
					<th class="" width="" align="left"><?php echo Text::_('COM_QUICK2CART_CKOUT_SHIP_METHODS');?></th>
				</tr>
				<?php
				// Load Attributes model
				$attri_model = $comquick2cartHelper->loadqtcClass($path  =  JPATH_SITE . '/components/com_quick2cart/models/attributes.php', "quick2cartModelAttributes");
				$i           = -1;

				foreach ($itemWiseShipDetail as $item)
				{
					$i++;
					$itemDetail    = $item['itemDetail'];
					$item_id       = $itemDetail['item_id'];
					$shippingMeths = $item['shippingMeths'];
						?>
						<tr>
							<td width="40%">
								<div class="">
									<?php
									$data         = (array) $itemDetail = $attri_model->getItemDetail(0,'', $item_id);
									$product_link = $comquick2cartHelper->getProductLink($data['item_id'], 'detailsLink');
									$images       = json_decode($data['images'], true);
									$img          = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';

									if (!empty($images))
									{
										require_once(JPATH_SITE . '/components/com_quick2cart/helpers/media.php');

										// create object of media helper class
										$media                       = new qtc_mediaHelper();
										$file_name_without_extension = $media->get_media_file_name_without_extension($images[0]);
										$media_extension             = $media->get_media_extension($images[0]);
										$img                         = $comquick2cartHelper->isValidImg($file_name_without_extension.'_L.'.$media_extension);

										if (empty($img))
										{
											$img = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';
										}
									}
									?>
									<div class="">
										<strong class="center">
											<a title="<?php echo $data['name'];?>" href="<?php echo $product_link;?>">
												<?php echo $data['name'];?>
											</a>
										</strong>
									</div>
									<div class="caption">
										<img class=' img-rounded '
											src="<?php echo $img;?>"
											alt="<?php echo  Text::_('QTC_IMG_NOT_FOUND') ?>"
											title="<?php echo $data['name'];?>" width='75px' />
									</div>
								</div>
							</td>
							<td>
								<div>
									<strong ><?php echo isset($itemDetail['title']) ? $itemDetail['title'] : '';?></strong>
								</div>
								<div>
									<?php
									$firstMeth = 0;
									$shippingMeths = $shippingMeths ? array_filter($shippingMeths) : '';

									if (!empty($shippingMeths))
									{
										if ($showShipTab == 0)
										{
											$showShipTab = 1;
											$input->set('showShipTab', 1);
										}

										foreach ($shippingMeths as $key=>$shipMeth)
										{
											if (empty($shipMeth))
											{
												continue;
											}

											$methodId = $shipMeth['methodId'];
											$checked = '';

											if ($firstMeth == 0)
											{
												$checked = ' checked ';
												$firstMeth = 1;
											}

											$fieldName      = "itemshipMethDetails[" . $methodId . "]";
											$radioFieldName = "itemshipMeth[" . $i . "][" . $item_id . "]";
											?>
											<div>
												<input type="hidden" name="<?php echo $fieldName . '[item_id]' ?>" value="<?php echo $item_id; ?>">
												<input type="hidden" name="<?php echo $fieldName . '[methodId]' ?>" value="<?php echo $shipMeth['methodId']; ?>">
												<input type="hidden" name="<?php echo $fieldName . '[methRateId]' ?>" value="<?php echo $shipMeth['plugMethRateId']; ?>">
												<input type="hidden" name="<?php echo $fieldName . '[totalShipCost]' ?>" value="<?php echo $shipMeth['totalShipCost']; ?>">
												<input type="hidden" name="<?php echo $fieldName . '[client]' ?>" value="<?php echo $shipMeth['client']; ?>">
												<label class="radio">
													<input type="radio" name="<?php echo $radioFieldName;?>" id="" value="<?php echo $shipMeth['methodId']; ?>" <?php echo $checked; ?> >
													<?php $pricetext =  $comquick2cartHelper->getFromattedPrice(number_format($shipMeth['totalShipCost'],2));
														echo $pricetext . ' - ' . $shipMeth['name'];
													?>
												</label>
											</div>
											<?php
										}
									}
									else
									{
										if ($shippingMode == 'itemLevel' && $restrictItemLevelShipping == 1)
										{
											?>
											<p style="color:red">
												<?php echo Text::_('COM_QUICK2CART_ITEM_LEVEL_SHIP_METHODS_RESTRICTED');?>
											</p>
											<?php
											$itemlLevelshppingrestricted = '1';
										}
										else
										{
											echo Text::_('COM_QUICK2CART_NO_SHIPPING_METHOS_WE_WILL_CONTACT_U');
										}
									}
									?>
								</div>
							</td>
						</tr>
					<?php
				}
				?>
			</tbody>
			<tfoot>
				<tr></tr>
			</tfoot>
		</table>
	<?php
	}
	else
	{
		?>
		<div class="alert alert-info"><?php echo Text::_('COM_QUICK2CART_NO_AVAIL_SHIP_METHODS_PRECEED')?></div>
		<?php
	}
	?>
	<input type="hidden" name="<?php echo 'itemlLevelshppingrestricted'?>" id='itemlLevelshppingrestricted' value="<?php echo $itemlLevelshppingrestricted; ?>">
</div>

<script type="text/javascript">
function goToByScroll(id){
	techjoomla.jQuery('html,body').animate({scrollTop: techjoomla.jQuery("#"+id).offset().top},'slow');
}
</script>
