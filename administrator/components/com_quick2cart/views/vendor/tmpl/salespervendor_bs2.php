<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
$document            = Factory::getDocument();
$comquick2cartHelper = new comquick2cartHelper;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(action){
		var form = document.adminForm;

		if(action=='publish' || action=='unpublish')
		{
			Joomla.submitform(action);
		}
		else if(action=='deletevendor')
		{
			if (document.adminForm.boxchecked.value==0){
				alert("<?php echo Text::_('QTC_MAKE_SEL');?>");
				return;
			}

			var r=confirm("<?php echo Text::_('QTC_DELETE_CONFIRM_VENDER');?>");
			if (r==true)
			{
				var aa;
			}
			else
				return;
		}
		else if(action=="addvendor")
		{
			window.location = "index.php?option=com_quick2cart&view=vendor&layout=newvendor";
			return;
		}
		else if(action=='edit')  /** edit */
		{
			if (document.adminForm.boxchecked.value==0){
				alert("<?php echo Text::_('QTC_MAKE_SEL');?>");
				return;
			}
			else if(document.adminForm.boxchecked.value > 1)
			{
				alert("<?php echo Text::_('QTC_MAKE_ONE_SEL');?>");
				return;
			}
		}
		submitform( action );
		return;
	 }
</script>

<div class="techjoomla-bootstrap">
	<form  method="post" name="adminForm" id="adminForm" class="form-validate">
		<?php
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
							<th colspan="7" width="100%">
								<div class="filter-search pull-left">
									<?php echo Text::_( 'Filter' ); ?>:
									<input type="text" name="search" id="search_list" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="input-medium" onchange="document.adminForm.submit();" />
									<button class="btn btn-success" onclick="this.form.submit();">
										<?php echo Text::_( 'SA_GO' ); ?>
									</button>
									<button class="btn btn-primary" onclick="document.getElementById('search_list').value='';this.form.getElementById('filter_type').value='0';this.form.getElementById('filter_logged').value='0';this.form.submit();">
										<?php echo Text::_( 'RESET' ); ?>
									</button>
								</div >
								<div class="btn-group pull-right hidden-phone"> </div>
							</th>
							<th colspan="5" >
								<div style="float:right;"></div>
							</th>
						</tr>
						<tr>
							<th width="2%" align="center" class="title hidden">
								<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
							</th>
							<th class="store_id hidden" align="left" width="12%" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_ID'), 'id',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th class="title" align="left" width="20%" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_TITLE'), 'title',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th class="vendor_name" align="left" width="20%" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'VENDOR_NAME'), 'title',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th class="description hidden" align="left" width="12%" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_DESCRIPTION'), 'description',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th class="owner hidden" align="left" width="10%" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_OWNER_NAME'), 'owner',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th class="email" align="left" width="20%" align="center">
								<?php  echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_EMAIL'), 'store_email',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th width="20%" class="phone" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'STORE_PHONE'), 'phone',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
							<th width="20%" class="sale" align="center">
								<?php echo HTMLHelper::_('grid.sort',   Text::_( 'TOTAL_SALE'), 'total_sale',   $this->lists['order_Dir'],   $this->lists['order'] ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						$n = count($this->storeinfo);

						for ($i=0 ; $i < $n; $i++)
						{
							$row       = $this->storeinfo[$i];
							$published = HTMLHelper::_('grid.published', $row, $i );	//print_r($row);
							$link      = Route::_(Uri::base()."index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=".$row->id);
						?>
							<tr class="<?php echo 'row$k'; ?>">
								<td align="center" class="hidden"> <!-- check box -->
									<?php echo HTMLHelper::_('grid.id', $i, $row->id ); ?>
								</td>
								<!-- STORE ID -->
								<td align="center"  class="hidden">
									<?php echo $row->id; ?>
								</td>
								<!-- STORE NAME / store title -->
								<td align="left">
									<a href="<?php echo $link; ?>">
										<?php echo HTMLHelper::tooltip(Text::_('QTC_TOOLTIP_VIEW_STORE'), Text::_('QTC_STORE_TOOLTIP_TITLE'), '', $row->title ) ;?>
									</a>
								</td>
								<!-- STORE OWNER NAME  -->
								<td align="left">
									<?php echo $row->username; ?>
								</td>
								<!-- STORE description -->
								<td align="center" class="hidden">
									<?php echo $row->description; ?>
								</td>
								<!-- STORE OWNER-->
								<td align="left"  class="hidden">
									<?php echo ($row->owner); ?>
								</td>
								<!-- store_email -->
								<td align="center">
									<?php echo $row->store_email ?>
								</td>
								<!-- STORE PHONE NO -->
								<td align="left">
									<?php echo $row->phone ?>
								</td>
								<!-- STORE Total Sale NO -->
								<td align="left">
									<?php
										$storeHelper = new storeHelper();
										$total_sale  = $storeHelper->getTotalSalePerStore($row->id);

										if($total_sale)
										{
											echo $comquick2cartHelper->getFromattedPrice($total_sale);
										}
									?>
								</td>
							</tr>
						<?php
							$k = 1 - $k;
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
				<input type="hidden" name="view" value="vendor" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="controller" value="vendor" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
				<?php echo HTMLHelper::_( 'form.token' ); ?>
			</div>
	</form>
</div>
