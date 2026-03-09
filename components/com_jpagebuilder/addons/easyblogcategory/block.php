<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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

class JpagebuilderAddonEasyblogcategory extends JpagebuilderAddons {
	
	public function render() {
		$settings = $this->addon->settings;
		$categoryId = !empty($settings->category_id) ? (int) $settings->category_id : 0;
		
		if (!$categoryId) {
			return '<div style="padding:1rem; margin-bottom:1rem; border:1px solid #f5c2c7; border-radius:0.375rem; background-color:#f8d7da; color:#842029; font-size:1rem; line-height:1.5; position:relative;">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_NO_POST_SELECTED') . '</div>';
		}
		
		$limit = !empty($settings->limit) ? (int) $settings->limit : 10;
		$ordering = !empty($settings->ordering) ? $settings->ordering : 'created_desc';
		$layout = !empty($settings->layout) ? $settings->layout : 'list';
		
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Query per recuperare i post della categoria
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('p.*')
			  ->from($db->quoteName('#__easyblog_post', 'p'))
			  ->where($db->quoteName('p.published') . ' = 1')
			  ->where($db->quoteName('p.state') . ' = 0');
		
		// Filtro per categoria
		if ($categoryId > 0) {
			$query->innerJoin(
					$db->quoteName('#__easyblog_post_category', 'pc') .
					' ON ' . $db->quoteName('pc.post_id') . ' = ' . $db->quoteName('p.id')
					);
			$query->where($db->quoteName('pc.category_id') . ' = ' . (int) $categoryId);
		}
		
		// Ordinamento
		switch ($ordering) {
			case 'created_desc':
				$query->order($db->quoteName('p.created') . ' DESC');
				break;
			case 'created_asc':
				$query->order($db->quoteName('p.created') . ' ASC');
				break;
			case 'title_asc':
				$query->order($db->quoteName('p.title') . ' ASC');
				break;
			case 'title_desc':
				$query->order($db->quoteName('p.title') . ' DESC');
				break;
			case 'hits_desc':
				$query->order($db->quoteName('p.hits') . ' DESC');
				break;
		}
		
		$query->setLimit($limit);
		
		$db->setQuery($query);
		$posts = $db->loadObjectList();
		
