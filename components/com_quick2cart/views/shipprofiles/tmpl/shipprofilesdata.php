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

$isShippingEnabled = $this->params->get('shipping', 0);
$user              = Factory::getUser();
$app               = Factory::getApplication();
$listOrder         = $this->state->get('list.ordering');
$listDirn          = $this->state->get('list.direction');
$saveOrder         = ($listOrder == 'a.ordering');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form action="" method="post" name="adminForm" id="adminForm">
		<!-- Toolbar -->
		<?php
		$isadmin = $app->isClient('administrator');
		if (!$isadmin)
		{
		?>
			<div class="row">
				<div class="span12">
					<?php
					$active = 'taxprofiles';
					ob_start();
					include($this->toolbar_view_path);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
					?>
				</div>
			</div>
		<?php
		}?>

		<div class="row">
			<?php
			if (!$isadmin)
			{
			?>
				<legend><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_S_MANAGE_LIST_LEGEND'); ?></legend>
			<?php
			}?>

			<?php
			// Shipping is diabled msg
			if ($isShippingEnabled == 0)
			{
				?>
				<div class="alert alert-danger">
					<?php echo Text::_('COM_QUICK2CART_U_HV_DISABLED_SHIPPING_OPTION_HELP_MSG'); ?>
				</div>
				<?php

				return false;
			}
			?>
			<!-- Help msg -->
			<div class="alert alert-info">
				<?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_SETUP_HELP'); ?>
			</div>

			<!-- Toolbar buttons -->
			<?php
			if (!$isadmin)
			{
				echo $this->toolbarHTML;
			}?>

			<div class="clearfix"> </div>
			<hr class="hr-condensed" />
		</div>

		<div id="qtc-filter-bar" class="qtc-btn-toolbar">
			<div class="filter-search btn-group pull-left q2c-btn-wrapper">
				<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_SEARCH_FILTER'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="qtc-hasTooltip"
				title="<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_SEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group pull-left q2c-btn-wrapper">
				<button type="submit" class="btn qtc-hasTooltip" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
				</button>
				<button type="button" class="btn qtc-hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
				onclick="document.getElementById('filter_search').value='';this.form.submit();">
					<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
				</button>
			</div>
			<div class="qtc-btn-group pull-right q2c-btn-wrapper">
				<label for="limit" class="element-invisible">
					<?php echo Text::_('COM_QUICK2CART_SEARCH_SEARCHLIMIT_DESC'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="qtc-btn-group pull-right hidden-xs q2c-btn-wrapper">
				<?php echo HTMLHelper::_('select.genericlist', $this->publish_states, "filter_published", 'class="input-medium"  onchange="document.adminForm.submit();" name="filter_published"', "value", "text", $this->state->get('filter.state'));
				?>
			</div>
			<div class="qtc-btn-group pull-right hidden-xs q2c-btn-wrapper">
				<?php echo HTMLHelper::_('select.genericlist', $this->stores, "filter_store", 'class="input-medium"  onchange="document.adminForm.submit();" name="filter_store"', "id", "title", $this->state->get('filter.stores'));
				?>
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
			<table class="table table-striped table-bordered" id="shippingProfilesList">
				<thead>
					<tr>
						<th class="q2c_width_1">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th>
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_SHIPPROFILE_S_SHIPPROFILE_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo Text::_('COM_QUICK2CART_STORE_NAME'); ?>
						</th>
						<?php if (isset($this->items[0]->state)): ?>
							<th class="hidden-xs nowrap center q2c_width_20">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
					</tr>
				</thead>

				<tbody>
					<?php
					$canCreate	= $user->authorise('core.create', 'com_quick2cart');
					$canEdit	= $user->authorise('core.edit.own', 'com_quick2cart');
					$canCheckin	= $user->authorise('core.manage', 'com_quick2cart');
					$canChange	= $user->authorise('core.edit.state', 'com_quick2cart');

					foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="q2c_width_1">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td>
								<?php
									if (isset($item->checked_out) && $item->checked_out)
									{
									    echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'shipprofiles.', $canCheckin);
									}

									if ($canEdit)
									{
									?>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=' . $actionViewName . '.edit&id=' . (int) $item->id); ?>">
											<?php echo $this->escape($item->name); ?>
										</a>
									<?php
									}
									else
									{
										echo $this->escape($item->name);
									}
								?>
							</td>
							<td><?php echo $item->store_title; ?></td>

							<?php
							if (isset($this->items[0]->state)): ?>
								<td class="hidden-xs center q2c_width_10">
									<a class=" "
										href="javascript:void(0);"
										title="<?php echo ( $item->state ) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ;?>"
										onclick="document.<?php echo $formName; ?>.cb<?php echo $i;?>.checked=1; document.<?php echo $formName; ?>.boxchecked.value=1; Joomla.submitbutton('<?php echo ($item->state) ? $actionControllerName . '.unpublish' : $actionControllerName . '.publish';?>');">
										<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($item->state) ? 'publish.png' : 'unpublish.png';?>"/>
									</a>
								</td>
								<?php
							endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="shipprofiles" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
