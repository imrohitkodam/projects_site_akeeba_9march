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

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

/*list of attributes of item*/
$params          = ComponentHelper::getParams('com_quick2cart');
$qtc_base_url    = Uri::base();
$document        = Factory::getDocument();
$addpre_select[] = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
$addpre_select[] = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));
$add_link        = $qtc_base_url . 'index.php?option=com_quick2cart&view=attributes&layout=attribute_bs2&tmpl=component&pid=' . $pid . '&client=' . $client;
$del_link        = $qtc_base_url . 'index.php?option=com_quick2cart&controller=attributes&task=delattribute';
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
<?php
if($pid && $client)
{
	$quick2cartModelAttributes =  new quick2cartModelAttributes();

	if(!empty($item_id))
	{
		$attributes       = $quick2cartModelAttributes->getItemAttributes($item_id);
		$attributes_count = $quick2cartModelAttributes->getItemAttributes($item_id);
	}
	?>
	<script type="text/javascript">
		function EditAttribute(att_id,pid)
		{
			var td_id = '#item_attris'+att_id;
			var tr_id = '.att_'+att_id;
			techjoomla.jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.EditAttribute&att_id='+att_id+'&pid='+pid,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					techjoomla.jQuery(tr_id).replaceWith(data);
					if(data){
						window.parent.location.reload();
					}
				}
			});
		}
		function AddNewAttribute(pid,count)
		{
			var rowCount = '';
			if (techjoomla.jQuery('#empty_attr').length == 1)
			{
				rowCount = 0;
			}
			else
			{
				rowCount = techjoomla.jQuery('#item_attris >tbody >tr').length;
			}

			techjoomla.jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.AddNewAttribute&pid='+pid+'&count='+rowCount,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if (rowCount == 0)
					{
						techjoomla.jQuery('#empty_attr').remove();
						techjoomla.jQuery("#item_attris tbody").append(data);
					}
					else
					{
						jQuery('#item_attris tr:last').after(data);
					}

					if(data)
					{
						window.parent.location.reload();
						SqueezeBox.trash();
					}
				}
			});
		}
		function deleteAttribute(dellink,pid)
		{
			if (confirm("<?php echo Text::_( 'COM_QUICK2CART_ATTRIBUTE_DELET_CONFIRM' ); ?>") == true)
			{
				var tr_id = '.att_'+dellink;
				techjoomla.jQuery.ajax({
					url: Joomla.getOptions('system.paths').base + '/index.php?option=com_quick2cart&task=attributes.delattribute&pid='+pid+'&attr_id='+dellink,
					type: 'GET',
					dataType: 'json',

					success: function(data)
					{
						techjoomla.jQuery(tr_id).remove();
						if (data.html != null)
						{
							jQuery("#item_attris").html(data);
						}
					}
				});
			}
		}
		function toggleAddAttributeListModal(itemAttributeId = '')
		{
			if (itemAttributeId == '')
			{
				jQuery('#addAttributeListModal').attr('data-width' , (window.innerWidth)/2);
				jQuery('#addAttributeListModal').attr('data-height' , window.innerHeight);
				jQuery('#addAttributeListModal').modal('show');
			}
			else
			{
				jQuery('#editAttributeListModal_'+itemAttributeId).attr('data-width' , (window.innerWidth)/2);
				jQuery('#editAttributeListModal_'+itemAttributeId).attr('data-height' , window.innerHeight);
				jQuery('#editAttributeListModal_'+itemAttributeId).modal('show');
			}
		}
	</script>
	<div class="table-responsive">
		<table id="item_attris" class="table table-striped table-bordered table-condensed item_attris">
			<thead>
				<tr>
					<th width="35%" align="left"><b><?php echo Text::_('QTC_ADDATTRI_NAME'); ?> </b></th>
					<th width="30%"	align="left"><b><?php echo Text::_('QTC_ADDATTRI_OPT'); ?></b> </th>
					<th width="15%"	align="left"><b><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$invalid_op_price=array();

				if(!empty($attributes) )
				{
					foreach($attributes as $attributes)
					{ ?>
						<tr class="<?php echo "att_".$attributes->itemattribute_id; ?>">
							<td><?php echo $attributes->itemattribute_name; ?></td>
							<td id="<?php echo "att_list_".$attributes->itemattribute_id; ?>">
								<?php
								$comquick2cartHelper = new comquick2cartHelper;
								$currencies          = $params->get('addcurrency');
								$curr                = explode(',',$currencies);
								$atri_options        = $comquick2cartHelper->getAttributeDetails($attributes->itemattribute_id	);

								foreach($atri_options as $atri_option)
								{?>
									<div>
									<?php
										$noticeicon = "";
										$opt_str    = $atri_option->itemattributeoption_name.": ".$atri_option->itemattributeoption_prefix;
										$itemnotice = '';

										foreach($curr as $value)
										{
											if(property_exists($atri_option,$value))
											{
												if($atri_option->$value)
												{
													$opt_str.= $atri_option->$value." ".$value.", ";
												}
											}
											else
											{
												$invalid_op_price[$value]=$value;
												if(empty($itemnotice))
												{
													$noticeicon="<i class='" . Q2C_TOOLBAR_ICON_HOME . "'></i> ";
												}
											}
										}
										echo $detail_str=$noticeicon.$opt_str;
									?>
									</div>
									<?php
								}
								?>
							</td>
							<?php
								$edit_link = $add_link.'&attr_id='.$attributes->itemattribute_id.'&edit=1';
								$del_link  = $del_link.'&attr_id='.$attributes->itemattribute_id;
								?>
							<td>
								<button
									class="btn btn-small btn-primary qtcAddAttributeLink"
									type="button"
									onclick="toggleAddAttributeListModal(<?php echo $attributes->itemattribute_id;?>);"
									data-target="#editAttributeListModal_<?php echo $attributes->itemattribute_id?>">
									<i class="<?php echo $qtc_icon_edit; ?>"></i>
								</button>
								<?php
									echo HTMLHelper::_(
										'bootstrap.renderModal',
										'editAttributeListModal_' . $attributes->itemattribute_id,
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
									class="btn  btn-small btn-danger"
									onclick="deleteAttribute('<?php echo $attributes->itemattribute_id;?>','<?php echo $item_id; ?>')">
									<i class="<?php echo Q2C_ICON_TRASH; ?>"></i>
								</button>
							</td>
						</tr>
						<?php
					}

					$count_tr = '';
				}
				else
				{
					$count_tr = 1;
					?>
					<tr id="empty_attr">
						<td colspan="3"> <?php echo Text::_( 'QTC_ADDATTRI_EMPTY_MSG' ); ?></td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;"></div>
	<?php
	if(count($invalid_op_price) > 0)
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
	?>
	<button class="btn btn-small btn-primary qtcAddAttributeLink" type="button" onclick="toggleAddAttributeListModal();" data-target="#addAttributeListModal">
		<?php echo Text::_('QTC_ADD_ATTRIB'); ?>
	</button>
	<?php
		echo HTMLHelper::_(
			'bootstrap.renderModal',
			'addAttributeListModal',
			array(
				'closeButton'=> false,
				'url'        => $add_link,
				'modalWidth' => '80',
				'bodyHeight' => '70',
				'width'      => '100px',
				'height'     => '800px',
				'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal">'. Text::_('COM_QUICK2CART_COMMON_CLOSE') .'</button>',
			)
		)
	?>
</div>
<div style="clear:both;"></div>
