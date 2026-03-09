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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
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
	};

	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
	});

	window.toggleField = function (id, task, field) {

		var f = document.adminForm,
			i = 0, cbx,
			cb = f[ id ];

		if (!cb) return false;

		while (true) {
			cbx = f[ 'cb' + i ];

			if (!cbx) break;

			cbx.checked = false;
			i++;
		}

		var inputField   = document.createElement('input');
		inputField.type  = 'hidden';
		inputField.name  = 'field';
		inputField.value = field;
		f.appendChild(inputField);

		cb.checked = true;
		f.boxchecked.value = 1;
		window.submitform(task);

		return false;
	};
</script>
<?php
	// If logged in user is not store owner then show him error message
	if (empty($this->storeList))
	{
	?>
		<div class="alert alert-error">
			<?php echo Text::_("COM_QUICK2CART_CREATE_ORDER_AUTHORIZATION_ERROR");?>
		</div>
	<?php
		return false;
	}

	$selectStore = array(array('id' => '0', 'title'=> Text::_('COM_QUICK2CART_COUPONFORM_STORE_SELECT')));
	$this->storeList = array_merge($selectStore, $this->storeList);
?>
<div id="filter-progress-bar" class="btn-toolprogress-bar">
	<div class="filter-search btn-group pull-left q2c-btn-wrapper">
		<label for="filter_search" class="element-invisible">
			<?php echo Text::_('JSEARCH_FILTER'); ?>
		</label>
		<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>"
				title="<?php echo Text::_('QTC_DOWNLOAD_SEARCH_PLACE'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo Text::_('JSEARCH_FILTER'); ?>"/>
	</div>
	<div class="qtc-btn-group pull-left q2c-btn-wrapper">
		<button class="btn btn-default qtc-hasTooltip" type="submit"
				title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
			<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
		<button class="btn btn-default qtc-hasTooltip" id="clear-search-button" type="button"
				title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>">
			<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
	</div>
	<div class="btn-group pull-right hidden-xm q2c-btn-wrapper">
		<label for="limit"
			   class="element-invisible">
			<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<div class="btn-group pull-right hidden-phone q2c-btn-wrapper">
		<?php
			echo HTMLHelper::_('select.genericlist', $this->storeList, "filter_store", 'class="input-medium" size="1" onchange="document.adminForm.submit();" name="filter_store"', "id", "title", $this->state->get('filter.store'));
		?>
	</div>
</div>
<div class="clearfix"></div>
<div id="no-more-tables">
<table class="table table-striped" id="promotionList">
	<?php
	if (!empty($this->items))
	{
	?>
	<thead>
	<tr>
		<?php if (isset($this->items[0]->ordering)): ?>
			<th width="1%" class="nowrap center hidden-xm">
				<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
			</th>
		<?php endif; ?>
		<th width="1%" class="hidden-xm">
			<input type="checkbox" name="checkall-toggle" value=""
				   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_PUBLISHED', 'a.`state`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_NAME', 'a.`name`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_DESCRIPTION', 'a.`description`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_STORE_NAME', 'a.`store_id`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_FROM_DATE', 'a.`from_date`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_EXP_DATE', 'a.`exp_date`', $listDirn, $listOrder); ?>
		</th>
		<th class='left'>
		<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_PROMOTIONS_ID', 'a.`id`', $listDirn, $listOrder); ?>
		</th>
	</tr>
	</thead>
	<tbody>
		<?php
		foreach ($this->items as $i => $item)
		{
		$ordering   = ($listOrder == 'a.ordering');
		$canCreate  = $user->authorise('core.create', 'com_quick2cart');
		$canEdit    = $user->authorise('core.edit', 'com_quick2cart');
		$canCheckin = $user->authorise('core.manage', 'com_quick2cart');
		$canChange  = $user->authorise('core.edit.state', 'com_quick2cart');
		?>
		<tr class="row<?php echo $i % 2; ?>">
			<?php if (isset($this->items[0]->ordering)) : ?>
				<td class="order nowrap center hidden-xm">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel    = '';

						if (!$saveOrder) :
							$disabledLabel    = Text::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>"
							  title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none" name="order[]" size="5"
							   value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
					<?php else : ?>
							<span class="sortable-handler inactive">
								<i class="icon-menu"></i>
							</span>
					<?php endif; ?>
				</td>
			<?php endif; ?>
			<td class="hidden-xm">
				<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_PUBLISHED');?>">
				<?php if ($canChange) : ?>
					<a class="btn btn-micro hasTooltip" href="javascript:void(0);" title="<?php echo ($item->state) ? Text::_('COM_QUICK2CART_UNPUBLISH_ITEM') : Text::_('COM_QUICK2CART_PUBLISH_ITEM');?>"
						onclick="document.adminForm.cb<?php echo $i; ?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ($item->state) ? 'promotions.unpublish' : 'promotions.publish';?>');">
							<img src="<?php echo Uri::root(true); ?>/components/com_quick2cart/assets/images/<?php echo ($item->state) ? 'publish.png' : 'unpublish.png';?>"/>
					</a>
				<?php endif; ?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_NAME');?>">
				<?php if ($canEdit) : ?>
				<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=promotion&layout=edit&id='.(int) $item->id); ?>"><?php echo $item->name; ?></a>
				<?php else: ?>
					<?php echo $item->name; ?>
				<?php endif; ?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_DESCRIPTION');?>">
				<?php echo $item->description; ?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_STORE_NAME');?>">
				<?php
					$storeName = '';

					if (!empty($item->store_id))
					{
						$storeName = $this->Quick2cartModelProducts->getStoreNmae($item->store_id);
					}

					echo (!empty($storeName))?$storeName:'---';
				?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_FROM_DATE');?>">
				<?php
				if ($item->from_date != "0000-00-00 00:00:00")
				{
					echo HTMLHelper::date($item->from_date , 'Y-m-d H:i:s', true);
				}
				else
				{
					echo "-";
				}
				?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_EXP_DATE');?>">
				<?php
				if ($item->exp_date != "0000-00-00 00:00:00")
				{
					echo HTMLHelper::date($item->exp_date , 'Y-m-d H:i:s', true);
				}
				else
				{
					echo "-";
				}
				?>
			</td>
			<td data-title="<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_ID');?>">
				<?php echo $item->id; ?>
			</td>
		</tr>
		<?php
		}
		?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php
	}
	?>
	</table>
	<?php
		if (empty($this->items))
		{
	?>
		<div class="alert alert-warning"><?php echo Text::_("COM_QUICK2CART_PROMOTION_NO_RECORDS_FOUND");?></div>
	<?php
		}
	?>

</div>
