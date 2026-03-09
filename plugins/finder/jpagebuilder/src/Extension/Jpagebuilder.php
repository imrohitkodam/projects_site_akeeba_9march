<?php
/**
 * @package JPageBuilder Finder Plugins
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace JExtstore\Plugin\Finder\Jpagebuilder\Extension;

defined ( '_JEXEC' ) or die ();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\Event;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseQuery;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Component\Finder\Administrator\Indexer\Indexer;

if (! class_exists ( 'JpagebuilderHelperSite' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
}

/**
 * Plugin class for Page Builder smart search finder plugin
 */
final class Jpagebuilder extends Adapter implements SubscriberInterface {
	use DatabaseAwareTrait;
	
	/**
	 * The plugin context.
	 *
	 * @var string $context The context.
	 * @since 1.0.0
	 */
	protected $context = 'Jpagebuilder';
	
	/**
	 * The extension name.
	 *
	 * @var string $extension The extension name
	 * @since 1.0.0
	 */
	protected $extension = 'com_jpagebuilder';
	
	/**
	 * The sub layout to use when rendering the results.
	 *
	 * @var string $layout The sub layout name.
	 * @since 1.0.0
	 */
	protected $layout = 'page';
	
	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var string $type_title The indexing type
	 * @since 1.0.0
	 */
	protected $type_title = 'Page';
	
	/**
	 * The table name.
	 *
	 * @var string $table The page builder table name.
	 */
	protected $table = '#__jpagebuilder';
	
	/**
	 * The field the published state is stored in.
	 *
	 * @var string $state_field The state field name.
	 * @since 1.0.0
	 */
	protected $state_field = 'published';
	
	/**
	 * Load the language file on instantiation.
	 *
	 * @var bool $autoloadLanguage Auto loading language
	 * @since 1.0.0
	 */
	protected $autoloadLanguage = true;
	
	protected function isJsonEscaped($string) {
		// Check if the string has outer escaped quotes
		$hasOuterQuotes = substr($string, 0, 1) === '"' && substr($string, -1) === '"';
		
		// Check for escape sequences inside
		$escapedPattern = '/\\\\["\\\\\/bfnrt]|\\\\u[0-9a-fA-F]{4}/';
		$hasEscapesInside = preg_match($escapedPattern, $string) === 1;
		
		// Return true if both conditions are met
		return $hasOuterQuotes && $hasEscapesInside;
	}
	
	protected function unescapePossiblyEscapedJson($jsonString) {
		if ($this->isJsonEscaped($jsonString)) {
			// Step 1: Remove the slashes before the inner quotes
			$jsonString = stripslashes(stripslashes($jsonString));
			// Step 2: Remove the outer escaped quotes
			$jsonString = trim($jsonString, '"');
		}
		
		return $jsonString;
	}
	
	/**
	 * Method to index an item.
	 * The item must be a Result object.
	 */
	protected function index(Result $item, $format = 'html') {
		$item->setLanguage ();
		
		// Check if the extension is enabled
		if (ComponentHelper::isEnabled ( $this->extension ) === false) {
			return;
		}
		
		// Set the item context
		$item->context = 'com_jpagebuilder.page';
		
		$menuItem = self::getActiveMenu ( $item->id );
		
		if (empty ( $item->body ) || $item->body === '[]') {
			$item->body = $item->text;
		}
		
		// Set the summary and the body from page builder settings object.
		
		$result = $this->unescapePossiblyEscapedJson($item->body);
		$item->body = $result;
		$item->summary = \JpagebuilderHelperSite::getPrettyText ( $item->body );
		$item->body = \JpagebuilderHelperSite::getPrettyText ( $item->body );
		
		$item->url = $this->getUrl ( $item->id, $this->extension, $this->layout );
		$link = 'index.php?option=com_jpagebuilder&view=page&id=' . $item->id;
		
		if ($item->language && $item->language !== '*' && Multilanguage::isEnabled ()) {
			$link .= '&lang=' . $item->language;
		}
		
		if (isset ( $menuItem->id ) && $menuItem->id) {
			$link .= '&Itemid=' . $menuItem->id;
		}
		
		$item->route = $link;
		$item->path = $item->route;
		
		if (isset ( $menuItem->title ) && $menuItem->title) {
			$item->title = $menuItem->title;
		}
		
		// Handle the page author data.
		$item->addInstruction ( Indexer::META_CONTEXT, 'user' );
		
		// Add the type taxonomy data.
		$item->addTaxonomy ( 'Type', 'Page' );
		
		// Add the language taxonomy data.
		$item->addTaxonomy ( 'Language', $item->language );
		
		// Index the item.
		$this->indexer->index ( $item );
	}
	
