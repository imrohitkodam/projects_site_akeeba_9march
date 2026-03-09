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
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$user      = Factory::getUser();
$userId	   = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_quick2cart');
$saveOrder = ($listOrder == 'a.ordering');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_quick2cart&task=taxprofiles.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'taxprofileList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();

// Allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=taxprofiles'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<?php
		if(!empty($this->sidebar))
		{
			?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
			<?php
		}
		else
		{
			?>
			<div id="j-main-container">
			<?php 
		}

			// Taxation is diabled msg
			if ($this->isTaxationEnabled == 0)
			{
				?>
				<div class="alert alert-error">
					<?php echo Text::_('COM_QUICK2CART_U_HV_DISABLED_TAXATION_OPTION_HELP_MSG'); ?>
				</div>
				<?php

				return false;
			}
			?>
			<!-- Help msg -->
			<div class="alert alert-info">
				<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_SETUP_HELP'); ?>
			</div>
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
			<div class="clearfix"> </div>
			<?php
			if (empty($this->items))
			{ ?>
				<div class="clearfix">&nbsp;</div>
					<div class="alert alert-warning">
						<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php
			}
			else
			{ ?>
				<table class="table table-striped" id="taxprofileList">
					<thead>
						<tr>
							<th width="1%" class=" center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
							</th>
							<th width="1%" class="hidden-phone">
								<?php echo HTMLHelper::_('grid.checkall');?>
							</th>
							<th width="1%" class=" center">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
							<th class='left'>
								<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_TAXPROFILES_TAXPROFILE_NAME', 'a.name', $listDirn, $listOrder); ?>
							</th>
							<th width="20%" class="">
								<?php echo Text::_('COM_QUICK2CART_TAXPROFILES_STORE_NAME'); ?>
							</th>
							<th width="1%" class=" center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$canCreate	= $user->authorise('core.create',		'com_quick2cart');
						$canEdit	= $user->authorise('core.edit',			'com_quick2cart');
						$canCheckin	= $user->authorise('core.manage',		'com_quick2cart');
						$canChange	= $user->authorise('core.edit.state',	'com_quick2cart');

						foreach ($this->items as $i => $item) :
							$ordering   = ($listOrder == 'a.ordering');
						?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="order nowrap center hidden-phone">
									<?php
									if ($canChange)
									{
										$disabledLabel    = (!$saveOrder) ? Text::_('JORDERINGDISABLED') : '';
										$disableClassName = (!$saveOrder) ? 'inactive tip-top' : '';
										?>
										<span
											class="sortable-handler hasTooltip <?php echo $disableClassName?>"
											title="<?php echo $disabledLabel?>">
											<i class="icon-menu"></i>
										</span>
										<input
											type="text"
											style="display:none"
											name="order[]" size="5"
											value="<?php echo $item->ordering;?>"
											class="width-20 text-area-order " />
										<?php
									}
									else
									{
										?>
										<span class="sortable-handler inactive"><i class="icon-menu"></i></span>
										<?php
									} ?>
								</td>
								<td class="center hidden-phone"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
								<td class="center"><?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'taxprofiles.', $canChange, 'cb'); ?></td>
								<td>
									<?php if (isset($item->checked_out) && $item->checked_out) : ?>
										<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'taxprofiles.', $canCheckin); ?>
									<?php endif; ?>
									<?php if ($canEdit) : ?>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=taxprofile.edit&id='.(int) $item->id); ?>">
										<?php echo $this->escape($item->name); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->name); ?>
									<?php endif; ?>
								</td>
								<td class=""><?php echo  $item->storeName; ?></td>
								<td class="center hidden-phone"><?php echo (int) $item->id; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php 
			} ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</div>
</form>
