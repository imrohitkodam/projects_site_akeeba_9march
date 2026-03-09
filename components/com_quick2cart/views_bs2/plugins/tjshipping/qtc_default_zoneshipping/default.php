<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$qtczoneShipHelper   = new qtczoneShipHelper;
$comquick2cartHelper = new comquick2cartHelper();
$taxHelper           = new taxHelper;

$app          = Factory::getApplication();
$jinput       = $app->input;
$extension_id = $jinput->get('extension_id');
$itemid       = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
?>
<script type="text/javascript">
	function qtcShipSubmitAction(action)
	{
		var form = document.qtcshipform;

		if (action=='publish' || action=='unpublish' || action=='delete')
		{
			if (document.qtcshipform.boxchecked.value==0)
			{
				alert("<?php echo Text::_('QTC_MAKE_SEL');?>");
				return;
			}

			switch(action)
			{
				case 'publish': form.plugtask.value='publish';
				break

				case 'unpublish': form.plugtask.value='unpublish';
				break

				case 'delete':
					var r = confirm("<?php echo Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING_DELETE_CONFIRM_SHIP_METH");?>");
					if (r==true)
					{
						var aa;
						form.plugtask.value='delete';
					}
					else
					{
						return false;
					}

				break
			}
		}
		else if (action == "add" )
		{
			switch(action)
			{
				case 'add':
					form.plugview.value='createshipmeth';
					form.plugtask.value='newshipmeth';
				break
			}
		}
		else
		{
			window.location = '';
		}

		// Submit form
		form.submit();

		return;
	}

	// When user clicked on publish/unpublish icon
	function qtcChangeShipMethodState(state,methodId)
	{
		var form = document.qtcshipform;
		switch(state)
		{
			case 'publish': form.plugtask.value='publish';
			break

			case 'unpublish': form.plugtask.value='unpublish';
			break
		}

		form.shipMethId.value = methodId;
		form.submit();
	}
</script>

