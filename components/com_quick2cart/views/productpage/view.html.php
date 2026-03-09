<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * View class for Product detail page.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewProductpage extends HtmlView
{
	protected $videoGallery;
	protected $videoSupportedExtension;
	protected $allowAutoplayVideo;
	protected $enableAudioOnVideoAutoplay;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws  Exception if there is an error in the form event.
	 */
	public function display($tpl = null)
	{
		$app                        = Factory::getApplication();
		$this->comquick2cartHelper  = new comquick2cartHelper;
		$input                      = $app->input;
		$layout                     = $input->get('layout', 'default', 'STRING');
		$option                     = $input->get('option', '', 'STRING');
		$this->applicablePromotions = array();

		$this->params            = ComponentHelper::getParams('com_quick2cart');
		$this->socialintegration = $this->params->get('integrate_with', 'none');
		$this->who_bought        = $this->params->get('who_bought', 0);
		$this->who_bought_limit  = $this->params->get('who_bought_limit', 2);

		$this->videoGallery               = $this->params->get('video_gallery');
		$this->videoSupportedExtension    = explode(",", $this->params->get('videoExtensions'));
		$this->allowAutoplayVideo         = $this->params->get('allowAutoplayVideo', '1', 'String');
		$this->enableAudioOnVideoAutoplay = $this->params->get('enableAudioOnVideoAutoplay', '0', 'String');
		$this->enable_tags                = $this->params->get('show_tags', '0', 'INT');
		$this->redirect_tags_to_products  = $this->params->get('redirect_tags_to_products', '0', 'INT');
		$this->currencies_sym             = $this->params->get('addcurrency_sym');

		// Load helper file
		$product_path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('productHelper'))
		{
			JLoader::register('productHelper', $product_path);
			JLoader::load('productHelper');
		}

		$productHelper = new productHelper;
		$this->item_id = $item_id = $input->get('item_id', 0, 'INTEGER');

		JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
		$model = new Quick2cartModelcart;

		// Getting stock min max,cat,store_id
		$this->itemdetail = $model->getItemRec($item_id);

		if ($layout == 'default')
		{
			// DECLARATION SECTION
			$this->client  = $client = "com_quick2cart";
			$this->pid     = 0;

			if (empty($this->itemdetail->item_id))
			{
				throw new Exception(Text::_('COM_QUICK2CART_ERROR_PAGE_NOT_FOUND'), 404);
			}

			// Retrun store_id,role etc with order by role,store_id
			$this->store_role_list = $this->comquick2cartHelper->getStoreIds();

			// GETTING AUTHORIZED STORE ID
			$storeHelper      = new storeHelper;
			$this->store_list = $storeHelper->getuserStoreList();

			// GETTING PRICE @TODO : DONT SHOW THE PRODUCT WHEN DOESN'T FOUND PRICE FOR CURRENT CURRENCY
			$this->price = $price = $model->getPrice($item_id, 1);

			// Getting Extra Fields Data
			$this->extraData = $this->get('DataExtra');

			// Getting stock min max,cat,store_id
			$this->itemdetail = $model->getItemRec($item_id);

			JLoader::import('promotion', JPATH_SITE . '/components/com_quick2cart/helpers');
			$promotionHelper = new PromotionHelper;

			// Get applicable promotions
			if (!empty($this->itemdetail->item_id) && !empty($this->itemdetail->category) && !empty($this->itemdetail->store_id))
			{
				$this->applicablePromotionsList = $promotionHelper->getApplicablePromotionsForProduct(
				$this->itemdetail->item_id, $this->itemdetail->category, $this->itemdetail->store_id
				);
				foreach ($this->applicablePromotionsList as $promotion)
				{
					if($promotion->coupon_required == '1' && $promotion->catlog_promotion == '1')
					{
						$this->applicablePromotions[] = $promotion;
					}
					elseif($promotion->coupon_required == '0')
					{
						$this->applicablePromotions[] = $promotion;
					}
				}
			}

			JLoader::import('product', JPATH_SITE . '/components/com_quick2cart/models');
			$Quick2cartModelProduct = new Quick2cartModelProduct;

			$this->form_extra = $Quick2cartModelProduct->getFormExtra(
				array(
					"clientComponent" => 'com_quick2cart',
					"client"          => 'com_quick2cart.product',
					"view"            => 'product',
					"layout"          => 'default',
					"content_id"      => $this->item_id,
					"category"        => $this->itemdetail->category)
				);

			if (!empty($this->form_extra))
			{
				$xmlFileName = $this->itemdetail->category . "productform_extra" . ".xml";
				$this->formXml = simplexml_load_file(JPATH_SITE . "/components/com_quick2cart/models/forms/" . $xmlFileName);
			}

			if (!empty($this->itemdetail))
			{
				// Get attributes
				$this->attributes = $productHelper->getItemCompleteAttrDetail($item_id);

				if (!empty($this->attributes))
				{
					$this->itemdetail->itemAttributes = $this->attributes;
				}

				$this->showBuyNowBtn = $productHelper->isInStockProduct($this->itemdetail);

				// Get free products media file
				$this->mediaFiles = $productHelper->getProdmediaFiles($item_id);

				$this->prodFromCat       = $productHelper->getSimilarProdFromCat($this->itemdetail->category, $this->item_id, "com_quick2cart");
				$this->peopleAlsoBought  = $productHelper->peopleAlsoBought($this->item_id);
				$this->peopleWhoBought   = $productHelper->peopleWhoBought($this->item_id, 0);

				// Trigger data
				$triggerData               = array();
				$triggerData['context']    = "com_quick2cart.productpage";
				$triggerData['itemDetail'] = $this->itemdetail;

				PluginHelper::importPlugin('content');
				$this->afterProductDisplay = $app->triggerEvent('onAfterQ2cProductDisplay', array($triggerData));

				if (!empty($this->afterProductDisplay))
				{
					$this->afterProductDisplay = trim(implode("\n", $this->afterProductDisplay));
				}

				// Get avg rating html
				PluginHelper::importPlugin('content');
				$this->productRating = $app->triggerEvent('onQ2cProductAvgRating', array($triggerData));

				if (!empty($this->productRating))
				{
					$this->productRating = trim(implode("\n", $this->productRating));
				}

				// Trigger for like dislike buttons
				PluginHelper::importPlugin('content');
				$this->addLikeButtons = $app->triggerEvent('onQ2cAddLikeButtons', array($triggerData));

				// Trigger for pincode check

				/*			$dispatcher = JDispatcher::getInstance();
				PluginHelper::importPlugin('tjshipping');
				$this->getPincodeCheckAvailability = $app->triggerEvent('onGetPincodeCheckAvailability');
				*/

				if (!empty($this->addLikeButtons))
				{
					$this->addLikeButtons = trim(implode("\n", $this->addLikeButtons));
				}
			}
		}
		elseif ($layout == 'popupslide')
		{
			$this->item_id = $item_id = $input->get('qtc_prod_id', '');
			JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
			$model = new Quick2cartModelcart;

			if (empty($item_id))
			{
				return false;
			}

			$this->itemdetail = $model->getItemRec($item_id);
			$this->item       = $this->itemdetail;
		}
		elseif ($layout == 'users_bs5' || $layout == 'users_bs3')
		{
			$this->item_id = $item_id = $input->get('itemid', '');
			$this->peopleWhoBought = $productHelper->peopleWhoBought($this->item_id, 0);
		}

		$this->item = $this->itemdetail;

		if (property_exists($this->item, 'tags'))
		{
			$this->item->tags = new TagsHelper;
			$this->item->tags->getItemTags('com_quick2cart.product', $this->item->item_id);
		}

		$moduleSearch = $input->get('module_search', 0, 'INTEGER');

		if ($moduleSearch)
		{
			$ssession = Factory::getSession();
			$moduleSearch = ($this->itemdetail && $this->itemdetail->name) ? $this->itemdetail->name : '';
			$ssession->set('module_search', $moduleSearch);
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		/* Because the application sets a default page title,
		 we need to get it from the menu item itself
		 @TODO Need to uncomment this when a menu for single product item can be created.
		 */

		/*
		$menu = $menus->getActive();

		if($menu)
		{
		$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
		$this->params->def('page_heading', Text::_('QTC_PRODUCTPAGE_PAGE'));
		}

		$title = $this->params->get('page_title', '');
		*/

		// @TODO Need to comment this if when a menu for single product item can be created.
		if (empty($title))
		{
			$title = $this->itemdetail->name . ' - ' . Text::_('QTC_PRODUCTPAGE_PAGE');
		}

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
