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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$app       = Factory::getApplication();
$user      = Factory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$strapperClass = Q2C_WRAPPER_CLASS;

// Check user is logged or not.
if (!$user->id)
{
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}

Factory::getDocument()->addScriptDeclaration('
	techjoomla.jQuery(document).ready(function() {
		techjoomla.jQuery(".q2c-wrapper #limit").removeAttr("size");
	});
');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task=='vendor.addNew')
		{
			Joomla.submitform(task);

			return true;
		}
		else if (task=='vendor.edit')
		{
			if (document.adminForm.boxchecked.value===0)
			{
				alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_NO_STORE_SELECTED')); ?>");

				return;
			}
			else if (document.adminForm.boxchecked.value > 1)
			{
				alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_MAKE_ONE_SEL')); ?>");

				return;
			}

			Joomla.submitform(task);
		}
		else
		{
			if (document.adminForm.boxchecked.value==0)
			{
				alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_NO_STORE_SELECTED')); ?>");

				return false;
			}
			switch(task)
			{
				case 'stores.publish':
					Joomla.submitform(task);
				break

				case 'stores.unpublish':
					<?php
					$admin_approval_stores = (int) $this->params->get('admin_approval_stores');

					if ($admin_approval_stores) :
					?>
						if (confirm("<?php echo Text::_('COM_QUICK2CART_MSG_CONFIRM_UNPUBLISH_STORE'); ?>"))
						{
							Joomla.submitform(task);
						}
						else
						{
							return false;
						}
					<?php
					else:
					?>
						Joomla.submitform(task);
					<?php
					endif;
					?>
				break

				case 'stores.delete':
					if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_VENDER'); ?>"))
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

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my-stores">
	<form method="post" name="adminForm" id="adminForm" class="form-validate">
		<?php
		$active = 'my_stores';
		ob_start();
		include($this->toolbar_view_path);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		?>
		<h1><strong><?php echo Text::_('COM_QUICK2CART_MY_STORES')?></strong></h1>
		<?php echo $this->toolbarHTML;?>
		<div class="clearfix"></div>
		<hr class="hr-condensed" />

       <div id="qtc-filter-bar" class="qtc-btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo JText::_('COM_QUICK2CART_FILTER_SEARCH_DESC_STORES'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="qtc-hasTooltip input-medium"
				title="<?php echo JText::_('COM_QUICK2CART_FILTER_SEARCH_DESC_STORES'); ?>" />
			</div>

			<div class="qtc-btn-group pull-left">
				<button type="submit" class="btn btn-default qtc-hasTooltip"
				title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
				</button>
				<button type="button" class="btn btn-default qtc-hasTooltip"
				title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
				onclick="document.id('filter_search').value='';this.form.submit();">
					<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
				</button>
			</div>

			<div class="qtc-btn-group pull-right hidden-xs q2c-btn-wrapper">
				<label for="limit" class="element-invisible">
					<?php echo JText::_('COM_QUICK2CART_SEARCH_SEARCHLIMIT_DESC'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>

			<div class="qtc-btn-group pull-right hidden-xs q2c-btn-wrapper">
			<?php
				echo JHtml::_('select.genericlist', $this->statuses, "filter_published", 'class="input-medium"  onchange="document.adminForm.submit();" name="filter_published"', "value", "text", $this->state->get('filter.state'));
			?>
			</div>
		</div>
		<div class="clearfix"> &nbsp;</div><br/>

		<?php
		if (empty($this->items))
		{
		    ?>
			<div class="clearfix">&nbsp;</div>
			<div class="alert alert-warning">
				<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php
		}
        else
        { ?>
			<div id='no-more-tables'>
			<table class="table table-striped table-bordered table-responsive" id="storeList">
				<thead>
					<tr>
						<th class="q2c_width_1 nowrap center hidden-xs">
							<input type="checkbox" name="checkall-toggle" value=""
							title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
							onclick="Joomla.checkAll(this)" />
						</th>
						<th class=''>
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STORE_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<?php if (isset($this->items[0]->published)) { ?>
							<th class="q2c_width_1 nowrap center">
								<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
							</th>
						<?php } ?>
						<th class='q2c_width_15 '>
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STORE_EMAIL', 'a.store_email', $listDirn, $listOrder); ?>
						</th>
						<th class='q2c_width_20 '>
							<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STORE_PHONE', 'a.phone', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
                    $canCreate  = $user->authorise('core.create', 'com_quick2cart');
                    $canEditOwn = $user->authorise('core.edit.own', 'com_quick2cart');
                    $canChange  = $user->authorise('core.edit.state', 'com_quick2cart');

					foreach ($this->items as $i => $item)
					{
					    $ordering   = ($listOrder == 'a.ordering'); ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="q2c_width_1 nowrap center hidden-xs" >
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="qtcWordWrap" data-title="<?php echo Text::_('COM_QUICK2CART_STORE_TITLE'); ?>">
								<?php
                                if ($canEditOwn)
                                { ?>
									<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . (int) $item->id . '&Itemid=' . $this->createstore_itemid); ?>"
									title="<?php echo Text::_('COM_QUICK2CART_EDIT_ITEM_LINK'); ?>">
										<?php echo $this->escape($item->title); ?>
									</a>
									<?php
                                }
					    else
					    {
					        echo $this->escape($item->title);
					    } ?>
							</td>
							<?php
                                if (isset($this->items[0]->published))
                                { ?>
								<td class="q2c_width_1 nowrap center"  data-title="<?php echo Text::_('JSTATUS'); ?>">
									<a class=" "
										href="javascript:void(0);"
										title="<?php echo ($item->published) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ?>"
										onclick="document.adminForm.cb<?php echo $i; ?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ($item->published) ? 'stores.unpublish' : 'stores.publish'; ?>');">
											<img class="q2c_button_publish" src="<?php echo Uri::root(true); ?>/components/com_quick2cart/assets/images/<?php echo ($item->published) ? 'publish.png' : 'unpublish.png'; ?>"/>
									</a>
								</td>
							    <?php
                                } ?>
							<td class="q2c_width_15 qtcWordWrap" data-title="<?php echo Text::_('COM_QUICK2CART_STORE_EMAIL'); ?>">
								<?php echo $this->escape($item->store_email); ?>
							</td>
							<td class="q2c_width_20 " data-title="<?php echo Text::_('COM_QUICK2CART_STORE_PHONE'); ?>">
								<?php echo empty($item->phone) ? "-" : $this->escape($item->phone); ?>
							</td>
						</tr>
					<?php
					}?>
				</tbody>
			</table>
			</div>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php
        } ?>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="stores" />
		<input type="hidden" name="layout" value="my" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
