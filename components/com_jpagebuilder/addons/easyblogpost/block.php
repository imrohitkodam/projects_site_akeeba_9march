<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined('_JEXEC') or die('Restricted access');

class JpagebuilderAddonEasyblogpost extends JpagebuilderAddons {
	
	public function render() {
		$settings = $this->addon->settings;
		$postId = !empty($settings->post_id) ? (int) $settings->post_id : 0;

		if (!$postId) {
			return '<div style="padding:1rem; margin-bottom:1rem; border:1px solid #f5c2c7; border-radius:0.375rem; background-color:#f8d7da; color:#842029; font-size:1rem; line-height:1.5; position:relative;">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_NO_POST_SELECTED') . '</div>';
		}
		
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Recupera il post
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('p.*')
			  ->from($db->quoteName('#__easyblog_post', 'p'))
			  ->where($db->quoteName('p.id') . ' = ' . (int) $postId);
		
		$db->setQuery($query);
		$post = $db->loadObject();
		
		if (!$post) {
			return '<div style="padding:1rem; margin-bottom:1rem; border:1px solid #f5c2c7; border-radius:0.375rem; background-color:#f8d7da; color:#842029; font-size:1rem; line-height:1.5; position:relative;">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_POST_NOT_FOUND') . '</div>';
		}
		
		// Recupera la categoria primaria
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('c.*')
			  ->from($db->quoteName('#__easyblog_post_category', 'pc'))
			  ->leftJoin($db->quoteName('#__easyblog_category', 'c') . ' ON ' . $db->quoteName('pc.category_id') . ' = ' . $db->quoteName('c.id'))
			  ->where($db->quoteName('pc.post_id') . ' = ' . (int) $postId)
			  ->where($db->quoteName('pc.primary') . ' = 1');
		
		$db->setQuery($query);
		$category = $db->loadObject();
		
		// Recupera l'autore
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('nickname AS name, avatar')
			  ->from($db->quoteName('#__easyblog_users'))
			  ->where($db->quoteName('id') . ' = ' . (int) $post->created_by);
		
		$db->setQuery ( $query );
		$author = $db->loadObject ();
		
		// Fallback to Joomla user table
		if( !$author ) {
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select('name, username')
				  ->from($db->quoteName('#__users'))
				  ->where($db->quoteName('id') . ' = ' . (int) $post->created_by);
			
			$db->setQuery($query);
			$author = $db->loadObject();
		}
		
		// Recupera i tag (se esistono)
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('t.title')
			  ->from($db->quoteName('#__easyblog_post_tag', 'pt'))
			  ->leftJoin($db->quoteName('#__easyblog_tag', 't') . ' ON ' . $db->quoteName('pt.tag_id') . ' = ' . $db->quoteName('t.id'))
			  ->where($db->quoteName('pt.post_id') . ' = ' . (int) $postId);
		
		$db->setQuery($query);
		$tags = $db->loadObjectList();
		
		// Parse parameters
		$params = new Registry($post->params ?: '{}');
		
		// Inizio output
		$html = '<article class="eb-post-item easyblog-post" itemscope itemtype="http://schema.org/BlogPosting">';
		
		// Titolo
		if (!isset($settings->show_title) || $settings->show_title) {
			$html .= '<header class="eb-post-header">';
			
			if (!empty($settings->link_title)) {
				$link = $this->getPostLink($post);
				$html .= '<h2 class="eb-post-title" itemprop="headline"><a href="' . $link . '">' . htmlspecialchars($post->title) . '</a></h2>';
			} else {
				$html .= '<h2 class="eb-post-title" itemprop="headline">' . htmlspecialchars($post->title) . '</h2>';
			}
			
			$html .= '</header>';
		}
		
		// Info Block (sopra)
		$infoPosition = isset($settings->info_block_position) ? $settings->info_block_position : '0';
		if ($infoPosition == '0' || $infoPosition == '2') {
			$html .= $this->renderInfoBlock($settings, $post, $author, $category, $tags, true);
		}
		
		// Immagine di copertina
		if (!empty($post->image) && (!isset($settings->show_image) || $settings->show_image)) {
			$imageUrl = $post->image;
			if (strpos($imageUrl, 'post:') === 0) {
				$imagePath = substr($imageUrl, 5);
				$parts = explode('/', $imagePath, 2);
				if (count($parts) == 2) {
					$imageUrl = Uri::root(false) . 'images/easyblog_articles/' . $parts[0] . '/' . $parts[1];
				}
			} elseif (strpos($imageUrl, 'http') !== 0 && strpos($imageUrl, '/') !== 0) {
				$imageUrl = Uri::root(false) . $imageUrl;
			}
			
			$html .= '<div class="eb-post-image">';
			$html .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="' . htmlspecialchars($post->title) . '" class="eb-post-image" itemprop="image" />';
			$html .= '</div>';
		}
		
		// Contenuto
		$html .= '<div class="eb-post-content" itemprop="articleBody">';
		
		// Intro text
		if (!empty($post->intro) && (!isset($settings->show_intro) || $settings->show_intro)) {
			$html .= '<div class="eb-post-intro">' . $post->intro . '</div>';
		}
		
		// Full content
		if (!isset($settings->show_full_content) || $settings->show_full_content) {
			$html .= '<div class="eb-post-body">' . $post->content . '</div>';
		}
		
		$html .= '</div>'; // Fine contenuto
		
		// Info Block (sotto)
		if ($infoPosition == '1') {
			$html .= $this->renderInfoBlock($settings, $post, $author, $category, $tags, false);
		}
		
		// Tags
		if (!empty($tags) && (!isset($settings->show_tags) || $settings->show_tags)) {
			$html .= '<div class="eb-post-tags">';
			$html .= '<span class="eb-tags-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_TAGS') . ': </span>';
			foreach ($tags as $i => $tag) {
				if ($i > 0) $html .= ', ';
				$html .= '<span class="eb-tag" itemprop="keywords">' . htmlspecialchars($tag->title) . '</span>';
			}
			$html .= '</div>';
		}
		
		$html .= '</article>';
		
		return $html;
	}
	
	public function stylesheets() {
		return array (
				'components/com_jpagebuilder/addons/easyblogpost/assets/css/easyblogpost.css'
		);
	}
	
	/**
	 * Trova il link alla pagina JPageBuilder che contiene questo post di EasyBlog
	 */
	private function getPostLink($post) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Cerca nelle pagine JPageBuilder
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('id, title, content, extension, extension_view, view_id, language')
			  ->from($db->quoteName('#__jpagebuilder'))
			  ->where($db->quoteName('published') . ' = 1')
			  ->where($db->quoteName('extension') . ' = ' . $db->quote('com_jpagebuilder'))
			  ->where($db->quoteName('extension_view') . ' = ' . $db->quote('page'))
			  ->where($db->quoteName('published') . ' = 1');
		
		$db->setQuery($query);
		$pages = $db->loadObjectList();
		
		// Cerca quale pagina contiene questo post
		foreach ($pages as $page) {
			if (empty($page->content)) {
				continue;
			}
			
			// Decodifica il JSON
			$content = json_decode($page->content);
			if (!$content) {
				continue;
			}
			
			// Cerca ricorsivamente nell'addon
			if ($this->findPostInContent($content, $post->id)) {
				// È una pagina standalone di JPageBuilder
				return JpagebuilderHelperRoute::getPageRoute($page->id, $page->language);
			}
		}
		
		// Fallback: se non trova una pagina JPageBuilder, usa il link nativo EasyBlog
		$link = 'index.php?option=com_easyblog&view=entry&id=' . $post->id;
		
		if (!empty($post->permalink)) {
			$link .= '&permalink=' . urlencode($post->permalink);
		}
		
		return Route::_($link);
	}
	
	/**
	 * Trova il link alla pagina JPageBuilder che contiene questo post di EasyBlog
	 */
	private function getJPageBuilderLink($categoryId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Cerca nelle pagine JPageBuilder
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('id, title, content, extension, extension_view, view_id, language')
			  ->from($db->quoteName('#__jpagebuilder'))
			  ->where($db->quoteName('extension') . ' = ' . $db->quote('com_jpagebuilder'))
			  ->where($db->quoteName('extension_view') . ' = ' . $db->quote('page'))
			  ->where($db->quoteName('published') . ' = 1');
		
		$db->setQuery($query);
		$pages = $db->loadObjectList();
		
		// Cerca quale pagina contiene questo post
		foreach ($pages as $page) {
			if (empty($page->content)) {
				continue;
			}
			
			// Decodifica il JSON
			$content = json_decode($page->content);
			if (!$content) {
				continue;
			}
			
			// Cerca ricorsivamente nell'addon
			if ($this->findCategoryInContent($content, $categoryId)) {
				// È una pagina standalone di JPageBuilder
				return JpagebuilderHelperRoute::getPageRoute($page->id, $page->language);
			}
		}
		
		// Fallback: se non trova una pagina JPageBuilder, usa il link nativo EasyBlog
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('id')
			  ->from($db->quoteName('#__menu'))
			  ->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $categoryId))
			  ->where($db->quoteName('published') . ' = 1')
			  ->where($db->quoteName('client_id') . ' = 0');
		
		$db->setQuery( $query );
		$categoryItemids = $db->loadColumn();
		
		return Route::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $categoryId . '&Itemid=' . (!empty($categoryItemids) ? (int) $categoryItemids[0] : ''));
	}
	
	/**
	 * Cerca ricorsivamente nel contenuto JSON se contiene il post specificato
	 */
	private function findCategoryInContent($content, $categoryId) {
		if (is_array($content)) {
			foreach ($content as $item) {
				if ($this->findCategoryInContent($item, $categoryId)) {
					return true;
				}
			}
		} elseif (is_object($content)) {
			// Controlla se è un addon easyblogpost con il post_id corretto
			if (isset($content->name) && $content->name === 'easyblogcategory') {
				if (isset($content->settings->category_id) && $content->settings->category_id == $categoryId) {
					return true;
				}
			}
			
			// Continua la ricerca ricorsiva
			foreach ($content as $value) {
				if ($this->findCategoryInContent($value, $categoryId)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Cerca ricorsivamente nel contenuto JSON se contiene il post specificato
	 */
	private function findPostInContent($content, $postId) {
		if (is_array($content)) {
			foreach ($content as $item) {
				if ($this->findPostInContent($item, $postId)) {
					return true;
				}
			}
		} elseif (is_object($content)) {
			// Controlla se è un addon easyblogpost con il post_id corretto
			if (isset($content->name) && $content->name === 'easyblogpost') {
				if (isset($content->settings->post_id) && $content->settings->post_id == $postId) {
					return true;
				}
			}
			
			// Continua la ricerca ricorsiva
			foreach ($content as $value) {
				if ($this->findPostInContent($value, $postId)) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Renderizza il blocco informazioni
	 */
	private function renderInfoBlock($settings, $post, $author, $category, $tags, $isTop) {
		$html = '';
		$infoPosition = isset($settings->info_block_position) ? $settings->info_block_position : '0';
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Se split, mostra solo alcune info in alto e altre in basso
		$showAuthor = !isset($settings->show_author) || $settings->show_author;
		$showCategory = !isset($settings->show_category) || $settings->show_category;
		$showCreateDate = !isset($settings->show_create_date) || $settings->show_create_date;
		$showModifyDate = isset($settings->show_modify_date) && $settings->show_modify_date;
		$showHits = isset($settings->show_hits) && $settings->show_hits;
		
		if ($infoPosition == '2') {
			// Split mode: in alto autore e categoria, in basso date e hits
			if ($isTop) {
				$showCreateDate = false;
				$showModifyDate = false;
				$showHits = false;
			} else {
				$showAuthor = false;
				$showCategory = false;
			}
		}
		
		if (!$showAuthor && !$showCategory && !$showCreateDate && !$showModifyDate && !$showHits) {
			return '';
		}
		
		$html .= '<div class="eb-post-meta">';
		
		// Autore
		if ($showAuthor && !empty($author)) {
			$html .= '<div class="eb-meta-author" itemprop="author" itemscope itemtype="http://schema.org/Person">';
			
			if (! empty ( $author->avatar )) {
				$userAvatar = $author->avatar != 'default_blogger.png' ? 'images/easyblog_avatar/' . $author->avatar : 'media/com_easyblog/images/avatars/author.png';
				$html .= '<span class="eb-meta-author-avatar">';
				$html .= '<img src="' . htmlspecialchars ( Uri::root(false) . $userAvatar ) . '" alt="' . htmlspecialchars ( $author->name ) . '" class="eb-author-avatar-img" />';
				$html .= '</span>';
			}
			
			$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_AUTHOR') . ': </span>';
			$html .= '<span itemprop="name">' . htmlspecialchars($author->name) . '</span>';
			$html .= '</div>';
		}
		
		// Categoria
		if ($showCategory && !empty($category)) {
			$html .= '<div class="eb-meta-category">';
			$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY') . ': </span>';
			
			if (!empty($settings->link_category)) {
				$catLink = $this->getJPageBuilderLink($category->id);
				$html .= '<a href="' . $catLink . '" itemprop="genre">' . htmlspecialchars($category->title) . '</a>';
			} else {
				$html .= '<span itemprop="genre">' . htmlspecialchars($category->title) . '</span>';
			}
			$html .= '</div>';
		}
		
		// Data creazione
		if ($showCreateDate && !empty($post->created)) {
			$html .= '<div class="eb-meta-date">';
			$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CREATED_DATE') . ': </span>';
			$html .= '<time datetime="' . HTMLHelper::_('date', $post->created, 'c') . '" itemprop="datePublished">';
			$html .= HTMLHelper::_('date', $post->created, Text::_('DATE_FORMAT_LC2'));
			$html .= '</time>';
			$html .= '</div>';
		}
		
		// Data modifica
		if ($showModifyDate && !empty($post->modified) && $post->modified != '0000-00-00 00:00:00') {
			$html .= '<div class="eb-meta-modified">';
			$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_MODIFIED_DATE') . ': </span>';
			$html .= '<time datetime="' . HTMLHelper::_('date', $post->modified, 'c') . '" itemprop="dateModified">';
			$html .= HTMLHelper::_('date', $post->modified, Text::_('DATE_FORMAT_LC2'));
			$html .= '</time>';
			$html .= '</div>';
		}
		
		// Visite
		if ($showHits) {
			// Estrae l'ID numerico da 'page-5' -> 5
			$pageName = $this->addon->pageName ?? '';
			$pageId = 0;
			
			if (preg_match('/page-(\d+)/', $pageName, $matches)) {
				$pageId = (int)$matches[1];
			}
			
			$pageHits = 0;
			if ($pageId > 0) {
				$query = method_exists($db, 'createQuery') ? $db->createQuery() : $db->getQuery(true);
				$query->select('hits')
					  ->from($db->quoteName('#__jpagebuilder'))
					  ->where($db->quoteName('id') . ' = ' . (int)$pageId);
				
				$db->setQuery($query);
				$pageHits = (int)$db->loadResult();
			}
			
			$html .= '<div class="eb-meta-hits">';
			$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_HITS') . ': </span>';
			$html .= '<span itemprop="interactionCount">' . ((int)$post->hits + $pageHits) . '</span>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		
		return $html;
	}
}