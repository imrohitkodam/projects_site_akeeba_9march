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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

$user              = Factory::getUser();
$listOrder         = $this->state->get('list.ordering');
$listDirn          = $this->state->get('list.direction');
$saveOrder         = ($listOrder == 'a.ordering');
$isTaxationEnabled = $this->params->get('enableTaxtion', 0);

Factory::getDocument()->addScriptDeclaration('
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery(".q2c-wrapper #limit").removeAttr("size");
	});
');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task =='taxrates.add' || task =='taxrates.backToDashboard')
		{
			Joomla.submitform(task);

			return true;
		}
		else
		{
			if (document.adminForm.boxchecked.value==0)
			{
				alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_MESSAGE_SELECT_ITEMS')); ?>");
				return false;
			}

			switch(task)
			{
				case 'taxrates.publish':
					Joomla.submitform(task);
				break

				case 'taxrates.unpublish':
					Joomla.submitform(task);
				break

				case 'taxrates.delete':
					if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_TEXRATE'); ?>"))
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

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> q2c-taxrates container-fluid">
	<form action="" method="post" name="adminForm" id="adminForm">
		<!-- Toolbar -->
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php
				$active = 'taxrates';
				ob_start();
				include($this->toolbar_view_path);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
				?>
			</div>
		</div>
		<div class="row">
			<h1>
				<strong>
					<?php echo Text::_('COM_QUICK2CART_SETUP_TAXRATES'); ?>
				</strong>
			</h1>

			<!-- Help msg -->
			<div class="alert alert-info"><?php echo Text::_('COM_QUICK2CART_TAXRATES_SETUP_HELP'); ?></div>

			<?php
			// Taxation is diabled msg
			if ($isTaxationEnabled == 0)
			{
				?>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_U_HV_DISABLED_TAXATION_OPTION_HELP_MSG'); ?>
				</div>
				<?php
			}
			?>

			<!-- Toolbar buttons -->
			<?php echo $this->toolbarHTML;?>
			<div class="clearfix"></div>
			<hr class="hr-condensed" />
		</div>
		<div class="col-md-12">
			<div class="clearfix"></div>
			<div class="row">
				<div class="col-md-12">
					<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
				</div>
			</div>
		</div>
		<div class="clearfix"> </div>

		<?php if (empty($this->items)) : ?>
			<div class="clearfix">&nbsp;</div>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
				</div>
		<?php
		else : ?>
			<table class="table table-hover border table-bordered table-striped mt-4" id="taxrateList">
				<thead class="table-primary table-bordered border">
					<tr>
						<th class="nowrap q2c_width_1 center">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="fw-bold">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_TAXTRATES_S_TAXRATE_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<?php if (isset($this->items[0]->state)): ?>
							<th class="nowrap hidden-xs center q2c_width_10 fw-bold">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
						<th class="q2c_width_20 center fw-bold">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_TAXTRATES_S_TAX_PERCENT', 'a.percentage', $listDirn, $listOrder); ?>
						</th>
						<th class="q2c_width_20 fw-bold">
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_TAXTRATES_S_ZONE_NAME', 'a.zone_id', $listDirn, $listOrder); ?>
						</th>
						<?php
						if (isset($this->items[0]->title))
						{
							?>
							<th class="nowrap hidden-xs q2c_width_20 fw-bold">
								<?php echo Text::_('COM_QUICK2CART_TAXTRATES_STORE_NAME'); ?>
							</th>
							<?php
						}
						?>
					</tr>
				</thead>
				<tbody>
				<?php 
				$canCreate	= $user->authorise('core.create', 'com_quick2cart');
				$canEdit	= $user->authorise('core.edit.own', 'com_quick2cart');
				$canCheckin	= $user->authorise('core.manage', 'com_quick2cart');
				$canChange	= $user->authorise('core.edit.state', 'com_quick2cart');

				foreach ($this->items as $i => $item)
				{
					$ordering   = ($listOrder == 'a.ordering');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="nowrap center q2c_width_1">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<?php if (isset($item->checked_out) && $item->checked_out) : ?>
								<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'taxrates.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit) : ?>
								<a href="<?php echo Route::_('index.php?option=com_quick2cart&task=taxrateform.edit&id='.(int) $item->id); ?>">
								<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
						</td>

						<?php
						if (isset($this->items[0]->state)): ?>
							<td class="nowrap hidden-xs center q2c_width_10">
								<a class=" "
									href="javascript:void(0);"
									title="<?php echo ( $item->state ) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ;?>"
									onclick="document.adminForm.cb<?php echo $i;?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ( $item->state ) ? 'taxrates.unpublish' : 'taxrates.publish';?>');">
										<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($item->state) ? 'publish.png' : 'unpublish.png';?>"/>
								</a>
							</td>
						<?php
						endif; ?>
						<td class="q2c_width_20 center">
							<?php echo $item->percentage; ?>
						</td>
						<td class="q2c_width_20">
							<?php echo $item->zonename; ?>
						</td>
						<?php
						if (isset($this->items[0]->id)): ?>
							<td class="nowrap hidden-xs q2c_width_20">
								<?php echo $this->escape($item->title); ?>
							</td>
						<?php
						endif; ?>
					</tr>
				<?php
				} ?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php
		endif; ?>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="taxrates" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