		if (empty($posts)) {
			return '<div class="alert alert-info">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY_NO_POSTS') . '</div>';
		}
		
		// Inizia il rendering
		$html = '<div class="eb-posts eb-posts-' . $layout . '">';
		
		foreach ($posts as $post) {
			// Recupera il link e l'ID della pagina JPageBuilder
			$jpageData = $this->getJPageBuilderData($post->id);
			
			// Recupera autore
			$author = $this->getAuthor($post->created_by);
			
			// Recupera categoria primaria
			$category = $this->getPrimaryCategory($post->id);
			
			$html .= $this->renderPost($post, $jpageData, $author, $category, $settings, $layout);
		}
		
		$html .= '</div>';
		
		return $html;
	}
	
	public function stylesheets() {
		return array (
				'components/com_jpagebuilder/addons/easyblogcategory/assets/css/easyblogcategory.css'
		);
	}
	
	/**
	 * Trova la pagina JPageBuilder che contiene questo post e restituisce link + hits
	 */
	private function getJPageBuilderData($postId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		// Cerca nelle pagine JPageBuilder
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('id, title, content, extension, extension_view, view_id, language, hits')
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
			if ($this->findPostInContent($content, $postId)) {
				// È una pagina standalone di JPageBuilder
				return (object)[
						'link' => JpagebuilderHelperRoute::getPageRoute($page->id, $page->language),
						'pageId' => $page->id,
						'hits' => (int)$page->hits
				];
			}
		}
		
		// Fallback: se non trova una pagina JPageBuilder, usa il link nativo EasyBlog
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('id')
			  ->from($db->quoteName('#__menu'))
			  ->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_easyblog&view=latest'))
			  ->where($db->quoteName('published') . ' = 1')
			  ->where($db->quoteName('client_id') . ' = 0');
		
		$db->setQuery( $query );
		$topLatestMenuItemids = $db->loadColumn();
		
		return (object)[
				'link' => Route::_('index.php?option=com_easyblog&view=entry&id=' . $postId . '&Itemid=' . (!empty($topLatestMenuItemids) ? (int) $topLatestMenuItemids[0] : '')),
				'pageId' => 0,
				'hits' => 0
		];
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
	 * Recupera l'autore
	 */
	private function getAuthor($userId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('nickname AS name, avatar')
			  ->from($db->quoteName('#__easyblog_users'))
			  ->where($db->quoteName('id') . ' = ' . (int) $userId);
		
		$db->setQuery ( $query );
		$ebUser = $db->loadObject ();
		
		// Fallback to Joomla user table
		if( !$ebUser ) {
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select('name')
				  ->from($db->quoteName('#__users'))
				  ->where($db->quoteName('id') . ' = ' . (int) $userId);
			
			$db->setQuery($query);
			return $db->loadObject();
		} else {
			return $ebUser;
		}
	}
	
	/**
	 * Recupera la categoria primaria
	 */
	private function getPrimaryCategory($postId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select('c.*')
			  ->from($db->quoteName('#__easyblog_post_category', 'pc'))
			  ->leftJoin($db->quoteName('#__easyblog_category', 'c') . ' ON ' . $db->quoteName('pc.category_id') . ' = ' . $db->quoteName('c.id'))
			  ->where($db->quoteName('pc.post_id') . ' = ' . (int) $postId)
			  ->where($db->quoteName('pc.primary') . ' = 1');
		
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * Renderizza un singolo post
	 */
	private function renderPost($post, $jpageData, $author, $category, $settings, $layout) {
		$showIntro = isset($settings->show_intro) ? $settings->show_intro : 1;
		$showImage = isset($settings->show_image) ? $settings->show_image : 1;
		$showCategory = !isset($settings->show_category) || $settings->show_category;
		$showAuthor = !isset($settings->show_author) || $settings->show_author;
		$showCreateDate = !isset($settings->show_create_date) || $settings->show_create_date;
		$showModifyDate = isset($settings->show_modify_date) && $settings->show_modify_date;
		$showHits = isset($settings->show_hits) && $settings->show_hits;
		
		$html = '<article class="eb-post eb-post-item">';
		
		// Immagine
		if ($showImage && !empty($post->image)) {
			// Converti il percorso immagine EasyBlog
			$imageUrl = $post->image;
			if (strpos($imageUrl, 'post:') === 0) {
				$imagePath = substr($imageUrl, 5);
				$parts = explode('/', $imagePath, 2);
				if (count($parts) == 2) {
					$imageUrl = Uri::root(false) . 'images/easyblog_articles/' . $parts[0] . '/b2ap3_large_' . $parts[1];
				}
			} elseif (strpos($imageUrl, 'http') !== 0 && strpos($imageUrl, '/') !== 0) {
				$imageUrl = Uri::root(false) . $imageUrl;
			}
			
			$html .= '<div class="eb-post-thumb">';
			$html .= '<a href="' . $jpageData->link . '">';
			$html .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="' . htmlspecialchars($post->title) . '" class="eb-post-image" />';
			$html .= '</a>';
			$html .= '</div>';
		}
		
		$html .= '<div class="eb-post-content">';

		// Titolo
		$html .= '<h2 class="eb-post-title">';
		$html .= '<a href="' . $jpageData->link . '">' . htmlspecialchars($post->title) . '</a>';
		$html .= '</h2>';
		
		// Meta
		if ($showAuthor || $showCategory || $showCreateDate || $showModifyDate || $showHits) {
			$html .= '<div class="eb-post-meta">';
			
			// Autore
			if ($showAuthor && !empty($author)) {
				$html .= '<div class="eb-post-meta-author">';

				if (! empty ( $author->avatar )) {
					$userAvatar = $author->avatar != 'default_blogger.png' ? 'images/easyblog_avatar/' . $author->avatar : 'media/com_easyblog/images/avatars/author.png';
					$html .= '<span class="eb-meta-author-avatar">';
					$html .= '<img src="' . htmlspecialchars ( Uri::root(false) . $userAvatar ) . '" alt="' . htmlspecialchars ( $author->name ) . '" class="eb-author-avatar-img" />';
					$html .= '</span>';
				}
				
				$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_AUTHOR') . ': </span>';
				$html .= '<span>' . htmlspecialchars($author->name) . '</span>';
				$html .= '</div>';
			}
			
			// Categoria
			if ($showCategory && !empty($category)) {
				$html .= '<div class="eb-post-meta-category">';
				$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY') . ': </span>';
				$html .= '<span>' . htmlspecialchars($category->title) . '</span>';
				$html .= '</div>';
			}
			
			// Data creazione
			if ($showCreateDate && !empty($post->created)) {
				$html .= '<div class="eb-post-meta-date">';
				$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CREATED_DATE') . ': </span>';
				$html .= '<time datetime="' . HTMLHelper::_('date', $post->created, 'c') . '">';
				$html .= HTMLHelper::_('date', $post->created, Text::_('DATE_FORMAT_LC2'));
				$html .= '</time>';
				$html .= '</div>';
			}
			
			// Data modifica
			if ($showModifyDate && !empty($post->modified) && $post->modified != '0000-00-00 00:00:00') {
				$html .= '<div class="eb-post-meta-modified">';
				$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_MODIFIED_DATE') . ': </span>';
				$html .= '<time datetime="' . HTMLHelper::_('date', $post->modified, 'c') . '">';
				$html .= HTMLHelper::_('date', $post->modified, Text::_('DATE_FORMAT_LC2'));
				$html .= '</time>';
				$html .= '</div>';
			}
			
			// Visite - Somma le hits del post EasyBlog + hits della pagina JPageBuilder
			if ($showHits) {
				$totalHits = (int)$post->hits + (int)$jpageData->hits;
				
				$html .= '<div class="eb-post-meta-hits">';
				$html .= '<span class="eb-meta-label">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_HITS') . ': </span>';
				$html .= '<span>' . $totalHits . '</span>';
				$html .= '</div>';
			}
			
			$html .= '</div>'; // Fine meta
		}
		
		// Intro text
		if ($showIntro && !empty($post->intro)) {
			$html .= '<div class="eb-post-intro">';
			
			// Estrae solo testo semplice dal contenuto EBD
			$introText = strip_tags($post->intro);
			$introText = substr($introText, 0, 250);
			if (strlen($post->intro) > 250) {
				$introText .= '...';
			}
			
			$html .= '<p>' . $introText . '</p>';
			$html .= '</div>';
		}
		
		// Link "Read more"
		$html .= '<div class="eb-post-more">';
		$html .= '<a href="' . $jpageData->link . '" class="btn btn-primary">' . Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_READ_MORE') . '</a>';
		$html .= '</div>';
		
		$html .= '</div>'; // Fine content
		$html .= '</article>';
		
		return $html;
	}
}