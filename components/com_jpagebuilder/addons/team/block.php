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
class JpagebuilderAddonTeam extends JpagebuilderAddons {
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
		$box_shadow = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$hover_box_shadow = (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->hover_box_shadow : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$width_mode = (isset ( $settings->width_mode ) && $settings->width_mode) ? $settings->width_mode : '';

		$autoplay = (isset ( $settings->autoplay ) && $settings->autoplay) ? 'autoplay: 1; ' : '';
		$pause = ($autoplay) ? ((isset ( $settings->pause ) && $settings->pause) ? '' : ' pauseOnHover: false;') : '';
		$interval = ($autoplay) ? ((isset ( $settings->autoplay_interval ) && $settings->autoplay_interval) ? 'autoplayInterval: ' . (( int ) $settings->autoplay_interval * 1000) . ';' : '') : '';
		$autoplay .= $interval . $pause;

		$slide_sets = (isset ( $settings->slidesets ) && $settings->slidesets) ? 'sets: 1; ' : '';
		$center_items = (isset ( $settings->center_slide ) && $settings->center_slide) ? 'center: 1;' : '';

		$larger_style = (isset ( $settings->larger_style ) && $settings->larger_style) ? $settings->larger_style : '';
		$larger_style_init = ($larger_style) ? ' uk-slidenav-large' : '';

		$slidenav_on_hover = (isset ( $settings->slidenav_on_hover ) && $settings->slidenav_on_hover) ? 1 : 0;
		$slidenav_on_hover_init = ($slidenav_on_hover) ? ' uk-hidden-hover uk-hidden-touch' : '';

		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . '' : '';

		// Navigation settings.
		$navigation_control = (isset ( $settings->navigation ) && $settings->navigation) ? $settings->navigation : '';
		$navigation_inverse = (isset ( $settings->navigation_color ) && $settings->navigation_color) ? $settings->navigation_color : '';
		$navigation_breakpoint = (isset ( $settings->navigation_breakpoint ) && $settings->navigation_breakpoint) ? $settings->navigation_breakpoint : '';
		$navigation_breakpoint_cls = ($navigation_breakpoint) ? ' uk-visible@' . $navigation_breakpoint . '' : '';

		$navigation = (isset ( $settings->nav_margin ) && $settings->nav_margin) ? ' uk-margin-' . $settings->nav_margin . '-top' : ' uk-margin-top';
		$navigation .= (isset ( $settings->navigation_position ) && $settings->navigation_position) ? ' uk-flex-' . $settings->navigation_position : '';

		// Sidenav Settings.
		$slidenav_position = (isset ( $settings->slidenav_position ) && $settings->slidenav_position) ? $settings->slidenav_position : '';
		$slidenav_position_cls = (! empty ( $slidenav_position ) || ($slidenav_position != 'default')) ? ' uk-position-' . $slidenav_position . '' : '';
		$slidenav_margin = (isset ( $settings->slidenav_margin ) && $settings->slidenav_margin) ? ' uk-position-' . $settings->slidenav_margin : '';

		$slidenav_breakpoint = (isset ( $settings->slidenav_breakpoint ) && $settings->slidenav_breakpoint) ? $settings->slidenav_breakpoint : '';
		$slidenav_breakpoint_cls = ($slidenav_breakpoint) ? 'uk-visible@' . $slidenav_breakpoint . '' : '';

		$slidenav_outside_breakpoint = (isset ( $settings->slidenav_outside_breakpoint ) && $settings->slidenav_outside_breakpoint) ? ' @' . $settings->slidenav_outside_breakpoint : 'xl';
		$slidenav_outside_color = (isset ( $settings->slidenav_outside_color ) && $settings->slidenav_outside_color) ? $settings->slidenav_outside_color : '';

		$slidenav_color = (isset ( $settings->slidenav_color ) && $settings->slidenav_color) ? $settings->slidenav_color : '';

		if ($slidenav_position == 'outside') {
			$slidenav_color_cls = ($slidenav_outside_color) ? ' uk-' . $slidenav_outside_color . '' : '';
		} else {
			$slidenav_color_cls = ($slidenav_color) ? ' uk-' . $slidenav_color . '' : '';
		}

		$grid_column_gap = (isset ( $settings->team_grid_column_gap ) && $settings->team_grid_column_gap) ? $settings->team_grid_column_gap : '';
		$divider = (isset ( $settings->divider ) && $settings->divider) ? 1 : 0;

		$grid_slider = '';
		$grid_slider .= $grid_column_gap ? ' uk-grid-column-' . $grid_column_gap : '';
		$grid_slider .= ($grid_column_gap != 'collapse' && $divider) ? ' uk-grid-divider' : '';

		$grid = (isset ( $settings->phone_portrait ) && $settings->phone_portrait) ? ' uk-width-' . $settings->phone_portrait : '';
		$grid .= (isset ( $settings->phone_landscape ) && $settings->phone_landscape) ? ' uk-width-' . $settings->phone_landscape . '@s' : '';
		$grid .= (isset ( $settings->tablet_landscape ) && $settings->tablet_landscape) ? ' uk-width-' . $settings->tablet_landscape . '@m' : '';
		$grid .= (isset ( $settings->desktop ) && $settings->desktop) ? ' uk-width-' . $settings->desktop . '@l' : '';
		$grid .= (isset ( $settings->large_screens ) && $settings->large_screens) ? ' uk-width-' . $settings->large_screens . '@xl' : '';

		$min_height = (isset ( $settings->min_height ) && $settings->min_height) ? 'min-height: ' . $settings->min_height . ';' : '300';

		$velocity = (isset ( $settings->velocity ) && $settings->velocity) ? (( int ) $settings->velocity / 100) : '';
		$velocity_init = (! empty ( $velocity )) ? 'velocity: ' . $velocity . ';' : '';
		$finite_slide = (isset ( $settings->finite_slide ) && $settings->finite_slide) ? 'finite: 1;' : '';

		$height = (isset ( $settings->height ) && $settings->height) ? $settings->height : '';
		$height_cls = '';
		if ($height == 'full') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . '"';
		} elseif ($height == 'percent') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: 20"';
		} elseif ($height == 'section') {
			$height_cls .= ' uk-height-viewport="offset-top: true; ' . $min_height . 'offset-bottom: !.jpb-section +"';
		}

		$grid_slider .= ($height && $width_mode == 'fixed') ? ' uk-grid-match' : '';

		// New style options.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->name_color ) && $settings->name_color) ? ' uk-' . $settings->name_color : '';
		$heading_style .= (isset ( $settings->title_text_transform ) && $settings->title_text_transform) ? ' uk-text-' . $settings->title_text_transform : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		$heading_style_cls = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style_cls_init = (empty ( $heading_style_cls )) ? ' uk-card-title' : '';

		// Meta.
		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->designation_style ) && $settings->designation_style) ? ' uk-text-' . $settings->designation_style : '';
		$meta_style .= (isset ( $settings->text_transform ) && $settings->text_transform) ? ' uk-text-' . $settings->text_transform : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : '';

		// Remove margin for heading element
		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_text_transform ) && $settings->content_text_transform) ? ' uk-text-' . $settings->content_text_transform : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$email_style = (isset ( $settings->email_class ) && $settings->email_class) ? ' uk-' . $settings->email_class : '';
		$email_style .= (isset ( $settings->email_style ) && $settings->email_style) ? ' uk-text-' . $settings->email_style : '';
		$email_style .= (isset ( $settings->email_text_transform ) && $settings->email_text_transform) ? ' uk-text-' . $settings->email_text_transform : '';
		$email_style .= (isset ( $settings->email_margin_top ) && $settings->email_margin_top) ? ' uk-margin-' . $settings->email_margin_top . '-top' : ' uk-margin-top';

		$card = (isset ( $settings->card_styles ) && $settings->card_styles) ? $settings->card_styles : '';
		$card_size = (isset ( $settings->card_size ) && $settings->card_size) ? ' uk-card-' . $settings->card_size : '';

		$panel_image_padding = (isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0;
		$image_padding = ($card) ? ((isset ( $settings->image_padding ) && $settings->image_padding) ? 1 : 0) : '';

		$social_position = (isset ( $settings->social_position ) && $settings->social_position) ? $settings->social_position : '';

		$overlay_on_hover = (isset ( $settings->overlay_on_hover ) && $settings->overlay_on_hover) ? $settings->overlay_on_hover : 0;

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles : '';

		$overlay_transition = ($overlay_on_hover) ? ((isset ( $settings->overlay_transition ) && $settings->overlay_transition) ? ' uk-transition-' . $settings->overlay_transition : '') : false;

		$vertical_icons = ($social_position == 'overlay') && (isset ( $settings->vertical_icons ) && $settings->vertical_icons) ? ' uk-iconnav-vertical' : '';

		$overlay_positions = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? 'uk-position-' . $settings->overlay_positions : '';

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

		$overlay_margin = ($overlay_styles) ? ((isset ( $settings->overlay_margin ) && $settings->overlay_margin) ? ' uk-position-' . $settings->overlay_margin : '') : '';
		$social_margin_top = (isset ( $settings->social_margin_top ) && $settings->social_margin_top) ? ' uk-margin-' . $settings->social_margin_top . '-top' : ' uk-margin-top';

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

		$panel_content_padding = (isset ( $settings->card_content_padding ) && $settings->card_content_padding) ? $settings->card_content_padding : '';
		$card_content_padding = ($panel_content_padding && empty ( $card )) ? 'uk-padding' . (($panel_content_padding == 'default') ? ' uk-margin-remove-first-child' : '-' . $panel_content_padding . ' uk-margin-remove-first-child') : '';

		$card_width = (isset ( $settings->card_width ) && $settings->card_width) ? ' uk-margin-auto uk-width-' . $settings->card_width : '';
		$image_border = (! empty ( $card ) && $image_padding) ? false : ((isset ( $settings->image_styles ) && $settings->image_styles) ? ' uk-border-' . $settings->image_styles : '');
		$box_shadow = (! empty ( $card )) ? false : ((isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '');
		$hover_box_shadow = (! empty ( $card )) ? false : ((isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->hover_box_shadow : '');
		$overlay_alignment = (isset ( $settings->overlay_alignment ) && $settings->overlay_alignment) ? ' uk-flex-' . $settings->overlay_alignment : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';

		$panel_cls = ($card) ? 'uk-card uk-card-' . $card . $card_size . $card_width : 'uk-panel' . $card_width;
		$panel_cls .= ($card && $card != 'hover') ? ' uk-card-hover' : '';
		$panel_cls .= ($card && $panel_image_padding == false) ? ' uk-card-body uk-margin-remove-first-child' : '';

		$panel_cls .= (empty ( $card ) && empty ( $panel_content_padding )) ? ' uk-margin-remove-first-child' : '';

		$icons_button = (isset ( $settings->icons_button ) && $settings->icons_button) ? 1 : 0;
		$display_icons_button = ($icons_button) ? 'uk-icon-button' : 'uk-icon-link';

		$output = '';

		$output .= '<div class="ui-team-slider' . $general . $max_width_cfg . '" uk-slider="' . $autoplay . $slide_sets . $center_items . $finite_slide . $velocity_init . '"' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}

		$output .= '<div class="ui-slider-wrapper' . $text_alignment . '">';

		$output .= ($slidenav_on_hover) ? '<div class="uk-position-relative uk-visible-toggle" tabindex="-1">' : '<div class="uk-position-relative">';

		if ($slidenav_position == 'outside') {
			$output .= '<div class="uk-slider-container">';
		}

		$output .= ($width_mode) ? '<ul class="uk-slider-items uk-grid' . $grid_slider . '"' . $height_cls . '>' : '<ul class="uk-slider-items uk-grid' . $grid_slider . '">';

		foreach ( $settings->ui_team_item as $key => $value ) {
			$image = (isset ( $value->avatar ) && $value->avatar) ? $value->avatar : '';
			$image_src = isset ( $image->src ) ? $image->src : $image;
			if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
				$image_src = $image_src;
			} elseif ($image_src) {
				$image_src = Uri::base ( true ) . '/' . $image_src;
			}
			$introtext = (isset ( $value->introtext ) && $value->introtext) ? $value->introtext : '';
			$email = (isset ( $value->email ) && $value->email) ? $value->email : '';
			$designation = (isset ( $value->designation ) && $value->designation) ? $value->designation : '';
			$name = (isset ( $value->title ) && $value->title) ? $value->title : '';
			$socials = (isset ( $value->socials ) && $value->socials) ? 1 : 0;
			$facebook = (isset ( $value->facebook ) && $value->facebook) ? $value->facebook : '';
			$twitter = (isset ( $value->twitter ) && $value->twitter) ? $value->twitter : '';
			$youtube = (isset ( $value->youtube ) && $value->youtube) ? $value->youtube : '';
			$linkedin = (isset ( $value->linkedin ) && $value->linkedin) ? $value->linkedin : '';
			$pinterest = (isset ( $value->pinterest ) && $value->pinterest) ? $value->pinterest : '';
			$flickr = (isset ( $value->flickr ) && $value->flickr) ? $value->flickr : '';
			$dribbble = (isset ( $value->dribbble ) && $value->dribbble) ? $value->dribbble : '';
			$behance = (isset ( $value->behance ) && $value->behance) ? $value->behance : '';
			$instagram = (isset ( $value->instagram ) && $value->instagram) ? $value->instagram : '';
			$image_panel = (isset ( $value->image_panel ) && $value->image_panel) ? 1 : 0;
			$media_background = ($image_panel) ? ((isset ( $value->media_background ) && $value->media_background) ? ' style="background-color: ' . $value->media_background . ';"' : '') : '';
			$media_blend_mode = ($image_panel && $media_background) ? ((isset ( $value->media_blend_mode ) && $value->media_blend_mode) ? ' uk-blend-' . $value->media_blend_mode : '') : false;
			$media_overlay = ($image_panel) ? ((isset ( $value->media_overlay ) && $value->media_overlay) ? '<div class="uk-position-cover" style="background-color: ' . $value->media_overlay . '"></div>' : '') : '';
			$image_alt = (isset ( $value->title ) && $value->title) ? $value->title : '';
			$image_alt_init = (! empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $image_alt ) . '"' : '';
			$output .= ($width_mode) ? '<li class="ui-item' . $grid . '">' : '<li class="ui-item">';

			$social_icons = '';

			$social_icons .= ($facebook) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $facebook . '" aria-label="Facebook"><i class="fab fa-facebook-f" aria-hidden="true" title="Facebook"></i></a></li>' : '';
			$social_icons .= ($twitter) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $twitter . '" aria-label="Twitter"><i class="fab fa-twitter" aria-hidden="true" title="Twitter"></i></a></li>' : '';
			$social_icons .= ($youtube) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $youtube . '" aria-label="YouTube"><i class="fab fa-youtube" aria-hidden="true" title="YouTube"></i></a></li>' : '';
			$social_icons .= ($linkedin) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $linkedin . '" aria-label="LinkedIn"><i class="fab fa-linkedin-in" aria-hidden="true" title="LinkedIn"></i></a></li>' : '';
			$social_icons .= ($pinterest) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $pinterest . '" aria-label="Pinterest"><i class="fab fa-pinterest" aria-hidden="true" title="Pinterest"></i></a></li>' : '';
			$social_icons .= ($flickr) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $flickr . '" aria-label="Flickr"><i class="fab fa-flickr" aria-hidden="true" title="Flickr"></i></a></li>' : '';
			$social_icons .= ($dribbble) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $dribbble . '" aria-label="Dribble"><i class="fab fa-dribbble" aria-hidden="true" title="Dribble"></i></a></li>' : '';
			$social_icons .= ($behance) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $behance . '" aria-label="Behance"><i class="fab fa-behance" aria-hidden="true" title="Behance"></i></a></li>' : '';
			$social_icons .= ($instagram) ? '<li><a class="' . $display_icons_button . '" target="_blank" rel="noopener noreferrer" href="' . $instagram . '" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true" title="Instagram"></i></a></li>' : '';

			$output .= '<div class="' . $panel_cls . '"' . $scrollspy_cls . '>';

			$output .= ($image_padding) ? '<div class="uk-card-media-top">' : '';

			if ($image_src) {

				$output .= ($image_transition || $overlay_on_hover) ? '<div class="uk-inline-clip uk-transition-toggle' . $box_shadow . $hover_box_shadow . '" tabindex="0"' . $media_background . '>' : '<div class="uk-inline-clip' . $box_shadow . $hover_box_shadow . '"' . $media_background . '>';

				$output .= ($image_transition) ? '<img class="ui-image' . $media_blend_mode . $image_transition . ' uk-transition-opaque" src="' . $image_src . '" ' . $image_alt_init . '>' : '<img class="ui-image' . $media_blend_mode . $image_border . '" src="' . $image_src . '" ' . $image_alt_init . '>';
				$output .= $media_overlay;

				if ($social_position == 'overlay' && $socials) {
					$output .= '<div class="' . $overlay_positions . $overlay_margin . '">';
					$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_transition . $overlay_styles . ' uk-margin-remove-first-child">';
					$output .= '<ul class="tm-social-list uk-iconnav uk-text-center' . $vertical_icons . $overlay_alignment . '">';
					$output .= $social_icons;
					$output .= '</ul>';
					$output .= '</div>';
					$output .= '</div>';
				}

				$output .= '</div>';
			}

			$output .= ($image_padding) ? '</div>' : '';

			$output .= ($image_padding) ? '<div class="uk-card-body uk-margin-remove-first-child">' : '';
			$output .= ($card_content_padding) ? '<div class="' . $card_content_padding . '">' : '';

			if ($meta_alignment == 'top' && $designation) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $designation;
				$output .= '</' . $meta_element . '>';
			}

			if ($name) {
				$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $heading_style_cls_init . $title_decoration . $font_weight . '">';
				$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
				$output .= $name;
				$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
				$output .= '</' . $heading_selector . '>';
			}

			if (empty ( $meta_alignment ) && $designation) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $designation;
				$output .= '</' . $meta_element . '>';
			}

			if ($social_position == 'before' && $socials) {
				$output .= '<ul class="tm-social-list uk-iconnav uk-text-center' . $social_margin_top . '">';
				$output .= $social_icons;
				$output .= '</ul>';
			}

			if ($email) {
				$output .= '<div class="ui-email' . $email_style . '">';
				$output .= $email;
				$output .= '</div>';
			}

			if ($introtext) {
				$output .= '<div class="ui-content uk-panel' . $content_style . '">';
				$output .= $introtext;
				$output .= '</div>';
			}

			if ($meta_alignment == 'content' && $designation) {
				$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . '">';
				$output .= $designation;
				$output .= '</' . $meta_element . '>';
			}

			if ($social_position == 'after' && $socials) {
				$output .= '<ul class="tm-social-list uk-iconnav uk-text-center' . $social_margin_top . '">';
				$output .= $social_icons;
				$output .= '</ul>';
			}

			$output .= ($image_padding) ? '</div>' : '';
			$output .= ($card_content_padding) ? '</div>' : '';

			$output .= '</div>';

			$output .= '</li>';
		}

		$output .= '</ul>';

		if ($slidenav_position == 'default') {
			$output .= '<div class="' . $slidenav_breakpoint_cls . $slidenav_color_cls . $slidenav_on_hover_init . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . ' uk-position-center-left' . $larger_style_init . '" href="#" uk-slidenav-previous uk-slider-item="previous"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . ' uk-position-center-right' . $larger_style_init . '" href="#" uk-slidenav-next uk-slider-item="next"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position == 'outside') {
			$output .= '<div class="' . $slidenav_breakpoint_cls . $slidenav_color_cls . $slidenav_on_hover_init . '">';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-left-out" href="#" uk-slidenav-previous uk-slider-item="previous" uk-toggle="cls: uk-position-center-left-out uk-position-center-left; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '<a class="ui-slidenav ' . $slidenav_margin . $larger_style_init . ' uk-position-center-right-out" href="#" uk-slidenav-next uk-slider-item="next" uk-toggle="cls: uk-position-center-right-out uk-position-center-right; mode: media; media:' . $slidenav_outside_breakpoint . '"></a>';
			$output .= '</div> ';
		} elseif ($slidenav_position != '') {
			$output .= '<div class="' . $slidenav_breakpoint_cls . $slidenav_position_cls . $slidenav_margin . $slidenav_color_cls . $slidenav_on_hover_init . ' uk-slidenav-container">';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-previous uk-slider-item="previous"></a>';
			$output .= '<a class="ui-slidenav' . $larger_style_init . '" href="#" uk-slidenav-next uk-slider-item="next"></a>';
			$output .= '</div>';
		}

		$output .= '</div>';

		if ($slidenav_position == 'outside') {
			$output .= '</div>';
		}

		$output .= '</div>';
		if ($navigation_control != '') {
			$output .= ($navigation_inverse) ? '<div class="uk-' . $navigation_inverse . '">' : '';
			$output .= '<ul class="uk-slider-nav uk-dotnav' . $navigation . $navigation_breakpoint_cls . '"></ul>';
			$output .= ($navigation_inverse) ? '</div>' : '';
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;

		$title_color = (isset ( $settings->name_color ) && $settings->name_color) ? $settings->name_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$designation_style = (isset ( $settings->designation_style ) && $settings->designation_style) ? $settings->designation_style : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
		$email_style = (isset ( $settings->email_style ) && $settings->email_style) ? $settings->email_style : '';
		$email_color = (isset ( $settings->email_color ) && $settings->email_color) ? 'color: ' . $settings->email_color . ';' : '';
		$icons_button = (isset ( $settings->icons_button ) && $settings->icons_button) ? 1 : 0;
		$icon_background = (isset ( $settings->icon_background ) && $settings->icon_background) ? 'background-color: ' . $settings->icon_background . ';' : '';
		$icon_color = (isset ( $settings->icon_color ) && $settings->icon_color) ? 'color: ' . $settings->icon_color . ';' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? $settings->overlay_styles : '';
		$overlay_background = (isset ( $settings->overlay_background ) && $settings->overlay_background) ? 'background-color: ' . $settings->overlay_background . ';' : '';

		$card_style = (isset ( $settings->card_styles ) && $settings->card_styles) ? $settings->card_styles : '';
		$card_background = (isset ( $settings->card_background ) && $settings->card_background) ? 'background-color: ' . $settings->card_background . ';' : '';
		$card_color = (isset ( $settings->card_color ) && $settings->card_color) ? 'color: ' . $settings->card_color . ';' : '';

		$css = '';
		if ($card_style == 'custom' && $card_background) {
			$css .= $addon_id . ' .uk-card-custom {' . $card_background . '}';
		}

		if ($card_style == 'custom' && $card_color) {
			$css .= $addon_id . ' .uk-card-custom.uk-card-body, ' . $addon_id . ' .uk-card-custom>:not([class*=uk-card-media]) {' . $card_color . '}';
		}
		if ($icons_button && $icon_background) {
			$css .= $addon_id . ' .uk-icon-button {' . $icon_background . '}';
		}
		if ($icons_button && $icon_color) {
			$css .= $addon_id . ' .uk-icon-button {' . $icon_color . '}';
		} elseif ($icon_color) {
			$css .= $addon_id . ' .uk-icon-link {' . $icon_color . '}';
		}
		if ($overlay_styles == 'overlay-custom' && $overlay_background) {
			$css .= $addon_id . ' .uk-overlay-custom {' . $overlay_background . '}';
		}
		if (empty ( $title_color ) && $custom_title_color) {
			$css .= $addon_id . ' .ui-title {' . $custom_title_color . '}';
		}
		if (empty ( $designation_style ) && $custom_meta_color) {
			$css .= $addon_id . ' .ui-meta {' . $custom_meta_color . '}';
		}
		if (empty ( $email_style ) && $email_color) {
			$css .= $addon_id . ' .ui-email {' . $email_color . '}';
		}
		if ($content_color) {
			$css .= $addon_id . ' .ui-content {' . $content_color . '}';
		}

		return $css;
	}
}
