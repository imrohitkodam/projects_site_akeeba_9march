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
use Joomla\CMS\Uri\Uri;

$user = Factory::getUser();
$document = Factory::getDocument();

$expiryMode = $this->params->get('eProdUExpiryMode');

$jinput = Factory::getApplication()->input;
//$orderid = $jinput->get('orderid','');
$guest_email = $jinput->get('guest_email','','RAW');

$productHelper = new productHelper;
// STEP 1: check for user login or not
if (!$user->id && empty($this->guest_email_chk))
{
	$app    = Factory::getApplication();
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
	<form action="" name="adminForm" id="adminForm" class="form-validate" method="post">
	<div class="row-fluid">
		<legend>
			<?php echo Text::_('QTC_DOWNLOADS_MY_DOWNLOAD_HEADING'); ?>
		</legend>
	</div>

	<div id="qtc-filter-bar" class="qtc-btn-toolbar">
		<div class="filter-search btn-group pull-left float-start q2c-btn-wrapper">
			<input type="text" name="search_list" id="search_list"
			placeholder="<?php echo Text::_('QTC_DOWNLOAD_SEARCH_PLACE'); ?>"
			value="<?php echo $this->lists['search_list']; ?>"
			class="qtc-hasTooltip"
			title="<?php echo Text::_('QTC_DOWNLOAD_SEARCH_PLACE'); ?>" />
		</div>

		<div class="btn-group pull-left float-start q2c-btn-wrapper">
			<button type="submit" class="btn qtc-hasTooltip"
			title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
			</button>
			<button type="button" class="btn qtc-hasTooltip"
			title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
			onclick="document.getElementById('search_list').value='';this.form.submit();">
				<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
			</button>
		</div>

		<div class="pull-right float-end q2c-btn-wrapper">
			<label for="limit" class="element-invisible">
				<?php echo Text::_('COM_QUICK2CART_SEARCH_SEARCHLIMIT_DESC'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>

	<div class="clearfix"></div>

	<?php if (empty($this->allDownloads)) : ?>
		<div class="clearfix">&nbsp;</div>
		<div class="alert alert-warning">
			<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php
		else : ?>
			<table class="table table-striped table-bordered" id="myDownloadsList">
				<thead>
					<tr>
						<th class="q2c_width_20">
							<?php echo HTMLHelper::_( 'grid.sort', Text::_('QTC_DOWNLOADS_ORDER_ID'),'oi.order_id', $this->lists['order_Dir'], $this->lists['order']); ?>
						</th>

						<th>
							<?php echo HTMLHelper::_( 'grid.sort', Text::_('QTC_DOWNLOADS_FILE_NAME'),'pf.file_display_name', $this->lists['order_Dir'], $this->lists['order']); ?>
						</th>

						<?php
						if ($expiryMode == 'epMaxDownload' | $expiryMode == 'epboth')
						{
							?>
							<th class="q2c_width_20">
								<?php echo HTMLHelper::_( 'grid.sort', Text::_('QTC_DOWNLOADS_LIMIT'),'f.download_count', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<?php
						}
						?>

						<?php
						if($expiryMode == 'epDateExpiry' | $expiryMode == 'epboth')
						{
							?>
							<th class="q2c_width_15">
								<?php echo HTMLHelper::_( 'grid.sort', Text::_('QTC_DOWNLOADS_PURCHASE_DEATE'),'f.cdate`', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>

							<th class="q2c_width_15">
								<?php echo HTMLHelper::_( 'grid.sort', Text::_('QTC_DOWNLOADS_VALID_TILL'),'f.expirary_date', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
							<?php
						}
						?>
					</tr>
				</thead>

				<tbody>
					<?php
					$id = 1;

					foreach($this->allDownloads as $media)
					{
						$authorize = $productHelper->mediaFileAuthorise($media->product_file_id,0,$guest_email,$media->order_item_id);
						$validDown = 'error';

						if (!empty($authorize['validDownload'])  && $authorize['validDownload']==1)
						{
							$validDown = 'success';
						}
						?>

						<tr class="<?php echo $validDown; ?>">
							<td class="q2c_width_20">
								<?php echo $media->prefix.$media->order_id ;?>
								<!--
								<a href="<?php //echo  JUri::base().substr(JRoute::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid='.$media->order_id),strlen(JUri::base(true))+1); ?>"><?php //echo JHtml::tooltip(Text::_('QTC_TOOLTIP_VIEW_ORDER_MSG'), Text::_('QTC_TOOLTIP_VIEW_ORDER'), '', $media->prefix.$media->order_id ) ;?></a>
								-->
							</td>

							<td>
								<?php
								$fileDetail = $media->file_display_name;
								$linkData = array();
								$linkData['linkName'] = $media->file_display_name;
								$linkData['href'] = $productHelper->getMediaDownloadLinkHref($media->product_file_id,'guest_email='.$guest_email.'&orderid='.$media->order_id.'&order_item_id='.$media->order_item_id);
								$linkData['event'] = '';
								$linkData['functionName'] = '';
								$linkData['fnParam'] = ''.$guest_email;

								echo $productHelper->showMediaDownloadLink($linkData);
								?>
							</td>

							<?php
							if ($expiryMode == 'epMaxDownload' | $expiryMode == 'epboth')
							{
								?>
								<td class="q2c_width_20">
									<?php
									if ($media->download_limit == "-1")
									{
										echo Text::_('QTC_MY_DOWN_UNLIMITED');
									}
									else
									{
										if($media->download_limit  == NULL)
										{
											echo Text::sprintf('	-	');
										}
										else
										{
											echo Text::sprintf('QTC_MY_DOWN_OUT_OF',($media->download_limit - $media->download_count),$media->download_limit);
										}
									}
									?>
								</td>
								<?php
							}
							?>

							<?php
							if($expiryMode == 'epDateExpiry' | $expiryMode == 'epboth')
							{
								?>
								<td class="q2c_width_15">
									<?php echo $media->cdate;  ?>
								</td>

								<td class="q2c_width_15">
									<?php echo ($media->expirary_date != '0000-00-00 00:00:00') ? $media->expirary_date : '-' ?>
								</td>
								<?php
							}
							?>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" id='hidid' name="id" value="" />
		<input type="hidden" id='hidstat' name="status" value="" />
		<input type="hidden" name="task" id="task" value="" />
		<input type="hidden" name="view" value="downloads" />
		<input type="hidden" name="controller" value="downloads" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	</form>
</div>
