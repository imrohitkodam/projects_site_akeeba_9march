<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Event\Event;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Version;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Filesystem\Folder;
use Joomla\String\StringHelper;

require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/autoload.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/integrations.php';
JpagebuilderAutoload::loadClasses ();
JpagebuilderAutoload::loadHelperClasses ();

class plgSystemJpagebuilder extends CMSPlugin implements SubscriberInterface {
	private static function getPageContent($extension = 'com_content', $extension_view = 'article', $view_id = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id',
				'text',
				'content',
				'active',
				'language',
				'version'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $extension ) );
		$query->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $extension_view ) );
		$query->where ( $db->quoteName ( 'view_id' ) . ' = ' . $view_id );
		$db->setQuery ( $query );
		$result = $db->loadObject ();

		if ($result) {
			return $result;
		}

		return false;
	}
	private static function getIntegration() {
		$app = Factory::getApplication ();
		$option = $app->getInput()->get ( 'option', '', 'string' );
		$group = str_replace ( 'com_', '', $option );
		$integrations = BuilderIntegrations::getIntegrations ();
		$cParams = ComponentHelper::getParams('com_jpagebuilder');
		
		if (! isset ( $integrations [$group] )) {
			return false;
		}

		$integration = $integrations [$group];
		$name = $integration ['name'];
		$enabled = PluginHelper::isEnabled ( $group, $name );

		if ($enabled && $cParams->get('integrationarticle', 0)) {
			return $integration;
		}

		return false;
	}
	private static function getTemplate() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'template'
		) ) );
		$query->from ( $db->quoteName ( '#__template_styles' ) );
		$query->where ( $db->quoteName ( 'client_id' ) . ' = ' . $db->quote ( 0 ) );
		$query->where ( $db->quoteName ( 'home' ) . ' = ' . $db->quote ( 1 ) );
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	
	/** Loading assets before render
	 *
	 * @param Event $event
	 * @access public
	 * @return void
	 */
	public function beforeRender(Event $event) {
		$app = Factory::getApplication ();

		if ($app->isClient ( 'administrator' )) {
			$integration = self::getIntegration ();

			if (! $integration) {
				return;
			}

			$input = $app->getInput();
			$option = $input->get ( 'option', '', 'string' );
			$view = $input->get ( 'view', '', 'string' );
			$id = $input->get ( $integration ['id_alias'], 0, 'int' );
			$layout = $input->get ( 'layout', '', 'string' );

			if (! ($option == 'com_' . $integration ['group'] && $view == $integration ['view'])) {
				return;
			}

			$doc = Factory::getApplication ()->getDocument ();
			$wa = $doc->getWebAssetManager();
			$wa->useScript('jquery');
			$wa->registerAndUseScript('jpagebuilder.init', 'plugins/system/jpagebuilder/assets/js/init.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], [], ['jquery']);
		
			$wa->registerAndUseStyle('jpagebuilder.faw5', 'components/com_jpagebuilder/assets/css/font-awesome-5.min.css');
			$wa->registerAndUseStyle('jpagebuilder.faw4shim', 'components/com_jpagebuilder/assets/css/font-awesome-v4-shims.css');
			$wa->registerAndUseStyle('jpagebuilder.pagebuilder', 'administrator/components/com_jpagebuilder/assets/css/jpagebuilder.css');
			
			$pagebuilder_enabled = 0;

			if ($page_content = self::getPageContent ( $option, $view, $id )) {
				$page_content = JpagebuilderApplicationHelper::preparePageData ( $page_content );
				$pagebuilder_enabled = ( int ) $page_content->active;
			}

			$integration_element = '.adminform';

			if ($option == 'com_content') {
				$integration_element = '.adminform';
			}

			$wa = $doc->getWebAssetManager();
			$wa->addInlineScript ( 'var jPagebuilderEditorIntegrationElement="' . $integration_element . '";' );
			$wa->addInlineScript ( 'var jPagebuilderEnabled=' . $pagebuilder_enabled . ';' );
		} else {
			$input = $app->getInput();
			$option = $input->get ( 'option', '', 'string' );
			$view = $input->get ( 'view', '', 'string' );
			$task = $input->get ( 'task', '', 'string' );
			$id = $input->get ( 'id', 0, 'int' );
			$pageName = '';

			if (($option == 'com_content' && $view == 'article') || ($option == 'com_j2store' && $view == 'products' && $task == 'view')) {
				$pageName = "article-{$id}.css";
			} elseif ($option == 'com_jpagebuilder' && $view == 'page') {
				$pageName = "page-{$id}.css";
			}

			$file_path = JPATH_ROOT . '/media/com_jpagebuilder/css/' . $pageName;
			$file_url = 'media/com_jpagebuilder/css/' . $pageName;
			$pageName = StringHelper::str_ireplace('.', '-', pathinfo($pageName, PATHINFO_FILENAME));

			if ($pageName && file_exists ( $file_path )) {
				$doc = Factory::getApplication ()->getDocument ();
				$wa = $doc->getWebAssetManager();
				$wa->registerAndUseStyle('jpagebuilder.' . $pageName, $file_url);
			}
		}
	}
	
	/**
	 * Manage links and integration with Joomla contents
	 *
	 * @param Event $event
	 * @access public
	 */
	public function afterRenderApp(Event $event) {
		$app = Factory::getApplication ();

		if ($app->isClient ( 'administrator' )) {
			$integration = self::getIntegration ();

			if (! $integration) {
				return;
			}

			$input = $app->getInput();
			$option = $input->get ( 'option', '', 'string' );
			$view = $input->get ( 'view', '', 'string' );
			$layout = $input->get ( 'layout', '', 'string' );
			$id = $input->get ( $integration ['id_alias'], 0, 'int' );

			if (! ($option === 'com_' . $integration ['group'] && $view === $integration ['view'])) {
				return;
			}

			if (isset ( $integration ['frontend_only'] ) && $integration ['frontend_only']) {
				return;
			}

			// Page Builder state
			$pagebuilder_enabled = 0;
			$viewId = 0;
			$language = "*";

			if ($page_content = self::getPageContent ( $option, $view, $id )) {
				$page_content = JpagebuilderApplicationHelper::preparePageData ( $page_content );
				$viewId = $page_content->id;
				$pagebuilder_enabled = $page_content->active;
				$language = $page_content->language;
			}

			// Add script
			$body = $app->getBody ();

			$frontendEditorLink = 'index.php?option=com_jpagebuilder&view=form&tmpl=component&layout=edit&extension=com_content&extension_view=article&id=' . $viewId;
			$backendEditorLink = 'index.php?option=com_jpagebuilder&view=editor&extension=com_content&extension_view=article&article_id=' . $id;

			if ($language && $language !== '*' && Multilanguage::isEnabled ()) {

				$frontendEditorLink .= '&lang=' . $language;
				$backendEditorLink .= '&lang=' . $language;
			}

			$backendEditorLink .= '&tmpl=component#/editor/' . $viewId;

			$frontendEditorLink = str_replace ( '/administrator', '', JpagebuilderHelperRoute::buildRoute ( $frontendEditorLink ) );

			if (! $viewId || ! $pagebuilder_enabled) {
				$dashboardHTML = '<div class="jpagebuilder-builder-loading"><svg version="1.1" id="L7" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve"><path fill="#444" d="M31.6,3.5C5.9,13.6-6.6,42.7,3.5,68.4c10.1,25.7,39.2,38.3,64.9,28.1l-3.1-7.9c-21.3,8.4-45.4-2-53.8-23.3 c-8.4-21.3,2-45.4,23.3-53.8L31.6,3.5z"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path><path fill="#444" d="M42.3,39.6c5.7-4.3,13.9-3.1,18.1,2.7c4.3,5.7,3.1,13.9-2.7,18.1l4.1,5.5c8.8-6.5,10.6-19,4.1-27.7 c-6.5-8.8-19-10.6-27.7-4.1L42.3,39.6z"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="-360 50 50" repeatCount="indefinite"></animateTransform></path><path fill="#444" d="M82,35.7C74.1,18,53.4,10.1,35.7,18S10.1,46.6,18,64.3l7.6-3.4c-6-13.5,0-29.3,13.5-35.3s29.3,0,35.3,13.5 L82,35.7z"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path></svg></div>';
			} else {
				$dashboardHTML = '<a href="' . $backendEditorLink . '" class="jpagebuilder-button">Open Backend Editor</a><a target="_blank" href="' . $frontendEditorLink . '" class="jpagebuilder-button">Open Frontend Editor</a>';
			}

			$body = str_replace ( '<fieldset class="adminform">', '<div class="jpagebuilder-integrations"><div class="jpagebuilder-integration-toggler"><span class="jpagebuilder-integration-button jpagebuilder-integration-button-joomla" action-switch-builder data-action="editor" role="button">Joomla Editor</span><span class="jpagebuilder-integration-button jpagebuilder-integration-button-editor" action-switch-builder data-action="jpagebuilder" role="button"><span class="builder-svg-icon"></span>JPageBuilder Editor</span></div></div><div class="jpagebuilder-integration-component pagebuilder-' . str_replace ( '_', '-', $option ) . '" style="display: none;">' . $dashboardHTML . '</div><fieldset class="adminform">', $body );

			// Page Builder fields
			$body = str_replace ( '</form>', '<input type="hidden" id="jform_attribs_jpagebuilder_content" name="jform[attribs][jpagebuilder_content]"></form>' . "\n", $body );
			$body = str_replace ( '</form>', '<input type="hidden" id="jform_attribs_jpagebuilder_article_id" name="jform[attribs][jpagebuilder_article_id]" value="' . $id . '"></form>' . "\n", $body );
			$body = str_replace ( '</form>', '<input type="hidden" id="jform_attribs_jpagebuilder_active" name="jform[attribs][jpagebuilder_active]" value="' . $pagebuilder_enabled . '"></form>' . "\n", $body );

			$app->setBody ( $body );
		}
	}

	/**
	 * Remove the Joomla! default template styles for the editor view.
	 *
	 * @param Event $event
	 * @return void
	 */
	public function beforeHeadCompilation(Event $event) {
		$app = Factory::getApplication ();
		$input = $app->getInput();
		$option = $input->get ( 'option' );
		$view = $input->get ( 'view', 'editor' );

		if ($app->isClient ( 'administrator' ) && $option === 'com_jpagebuilder' && $view === 'editor') {
			$wa = Factory::getApplication ()->getDocument ()->getWebAssetManager ();
			$wa->disablePreset ( 'template.atum.ltr' );
			$wa->disablePreset ( 'template.atum.rtl' );
			$wa->disableStyle ( 'template.atum.ltr' );
			$wa->disableStyle ( 'template.atum.rtl' );
			$wa->disableStyle ( 'template.active.language' );
			$wa->disableStyle ( 'template.user' );
		}
	}

	/**
	 * onAfterDispatch handler
	 * Enforce the application to use tmpl=component if there is not.
	 *
	 * @access	public
	 * @param Event $event
	 * @return null
	 */
	public function afterDispatch(Event $event) {
		$app = Factory::getApplication ();
		$input = $app->getInput();

		$option = $input->get ( 'option' );
		$view = $input->get ( 'view', 'editor' );
		$tmpl = $input->get ( 'tmpl' );

		if ($app->isClient ( 'administrator' ) && $option === 'com_jpagebuilder' && $view === 'editor') {
			if ($tmpl !== 'component') {
				$input->set ( 'tmpl', 'component' );
			}
		}
	}
	
	/**
	 * Preserve component settings
	 *
	 * @access	public
	 * @param Event $event
	 * @return null
	 */
	public function beforeSaveExtension(Event $event) {
		// subparams: $request
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$data = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		
		if (($context === 'com_config.component') && ($data->element === 'com_jpagebuilder')) {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( $db->quoteName ( array (
					'params'
			) ) );
			$query->from ( $db->quoteName ( '#__extensions' ) );
			$query->where ( $db->quoteName ( 'element' ) . ' = ' . $db->quote ( 'com_jpagebuilder' ) );
			$db->setQuery ( $query );
			$params = $db->loadResult ();
			
			$data->params = $params;
		}
	}
	
	/**
	 * Delete the cache on extension config save
	 *
	 * @access	public
	 * @param Event $event
	 * @return null
	 */
	public function afterSaveExtension(Event $event) {
		// subparams: $request
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$data = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		
		if (($context === 'com_config.component') && ($data->element === 'com_jpagebuilder')) {
			$admin_cache = JPATH_ROOT . '/administrator/cache/jpagebuilder';

			if (\file_exists ( $admin_cache )) {
				Folder::delete ( $admin_cache );
			}

			$site_cache = JPATH_ROOT . '/cache/jpagebuilder';

			if (\file_exists ( $site_cache )) {
				Folder::delete ( $site_cache );
			}
		}
	}
	
	/**
	 * Event to manipulate the menu item dashboard in backend
	 *
	 * @param Event $event
	 * @subparam   array  &$policy  The privacy policy status data, passed by reference, with keys "published" and "editLink"
	 *
	 * @return  void
	 */
	public function processMenuItemsDashboard(Event $event) {
		// subparams: $context, $items
		$arguments = $event->getArguments();
		$context = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('context');
		$items = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('subject');
		
		if(!empty($items) && $context == 'administrator.module.mod_submenu') {
			foreach ($items as $item) {
				if($item->element == 'com_jpagebuilder') {
					$item->img = Uri::base() . 'components/com_jpagebuilder/assets/images/jpagebuilder-16x16.png';
					$item->title = 'COM_JPAGEBUILDER_DASHBOARD_TITLE';
				}
			}
		}
		
		// Kill com_joomlaupdate informations about extensions missing updater info, leave only main one
		$appInstance = Factory::getApplication();
		$document = $appInstance->getDocument();
		if(!$appInstance->get('jextstore_joomlaupdate_script') && $appInstance->getInput()->get('option') == 'com_joomlaupdate' && !$appInstance->getInput()->get('view') && !$appInstance->getInput()->get('task')) {
			$document->getWebAssetManager()->addInlineScript ("
				window.addEventListener('DOMContentLoaded', function(e) {
					if(document.querySelector('#preupdatecheck')) {
						var jextensionsIntervalCount = 0;
						var jextensionsIntervalTimer = setInterval(function() {
						    [].slice.call(document.querySelectorAll('#compatibilityTable1 tbody tr th.exname')).forEach(function(th) {
						        let txt = th.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|gptranslate|jpagebuilder|responsivizer|jchatsocial|jcomment|jshortcodes|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
						            th.parentElement.style.display = 'none';
						            th.parentElement.classList.remove('error');
									th.parentElement.classList.add('jextcompatible');
						        }
						    });
							[].slice.call(document.querySelectorAll('#compatibilityTable2 tbody tr th.exname')).forEach(function(th) {
						        let txt = th.innerText;
						        if (txt && txt.toLowerCase().match(/jsitemap|gdpr|gptranslate|jpagebuilder|responsivizer|jchatsocial|jcomment|jshortcodes|jrealtime|jspeed|jredirects|vsutility|visualstyles|visual\sstyles|instant\sfacebook\slogin|instantpaypal|screen\sreader|jspeed|jamp/i)) {
									th.parentElement.classList.remove('error');
									th.parentElement.classList.add('jextcompatible');
						            let smallDiv = th.querySelector(':scope div.small');
									if(smallDiv) {
										smallDiv.style.display = 'none';
									}
						        }
						    });
							if (document.querySelectorAll('#compatibilityTable0 tbody tr').length == 0 &&
								document.querySelectorAll('#compatibilityTable1 tbody tr:not(.jextcompatible)').length == 0 &&
								document.querySelectorAll('#compatibilityTable2 tbody tr:not(.jextcompatible)').length == 0) {
						        [].slice.call(document.querySelectorAll('#preupdatecheckbox, #preupdateCheckCompleteProblems')).forEach(function(element) {
						            element.style.display = 'none';
						        });
								if(document.querySelector('#noncoreplugins')) {
									document.querySelector('#noncoreplugins').checked = true;
								}
								if(document.querySelector('button.submitupdate')) {
							        document.querySelector('button.submitupdate').disabled = false;
							        document.querySelector('button.submitupdate').classList.remove('disabled');
								}
								if(document.querySelector('#joomlaupdate-precheck-extensions-tab span.fa')) {
									let tabIcon = document.querySelector('#joomlaupdate-precheck-extensions-tab span.fa');
									tabIcon.classList.remove('fa-times');
									tabIcon.classList.remove('text-danger');
									tabIcon.classList.remove('fa-exclamation-triangle');
									tabIcon.classList.remove('text-warning');
									tabIcon.classList.add('fa-check');
									tabIcon.classList.add('text-success');
								}
						    };
					
							if (document.querySelectorAll('#compatibilityTable0 tbody tr').length == 0) {
								if(document.querySelectorAll('#compatibilityTable1 tbody tr:not(.jextcompatible)').length == 0) {
									let compatibilityTable1 = document.querySelector('#compatibilityTable1');
									if(compatibilityTable1) {
										compatibilityTable1.style.display = 'none';
									}
								}
								clearInterval(jextensionsIntervalTimer);
							}
					
						    jextensionsIntervalCount++;
						}, 1000);
					};
				});");
			$appInstance->set('jextstore_joomlaupdate_script', true);
		}
	}
	
	/** Manage the Joomla updater based on the user license
	 *
	 * @access public
	 * @return void
	 */
	public function jpagebuilderUpdateInstall(Event $event) {
		// subparams: &$url, &$headers
		$arguments = $event->getArguments();
		$url = isset($arguments[0]) ? $event->getArgument(0) : $event->getArgument('url');
		$headers = isset($arguments[1]) ? $event->getArgument(1) : $event->getArgument('headers');
		
		$uri 	= Uri::getInstance($url);
		$parts 	= explode('/', $uri->getPath());
		$app = Factory::getApplication();
		if ($uri->getHost() == 'storejextensions.org' && in_array('com_jpagebuilder.zip', $parts)) {
			// Init as false unless the license is valid
			$validUpdate = false;
			
			// Manage partial language translations
			$jLang = $app->getLanguage();
			$jLang->load ( 'com_jpagebuilder', JPATH_ADMINISTRATOR );
			
			// Email license validation API call and &$url building construction override
			$cParams = ComponentHelper::getParams('com_jpagebuilder');
			$registrationEmail = $cParams->get('registrationemail', null);
			
			// License
			if($registrationEmail) {
				$prodCode = 'jpagebuilder';
				$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
				
				// Retrieve license informations from the remote REST API
				$apiResponse = null;
				$apiEndpoint = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet') . "/option,com_easycommerce/action,licenseCode/email,$registrationEmail/productcode,$prodCode";
				if (function_exists('curl_init')){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$apiResponse = curl_exec($ch);
				}
				$objectApiResponse = json_decode($apiResponse);
				
				if(!is_object($objectApiResponse)) {
					// Message user about error retrieving license informations
					$app->enqueueMessage(Text::_('COM_JPAGEBUILDER_ERROR_RETRIEVING_LICENSE_INFO'));
				} else {
					if(!$objectApiResponse->success) {
						switch ($objectApiResponse->reason) {
							// Message user about the reason the license is not valid
							case 'nomatchingcode':
								$app->enqueueMessage(Text::_('COM_JPAGEBUILDER_LICENSE_NOMATCHING'));
								break;
								
							case 'expired':
								// Message user about license expired on $objectApiResponse->expireon
								$app->enqueueMessage(Text::sprintf('COM_JPAGEBUILDER_LICENSE_EXPIRED', $objectApiResponse->expireon));
								break;
						}
						
					}
					
					// Valid license found, builds the URL update link and message user about the license expiration validity
					if($objectApiResponse->success) {
						$url = $cdFuncUsed('uggc' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/WCNTROHVYQRE1406WFqCvtmu9943568423p8nqsvq24td1pcbu568bf2.ugzy');
						// Joomla 5+ native BeforePackageDownloadEvent
						if(method_exists($event, 'updateUrl')) {
							$event->updateUrl($url);
						} else {
							// Fallback to generic Event up to Joomla 4
							$event->setArgument(0, $url);
						}
						
						$validUpdate = true;
						$app->enqueueMessage(Text::sprintf('COM_JPAGEBUILDER_EXTENSION_UPDATED_SUCCESS', $objectApiResponse->expireon));
					}
				}
			} else {
				// Message user about missing email license code
				$app->enqueueMessage(Text::sprintf('COM_JPAGEBUILDER_MISSING_REGISTRATION_EMAIL_ADDRESS', OutputFilter::ampReplace('index.php?option=com_jpagebuilder&view=editor#/settings')));
			}
			
			if(!$validUpdate) {
				$app->enqueueMessage(Text::_('COM_JPAGEBUILDER_UPDATER_STANDARD_ADVISE'), 'notice');
			}
		}
	}
	
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since 4.0.0
	 */
	public static function getSubscribedEvents(): array {
		return [
				'onBeforeRender' => 'beforeRender',
				'onAfterRender' => 'afterRenderApp',
				'onBeforeCompileHead' => 'beforeHeadCompilation',
				'onAfterDispatch' => 'afterDispatch',
				'onExtensionBeforeSave' => 'beforeSaveExtension',
				'onExtensionAfterSave' => 'afterSaveExtension',
				'onPreprocessMenuItems' => 'processMenuItemsDashboard',
				'onInstallerBeforePackageDownload' => 'jpagebuilderUpdateInstall'
		];
	}
	
	/**
	 * Class constructor, manage params from component
	 *
	 * @access private
	 * @return boolean
	 */
	public function __construct($subject, $config = []) {
		parent::__construct ( $subject, $config );
		
		// Init application
		$appInstance = Factory::getApplication();
		
		// Fix for Joomla 5.1+ later SEF plugin router forces. Always treat as no sef suffix and no enforce sef all SEF routed sitemap links
		if(version_compare(JVERSION, '5.1', '>=') && $appInstance->isClient ('site') && isset($_SERVER['REQUEST_URI'])) {
			if($appInstance->getInput()->get('option') == 'com_jpagebuilder') {
				$server = $appInstance->getInput()->server;
				$server->set('REQUEST_METHOD', 'POST');
			}
		}
	}
}
