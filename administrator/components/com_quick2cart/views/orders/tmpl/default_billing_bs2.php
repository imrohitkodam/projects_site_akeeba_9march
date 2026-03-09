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
use Joomla\CMS\Language\Text;

if(in_array('billing', $order_blocks))
{
	?>
	<div class="">
		<?php 
		if (!empty($shipinfo) || !empty($billinfo))
		{
		?>
			<h4 <?php echo ($orders_email) ? $emailstyle : '' ; ?>>
				<?php echo Text::_('QTC_CUST_INFO'); ?>
			</h4>
			<div class="table-responsive" id='no-more-tables' style="margin:10px 0 0px 0 !important;">
				<table class="table table-condensed table-bordered qtc-table" style="<?php echo $this->email_table_bordered; ?>">
					<thead>
						<tr>
							<th align="left">
								<?php echo Text::_('QTC_BILLIN_INFO'); ?>
							</th>
							<?php
							if ($this->params->get('shipping') == '1' && isset($shipinfo) && in_array('shipping', $order_blocks))
							{
							?>
								<th align="left">
									<?php echo Text::_('QTC_SHIPIN_INFO'); ?>
								</th>
							<?php
							}
							?>
						</tr>
					</thead>
					<tbody>
						<?php $emailTdStyle = ($orders_email) ? "" : '' ;?>
						<tr style="width: 100%; ">
							<?php
							if (!empty($billinfo))
							{
							?>
								<td data-title="<?php echo Text::_('QTC_BILLIN_INFO'); ?>" style="<?php echo $emailTdStyle; ?>" class="qtcWordWrap">
									<address>
										<strong>
											<?php
											echo htmlspecialchars($billinfo->firstname, ENT_COMPAT, 'UTF-8') . ' ';

											if ($billinfo->middlename)
											{
												echo htmlspecialchars($billinfo->middlename, ENT_COMPAT, 'UTF-8') . '&nbsp;';
											}

											echo htmlspecialchars($billinfo->lastname, ENT_COMPAT, 'UTF-8');
											?> &nbsp;&nbsp;
										</strong>
										<br />
											<?php echo htmlspecialchars($billinfo->address, ENT_COMPAT, 'UTF-8') . ","; ?>
										<br/>

										<?php
											if (!empty($billinfo->land_mark))
											{
												echo htmlspecialchars($billinfo->land_mark, ENT_COMPAT, 'UTF-8') . ', ';
											}

											echo htmlspecialchars($billinfo->city, ENT_COMPAT, 'UTF-8') . ', ' ;
											echo (!empty($billinfo->state_name) ? $billinfo->state_name : $billinfo->state_code) . ' ' . htmlspecialchars($billinfo->zipcode, ENT_COMPAT, 'UTF-8');
											echo '<br/>';
											echo (!empty($billinfo->country_name) ? $billinfo->country_name : $billinfo->country_code) . ', ';
										?>
										<br/><?php echo htmlspecialchars($billinfo->user_email, ENT_COMPAT, 'UTF-8'); ?><br/>
										<abbr title="<?php echo Text::_('QTC_BILLIN_PHON'); ?>"><?php echo Text::_('QTC_BILLIN_PHON'); ?> :</abbr> <?php	echo htmlspecialchars($billinfo->phone, ENT_COMPAT, 'UTF-8'); ?>
									</address>
								</td>
							<?php
							}

							if ($this->params->get('shipping') == '1' && isset($shipinfo) && in_array('shipping', $order_blocks))
							{
								?>
								<td data-title="<?php echo Text::_('QTC_SHIPIN_INFO');?>"  style="<?php echo $emailTdStyle; ?>" class="qtcWordWrap">
									<address>
										<strong>
											<?php echo htmlspecialchars($shipinfo->firstname, ENT_COMPAT, 'UTF-8') . ' ';
												if ($shipinfo->middlename)
												{
													echo htmlspecialchars($shipinfo->middlename, ENT_COMPAT, 'UTF-8') . '&nbsp;';
												}
											echo htmlspecialchars($shipinfo->lastname, ENT_COMPAT, 'UTF-8');
											?> &nbsp;&nbsp;
										</strong><br /><?php echo htmlspecialchars($shipinfo->address, ENT_COMPAT, 'UTF-8') . ","; ?><br/>
											<?php
											if (!empty($billinfo->land_mark))
											{
												echo htmlspecialchars($billinfo->land_mark, ENT_COMPAT, 'UTF-8') . ", ";
											}
											echo htmlspecialchars($shipinfo->city, ENT_COMPAT, 'UTF-8') . ', ' ;
											echo (!empty($shipinfo->state_name) ? $shipinfo->state_name : $shipinfo->state_code) . ' ' . $shipinfo->zipcode;
											echo '<br/>';
											echo (!empty($shipinfo->country_name) ? $shipinfo->country_name : $shipinfo->country_code) . ', ';
											?>
										<br/><?php echo htmlspecialchars($shipinfo->user_email, ENT_COMPAT, 'UTF-8'); ?><br/>
										<abbr title="<?php echo Text::_('QTC_BILLIN_PHON'); ?>"><?php echo Text::_('QTC_BILLIN_PHON'); ?>:</abbr> <?php echo htmlspecialchars($shipinfo->phone, ENT_COMPAT, 'UTF-8'); ?>
									</address>
								</td>
							<?php
							}
							?>
						</tr>
					</tbody>
				</table>
			</div>
		<?php
		}
		?>
	</div>
	<?php
}
?>
