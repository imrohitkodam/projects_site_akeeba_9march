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
class JpagebuilderAddonLightgallery extends JpagebuilderAddons {
	public function render() {
		$doc = Factory::getApplication()->getDocument ();
		$settings = $this->addon->settings;
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
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

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$overlay_mode = (isset ( $settings->overlay_mode ) && $settings->overlay_mode) ? $settings->overlay_mode : 'cover';

		$overlay_on_hover = (isset ( $settings->overlay_on_hover ) && $settings->overlay_on_hover) ? $settings->overlay_on_hover : 0;
		$overlay_transition_background = (isset ( $settings->overlay_transition_background ) && $settings->overlay_transition_background) ? $settings->overlay_transition_background : 0;

		$title_transition = ($overlay_on_hover) ? ((isset ( $settings->title_transition ) && $settings->title_transition) ? ' uk-transition-' . $settings->title_transition . '' : '') : false;

		$content_transition = ($overlay_on_hover) ? ((isset ( $settings->content_transition ) && $settings->content_transition) ? ' uk-transition-' . $settings->content_transition . '' : '') : false;

		$meta_transition = ($overlay_on_hover) ? ((isset ( $settings->meta_transition ) && $settings->meta_transition) ? ' uk-transition-' . $settings->meta_transition . '' : '') : false;

		$icon_transition = ($overlay_on_hover) ? ((isset ( $settings->icon_transition ) && $settings->icon_transition) ? ' uk-transition-' . $settings->icon_transition . '' : '') : false;

		$overlay_positions = (isset ( $settings->overlay_positions ) && $settings->overlay_positions) ? 'uk-position-' . $settings->overlay_positions : '';
		// $overlay_transition = ( isset( $settings->overlay_transition ) && $settings->overlay_transition ) ? ' uk-transition-' . $settings->overlay_transition : '';
		$overlay_transition = ($overlay_on_hover) ? ((isset ( $settings->overlay_transition ) && $settings->overlay_transition) ? ' uk-transition-' . $settings->overlay_transition : '') : false;

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles . '' : '';

		$image_transition = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . ' uk-transition-opaque' : '';
		$image_transition_hover = (isset ( $settings->image_transition ) && $settings->image_transition) ? ' uk-transition-' . $settings->image_transition . '' : ' uk-transition-fade';

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

		$overlay_margin = (isset ( $settings->overlay_margin ) && $settings->overlay_margin) ? ' uk-position-' . $settings->overlay_margin . '' : '';
		// Inverse text color on hover
		$inverse_text_color = ($overlay_mode == 'cover' && $overlay_on_hover && $overlay_transition_background);
		$overlay_cover = ! empty ( $overlay_styles ) && $overlay_mode == 'cover';
		$gallery_style = (isset ( $settings->gallery_style ) && $settings->gallery_style) ? $settings->gallery_style : '';

		$show_thumbnail = (isset ( $settings->show_thumbnail ) && $settings->show_thumbnail) ? 1 : 0;
		$toogle_thumbnail = ($show_thumbnail) ? ((isset ( $settings->show_toogle_thumbnail ) && $settings->show_toogle_thumbnail) ? $settings->show_toogle_thumbnail : '') : false;
		$share_button = (isset ( $settings->show_share_button ) && $settings->show_share_button) ? 1 : 0;
		$hash = (isset ( $settings->show_hash ) && $settings->show_hash) ? 1 : 0;
		$zoom_button = (isset ( $settings->show_zoom_button ) && $settings->show_zoom_button) ? 1 : 0;
		$autoplay_button = (isset ( $settings->show_autoplay_button ) && $settings->show_autoplay_button) ? 1 : 0;
		$fullscreen_button = (isset ( $settings->show_fullscreen_button ) && $settings->show_fullscreen_button) ? 1 : 0;
		$show_caption = (isset ( $settings->show_caption ) && $settings->show_caption) ? 1 : 0;
		$caption_alt = 'getCaptionFromTitleOrAlt: false,';

		$thumbnail = ($show_thumbnail) ? 'thumbnail: true,' : 'thumbnail: false,';
		$show_toogle_thumbnail = ($toogle_thumbnail) ? 'toogleThumb: false,' : '';
		$show_hash = ($hash) ? 'hash: true,' : 'hash: false,';
		$show_share_button = ($share_button) ? 'share: true,' : 'share: false,';
		$show_zoom_button = ($zoom_button) ? 'zoom: true,' : 'zoom: false,';
		$show_autoplay_button = ($autoplay_button) ? 'autoplayControls: true,' : 'autoplayControls: false,';
		$show_fullscreen_button = ($fullscreen_button) ? 'fullScreen: true,' : 'fullScreen: false,';

		$transition_mode = (isset ( $settings->transition_mode ) && $settings->transition_mode) ? $settings->transition_mode : '';
		$transition_mode_cls = ($transition_mode) ? 'mode: \'' . $transition_mode . '\',' : '';

		$control_cls = $thumbnail . $show_toogle_thumbnail . $show_share_button . $show_zoom_button . $show_autoplay_button . $show_fullscreen_button . $transition_mode_cls . $show_hash;

		$size_width = (isset ( $settings->size_width ) && $settings->size_width) ? $settings->size_width : '';
		$size_width_cls = ($size_width) ? ' width:\'' . $size_width . 'px\',' : '';

		$size_height = (isset ( $settings->size_height ) && $settings->size_height) ? $settings->size_height : '';
		$size_height_cls = ($size_height) ? ' height:\'' . $size_height . 'px\',' : '';

		// New.

		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$heading_style = (isset ( $settings->heading_style ) && $settings->heading_style) ? ' uk-' . $settings->heading_style : '';
		$heading_style .= (isset ( $settings->title_color ) && $settings->title_color) ? ' uk-text-' . $settings->title_color : '';
		$heading_style .= (isset ( $settings->title_margin_top ) && $settings->title_margin_top) ? ' uk-margin-' . $settings->title_margin_top . '-top' : ' uk-margin-top';
		$title_decoration = (isset ( $settings->title_decoration ) && $settings->title_decoration) ? ' ' . $settings->title_decoration : '';

		$meta_element = (isset ( $settings->meta_element ) && $settings->meta_element) ? $settings->meta_element : 'div';
		$meta_style_cls = (isset ( $settings->meta_style ) && $settings->meta_style) ? $settings->meta_style : '';

		$meta_style = (isset ( $settings->meta_style ) && $settings->meta_style) ? ' uk-' . $settings->meta_style : '';
		$meta_style .= (isset ( $settings->meta_color ) && $settings->meta_color) ? ' uk-text-' . $settings->meta_color : '';
		$meta_style .= (isset ( $settings->meta_margin_top ) && $settings->meta_margin_top) ? ' uk-margin-' . $settings->meta_margin_top . '-top' : ' uk-margin-top';

		$meta_alignment = (isset ( $settings->meta_alignment ) && $settings->meta_alignment) ? $settings->meta_alignment : '';

		// Remove margin for heading element

		if ($meta_element != 'div' || ($meta_style_cls && $meta_style_cls != 'text-meta')) {
			$meta_style .= ' uk-margin-remove-bottom';
		}

		$content_style = (isset ( $settings->content_style ) && $settings->content_style) ? ' uk-' . $settings->content_style : '';
		$content_style .= (isset ( $settings->content_margin_top ) && $settings->content_margin_top) ? ' uk-margin-' . $settings->content_margin_top . '-top' : ' uk-margin-top';

		$image_styles = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' uk-box-shadow-' . $settings->box_shadow : '';
		$image_styles .= (isset ( $settings->hover_box_shadow ) && $settings->hover_box_shadow) ? ' uk-box-shadow-hover-' . $settings->hover_box_shadow : '';

		$show_lightbox_title = (isset ( $settings->show_lightbox_title ) && $settings->show_lightbox_title) ? $settings->show_lightbox_title : '';
		$show_lightbox_content = (isset ( $settings->show_lightbox_content ) && $settings->show_lightbox_content) ? $settings->show_lightbox_content : '';

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

		$overlay_text_color = (isset ( $settings->overlay_text_color ) && $settings->overlay_text_color) ? $settings->overlay_text_color : '';
		$item_maxwidth = (isset ( $settings->item_maxwidth ) && $settings->item_maxwidth) ? ' uk-margin-auto uk-width-' . $settings->item_maxwidth : '';
		$overlay_maxwidth = (isset ( $settings->overlay_maxwidth ) && $settings->overlay_maxwidth) ? ' uk-width-' . $settings->overlay_maxwidth : '';
		$font_weight = (isset ( $settings->font_weight ) && $settings->font_weight) ? ' uk-text-' . $settings->font_weight : '';
		$icon_text_color = (isset ( $settings->icon_text_color ) && $settings->icon_text_color) ? ' uk-' . $settings->icon_text_color : '';
		$output = '';

		$output .= '<div class="ui-light-gallery' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';

		if ($title) {

			$output .= '<' . $title_heading_selector . ' class="tm-addon-title' . $title_style . $title_heading_decoration . '">';

			if ($title_heading_decoration == ' uk-heading-line') {
				$output .= '<span>';
				$output .= nl2br ( $title );
				$output .= '</span>';
			} else {
				$output .= nl2br ( $title );
			}

			$output .= '</' . $title_heading_selector . '>';
		}

		$wa = $doc->getWebAssetManager();
		if ($gallery_style == 'comment') {
			$commentBox = '$(\'#comment-box-' . $this->addon->id . '\')';
			$fbscript = 'jQuery(function($){(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";fjs.parentNode.insertBefore(js,fjs);}(document,\'script\',\'facebook-jssdk\'));jQuery(function($){' . $commentBox . '.lightGallery({selector: \'.el-item\',appendSubHtmlTo:\'.lg-item\',addClass:\'fb-comments\',mode:\'lg-fade\',download:false,enableDrag:false,enableSwipe:false,' . $control_cls . '});' . $commentBox . '.on(\'onAfterSlide.lg\',function(event,prevIndex,index){if(!$(\'.lg-outer.lg-item\').eq(index).attr(\'data-fb\')){try{$(\'.lg-outer.lg-item\').eq(index).attr(\'data-fb\',\'loaded\');FB.XFBML.parse();}catch(err){$(window).on(\'fbAsyncInit\',function(){$(\'.lg-outer.lg-item\').eq(index).attr(\'data-fb\',\'loaded\');FB.XFBML.parse();});}}});});})';
			$fbscript .= "\n";
			$wa->addInlineScript ( $fbscript );
		} elseif ($gallery_style == 'fixed-size') {
			$lightgallery_id = '#fixed-size-' . $this->addon->id;
			$js = 'jQuery(function($){$("' . $lightgallery_id . '").lightGallery({' . $control_cls . $size_width_cls . $size_height_cls . $caption_alt . ' addClass: \'fixed-size\', selector: \'.el-item\', counter: false, download: false, startClass: \'\', enableSwipe: false, mousewheel: true, enableDrag: false, speed: 500});})';
			$js .= "\n";
			$wa->addInlineScript ( $js );
		} else {
			$lightgallery_id = '#hash-' . $this->addon->id;
			$js = 'jQuery(function($){$("' . $lightgallery_id . '").lightGallery({' . $control_cls . $caption_alt . ' selector: \'.el-item\',});})';
			$js .= "\n";
			$wa->addInlineScript ( $js );
		}

		if ($gallery_style == 'comment') {
			$output .= '<div id="fb-root"></div>';
			$output .= '<div id="comment-box-' . $this->addon->id . '" class="' . $grid . '" uk-grid="' . $masonry_cls . $gallery_parallax . '">';
		} elseif ($gallery_style == 'fixed-size') {
			$output .= '<div id="fixed-size-' . $this->addon->id . '" class="fixed-size' . $grid . '" uk-grid="' . $masonry_cls . $gallery_parallax . '">';
		} else {
			$output .= '<div id="hash-' . $this->addon->id . '" class="fixed-size' . $grid . '" uk-grid="' . $masonry_cls . $gallery_parallax . '">';
		}

		if (isset ( $settings->ui_simple_gallery_item ) && count ( ( array ) $settings->ui_simple_gallery_item )) {
			foreach ( $settings->ui_simple_gallery_item as $key => $value ) {
				$media_item = (isset ( $value->media_item ) && $value->media_item) ? $value->media_item : '';
				$image_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
				if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
					$image_src = $image_src;
				} elseif ($image_src) {
					$image_src = Uri::base ( true ) . '/' . $image_src;
				}

				$fb_src = isset ( $media_item->src ) ? $media_item->src : $media_item;
				if (strpos ( $fb_src, 'http://' ) !== false || strpos ( $fb_src, 'https://' ) !== false) {
					$fb_src = $fb_src;
				} elseif ($fb_src) {
					$fb_src = Uri::base () . $fb_src;
				}

				$media_item_hover = (isset ( $value->media_item_hover ) && $value->media_item_hover) ? $value->media_item_hover : '';
				$image_hover_src = isset ( $media_item_hover->src ) ? $media_item_hover->src : $media_item_hover;
				if (strpos ( $image_hover_src, 'http://' ) !== false || strpos ( $image_hover_src, 'https://' ) !== false) {
					$image_hover_src = $image_hover_src;
				} elseif ($image_hover_src) {
					$image_hover_src = Uri::base ( true ) . '/' . $image_hover_src;
				}

				$media_thumb = (isset ( $value->media_thumb ) && $value->media_thumb) ? $value->media_thumb : '';
				$image_thumb_src = isset ( $media_thumb->src ) ? $media_thumb->src : $media_thumb;
				if (strpos ( $image_thumb_src, 'http://' ) !== false || strpos ( $image_thumb_src, 'https://' ) !== false) {
					$image_thumb_src = $image_thumb_src;
				} elseif ($image_thumb_src) {
					$image_thumb_src = Uri::base ( true ) . '/' . $image_thumb_src;
				}
				$image_alt = (isset ( $value->image_alt ) && $value->image_alt) ? $value->image_alt : '';
				$title_alt_text = (isset ( $value->title ) && $value->title) ? $value->title : '';
				$image_alt_init = (empty ( $image_alt )) ? 'alt="' . str_replace ( '"', '', $title_alt_text ) . '"' : 'alt="' . str_replace ( '"', '', $image_alt ) . '"';

				$tab_transition = ($overlay_on_hover || $media_item_hover || ! empty ( $image_transition )) ? ' tabindex="0"' : '';
				$toggle_transition = ($overlay_on_hover || $media_item_hover || ! empty ( $image_transition )) ? ' uk-transition-toggle' : '';

				// Helper
				$helper_color = empty ( $overlay_styles ) || $overlay_mode == 'cover';
				$helper_toggle = $inverse_text_color || $overlay_text_color && (empty ( $overlay_styles ) && $media_item_hover || $overlay_cover && $overlay_on_hover && $overlay_transition_background);
				$helper = $helper_color || $helper_toggle;

				// Special item color based on overlay styles
				$item_color = '';
				if (empty ( $overlay_styles ) || $overlay_mode == 'cover') {
					$item_color = (isset ( $value->item_color ) && $value->item_color) ? 'uk-' . $value->item_color : $overlay_text_color;
				}

				if ($show_lightbox_title != 'title-ovl') {
					$data_caption_title = str_replace ( '"', '', (isset ( $value->title ) ? '<h3 class="uk-margin-remove">' . $value->title . '</h3>' : '') );
				} else {
					$data_caption_title = '';
				}

				if ($show_lightbox_content != 'content-ovl') {
					$data_caption_content = (isset ( $value->content )) ? str_replace ( '"', '', '<h6>' . $value->content . '</h6>' ) : '';
				} else {
					$data_caption_content = '';
				}

				$data_caption_init = '';

				if (! empty ( $data_caption_title || $data_caption_content )) {
					$data_caption_init = ' data-sub-html="' . $data_caption_title . $data_caption_content . '"';
				}

				$output .= '<div>';

				$output .= ($helper) ? '<div' . ($helper_color && $item_color ? ' class="' . $item_color . '"' : '') . ($helper_toggle ? ' uk-toggle="cls: uk-light uk-dark; mode: hover"' : '') . '>' : '';

				if ($gallery_style == 'comment') {
					$output .= '<a class="el-item' . $item_maxwidth . ' uk-inline-clip' . $toggle_transition . $image_styles . ' uk-link-toggle" href="' . $image_src . '" data-sub-html=\'<div class="fb-comments" data-href="' . $fb_src . '" data-width="400"></div>\'' . $tab_transition . $scrollspy_cls . '>';
				} else {
					$output .= ($show_caption) ? '<a class="el-item' . $item_maxwidth . ' uk-inline-clip' . $toggle_transition . $image_styles . ' uk-link-toggle" href="' . $image_src . '"' . $data_caption_init . '' . $tab_transition . $scrollspy_cls . '>' : '<a class="el-item' . $item_maxwidth . ' uk-inline-clip' . $toggle_transition . $image_styles . ' uk-link-toggle" href="' . $image_src . '"' . $tab_transition . $scrollspy_cls . '>';
				}

				if ($image_thumb_src) {
					$output .= '<img class="ui-image' . ($media_item_hover ? '' : $image_transition) . '" src="' . $image_thumb_src . '" ' . $image_alt_init . '>';
				} else {
					$output .= '<img class="ui-image' . ($media_item_hover ? '' : $image_transition) . '" src="' . $image_src . '" ' . $image_alt_init . '>';
				}

				if ($image_hover_src) {

					$output .= '<div class="uk-position-cover' . $image_transition_hover . '">';

					$output .= '<img class="ui-image" src="' . $image_hover_src . '" ' . $image_alt_init . ' uk-cover>';

					$output .= '</div>';
				}

				if ($overlay_styles && ($overlay_mode == 'cover' || $overlay_mode == 'icon')) {
					$output .= '<div class="uk-position-cover' . $overlay_margin . $overlay_styles . $overlay_transition . '"></div>';
				}

				if ($overlay_mode == 'icon') {
					$output .= '<div class="' . $overlay_positions . $overlay_margin . $icon_text_color . '">';
					$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . $overlay_transition . ' uk-margin-remove-first-child">';
					$output .= '<span class="ui-icon' . $icon_transition . '" uk-overlay-icon></span>';
					$output .= '</div>';
					$output .= '</div>';
				} else {
					if (! empty ( (isset ( $value->title ) && $value->title) || (isset ( $value->meta )) || (isset ( $value->content )) )) {

						$output .= '<div class="' . $overlay_positions . $overlay_margin . '">';

						if ($overlay_mode == 'cover') {
							$output .= ($overlay_styles && $overlay_transition_background) ? '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . ' uk-margin-remove-first-child">' : '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . $overlay_transition . ' uk-margin-remove-first-child">';
						} else {
							$output .= '<div class="' . $overlay_styles_int . $overlay_padding_init . $overlay_maxwidth . $overlay_transition . $overlay_styles . ' uk-margin-remove-first-child">';
						}

						if ($meta_alignment == 'top' && (isset ( $value->meta ))) {
							$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
							$output .= $value->meta;
							$output .= '</' . $meta_element . '>';
						}

						if ($show_lightbox_title != 'title-lightbox') {

							if ((isset ( $value->title ) && $value->title)) {
								$output .= '<' . $heading_selector . ' class="ui-title uk-margin-remove-bottom' . $heading_style . $title_decoration . $title_transition . $font_weight . '">';
								$output .= ($title_decoration == ' uk-heading-line') ? '<span>' : '';
								$output .= $value->title;
								$output .= ($title_decoration == ' uk-heading-line') ? '</span>' : '';
								$output .= '</' . $heading_selector . '>';
							}
						}

						if ($meta_alignment != 'top' && (isset ( $value->meta ))) {
							$output .= '<' . $meta_element . ' class="ui-meta' . $meta_style . $meta_transition . '">';
							$output .= $value->meta;
							$output .= '</' . $meta_element . '>';
						}

						if ($show_lightbox_content != 'content-lightbox' && (isset ( $value->content ))) {
							$output .= '<div class="ui-content uk-panel' . $content_style . $content_transition . '">';
							$output .= $value->content;
							$output .= '</div>';
						}

						$output .= '</div>';
						$output .= '</div>';
					}
				}

				$output .= '</a>';
				$output .= ($helper) ? '</div>' : '';
				$output .= '</div>';
			}
		}

