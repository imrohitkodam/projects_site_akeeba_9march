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
 * Helper function per recuperare la lista delle categorie di EasyBlog
 */
function JPageBuilderGetEasyBlogCategoriesList() {
	$db = Factory::getContainer()->get('DatabaseDriver');
	$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
	
	$query->select('c.id, c.title, c.parent_id, c.lft, c.rgt')
		  ->from($db->quoteName('#__easyblog_category', 'c'))
		  ->where($db->quoteName('c.published') . ' = 1')
		  ->order($db->quoteName('c.lft') . ' ASC'); // Ordina per lft per rispettare la gerarchia
	
	try {
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		$options = [];
		$options[''] = Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ALL_CATEGORIES');
		
		if (!empty($categories)) {
			// Calcola il livello di ogni categoria usando lft/rgt
			$levels = [];
			foreach ($categories as $category) {
				$level = 0;
				// Conta quante categorie contengono questa (lft < current.lft AND rgt > current.rgt)
				foreach ($categories as $parent) {
					if ($parent->lft < $category->lft && $parent->rgt > $category->rgt) {
						$level++;
					}
				}
				$levels[$category->id] = $level;
			}
			
			// Costruisci le opzioni con indentazione
			foreach ($categories as $category) {
				$level = $levels[$category->id];
				$indent = str_repeat('|- ', $level);
				$options[$category->id] = $indent . $category->title;
			}
		}
		
		return $options;
	} catch (Exception $e) {
		return ['' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_NOT_INSTALLED')];
	}
}

JpagebuilderConfig::addonConfig([
		'type' => 'content',
		'addon_name' => 'easyblogcategory',
		'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY_TITLE'),
		'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY_DESC'),
		'icon' => '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h24v4H4zM4 12h24v4H4zM4 18h24v4H4zM4 24h24v4H4z" fill="currentColor"/></svg>',
		'category' => 'Content',
		'settings' => [
				'general' => [
						'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_CATEGORY_SETTINGS'),
						'fields' => [
								'category_id' => [
										'type' => 'select',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SELECT_CATEGORY'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SELECT_CATEGORY_DESC'),
										'values' => JPageBuilderGetEasyBlogCategoriesList(),
										'std' => ''
								],
								'separator_display' => [
										'type' => 'separator',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_DISPLAY_OPTIONS')
								],
								'limit' => [
										'type' => 'number',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LIMIT'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LIMIT_DESC'),
										'std' => '10',
										'min' => 1,
										'max' => 100
								],
								'ordering' => [
										'type' => 'select',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_DESC'),
										'values' => [
												'created_desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_CREATED_DESC'),
												'created_asc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_CREATED_ASC'),
												'title_asc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_TITLE_ASC'),
												'title_desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_TITLE_DESC'),
												'hits_desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_ORDERING_HITS_DESC')
										],
										'std' => 'created_desc'
								],
								'layout' => [
										'type' => 'select',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LAYOUT'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LAYOUT_DESC'),
										'values' => [
												'list' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LAYOUT_LIST'),
												'grid' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LAYOUT_GRID'),
												'masonry' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_LAYOUT_MASONRY')
										],
										'std' => 'list'
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
								'separator_info' => [
										'type' => 'separator',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_INFO_BLOCK')
								],
								'show_category' => [
										'type' => 'checkbox',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CATEGORY'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_SHOW_CATEGORY_DESC'),
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
								],
								'separator_columns' => [
										'type' => 'separator',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_COLUMNS_SETTINGS'),
										'depends' => [
												['layout', '!=', 'list']
										]
								],
								'columns' => [
										'type' => 'select',
										'title' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_COLUMNS'),
										'desc' => Text::_('COM_JPAGEBUILDER_ADDON_EASYBLOG_COLUMNS_DESC'),
										'values' => [
												'2' => '2',
												'3' => '3',
												'4' => '4'
										],
										'std' => '3',
										'depends' => [
												['layout', '!=', 'list']
										]
								]
						]
				]
		]
]);