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
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

$user      = Factory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task=='couponform.addNew')
		{
			Joomla.submitform(task);

			return true;
		}
		else if (task=='couponform.edit')
		{
			if (document.adminForm.boxchecked.value===0)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_NO_SELECTION_MSG");?>');

				return;
			}
			elseif (document.adminForm.boxchecked.value > 1)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_MAKE_ONE_SEL");?>');

				return;
			}

			Joomla.submitform(task);
		}
		else
		{
			if (document.adminForm.boxchecked.value==0)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_NO_SELECTION_MSG");?>');

				return false;
			}
			switch(task)
			{
				case 'coupons.publish':
					Joomla.submitform(task);
				break

				case 'coupons.unpublish':
					Joomla.submitform(task);
				break

				case 'coupons.delete':
					if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_COUPON'); ?>"))
					{
						Joomla.submitform(task);
					}
					else
					{
						return false;
					}
				break
			}
		}
	}
</script>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my-coupons">
	<form method="post" name="adminForm" id="adminForm" class="form-validate">
		<?php
		$active = 'my_coupons';
		ob_start();
		include($this->toolbar_view_path);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		?>

		<legend>
			<?php
			if (!empty($this->store_role_list))
			{
				$app       = Factory::getApplication();
				$default   = $app->getUserStateFromRequest('com_quick2cart' . '.current_store', 'current_store');
				$storehelp = new storeHelper();
				$index     = $storehelp->array_search2d($default, $this->store_role_list);

				if (is_numeric( $index))
				{
					$store_name = $this->store_role_list[$index]['title'];
				}

				echo Text::sprintf('QTC_MANAGE_STORE_COUPONS', $store_name) ;
			}
			else
			{
				echo Text::_('QTC_MANAGE_STORE_COUPONS');
			}
			?>
		</legend>

		<?php echo $this->toolbarHTML;?>
		<div class="clearfix"> </div>
		<hr class="hr-condensed" />
		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
		<div class="clearfix">&nbsp;</div>
		<div class="clearfix">&nbsp;</div>

		<?php
		if (empty($this->items))
		{?>
			<div class="clearfix">&nbsp;</div>
			<div class="alert alert-warning">
				<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php
		}
		else
		{?>
			<table class="table table-striped table-bordered" id="couponList">
				<thead>
					<tr>
						<th class="q2c_width_1 nowrap center">
							<input type="checkbox" name="checkall-toggle" value=""
							title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
							onclick="Joomla.checkAll(this);" />
						</th>
						<th class="">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_COUPON_LIST_NAME', 'name', $listDirn, $listOrder ); ?>
						</th>
						<th class="q2c_width_1 nowrap center">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_COUPON_LIST_PUB', 'published', $listDirn, $listOrder ); ?>
						</th>
						<th class="q2c_width_15 center hidden-xs">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_COUPON_LIST_COD', 'code', $listDirn, $listOrder ); ?>
						</th>
						<th class="q2c_width_15 center hidden-xs">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_COUPON_LIST_COUPONP_VAL', 'value', $listDirn, $listOrder ); ?>
						</th>
						<th class="q2c_width_10 center hidden-xs">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_COUPON_LIST_C_TYP', 'val_type', $listDirn, $listOrder ); ?>
						</th>
						<th class="q2c_width_30 hidden-xs">
							<?php echo Text::_('COM_QUICK2CART_COUPON_LIST_C_PROD'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 0;
					$canCreate  = $user->authorise('core.create', 'com_quick2cart');
					$canEdit    = $user->authorise('core.edit', 'com_quick2cart');
					$canCheckin = $user->authorise('core.manage', 'com_quick2cart');
					$canChange  = $user->authorise('core.edit.state', 'com_quick2cart');

					foreach ($this->items as $i => $item)
					{
						$ordering   = ($listOrder == 'a.ordering');
						?>
						<tr class="row<?php echo $i % 2;?>">
							<td class="q2c_width_1 nowrap center">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id);?>
							</td>
							<td class="">
								<a href="<?php echo 'index.php?option=com_quick2cart&task=couponform.edit&id=' . $item->id; ?>" title="<?php echo Text::_('COM_QUICK2CART_COUPON_EDIT_TOOLTIP');?>">
									<?php echo $item->name;?>
								</a>
							</td>
							<?php if (isset($this->items[0]->published)): ?>
								<td class="q2c_width_1 nowrap center">
									<a class=" "
										href="javascript:void(0);"
										title="<?php echo ( $item->published ) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ;?>"
										onclick="document.adminForm.cb<?php echo $i;?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ( $item->published ) ? 'coupons.unpublish' : 'coupons.publish';?>');">
											<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($item->published) ? 'publish.png' : 'unpublish.png';?>"/>
									</a>
								</td>
							<?php endif; ?>
							<td class="q2c_width_15 center hidden-xs small">
								<?php echo stripcslashes($item->code);?>
							</td>
							<td class="q2c_width_15 center hidden-xs small">
								<span class="small badge badge-info">
									<?php echo $item->value;?>
								</span>
							</td>
							<td class="q2c_width_10 center hidden-xs small">
								<?php
								if ($item->val_type==1)
								{
									echo Text::_("COM_QUICK2CART_COUPON_PER");
								}
								else
								{
									echo Text::_("COM_QUICK2CART_COUPON_LIST_C_FLAT");
								}
								?>
							</td>
							<td class="q2c_width_30 hidden-xs small">
								<?php
								if (isset($item->item_id_name))
								{
									echo $item->item_id_name;
								}
								else
								{
									echo '-';
								}
								?>
							</td>
						</tr>
						<?php
						$i++;
					}
					?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
			<?php 
		} ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="coupons" />
		<input type="hidden" name="layout" value="my" />
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
