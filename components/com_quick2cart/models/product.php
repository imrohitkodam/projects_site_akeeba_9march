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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Uri\Uri;

require_once JPATH_SITE . "/components/com_tjfields/filterFields.php";
/**
 * Product model class.
 *
 * @package  Quick2cart
 * @since    1.0
 */
class Quick2cartModelProduct extends FormModel
{
	use TjfieldsFilterField;

	/**
	 * Method to get the profile form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since  1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.product', 'product', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * This function store attributes
	 *
	 * @param   integer  $item_id    item_id
	 * @param   array    $allAttrib  All attribute details
	 * @param   string   $sku        Sku
	 * @param   string   $client     client
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function StoreAllAttribute($item_id, $allAttrib, $sku, $client)
	{
		// Get  attributeid list FROM POST
		$attIdList = array();

		foreach ($allAttrib as $attributes)
		{
			if (!empty($attributes['attri_id']))
			{
				$attIdList[] = $attributes['attri_id'];
			}
		}

		// DEL EXTRA ATTRIBUTES
		if (!class_exists('productHelper'))
		{
			// Require while called from backend
			JLoader::register('productHelper', JPATH_SITE . '/components/com_quick2cart/helpers/product.php');
			JLoader::load('productHelper');
		}

		// THIS  DELETE db attributes which is not present now or removed
		$productHelper = new productHelper;
		$productHelper->deleteExtaAttribute($item_id, $attIdList);

		if (!class_exists('quick2cartModelAttributes'))
		{
			// Require while called from backend
			JLoader::register('quick2cartModelAttributes', JPATH_SITE . '/components/com_quick2cart/models/attributes.php');
			JLoader::load('quick2cartModelAttributes');
		}

		$quick2cartModelAttributes = new quick2cartModelAttributes;

		foreach ($allAttrib as $key => $attr)
		{
			$attr['sku']     = $sku;
			$attr['client']  = $client;
			$attr['item_id'] = $item_id;

			// Dont consider empty attributes
			if (!empty($attr['attri_name']))
			{
				$quick2cartModelAttributes->store($attr);
			}
		}
	}

	/**
	 * This function store attributes
	 *
	 * @param   string  $sku  sku
	 *
	 * @since    1.0
	 * @return   number
	 */
	public function getItemidFromSku($sku)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT `item_id` from `#__kart_items` where `sku`="' . $sku . '"';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This model function manage items published or unpublished state
	 *
	 * @param   array   $items  items
	 * @param   integr  $state  state
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function setItemState($items, $state)
	{
		$db = Factory::getDBO();

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$db    = Factory::getDBO();
				$query = "UPDATE #__kart_items SET state=" . $state . " WHERE item_id=" . $id;
				$db->setQuery($query);

				try
				{
					$db->execute();
				}
				catch(\RuntimeException $e)
				{
					$this->setError($e->getMessage());
					return false;
				}
			}
		}
	}

	/**
	 * Send mail to owner
	 *
	 * @param   Object  $values  values
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function SendMailToOwner($values)
	{
		$app                 = Factory::getApplication();
		$loguser             = Factory::getUser();
		$comquick2cartHelper = new comquick2cartHelper;

		$mailfrom            = $app->get('mailfrom');
		$fromname            = $app->get('fromname');
		$sitename            = $app->get('sitename');
		$sendto              = $loguser->email;
		$subject             = Text::_('COM_Q2C_PRODUCT_AAPROVAL_OWNER_SUBJECT');
		$subject             = str_replace('{sellername}', $loguser->name, $subject);
		$itemid              = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=category&layout=default');
		$body                = Text::_('COM_Q2C_PRODUCT_AAPROVAL_OWNER_BODY');
		$body                = str_replace('{sellername}', $loguser->name, $body);
		$body                = str_replace('{title}', $values->get('item_name', '', 'RAW'), $body);
		$url                 = Uri::base() . 'index.php?option=com_quick2cart&view=category&layout=default&Itemid=' . $itemid;
		$body                = str_replace('{link}', $url, $body);
		$body                = str_replace('{sitename}', $sitename, $body);
		$res                 = $comquick2cartHelper->sendmail($mailfrom, $subject, $body, $sendto);
	}

	/**
	 * This function return product images according to integration
	 * TODO: for now it only work for zoo & native, so the changes will be needed for other integration
	 *
	 * @param   integer  $item_id  item_id
	 *
	 * @since   1.0
	 * @return   Countable|array
	 */
	public function getProdutImages($item_id)
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		// Get the product id ( this is needed integration) & client(parent)
		$query->select($db->quoteName(array('parent', 'product_id')));
		$query->from($db->quoteName('#__kart_items'));
		$query->where($db->quoteName('item_id') . ' = ' . $item_id);
		$db->setQuery($query);
		$results = $db->loadObject();

		switch ($results->parent)
		{
			// Get Zoo item image
			case 'com_zoo':

				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('i.elements', 'i.application_id', 'i.type', 'app.application_group')));
				$query->from($db->quoteName('#__zoo_item', 'i'));
				$query->join('LEFT', $db->quoteName('#__zoo_application', 'app') .
				' ON (' . $db->quoteName('app.id') . ' = ' . $db->quoteName('i.application_id') . ')');
				$query->where($db->quoteName('i.id') . ' = ' . $results->product_id);
				$db->setQuery($query);
				$zoo_item = $db->loadObject();
				$image_path[0] = $this->getItemFieldData($zoo_item->application_group, $zoo_item->type, $zoo_item->elements);

				return $image_path;

				break;

			default:
				if (!empty($item_id))
				{
					$query = $db->getQuery(true);
					$query = "SELECT `images` FROM `#__kart_items` WHERE `item_id` = " . $item_id;
					$db->setQuery($query);
					$image_path = $db->loadResult();

					if (!empty($image_path))
					{
						return json_decode($image_path, false);
					}
				}
		}
	}

	/**
	 * Get field detail
	 *
	 * @param   string    $application_group  Zoo Item Application group
	 * @param   integer   $type               Zoo Item type
	 * @param   integer   $elements           Zoo element info
	 *
	 * @since   1.0
	 * @return   null
	 */
	public static function getItemFieldData($application_group, $type, $elements)
	{
		$elements = json_decode($elements, true);

		$app               = App::getInstance('zoo');
		$db                = Factory::getDBO();
		$application_group = strtolower($application_group);
		$item_type         = strtolower($type);
		$zoo_config_file   = array();
		$fielContent       = file_get_contents(JPATH_SITE . '/media/zoo/applications/' . $application_group . '/types/' . $item_type . '.config');
		$zoo_config_file   = json_decode($fielContent, true);

		// Get the image key
		$image_flag = 0;

		// Check is the image available
		foreach ($zoo_config_file['elements'] as $image_key => $arr_row)
		{
			if ($arr_row['type'] == "image" AND $arr_row['name'] != "Teaser Image")
			{
				$image_flag = 1;
				break;
			}
			elseif ($arr_row['type'] == "image" AND $arr_row['name'] == "Teaser Image")
			{
				$image_flag = 1;
				break;
			}
		}

		// Get the image path from $element array
		$image = ($image_flag == 1) ? $elements[$image_key]['file'] : '';

		return $image;
	}

	/**
	 * This function sends mail to admin after editing product
	 *
	 * @param   object    $prod_values  product values
	 * @param   integer  $item_id      item id to remember or not
	 * @param   integer  $newProduct   newProduct URL
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function SendMailToAdminApproval($prod_values, $item_id, $newProduct = 1)
	{
		$loguser             = Factory::getUser();
		$comquick2cartHelper = new comquick2cartHelper;
		$quick2cartModelProduct = new quick2cartModelProduct;
		$app                 = Factory::getApplication();
		$params              = ComponentHelper::getParams('com_quick2cart');

		$mailfrom            = $app->get('mailfrom');
		$fromname            = $app->get('fromname');
		$sitename            = $app->get('sitename');
		$sendto              = $params->get('sale_mail');
		$currency            = $comquick2cartHelper->getCurrencySession();

		$multiple_img           = array();
		$count                  = 0;
		$prod_imgs              = $prod_values->get('qtc_prodImg', array(), "ARRAY");		
		$multiple_img           = $quick2cartModelProduct->getProdutImages($item_id);
		$body                   = '';

		// Edit product
		if ($newProduct == 0)
		{
			$subject   = Text::_('COM_Q2C_EDIT_PRODUCT_SUBJECT');
			$subject   = str_replace('{sellername}', $loguser->name, $subject);
			$body      = Text::_('COM_Q2C_EDIT_PRODUCT_BODY');
			$body      = str_replace('{productname}', $prod_values->get('item_name', '', 'RAW'), $body);
			$pod_price = $prod_values->get('multi_cur', array(), "ARRAY");
			$body      = str_replace('{price}', $pod_price[$currency], $body);
			$body      = str_replace('{sellername}', $loguser->name, $body);
			$body      = str_replace('{sku}', $prod_values->get('sku', '', 'RAW'), $body);

			if (!empty($multiple_img))
			{
				$multiple_img = (array) $multiple_img;

				foreach ($multiple_img as $img)
				{
					$body .= '<br><img src="' . Uri::root() . 'images/quick2cart/' . $img . '" alt="No image" ><br>';
				}
			}
		}

		// New product
		else
		{
			$subject = Text::_('COM_Q2C_PRODUCT_AAPROVAL_SUBJECT');
			$body    = Text::_('COM_Q2C_PRODUCT_AAPROVAL_BODY');
			$body    = str_replace('{title}', $prod_values->get('item_name', '', 'RAW'), $body);
			$body    = str_replace('{sellername}', $loguser->name, $body);
			$desc    = $prod_values->get('description', '', 'ARRAY');
			$data        = ($desc && is_array($desc)) ? $desc['data'] : '';
			$desc    = strip_tags(trim($data));
			$body    = str_replace('{des}', $desc, $body);
			$body    = str_replace('{link}', Uri::base() . 'administrator/index.php?option=com_quick2cart&view=products&filter_published=0', $body);


			if ($multiple_img && !empty($multiple_img))
			{
				$multiple_img = (array) $multiple_img;
				for ($i = 0; $i < count($multiple_img); $i++)
				{
					$body .= '<br><img src="' . Uri::ROOT() . 'images/quick2cart/' . $multiple_img[$i] . '" alt="No image" ><br>';
				}
			}
		}

		$res = $comquick2cartHelper->sendmail($mailfrom, $subject, $body, $sendto);
	}
}
