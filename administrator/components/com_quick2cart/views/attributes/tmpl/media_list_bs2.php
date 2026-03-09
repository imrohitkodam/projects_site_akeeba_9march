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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$params       = ComponentHelper::getParams('com_quick2cart');
$qtc_base_url = Uri::base();
$lang         = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$document        = Factory::getDocument();
$addpre_select[] = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
$addpre_select[] = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));
$del_link        = $qtc_base_url.'index.php?option=com_quick2cart&task=attributes.delattribute';

if( !empty($pid) && $client )
{
	$quick2cartModelAttributes =  new quick2cartModelAttributes();
	$path                      = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

	if(!class_exists('productHelper'))
	{
		JLoader::register('productHelper', $path );
		JLoader::load('productHelper');
	}
	$productHelper = new productHelper;

	if(!empty($item_id))
	{
		$attributes     = $quick2cartModelAttributes->getItemAttributes($item_id);
		$getMediaDetail = $productHelper->getMediaDetail($item_id);
		$addMediaLink   = $qtc_base_url.'index.php?option=com_quick2cart&view=attributes&layout=media_bs2&tmpl=component&item_id='.$item_id;
	}
	?>
	<script type="text/javascript">
		function EditFile(file_id,pid)
		{
			var tr_id = '.file_'+file_id;
			techjoomla.jQuery.ajax({
				url:  Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.EditMediFile&pid='+pid+'&file_id='+file_id,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					techjoomla.jQuery('.empty_media').remove();

					if (data != null && data != '')
					{
						techjoomla.jQuery(tr_id).replaceWith(data);
						window.parent.location.reload();
					}
				}
			});
		}
		function deleteMediFile(file_id,pid)
		{
			if (confirm("<?php echo Text::_( 'COM_QUICK2CART_EFILE_DELET_CONFIRM' ); ?>") == true)
			{
				var tr_id = '.file_'+file_id;
				techjoomla.jQuery.ajax({
					url:  Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.deleteMediFile&pid='+pid+'&file_id='+file_id,
					type: 'GET',
					dataType: 'json',

					success: function(data)
					{
						techjoomla.jQuery(tr_id).remove();

						if (data.html = null)
						{
							jQuery("#mediafile").html(data);
						}
					}
				});
			}
		}
		function AddNewMedia(pid,count)
		{
			var rowCount = '';

			if (techjoomla.jQuery('.empty_media').length ==1)
			{
				rowCount = 0;
			}
			else
			{
				rowCount = techjoomla.jQuery('#mediafile >tbody >tr').length;
			}

			techjoomla.jQuery.ajax({
				url:  Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.EditMediFile&pid='+pid+'&count='+rowCount,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if (rowCount == 0)
					{
						techjoomla.jQuery('.empty_media').remove();
						techjoomla.jQuery("#mediafile tbody").append(data);
					}
					else
					{
						jQuery('#mediafile tr:last').after(data);
					}
					window.parent.location.reload();
				}
			});
		}
		function toggleAddMediaListModal(itemAttributeId = '')
		{
			if (itemAttributeId == '')
			{
				jQuery('#addMediaListModal').attr('data-width' , (window.innerWidth)/2);
				jQuery('#addMediaListModal').attr('data-height' , window.innerHeight);
				jQuery('#addMediaListModal').modal('show');
			}
			else
			{
				jQuery('#editMediaListModal_'+itemAttributeId).attr('data-width' , (window.innerWidth)/2);
				jQuery('#editMediaListModal_'+itemAttributeId).attr('data-height' , window.innerHeight);
				jQuery('#editMediaListModal_'+itemAttributeId).modal('show');
			}
		}
	</script>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-condensed item_attris " id="mediafile">
			<thead>
				<tr>
					<th width="35%" align="left"><b><?php echo Text::_( 'QTC_MEDIAFILE_NAME' ); ?> </b></th>
					<th width="30%"	align="left"><b><?php echo Text::_( 'QTC_MEDIAFILE_PURCHASE_REQUIRE' ); ?></b> </th>
					<th width="15%"	align="left"><b><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$invalid_op_price = array();

				if(!empty($getMediaDetail))
				{
					foreach($getMediaDetail as $media)
					{ ?>
						<tr class="<?php echo "file_".$media['file_id']; ?>">
							<td> <?php echo $media['file_display_name']; ?></td>
							<td>
								<?php
									$mediaClass     = ' badge';
									$purchaseStatus = Text::_( 'QTC_ADDATTRI_PURCHASE_REQ_NO' );

									if(!empty($media['purchase_required']))
									{
										$mediaClass     = ' badge badge-success';
										$purchaseStatus = Text::_( 'QTC_ADDATTRI_PURCHASE_REQ_YES' );
									}
								?>
								<span class="<?php echo $mediaClass; ?>"><?php echo $purchaseStatus;	?></span>
							</td>
							<?php $edit_link = $addMediaLink.'&file_id='.$media['file_id'].'&edit=1';?>
							<?php $del_link  = $addMediaLink.'&file_id='.$media['file_id'];?>
							<td>
								<button
									class="btn btn-small btn-primary"
									type="button"
									onclick="toggleAddMediaListModal(<?php echo $media['file_id'];?>);"
									data-target="#editMediaListModal_<?php echo $media['file_id'];?>">
									<i class="<?php echo $qtc_icon_edit; ?>"></i>
								</button>
								<?php
									echo HTMLHelper::_(
										'bootstrap.renderModal',
										'editMediaListModal_' . $media['file_id'],
										array(
											'closeButton'=> false,
											'url'        => $edit_link,
											'modalWidth' => '80',
											'bodyHeight' => '70',
											'width'      => '100px',
											'height'     => '800px',
											'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal">'. Text::_('COM_QUICK2CART_COMMON_CLOSE') .'</button>',
										)
									)
								?>
								<button
									type="button"
									class="btn btn-small btn-danger"
									onclick="deleteMediFile('<?php echo $media['file_id'];?>','<?php echo $item_id; ?>' )">
									<i class="<?php echo Q2C_ICON_TRASH; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
								</button>
							 </td>
						</tr>
					<?php
					}
				}
				else
				{
				?>
					<tr class="empty_media">
						<td colspan="3"> <?php echo Text::_( 'QTC_MEDIAFILE_EMPTY_MSG' ); ?></td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;"></div>
	<?php
	if(count($invalid_op_price)>0)
	{
		$msg_curr=implode("/",$invalid_op_price);
		?>
		<div class="alert ">
			<button type="button" class="close" data-dismiss="alert"></button>
			<strong><?php echo Text::_('QTC_NOTE'); ?></strong>
			<?php echo Text::sprintf('QTC_NOTICE_ATTRIBUTE_OPTION_CURR_NOT_FOUND',$msg_curr,$noticeicon); ?>
		</div>
		<?php
	}
}

$fparam = "'" . (!empty($item_id) ? $item_id :0 ) . "','" . (!empty($getMediaDetail) ? count($getMediaDetail) : 0 ) . "'";
?>

<button class="btn btn-small btn-primary qtcAddMediaLink" type="button" onclick="toggleAddMediaListModal();" data-target="#addMediaListModal">
	<?php echo Text::_('QTC_ADD_MEDIA_FILES'); ?>
</button>
<?php
	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'addMediaListModal',
		array(
			'closeButton'=> false,
			'url'        => $addMediaLink,
			'modalWidth' => '80',
			'bodyHeight' => '70',
			'width'      => '100px',
			'height'     => '800px',
			'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal">'. Text::_('COM_QUICK2CART_COMMON_CLOSE') .'</button>',
		)
	)
?>
<div style="clear:both;"></div>
