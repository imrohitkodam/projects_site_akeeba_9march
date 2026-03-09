<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonbusinesshours extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';

		$block_align = (isset ( $settings->block_align ) && $settings->block_align) ? $settings->block_align : '';
		$block_align_breakpoint = (isset ( $settings->block_align_breakpoint ) && $settings->block_align_breakpoint) ? '@' . $settings->block_align_breakpoint : '';
		$block_align_fallback = (isset ( $settings->block_align_fallback ) && $settings->block_align_fallback) ? $settings->block_align_fallback : '';

		// Block Alignment CLS.
		$block_cls [] = '';

		if (empty ( $block_align )) {
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = ' uk-margin-auto-right' . $block_align_breakpoint;
				$block_cls [] = 'uk-margin-remove-left' . $block_align_breakpoint . ($block_align_fallback == 'center' ? ' uk-margin-auto' : ' uk-margin-auto-left');
			}
		}

		if ($block_align == 'center') {
			$block_cls [] = ' uk-margin-auto' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = 'uk-margin-auto' . ($block_align_fallback == 'right' ? '-left' : '');
			}
		}

		if ($block_align == 'right') {
			$block_cls [] = ' uk-margin-auto-left' . $block_align_breakpoint;
			if (! empty ( $block_align_breakpoint ) && ! empty ( $block_align_fallback )) {
				$block_cls [] = $block_align_fallback == 'center' ? 'uk-margin-remove-right' . $block_align_breakpoint . ' uk-margin-auto' : 'uk-margin-auto-left';
			}
		}

		$block_cls = implode ( ' ', array_filter ( $block_cls ) );

		$max_width_cfg .= $addon_max_width_breakpoint . ($max_width_cfg ? $block_cls : '');

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$general .= $text_alignment . $text_breakpoint . $text_alignment_fallback;
		$general .= $max_width_cfg;

		$title_alignment = (isset ( $settings->title_alignment ) && $settings->title_alignment) ? ' ' . $settings->title_alignment : '';
		$title_alignment .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' ' . $settings->title_text_transform : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h2';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';

		$icon = (isset ( $settings->icon ) && $settings->icon) ? $settings->icon : '';
		$title_icon_position = (isset ( $settings->title_icon_position ) && $settings->title_icon_position) ? $settings->title_icon_position : 'before';

		$list_style = (isset ( $settings->large_padding ) && $settings->large_padding) ? ' uk-list-large' : '';
		$list_style .= (isset ( $settings->viewports ) && $settings->viewports) ? ' uk-table-responsive' : '';
		$list_style .= (isset ( $settings->list_style ) && $settings->list_style) ? ' ' . $settings->list_style : '';

		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? ' ' . $settings->card_style : '';

		// Parallax Animation
		$horizontal_start = (isset ( $settings->horizontal_start ) && $settings->horizontal_start) ? $settings->horizontal_start : '0';
		$horizontal_end = (isset ( $settings->horizontal_end ) && $settings->horizontal_end) ? $settings->horizontal_end : '0';
		$horizontal = (! empty ( $horizontal_start ) || ! empty ( $horizontal_end )) ? 'x: ' . $horizontal_start . ',' . $horizontal_end . ';' : '';

		$vertical_start = (isset ( $settings->vertical_start ) && $settings->vertical_start) ? $settings->vertical_start : '0';
		$vertical_end = (isset ( $settings->vertical_end ) && $settings->vertical_end) ? $settings->vertical_end : '0';
		$vertical = (! empty ( $vertical_start ) || ! empty ( $vertical_end )) ? 'y: ' . $vertical_start . ',' . $vertical_end . ';' : '';

		$scale_start = (isset ( $settings->scale_start ) && $settings->scale_start) ? (( int ) $settings->scale_start / 100) : 1;
		$scale_end = (isset ( $settings->scale_end ) && $settings->scale_end) ? (( int ) $settings->scale_end / 100) : 1;
		$scale = (! empty ( $scale_start ) || ! empty ( $scale_end )) ? 'scale: ' . $scale_start . ',' . $scale_end . ';' : '';

		$rotate_start = (isset ( $settings->rotate_start ) && $settings->rotate_start) ? $settings->rotate_start : '0';
		$rotate_end = (isset ( $settings->rotate_end ) && $settings->rotate_end) ? $settings->rotate_end : '0';
		$rotate = (! empty ( $rotate_start ) || ! empty ( $rotate_end )) ? 'rotate: ' . $rotate_start . ',' . $rotate_end . ';' : '';

		$opacity_start = (isset ( $settings->opacity_start ) && $settings->opacity_start) ? (( int ) $settings->opacity_start / 100) : 1;
		$opacity_end = (isset ( $settings->opacity_end ) && $settings->opacity_end) ? (( int ) $settings->opacity_end / 100) : 1;
		$opacity = (! empty ( $opacity_start ) || ! empty ( $opacity_end )) ? 'opacity: ' . $opacity_start . ',' . $opacity_end . ';' : '';

		$easing = (isset ( $settings->easing ) && $settings->easing) ? (( int ) $settings->easing / 100) : '';
		$easing_cls = (! empty ( $easing )) ? 'easing:' . $easing . ';' : '';

		$breakpoint = (isset ( $settings->breakpoint ) && $settings->breakpoint) ? $settings->breakpoint : '';
		$breakpoint_cls = (! empty ( $breakpoint )) ? 'media: @' . $breakpoint . ';' : '';

		$viewport = (isset ( $settings->viewport ) && $settings->viewport) ? (( int ) $settings->viewport / 100) : '';
		$viewport_cls = (! empty ( $viewport )) ? 'viewport:' . $viewport . ';' : '';

		$parallax_target = (isset ( $settings->parallax_target ) && $settings->parallax_target) ? $settings->parallax_target : false;
		$target_cls = ($parallax_target) ? ' target: !.jpb-section;' : '';

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		$parallax_zindex = (isset ( $settings->parallax_zindex ) && $settings->parallax_zindex) ? $settings->parallax_zindex : false;
		$zindex_cls = ($parallax_zindex && $animation == 'parallax') ? ' uk-position-z-index uk-position-relative' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$output = '';

		// Output.
		$output .= '<div class="uk-card' . $card_style . $zindex_cls . $general . '"' . $animation . '>';

		if ($title) {

			$icon_arr = array_filter ( explode ( ' ', $icon ) );
			if (count ( $icon_arr ) === 1) {
				$icon = 'fa ' . $icon;
			}
			$output .= '<' . $heading_selector . ' class="tm-title uk-margin-remove-bottom' . $heading_style . $title_alignment . '">';
			if ($icon && $title_icon_position == 'before') {

				$output .= '<i class="uk-margin-small-right ' . $icon . '" aria-hidden="true"></i>';

				$output .= '<span class="ui-title' . $font_weight . '">' . nl2br ( $title ) . '</span> ';
			}

			if ($icon && $title_icon_position == 'after') {

				$output .= '<span class="ui-title' . $font_weight . '">' . nl2br ( $title ) . '</span> ';

				$output .= '<i class="uk-margin-small-left ' . $icon . '" aria-hidden="true"></i>';
			} elseif (empty ( $icon )) {
				$output .= nl2br ( $title );
			}
			$output .= '</' . $heading_selector . '>';
		}

		$output .= '<div class="tm-body-wrapper">';

		$output .= '<ul class="uk-list uk-margin-remove-bottom' . $list_style . '">';

		foreach ( $settings->ui_business_day_items as $key => $value ) {
			$key ++;

			$el_id = $this->addon->id . $key;

			$business_day = (isset ( $value->business_day ) && $value->business_day) ? $value->business_day : '';
			$business_time = (isset ( $value->business_time ) && $value->business_time) ? $value->business_time : '';
			$active = (isset ( $value->active ) && $value->active) ? ' id="day-highlight-' . $key . '"' : '';

			$output .= '<li' . $active . ' class="uk-block">';

			if (! empty ( $business_day )) {
				$business_days = explode ( "\n", $business_day );
				foreach ( $business_days as $days ) {
					$output .= '<span>' . $days . '</span>';
				}
			}

			if (! empty ( $business_time )) {

				$business_times = explode ( "\n", $business_time );
				foreach ( $business_times as $times ) {
					$output .= '<span class="uk-float-right">' . $times . '</span>';
				}
			}

			$output .= '</li>';
		}

		$output .= '</ul>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme.js', [ ], [
				'defer' => true
		] );
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme-icons.js', [ ], [
				'defer' => true
		] );
	}
	public function stylesheets() {
		$style_sheet = [
				'components/com_jpagebuilder/assets/css/uitheme.css'
		];
		
		return $style_sheet;
	}
	public function css() {
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';
		$card_color = (isset ( $settings->card_color ) && $settings->card_color) ? 'color: ' . $settings->card_color . ';' : '';
		$card_padding = (isset ( $settings->card_padding ) && $settings->card_padding) ? $settings->card_padding : '20';
		$head_padding = (isset ( $settings->head_padding ) && $settings->head_padding) ? $settings->head_padding : '10';

		$background_style = (isset ( $settings->title_background ) && $settings->title_background) ? 'background: ' . $settings->title_background . ';' : '';
		$background_style .= (isset ( $settings->title_color ) && $settings->title_color) ? 'color: ' . $settings->title_color . ';' : '';
		$css = '';

		$css .= $addon_id . ' .tm-body-wrapper {padding: ' . $card_padding . 'px;}';
		$css .= $addon_id . ' .tm-title {padding: ' . $head_padding . 'px;}';
		if ($card_style == 'uk-card-custom') {
			if ($card_color || $card_background) {
				$css .= $addon_id . ' .uk-card-custom {' . $card_background . $card_color . '}';
			}
		}

		if ($background_style) {
			$css .= $addon_id . ' .tm-title {';
			$css .= $background_style;
			$css .= '' . '}' . "\n";
		}
		foreach ( $settings->ui_business_day_items as $key => $value ) {
			$key ++;
			$active_color = '';
			$active_color .= (isset ( $value->active_color ) && $value->active_color) ? 'color: ' . $value->active_color . ';' : '';
			if (isset ( $value->active ) && $value->active !== '') {
				$css .= $addon_id . ' #day-highlight-' . $key . '{';
				$css .= $active_color;
				$css .= '' . '}' . "\n";
			}
		}

		return $css;
	}
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$output = '
		<style type="text/css">
		#jpb-addon-{{ data.id }} .tm-body-wrapper {
			padding: {{ data.card_padding }}px;
		}
		#jpb-addon-{{ data.id }} .tm-title {
			padding: {{ data.head_padding }}px;
		}
		#jpb-addon-{{ data.id }} .tm-title {
			background-color: {{ data.title_background }};
			<# if ( data.title_color ) { #>
				color: {{ data.title_color }};
			<# } #>
		}
		
		<# if(data.card_style == "uk-card-custom" && data.card_background) { #>
			#jpb-addon-{{ data.id }} .uk-card-custom {
				background-color: {{ data.card_background }};
				<# if ( data.card_color ) { #>
					color: {{ data.card_color }};
				<# } #>
			}
		<# } #>
		<# _.each(data.ui_business_day_items, function(value, key){ 
			key++; #>
			<# if ( value.active && value.active_color ) { #>
				#jpb-addon-{{ data.id }} li#day-highlight-{{key}} {
					color: {{ value.active_color }};
				}
			<# } #>
		<# }); #>
		</style>		
		<#
		let max_width_cfg = ( data.addon_max_width ) ? " uk-width-" + data.addon_max_width : "";
		let addon_max_width_breakpoint = (!_.isEmpty( data.addon_max_width) && data.addon_max_width_breakpoint ) ? "@" + data.addon_max_width_breakpoint : "";

		let block_align = ( data.block_align ) ? data.block_align : "";
		let block_align_breakpoint = ( data.block_align_breakpoint ) ? "@" + data.block_align_breakpoint : "";
		let block_align_fallback = ( data.block_align_fallback ) ? data.block_align_fallback : "";

		var block_cls = "";

		if ( _.isEmpty( block_align ) ) {
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto-right" + block_align_breakpoint;
				block_cls += " uk-margin-remove-left" + block_align_breakpoint + ( block_align_fallback == "center" ? " uk-margin-auto" : " uk-margin-auto-left" );
			}
		}

		if ( block_align == "center" ) {
			block_cls += " uk-margin-auto" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += " uk-margin-auto" + ( block_align_fallback == "right" ? "-left" : "" );
			}
		}

		if ( block_align == "right" ) {
			 block_cls += " uk-margin-auto-left" + block_align_breakpoint;
			if ( !_.isEmpty( block_align_breakpoint ) && !_.isEmpty( block_align_fallback ) ) {
				block_cls += block_align_fallback == "center" ? " uk-margin-remove-right" + block_align_breakpoint + " uk-margin-auto" : "uk-margin-auto-left";
			}
		}

		max_width_cfg += addon_max_width_breakpoint + ( max_width_cfg ? block_cls : "" );

		let text_alignment = data.alignment ? " " + data.alignment : "";
		let text_breakpoint = (data.alignment && data.text_breakpoint) ? "@" + data.text_breakpoint : "";
		let text_alignment_fallback = (data.alignment && data.text_breakpoint && data.text_alignment_fallback) ? " uk-text-" + data.text_alignment_fallback : "";
		
		let addon_margin = data.addon_margin || "";

		var general = "";
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		general += text_alignment + text_breakpoint + text_alignment_fallback + max_width_cfg;

		var title_alignment  = ( data.title_alignment ) ? " "+ data.title_alignment : "";
		title_alignment += ( data.title_text_transform ) ? " "+ data.title_text_transform : "";
		let title            = ( data.title ) ? data.title : "";
		let heading_selector = ( data.heading_selector ) ? data.heading_selector : "h2";
		let heading_style    = ( data.heading_style ) ? " uk-"+data.heading_style : "";

		let icon                = ( data.icon ) ? data.icon : "";
		let title_icon_position = ( data.title_icon_position ) ? data.title_icon_position : "before";

		var list_style  = ( data.large_padding ) ? " uk-list-large" : "";
		list_style += ( data.viewports ) ? " uk-table-responsive" : "";
		list_style += ( data.list_style ) ? " " +data.list_style : "";

		let card_style  = ( data.card_style ) ? " " + data.card_style : "";

		// Animation
		let horizontal_start = (!_.isEmpty(data.horizontal_start) && data.horizontal_start) ? data.horizontal_start : "0";
		let horizontal_end = (!_.isEmpty(data.horizontal_end) && data.horizontal_end) ? data.horizontal_end : "0";
		let horizontal = (!_.isEmpty(data.horizontal_start) || !_.isEmpty(data.horizontal_end)) ? \'x:\'+horizontal_start+\',\'+ horizontal_end +\';\' : "";

		let vertical_start = (!_.isEmpty(data.vertical_start) && data.vertical_start) ? data.vertical_start : "0";
		let vertical_end = (!_.isEmpty(data.vertical_end) && data.vertical_end) ? data.vertical_end : "0";
		let vertical = (!_.isEmpty(data.vertical_start) || !_.isEmpty(data.vertical_end)) ? \'y:\'+vertical_start+\',\'+ vertical_end +\';\' : "";

		let scale_start = (!_.isEmpty(data.scale_start) && data.scale_start) ? data.scale_start / 100 : "1";
		let scale_end = (!_.isEmpty(data.scale_end) && data.scale_end) ? data.scale_end / 100 : "1";
		let scale = (!_.isEmpty(data.scale_start) || !_.isEmpty(data.scale_end)) ? \'scale:\'+scale_start+\',\'+ scale_end +\';\' : "";

		let rotate_start = (!_.isEmpty(data.rotate_start) && data.rotate_start) ? data.rotate_start : "0";
		let rotate_end = (!_.isEmpty(data.rotate_end) && data.rotate_end) ? data.rotate_end : "0";
		let rotate = (!_.isEmpty(data.rotate_start) || !_.isEmpty(data.rotate_end)) ? \'rotate:\'+rotate_start+\',\'+ rotate_end +\';\' : "";

		let opacity_start = (!_.isEmpty(data.opacity_start) && data.opacity_start) ? parseInt(data.opacity_start) / 100 : "1";
		let opacity_end = (!_.isEmpty(data.opacity_end) && data.opacity_end) ? parseInt(data.opacity_end) / 100 : "1";
		let opacity = (!_.isEmpty(data.opacity_start) || !_.isEmpty(data.opacity_end)) ? \'opacity:\'+opacity_start+\',\'+ opacity_end +\';\' : "";

		let easing = (!_.isEmpty(data.easing) && data.easing) ? parseInt(data.easing) / 100 : "";
		let easing_cls = (!_.isEmpty(data.easing)) ? \'easing:\'+easing+\';\' : "";

		let breakpoint = (!_.isEmpty(data.breakpoint) && data.breakpoint) ? data.breakpoint : "";
		let breakpoint_cls = (!_.isEmpty(data.breakpoint)) ? \'media:@\'+breakpoint+\';\' : "";

		let viewport = (!_.isEmpty(data.viewport) && data.viewport) ? parseInt(data.viewport) / 100 : "";
		let viewport_cls = (!_.isEmpty(data.viewport)) ? \'viewport:\'+viewport+\';\' : "";
		
		let target_cls = data.parallax_target ? \'target:!.jpb-section;\' : "";

		let animation = data.animation ? data.animation : "";
		let animation_repeat = (animation && data.animation_repeat) ? "; repeat: true;" : "";
		
		let zindex_cls = (animation == "parallax" && data.parallax_zindex) ? " uk-position-z-index uk-position-relative" : "";	
		if (animation == "parallax") {
			animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${target_cls}${breakpoint_cls}${viewport_cls}`;
		} else if (animation) {
			animation = ` uk-scrollspy="cls:uk-animation-${animation}${animation_repeat}"`;
		}

		let font_weight = ( data.font_weight ) ? " uk-text-"+data.font_weight : "";

		#>

		<div class="uk-card{{ card_style }}{{ zindex_cls }}{{ general }}"{{{ animation }}}>

		<# if ( title ) { #>
			<#
			let icon_arr = (typeof icon !== "undefined" && icon) ? icon.split(" ") : "";
			icon = icon_arr.length === 1 ? "fa "+icon : icon;
			#>

			<{{ heading_selector }} class="tm-title uk-margin-remove-bottom{{ heading_style }}{{ title_alignment }}">
			
			<# if ( icon && title_icon_position == "before" ) { #>
				<i class="uk-margin-small-right {{ icon }}" aria-hidden="true"></i>
				<span class="ui-title{{ font_weight }}">{{{ title }}}</span>
			<# } #>

			<# if ( icon && title_icon_position == "after" ) { #>
				<span class="ui-title{{ font_weight }}">{{{ title }}}</span>
				<i class="uk-margin-small-left {{ icon }}" aria-hidden="true"></i>
			<# } else if ( _.isEmpty( icon ) ) { #>
				{{{ title }}}
			<# } #>
			</{{ heading_selector }}>
		<# } #>

		<div class="tm-body-wrapper">

		<ul class="uk-list uk-margin-remove-bottom{{ list_style }}">
		
		<#
		_.each(data.ui_business_day_items, function(value, key){
			key++;
			let el_id = data.id + key;

			let business_day  = ( value.business_day ) ? value.business_day : "";
			let business_time = ( value.business_time ) ? value.business_time : "";
			let active        = ( value.active ) ? \' id="day-highlight-\'+key+\'"\' : "";
		#>
			<li{{{ active }}} class="uk-block">

			<# if ( !_.isEmpty( business_day ) ) { #>
				<# let business_days = _.split(business_day, "\n" );
				_.each(business_days, function(days){ #>
					<span>{{{ days }}}</span>
				<# }); #>
			<# } #>

			<# if ( !_.isEmpty( business_time ) ) { #>
				<# let business_times = _.split(business_time, "\n" );
				_.each(business_times, function(times){ #>
					<span class="uk-float-right">{{{ times }}}</span>
				<# }); #>
			<# } #>
			</li>
		<# }); #>

		</ul>
		</div>
		</div>

		';
		return $output;
	}
}
