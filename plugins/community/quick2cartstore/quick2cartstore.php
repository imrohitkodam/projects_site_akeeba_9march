<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die ('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;

require_once JPATH_ROOT .'/components/com_community/libraries/core.php';
$lang = Factory::getLanguage();
$lang->load('plg_community_quick2cartstore', JPATH_ADMINISTRATOR);

class plgCommunityQuick2cartstore extends CApplications
{
	var $name  = "Quick2cartstore";
	var $_name = 'quick2cartstore';

	function onProfileDisplay()
	{
		$app     = Factory::getApplication();
		$caching = $this->params->get('cache', 1);

		if ($caching)
		{
			$caching = $app->get('caching');
		}

		$cache = Factory::getCache('plgCommunityQuick2cartstore');
		$cache->setCaching($caching);
		$callback = array($this, '_getquick2cartstoreHTML');
		$content  = $cache->get($callback);

		return $content;
	}

	function _getquick2cartstoreHTML()
	{
		if(File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			$lang = Factory::getLanguage();
			$lang->load('com_quick2cart', JPATH_SITE);

			$path = JPATH_SITE.'/components/com_quick2cart/helper.php';

			if(!class_exists('comquick2cartHelper'))
			{
				//require_once $path;
				JLoader::register('comquick2cartHelper', $path );
				JLoader::load('comquick2cartHelper');
			}

			// Load assets
			comquick2cartHelper::loadQuicartAssetFiles();
			$product_path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

			if(!class_exists('productHelper'))
			{
				//require_once $path;
				JLoader::register('productHelper', $product_path );
				JLoader::load('productHelper');
			}

			$storeHelper_path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

			if(!class_exists('StoreHelper'))
			{
			  //require_once $path;
			   JLoader::register('StoreHelper', $storeHelper_path );
			   JLoader::load('StoreHelper');
			}

			$params = $this->params;
			$no_of_stores = $params->get('no_of_stores','2');

			//Get profile id
			$user        = CFactory::getRequestUser();
			$model       = new productHelper();
			$target_data = $model->getUserStores($user->_userid,$no_of_stores);

			if(!empty($target_data))
			{
				$html="
				<div class='techjoomla-bootstrap' >
					<div  class=''>
					<ul class='thumbnails'  >
					";

					foreach($target_data as $data)
					{
						$path = JPATH_SITE . '/components/com_quick2cart/views/vendor/tmpl/thumbnail.php';
						ob_start();
						include($path);
						$html.= ob_get_contents();
						ob_end_clean();
					}
				$html.="
						</ul>
					</div>
				</div>";
				return $html;
			}
		}
	}

}
