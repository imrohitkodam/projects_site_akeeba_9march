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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// This is require for media pop up in backend
$this->productHelper = new productHelper;
$prodMedia           = 'prodMedia';
?>
<div class='qtcMediaWrapper container-fluid'>
	<div class="row">
		<div class="col-sm-6 col-xs-12 ">
			<!-- product name-->
			<div class="form-group row">
				<label class="col-xs-12 col-sm-3 form-label" for="qtcmedianame">
					<?php echo Text::_( "COM_QUICK2CART_PROD_PAGE_MEDIA_NAME")?>
				</label>
				<div class="col-xs-12 col-sm-9">
					<input
						type="text"
						name="prodMedia[<?php echo $m ?>][name]>"
						value="<?php echo !empty($mediaDetail[$m]['file_display_name']) ? $mediaDetail[$m]['file_display_name'] : ''; ?>"
						class='input-medium qtcMediaFileName form-control'
						id="qtcmedianame<?php echo $m ?>"
						placeholder="<?php echo Text::_( "COM_QUICK2CART_PROD_PAGE_MEDIA_NAME_PLACEHOLDER")?>" />
					<input
						type="hidden"
						name="prodMedia[<?php echo $m ?>][file_id]>"
						class='input-medium'
						id="qtcmediaFileId<?php echo $m ?>"
						value="<?php echo !empty($mediaDetail[$m]['file_id']) ? $mediaDetail[$m]['file_id'] : ''; ?>" />
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!-- enable-->
			<div class="form-group row">
				<label class="col-xs-12 col-sm-3 form-label" for="qtcmediaStatus">
					<?php echo Text::_( "COM_QUICK2CART_PROD_PAGE_MEDIA_STATUS")?>
				</label>
				<div class="col-xs-12 col-sm-9">
					<label class="checkbox-inline">
						<?php
						$mediastatus = "checked";

						if (isset($mediaDetail[$m]['state']))
						{
							$mediastatus = ($mediaDetail[$m]['state']) ? "checked" : "";
						}
						?>
						<input
							type="checkbox"
							class="form-check-input qtcMediaStatus"
							name="prodMedia[<?php echo $m ?>][status]>"
							autocomplete="off" <?php echo $mediastatus;?> />
							<?php echo Text::_('COM_QUICK2CART_PROD_PAGE_MEDIA_PUBLISHED')?>
					</label>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!-- upload mode-->
			<?php
			if ($fileUploadMode == 3)
			{
				$isPublished = " checked ";
				$uploadModeDisplay = !empty($mediaDetail[$m]['file_id']) ? "af-d-none" : '' ?>
				<div class="form-group row qtcMedUploadModeWrapper <?php echo $uploadModeDisplay; ?>"  >
					<label class="col-xs-12 col-sm-3 form-label" for="qtcmediaStatus">
						<?php echo Text::_( "COM_QUICK2CART_PROD_PAGE_MEDIA_UPLOADMODE")?>
					</label>
					<div class="col-xs-12 col-sm-9">
						<label class="radio-inline">
							<input
								type="radio"
								class="form-check-input qtcMeduaUploadMode_upload"
								id="qtcMeduaUploadMode_upload<?php echo $m?>"
								name="prodMedia[<?php echo $m ?>][uploadMode]>"
								value="upload"
								onchange="changeUploadMethod('upload',<?php echo $m ?>)" checked />
							<?php echo Text::_('COM_QUICK2CART_PROD_PAGE_MEDIA_UPLOAD_FILE')?>
						</label>
						<label class="radio-inline">
							<input
								type="radio"
								class="form-check-input qtcMeduaUploadMode_filepath"
								id="qtcMeduaUploadMode_filepath<?php echo $m?>"
								name="prodMedia[<?php echo $m ?>][uploadMode]>"
								onchange="changeUploadMethod('useFilePath',<?php echo $m ?>)"
								value="useFilePath" />
							<?php echo Text::_('COM_QUICK2CART_PROD_PAGE_MEDIA_USE_FILE_PATH')?>
						</label>
					</div>
					<div class="qtcClearBoth"></div>
				</div>
			<?php
			}
			?>

			<!-- file upload-->
			<div class="form-group row qtcMedUploadWrapper <?php echo !empty($mediaDetail[$m]['file_id']) ? 'af-d-none' : ''; ?>">
				<?php
				$qtcFieldType = 'd-none;';

				if ( $fileUploadMode == 1 || $fileUploadMode == 3 )
				{
					?>
					<div class="">
						<input
							class="qtcMediaFileUploadEle form-control"
							id="qtcMediaFile<?php echo $m ?>"
							type="file"
							name="qtcMediaFile<?php echo $m ?>" />
						<div class="progress mt-1 qtc_progress-barWrapper" id="qtc_progress-barWrapper<?php echo $m ?>">
							<div class="progress-bar progress-bar-success progress-bar-striped af-bg-success qtcMediaProgressBar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" id="qtc_progress-bar<?php echo $m ?>">
							</div>
						</div>
					</div>
				<?php
				}

				$qtcFieldType = ($fileUploadMode == 2) ? '' : 'af-d-none';?>
				<label class="col-xs-12 col-sm-3 form-label <?php echo $qtcFieldType; ?>" for="qtcmediaStatus">
					<?php echo Text::_( "FILEPATH")?>
				</label>
				<div class="col-xs-12 col-sm-9">
					<input
					type="text"
					class="form-control qtcMediaUpload input-medium <?php echo $qtcFieldType; ?>"
					name="prodMedia[<?php echo $m ?>][mediaFilePath]" id="ajax_upload_hidden<?php echo $m ?>"
					value="<?php echo !empty($mediaDetail[$m]['filePath']) ? $mediaDetail[$m]['filePath'] :'' ?>"
					placeholder="<?php echo Text::_( "QTC_FILEPATH")?>" />
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<!-- file upload END -->
			<?php
			if (!empty($mediaDetail[$m]['file_id']) )
			{
				?>
				<div class="qtcMediaProdLink ">
					<div class="col-xs-12 col-sm-3 form-label">
						<strong><?php echo Text::_( "COM_QUICK2CART_PROD_PG_DOWNLOAD"); ?></strong>
					</div>
					<br>
					<div class="qtcProdPgDownLink">
					<?php
						$linkData = array();
						$linkData['linkName']     = $mediaDetail[$m]['file_display_name'];
						$linkData['href']         = $this->productHelper->getMediaDownloadLinkHref($mediaDetail[$m]['file_id'], "strorecall=1"); // authoized to store releated persons
						$linkData['event']        = '';
						$linkData['functionName'] = '';
						$linkData['fnParam']      = '';
						echo $this->productHelper->showMediaDownloadLink($linkData);
						?>
					</div>
				</div>
				<?php
			}
			?>
		</div> <!-- FIRST col-md-6  col-xs-12   end-->
		<div class="col-sm-6 col-xs-12">
			<!-- purchase require name-->
			<div class="form-group row">
				<label class="col-xs-12 col-sm-5 form-label" for="qtcpurchaseRequire">
					<?php echo Text::_( "COM_QUICK2CART_PROD_PURCHASE_REQ")  ?>
				</label>
				<div class="col-xs-12 col-sm-7">
					<label class="checkbox-inline">
						<?php
						$hideExpirationFields = "";
						$qtc_ck_att = "checked";

						if (isset($mediaDetail[$m]['purchase_required']))
						{
							$qtc_ck_att = ($mediaDetail[$m]['purchase_required']) ? "checked" : "";
							$hideExpirationFields = ($mediaDetail[$m]['purchase_required']) ? "" : "af-d-none";
						}
						?>
						<input type="checkbox" class="form-check-input qtc_MedPurchaseReq" name="prodMedia[<?php echo $m ?>][purchaseReq]" autocomplete="off" <?php echo $qtc_ck_att;?> onChange="qtc_expirationChange(<?php echo $m ?>)" />
						<?php echo Text::_('COM_QUICK2CART_PROD_PAGE_MEDIA_PUBLISHE_YES')?>
					</label>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<?php
			if ($eProdUExpiryMode == 'epMaxDownload' || $eProdUExpiryMode == 'epboth')
			{
				$downcount = - 1;

				if (!empty($mediaDetail[$m]['download_limit']))
				{
					$downcount = $mediaDetail[$m]['download_limit'];
				}
				?>
				<div class="form-group row <?php echo $hideExpirationFields; ?>">
					<label class="col-xs-12 col-sm-5 form-label" for="qtcDownCount" title="<?php echo Text::_('COM_QUICK2CART_PROD_DOWN_COUNT_DES')?>">
						<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PROD_DOWN_COUNT_DES'), '', Text::_('COM_QUICK2CART_PROD_DOWN_COUNT'));?>
					</label>
					<div class="col-xs-12 col-sm-7">
						<input type="text" name="prodMedia[<?php echo $m ?>][downCount]" value="<?php echo $downcount;?>" class='form-control input-mini qtcMediaDownCount' id="" placeholder="" />
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<?php
			}
			?>
			<?php
			if ($eProdUExpiryMode == 'epDateExpiry' || $eProdUExpiryMode == 'epboth')
			{
				// days or months
				$expFormat=Text::_( "COM_QUICK2CART_PROD_EXPIRARY_DAYS");
				$eProdExpFormat = $this->params->get('eProdExpFormat','epMonthExp');
				if ($eProdExpFormat == 'epMonthExp')
				{
					$expFormat = Text::_( "COM_QUICK2CART_PROD_EXPIRARY_MONTHS");
				}

				// DB EXPIRARY VALUE
				$expValue = 2;
				if (isset($mediaDetail[$m]['expiry_in']))
				{
					// -1 : it edit produt and changed setting form limit to date
					$expValue = $mediaDetail[$m]['expiry_in'];
				}
				?>
				<div class="form-group row" style="<?php echo $hideExpirationFields; ?>">
					<label class="col-xs-12 col-sm-5 form-label" for="">
						<?php echo Text::_( "COM_QUICK2CART_PROD_EXPIRARY")?>
					</label>
					<div class="col-xs-12 col-sm-7">
						<div class="input-group  col-xs-12">
							<input id="" name="prodMedia[<?php echo $m ?>][expirary]" value="<?php echo $expValue; ?>" class="form-control qtcMediaExp" placeholder="" type="text" />
							<span class="input-group-text"><?php echo $expFormat;?></span>
						</div>
					</div>
				</div>
				<?php
			}?>
		</div>
	</div>
</div>

