<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Component\ComponentHelper;

$data = $displayData;

// Remove extra data
unset($data->renderer);
$selectedFilters=$data->selectedFilters;

//Get bootstrap version
$comparams      = ComponentHelper::getParams( 'com_quick2cart' );
$currentBSViews = $comparams->get('bootstrap_version', "bs3");
$filtertick     = ($currentBSViews == "bs2") ? 'bs2' : 'bs3';

// Remove extra data
unset($data->selectedFilters);

$style = $data->style;

// Remove extra data
unset($data->style);
?>
<div id="qtc-filters_stylishcheckbox" >
	<?php
	foreach ($data as $filterName => $options)
	{
	?>
		<div class="qtcfilterwrapper <?php echo $filterName;?>filterwrapper">
			<?php

			if (!empty($options))
			{
			?>
				<div class="qtcfiltername <?php echo $filterName;?>filtername">
					<?php echo $filterName;?>
				</div>
			<?php
			}
			?>
			<div class="qtcfilterlistwrapper tj-filterlistwrapper" style="<?php echo $style;?>">
				<ul class="qtcfilterlist <?php echo $filterName;?>filterlist ">
				<?php foreach ($options as $filterOption)
				{
				?>
					<li class="qtcfilteritem <?php echo $filterName;?>filteritem <?php echo $filterName . $filterOption['option_name'];?>">
						<input type="checkbox" class="qtcCheck filter-fieldCheckbox  <?php echo $filtertick;?> " name="attributeoptions[]" id="<?php echo $filterName . $filterOption['option_name'];?>" onclick="qtcfiltersubmit('1')" value="<?php echo $filterOption['id'];?>" <?php echo in_array($filterOption['id'],$selectedFilters)?'checked="checked"':'';?>/>
						<label for="<?php echo $filterName . $filterOption['option_name'];?>"> <?php echo $filterOption['option_name'];?></label>
					</li>
				<?php
				}
				?>
				</ul>
				<div style="clear:both"></div>
			</div>
		</div>
		<?php
	}?>
</div>
