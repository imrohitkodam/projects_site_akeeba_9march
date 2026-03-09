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
class JpagebuilderAddonButtonGroup extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$heading_addon_margin = (isset ( $settings->heading_addon_margin ) && $settings->heading_addon_margin) ? $settings->heading_addon_margin : '';
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= ($heading_addon_margin) ? ' uk-margin' . (($heading_addon_margin == 'default') ? '' : '-' . $heading_addon_margin) : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

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

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= $max_width_cfg;

		$button_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' uk-flex ' . $settings->alignment : '';
		$button_breakpoint = ($button_alignment) ? ((isset ( $settings->button_breakpoint ) && $settings->button_breakpoint) ? '@' . $settings->button_breakpoint : '') : '';
		$button_alignment_fallback = ($button_alignment && $button_breakpoint) ? ((isset ( $settings->button_alignment_fallback ) && $settings->button_alignment_fallback) ? ' ' . $settings->button_alignment_fallback : '') : '';
		$button_alignment .= $button_breakpoint . $button_alignment_fallback;

		// Parallax Animation.
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

		$delay_element_animations = (isset ( $settings->delay_element_animations ) && $settings->delay_element_animations) ? $settings->delay_element_animations : '';
		$scrollspy_cls = ($delay_element_animations) ? ' uk-scrollspy-class' : '';
		$scrollspy_target = ($delay_element_animations) ? 'target: [uk-scrollspy-class]; ' : '';
		$animation_delay = ($delay_element_animations) ? ' delay: 200;' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . $target_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="' . $scrollspy_target . 'cls: uk-animation-' . $animation . ';' . $animation_repeat . $animation_delay . '"';
		}

		$size = (isset ( $settings->size ) && $settings->size) ? ' ' . $settings->size : '';

		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$output = '';

		$output .= '<div class="uikit-addon-button-group' . $zindex_cls . $general . '"' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}
		$output .= '<div class="uk-button-group' . $button_alignment . '">';

		if (isset ( $settings->ui_button_group_item ) && count ( ( array ) $settings->ui_button_group_item )) {
			foreach ( $settings->ui_button_group_item as $key => $value ) {
				if ((isset ( $value->title ) && $value->title) || (isset ( $value->icon ) && $value->icon)) {

					list ( $link, $target ) = JpagebuilderAddonHelper::parseLink ( $value, 'url', [ 
							'new_tab' => 'target',
							'url' => 'url'
					] );

					$link_title = (isset ( $value->link_title ) && $value->link_title) ? ' title="' . $value->link_title . '"' : '';
					$ariaLabel = ! empty ( $value->link_aria_label ) ? ' aria-label="' . $value->link_aria_label . '"' : '';

					$button_style = (isset ( $value->button_style ) && $value->button_style) ? $value->button_style : '';

					$button_style_cls = '';
					if (empty ( $button_style )) {
						$button_style_cls .= ' uk-button uk-button-default' . $size;
					} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
						$button_style_cls .= ' uk-' . $button_style;
					} else {
						$button_style_cls .= ' uk-button uk-button-' . $button_style . $size;
					}

					$text = (isset ( $value->title ) && $value->title) ? $value->title : '';
					$icon = (isset ( $value->btn_icon ) && $value->btn_icon) ? $value->btn_icon : '';
					$icon_position = (isset ( $value->icon_position ) && $value->icon_position) ? $value->icon_position : 'left';
					$icon_arr = array_filter ( explode ( ' ', $icon ) );
					if (count ( $icon_arr ) === 1) {
						$icon = 'fa ' . $icon;
					}

					if ($icon_position == 'left') {
						$text = ($icon) ? '<i class="' . $icon . '" aria-hidden="true"></i> ' . $text : $text;
					} else {
						$text = ($icon) ? $text . ' <i class="' . $icon . '" aria-hidden="true"></i>' : $text;
					}

					$output .= '<a href="' . $link . '" class="ui-item-' . $key . $button_style_cls . $font_weight . '"' . $target . $ariaLabel . $link_title . $scrollspy_cls . '>' . $text . '</a>';
				}
			}
		}
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$title_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? $settings->title_heading_decoration : '';
		$decoration_color = '';
		$decoration_color .= (isset ( $settings->title_heading_decoration_color ) && $settings->title_heading_decoration_color) ? ' border-color: ' . $settings->title_heading_decoration_color . ';' : '';
		$decoration_color .= (isset ( $settings->title_heading_decoration_width ) && $settings->title_heading_decoration_width) ? ' border-width: ' . $settings->title_heading_decoration_width . 'px;' : '';

		$css = '';

		// Buttons style
		if (isset ( $settings->ui_button_group_item ) && count ( ( array ) $settings->ui_button_group_item )) {
			foreach ( $settings->ui_button_group_item as $key => $value ) {
				$link_button_style = (isset ( $value->button_style ) && $value->button_style) ? $value->button_style : '';
				$button_background = (isset ( $value->button_background ) && $value->button_background) ? 'background-color: ' . $value->button_background . ';' : '';
				$button_color = (isset ( $value->button_color ) && $value->button_color) ? 'color: ' . $value->button_color . ';' : '';

				$button_background_hover = (isset ( $value->button_background_hover ) && $value->button_background_hover) ? 'background-color: ' . $value->button_background_hover . ';' : '';
				$button_hover_color = (isset ( $value->button_hover_color ) && $value->button_hover_color) ? 'color: ' . $value->button_hover_color . ';' : '';

				if ($link_button_style == 'custom') {
					if ($button_background || $button_color) {
						$css .= $addon_id . ' .ui-item-' . $key . '.uk-button-custom {' . $button_background . $button_color . '}';
					}
					if ($button_background_hover || $button_hover_color) {
						$css .= $addon_id . ' .ui-item-' . $key . '.uk-button-custom:hover, ' . $addon_id . ' .ui-item-' . $key . '.uk-button-custom:focus, ' . $addon_id . ' .ui-item-' . $key . '.uk-button-custom:active {' . $button_background_hover . $button_hover_color . '}';
					}
				}
			}
		}
		if ($decoration_color && $title_decoration) {
			$css .= "\n";
			$css .= $addon_id . ' .uk-heading-bullet::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::after {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-line>::before {' . $decoration_color . '}';
			$css .= $addon_id . ' .uk-heading-divider {' . $decoration_color . '}';
			$css .= "\n";
		}

		return $css;
	}
	public static function getFrontendEditor() {
		$output = '

		<style type="text/css">
		<# _.each(data.ui_button_group_item, function(button, key){ #>
			<# let button_style = ( button.button_style ) ? button.button_style : ""; #>
			<# if( button_style == "custom" ) { #>
				#jpb-addon-{{ data.id }} .ui-item-{{ key }}.uk-button-custom {
					<# if(button.button_background) { #>
						background-color: {{ button.button_background }};
					<# } #>
					<# if(button.button_color) { #>
						color: {{ button.button_color }};
					<# } #>
				}
				#jpb-addon-{{ data.id }} .ui-item-{{ key }}.uk-button-custom:hover {
					<# if(button.button_background_hover) { #>
						background-color: {{ button.button_background_hover }};
					<# } #>
					<# if(button.button_hover_color) { #>
					color: {{ button.button_hover_color }};
					<# } #>
				}
				<# } #>
		<# }); #>

		<# if(data.title_heading_decoration_color && !_.isEmpty(data.title_heading_decoration)) { #>
			#jpb-addon-{{ data.id }} .uk-heading-bullet::before {
				border-color: {{data.title_heading_decoration_color}};
				border-width: {{data.title_heading_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::after {
				border-color: {{data.title_heading_decoration_color}};
				border-width: {{data.title_heading_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-line>::before {
				border-color: {{data.title_heading_decoration_color}};
				border-width: {{data.title_heading_decoration_width}}px;
			}
			#jpb-addon-{{ data.id }} .uk-heading-divider {
				border-color: {{data.title_heading_decoration_color}};
				border-width: {{data.title_heading_decoration_width}}px;
			}
		<# } #>
		</style>
		<#
		let heading_addon_margin = data.heading_addon_margin || "";

		var title_style = "";
		title_style = data.title_heading_style ? " uk-"+data.title_heading_style : "";
		title_style += data.title_heading_color ? " uk-"+data.title_heading_color : "";
		title_style += ( heading_addon_margin ) ? " uk-margin" + (( heading_addon_margin == "default" ) ? "" : "-" + heading_addon_margin ) : "";
		
		let title_heading_selector = data.title_heading_selector || "h3";
		var title_heading_decoration = data.title_heading_decoration ? " "+data.title_heading_decoration : "";

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
		general += max_width_cfg;

		let addon_margin = data.addon_margin || "";

		var general = "";
		
		general += ( addon_margin ) ? " uk-margin" + (( addon_margin == "default" ) ? "" : "-" + addon_margin ) : "";
		general += ( data.visibility ) ? " " + data.visibility : "";
		general += ( data.class ) ? " " + data.class : "";

		let button_alignment          = ( data.alignment ) ? " uk-flex "+ data.alignment : "";
		let button_breakpoint         = ( button_alignment ) ? ( ( data.button_breakpoint ) ? "@"+data.button_breakpoint : "" ) : "";
		let button_alignment_fallback = ( button_alignment && button_breakpoint ) ? ( ( data.button_fallback ) ? " "+data.button_fallback : "" ) : "";
		button_alignment         += button_breakpoint + button_alignment_fallback;

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
		let parallax_zindex = ( data.parallax_zindex ) ? data.parallax_zindex : "";
		let zindex_cls = (animation == "parallax" && parallax_zindex) ? " uk-position-z-index uk-position-relative" : "";	

		let animation_repeat = (animation && data.animation_repeat) ? " repeat: true;" : "";
		let delay_element_animations = ( data.delay_element_animations ) ? data.delay_element_animations : "";
		
		let scrollspy_cls            = ( delay_element_animations ) ? " uk-scrollspy-class" : "";
		var scrollspy_target         = ( delay_element_animations ) ? \'target: [uk-scrollspy-class]; \' : "";
		let animation_delay          = ( delay_element_animations ) ? " delay: 200" : "";

		if (animation == "parallax") {
			animation = ` uk-parallax=${horizontal}${vertical}${scale}${rotate}${opacity}${easing_cls}${target_cls}${breakpoint_cls}${viewport_cls}`;
		} else if ( !_.isEmpty( animation ) ) {
			animation = ` uk-scrollspy="${scrollspy_target}cls: uk-animation-${animation};${animation_repeat}${animation_delay}"`;
		}

		let size = ( data.size ) ? " " + data.size : "";
		let font_weight = ( data.font_weight ) ? " uk-text-" + data.font_weight : "";

		#>

		<# if( !_.isEmpty( data.title_addon ) ){ #>
			<{{ title_heading_selector }} class="tm-addon-title{{ title_style }}{{ title_heading_decoration }}">
				<# if (title_heading_decoration == " uk-heading-line") { #><span> <# } #>
					{{{ data.title_addon }}}
			 	<# if (title_heading_decoration == " uk-heading-line") { #></span> <# } #>
			</{{ title_heading_selector }}>
		<# } #>

		<div class="uikit-addon-button-group{{ zindex_cls }}{{ general }}"{{{ animation }}}>

		<div class="uk-button-group{{ button_alignment }}">
		<#
		_.each(data.ui_button_group_item, function(value, key){

			if ( ( value.title ) || ( value.icon ) ) {

				const isUrlObj = _.isObject(value?.url) && (!!value?.url?.url || !!value?.url?.page || !!value?.url?.menu);
				const isUrlString = _.isString(value?.url) && value?.url !== "";
				
				const isTarget = value?.link_open_new_window ? "_blank" : "";
				const urlObj = value?.url?.url ? value?.url : window.getSiteUrl(value?.url, isTarget);
				const {url, new_tab, nofollow, type, } = urlObj;
				const target = new_tab ? "_blank" : "";
				
				const rel = nofollow ? "noopener noreferrer" : "";
				const buttonUrl= (type === "url" && url) || (type === "menu" && urlObj.menu) || ((type === "page" && !!urlObj.page) && "index.php?option=com_jpagebuilder&view=page&id=" + urlObj.page) || "";
	
				let button_style = ( value.button_style ) ? value.button_style : "";
				let link_title    = ( value.link_title ) ? \' title="\'+value.link_title.replace(/"/g, "")+\'"\' : "" ;
				let ariaLabel = ( value.link_aria_label ) ? \' aria-label="\' + value.link_aria_label + \'" \' : "";

				var button_style_cls = "";
				if (_.isEmpty(button_style)) {
					button_style_cls += " uk-button uk-button-default" + size;
				} else if ( button_style == "link" || button_style == "link-muted" || button_style == "link-text") {
					button_style_cls += " uk-" + button_style;
				} else {
					button_style_cls += " uk-button uk-button-" + button_style + size;
				}

				let text          = ( value.title ) ? value.title : "";
				let icon          = ( value.btn_icon ) ? value.btn_icon : "";

				let icon_position = ( value.icon_position ) ? value.icon_position : "left";

				let icon_arr = (typeof icon !== "undefined" && icon) ? icon.split(" ") : "";
				icon = icon_arr.length === 1 ? "fa "+icon : icon;

				if ( icon_position == "left" ) {
					text = ( icon ) ? \'<i class="\' + icon + \'" aria-hidden="true"></i> \' + text : text;
				} else {
					text = ( icon ) ? text + \' <i class="\' + icon + \'" aria-hidden="true"></i>\' : text;
				}
			#>
				<a href=\'{{ buttonUrl }}\' class="ui-item-{{ key }}{{ button_style_cls }}{{ font_weight }}" target=\'{{ target }}\'{{{ ariaLabel }}} rel=\'{{ rel }}\'{{{ link_title }}}{{ scrollspy_cls }}>{{{ text }}}</a>
			<# } #>
		<# }); #>
		
		</div>
		</div>

		';
		return $output;
	}
}
