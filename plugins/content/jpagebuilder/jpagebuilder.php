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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\Event;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Version;

$jpb_helper_path = JPATH_ADMINISTRATOR . '/components/com_jpagebuilder/helpers/jpagebuilder.php';

if (! file_exists ( $jpb_helper_path )) {
	return;
}

if (! class_exists ( 'JpagebuilderHelper' )) {
	require_once $jpb_helper_path;
}

if (! class_exists ( 'JpagebuilderHelperSite' )) {
	require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
}

// Load language file
$language = Factory::getApplication()->getLanguage ();
$language->load ( 'com_jpagebuilder', JPATH_SITE, 'en-GB', true );
$language->load ( 'com_jpagebuilder', JPATH_SITE, null, true );
class PlgContentJpagebuilder extends CMSPlugin implements SubscriberInterface {
	protected $autoloadLanguage = true;
	protected $jpagebuilder_content = '';
	protected $jpagebuilder_active = 0;
	protected $isJpagebuilderEnabled = 0;
	private static function addFullText($id, $data) {
		$article = new stdClass ();
		$article->id = $id;
		$article->fulltext = JpagebuilderHelperSite::getPrettyText ( $data );
		$db = Factory::getContainer()->get('DatabaseDriver');
		$db->updateObject ( '#__content', $article, 'id' );
	}
	private function isJpagebuilderEnabled() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( 'enabled' )
			  ->from ( $db->quoteName ( '#__extensions' ) )
			  ->where ( $db->quoteName ( 'element' ) . ' = ' . $db->quote ( 'com_jpagebuilder' ) )
			  ->where ( $db->quoteName ( 'type' ) . ' = ' . $db->quote ( 'component' ) );

		$db->setQuery ( $query );

