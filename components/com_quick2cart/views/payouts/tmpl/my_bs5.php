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
use Joomla\CMS\Layout\LayoutHelper;

$user   = Factory::getUser();
$userId = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'a.ordering');
Factory::getDocument()->addScriptDeclaration('
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery(".q2c-wrapper #limit").removeAttr("size");
	});
');
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form action="" method="post" name="adminForm" id="adminForm">
		<?php
		$active = 'payouts';
		ob_start();
		include($this->toolbar_view_path);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		?>
		<h1><strong><?php echo Text::_('COM_QUICK2CART_MY_CASHBACK')?></strong></h1>

		<div id="filter-bar" class="js-stools">
			<div class="js-stools-container-selector btn-group float-start filter-search">
				<input
				type="text"
				name="filter_search"
				id="filter_search"
				placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_MY_PAYOUTS'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="form-control hasTooltip"
				title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_MY_PAYOUTS'); ?>" />
				<button type="submit" class="btn btn-primary hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="fa fa-search"></i>
				</button>
				<button type="button" class="btn btn-secondary hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
				onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<i class="fa fa-remove"></i>
				</button>
			</div>

			<div class="btn-group float-end q2c-btn-wrapper">
				<div class="qtc-btn-group float-end">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			</div>
		</div>

		<div class="clearfix"></div>

		<?php if (empty($this->items)) : ?>
			<div class="clearfix">&nbsp;</div>
			<div class="alert alert-warning">
				<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php
		else : ?>
			<div id='no-more-tables'>
				<table class="table table-striped table-bordered table-responsive" id="payoutList">
					<thead>
						<tr>
							<th class=''>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYOUT_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_20 center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_TRANSACTION_ID', 'a.transaction_id', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_15 center'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYOUT_DATE', 'a.date', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_30 center'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PAYMENT_STATUS', 'a.status', $listDirn, $listOrder); ?>
							</th>
							<th class='q2c_width_15'>
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ORDER_AMOUNT', 'a.amount', $listDirn, $listOrder); ?>
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
								<td data-title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_ID');?>">
									<?php
									$original_payout_id = $payout->id;

									if (strlen($payout->id) <= 6)
									{
										$append = '';

										for ($z=0; $z < (6-strlen($payout->id)); $z++)
										{
											$append .= '0';
										}

										$payout->id = $append . $payout->id;
									}

									echo $payout->id;
									?>
								</td>
								<td class="q2c_width_20 center" data-title="<?php echo Text::_('COM_QUICK2CART_TRANSACTION_ID');?>">
									<?php echo $payout->transaction_id;?>
								</td>
								<td class="q2c_width_15 center small" data-title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_DATE');?>">
									<?php echo Factory::getDate($payout->date)->Format(Text::_('COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT'));?>
								</td>
								<td class="q2c_width_30 center small" data-title="<?php echo Text::_('COM_QUICK2CART_PAYMENT_STATUS');?>">
									<?php
									$status_text = ($payout->status == 1) ? Text::_('COM_QUICK2CART_PAID') : Text::_('COM_QUICK2CART_NOT_PAID');
									$badge_class = ($payout->status == 1) ? "success" : "warning";
									?>
									<span class="small badge badge-<?php echo $badge_class;?>">
										<?php echo $status_text; ?>
									</span>
								</td>
								<td class="q2c_width_15" data-title="<?php echo Text::_('COM_QUICK2CART_ORDER_AMOUNT');?>" class=''>
									<?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($payout->amount, 2)); ?>
								</td>
							</tr>
						<?php
						$i++;
						}
						?>

						<!-- PAID AMOUNT-->
						<tr>
							<td colspan="3"></td>
							<td class="">
								<strong><?php echo Text::_('COM_QUICK2CART_PAID_OUT'); ?></strong>
							</td>
							<td>
								<strong> <?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->totalpaidamount, 2)); ?> </strong>
							</td>
						</tr>

						<!-- TO BE PAY-->
						<tr>
							<td colspan="3"></td>
							<td class="">
								<strong><?php echo Text::_( 'COM_QUICK2CART_CASHBACK'); ?></strong>
							</td>
							<td>
								<strong> <?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->totalAmount2BPaidOut, 2)); ?> </strong>
							</td>
						</tr>

						<!-- COMMISSION AMOUNT-->
						<tr>
							<td colspan="3"></td>
							<td class="">
								<strong><?php echo Text::_('COM_QUICK2CART_PAYOUT_COMMISSION'); ?></strong>
							</td>
							<td>
								<strong> <?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->commission_cut, 2));?> </strong>
							</td>
						</tr>
						<tr>
							<td colspan="3"></td>
							<td class="">
								<strong><?php echo Text::_( 'COM_QUICK2CART_BALANCE'); ?></strong>
							</td>
							<td>
								<strong>
									<?php
									$balanceamt = number_format($this->balanceamt1, 2, '.', '');
									if ($balanceamt=='-0.00')
									{
										echo $this->comquick2cartHelper->getFromattedPrice('0.00');
									}
									else
									{
										echo $this->comquick2cartHelper->getFromattedPrice(number_format($this->balanceamt1,2));
									}
									?>
								</strong>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="payouts" />
		<input type="hidden" name="layout" value="my" />
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
