<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\CMSPlugin;

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

if (! class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

/**
 * Quick2cart element for Flexi content
 *
 * @version  Release: <1.0>
 * @since    1.0
 */
class PlgFlexicontent_FieldsQuick2cart extends CMSPlugin
{
	public static $field_types = array('quick2cart');
	/**
	 * [plgFlexicontent_fieldsQuick2cart description]
	 *
	 * @param   [type]  &$subject  [description]
	 * @param   [type]  $params    [description]
	 *
	 * @return  [type]             [description]
	 */
	public function plgFlexicontent_fieldsQuick2cart (&$subject, $params)
	{
		parent::__construct($subject, $params);
		CMSPlugin::loadLanguage('plg_flexicontent_fields_quick2cart', JPATH_ADMINISTRATOR);
	}

	/**
	 * [Method to create field's HTML display for item form. DISPLAY methods, item form & frontend views]
	 *
	 * @param   [type]  &$field  [description]
	 * @param   [type]  &$item   [description]
	 *
	 * @return  [type]           [description]
	 */
	public function onDisplayField (&$field, &$item)
	{
		// Execute the code only if the field type match the plugin type
		if (! in_array($field->field_type, self::$field_types))
		{
			return;
		}

		$field->label = Text::_($field->label);

		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}
		// For copying the the item title to q2c Item Name
		Factory::getApplication()->input->set("qtc_article_name", $item->title);

		HTMLHelper::_('bootstrap.renderModal', 'a.modal');
		$html = '';
		$client = "com_flexicontent";
		$input = Factory::getApplication()->input;
		$pid = $item->id;

		$comquick2cartHelper = new comquick2cartHelper;
		//$fieldName           = $this->fieldname;
		$html                = '';
		$client              = "com_content";
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$isAdmin             = $app->isClient('administrator');
		$pid                 = $jinput->get('id');

		if ($pid)
		{
			/* If someone has already created the article and admin is adding product details the the product
			should be owned by the content creator */
			JLoader::import('components.com_content.models.article', JPATH_ADMINISTRATOR);
			$contentModel  = BaseDatabaseModel::getInstance('Article', 'ContentModel');
			$contentDetail = $contentModel->getItem($pid);
			$owner         = $contentDetail->created_by;
		}

		// For admin, no need of bs-3 layout. Check override in admin template if not present then take from site->com_quick2cart->layout
		if ($isAdmin)
		{
			if (JVERSION < '4.0.0')
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs2', 'JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');
			}
			else
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs5', 'JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');
			}
		}
		else
		{
			if (JVERSION < '4.0.0')
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs3', 'SITE', 'SITE');
			}
			else
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs5', 'SITE', 'SITE');
			}
		}

		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/models');
		$Quick2cartModelWeights = BaseDatabaseModel::getInstance('Weights', 'Quick2cartModel');
		$Quick2cartModelLengths = BaseDatabaseModel::getInstance('Lengths', 'Quick2cartModel');

		$this->weightClasses = $Quick2cartModelWeights->getItems();
		$this->lengthClasses = $Quick2cartModelLengths->getItems();

		ob_start();
		include $path;
		$field->html = ob_get_contents();
		ob_end_clean();
	}

	/**
	 * [Method to create field's HTML display for frontend views]
	 *
	 * @param   [type]  &$field  [description]
	 * @param   [type]  $item    [description]
	 * @param   [type]  $values  [description]
	 * @param   string  $prop    [description]
	 *
	 * @return  [type]           [description]
	 */
	public function onDisplayFieldValue (&$field, $item, $values = null, $prop = 'display')
	{
		// Execute the code only if the field type match the plugin type
		if (! in_array($field->field_type, self::$field_types))
		{
			return;
		}

		if (File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			$app = Factory::getApplication();
			$lang = Factory::getLanguage();
			$lang->load('com_quick2cart');
			$comquick2cartHelper = new comquick2cartHelper;
			$output = $comquick2cartHelper->getBuynow($item->id, "com_flexicontent");
		}

		$field->{$prop} = $output;
	}

	/**
	 * [METHODS HANDLING before & after saving / deleting field events. Method to handle field's values after they are saved into the DB]
	 *
	 * @param   [type]  &$field  [description]
	 * @param   [type]  &$post   [description]
	 * @param   [type]  &$file   [description]
	 * @param   [type]  &$item   [description]
	 *
	 * @return  [type]           [description]
	 */
	public function onAfterSaveField (&$field, &$post, &$file, &$item)
	{
		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if (class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$input = Factory::getApplication()->input;
		$post_data = $input->post;

		$item_name = $post_data->get('item_name', '', 'STRING');
		$sku = $post_data->get('sku', '', 'RAW');
		$stock = $post_data->get('stock', '');
		$min_qty = $post_data->get('min_item');
		$max_qty = $post_data->get('max_item');

		// Getting store id
		$store_id = $input->get('store_id', '0');
		$pid = $item->id;

		if (!$pid || empty($store_id))
		{
			return;
		}

		$state = $input->get('state', 0, "INTEGER");
		$post_data->set('state', $state);
		$comquick2cartHelper = new comquick2cartHelper;
		$client = $post_data->set('client', 'com_flexicontent');
		$pid = $post_data->set('pid', $pid);

		$comquick2cartHelper = $comquick2cartHelper->saveProduct($post_data);
	}

	/**
	 * [METHODS HANDLING before & after saving / deleting field events]
	 *
	 * @param   [type]  &$field  [description]
	 * @param   [type]  &$post   [description]
	 * @param   [type]  &$file   [description]
	 * @param   [type]  &$item   [description]
	 *
	 * @return  [type]           [description]
	 */
	public function onBeforeSaveField (&$field, &$post, &$file, &$item)
	{
		$jinput = Factory::getApplication()->input;
		$postdata   = $jinput->post;
		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$postdata = Factory::getApplication()->input->get('post');

		// If first time save //@TODO change into
		if (! $postdata['jform']['id'])
		{
			return;
		}

		$input = Factory::getApplication()->input;
		$post_data = $input->post;

		$item_name = $post_data->get('item_name', '', 'STRING');
		$sku = $post_data->get('sku', '', 'RAW');
		$stock = $post_data->get('itemstock', '');
		$min_qty = $post_data->get('min_item');
		$max_qty = $post_data->get('max_item');
		$state = $input->get('state', 0, "INTEGER");
		$post_data->set('state', $state);

		// Getting store id
		$store_id = $input->get('store_id', '0');
		$comquick2cartHelper = new comquick2cartHelper;
	}

	/**
	 * [Method called just before the item is deleted to remove custom item data related to the field]
	 *
	 * @param   [type]  &$field  [description]
	 * @param   [type]  &$item   [description]
	 *
	 * @return  [type]           [description]
	 */
	public function onBeforeDeleteField (&$field, &$item)
	{
		$articleId = isset($item->id) ? $item->id : 0;

		if ($articleId)
		{
			$db = Factory::getDbo();
			$db->setQuery("DELETE FROM #__kart_items WHERE product_id = $articleId AND  parent = 'com_flexicontent'");

			try
			{
				$db->execute();
			}
			catch(\RuntimeException $e)
			{

				$this->setError(Text::_('QTC_PARAMS_DEL_FAIL'));

				return false;
			}
		}
	}

	/**
	 * [VARIOUS HELPER METHODS]
	 *
	 * @param   array|string  $url  [description]
	 *
	 * @return  [type]        [description]
	 */
	public function cleanurl ($url)
	{
		$prefix = array(
				"http://",
				"https://",
				"ftp://"
		);
		$cleanurl = str_replace($prefix, "", $url);

		return $cleanurl;
	}
}
