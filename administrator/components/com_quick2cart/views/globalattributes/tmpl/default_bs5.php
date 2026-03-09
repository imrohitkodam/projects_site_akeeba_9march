<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
$document = Factory::getDocument();
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');

$user      = Factory::getUser();
$userId    = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_quick2cart');
$saveOrder = $listOrder == 'a.`ordering`';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_quick2cart&task=globalattributes.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'attributeList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'globalattributes.delete')
		{
			var confirmdelete = confirm("<?php echo Text::_('COM_QUICK2CART_ATTRIBUTE_DELETE_POPUP');?>");

			if( confirmdelete == false )
			{
				return false;
			}
			else
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
		else
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=globalattributes'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<div class="alert alert-info">
			<?php echo Text::_('COM_QUICK2CART_GLOBAL_ATTRIBUTES_INFO');?>
		</div>
		<?php
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));

		if(empty($this->items))
		{
			?>
			<div class="clearfix"></div>
			<div class="alert alert-warning">
				<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
			</div>
			<?php
		}
		else
		{?>
			<div class="clearfix"></div>
			<table class="table table-striped" id="attributeList">
				<thead>
					<tr>
						<th class="w-1 af-text-center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th class='left'>
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_GLOBALATTRIBUTES_ATTRIBUTE_NAME', 'a.attribute_name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
					$canCreate  = $user->authorise('core.create', 'com_quick2cart');
					$canEdit    = $user->authorise('core.edit', 'com_quick2cart');
					$canCheckin = $user->authorise('core.manage', 'com_quick2cart');
					$canChange  = $user->authorise('core.edit.state', 'com_quick2cart');

					foreach ($this->items as $i => $item)
					{
						$ordering   = ($listOrder == 'a.ordering');
					?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="hidden-phone">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="center">
								<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'globalattributes.', $canChange, 'cb'); ?>
							</td>
							<td>
								<?php
								if (isset($item->checked_out) && $item->checked_out)
								{
									echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'globalattributes.', $canCheckin); ?>
								<?php
								}

								if ($canEdit)
								{ ?>
									<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=globalattribute.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->attribute_name); ?>
									</a>
								<?php
								}
								else
								{
									echo $this->escape($item->attribute_name);
								}?>
							</td>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php
					} ?>
				</tbody>
			</table>
			<?php
		}
		?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="globalattributes" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>