		$output .= '</div>';

		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/uitheme.js',
				'components/com_jpagebuilder/assets/js/uitheme-icons.js',
				'components/com_jpagebuilder/addons/lightgallery/assets/js/lightgallery.min.js'
		);
	}
	public function stylesheets() {
		return array (
				'components/com_jpagebuilder/assets/css/uitheme.css',
				'components/com_jpagebuilder/addons/lightgallery/assets/css/lightgallery.min.css',
				'components/com_jpagebuilder/addons/lightgallery/assets/css/lg-transitions.min.css',
				'components/com_jpagebuilder/addons/lightgallery/assets/css/lg-fb-comment-box.min.css'
		);
	}
	public function css() {
		$lang = Factory::getApplication()->getLanguage ();
		$dir = $lang->get ( 'rtl' );
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;

		$title_color = (isset ( $settings->title_color ) && $settings->title_color) ? $settings->title_color : '';
		$custom_title_color = (isset ( $settings->custom_title_color ) && $settings->custom_title_color) ? 'color: ' . $settings->custom_title_color . ';' : '';
		$meta_color = (isset ( $settings->meta_color ) && $settings->meta_color) ? $settings->meta_color : '';
		$custom_meta_color = (isset ( $settings->custom_meta_color ) && $settings->custom_meta_color) ? 'color: ' . $settings->custom_meta_color . ';' : '';
		$content_color = (isset ( $settings->content_color ) && $settings->content_color) ? 'color: ' . $settings->content_color . ';' : '';
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
		if ($dir == 1) {
			$css .= ' .lg-outer { direction: ltr; }.lg-sub-html h3, .lg-sub-html h6{ color: #fff;} .lg-sub-html h6{ font-size: 16px;}';
		}

		return $css;
	}
}
