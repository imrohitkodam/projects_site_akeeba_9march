<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2022 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Plugin\CMSPlugin;

// Load language file
$lang = Factory::getLanguage();
$lang->load('plg_jlike_quick2cart', JPATH_ADMINISTRATOR);

/**
 * Jlike plugin for Quick2cart
 *
 * @package     Com_Jlike
 * @subpackage  site
 * @since       2.2
 */
class PlgContentjlike_Quick2cart extends CMSPlugin
{
	/**
	 * Constructor
	 *
	 * @param   array  &$subject  config
	 * @param   array  $config    config
	 *
	 * @since   2.2
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$jlikeEnabled = ComponentHelper::isEnabled('com_jlike');

		if ($jlikeEnabled)
		{
			// Import library dependencies
			JLoader::import('helper', JPATH_SITE . '/components/com_jlike');
		}

		$this->jlikeParams = ComponentHelper::getParams('com_jlike');
	}

	/**
	 * QtcShowReviewAndRating
	 *
	 * @param   array  $data  Data required for plugin. context and itemdetails
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function onAfterQ2cProductDisplay($data)
	{
		$app = Factory::getApplication();

		if ($app->getName() != 'site')
		{
			return;
		}

		$isCompInstalled = $this->isComponentEnabled("quick2cart");

		if ($app->scope != 'com_quick2cart' || empty($isCompInstalled))
		{
			return;
		}

		if (!empty($data['context']) && !empty($data['itemDetail']->item_id) && $data['context'] == "com_quick2cart.productpage")
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

			if (!class_exists('comquick2cartHelper'))
			{
				JLoader::register('comquick2cartHelper', $path);
				JLoader::load('comquick2cartHelper');
			}

			$comquick2cartHelper = new comquick2cartHelper;
			$product_link        = $comquick2cartHelper->getProductLink($data['itemDetail']->item_id, 'detailsLink');

			$jlikeparams               = array();
			$jlikeparams['url']        = $product_link;
			$jlikeparams['product_id'] = $data['itemDetail']->item_id;
			$jlikeparams['title']      = $data['itemDetail']->name;

			// Call to internal function
			return $this->qtcShowReviewAndRating($data['context'], $jlikeparams);
		}
	}

	/**
	 * QtcShowReviewAndRating
	 *
	 * @param   integer  $context  contenxt of calling. eg com_quick2cart.productpage
	 * @param   array    $data     Like Link link and config param
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function qtcShowReviewAndRating($context, $data)
	{
		$compRating     = $this->jlikeParams->get("jlike_enable_rating", 1);
		$compCommenting = $this->jlikeParams->get("allow_comments", 1);

		$operationMode      = $this->params->get('jlike_qtcMode', "jqtcReviewsAnddRating");
		$jlike_allow_rating = $this->params->get('jlike_allow_rating', "enrolledUser");
		$show_like_buttons  = 0;
		$show_reviews       = 0;
		$show_comments      = -1;
		$this->isUserAllowToRate = 0;

		// Show review and rating. (Mean don't show comments)
		if ($compRating == 1 && $operationMode == "jqtcReviewsAnddRating")
		{
			$show_reviews            = 1;
			$show_comments           = -1;
			$this->isUserAllowToRate = 0;

			// If rating is allowed forbought users
			if ($jlike_allow_rating == "boughtUser"  && !empty($data['product_id']))
			{
				$this->isUserAllowToRate = $this->isUserAllowToRate($data['product_id']);
			}
			else
			{
				$this->isUserAllowToRate = 1;
			}
		}
		elseif ($compCommenting == 1 && $operationMode == "jqtcCommenting")
		{
			// Hv to show commenting only.
			$show_reviews            = 0;
			$show_comments           = 1;
			$this->isUserAllowToRate = 0;
		}
		else
		{
			// If both are off
			return;
		}

		$jlikeData = array(
			'cont_id' => $data['product_id'],
			'element' => $context,
			'title' => $data['title'],
			'url' => $data['url'],
			'plg_name' => 'jlike_quick2cart',
			'plg_type' => 'content',
			'show_comments' => $show_comments,
			'show_reviews' => $show_reviews,
			'show_like_buttons' => $show_like_buttons,
			'jlike_allow_rating' => $this->isUserAllowToRate
		);

		$app                 = Factory::getApplication();
		$app->input->set('data', json_encode($jlikeData));

		$jlikehelperObj = new comjlikeHelper;
		$html           = $jlikehelperObj->showlike();

		return $html;
	}

	/**
	 * Get store Owner detail
	 *
	 * @param   integer  $item_id  Q2cart's unique product id i.e item_id.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function getjlike_quick2cartOwnerDetails($item_id)
	{
		$db = Factory::getDBO();
		$query = "SELECT s.owner
				FROM #__kart_items AS i
				LEFT JOIN #__kart_store AS s ON i.store_id = s.id
				WHERE i.item_id =" . $item_id;

		$db->setQuery($query);

		return $created_by = $db->loadResult();
	}

	/**
	 * Method to get allow rating to bought the product user
	 *
	 * @param   integer  $item_id  Q2cart's unique product id i.e item_id.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function isUserAllowToRate($item_id)
	{
		if (Factory::getUser()->id)
		{
			try
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("ko.id")->from('#__kart_order_item as koi')
				->join('LEFT', '#__kart_orders as ko ON ko.id = koi.order_id')
				->join('LEFT', '#__users as u ON u.id = ko.user_info_id')
				->where("koi.item_id=" . $item_id)
				->where("u.id=" . Factory::getUser()->id)
				->where("ko.status='C'");
				$db->setQuery($query);

				return count($db->loadObjectList());
			}
			catch (Exception $e)
			{
				$this->setError($e->getMessage());

				return 0;
			}
		}
	}

	/**
	 * Method to get allow rating to bought the product user
	 *
	 * @param   string  $option  component name. eg quick2cart for component com_quick2cart etc.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	private function isComponentEnabled($option)
	{
		$status = 0;

		if (File::exists(JPATH_ROOT . '/components/com_' . $option . '/' . $option . '.php'))
		{
			if (ComponentHelper::isEnabled('com_' . $option, true))
			{
				$status = 1;
			}
		}

		return $status;
	}

	/**
	 * QtcShowReviewAndRating
	 *
	 * @param   array  $data  Data required for plugin. context and itemdetails
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function onQ2cProductAvgRating($data)
	{
		$app           = Factory::getApplication();
		$compRating    = $this->jlikeParams->get("jlike_enable_rating", 1);
		$jlike_reviews = $this->params->get('jlike_qtcMode');

		if ($app->getName() != 'site')
		{
			return;
		}

		// Show review. -1 for : Not to show anything related to commenting
		$show_reviews = ($jlike_reviews == "jqtcReviewsAnddRating"  && $compRating == 1) ? 1 : 0;

		if (empty($show_reviews))
		{
			return;
		}

		$html = '';
		$isCompInstalled = $this->isComponentEnabled("quick2cart");

		if ($app->scope != 'com_quick2cart' && empty($isCompInstalled))
		{
			return;
		}

		if (!empty($data['context']) && !empty($data['itemDetail']->item_id) && $data['context'] == "com_quick2cart.productpage")
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

			if (!class_exists('comquick2cartHelper'))
			{
				JLoader::register('comquick2cartHelper', $path);
				JLoader::load('comquick2cartHelper');
			}

			$comquick2cartHelper = new comquick2cartHelper;
			$product_link = $comquick2cartHelper->getProductLink($data['itemDetail']->item_id, 'detailsLink');
			$html = '';

			$jlike_allow_rating = $this->params->get('jlike_allow_rating');

			$app->input->set('data', json_encode(
				array(
					'cont_id' => $data['itemDetail']->item_id,
					'element' => $data['context'],
					'title' => $data['itemDetail']->name,
					'url' => $product_link,
					'plg_name' => 'jlike_quick2cart',
					'plg_type' => 'content',
					'show_comments' => 0,
					'show_reviews' => 0,
					'show_like_buttons' => 0,
					'jlike_allow_rating' => $jlike_allow_rating
				)
			));

			$jlikehelperObj = new comjlikeHelper;
			$html           = $jlikehelperObj->getAvarageRating();

			return $html;
		}
	}

	/**
	 * QtcShowReviewAndRating
	 *
	 * @param   array  $data  Data required for plugin. context and itemdetails
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function onQ2cAddLikeButtons($data)
	{
		$app = Factory::getApplication();

		if ($app->getName() != 'site' || $app->scope != 'com_quick2cart')
		{
			return;
		}

		$html                 = '';
		$isCompInstalled      = $this->isComponentEnabled("quick2cart");
		$jlQtcShowLikeButtons = $this->params->get('jlQtcShowLikeButtons', 1);

		if ($jlQtcShowLikeButtons == 0 || empty($isCompInstalled))
		{
			return;
		}

		if (!empty($data['context']) && !empty($data['itemDetail']->item_id) && $data['context'] == "com_quick2cart.productpage")
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

			if (!class_exists('comquick2cartHelper'))
			{
				JLoader::register('comquick2cartHelper', $path);
				JLoader::load('comquick2cartHelper');
			}

			$comquick2cartHelper = new comquick2cartHelper;
			$product_link = $comquick2cartHelper->getProductLink($data['itemDetail']->item_id, 'detailsLink', 1);
			$html = '';

			$jlikeData = array(
				'cont_id' => $data['itemDetail']->item_id,
				'element' => $data['context'],
				'title' => $data['itemDetail']->name,
				'url' => $product_link,
				'plg_name' => 'jlike_quick2cart',
				'plg_type' => 'content',
				'show_comments' => -1,
				'show_reviews' => 0,
				'show_like_buttons' => 1,
				'jlike_allow_rating' => 0
			);

			$app->input->set('data', json_encode($jlikeData));

			$jlikehelperObj = new comjlikeHelper;
			$html           = $jlikehelperObj->showlike();

			return $html;
		}
	}
}
