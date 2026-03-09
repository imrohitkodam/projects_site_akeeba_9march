<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonSimpleGallery extends JpagebuilderAddons {
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

		$grid_parallax = (isset ( $settings->grid_parallax ) && $settings->grid_parallax) ? $settings->grid_parallax : '';
		$gallery_parallax = ($grid_parallax) ? 'parallax: ' . $grid_parallax . '' : '';
		$masonry = (isset ( $settings->masonry ) && $settings->masonry) ? 1 : 0;
		$masonry_cls = ($masonry) ? 'masonry: true;' : '';

		$grid_column_gap = (isset ( $settings->grid_column_gap ) && $settings->grid_column_gap) ? $settings->grid_column_gap : '';
		$grid_row_gap = (isset ( $settings->grid_row_gap ) && $settings->grid_row_gap) ? $settings->grid_row_gap : '';

		$divider = ($grid_column_gap != 'collapse' && $grid_row_gap != 'collapse') ? (isset ( $settings->divider ) && $settings->divider) ? 1 : 0 : '';

		$column_align = (isset ( $settings->grid_column_align ) && $settings->grid_column_align) ? 1 : 0;
		$row_align = (isset ( $settings->grid_row_align ) && $settings->grid_row_align) ? 1 : 0;

		$phone_portrait = (isset ( $settings->phone_portrait ) && $settings->phone_portrait) ? $settings->phone_portrait : '';
		$phone_landscape = (isset ( $settings->phone_landscape ) && $settings->phone_landscape) ? $settings->phone_landscape : '';
		$tablet_landscape = (isset ( $settings->tablet_landscape ) && $settings->tablet_landscape) ? $settings->tablet_landscape : '';
		$desktop = (isset ( $settings->desktop ) && $settings->desktop) ? $settings->desktop : '';
		$large_screens = (isset ( $settings->large_screens ) && $settings->large_screens) ? $settings->large_screens : '';

		$grid = '';

		$grid .= ($phone_portrait) ? ' uk-child-width-' . (($phone_portrait == 'auto') ? '' : '1-') . $phone_portrait : '';
		$grid .= ($phone_landscape) ? ' uk-child-width-' . (($phone_landscape == 'auto') ? '' : '1-') . $phone_landscape . '@s' : '';
		$grid .= ($tablet_landscape) ? ' uk-child-width-' . (($tablet_landscape == 'auto') ? '' : '1-') . $tablet_landscape . '@m' : '';
		$grid .= ($desktop) ? ' uk-child-width-' . (($desktop == 'auto') ? '' : '1-') . '' . $desktop . '@l' : '';
		$grid .= ($large_screens) ? ' uk-child-width-' . (($large_screens == 'auto') ? '' : '1-') . $large_screens . '@xl' : '';

		$grid .= ($divider) ? ' uk-grid-divider' : '';
		$grid .= ($column_align) ? ' uk-flex-center' : '';
		$grid .= ($row_align) ? ' uk-flex-middle' : '';

		if ($grid_column_gap == $grid_row_gap) {
			$grid .= (! empty ( $grid_column_gap ) && ! empty ( $grid_row_gap )) ? ' uk-grid-' . $grid_column_gap : '';
		} else {
			$grid .= ! empty ( $grid_column_gap ) ? ' uk-grid-column-' . $grid_column_gap : '';
			$grid .= ! empty ( $grid_row_gap ) ? ' uk-grid-row-' . $grid_row_gap : '';
		}

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

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
		$delay_element_animations = (isset ( $settings->delay_element_animations ) && $settings->delay_element_animations) ? $settings->delay_element_animations : '';
		$scrollspy_cls = ($delay_element_animations) ? ' uk-scrollspy-class' : '';
		$scrollspy_target = ($delay_element_animations) ? 'target: [uk-scrollspy-class]; ' : '';
		$animation_delay = ($delay_element_animations) ? ' delay: 200;' : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="' . $scrollspy_target . 'cls: uk-animation-' . $animation . ';' . $animation_repeat . $animation_delay . '"';
		}

		$lightbox = (isset ( $settings->lightbox ) && $settings->lightbox) ? 1 : 0;
		$lightbox_cls = ($lightbox) ? ' uk-lightbox="toggle: a[data-type];"' : '';

		$overlay_mode = (isset ( $settings->overlay_mode ) && $settings->overlay_mode) ? $settings->overlay_mode : 'cover';

		$overlay_on_hover = (isset ( $settings->overlay_on_hover ) && $settings->overlay_on_hover) ? $settings->overlay_on_hover : 0;
		$overlay_transition_background = (isset ( $settings->overlay_transition_background ) && $settings->overlay_transition_background) ? $settings->overlay_transition_background : 0;
		$check_animate_bg = $overlay_mode == 'cover' && $overlay_on_hover && $overlay_transition_background;
		$title_transition = ($overlay_on_hover) ? ((isset ( $settings->title_transition ) && $settings->title_transition) ? ' uk-transition-' . $settings->title_transition : '') : false;

		$content_transition = ($overlay_on_hover) ? ((isset ( $settings->content_transition ) && $settings->content_transition) ? ' uk-transition-' . $settings->content_transition : '') : false;

		$meta_transition = ($overlay_on_hover) ? ((isset ( $settings->meta_transition ) && $settings->meta_transition) ? ' uk-transition-' . $settings->meta_transition : '') : false;

		$icon_transition = ($overlay_on_hover) ? ((isset ( $settings->icon_transition ) && $settings->icon_transition) ? ' uk-transition-' . $settings->icon_transition : '') : false;

		$overlay_transition = ($overlay_on_hover) ? ((isset ( $settings->overlay_transition ) && $settings->overlay_transition) ? ' uk-transition-' . $settings->overlay_transition : '') : false;

		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '';

		$image_transition_hover = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . '' : ' uk-transition-fade';

		$overlay_positions = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? $settings->overlay_positions : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles : '';

		$overlay_styles_int = ($overlay_styles) ? 'uk-overlay' : 'uk-panel';

		$overlay_padding_init = '';
		$overlay_padding = (isset ( $settings->overlay_padding ) && $settings->overlay_padding) ? $settings->overlay_padding : '';

		if (empty ( $overlay_styles ) && empty ( $overlay_padding )) {
			$overlay_padding_init = ' uk-padding';
		} elseif (empty ( $overlay_styles ) && $overlay_padding == 'remove') {
			$overlay_padding_init = '';
		} elseif (! empty ( $overlay_padding )) {
			$overlay_padding_init = ' uk-padding-' . $overlay_padding;
		}

		$overlay_cover = ! empty ( $overlay_styles ) && $overlay_mode == 'cover';

		$overlay_margin = ($overlay_styles) ? ((isset ( $settings->overlay_margin ) && $settings->overlay_margin) ? ' uk-position-' . $settings->overlay_margin : '') : '';

		// Inverse text color on hover
		$inverse_text_color = ($overlay_mode == 'cover' && $overlay_on_hover && $overlay_transition_background);

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';

		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

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

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$image_styles = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->hover_box_shadow : '';

		$show_lightbox_title = (isset ( $settings->show_lightbox_title ) && $settings->show_lightbox_title) ? $settings->show_lightbox_title : '';
		$show_lightbox_content = (isset ( $settings->show_lightbox_content ) && $settings->show_lightbox_content) ? $settings->show_lightbox_content : '';

		// New options.
		$link_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? 'target="_blank"' : '';

		$overlay_text_color = (isset ( $settings->overlay_text_color ) && $settings->overlay_text_color) ? $settings->overlay_text_color : '';

		$link_title = (isset ( $settings->link_title ) && $settings->link_title) ? 1 : 0;
		$link_title_hover = (isset ( $settings->title_hover_style ) && $settings->title_hover_style) ? ' class="uk-link-' . $settings->title_hover_style . '"' : '';
		$overlay_link = (isset ( $settings->overlay_link ) && $settings->overlay_link) ? 1 : 0;

		$button_margin = (isset ( $settings->button_margin_top ) && $settings->button_margin_top) ? 'uk-margin-' . $settings->button_margin_top . '-top' : 'uk-margin-top';
		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_size = (isset ( $settings->button_size ) && $settings->button_size) ? ' uk-button-' . $settings->button_size : '';
		$button_style_cls = '';

		if (empty ( $button_style )) {
			$button_style_cls .= 'uk-button uk-button-default' . $button_size;
		} elseif ($button_style == 'link' || $button_style == 'link-muted' || $button_style == 'link-text') {
			$button_style_cls .= 'uk-' . $button_style;
		} else {
			$button_style_cls .= 'uk-button uk-button-' . $button_style . $button_size;
		}

		$button_transition = ($overlay_on_hover) ? ((isset ( $settings->button_transition ) && $settings->button_transition) ? ' uk-transition-' . $settings->button_transition : '') : false;

		$overlay_maxwidth = (isset ( $settings->overlay_maxwidth ) && $settings->overlay_maxwidth) ? ' uk-width-' . $settings->overlay_maxwidth : '';

		$thumb_width = (isset ( $settings->thumb_width ) && $settings->thumb_width) ? ' width="' . $settings->thumb_width . '"' : '';
		$thumb_height = (isset ( $settings->thumb_height ) && $settings->thumb_height) ? ' height="' . $settings->thumb_height . '"' : '';

		if ($thumb_width || $thumb_height) {
			$item_maxwidth = '';
		} else {
			$item_maxwidth = (isset ( $settings->item_maxwidth ) && $settings->item_maxwidth) ? ' uk-margin-auto uk-width-' . $settings->item_maxwidth : '';
		}
		$icon_text_color = $overlay_mode == 'icon' ? ((isset ( $settings->icon_text_color ) && $settings->icon_text_color) ? ' uk-' . $settings->icon_text_color : '') : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$output = '';

		$output .= '<div class="ui-simple-gallery' . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-addon-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="' . $text_alignment . $grid . '" uk-grid="' . $masonry_cls . $gallery_parallax . '"' . $lightbox_cls . '>';

		$mediaItemFound = null;
		if (isset ( $settings->ui_simple_gallery_item ) && count ( ( array ) $settings->ui_simple_gallery_item )) {
			foreach ( $settings->ui_simple_gallery_item as $key => $value ) {
				$media_item_thumb = (isset ( $value->media_item_thumb ) && $value->media_item_thumb) ? $value->media_item_thumb : '';
				$image_thumb_src = isset ( $media_item_thumb->src ) ? $media_item_thumb->src : $media_item_thumb;
				if (strpos ( $image_thumb_src, 'http://' ) !== false || strpos ( $image_thumb_src, 'https://' ) !== false) {
					$image_thumb_src = $image_thumb_src;
				} elseif ($image_thumb_src) {
					$image_thumb_src = Uri::base ( true ) . '/' . $image_thumb_src;
				}

				$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
				$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
				
				if(!$mediaItemFound && ($media_item || $image_thumb_src)) {
					$mediaItemFound = true;
				}

				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}
				$media_item_hover = (isset ( $value->media_item_hover ) && $value->media_item_hover) ? $value->media_item_hover : '';
				$image_hover_src = isset ( $media_item_hover->src ) ? $media_item_hover->src : $media_item_hover;
				if (strpos ( $image_hover_src, 'http://' ) !== false || strpos ( $image_hover_src, 'https://' ) !== false) {
					$image_hover_src = $image_hover_src;
				} elseif ($image_hover_src) {
					$image_hover_src = Uri::base ( true ) . '/' . $image_hover_src;
				}

				$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

				$title_link = (isset ( $value->title_link->src ) && $value->title_link->src) ? $value->title_link->src : '';

				$check_target = (isset ( $settings->link_new_tab ) && $settings->link_new_tab) ? $settings->link_new_tab : '';
				$render_linkscroll = (empty ( $check_target ) && strpos ( $title_link, '#' ) === 0) ? ' uk-scroll' : '';

				$lightbox_init = (! empty ( $title_link )) ? ' data-type="iframe"' : ' data-type="image"';

				// Special item color based on overlay styles

				$item_color = '';
				if (empty ( $overlay_styles ) || $overlay_mode == 'cover') {
					$item_color = (isset ( $value->item_color ) && $value->item_color) ? 'uk-' . $value->item_color : $overlay_text_color;
				}

				$data_alt_init = (! empty ( $image_alt )) ? ' data-alt="' . str_replace ( '"', '', $image_alt ) . '"' : '';
				$data_caption_title = ($show_lightbox_title != 'title-ovl') ? str_replace ( '"', '', (isset ( $value->title ) ? '<h4 class=\'uk-margin-remove\'>' . $value->title . '</h4>' : '') ) : '';
				$data_caption_content = ($show_lightbox_content != 'content-ovl') ? str_replace ( '"', '', (isset ( $value->content ) ? $value->content : '') ) : '';

				$data_caption_init = (! empty ( $data_caption_title || $data_caption_content )) ? ' data-caption="' . $data_caption_title . $data_caption_content . '"' : '';

				$tab_transition = ($overlay_on_hover || $media_item_hover || ! empty ( $image_transition )) ? ' tabindex="0"' : '';
				$toggle_transition = ($overlay_on_hover || $media_item_hover || ! empty ( $image_transition )) ? ' uk-transition-toggle' : '';

				// Helper
				$helper_color = empty ( $overlay_styles ) || $overlay_mode == 'cover';
				$helper_toggle = $inverse_text_color || $overlay_text_color && (empty ( $overlay_styles ) && $media_item_hover || $overlay_cover && $overlay_on_hover && $overlay_transition_background);
				$helper = $helper_color || $helper_toggle;

				$output .= '<div>';

				$output .= ($helper) ? '<div' . ($helper_color && $item_color ? ' class="' . $item_color . '"' : '') . ($helper_toggle ? ' uk-toggle="cls: uk-light uk-dark; mode: hover"' : '') . '>' : '';

				if ($overlay_link && $title_link) {
					$output .= ($lightbox) ? '<a class="ui-item' . $item_maxwidth . ' uk-inline-clip' . $toggle_transition . $image_styles . ' uk-link-toggle" href="' . $title_link . '"' . $lightbox_init . $data_alt_init . $data_caption_init . $tab_transition . $scrollspy_cls . '>' : '<a class="ui-item' . $item_maxwidth . $item_color . ' uk-inline-clip' . $toggle_transition . $image_styles . ' uk-link-toggle" href="' . $title_link . '"' . $link_target . $render_linkscroll . $tab_transition . $scrollspy_cls . '>';
				} else {
					$output .= '<div class="ui-item' . $item_maxwidth . ' uk-inline-clip' . $toggle_transition . $image_styles . '"' . $tab_transition . $scrollspy_cls . '>';
				}

				$output .= $image_src || $image_thumb_src ? '<img class="ui-image' . ($media_item_hover ? '' : $image_transition) . '" src="' . (empty ( $image_thumb_src ) ? $image_src : $image_thumb_src) . '" ' . $image_alt_init . $thumb_width . $thumb_height . '>' : '';

				if ($image_hover_src) {
					$output .= '<div class="uk-position-cover' . $image_transition_hover . '">';

					$output .= '<img class="ui-image" src="' . $image_hover_src . '" ' . $image_alt_init . ' uk-cover>';

					$output .= '</div>';
				}

				if ($overlay_styles && $overlay_mode != 'caption') {
					$output .= '<div class="uk-position-cover' . $overlay_margin . $overlay_styles . $overlay_transition . '"></div>';
				}

				if (in_array ( $overlay_positions, array (
						'center',
						'center-left',
						'center-right',
						'top-center',
						'bottom-center'
				) )) {
					$output .= '<div class="uk-position-' . $overlay_positions . $overlay_margin . '">';
				}

				if ($overlay_mode == 'icon') {
					$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . $overlay_transition . $icon_text_color . (! in_array ( $overlay_positions, array (
							'center',
							'center-left',
							'center-right',
							'top-center',
							'bottom-center'
					) ) ? ' uk-position-' . $overlay_positions . $overlay_margin : '') . ' uk-margin-remove-first-child">';
					$output .= '<span class="ui-icon' . $icon_transition . '" uk-overlay-icon></span>';
					$output .= '</div>';
				} else {
					if ((isset ( $value->title )) || (isset ( $value->meta )) || (isset ( $value->content )) || $lightbox) {

						$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . (! in_array ( $overlay_positions, array (
								'center',
								'center-left',
								'center-right',
								'top-center',
								'bottom-center'
						) ) ? ' uk-position-' . $overlay_positions . $overlay_margin : '') . (empty ( $overlay_styles ) || $check_animate_bg == false ? $overlay_transition : '') . (! empty ( $overlay_styles ) && $overlay_mode == 'caption' ? $overlay_styles : '') . ' uk-margin-remove-first-child">';

						if ($meta_alignment == 'top' && (isset ( $value->meta ))) {
							$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
							$output .= $value->meta;
							$output .= '</' . $meta_element . '>';
						}

						if ($show_lightbox_title != 'title-lightbox') {

							if ((isset ( $value->title ) && $value->title)) {
								$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $title_transition . $font_weight . '">';

								$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';

								if ($link_title && $title_link && $overlay_link == false) {
									$output .= ($lightbox) ? '<a' . $link_title_hover . ' href="' . $title_link . '"' . $lightbox_init . $data_alt_init . $data_caption_init . '>' : '<a' . $link_title_hover . ' href="' . $title_link . '"' . $link_target . $render_linkscroll . '>';
								}

								$output .= $value->title;

								if ($link_title && $title_link && $overlay_link == false) {
									$output .= '</a>';
								}

								$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';

								$output .= '</' . $heading_selector . '>';
							}
						}

						if (empty ( $meta_alignment ) && (isset ( $value->meta ))) {
							$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
							$output .= $value->meta;
							$output .= '</' . $meta_element . '>';
						}

						if ($show_lightbox_content != 'content-lightbox' && (isset ( $value->content ))) {
							$output .= '<div class="ui-content uk-panel' . $content_style . $content_transition . '">';
							$output .= $value->content;
							$output .= '</div>';
						}

						if ($meta_alignment == 'content' && (isset ( $value->meta ))) {
							$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
							$output .= $value->meta;
							$output .= '</' . $meta_element . '>';
						}

						if ($title_link && $button_title) {
							$output .= '<div class="' . $button_margin . '">';
							if ($overlay_link) {
								$output .= '<div class="' . $button_style_cls . $button_transition . '">' . $button_title . '</div>';
							} else {
								$output .= ($lightbox) ? '<a href="' . $title_link . '"' . $lightbox_init . $data_alt_init . $data_caption_init . ' class="' . $button_style_cls . $button_transition . '">' . $button_title . '</a>' : '<a class="' . $button_style_cls . $button_transition . '" href="' . $title_link . '"' . $link_target . $render_linkscroll . '>' . $button_title . '</a>';
							}
							$output .= '</div>';
						}

						$output .= '</div>';
					}
				}

				if (in_array ( $overlay_positions, array (
						'center',
						'center-left',
						'center-right',
						'top-center',
						'bottom-center'
				) )) {
					$output .= '</div>';
				}

				$output .= ($overlay_link && $title_link) ? '</a>' : '</div>';

				$output .= ($helper) ? '</div>' : '';
				// End Overlay cover mode.

				$output .= '</div>'; // end div grid.
			}
		}

		$output .= '</div>';

		$output .= '</div>';

		// Detect ajax addon rendering for edit mode
		$app = Factory::getApplication();
		if($app->getInput()->get('format') == 'json' && $app->getInput()->get('callback') == 'addon' && !$mediaItemFound) {
			$output = '';
		}
		
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
		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$lightbox = (isset ( $settings->lightbox ) && $settings->lightbox) ? 1 : 0;
		$button_style = (isset ( $settings->button_style ) && $settings->button_style) ? $settings->button_style : '';
		$button_background = (isset ( $settings->button_background ) && $settings->button_background) ? 'background-color: ' . $settings->button_background . ';' : '';
		$button_color = (isset ( $settings->button_color ) && $settings->button_color) ? 'color: ' . $settings->button_color . ';' : '';
		$button_title = (isset ( $settings->button_title ) && $settings->button_title) ? $settings->button_title : '';
		$button_background_hover = (isset ( $settings->button_background_hover ) && $settings->button_background_hover) ? 'background-color: ' . $settings->button_background_hover . ';' : '';
		$button_hover_color = (isset ( $settings->button_hover_color ) && $settings->button_hover_color) ? 'color: ' . $settings->button_hover_color . ';' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? $settings->overlay_styles : '';
		$overlay_background = (isset ( $settings->overlay_background ) && $settings->overlay_background) ? 'background-color: ' . $settings->overlay_background . ';' : '';

		$css = '';

		if ($overlay_styles == 'overlay-custom' && $overlay_background) {
			$css .= $addon_id . ' .uk-overlay-custom {' . $overlay_background . '}';
		}

		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $meta_color ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
		}

		if ($lightbox && ! empty ( $button_title ) && $button_style == 'custom') {
			if ($button_background || $button_color) {
				$css .= $addon_id . ' .uk-button-custom {' . $button_background . $button_color . '}';
			}
			if ($button_background_hover || $button_hover_color) {
				$css .= $addon_id . ' .uk-button-custom:hover, ' . $addon_id . ' .uk-button-custom:focus, ' . $addon_id . ' .uk-button-custom:active, ' . $addon_id . ' .uk-button-custom.uk-active {' . $button_background_hover . $button_hover_color . '}';
			}
		}

		return $css;
	}
}
