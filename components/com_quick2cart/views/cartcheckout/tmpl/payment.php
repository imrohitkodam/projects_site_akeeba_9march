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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

if(!empty($showLegend))
{
?>
<legend id="" class="">
	<?php echo Text::_('COM_QUICK2CART_PAYMENT_DETAILS')?>&nbsp;
	<small><?php //echo JText::_('QTC_SHIPIN_DESC')?></small>
</legend>
<?php
}
?>

<!-- COMMENT-->
<div class="control-group ">
	<label for="" class="control-label"><?php echo Text::_( 'QTC_USER_COMMENT' ); ?></label>
	<div class="controls">
		<textarea id="comment" name="comment"  rows="3" maxlength="135" ></textarea>
	</div>
</div>
<!-- PAY,ENT GETEWAY -->
<div class="control-group "  >
	<?php
	//VM:IF  TOTAL PRICE IS 0 THEN DONT SHOW GATEWAYS, SHOW BTN
	if(!empty($shipval))
	{
		$default="";
		$lable=Text::_( 'SEL_GATEWAY' );
		$gateway_div_style=1;
		if(!empty($this->gateways)) //if only one geteway then keep it as selected
		{
			$default=$this->gateways[0]->id; // id and value is same
		}
		if(!empty($this->gateways) && count($this->gateways)==1) //if only one geteway then keep it as selected
		{
			$default=$this->gateways[0]->id; // id and value is same
			$lable=Text::_( 'QTC_GATEWAY_IS' );
			$gateway_div_style=0;
		}
		?>
		<label for="" class="control-label"><?php echo $lable ?></label>
		<div class="controls" style="<?php echo ($gateway_div_style==1)?"" : "display:none;" ?>">
			<?php
			if(empty($this->gateways))
				echo Text::_( 'NO_PAYMENT_GATEWAY' );
			else
			{
				$pg_list = HTMLHelper::_('select.radiolist', $this->gateways, 'gateways', 'class="required" ', 'id', 'name',$default,false);
				echo $pg_list;
			}
			?>
		</div>
		<?php
		if(empty($gateway_div_style))
		{
			?>
				<div class="controls qtc_left_top">
				<?php echo 	$this->gateways[0]->name; // id and value is same ?>
				</div>
			<?php
		}
	}// end of shipval else
	?>

</div>
<!-- FOR TERMS AND CONDITON-->
<?php

if($showTersmAndCond ) {
	HTMLHelper::_('bootstrap.renderModal');
	$Itemid = $helperobj->getitemid('index.php?option=com_content&view=article');
	$catid=0;
	//$link =JUri::root().ContentHelperRoute::getArticleRoute($res["product_id"], $catid);
	$terms_link = Uri::root().substr(Route::_('index.php?option=com_content&view=article&id='.$termsCondArtId."&Itemid=".$Itemid."&tmpl=component"),strlen(Uri::base(true))+1);
?>

<div class="control-group">
	<input class="qtc_checkbox_style" type="checkbox" name="qtc_accpt_terms" id="qtc_accpt_terms" aria-invalid="false">&nbsp;&nbsp;<?php  echo Text::_( 'COM_QUICK2CART_ACCEPT' ); ?>
	<?php 
	$modalConfig = array('width' => '600px', 
		'height' => '600px', 
		'closeButton' => false, 
		'modalWidth' => 80, 
		'bodyHeight' => 70);
	$modalConfig['url'] = $terms_link;
	echo HTMLHelper::_('bootstrap.renderModal', 'termsConditionModal', $modalConfig);
	?>
	<a data-bs-target="#termsConditionModal" data-bs-toggle="modal" data-target="#termsConditionModal" data-toggle="modal" class="qtc_modal" title="<?php echo Text::_('COM_QUICK2CART_TERMS_CONDITION'); ?>">
		<span class="qtc_terms_conditons hasTip" title="<?php echo Text::_( 'COM_QUICK2CART_TERMS_CONDITION' ); ?>">
				<?php  echo Text::_( 'COM_QUICK2CART_TERMS_CONDITION' ); ?>
		</span>
	</a>
</div>
<?php
} ?>
