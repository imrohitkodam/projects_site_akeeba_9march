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

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user       = Factory::getUser();
$userId	    = $user->id;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= $user->authorise('core.edit.state', 'com_quick2cart');
$saveOrder	= ($listOrder == 'a.ordering');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_quick2cart&task=zones.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'zoneList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>

<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table     = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order     = table.options[table.selectedIndex].value;

		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<?php
// Allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>
<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=zones'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row <?php echo Q2C_WRAPPER_CLASS; ?>">
		<div class="col-md-12">
			<div id="j-main-container">
				<!-- Help msg -->
				<div class="alert alert-info">
					<?php echo Text::_('COM_QUICK2CART_ZONE_SETUP_HELP'); ?>
				</div>
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
				<div class="clearfix"></div>
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
				{ ?>
					<table class="table table-striped" id="zoneList">
						<thead>
							<tr>
								<?php if (isset($this->items[0]->ordering)) { ?>
									<th width="1%" class="nowrap center hidden-phone">
										<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
									</th>
								<?php } ?>

								<th class="w-1 af-text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</th>
								<?php if (isset($this->items[0]->state)) { ?>
									<th width="5%" class="nowrap center">
										<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
									</th>
								<?php } ?>
								<th class='left' >
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ZONES_ZONE_NAME', 'a.name', $listDirn, $listOrder); ?>
								</th>
								<th class='left' width="20%">
									<?php echo Text::_('COM_QUICK2CART_ZONES_STORE'); ?>
								</th>
								<th class='center' width="10%">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ZONES_ZONE_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tfoot>
							<?php $colspan = (isset($this->items[0])) ? count(get_object_vars($this->items[0])) : 10; ?>
							<tr>
								<td colspan="<?php echo $colspan; ?>">
									<?php echo $this->pagination->getListFooter(); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							$canCreate	  = $user->authorise('core.create', 'com_quick2cart');
							$canEdit	    = $user->authorise('core.edit', 'com_quick2cart');
							$canCheckin	 = $user->authorise('core.manage', 'com_quick2cart');
							$canChange	  = $user->authorise('core.edit.state', 'com_quick2cart');

							foreach ($this->items as $i => $item)
							{
								$ordering   = ($listOrder == 'a.ordering'); ?>
								<tr class="row<?php echo $i % 2; ?>">
									<?php
									if (isset($this->items[0]->ordering)) { ?>
										<td class="order nowrap center hidden-phone">
											<?php
											if ($canChange)
											{
												$disabledLabel    = (!$saveOrder) ? Text::_('JORDERINGDISABLED') : '';
												$disableClassName = (!$saveOrder) ? 'inactive tip-top' : ''; ?>
												<span class="sortable-handler hasTooltip <?php echo $disableClassName; ?>" title="<?php echo $disabledLabel; ?>">
													<i class="icon-menu"></i>
												</span>
												<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
											<?php
											}
											else
											{?>
												<span class="sortable-handler inactive" >
													<i class="icon-menu"></i>
												</span>
											<?php
											} ?>
										</td>
									<?php
									} ?>
									<td class="center hidden-phone">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
									</td>
									<?php
									if (isset($this->items[0]->state)) { ?>
										<td class="center">
											<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'zones.', $canChange, 'cb'); ?>
										</td>
									<?php
									} ?>
									<td class="left">
										<?php if (isset($item->checked_out) && $item->checked_out) { ?>
											<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'zones.', $canCheckin); ?>
										<?php } ?>
										<?php if ($canEdit) { ?>
											<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=zone.edit&id=' . (int) $item->id); ?>">
												<?php echo $this->escape($item->name); ?>
											</a>
										<?php }
								else
								{ ?>
											<?php echo $this->escape($item->name); ?>
										<?php } ?>
									</td>
									<td class=''>
										<?php echo $item->title; ?>
									</td>
									<td class='center'>
										<?php echo $item->id; ?>
									</td>
								</tr>
							<?php
							} ?>
						</tbody>
					</table>
				<?php
				} ?>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>