<form name="qtcshipform" method="post" id="adminForm" enctype="multipart/form-data">
	<h1 id="qtc_shipmethodInfo" ><strong><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SEL_SHIPMETHOLIST')?>&nbsp;</strong></h1>
	<div class="alert alert-info">
		<p><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING_HELP'); ?></p>
		<hr class="hr hr-condensed"/>
		<p><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING_SETRATES_HELP'); ?></p>
	</div>

	<!-- Add action buttons-->
	<div class="qtc_action_button pull-right">
		<button type="button" class="btn btn-success btn-sm" title="<?php echo Text::_('COM_QUICK2CART_GENERAL_S_NEW'); ?>" onclick="qtcShipSubmitAction('add');">
			<i class="<?php echo QTC_ICON_PLUS; ?> <?php echo Q2C_ICON_WHITECOLOR; ?> "></i>
			<span class="hidden-xs">
				<?php echo Text::_('COM_QUICK2CART_GENERAL_S_NEW'); ?>
			</span>
		</button>
		<button type="button" class="btn btn-success btn-sm hidden-xs" title="<?php echo Text::_('COM_QUICK2CART_S_PUBLISHED'); ?>" onclick="qtcShipSubmitAction('publish');">
			<i class="<?php echo QTC_ICON_CHECKMARK;?> <?php echo Q2C_ICON_WHITECOLOR; ?>"> </i>
			<?php echo " " . Text::_('COM_QUICK2CART_S_PUBLISHED'); ?>
		</button>
		<button type="button" class="btn btn-warning btn-sm hidden-xs" title="<?php echo Text::_('COM_QUICK2CART_S_JUNPUBLISHED'); ?>" onclick="qtcShipSubmitAction('unpublish');">
			<i class="<?php echo QTC_ICON_PUBLISH;?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
			<?php echo " " . Text::_('COM_QUICK2CART_S_JUNPUBLISHED'); ?>
		</button>
		<button type="button" class="btn btn-danger btn-sm" title="<?php echo Text::_('COM_QUICK2CART_GENERAL_S_DELETE'); ?> " onclick="return qtcShipSubmitAction('delete');">
			<i class="<?php echo Q2C_ICON_TRASH; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
			<span class="hidden-xs">
				<?php echo Text::_('COM_QUICK2CART_GENERAL_DELETE'); ?>
			</span>
		</button>
	</div>
	<table class="table table-striped table-bordered" id="taxProfilesList">
		<thead>
			<tr >
				<th width="1%">
					<input type="checkbox" name="checkall-toggle"
					value="" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_CHECK_ALL'); ?>"
					onclick="Joomla.checkAll(this)" />
				</th>
				<th width="40%" class="title"><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPM_NAME'); ?></th>
				<th class="center" width="10%"><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPM_STATE'); ?></th>
				<th width="30%"><?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_TAX_PROFILE_NAME'); ?></th>
				<th width="18%" class="title"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20">&nbsp;</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$i = 0; $k=0;
			$items =  !empty($shipFormData) ? $shipFormData: array();

			foreach ($items as $item)
			{
				$checked = HTMLHelper::_('grid.id', $i, $item['methodId'] );
			?>
			<tr class='row<?php echo $k; ?>'>
				<td style="">
					<?php echo $checked; ?>
				</td>
				<td style="text-align: left;">
					<?php
					$methlink = "index.php?option=com_quick2cart&view=shipping&layout=list&plugview=createshipmeth&extension_id=" . $extension_id . "&Itemid=" . $itemid . "&methodId=" . $item['methodId'];
					?>
					<a 	href="<?php echo Route::_($methlink); ?>" title="<?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_CLICK_TO_VIEWDETAIL'); ?>">
						<?php echo htmlspecialchars($item['name'], ENT_COMPAT, 'UTF-8');?>
					</a>
					<div class="shipping_rates">
						<b><?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPM_TYPE'); ?></b>:
						<?php echo $qtczoneShipHelper->getShipTypeName($item['shipping_type']); ?>
						<br>
						<b><?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_MIN_SUBTOATL_REQ'); ?></b>:
						<?php echo number_format($item['min_value'],2); ?>
						<br/>
						<b><?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_MAX_SUBTOATL_REQ'); ?></b>:
						<?php echo number_format($item['max_value'],2); ?>
					</div>
				</td>
				<td class="center" >
					<?php
					if (!empty($item['state']))
					{
						$qtc_icon_state = QTC_ICON_CHECKMARK; // ok mark
						$qtcCopState = Text::_('QTC_PUBLISH');
						$qtcstate = 'unpublish'; // toggle
						$color = "btn-success";
						$title = Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING_CLICKTO_UNPUBLISH");
					}
					else{
						$qtc_icon_state = QTC_ICON_REMOVE; // cross mark
						$qtcCopState = Text::_('QTC_UNPUBLISH');
						$qtcstate = 'publish';// toggle
						$color = "btn-danger";
						$title = Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING_CLICKTO_PUBLISH");
					}
					?>
					<a class=" "
						href="javascript:void(0);"
						title="<?php echo $title;?>" onclick=" qtcChangeShipMethodState('<?php echo $qtcstate;?>',<?php echo $item['methodId']; ?>)">
							<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($item['state']) ? 'publish.png' : 'unpublish.png';?>"/>
					</a>
				</td>
				<td style="">
					<?php
					if (!empty($item['taxprofileId']))
					{
						$taxprofileDetails = $taxHelper->getTaxprofileDetail($item['taxprofileId']);
						echo $taxprofileDetails['name'] . '<br/>' . '[' .Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPPING__STPRE_NAME') . ' : ' . $taxprofileDetails['title']  . ']';
					}
					else
					{
						echo '-';
					}
					 ?>
				</td>
				<td>
					<?php
					$setRateLink = "index.php?option=com_quick2cart&view=shipping&layout=list&plugview=setrates&extension_id=" . $extension_id . "&Itemid=" . $itemid . "&methodId=" . $item['methodId'] . '';
					?>
					<a class="btn btn-primary btn-sm" href="<?php echo Route::_($setRateLink); ?>" title="<?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_CLICK_TO_SETRATE_TITLE'); ?>" >
						<span class="">
							<i class="<?php echo Q2C_TOOLBAR_ICON_SETTINGS;?>"></i>
							<?php  echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_CLICK_TO_SETRATE'); ?>
						</span>
					</a>
				</td>
			</tr>
			<?php $i++; $k = (1 - $k); ?>
			<?php
			} ?>

			<?php if (!count($items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<div class="alert alert-warning">
						<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_NO_ITEMS_FOUND'); ?>
					</div>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<!-- Component related things -->
<!--
	<input type="hidden" name="option" value="com_quick2cart" />
-->
	<input type="hidden" name="com_quick2cart" value="shipping" />
	<input type="hidden" name="task" value="shipping.getShipView" />
	<input type="hidden" name="view" value="shipping" />

	<!-- plugin related things -->
	<input type="hidden" name="plugview" value="default" />
	<input type="hidden" name="shipMethId" value="" />
	<input type="hidden" name="plugtask" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
