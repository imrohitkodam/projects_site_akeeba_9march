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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

$app = Factory::getApplication();
ToolBarHelper::back( Text::_('QTC_HOME') , 'index.php?option=com_quick2cart');

$input       = $app->input;
$cid		 = $input->get(  'cid','','ARRAY');
$store_names = array();
$comquick2cartHelper = new comquick2cartHelper;
$Itemid              =(isset($this->Itemid)) ? $this->Itemid : 0;
?>
<script type="text/javascript">
Joomla.submitbutton = function(action){
//console.log(action);
		if(action=='publish' || action=='unpublish')
		{
			Joomla.submitform(action);
		}
		else if(action=='delete')
		{
				/*if (document.adminForm.boxchecked.value==0){
					alert('<?php echo Text::_("QTC_MAKE_SEL");?>');
				return;
				}*/

				var r=confirm('<?php echo Text::_("QTC_DELETE_CONFIRM_PROD");?>');
				if (r==true)
				{
					var aa;
				}
				else
					return;
		}
		else
		{
			window.location = 'index.php?option=com_quick2cart&view=products';
		}
	var form = document.adminForm;
	submitform( action );
	return;

 }

</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-delaysreport">
	<form  method="post" name="adminForm" id="adminForm" class="form-validate">
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
		<table class="adminlist table table-striped table-bordered ">
			<thead>
				<tr>
					<th colspan="12" width="100%">
						<!-- search div -->
						<div class="filter-search pull-left">
							<?php echo Text::_('Filter'); ?>:
							<input type="text" name="search" id="search_list" value="<?php echo $this->lists['search_list'];?>" class="input-medium" onchange="document.adminForm.submit();" />
							<button class="btn btn-success" onclick="this.form.submit();"><?php echo Text::_('SA_GO'); ?></button>
							<button class="btn btn-primary" onclick="document.getElementById('search_list').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();"><?php echo Text::_('RESET'); ?></button>
						</div >

						<!-- CALENDER ND REFRESH BTN  -->
						<?php
						//$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));
						//$toDate = date('Y-m-d');
						$backdate = $toDate = '';
						if(!empty($this->lists['salesfromDate'])) {
							$backdate = $this->lists['salesfromDate'];
						}
						if(!empty($this->lists['salestoDate'])) {
							$toDate = $this->lists['salestoDate'];
						}
						?>
						<div class="form-inline pull-right" title="<?php  echo Text::_('MSG_ON_FILTER'); ?>" >
							<label class=""><?php  echo Text::_('FROM_DATE'); ?>  </label>
							<?php echo HTMLHelper::_('calendar', $backdate, 'salesfromDate', 'from', '%Y-%m-%d', array('class'=>'inputbox input-small')); ?>
							<label class=""><?php  echo Text::_('TO_DATE'); ?>  </label>
								 <?php echo HTMLHelper::_('calendar', $toDate, 'salestoDate', 'to', '%Y-%m-%d', array('class'=>'inputbox input-small')); ?>
							<input id="btnRefresh" class="btn  btn-small btn-primary" type="button" value=">>" style="font-weight: bold;" onclick="this.form.submit();"/>

						</div>
					</th>
				</tr>

				<tr class="hidden">
					<th class="id" width="20%" align="left"  align="center">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('NUMBER_COUNT'), 'id',   $this->lists['order_Dir'],   $this->lists['order'] );
						 ?>
					</th>
					<th width="20%" class="order_id" align="left">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ORDERS_ID'), 'order_id',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
					</th>
					<th width="20%" class="delay" align="left">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ORDERS_DELAY'), 'delay',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
					</th>
					<th width="20%" class="buyer" align="left">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('BUYER_NAME'), 'buyer',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
					</th>
					<th width="20%" class="status" align="left">
						<?php echo HTMLHelper::_('grid.sort',   Text::_('ORDER_STATUS'), 'status',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
					</th>
				</tr>

				<tr>
					<th class="id" width="20%" align="left"  align="center">
						<?php echo Text::_('NUMBER_COUNT'); ?>
					</th>
					<th width="20%" class="order_id" align="left">
						<?php echo Text::_('ORDERS_ID'); ?>
					</th>
					<th width="20%" class="delay" align="left">
						<?php echo Text::_('ORDERS_DELAY'); ?>
					</th>
					<th width="20%" class="buyer" align="left">
						<?php echo Text::_('BUYER_NAME'); ?>
					</th>
					<th width="20%" class="status" align="left">
						<?php echo Text::_('ORDER_STATUS'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php
				$k = 0;
				if(!empty($this->getDelaysReport))
				{
					for ($i=0, $n=count( $this->getDelaysReport ); $i < $n; $i++)
					{
						$row 	= $this->getDelaysReport[$i];
						?>
						<tr class="<?php //echo 'row$k'; ?>">
							<td align="left">
									<?php echo $i+1;; ?>
							</td>
							<td>
								<a href="<?php echo  Uri::base().substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid='.$row->id.'&Itemid='.$Itemid),strlen(Uri::base(true))+1); ?>"><?php echo HTMLHelper::tooltip(Text::_('QTC_TOOLTIP_VIEW_ORDER_MSG'), Text::_('QTC_TOOLTIP_VIEW_ORDER'), '', $row->prefix.$row->id ) ;?></a>
							</td>
							<td align="center">
								<?php /*
								$dispatcher = JDispatcher::getInstance();
								PluginHelper::importPlugin("system");
								$result=$dispatcher->trigger("onGetDelaysInOrder",array($row->id,$row->status));

								$storeHelper=new storeHelper();
								$delay=$storeHelper->GetDelaysInOrder($row->id);*/

								if($row->delay)
									echo $row->delay;
								else
									echo '-';
								 ?>
							</td>
							<td align="left">
									<?php echo $row->name; ?>
							</td>
							<td align="left">
								<?php
								if($row->status == 'C')
									echo Text::_('ORDER_CONFIRMED');
								elseif($row->status == 'S')
									echo Text::_('ORDER_SHIPPED');
								else
									echo Text::_('ORDER_CANCELLED');
								 ?>
							</td>
						</tr>
						<?php
						//$k = 1 - $k;
						$k++;
					}
				}
				else
				{
					?>
					<td colspan="8">
						<div class="well" >
							<div class="alert alert-error">
								<span ><?php echo Text::_('COM_QUICK2CART_SALESREPORT_NO_DETAIL_VIEW'); ?> </span>
							</div>
						</div>
					</td>
					<?php
				}
				?>
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
		<input type="hidden" name="view" value="delaysreport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="delaysreport" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
