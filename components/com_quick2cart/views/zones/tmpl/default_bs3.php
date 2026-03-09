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

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$user = Factory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$saveOrder = ($listOrder == 'a.ordering');

Factory::getDocument()->addScriptDeclaration('
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery(".q2c-wrapper #limit").removeAttr("size");
	});
');
?>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery("#limit").removeAttr('size');
	});

	Joomla.submitbutton = function(task)
	{
		if (task=='zoneform.add' || task=='zones.backToDashboard')
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
				case 'zones.publish':
					Joomla.submitform(task);
				break

				case 'zones.unpublish':
					Joomla.submitform(task);
				break

				case 'zones.delete':
					if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_MESSAGE'); ?>"))
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

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> q2c_zones container-fluid">
	<form action="" method="post" name="adminForm" id="adminForm">
		<!-- Toolbar -->
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<?php
				$active = 'zones';
				ob_start();
				include($this->toolbar_view_path);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
				?>
			</div>
		</div>

		<div class="row">
			<h1><strong><?php echo Text::_('COM_QUICK2CART_SETUP_ZONE'); ?></strong></h1>

			<!-- Help msg -->
			<div class="alert alert-info ">
				<?php echo Text::_('COM_QUICK2CART_SETUP_ZONE_HELP'); ?>
			</div>

			<?php echo $this->toolbarHTML;?>

			<div class="clearfix"> </div>
			<hr class="hr-condensed" />
		</div>

		<div id="qtc-filter-bar" class="qtc-btn-toolbar">
			<div class="filter-search btn-group pull-left q2c-btn-wrapper">
				<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="qtc-hasTooltip"
				title="<?php echo Text::_('COM_QUICK2CART_FILTER_ZONES_SEARCH'); ?>" />
			</div>

			<div class="btn-group pull-left q2c-btn-wrapper">
				<button type="submit" class="btn btn-default qtc-hasTooltip"
				title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
				</button>
				<button type="button" class="btn btn-default qtc-hasTooltip"
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
				<?php
				if (!empty($this->userStores))
				{
					if (count($this->userStores)>1)
					{
						$default = $this->state->get('filter.sel_store');
						$options   = array();
						$options[] = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELET_STORE'));

						foreach ($this->userStores as $key=>$value)
						{
							$options[] = HTMLHelper::_('select.option', $value['id'], $value['title']);
						}

						echo $this->dropdown = HTMLHelper::_('select.genericlist', $options, 'filter_store', 'class="input-medium"  autocomplete="off" onchange="document.adminForm.submit();" ', 'value', 'text', $default);
					}
				}
				?>
			</div>

			<div class="qtc-btn-group pull-right hidden-xs q2c-btn-wrapper">
				<?php
				echo HTMLHelper::_('select.genericlist', $this->publish_states, "filter_published", 'class="input-medium"  onchange="document.adminForm.submit();" name="filter_published"', "value", "text", $this->state->get('filter.state'));
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
			<table class="table table-striped table-bordered" id="zoneList">
				<thead>
					<tr>
						<th class="q2c_width_1">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>

						<th class=''>
							<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_ZONES_ZONE_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>

						<?php if (isset($this->items[0]->state)): ?>
							<th class="hidden-xs nowrap center q2c_width_10">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>

						<th class="q2c_width_30">
							<?php echo HTMLHelper::_('grid.sort',  'COM_QUICK2CART_STORE', 'store_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<tbody>
					<?php
					foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'a.ordering');
						$canCreate	= $user->authorise('core.create', 'com_quick2cart');
						$canEdit	= $user->authorise('core.edit', 'com_quick2cart');
						$canCheckin	= $user->authorise('core.manage', 'com_quick2cart');
						$canChange	= $user->authorise('core.edit.state', 'com_quick2cart');
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center q2c_width_1">
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>

							<td class="">
								<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=zoneform&task=zone.edit&id=' . (int)$item->id); ?>">
									<?php echo htmlspecialchars($item->name, ENT_COMPAT, 'UTF-8'); ?>
								</a>
							</td>

							<?php if (isset($this->items[0]->state)): ?>
								<td class="hidden-xs center q2c_width_10">
									<a class=" "
										href="javascript:void(0);"
										title="<?php echo ( $item->state ) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ;?>"
										onclick="document.adminForm.cb<?php echo $i;?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ( $item->state ) ? 'zones.unpublish' : 'zones.publish';?>');">
											<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($item->state) ? 'publish.png' : 'unpublish.png';?>"/>
									</a>
								</td>
							<?php endif; ?>

							<td class="q2c_width_30">
								<?php echo htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8');?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter();
			endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="zones" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