	/**
	 * Method to setup the indexer to be run.
	 */
	protected function setup() {
		return true;
	}
	
	/**
	 * Method to get the SQL query used to retrieve the list of page items.
	 */
	protected function getListQuery($query = null) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		
		// Check if we can use the supplied SQL query.
		$query->select('a.id, a.view_id, a.title AS title, a.content AS body, a.text, a.created_on AS start_date')
			  ->select('a.created_by, a.modified, a.modified_by, a.language')
			  ->select('a.access, a.catid, a.extension, a.extension_view, a.published AS state, a.ordering')
			  ->select('u.name')
			  ->from('#__jpagebuilder AS a')
			  ->join('LEFT', '#__users AS u ON u.id = a.created_by')
			  ->where($db->quoteName('a.extension') . ' = '  . $db->quote('com_jpagebuilder'));
		
		return $query;
	}
	
	/**
	 * Method to remove the link information for items that have been deleted.
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  \Exception on database error.
	 */
	public function onFinderAfterDelete(Event $event): void {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$table = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		
		if ($context === 'com_jpagebuilder.page') {
			$id = $table->id;
		} elseif ($context === 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return;
		}
		
		// Remove the items.
		$this->remove ( $id );
	}
	
	/**
	 * Smart Search after save content method.
	 * Reindexes the link information for an article that has been saved.
	 * It also makes adjustments if the access level of an item or the
	 * category to which it belongs has changed.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  \Exception on database error.
	 */
	public function onFinderAfterSave(Event $event): void {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$row = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$isNew = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('isNew');
		
		if ($context === 'com_jpagebuilder.page') {
			if (! $isNew && $this->old_access != $row->access) {
				$this->itemAccessChange ( $row );
			}
			
			$this->reindex ( $row->id );
		}
	}
	
	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This event is fired before the data is actually saved so we are going
	 * to queue the item to be indexed later.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  \Exception on database error.
	 */
	public function onFinderBeforeSave(Event $event): void {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$row = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$isNew = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('isNew');
		
		if ($context === 'com_jpagebuilder.page') {
			if (! $isNew) {
				$this->checkItemAccess ( $row );
			}
		}
	}
	
	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen.
	 * This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 * @throws  \Exception on database error.
	 */
	public function onFinderChangeState(Event $event): void {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$pks = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$value = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('value');
		
		if ($context === 'com_jpagebuilder.page') {
			$this->itemStateChange ( $pks, $value );
		}
		
		if ($context === 'com_plugins.plugin' && $value === 0) {
			$this->pluginDisable ( $pks );
		}
	}
	
	public static function getActiveMenu($pageId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		
		$query->select(array('title, id'));
		$query->from($db->quoteName('#__menu'));
		$query->where($db->quoteName('link') . ' LIKE '. $db->quote('%option=com_jpagebuilder&view=page&id='. $pageId .'%'));
		$query->where($db->quoteName('published') . ' = '. $db->quote('1'));
		$db->setQuery($query);
		$item = $db->loadObject();
		
		return $item;
	}
	
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return array
	 *
	 * @since 5.0.0
	 */
	public static function getSubscribedEvents(): array {
		return [
				'onFinderAfterDelete' => 'onFinderAfterDelete',
				'onFinderAfterSave' => 'onFinderAfterSave',
				'onFinderBeforeSave' => 'onFinderBeforeSave',
				'onFinderChangeState' => 'onFinderChangeState',
				'onBeforeIndex' => 'onBeforeIndex',
				'onBuildIndex' => 'onBuildIndex',
				'onFinderGarbageCollection' => 'onFinderGarbageCollection',
				'onStartIndex' => 'onStartIndex'
		];
	}
}
