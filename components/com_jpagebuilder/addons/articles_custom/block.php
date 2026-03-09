<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
class JpagebuilderAddonArticles_custom extends JpagebuilderAddons {
	public function render() {
		$page_view_name = isset ( $_GET ['view'] );
		$app = Factory::getApplication ();
		if ($app->isClient ( 'administrator' )) {
			return ''; // prevent from loading in the admin view
		}

		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? $settings->class : '';
		$style = (isset ( $settings->style ) && $settings->style) ? $settings->style : 'panel-default';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';

		// Addon options
		$resource = (isset ( $settings->resource ) && $settings->resource) ? $settings->resource : 'article';
		$catid = (isset ( $settings->catid ) && $settings->catid) ? $settings->catid : 0;
		$tagids = (isset ( $settings->tagids ) && $settings->tagids) ? $settings->tagids : array ();
		$include_subcat = (isset ( $settings->include_subcat )) ? $settings->include_subcat : 1;
		$post_type = (isset ( $settings->post_type ) && $settings->post_type) ? $settings->post_type : '';
		$ordering = (isset ( $settings->ordering ) && $settings->ordering) ? $settings->ordering : 'latest';
		$article_style = (isset ( $settings->article_style ) && $settings->article_style) ? $settings->article_style : 'article-style-01';
		$image_link = (isset ( $settings->image_link )) ? $settings->image_link : 1;
		$limit = (isset ( $settings->limit ) && $settings->limit) ? $settings->limit : 3;
		$columns_md = (isset ( $settings->columns_md ) && $settings->columns_md) ? $settings->columns_md : 3;
		$columns_sm = (isset ( $settings->columns_sm ) && $settings->columns_sm) ? $settings->columns_sm : 2;
		$columns_xs = (isset ( $settings->columns_xs ) && $settings->columns_xs) ? $settings->columns_xs : 1;
		$show_intro = (isset ( $settings->show_intro )) ? $settings->show_intro : 1;
		$intro_limit = (isset ( $settings->intro_limit ) && $settings->intro_limit) ? $settings->intro_limit : 200;
		$hide_thumbnail = (isset ( $settings->hide_thumbnail )) ? $settings->hide_thumbnail : 0;
		$show_author = (isset ( $settings->show_author )) ? $settings->show_author : 1;
		$show_category = (isset ( $settings->show_category )) ? $settings->show_category : 1;
		$show_date = (isset ( $settings->show_date )) ? $settings->show_date : 1;
		$show_readmore = (isset ( $settings->show_readmore )) ? $settings->show_readmore : 1;
		$readmore_text = (isset ( $settings->readmore_text ) && $settings->readmore_text) ? $settings->readmore_text : '';
		$link_articles = (isset ( $settings->link_articles )) ? $settings->link_articles : 0;
		$link_catid = (isset ( $settings->link_catid )) ? $settings->link_catid : 0;

		$all_articles_btn_text = (isset ( $settings->all_articles_btn_text ) && $settings->all_articles_btn_text) ? $settings->all_articles_btn_text : 'See all posts';
		$all_articles_btn_class = (isset ( $settings->all_articles_btn_size ) && $settings->all_articles_btn_size) ? ' jpb-btn-' . $settings->all_articles_btn_size : '';
		$all_articles_btn_class .= (isset ( $settings->all_articles_btn_type ) && $settings->all_articles_btn_type) ? ' jpb-btn-' . $settings->all_articles_btn_type : ' jpb-btn-default';
		$all_articles_btn_class .= (isset ( $settings->all_articles_btn_shape ) && $settings->all_articles_btn_shape) ? ' jpb-btn-' . $settings->all_articles_btn_shape : ' jpb-btn-rounded';
		$all_articles_btn_class .= (isset ( $settings->all_articles_btn_appearance ) && $settings->all_articles_btn_appearance) ? ' jpb-btn-' . $settings->all_articles_btn_appearance : '';
		$all_articles_btn_class .= (isset ( $settings->all_articles_btn_block ) && $settings->all_articles_btn_block) ? ' ' . $settings->all_articles_btn_block : '';
		$all_articles_btn_icon = (isset ( $settings->all_articles_btn_icon ) && $settings->all_articles_btn_icon) ? $settings->all_articles_btn_icon : '';
		$all_articles_btn_icon_position = (isset ( $settings->all_articles_btn_icon_position ) && $settings->all_articles_btn_icon_position) ? $settings->all_articles_btn_icon_position : 'left';

		$output = '';
		$article_helper = JPATH_ROOT . '/components/com_jpagebuilder/helpers/articles.php';

		require_once $article_helper;
		$items = JpagebuilderHelperArticles::getArticles ( $limit, $ordering, $catid, $include_subcat, $post_type, $tagids );

		if (! count ( $items )) {
			$output .= '<p class="alert alert-warning">' . Text::_ ( 'COM_JPAGEBUILDER_NO_ITEMS_FOUND' ) . '</p>';
			return $output;
		}

		if (count ( ( array ) $items )) {
			$output .= '<div class="jpb-addon jpb-addon-articles ' . $article_style . ' ' . $class . '">';

			if ($title) {
				$output .= '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>';
			}

			$output .= '<div class="jpb-addon-content">';
			$output .= '<div class="jpb-row">';

			foreach ( $items as $key => $item ) {
				$output .= '<div class="jpb-col-md-' . round ( 12 / $columns_md ) . ' jpb-col-sm-' . round ( 12 / $columns_sm ) . ' jpb-col-xs-' . round ( 12 / $columns_xs ) . '">';
				$output .= '<div class="jpb-addon-article">';

				if (! $hide_thumbnail) {
					$image = $item->image_thumbnail;

					if ($item->post_format == 'gallery') {
						if (count ( ( array ) $item->imagegallery->images )) {
							$output .= '<div class="jpb-carousel jpb-slide" data-jpb-ride="jpb-carousel">';
							$output .= '<div class="jpb-carousel-inner">';
							foreach ( $item->imagegallery->images as $key => $gallery_item ) {
								$active_class = '';
								if ($key == 0) {
									$active_class = ' active';
								}
								if (isset ( $gallery_item ['thumbnail'] ) && $gallery_item ['thumbnail']) {
									$output .= '<div class="jpb-item' . $active_class . '">';
									$output .= '<img src="' . $gallery_item ['thumbnail'] . '" alt="">';
									$output .= '</div>';
								} elseif (isset ( $gallery_item ['full'] ) && $gallery_item ['full']) {
									$output .= '<div class="jpb-item' . $active_class . '">';
									$output .= '<img src="' . $gallery_item ['full'] . '" alt="">';
									$output .= '</div>';
								}
							}
							$output .= '</div>';

							$output .= '<a class="left jpb-carousel-control" role="button" data-slide="prev" aria-label="' . Text::__ ( 'COM_JPAGEBUILDER_ARIA_PREVIOUS' ) . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
							$output .= '<a class="right jpb-carousel-control" role="button" data-slide="next" aria-label="' . Text::__ ( 'COM_JPAGEBUILDER_ARIA_NEXT' ) . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';

							$output .= '</div>';
						} elseif (isset ( $item->image_thumbnail ) && $item->image_thumbnail) {
							// Lazyload image
							$placeholder = $item->image_thumbnail == '' ? false : $this->get_image_placeholder ( $item->image_thumbnail );

							// Get image ALT text
							$img_obj = json_decode ( $item->images );
							$img_obj_helix = json_decode ( $item->attribs );

							$img_blog_op_alt_text = (isset ( $img_obj->image_intro_alt ) && $img_obj->image_intro_alt) ? $img_obj->image_intro_alt : "";
							$img_helix_alt_text = (isset ( $img_obj_helix->helix_ultimate_image_alt_txt ) && $img_obj_helix->helix_ultimate_image_alt_txt) ? $img_obj_helix->helix_ultimate_image_alt_txt : "";
							$img_alt_text = "";

							if ($img_helix_alt_text) {
								$img_alt_text = $img_helix_alt_text;
							} else if ($img_blog_op_alt_text) {
								$img_alt_text = $img_blog_op_alt_text;
							} else {
								$img_alt_text = $item->title;
							}

							$output .= '<a href="' . $item->link . '" itemprop="url"><img class="jpb-img-responsive' . ($placeholder && $page_view_name != 'form' ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder && $page_view_name != 'form' ? $placeholder : $item->image_thumbnail) . '" alt="' . $img_alt_text . '" itemprop="thumbnailUrl" ' . ($placeholder && $page_view_name != 'form' ? 'data-large="' . $image . '"' : '') . '  loading="lazy"></a>';
						}
					} elseif ($item->post_format == 'video' && isset ( $item->video_src ) && $item->video_src) {
						$output .= '<div class="entry-video embed-responsive embed-responsive-16by9">';
						$output .= '<object class="embed-responsive-item" style="width:100%;height:100%;" data="' . $item->video_src . '">';
						$output .= '<param name="movie" value="' . $item->video_src . '">';
						$output .= '<param name="wmode" value="transparent" />';
						$output .= '<param name="allowFullScreen" value="true">';
						$output .= '<param name="allowScriptAccess" value="always"></param>';
						$output .= '<embed src="' . $item->video_src . '" type="application/x-shockwave-flash" allowscriptaccess="always"></embed>';
						$output .= '</object>';
						$output .= '</div>';
					} elseif ($item->post_format == 'audio' && isset ( $item->audio_embed ) && $item->audio_embed) {
						$output .= '<div class="entry-audio embed-responsive embed-responsive-16by9">';
						$output .= $item->audio_embed;
						$output .= '</div>';
					} elseif ($item->post_format == 'link' && isset ( $item->link_url ) && $item->link_url) {
						$output .= '<div class="entry-link">';
						$output .= '<a target="_blank" rel="noopener noreferrer" href="' . $item->link_url . '"><h4>' . $item->link_title . '</h4></a>';
						$output .= '</div>';
					} else {
						if (isset ( $image ) && $image) {
							// Lazyload image
							$default_placeholder = $image == '' ? false : $this->get_image_placeholder ( $image );

							// Get image ALT text
							$img_obj = json_decode ( $item->images );
							$img_obj_helix = json_decode ( $item->attribs );

							$img_blog_op_alt_text = (isset ( $img_obj->image_intro_alt ) && $img_obj->image_intro_alt) ? $img_obj->image_intro_alt : "";
							$img_helix_alt_text = (isset ( $img_obj_helix->helix_ultimate_image_alt_txt ) && $img_obj_helix->helix_ultimate_image_alt_txt) ? $img_obj_helix->helix_ultimate_image_alt_txt : "";
							$img_alt_text = "";

							if ($img_helix_alt_text) {
								$img_alt_text = $img_helix_alt_text;
							} else if ($img_blog_op_alt_text) {
								$img_alt_text = $img_blog_op_alt_text;
							} else {
								$img_alt_text = $item->title;
							}

							if ($image_link) {
								$output .= '<a class="jpb-article-img-wrap" href="' . $item->link . '" itemprop="url"><img class="jpb-img-responsive' . ($default_placeholder && $page_view_name != 'form' ? ' jpb-element-lazy' : '') . '" src="' . ($default_placeholder && $page_view_name != 'form' ? $default_placeholder : $image) . '" alt="' . $img_alt_text . '" itemprop="thumbnailUrl" ' . ($default_placeholder && $page_view_name != 'form' ? 'data-large="' . $image . '"' : '') . ' loading="lazy"></a>';
							} else {
								$output .= '<div class="jpb-article-img-wrap"><img class="jpb-img-responsive' . ($default_placeholder && $page_view_name != 'form' ? ' jpb-element-lazy' : '') . '" src="' . ($default_placeholder && $page_view_name != 'form' ? $default_placeholder : $image) . '" alt="' . $img_alt_text . '" itemprop="thumbnailUrl" ' . ($default_placeholder && $page_view_name != 'form' ? 'data-large="' . $image . '"' : '') . ' loading="lazy"></div>';
							}
						}
					}
				}

				$output .= '<div class="jpb-article-info-wrap">';

				if ($show_category) {

					if ($show_category) {
						$item->catUrl = Route::_ ( Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute ( $item->catslug ) );
						$output .= '<div class="jpb-meta-category"><a href="' . $item->catUrl . '" itemprop="genre">' . $item->category . '</a></div>';
					}
				}

				$output .= '<div class="jpb-article-title">';

				$output .= '<h3><a href="' . $item->link . '" itemprop="url">' . $item->title . '</a></h3>';

				$output .= '</div>';

				if ($show_intro) {
					$output .= '<div class="jpb-article-introtext">' . mb_substr ( strip_tags ( $item->introtext ), 0, $intro_limit, 'UTF-8' ) . '...</div>';
				}

				if ($show_author || $show_date) {
					$output .= '<div class="jpb-article-meta">';

					if ($show_date) {
						$output .= '<span class="jpb-meta-date" itemprop="datePublished">' . HTMLHelper::_ ( 'date', $item->publish_up, 'DATE_FORMAT_LC3' ) . '</span>';
					}

					if ($show_author) {
						$author = ($item->created_by_alias ? $item->created_by_alias : $item->username);
						$output .= '<span class="jpb-meta-author" itemprop="name">' . "By " . $author . '</span>';
					}

					$output .= '</div>';
				}

				if ($show_readmore) {
					$output .= '<div class="jpb-readmore">';
					$output .= '<a href="' . $item->link . '" itemprop="url">' . $readmore_text . '</a>';
					$output .= '</div>';
				}
				$output .= '</div>'; // .jpb-article-info-wrap

				$output .= '</div>';
				$output .= '</div>';
			}

			$output .= '</div>';

			// See all link
			if ($link_articles) {

				$icon_arr = array_filter ( explode ( ' ', $all_articles_btn_icon ) );
				if (count ( $icon_arr ) === 1) {
					$all_articles_btn_icon = 'fa ' . $all_articles_btn_icon;
				}

				if ($all_articles_btn_icon_position == 'left') {
					$all_articles_btn_text = ($all_articles_btn_icon) ? '<i class="' . $all_articles_btn_icon . '" aria-hidden="true"></i> ' . $all_articles_btn_text : $all_articles_btn_text;
				} else {
					$all_articles_btn_text = ($all_articles_btn_icon) ? $all_articles_btn_text . ' <i class="' . $all_articles_btn_icon . '" aria-hidden="true"></i>' : $all_articles_btn_text;
				}

				if (! empty ( $link_catid )) {
					$output .= '<a href="' . Route::_ ( Joomla\Component\Content\Site\Helper\RouteHelper::getCategoryRoute ( $link_catid ) ) . '" id="btn-' . $this->addon->id . '" class="jpb-btn' . $all_articles_btn_class . '">' . $all_articles_btn_text . '</a>';
				}
			}

			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$layout_path = JPATH_ROOT . '/components/com_jpagebuilder/layouts';
		$css_path = new FileLayout ( 'addon.css.button', $layout_path );

		$options = new stdClass ();
		$options->button_type = (isset ( $this->addon->settings->all_articles_btn_type ) && $this->addon->settings->all_articles_btn_type) ? $this->addon->settings->all_articles_btn_type : '';
		$options->button_appearance = (isset ( $this->addon->settings->all_articles_btn_appearance ) && $this->addon->settings->all_articles_btn_appearance) ? $this->addon->settings->all_articles_btn_appearance : '';
		$options->button_color = (isset ( $this->addon->settings->all_articles_btn_color ) && $this->addon->settings->all_articles_btn_color) ? $this->addon->settings->all_articles_btn_color : '';
		$options->button_color_hover = (isset ( $this->addon->settings->all_articles_btn_color_hover ) && $this->addon->settings->all_articles_btn_color_hover) ? $this->addon->settings->all_articles_btn_color_hover : '';
		$options->button_background_color = (isset ( $this->addon->settings->all_articles_btn_background_color ) && $this->addon->settings->all_articles_btn_background_color) ? $this->addon->settings->all_articles_btn_background_color : '';
		$options->button_background_color_hover = (isset ( $this->addon->settings->all_articles_btn_background_color_hover ) && $this->addon->settings->all_articles_btn_background_color_hover) ? $this->addon->settings->all_articles_btn_background_color_hover : '';
		$options->button_fontstyle = (isset ( $this->addon->settings->all_articles_btn_font_style ) && $this->addon->settings->all_articles_btn_font_style) ? $this->addon->settings->all_articles_btn_font_style : '';
		$options->button_font_style = (isset ( $this->addon->settings->all_articles_btn_font_style ) && $this->addon->settings->all_articles_btn_font_style) ? $this->addon->settings->all_articles_btn_font_style : '';
		$options->button_letterspace = (isset ( $this->addon->settings->all_articles_btn_letterspace ) && $this->addon->settings->all_articles_btn_letterspace) ? $this->addon->settings->all_articles_btn_letterspace : '';

		return $css_path->render ( array (
				'addon_id' => $addon_id,
				'options' => $options,
				'id' => 'btn-' . $this->addon->id
		) );
	}
	static function isComponentInstalled($component_name) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'a.enabled' );
		$query->from ( $db->quoteName ( '#__extensions', 'a' ) );
		$query->where ( $db->quoteName ( 'a.name' ) . " = " . $db->quote ( $component_name ) );
		$db->setQuery ( $query );
		$is_enabled = $db->loadResult ();
		return $is_enabled;
	}
}
