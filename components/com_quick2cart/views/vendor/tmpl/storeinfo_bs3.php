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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.renderModal');

$comquick2cartHelper = new comquick2cartHelper;
$libclass            = $comquick2cartHelper->getQtcSocialLibObj();

$app    = Factory::getApplication();
$input  = $app->input;
$layout = $input->get('layout');
$tmpl   = ($input->get('tmpl') != null) ? $input->get('tmpl') : '';

$storeHelper    = new storeHelper;
$storeOwner     = $storeHelper->getStoreOwner($this->store_id);
$integrate_with = $this->params->get('integrate_with','none');

if ($integrate_with != 'none')
{
	$profile_url  = $libclass->getProfileUrl(Factory::getUser($storeOwner));
	$UserName     = Factory::getUser($storeOwner)->name;
	$profile_path = "<a alt='' href='".$profile_url."'>".$UserName."</a>";
}

if (!empty($this->storeDetailInfo))
{
	$sinfo = $this->storeDetailInfo;

	// Sanitize data
	$sinfo['title']        = htmlspecialchars($sinfo['title'], ENT_COMPAT, 'UTF-8');
	$sinfo['address']      = htmlspecialchars($sinfo['address'], ENT_COMPAT, 'UTF-8');
	$sinfo['land_mark']    = htmlspecialchars($sinfo['land_mark'], ENT_COMPAT, 'UTF-8');
	$sinfo['store_email']  = htmlspecialchars($sinfo['store_email'], ENT_COMPAT, 'UTF-8');
	$sinfo['company_name'] = htmlspecialchars($sinfo['company_name'], ENT_COMPAT, 'UTF-8');

	if ($layout == "storeinfo")
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<?php
	}
	?>
			<div class="row-">
				<div class=" col-xs-12">
					<legend>
						<?php
						$storeHelper = new storeHelper();
						$storeLink   = $storeHelper->getStoreLink($this->storeDetailInfo['id']);

						if (empty($tmpl))
						{?>
							<a href="<?php echo $storeLink; ?>" class="btn btn-sm">
								<i class="<?php echo Q2C_ICON_HOME;?>"></i>
							</a><?php 
						}?> &nbsp; <?php echo $sinfo['title']; ?>

						<?php
						if (empty($this->editstoreBtn))
						{
							$social_options= '';
							PluginHelper::importPlugin('system');
							$result = $app->triggerEvent('onProductDisplaySocialOptions', array($this->storeDetailInfo['id'], 'com_quick2cart.vendor.storeinfo', $sinfo['title'], $storeLink));

							// Call the plugin and get the result
							if (!empty($result))
							{
								$social_options=$result[0];
							}

							if (!empty($social_options))
							{
								?>
									<span class="social_options"><?php echo $social_options; ?></span>
								<?php
							}
						}

						if (!empty($this->editstoreBtn) && (!empty($this->store_id)))
						{
							$storeid = $this->store_id;
							$createstore_Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
							echo "<button type='button' title=".Text::_( 'SA_EDIT' )." class='btn  btn_margin pull-right btn-sm' onclick=\"window.open('".Route::_("index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=".$storeid."&Itemid=".$createstore_Itemid)."')\" >
									<i class='" . QTC_ICON_EDIT . "'></i></button>";
						}

						if ($integrate_with != 'none')
						{
							?>
							<p style="font-size: 13px;">
								<?php echo Text::sprintf('COM_QUICK2CART_CREATED_BY',$profile_path); ?>
							</p>
							<?php
						}
						?>
					</legend>
					<div class="row">
						<div class="col-sm-4 col-xs-12">
							<?php
							$addInfo              = array();
							$addInfo["address"]   = $sinfo['address'];
							$addInfo["land_mark"] = $sinfo['land_mark'];

							if ($sinfo['pincode'])
							{
								$addInfo["cityPincode"] = $sinfo['city'] . " - " . $sinfo['pincode'];
							}

							if ($sinfo['region'])
							{
								$addInfo["region"] = $sinfo['region'];
							}

							$addInfo["country"] = $sinfo['country'];

							$addInfo = array_filter($addInfo,"strlen");
							$addStr = implode(",<br/>", $addInfo);
							if (!empty($sinfo['address']))
							{ ?>
								<address class="">
									<strong><?php echo Text::_('VENDER_ADDRESS'); ?></strong><br/>
									<span class="qtcWordWrap"><?php echo $addStr; ?></span>
								</address>
							<?php
							} ?>
						</div>
						<div class="col-sm-4 col-xs-12">
							<address>
								<abbr title="Phone">
									<strong><?php echo Text::_('VENDER_CONTACT_INFO'); ?></strong>:
								</abbr>
								<?php echo $sinfo['phone']; ?>
								<br/>
								<span class="qtcWordWrap"><?php echo $sinfo['store_email']; ?></span>
							</address>
						</div>
						<div class="col-sm-4 col-xs-12">
							<?php
							$img = (!empty($sinfo['store_avatar'])) ? $comquick2cartHelper->isValidImg($sinfo['store_avatar']) : '';

							if (empty($img))
							{
								$img = $storeHelper->getDefaultStoreImage();
							}
							?>
							<img align="" class='img-rounded img-polaroid qtcImgAlignCenter' src="<?php echo $img;?>" alt="<?php echo  Text::_('QTC_IMG_NOT_FOUND') ?>"/>
						</div>
					</div>
					<div>
						<div class="col-xs-12">
							<p>
								<?php
								$enableEditor = $this->params->get('enable_editor', 0);

								if ($layout=="storeinfo" || $enableEditor)
								{
									echo $sinfo['description'] ;
								}
								else
								{
									// GETTING STORE INFO LINK
									$vendor_Itemid      = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor');
									$storeinfo_link     = Route::_('index.php?option=com_quick2cart&view=vendor&layout=storeinfo&Itemid=0&store_id='.$sinfo['id'].'&tmpl=component');
									$description_length = strlen($sinfo['description'] );
									$limit              = $this->params->get("storeDescriptionLimit",100);
									$readmore           = substr($sinfo['description'] , 0, $limit);

									if (!empty($readmore) && $limit < $description_length)
									{
										$readmore =$readmore." ...&nbsp;";
									}

									echo $readmore;

									// chk FOR CHAR LIMIT TO SHOW
									if ($limit < $description_length)
									{
										$modalConfig = array('width' => '600px', 
											'height' => '600px', 
											'closeButton' => false, 
											'modalWidth' => 80, 
											'bodyHeight' => 70);
										$modalConfig['url'] = $storeinfo_link;
										echo HTMLHelper::_('bootstrap.renderModal', 'qtcRemoveModal', $modalConfig);
										?>
										<a href="javascript:;" title="<?php echo Text::_('QTC_READMORE')?>" data-target="#qtcRemoveModal" data-toggle="modal" class="qtc_modal" title="<?php echo Text::_('QTC_READMORE'); ?>">
											<?php echo Text::_('QTC_READMORE');?>
										</a>
										<?php
									}
								}
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
	<?php
	if ($layout=="storeinfo")
	{
		?>
		</div>
		<?php
	}
}
?>

