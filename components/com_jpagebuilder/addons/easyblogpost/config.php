<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined('_JEXEC') or die('Restricted access');

/**
 * Helper function per recuperare la lista dei post di EasyBlog
 */
function JPagebuilderGetEasyBlogPostsList() {
	$db = Factory::getContainer()->get('DatabaseDriver');
	$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
	
	$query->select('p.id, p.title, p.created, p.published, c.title as category_title')
		  ->from($db->quoteName('#__easyblog_post', 'p'))
		  ->leftJoin($db->quoteName('#__easyblog_post_category', 'pc') . ' ON ' . $db->quoteName('pc.post_id') . ' = ' . $db->quoteName('p.id') . ' AND ' . $db->quoteName('pc.primary') . ' = 1')
		  ->leftJoin($db->quoteName('#__easyblog_category', 'c') . ' ON ' . $db->quoteName('pc.category_id') . ' = ' . $db->quoteName('c.id'))
		  ->where($db->quoteName('p.state') . ' >= 0')
		  ->order($db->quoteName('p.created') . ' DESC');
	
	try {
		$db->setQuery($query);
		$posts = $db->loadObjectList();
		
		$options = [];
		$options[''] = Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SELECT_POST');
		
		if (!empty($posts)) {
			foreach ($posts as $post) {
				$label = $post->title;
				
				if (!empty($post->category_title)) {
					$label .= ' (' . $post->category_title . ')';
				}
				
				// Aggiungi stato pubblicazione
				if ($post->published == 0) {
					$label .= ' [' . Text::_('JUNPUBLISHED') . ']';
				}
				
				$options[$post->id] = $label;
			}
		}
		
		return $options;
	} catch (Exception $e) {
		// Se le tabelle non esistono, restituisci array vuoto
		return ['' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_NOT_INSTALLED')];
	}
}

JpagebuilderConfig::addonConfig([
	'type' => 'content',
	'addon_name' => 'easyblogpost',
	'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_TITLE'),
	'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_DESC'),
	'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M28 4H4a2 2 0 00-2 2v20a2 2 0 002 2h24a2 2 0 002-2V6a2 2 0 00-2-2zm0 2v4H4V6h24zM4 26V12h24v14H4z" fill="currentColor"/><path d="M6 14h12v2H6zM6 18h16v2H6zM6 22h10v2H6z" fill="currentColor"/></svg>',
	'category' => 'Content',
	'settings' => [
		'general' => [
			'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SETTINGS'),
			'fields' => [
				'post_id' => [
					'type' => 'select',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SELECT_POST'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SELECT_POST_DESC'),
					'values' => JPagebuilderGetEasyBlogPostsList(),
					'std' => ''
				],
				'separator_display' => [
					'type' => 'separator',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_DISPLAY_OPTIONS')
				],
				'show_title' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_TITLE'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_TITLE_DESC'),
					'std' => '1'
				],
				'link_title' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LINK_TITLE'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LINK_TITLE_DESC'),
					'std' => '0'
				],
				'show_image' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_IMAGE'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_IMAGE_DESC'),
					'std' => '1'
				],
				'show_intro' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_INTRO'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_INTRO_DESC'),
					'std' => '1'
				],
				'show_full_content' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_FULL_CONTENT'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_FULL_CONTENT_DESC'),
					'std' => '1'
				],
				'show_tags' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_TAGS'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_TAGS_DESC'),
					'std' => '1'
				],
				'separator_info' => [
					'type' => 'separator',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_INFO_BLOCK')
				],
				'info_block_position' => [
					'type' => 'select',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_INFO_POSITION'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_INFO_POSITION_DESC'),
					'values' => [
						'0' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_POSITION_ABOVE'),
						'1' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_POSITION_BELOW'),
						'2' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_POSITION_SPLIT')
					],
					'std' => '0'
				],
				'show_category' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CATEGORY'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CATEGORY_DESC'),
					'std' => '1'
				],
				'link_category' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LINK_CATEGORY'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LINK_CATEGORY_DESC'),
					'std' => '1'
				],
				'show_author' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_AUTHOR'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_AUTHOR_DESC'),
					'std' => '1'
				],
				'show_create_date' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CREATE_DATE'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CREATE_DATE_DESC'),
					'std' => '1'
				],
				'show_modify_date' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_MODIFY_DATE'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_MODIFY_DATE_DESC'),
					'std' => '0'
				],
				'show_hits' => [
					'type' => 'checkbox',
					'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_HITS'),
					'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_HITS_DESC'),
					'std' => '1'
				]
			]
		]
	]
]);