<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonSinglearticle extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$articleId = ! empty ( $settings->article_id ) ? ( int ) $settings->article_id : 0;
		$customItemid = ! empty ( $settings->itemid ) ? ( int ) $settings->itemid : 0;

		if (! $articleId) {
			return '<div style="padding:1rem; margin-bottom:1rem; border:1px solid #f5c2c7; border-radius:0.375rem; background-color:#f8d7da; color:#842029; font-size:1rem; line-height:1.5; position:relative;">' . Text::_('COM_JPAGEBUILDER_ADDON_NOARTICLES_SELECTED') . '</div>';
		}

		$app = Factory::getApplication ();
		$db = Factory::getContainer()->get('DatabaseDriver');
		$input = $app->getInput ();

		$extension = $app->bootComponent('com_content');
		$factory = $extension->getMVCFactory();
		
		$view = $factory->createView('Article', 'Site', 'Html');
		
		$model = $factory->createModel('Article', 'Site');
		$view->setModel($model, true);
		
		$model->getState('filter.language');
		$model->setState('filter.language', false);
		$originalMultilanguageState = Multilanguage::$enabled;
		Multilanguage::$enabled = false;
		$item = $model->getItem($articleId);
		$model->setState('article.id', $articleId);
		
		// 2. Recupera i params originali dal DB, evitando contaminazioni
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select($db->quoteName('attribs'))
			  ->from($db->quoteName('#__content'))
			  ->where($db->quoteName('id') . ' = ' . (int) $articleId);
		
		$db->setQuery($query);
		$rawParams = $db->loadResult();
		
		$params = new Registry($rawParams ?: '{}');
		$params->set('access-view', true);
		
		$overrideParams = [
				'show_title', 'link_titles', 'show_tags', 'show_intro',
				'info_block_position', 'info_block_show_title',
				'show_category', 'link_category', 'show_parent_category', 'link_parent_category',
				'show_associations', 'flags',
				'show_author', 'link_author',
				'show_create_date', 'show_modify_date', 'show_publish_date',
				'show_item_navigation', 'show_hits'
		];
		foreach ($overrideParams as $paramName) {
			if (isset($settings->{$paramName})) {
				$params->set($paramName, $settings->{$paramName});
			}
		}
		
		$jpageBuilderOriginalParams = $model->getState('params');
		$item->params = $params;
		$model->setState('params', $params);
		
		$view->addTemplatePath(JPATH_SITE . '/components/com_content/tmpl/article');
		$view->addTemplatePath(JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_content/article');
		
		$input->set('option', 'com_content');
		$input->set('view', 'article');
		$input->set('layout', 'default');
		$input->set('id', $articleId);
		
		if($customItemid) {
			$input->set('Itemid', $customItemid);
			$menuObject = $app->getMenu('site');
			$menuObject->setActive($customItemid);
		}
		
		Factory::getApplication()->getLanguage()->load('com_content', JPATH_SITE, null, false, true);
		
		// Needed to import system plugins here, otherwise plgsystemcache is not attached because the actionlogs __construct breaks the import loop loading actionlogs plugins
		PluginHelper::importPlugin('content', null, true, $app->getDispatcher());
		$excludedPluginsClassname['joomla\plugin\content\joomla\extension\joomla'] = true;
		
		// Get all Joomla events
		$dispatcherObject = $app->getDispatcher();
		$allSupportedEvents = array(
				'onContentPrepare',
				'onContentAfterTitle',
				'onContentBeforeDisplay',
				'onContentAfterDisplay',
				'onContentBeforeSave',
				'onContentAfterSave',
				'onContentPrepareForm',
				'onContentPrepareData',
				'onContentBeforeDelete',
				'onContentAfterDelete',
				'onContentChangeState',
				'onAfterInitialise',
				'onAfterRoute',
				'onAfterDispatch',
				'onAfterRender',
				'onBeforeRender',
				'onBeforeCompileHead',
				'onBeforeRespond',
				'onAfterRespond',
				'onSearch',
				'onSearchAreas',
				'onGetWebServices'
		);
		foreach ($allSupportedEvents as $eventName) {
			$registeredEventListeners = $dispatcherObject->getListeners($eventName);
			
			foreach ($registeredEventListeners as $registeredEventListener) {
				// We have a legacy plugin with a legacy 'Closure' attached
				if(!is_array($registeredEventListener)) {
					$reflectionFunctionClosure = new ReflectionFunction($registeredEventListener);
					$closureThisPluginClass = $reflectionFunctionClosure->getClosureThis();
					
					$reflectionClassClosure = new ReflectionClass($closureThisPluginClass);
					$closureThisPluginClassName = strtolower($reflectionClassClosure->getName());
					
					if(array_key_exists($closureThisPluginClassName, $excludedPluginsClassname)) {
						$app->getDispatcher()->removeListener($eventName, $registeredEventListener);
					}
				} else {
					// We have a new plugin with a SubscriberInterface
					$subscriberClassInstance = $registeredEventListener[0];
					$subscriberClassName = strtolower(get_class($registeredEventListener[0]));
					if(class_exists($subscriberClassName) && array_key_exists($subscriberClassName, $excludedPluginsClassname)) {
						$app->getDispatcher()->removeSubscriber($subscriberClassInstance);
					}
				}
			}
		}
		
		ob_start();
		
		$doc = Factory::getDocument();
		if(method_exists($view, 'setDocument')) {
			$view->setDocument($doc);
		} else {
			$view->document = $doc;
		}
		
		$view->display();
		$output = ob_get_clean();
		
		// Restore original params
		$model->setState('params', $jpageBuilderOriginalParams);
		Multilanguage::$enabled = $originalMultilanguageState;
		
		return $output;
	}
}