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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$user      = Factory::getUser();
$userId    = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_quick2cart');
$saveOrder = ($listOrder == 'a.ordering');
?>

<?php
// Joomla Component Creator code to allow adding non select list filters
if (! empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'payouts.csvexport')
		{
			url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=payouts.csvexport&tmpl=component&"+Joomla.getOptions('csrf.token')+"=1";
			window.location.href = url;
		}
		else if (task == 'payouts.delete')
		{
			var r=confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_PAYOUTS');?>");

			if (r===false)
			{
				return;
			}

			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=payouts'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-payouts">
		<div id="j-main-container">
			<div class="alert alert-info">
				<?php echo Text::sprintf("COM_QUICK2CART_OLD_PAYOUTS_NOTE", Uri::base() . 'index.php?option=com_tjvendors&view=vendors&client=com_quick2cart');?>
			</div>
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
			<?php
			if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
				<table class="table table-striped" id="payoutList">
					<thead>
						<tr>
							<th class="q2c_width_1">
								<input type="checkbox" name="checkall-toggle" value=""
								title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
								onclick="Joomla.checkAll(this)" />
							</th>
							<?php if (isset($this->items[0]->com_quick2cart)): ?>
								<th width="1%" class="nowrap center">
									<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
								</th>
							<?php
							endif;
							?>
							<th class='q2c_width_10'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYOUT_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_10">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYEE_NAME', 'a.payee_name', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_10 hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYPAL_EMAIL', 'a.email_id', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_10 hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_TRANSACTION_ID', 'a.transaction_id', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_10 hidden-phone'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYOUT_DATE', 'a.date', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_5 hidden-phone'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STATUS', 'a.status', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_5'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CASHBACK_AMOUNT', 'a.amount', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 0;

						foreach ($this->items as $payout)
						{
						?>
							<tr class="row<?php echo $i % 2;?>">
								<td align="center"><?php echo HTMLHelper::_('grid.id', $i, $payout->id);?></td>
								<td class=''>
									<?php
									$original_payout_id = $payout->id;

									if (strlen($payout->id) <= 6)
									{
										$append = '';

										for ($z = 0; $z < (6 - strlen($payout->id)); $z++)
										{
											$append .= '0';
										}

										$payout->id = $append . $payout->id;
									}?>
									<a href="<?php echo 'index.php?option=com_quick2cart&task=payout.edit&id=' . $original_payout_id; ?>"
									title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_ID_TOOLTIP');?>">
										<?php echo htmlspecialchars($payout->id, ENT_COMPAT, 'UTF-8');
									?>
									</a>
								</td>
								<td class='q2c_width_10 small'><?php echo htmlspecialchars($payout->payee_name, ENT_COMPAT, 'UTF-8');?></td>
								<td class='q2c_width_10 small hidden-phone'><?php echo htmlspecialchars($payout->email_id, ENT_COMPAT, 'UTF-8');?></td>
								<td class='q2c_width_10 small hidden-phone'><?php echo htmlspecialchars($payout->transaction_id, ENT_COMPAT, 'UTF-8');?></td>
								<td class='q2c_width_10 small hidden-phone'>
									<?php echo HTMLHelper::_('date', $payout->date, "Y-m-d");?>
								</td>
								<td class='q2c_width_5 small hidden-phone'>
									<?php
									if ($payout->status == 1)
									{
										echo Text::_('COM_QUICK2CART_PAID');
									}
									else
									{
										echo Text::_('COM_QUICK2CART_NOT_PAID');
									}
									?>
								</td>
								<td class='q2c_width_5'><?php echo $payout->amount; ?></td>
							</tr>
						<?php
							$i++;
						}
						?>
					</tbody>
				</table>
			<?php echo $this->pagination->getListFooter();
			endif;
			?>
			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" name="view" value="payouts" />
			<input type="hidden" name="layout" value="default" />
			<input type="hidden" id="task" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</div>
</form>
