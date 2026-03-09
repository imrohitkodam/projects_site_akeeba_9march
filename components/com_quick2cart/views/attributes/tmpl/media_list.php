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

/*list of attributes of item*/
$params       = ComponentHelper::getParams('com_quick2cart');
$qtc_base_url = Uri::base();
$lang         = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$document        = Factory::getDocument();
$addpre_select[] = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
$addpre_select[] = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));
$del_link        = $qtc_base_url.'index.php?option=com_quick2cart&task=attributes.delattribute';
?>
<?php
	if( !empty($pid) && $client )
	{
		// declaration section
		$quick2cartModelAttributes =  new quick2cartModelAttributes();
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if(!class_exists('productHelper'))
		{
			//require_once $path;
			 JLoader::register('productHelper', $path );
			 JLoader::load('productHelper');
		}

		$productHelper = new productHelper;

		if(!empty($item_id))
		{
			$attributes     = $quick2cartModelAttributes->getItemAttributes($item_id);
			$getMediaDetail = $productHelper->getMediaDetail($item_id);
			$addMediaLink   = $qtc_base_url.'index.php?option=com_quick2cart&view=attributes&layout=media&tmpl=component&item_id='.$item_id;
		}
		?>
		<script type="text/javascript">
			function EditFile(file_id,pid)
			{
				var tr_id = '.file_'+file_id;
				techjoomla.jQuery.ajax({
					url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.EditMediFile&tmpl=component&pid='+pid+'&file_id='+file_id,
					type: 'GET',
					dataType: 'json',

					success: function(data)
					{
						//techjoomla.jQuery(tr_id).hide();
						techjoomla.jQuery('.empty_media').remove();

						if (data != null && data != '')
						{
							//techjoomla.jQuery(tr_id).remove();

							//jQuery('#mediafile tr:last').after(data);
							techjoomla.jQuery(tr_id).replaceWith(data);
							window.parent.location.reload();
						}
						//w$media['file_id']indow.location.reload();
					}
				});
			}

			function deleteMediFile(file_id,pid)
			{
				if (confirm("<?php echo Text::_( 'COM_QUICK2CART_EFILE_DELET_CONFIRM' ); ?>") == true)
				{
					var tr_id = '.file_'+file_id;
					techjoomla.jQuery.ajax({
						url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.deleteMediFile&tmpl=component&pid='+pid+'&file_id='+file_id,
						type: 'GET',
						dataType: 'json',

						success: function(data)
						{
							techjoomla.jQuery(tr_id).remove();
							if (data.html = null)
							{
								jQuery("#mediafile").html(data);
							}
							//w$media['file_id']indow.location.reload();
						}
					});
				}
			}

			function AddNewMedia(pid,count)
			{
				//techjoomla.jQuery('.empty_media').remove();
				var rowCount = '';
				if ( techjoomla.jQuery('.empty_media').length ==1 )
				{
					rowCount = 0;
				}
				else
				{
					rowCount = techjoomla.jQuery('#mediafile >tbody >tr').length;
				}
				techjoomla.jQuery.ajax({
					url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.EditMediFile&tmpl=component&pid='+pid+'&count='+rowCount,
					type: 'GET',
					dataType: 'json',
					success: function(data)
					{

						if (rowCount == 0) {
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
		</script>

		<div class="table-responsive">
			<table class="table table-striped table-bordered table-condensed item_attris " id="mediafile">
				<thead>
					<tr>
						<th width="35%" align="left"><b><?php echo Text::_( 'QTC_MEDIAFILE_NAME' ); ?> </b></th>
						<th width="30%"	align="left"><b><?php echo Text::_( 'QTC_MEDIAFILE_PURCHASE_REQUIRE' ); ?></b> </th>
						<th width="15%"	align="left"></th>
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
								<td><?php echo $media['file_display_name']; ?></td>
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
								<?php $del_link= $addMediaLink.'&file_id='.$media['file_id'];?>
								<td>
									<a  rel="{handler: 'iframe', size: {x: window.innerWidth-450, y: window.innerHeight-150}, onClose: function(){EditFile('<?php echo $media['file_id'];?>','<?php echo $item_id; ?>');}}" class="btn btn-sm btn-primary modal qtc_modal" href="<?php echo $edit_link; ?> ">
										<i class="<?php echo $qtc_icon_edit; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
									</a>
									<button type="button" class="btn btn-sm btn-danger "  onclick="deleteMediFile('<?php echo $media['file_id'];?>','<?php echo $item_id; ?>' )">
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
				<strong><?php echo Text::_('QTC_NOTE'); ?></strong><?php echo Text::sprintf('QTC_NOTICE_ATTRIBUTE_OPTION_CURR_NOT_FOUND',$msg_curr,$noticeicon); ?>
			</div>
			<?php
		} // end of count($invalid_op_price,1)>0
	} // if( $pid && $client )
	?>
	<?php
	$fparam = "'" . (!empty($item_id) ? $item_id :0 ) . "','" . (!empty($getMediaDetail) ? count($getMediaDetail) : 0 ) . "'";
	//if(empty($button_dis)){ // $button_dis = ''; ?>
	<a rel="{handler: 'iframe', size: {x: window.innerWidth-350, y: window.innerHeight-150}, onClose: function(){AddNewMedia(<?php echo $fparam; ?>);}}"
		class="btn btn-primary btn-sm <?php echo ($button_dis == ""?'modal':$button_dis)?> qtcAddMediaLink"
		href="<?php echo ($button_dis == ""?$addMediaLink:"javascript:void(0);")?>" style="display: inline;">
		<?php echo Text::_('QTC_ADD_MEDIA_FILES'); ?>
	 </a>
	<div style="clear:both;"></div>


