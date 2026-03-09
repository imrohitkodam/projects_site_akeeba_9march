<?php
/**  
 * @package INSTANTPAYPAL::plugins::system
 * @subpackage libraries
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @copyright (C) 2014 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/**
 * Form Field for css purpouse
 * 
 * @package INSTANTPAYPAL::plugins::system
 * @subpackage libraries
 * @subpackage fields
 * @since 2.4
 */
class JFormFieldCssField extends FormField {
	/**
	 * Method to get the field label markup.
	 *
	 * @return string The field label markup.
	 *        
	 * @since 11.1
	 */
	protected function getLabel() {
		return null;
	}
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 *        
	 * @since 11.1
	 */
	protected function getInput() {
		// Add the css file for plugin settings styling
		$doc = Factory::getApplication()->getDocument ();
		$doc->getWebAssetManager()->addInlineStyle ( 'small.form-text.text-muted{display:none}span.spacer span.badge{padding:.3rem .5rem}span.spacer span.badge label{margin-bottom:0;color: #FFF;width:auto}joomla-tab-element[id*=general] > div.row > div:first-child select.form-select,joomla-tab-element[id*=general] > div.row > div:first-child input.form-control{max-width:300px}' );
		
		// Include jQuery/Bootstrap framework
		$wa = $doc->getWebAssetManager();
		$wa->useScript('jquery');
		$wa->useScript('jquery-noconflict');
		array_map ( function ($script) use ($wa) {
			$wa->useScript ( 'bootstrap.' . $script );
		}, [
				'popover'
		] );
		// Add custom JS to rework bootstrap popovers for the label description
		$script = <<<EOL
		jQuery(function($){
			var smallText = $('div.control-group small.form-text').hide();
			smallText.each(function(index, elem){
				var parentContainer = $(elem).parents('div.control-group');
				var targetLabel = $('div.control-label label,div.controls legend', parentContainer);
				var sourceDescription = $(elem).text();
				targetLabel.attr('title', $(targetLabel.get(0)).text());
				targetLabel.attr('data-bs-content', sourceDescription);
				targetLabel.addClass('hasPopover');
				targetLabel.attr('aria-haspopup', 'true');
			});
			[].slice.call(document.querySelectorAll('div.control-group label.hasPopover,div.controls legend.hasPopover')).map(function (popoverEl) {
					return new bootstrap.Popover(popoverEl, {
												 "template":'<div class="popover"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
												 "container":"body",
												 "html":true,
												 "placement":"top",
												 "trigger":"hover focus"
				});
			});
		});
		
		document.addEventListener('DOMContentLoaded', function() {
			var showHideControl = function(selectValue) {
				var targetCtrls = document.querySelectorAll('*.smartcheckout,input[name*=smartcheckout]');
				[].forEach.call(targetCtrls, (control) => {
					var parentControlContainer = control.closest('div.control-group');
					if(selectValue != 'smartcheckout') {
						parentControlContainer.style.display = 'none';
					} else {
						parentControlContainer.style.display = 'flex';
					}
				});
			}
				
			var selectControlOptions = document.querySelectorAll('#jform_params_button_type option');
			var selectControl = document.querySelector('#jform_params_button_type');
			selectControl.addEventListener('change', (e) => {
				showHideControl(e.target.value);
			});
				
			var selectControlSelectedOption = document.querySelector('#jform_params_button_type option:checked');
			showHideControl(selectControlSelectedOption.value);
		});
EOL;
		$doc->getWebAssetManager()->addInlineScript($script);
		
		return null;
	}
}