		return ( bool ) $db->loadResult ();
	}
	private function displayJPBEditLink($article, $params) {
		$user = Factory::getApplication()->getIdentity();

		// Ignore if in a popup window.
		if ($params && $params->get ( 'popup' ))
			return;

		// Ignore if the state is negative (trashed).
		if ($article->state < 0)
			return;

		$item = JpagebuilderHelper::getPageContent ( 'com_content', 'article', $article->id );

		if (! $item || ! $item->id)
			return;

		if (property_exists ( $article, 'checked_out' ) && property_exists ( $article, 'checked_out_time' ) && $article->checked_out > 0 && $article->checked_out != $user->get ( 'id' )) {
			return '<a href="#"><span class="fa fa-lock"></span> Checked out</a>';
		}

		$version = new Version ();
		$JoomlaVersion = ( float ) $version->getShortVersion ();

		if ($JoomlaVersion < 4) {
			$app = CMSApplication::getInstance ( 'site' );
			$router = $app->getRouter ();
		} else {
			$router = Factory::getContainer ()->get ( \Joomla\CMS\Router\SiteRouter::class );
		}

		// Get item language code
		$lang_code = (isset ( $item->language ) && $item->language && explode ( '-', $item->language ) [0]) ? explode ( '-', $item->language ) [0] : '';
		// check language filter plugin is enable or not
		$enable_lang_filter = PluginHelper::getPlugin ( 'system', 'languagefilter' );
		// get joomla config
		$conf = Factory::getApplication ()->getConfig ();

		$front_link = 'index.php?option=com_jpagebuilder&view=form&tmpl=component&layout=edit&id=' . $item->id;
		$sefURI = str_replace ( '/administrator', '', $router->build ( $front_link ) );

		if ($lang_code && $lang_code !== '*' && $enable_lang_filter && $conf->get ( 'sef' )) {
			$sefURI = str_replace ( '/index.php/', '/index.php/' . $lang_code . '/', $sefURI );
		} elseif ($lang_code && $lang_code !== '*') {
			$sefURI = $sefURI . '&lang=' . $lang_code;
		}

		return '<a class="btn btn-sm btn-primary" target="_blank" href="' . $sefURI . '"><span class="fas fa-edit" area-hidden="true"></span> Edit with JPageBuilder</a>';
	}
	
	/**
	 * Method to be called everytime an article is saved
	 *
	 * @subparam string $context The context of the content passed to the plugin (added in 1.6)
	 * @subparam object $module_data A Table Content object
	 *
	 * @return boolean true if function not enabled, is in front-end or is new. Else true or false depending on success of save function.
	 */
	public function saveComponentConfig(Event $event) {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$module_data = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
			
		$input = Factory::getApplication ()->getInput();
		$user = Factory::getApplication()->getIdentity();

		$form = $input->post->get ( 'jform', array (), 'Array' );
		$option = $input->get ( 'option', '', 'string' );
		$task = $input->post->get ( 'task' );

		if (empty ( $task ) || $task !== 'module.save2copy') {
			return;
		}

		$jpagebuilder_active = (isset ( $form ['attribs'] ['jpagebuilder_active'] ) && $form ['attribs'] ['jpagebuilder_active']) ? ( int ) $form ['attribs'] ['jpagebuilder_active'] : 0;
		$jpagebuilder_module_id = (isset ( $form ['attribs'] ['jpagebuilder_module_id'] ) && $form ['attribs'] ['jpagebuilder_module_id']) ? $form ['attribs'] ['jpagebuilder_module_id'] : null;
		$jpagebuilder_content = '[]';

		if ($jpagebuilder_module_id) {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->clear ();
			$query->select ( '*' )->from ( $db->quoteName ( '#__jpagebuilder' ) )->where ( $db->quoteName ( 'view_id' ) . '=' . $jpagebuilder_module_id );
			$db->setQuery ( $query );
			$result = $db->loadObject ();
			$jpagebuilder_content = $result->content ?? $result->text ?? '[]';
		}

		if (empty ( $jpagebuilder_content )) {
			return;
		}

		if ($context === 'com_modules.module') {
			$dateTime = Factory::getDate ()->toSql ();

			$values = [ 
					'title' => $module_data->title,
					'text' => '',
					'content' => $jpagebuilder_content,
					'option' => 'mod_jpagebuilder',
					'view' => 'module',
					'id' => $module_data->id,
					'active' => $jpagebuilder_active,
					'published' => 1,
					'catid' => 0,
					'created_on' => $dateTime,
					'created_by' => $user->id,
					'modified' => $dateTime,
					'modified_by' => $user->id,
					'access' => $module_data->access,
					'language' => '*',
					'action' => 'apply',
					'version' => JpagebuilderHelper::getVersion ()
			];

			JpagebuilderHelper::onAfterSavingModule ( $values );
		}
	}
	
	/**
	 * Method to be called everytime an article is saved
	 *
	 * @subparam string $context The context of the content passed to the plugin (added in 1.6)
	 * @subparam object $article A Table Content object
	 * @subparam boolean $isNew If the content is just about to be created
	 *
	 * @return boolean true if function not enabled, is in front-end or is new. Else true or false depending on success of save function.
	 */
	public function afterSaveContent(Event $event) {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$article = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$isNew = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('isNew');
		$arrayPost = isset($arguments[3]) ? $event->getArgument(3) : $event->getArgument('data');
		
		if (! $this->isJpagebuilderEnabled) {
			return;
		}

		$input = Factory::getApplication ()->getInput();
		$option = $input->get ( 'option', '', 'string' );
		$view = 'article';
		$form = $input->post->get ( 'jform', array (), 'Array' );
		$jpagebuilder_active = (isset ( $form ['attribs'] ['jpagebuilder_active'] ) && $form ['attribs'] ['jpagebuilder_active']) ? ( int ) $form ['attribs'] ['jpagebuilder_active'] : 0;
		$jpagebuilder_article_id = (isset ( $form ['attribs'] ['jpagebuilder_article_id'] ) && $form ['attribs'] ['jpagebuilder_article_id']) ? $form ['attribs'] ['jpagebuilder_article_id'] : null;
		$jpagebuilder_content = '[]';

		if ($jpagebuilder_article_id) {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->clear ();
			$query->select ( '*' )->from ( $db->quoteName ( '#__jpagebuilder' ) )->where ( $db->quoteName ( 'view_id' ) . '=' . $jpagebuilder_article_id );
			$db->setQuery ( $query );
			$result = $db->loadObject ();
			$jpagebuilder_content = $result->content ?? $result->text ?? '[]';
		}

		if (! $jpagebuilder_content)
			return;

		if ($context === 'com_content.article') {
			$article_state = $article->state;

			if (! $jpagebuilder_active) {
				$article_state = 0;
			}

			$values = [ 
					'title' => $article->title,
					'text' => '',
					'content' => $jpagebuilder_content,
					'option' => $option,
					'view' => $view,
					'id' => $article->id,
					'active' => $jpagebuilder_active,
					'published' => $article_state,
					'catid' => $article->catid,
					'created_on' => $article->created,
					'created_by' => $article->created_by,
					'modified' => $article->modified,
					'modified_by' => $article->modified_by,
					'access' => $article->access,
					'language' => '*',
					'action' => 'apply',
					'version' => JpagebuilderHelper::getVersion ()
			];

			if ($article->state == 2) {
				$values ['published'] = 1;
			}

			if ($jpagebuilder_active) {
				self::addFullText ( $article->id, $jpagebuilder_content );
			}

			JpagebuilderHelper::onAfterIntegrationSave ( $values );
		}
	}
	
	/**
	 * Prepare page builder content
	 *
	 * @param Event $event
	 * @subparam   string  The context of the content being passed to the plugin.
	 * @subparam   object  The content object.  Note $article->text is also available
	 * @subparam   object  The content params
	 * @subparam   int     The 'page' number
	 * @since 4.0
	 */
	public function preparePagebuilderContent(Event $event) {
		// subparams: $context, &$article, &$params, $page
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$article = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$params = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('params');
		$page = isset($arguments[3]) ? $event->getArgument(3) : $event->getArgument('page');

		$input = Factory::getApplication ()->getInput();
		$option = $input->get ( 'option', '', 'string' );
		$view = $input->get ( 'view', '', 'string' );
		$task = $input->get ( 'task', '', 'string' );

		if (! isset ( $article->id ) || ! ( int ) $article->id) {
			return true;
		}

		if ($this->isJpagebuilderEnabled) {
			if (($option === 'com_content') && ($view === 'article')) {
				$article->text = JpagebuilderHelper::onIntegrationPrepareContent ( $article->text, $option, $view, $article->id );
			}

			if (($option == 'com_j2store') && ($view === 'products') && ($task === 'view') && ($context === 'com_content.article.productlist')) {
				$article->text = JpagebuilderHelper::onIntegrationPrepareContent ( $article->text, 'com_content', 'article', $article->id );
			}
		}
	}
	
	
	/**
	 * Delete content
	 *
	 * @param Event $event
	 * @subparam string $context The context of the content being passed to the plugin.
	 * @subparam object $data The content params
	 * @subparam int The 'page' number
	 * @since 4.0
	 */
	public function deleteContent(Event $event) {
		// subparams: $context, &$article, &$params, $page
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$data = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		
		if ($this->isJpagebuilderEnabled) {
			$input = Factory::getApplication ()->getInput();
			$option = $input->get ( 'option', '', 'string' );
			$task = $input->get ( 'task', '', 'string' );
			if ($option == 'com_content' && $context == 'com_content.article') {
				$values = array (
						'option' => $option,
						'view' => 'article',
						'id' => $data->id,
						'action' => 'delete'
				);
				JpagebuilderHelper::onAfterIntegrationSave ( $values );
			}
		}
	}
	
	/**
	 * Method to be called to add the edit button after an article title
	 *
	 * @subparam string $context The context of the content passed to the plugin (added in 1.6)
	 * @subparam object $article A Table Content object
	 * @subparam object $params
	 * @subparam int $limitstart
	 *
	 * @return boolean true if function not enabled, is in front-end or is new. Else true or false depending on success of save function.
	 */
	public function editButtonAfterTitle(Event $event) {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$article = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$params = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('params');
		$limitstart = isset($arguments[3]) ? $event->getArgument(3) : $event->getArgument('page');
		
		$input = Factory::getApplication ()->getInput();
		$option = $input->get ( 'option', '', 'string' );
		$view = $input->get ( 'view', '', 'string' );
		$task = $input->get ( 'task', '', 'string' );
		
		if (! isset ( $article->id ) || ! ( int ) $article->id) {
			return true;
		}
		
		if ($this->isJpagebuilderEnabled) {
			if ($option == 'com_content' && $view == 'article' && $params->get ( 'access-edit' )) {
				$jpbEditLink = $this->displayJPBEditLink ( $article, $params );
				
				if ($jpbEditLink) {
					// Joomla 5+ native BeforePackageDownloadEvent
					if(method_exists($event, 'addResult')) {
						$event->addResult($jpbEditLink);
					} else {
						$result = isset($arguments['result']) ? $arguments['result'] : [];
						$result[] = $jpbEditLink;
						// Fallback to generic Event up to Joomla 4
						$event->setArgument('result', $result);
					}
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Method to be called to change content state
	 *
	 * @subparam string $context The context of the content passed to the plugin (added in 1.6)
	 * @subparam object $pks A Table Content object
	 * @subparam int $value
	 *
	 * @return boolean true if function not enabled, is in front-end or is new. Else true or false depending on success of save function.
	 */
	public function changeStateOfContent(Event $event) {
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$pks = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		$value = isset($arguments[2]) ? $event->getArgument(2) : $event->getArgument('value');
		
		if ($this->isJpagebuilderEnabled) {
			$input = Factory::getApplication ()->getInput();
			$option = $input->get ( 'option', '', 'string' );
			$view = $input->get ( 'view', '', 'string' );
			$task = $input->get ( 'task', '', 'string' );
			if ($option == 'com_content' && $context == 'com_content.article') {
				$actions = array (
						0,
						1,
						- 2
				);
				if (! in_array ( $value, $actions ))
					return;
				foreach ( $pks as $id ) {
					$values = array (
							'option' => $option,
							'view' => 'article',
							'id' => $id,
							'published' => $value,
							'action' => 'stateChange'
					);
					JpagebuilderHelper::onAfterIntegrationSave ( $values );
				}
			}
		}
	}
	
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   4.0.0
	 */
	public static function getSubscribedEvents(): array {
		return [
				'onExtensionAfterSave' => 'saveComponentConfig',
				'onContentAfterSave' => 'afterSaveContent',
				'onContentPrepare' => 'preparePagebuilderContent',
				'onContentAfterDelete' => 'deleteContent',
				'onContentAfterTitle' => 'editButtonAfterTitle',
				'onContentChangeState' => 'changeStateOfContent'
		];
	}
	
	public function __construct($subject, $config = []) {
		$this->isJpagebuilderEnabled = $this->isJpagebuilderEnabled ();

		parent::__construct ( $subject, $config );
	}
}
