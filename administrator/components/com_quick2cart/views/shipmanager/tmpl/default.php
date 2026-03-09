<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');

if (JVERSION < '4.0.0')
{
	JHtmlBehavior::framework();
}

$document = Factory::getDocument();
HTMLHelper::_('stylesheet', 'components/com_quick2cart/js/order.js');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/css/geo/geo.css');
HTMLHelper::_('script', 'administrator/components/com_quick2cart/js/geo/jquery-1.7.2.js');
HTMLHelper::_('script', 'components/com_quick2cart/js/geo/jquery.ui.core.js');
HTMLHelper::_('script', 'components/com_quick2cart/js/geo/jquery.ui.widget.js');
HTMLHelper::_('script', 'components/com_quick2cart/js/geo/jquery.ui.position.js');
HTMLHelper::_('script', 'components/com_quick2cart/js/geo/jquery.ui.autocomplete.js');
HTMLHelper::_('script', 'components/com_quick2cart/js/geo/geo.js');

$params     = ComponentHelper::getParams('com_quick2cart');
$multi_curr = $params->get( 'addcurrency' );

function addCurrencyField($element,$multi_curr,$forallcountry=null)
{
	$html = "";

	if($multi_curr)
	{
		$multi_currs = explode(",",$multi_curr);

		foreach($multi_currs as $key=>$value)
		{
			$elename = (!empty($forallcountry))?($forallcountry.'_'.$value):($element.'_'.$value);
			$html.=	"<div class='input-append'>";
			$html.="<input class='input-mini countrycurrvalue' autocomplete='off' id='$elename' name='$elename' $value' size='16' type='text' placeholder='$value' />";
			$html.="<span class='add-on'>$value</span>";
			$html.="</div>";
		}
	}

	return $html;
}

$qtc_ul_currtextstyle="margin-top:25px;";
?>
<div class="techjoomla-bootstrap">
	<form class="form-inline" id="shipmanagerid">
		<!-- According to social ads  -->
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
		<div id="geo_target_space" class="target_space">
			<div id="geo_target_div" <?php // echo $geo_dis; ?>>
				<legend class="sa_labels1"><?php echo Text::_('QTC_GEO_TARGET_TIP'); ?> </legend>
				<table id="mapping-field-table">
					<tr>
			 			<td class="ad-fields-lable"><?php echo Text::_("QTC_GEO_COUNTRY");?></td>
			 			<td>
							<ul class='selections' id='selections_country'>
								<input type="text" class="geo_fields  ad-fields-inputbox"  id="country" value="<?php  echo (isset($this->geo_fields['country']) ) ? $this->geo_fields['country'] : Text::_('SAGEOEVERY_TYP_MSG') ; ?>" />
								<input type="hidden" class="geo_fields_hidden" name="geo[country]" id="country_hidden" value="" autocomplete='off' />
							</ul>
						</td>
						<td>
							<ul>
								<div  id="qtc_countryCurrency" class="qtc_countryCurrency"  style="display:none;">
									<?php echo $pt=addCurrencyField("country",$multi_curr); ?>
								</div>
							</ul>
					 	</td>
					</tr>
					<tr colspan="0">
						<td></td>
						<td colspan="2">
							<div id ="geo_others" style="display:none;">
								<table>
									<tr>
										<td>
											<input type="radio" <?php //echo (JRequest::getVar("frm")!='editad' || $this->geo_type == "everywhere" )?'checked="checked"' : ''; ?> value="everywhere" name="geo_type" id="everywhere" class="saradioLabel"  autocomplete='off' checked="checked">
											<label class="saradioLabel" for="everywhere"><?php echo Text::_("SAGEOEVERY"); ?></label>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>
											<input type="radio" <?php // echo ($this->geo_type == "byregion" )?'checked="checked"' : ''; ?> value="byregion" name="geo_type" id="byregion" class="saradioLabel"  autocomplete='off'>
											<label class="saradioLabel" for="byregion"><?php echo Text::_("QTC_GEO_STATE"); ?></label>
											<ul style="display:none;" class="selections byregion_ul" id='selections_region' >
												<input type="text" class="geo_fields ad-fields-inputbox"  id="region" value="<?php  echo (isset($this->geo_fields['region']) ) ? $this->geo_fields['region'] : Text::_('QTC_GEO_STATE_TYP_MSG') ; ?>" />
												<input type="hidden" class="geo_fields_hidden" name="geo[region]" id="region_hidden" value="" autocomplete='off'  />
											</ul>
										</td>
										<td>
											<ul style="display:none;<?php echo $qtc_ul_currtextstyle;?>" class="byregion_ul" id=''>
												<?php echo $pt=addCurrencyField("region",$multi_curr);?>
											</ul>
										</td>
									</tr>
									<tr>
										<td>
											<input type="radio" <?php //echo ($this->geo_type == "bycity" )?'checked="checked"' : ''; ?> value="bycity" name="geo_type" id="bycity" class="saradioLabel"  autocomplete='off' >
											<label class="saradioLabel" for="bycity"><?php echo Text::_("QTC_GEO_CITY"); ?></label>
											<ul style="display:none;" class="selections bycity_ul"  id='selections_city' >
												<input type="text" class="geo_fields ad-fields-inputbox"  id="city" value="<?php echo (isset($this->geo_fields['city']) ) ? $this->geo_fields['city'] : Text::_('QTC_GEO_CITY_TYP_MSG') ; ?>" />
												<input type="hidden" class="geo_fields_hidden" name="geo[city]" id="city_hidden" value="" autocomplete='off'  />
											</ul>
										</td>
										<td>
											<ul style="display:none; <?php echo $qtc_ul_currtextstyle;?>" class="bycity_ul" id='city_price'>
												<?php echo $pt=addCurrencyField("city",$multi_curr);?>
											</ul>
										</td>
									</tr>
								</table>  <!-- inner table End -->
							</div> <!--  end geo_others-->
						</td>
					</tr>
				</table>
			</div><!-- geo_target_div end here -->
			<div style="clear:both;"></div>
			<div class="form-actions">
				<input type="submit" class="btn btn-success validate" value="Save Shipping Options" aria-invalid="false">
				<input type="hidden" name="option" value="com_quick2cart">
				<input type="hidden" id="task" name="task" value="saveShipOption">
				<input type="hidden" name="controller" value="shipmanager" />
			</div>
		</div>
		<input type="hidden" name="editview" id="editview" value="<?php echo (Factory::getApplication()->input->get('frm')=='editad')? '1' : '0'; ?>">
		<!-- END of According to social ads  -->
	</form>
</div>





