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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

HTMLHelper::_('bootstrap.tooltip');
ToolBarHelper::back( Text::_('QTC_HOME') , 'index.php?option=com_quick2cart');
ToolBarHelper::publishList();
ToolBarHelper::unpublishList();
ToolBarHelper::editList();
ToolBarHelper::deleteList('', 'deletecoupon');
ToolBarHelper::addNew($task = 'add', $alt = Text::_('QTC_NEW'));

// MULTIVENDOR OFF THEN SHOW new button
$params             = ComponentHelper::getParams('com_quick2cart');
$multivendor_enable = $params->get('multivendor');
$input              = Factory::getApplication()->input;
$cid		        = $input->get(  'cid','','ARRAY');
$store_names        = array(); // used to store store_name against store_id
?>
<script type="text/javascript">
	Joomla.submitbutton = function(action)
	{
		if(action=='publish' || action=='unpublish')
		{
			Joomla.submitform(action);
		}
		else if(action=='deletecoupon')
		{
				if (document.adminForm.boxchecked.value==0){
					alert("<?php echo Text::_('QTC_MAKE_SEL');?>");
				return;
				}

				var r=confirm("<?php echo Text::_('QTC_DELETE_CONFIRM_COUPON');?>");
				if (r==true)
				{
					var aa;
				}
				else
					return;
		}
		else
		{
			window.location = 'index.php?option=com_quick2cart&view=managecoupon';
		}
	var form = document.adminForm;
	submitform( action );
	return;
 }
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> managecoupons">
	<form action="index.php?option=com_quick2cart" method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php
		// @ sice version 3.0 Jhtmlsidebar for menu
			if (!empty( $this->sidebar)) : ?>
				<div id="j-sidebar-container" class="span2">
					<?php echo $this->sidebar; ?>
				</div>
				<div id="j-main-container" class="span10">
			<?php else : ?>
				<div id="j-main-container">
			<?php endif;
		?>
	<table class="adminlist table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th colspan="12" width="100%">
				<div class="filter-search pull-left">
					<?php echo Text::_('Filter'); ?>:
					<input type="text" name="search" id="search_list" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="input-medium" onchange="document.adminForm.submit();" />
					<button class="btn btn-success" onclick="this.form.submit();"><?php echo Text::_('SA_GO'); ?></button>
					<button class="btn btn-primary" onclick="document.getElementById('search_list').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo Text::_('RESET'); ?></button>
				</div >
				<div class="btn-group pull-right hidden-phone"> </div>
				</th>
			</tr>
			<tr>
				<th width="2%" class="title">
					<?php echo Text::_('AD_NUM'); ?>
				</th>
				<th width="2%" align="center" class="title">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="title" align="left" width="10%" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_NAM'), 'name',   $this->lists['order_Dir'],   $this->lists['order'] );?>
				</th>
				<th width="8%" class="title" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_PUB'), 'published',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_COD'), 'code',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="12%" class="title" align="center">

					<?php echo HTMLHelper::_('grid.sort',   Text::_('BACKEND_COUPAN_VALUE'), 'value',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="left">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_TYP'), 'val_type',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('M_USE'), 'max_use',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="10%" class="title" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_VALF'), 'from_date',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_EXP'), 'exp_date',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
				<th width="15%" class="title" align="center">
					<?php echo HTMLHelper::_('grid.sort',   Text::_('C_STORE_NAME'), 'store_id',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(empty($this->coupons))
			{
			?>
				<tr >
					<td colspan='11'>
						<div class="well">
							<div class="alert alert-info">
								<?php echo Text::_( "COM_QUICK2CART_COUPON_NOT_YET_CREATED"); ?>
							</div>
						</div>
					</td>
				</tr>
			<?php
			}
			else
			{
				$k = 0;
				for ($i=0, $n=count( $this->coupons ); $i < $n; $i++)
				{
					$zone_type = '';
					$row 	   = $this->coupons[$i];
					$published = HTMLHelper::_('grid.published', $row, $i );
					$link 	   = 'index.php?option=com_quick2cart&amp;view=managecoupon&layout=form&amp;cid[]='. $row->id. '';
					?>
					<tr class="<?php echo 'row$k'; ?>">
						<td>
							<?php echo $i+1+$this->pagination->limitstart;?>
						</td>
						<td align="center"> <!-- check box -->
							<?php echo HTMLHelper::_('grid.id', $i, $row->id ); ?>
						</td>
						<td align="left">
							<a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
						</td>
						<td align="center">
							<?php echo $published ?>
						</td>
						<td align="left">
							<?php echo stripslashes($row->code); ?>
						</td>
						<td align="center">
							<?php echo $row->value ?>
						</td>
						<td align="left">
							<?php  if($row->val_type==0){echo Text::_( "C_FLAT");}else{echo Text::_( "C_PER");} ?>
						</td>
						<td align="center">
							<?php echo $row->max_use ?>
						</td>
						<td align="center">
							<?php
							if($row->from_date!='0000-00-00 00:00:00')
							{
								$from_date = date("Y-m-d",strtotime($row->from_date));
								echo $from_date;
							}
							else
							{
								echo "-";
							}
							?>
						</td>
						<td align="center">
							<?php
							if($row->exp_date!='0000-00-00 00:00:00')
							{
								$exp_date=date("Y-m-d",strtotime($row->exp_date));
								echo $exp_date ;
							}
							else
									echo "-";
							?>
						</td>
						<td>
							<?php
								if($row->store_id)
								{
									$quick2cartModelManagecoupon=new quick2cartModelManagecoupon();
									echo $store_names[$row->store_id]=(!empty($store_names[$row->store_id])?$store_names[$row->store_id]:$quick2cartModelManagecoupon->getStoreNmae($row->store_id));
								}
							?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
			?>
			<?php
			} // end of else?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13">
					<div class="pager"><?php echo $this->pagination->getListFooter(); ?></div>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="option" value="com_quick2cart" />
	<input type="hidden" name="view" value="managecoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>
