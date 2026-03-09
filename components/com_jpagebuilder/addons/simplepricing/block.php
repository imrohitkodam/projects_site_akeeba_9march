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
class JpagebuilderAddonSimplePricing extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;

		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';

		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		// Options.

		$header_card = (isset ( $settings->header_card_style ) && $settings->header_card_style) ? ' uk-card-' . $settings->header_card_style : '';
		$header_card_size = (isset ( $settings->header_card_size ) && $settings->header_card_size) ? ' ' . $settings->header_card_size : '';

		$price_title = (isset ( $settings->price_title ) && $settings->price_title) ? $settings->price_title : '';

		$price_description = (isset ( $settings->price_description ) && $settings->price_description) ? $settings->price_description : '';

		$price = (isset ( $settings->price ) && $settings->price) ? $settings->price : '';
		$period = (isset ( $settings->period ) && $settings->period) ? $settings->period : '';
		$currency = (isset ( $settings->currency ) && $settings->currency) ? $settings->currency : '';

		$divider_type = (isset ( $settings->divider_type ) && $settings->divider_type) ? '<hr class="uk-divider-' . $settings->divider_type . '">' : '';

		$box_shadow = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$box_shadow .= (isset ( $settings->hover ) && $settings->hover) ? ' uk-box-shadow-hover-' . $settings->hover : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$label_text = (isset ( $settings->label_text ) && $settings->label_text) ? $settings->label_text : '';
		$label_styles = (isset ( $settings->label_styles ) && $settings->label_styles) ? ' ' . $settings->label_styles : ' uk-label';

		$text_color = (isset ( $settings->text_color ) && $settings->text_color) ? $settings->text_color : '';

		$list_marker = (isset ( $settings->list_marker ) && $settings->list_marker) ? $settings->list_marker : '';

		$list_styles = (isset ( $settings->list_styles ) && $settings->list_styles) ? ' uk-list-' . $settings->list_styles : '';
		$list_styles .= (isset ( $settings->list_marker ) && $settings->list_marker) ? ' uk-list-' . $settings->list_marker : '';
		$list_styles .= ($list_marker) ? ((isset ( $settings->list_marker_color ) && $settings->list_marker_color) ? ' uk-list-' . $settings->list_marker_color : '') : '';

		$list_styles .= (isset ( $settings->title_text_color ) && $settings->title_text_color) ? ' uk-text-' . $settings->title_text_color : '';
		$list_styles .= (isset ( $settings->scrollable ) && $settings->scrollable) ? ' ' . $settings->scrollable : '';

		$price_heading = (isset ( $settings->price_heading ) && $settings->price_heading) ? ' uk-' . $settings->price_heading : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		// Meta
		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';
		$meta_style .= (isset ( $settings->meta_text_transform ) && $settings->meta_text_transform) ? ' uk-text-' . $settings->meta_text_transform : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		// Remove margin for heading element
		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}
		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : '';

		$price_margin_top = (isset ( $settings->price_margin_top ) && $settings->price_margin_top) ? ' uk-margin-' . $settings->price_margin_top . '-top' : ' uk-margin-top';
		$link_title = ($price_title) ? ' title="' . $price_title . '"' : '';

		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$button_link = (isset ( $settings->button_link ) && $settings->button_link) ? $settings->button_link : '';
		$attribs = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? ' target="' . $settings->link_new_tab . '"' : '';
		$btn_styles = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' ' . $settings->button_size : '';
		$button_width = (isset ( $settings->button_width ) && $settings->button_width) ? ' uk-width-1-1' : '';

		$button_style_cls = '';

		if (empty ( $btn_styles )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size . $button_width;
		} elseif ($btn_styles == 'link' || $btn_styles == 'link-muted' || $btn_styles == 'link-text') {
			$button_style_cls .= 'uk-' . $btn_styles;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $btn_styles . $button_size . $button_width;
		}

		$btn_margin_top = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';

		$image_blend_bg_color = (isset ( $settings->image_blend_bg_color ) && $settings->image_blend_bg_color) ? $settings->image_blend_bg_color : '#1e87f0';
		if (! empty ( $image_blend_bg_color )) {
			$image_blend_bg_color = 'background-color: ' . $image_blend_bg_color . '; ';
		}

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

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}
		$use_header_background = (isset ( $settings->use_header_background ) && $settings->use_header_background) ? 1 : 0;

		$image = (isset ( $settings->header_image ) && $settings->header_image) ? $settings->header_image : '';
		$image_src = isset ( $image->src ) ? $image->src : $image;
		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}
		$bg_content_inverse = (isset ( $settings->bg_content_inverse ) && $settings->bg_content_inverse) ? ' uk-' . $settings->bg_content_inverse : '';

		$media_background = ($use_header_background) ? ((isset ( $settings->image_blend_bg_color ) && $settings->image_blend_bg_color) ? ' style="background-color: ' . $settings->image_blend_bg_color . ';"' : '') : '';

		$media_blend_mode = ($use_header_background) ? ((isset ( $settings->image_blend_modes ) && $settings->image_blend_modes) ? ' ' . $settings->image_blend_modes : '') : false;

		$media_overlay = ($use_header_background) ? ((isset ( $settings->media_overlay ) && $settings->media_overlay) ? '<div class="uk-position-cover" style="background-color: ' . $settings->media_overlay . '"></div>' : '') : '';

		$price_color = (isset ( $settings->price_color ) && $settings->price_color) ? ' uk-text-' . $settings->price_color : '';
		$divider_align = (isset ( $settings->divider_align ) && $settings->divider_align) ? $settings->divider_align : '';
		$currency_color = (isset ( $settings->currency_color ) && $settings->currency_color) ? ' uk-text-' . $settings->currency_color : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$icon_size = (isset ( $settings->icon_size ) && $settings->icon_size) ? '; width: ' . $settings->icon_size . '' : '';

		$button_link_new_tab = (isset ( $settings->button_link_new_tab ) && $settings->button_link_new_tab) ? 'target="_blank"' : '';

		$render_linkscroll = (empty ( $button_link_new_tab ) && strpos ( $button_link, '#' ) === 0) ? ' uk-scroll' : '';

		// Global Icon

		$all_icon_type = (isset ( $settings->all_icon_type ) && $settings->all_icon_type) ? $settings->all_icon_type : '';
		$all_icon_name = (isset ( $settings->all_icon_name ) && $settings->all_icon_name) ? $settings->all_icon_name : '';
		$all_uikit = (isset ( $settings->all_uikit ) && $settings->all_uikit) ? $settings->all_uikit : '';

		$all_icon_arr = array_filter ( explode ( ' ', $all_icon_name ) );
		if (count ( $all_icon_arr ) === 1) {
			$all_icon_name = 'fa ' . $all_icon_name;
		}

		$body_card_style = (isset ( $settings->body_card_styles ) && $settings->body_card_styles) ? $settings->body_card_styles : '';
		$count_feature_items = (isset ( $settings->ui_feature_items ) && $settings->ui_feature_items) ? $settings->ui_feature_items : array ();

		$output = '';

		// Output.
		$output .= '<div class="ui-simple-pricing' . $text_alignment . $box_shadow . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= ($label_text) ? '<div class="tm-price-table_featured f-2"><div class="tm-price-table_featured-inner' . $label_styles . '">' . $label_text . '</div></div>' : '';

		if ($use_header_background) {
			$output .= '<div class="tm-price-header uk-position-relative uk-background-norepeat uk-background-cover uk-background-center-center' . $media_blend_mode . $bg_content_inverse . '" data-src="' . $image_src . '"' . $media_background . ' uk-img>';
			$output .= $media_overlay;
			$output .= '<div class="uk-position-relative">';
		} else {

			if (! empty ( $header_card )) {
				$output .= '<div class="tm-price-header uk-card uk-margin-remove-first-child' . $header_card . '">';
			} else {
				$output .= '<div class="tm-price-header uk-panel uk-margin-remove-first-child">';
			}
		}

		if ($meta_alignment == 'top' && $price_description) {
			$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
			$output .= $price_description;
			$output .= '</' . $meta_element . '>';
		}

		if ($price_title) {
			$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $font_weight . '">';
			$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
			$output .= $price_title;
			$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
			$output .= '</' . $heading_selector . '>';
		}

		if (empty ( $meta_alignment ) && $price_description) {
			$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
			$output .= $price_description;
			$output .= '</' . $meta_element . '>';
		}

		$output .= ($divider_align == 'top') ? $divider_type : '';

		$output .= '<div class="tm-price-wrapper' . $price_margin_top . '">';
		$output .= ($currency) ? '<span class="tm-currency' . $currency_color . '">' . $currency . '</span>' : '';
		$output .= ($price) ? '<span class="tm-price' . $price_heading . $price_color . '">' . $price . '</span>' : '';
		$output .= ($period) ? '<span class="tm-period">' . $period . '</span>' : '';
		$output .= '</div>';

		$output .= (empty ( $divider_align )) ? $divider_type : '';

		if ($meta_alignment == 'content' && $price_description) {
			$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
			$output .= $price_description;
			$output .= '</' . $meta_element . '>';
		}

		$output .= ($use_header_background) ? '</div></div>' : '</div>';

		if (is_array ( $count_feature_items ) && count ( $count_feature_items ) > 1) {
			$output .= '<div class="tm-body uk-margin-remove-first-child">';
		}

		if (isset ( $settings->ui_feature_items ) && count ( ( array ) $settings->ui_feature_items )) {
			$output .= '<ul class="uk-list' . $list_styles . '">';
			foreach ( $settings->ui_feature_items as $key => $item ) {
				$title_link = (isset ( $item->title_link ) && $item->title_link) ? $item->title_link : '';
				$link_target = (isset ( $item->link_new_tab ) && $item->link_new_tab) ? 'target="_blank"' : '';

				$key ++;
				$icon_id = $this->addon->id . $key;

				$icon_type = (isset ( $item->icon_type ) && $item->icon_type) ? $item->icon_type : '';
				$icon = (isset ( $item->icon_name ) && $item->icon_name) ? $item->icon_name : '';
				$uk_icon = (isset ( $item->uikit ) && $item->uikit) ? $item->uikit : '';

				$icon_arr = array_filter ( explode ( ' ', $icon ) );
				if (count ( $icon_arr ) === 1) {
					$icon = 'fa ' . $icon;
				}

				if ($icon_type === 'fontawesome_icon') {
					$icon_render = '<i id="icon-' . $icon_id . '" class="' . $icon . '" aria-hidden="true"></i>';
				} else {
					$icon_render = '<span id="icon-' . $icon_id . '" class="uk-icon" uk-icon="icon: ' . $uk_icon . $icon_size . '"></span>';
				}

				if ($all_icon_type === 'fontawesome_icon') {
					$all_icon_render = '<i id="icon-' . $icon_id . '" class="' . $all_icon_name . '" aria-hidden="true"></i>';
				} else {
					$all_icon_render = '<span id="icon-' . $icon_id . '" class="uk-icon" uk-icon="icon: ' . $all_uikit . $icon_size . '"></span>';
				}

				$output .= '<li class="ui-item">';

				if ($icon_type || $all_icon_type) {
					$output .= '<div class="uk-grid-small uk-child-width-expand uk-flex-nowrap uk-flex-middle" uk-grid>';
					$output .= '<div class="uk-width-auto">';
					$output .= ($icon_type) ? $icon_render : $all_icon_render;
					$output .= '</div>';
					$output .= '<div>';
				}

				$output .= '<div class="el-content uk-panel">';
				$output .= ($title_link) ? '<a class="el-link uk-margin-remove-last-child" href="' . $title_link . '" ' . $link_target . '>' : '';
				$output .= $item->title;
				$output .= ($title_link) ? '</a>' : '';
				$output .= '</div>';

				if ($icon_type || $all_icon_type) {
					$output .= '</div>';
					$output .= '</div>';
				}

				$output .= '</li>';
			}
			$output .= '</ul>';
		}

		if (! empty ( $button_link )) {
			$output .= '<div class="' . $btn_margin_top . '"><a class="' . $button_style_cls . '" href="' . $button_link . '"' . $attribs . $render_linkscroll . $link_title . '>' . $button_title . '</a></div>';
		}

		if (is_array ( $count_feature_items ) && count ( $count_feature_items ) > 1) {
			$output .= '</div>';
		}

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

		$styles = array ();
		if (isset ( $settings->ui_feature_items ) && count ( ( array ) $settings->ui_feature_items )) {
			foreach ( $settings->ui_feature_items as $key => $item ) {
				$key ++;
				$icon_type = (isset ( $item->icon_type ) && $item->icon_type) ? $item->icon_type : '';
				$icon_id = $this->addon->id . $key;
				$icon_style = '';
				$icon_style .= (isset ( $item->icon_color ) && $item->icon_color) ? 'color: ' . $item->icon_color . ';' : '';
				$css = '';

				if ($icon_style && $icon_type) {
					$css .= $addon_id . ' .uk-list > li #icon-' . $icon_id . ' {';
					$css .= $icon_style;
					$css .= "\n" . '}' . "\n";
				}
				$styles [$key] = $css;
			}
		}
		$styles_explode = implode ( "\n", $styles );

		// return $styles_explode.

		$label_styles = (isset ( $settings->label_styles ) && $settings->label_styles) ? $settings->label_styles : '';
		$label_background_color = (isset ( $settings->label_background_color ) && $settings->label_background_color) ? 'background-color: ' . $settings->label_background_color . ';' : '';
		$label_color = (isset ( $settings->label_color ) && $settings->label_color) ? 'color: ' . $settings->label_color . ';' : '';

		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';

		$title_text_color = (isset ( $settings->title_text_color ) && $settings->title_text_color) ? $settings->title_text_color : '';
		$custom_title_text_color = (isset ( $settings->custom_title_text_color ) && $settings->custom_title_text_color) ? 'color: ' . $settings->custom_title_text_color . ';' : '';

		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';

		$price_color = (isset ( $settings->price_color ) && $settings->price_color) ? $settings->price_color : '';
		$custom_price_color = (isset ( $settings->custom_price_color ) && $settings->custom_price_color) ? 'color: ' . $settings->custom_price_color . ';' : '';

		$currency_color = (isset ( $settings->currency_color ) && $settings->currency_color) ? $settings->currency_color : '';
		$custom_currency_color = (isset ( $settings->custom_currency_color ) && $settings->custom_currency_color) ? 'color: ' . $settings->custom_currency_color . ';' : '';

		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';

		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$heading_fontsize = (isset ( $settings->price_fontsize ) && $settings->price_fontsize) ? 'font-size: ' . $settings->price_fontsize . 'px; ' : '';
		$currency_fontsize = (isset ( $settings->currency_fontsize ) && $settings->currency_fontsize) ? 'font-size: ' . $settings->currency_fontsize . 'px; ' : '';
		$currency_margin = (isset ( $settings->currency_margin ) && $settings->currency_margin) ? $settings->currency_margin : '15';

		$price_padding_left = (isset ( $settings->price_padding_left ) && $settings->price_padding_left) ? 'padding-left: ' . $settings->price_padding_left . 'px; ' : '';

		$header_card_style = (isset ( $settings->header_card_style ) && $settings->header_card_style) ? $settings->header_card_style : '';

		$header_padding_top = (isset ( $settings->header_padding_top ) && $settings->header_padding_top) ? 'padding-top: ' . $settings->header_padding_top . 'px;' : '';
		$header_padding_bottom = (isset ( $settings->header_padding_bottom ) && $settings->header_padding_bottom) ? 'padding-bottom: ' . $settings->header_padding_bottom . 'px;' : '';
		$header_padding_left = (isset ( $settings->header_padding_left ) && $settings->header_padding_left) ? 'padding-left: ' . $settings->header_padding_left . 'px;' : '';
		$header_padding_right = (isset ( $settings->header_padding_right ) && $settings->header_padding_right) ? 'padding-right: ' . $settings->header_padding_right . 'px;' : '';

		$header_background_color = (isset ( $settings->header_background_color ) && $settings->header_background_color) ? 'background-color: ' . $settings->header_background_color . ';' : '';

		$card_style = (isset ( $settings->card_style ) && $settings->card_style) ? $settings->card_style : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? $settings->card_size : '';

		$body_padding_top = (isset ( $settings->body_padding_top ) && $settings->body_padding_top) ? 'padding-top: ' . $settings->body_padding_top . 'px;' : '';
		$body_padding_bottom = (isset ( $settings->body_padding_bottom ) && $settings->body_padding_bottom) ? 'padding-bottom: ' . $settings->body_padding_bottom . 'px;' : '';
		$body_padding_left = (isset ( $settings->body_padding_left ) && $settings->body_padding_left) ? 'padding-left: ' . $settings->body_padding_left . 'px;' : '';
		$body_padding_right = (isset ( $settings->body_padding_right ) && $settings->body_padding_right) ? 'padding-right: ' . $settings->body_padding_right . 'px;' : '';

		$divider_type = (isset ( $settings->divider_type ) && $settings->divider_type) ? $settings->divider_type : '';
		$divider_color = (isset ( $settings->divider_color ) && $settings->divider_color) ? ' border-top-color: ' . $settings->divider_color . ';' : '';
		$divider_height = (isset ( $settings->divider_height ) && $settings->divider_height) ? ' border-top-width: ' . $settings->divider_height . 'px;' : '';

		$body_background_color = (isset ( $settings->body_background_color ) && $settings->body_background_color) ? 'background-color: ' . $settings->body_background_color . ';' : '';

		$all_icon_type = (isset ( $settings->all_icon_type ) && $settings->all_icon_type) ? $settings->all_icon_type : '';
		$all_icon_color = (isset ( $settings->all_icon_color ) && $settings->all_icon_color) ? 'color: ' . $settings->all_icon_color . ';' : '';

		$price_heading = (isset ( $settings->price_heading ) && $settings->price_heading) ? $settings->price_heading : '';

		$price_css = '';

		if (! empty ( $divider_type ) && $divider_type === 'small') {
			$price_css .= $addon_id . ' .uk-divider-small::after {' . $divider_color . $divider_height . '}';
		}
		if ($all_icon_type && $all_icon_color) {
			$price_css .= $addon_id . ' .uk-list .ui-item .uk-icon {' . $all_icon_color . '}';
		}
		if (empty ( $title_color ) && $custom_title_color) {
			$price_css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$price_css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if (empty ( $title_text_color ) && $custom_title_text_color) {
			$price_css .= $addon_id . ' .uk-list .ui-item {' . $custom_title_text_color . '}';
		}
		if (empty ( $price_color ) && $custom_price_color) {
			$price_css .= $addon_id . ' .tm-price {' . $custom_price_color . '}';
		}
		if (empty ( $currency_color ) && $custom_currency_color) {
			$price_css .= $addon_id . ' .tm-currency {' . $custom_currency_color . '}';
		}
		if ($content_color) {
			$price_css .= $addon_id . ' .ui-content {' . $content_color . '}';
		}
		if ($label_styles == 'uk-label-custom' && $label_background_color) {
			$price_css .= $addon_id . ' .uk-label-custom {' . $label_background_color . $label_color . '}';
		}
		if ($button_style == 'custom') {
			if ($button_background || $button_color) {
				$price_css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$price_css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}
		if (! empty ( $heading_fontsize ) && empty ( $price_heading )) {
			$price_css .= $addon_id . ' .tm-price {' . $heading_fontsize . '}';
		}

		if (! empty ( $currency_fontsize )) {
			$price_css .= $addon_id . ' .tm-currency {' . $currency_fontsize . '}';
		}

		if (! empty ( $price_padding_left )) {
			$price_css .= $addon_id . ' .price-wrapper {' . $price_padding_left . '}';
		}

		if ($header_padding_top || $header_padding_bottom || $header_padding_left || $header_padding_right) {
			$price_css .= $addon_id . ' .tm-price-header { ' . $header_padding_top . $header_padding_bottom . $header_padding_left . $header_padding_right . '}';
		}

		if ($header_card_style == 'custom' && $header_background_color) {
			$price_css .= $addon_id . ' .uk-card-custom {' . $header_background_color . '}';
		}

		if ($body_padding_top || $body_padding_bottom || $body_padding_left || $body_padding_right) {
			$price_css .= $addon_id . ' .tm-body {' . $body_padding_top . $body_padding_bottom . $body_padding_left . $body_padding_right . '}';
		}

		if ($body_background_color) {
			$price_css .= $addon_id . ' .tm-body {' . $body_background_color . '}';
		}
		if ($currency_margin) {
			$price_css .= $addon_id . ' .pricing-symbol {margin-top:' . $currency_margin . 'px;}';
		}
		$price_css .= $addon_id . ' .tm-currency { display: inline-block; margin-right: 5px; vertical-align: top; }';

		return $price_css . $styles_explode;
	}
}
