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
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;

require_once JPATH_SITE . '/plugins/tjshipping/qtc_default_zoneshipping/qtc_default_zoneshipping/qtczoneShipHelper.php';

$lang = Factory::getLanguage();
$lang->load('plg_tjshipping_qtc_default_zoneshipping', JPATH_ADMINISTRATOR);

/**
 * PlgTjshippingQtc_Default_Zoneshipping
 *
 * @package     Com_Quick2cart
 * @subpackage  site
 * @since       1.0
 */
class PlgTjshippingQtc_Default_Zoneshipping extends CMSPlugin
{
	/**
	 * @var $element  string  Should always correspond with the plugin's filename,
	 *  forcing it to be unique
	 */
	protected $element   = 'qtc_default_zoneshipping';

	/**
	 * Construtor
	 *
	 * @param   STRING  &$subject  subject
	 * @param   STRING  $config    config
	 *
	 * @since   1.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		// Set the language in the class
		$config = Factory::getConfig();
	}

	/**
	 * Checks to make sure that this plugin is the one being triggered by the extension
	 *
	 * @param   object  $row  row
	 *
	 * @access public
	 *
	 * @return $row object row from extension.
	 *
	 * @since 2.5
	 */
	public function on_shipGetInfo($row)
	{
		$element = $this->element;

		if (is_object($row) && !empty($row->element) && $row->element == $element )
		{
			$row->plugConfigLink = "index.php?option=com_plugins&task=plugin.edit&extension_id={$row->extension_id}";
			$row->plugDescription = Text::_("PLG_TJSHIPPING_QTC_DEFAULT_ZONESHIPPING_XML_DESCRIPTION");

			return $row;
		}

		return;
	}

	/**
	 * Method used to display HTML.
	 *
	 * @param   object  $jinput  Joomla's jinput Object.
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function on_getShipMethodForm($jinput)
	{
		return $html = $this->_shipBuildLayout($jinput, $layout = 'default');
	}

	/**
	 * This method handles all ajax related things;
	 *
	 * @param   object  $jinput  Joomla's jinput Object.
	 *
	 * @since   1.0
	 * @return   Json format result.
	 */
	public function onTjShip_AjaxCallHandler($jinput)
	{
		$post              = $jinput->post;
		$qtcshiphelper     = new qtcshiphelper;
		$qtczoneShipHelper = new qtczoneShipHelper;
		$ajaxTask          = $jinput->get('plugtask');

		if (empty($ajaxTask))
		{
			$ajaxTask = $post->get('plugtask');
		}

		switch ($ajaxTask)
		{
			case "addShipMethRate" : $result = $qtcshiphelper->addShipMethRate($jinput);
			break;

			case "qtcDelshipMethRate" : $result = $qtcshiphelper->qtcDelshipMethRate($jinput);
			break;

			case "updateShipMethRate" : $result = $qtcshiphelper->qtcUpdateShipMethRate($jinput);
			break;

			case "getFieldHtmlForShippingType" :
				$fieldData = $jinput->get('fieldData', array(), "ARRAY");
				$result    = $qtczoneShipHelper->getFieldHtmlForShippingType($fieldData);
			break;
		}

		// Return json formatted result
		return $result;
	}

	/**
	 * Shipping form related action will be handled by this function.
	 *
	 * @param   object  $jinput  Joomla's jinput Object.
	 *
	 * @since   1.0
	 * @return   URL param that have to add by component
	 */
	public function onTjShip_plugActionkHandler($jinput)
	{
		$post = $jinput->post;
		$plugview = $jinput->get('plugview');
		$qtczoneShipHelper = new qtczoneShipHelper;

		// Plugin view is not found in URL then check in post array.
		if (empty($plugview))
		{
			$plugview = $post->get('plugview');
		}

		$actionDetail = array();

		// Add plugin related params Eg nextview=edit&task=display
		$actionDetail['urlPramStr'] = '';

		// Action status msg
		$actionDetail['statusMsg'] = '';

		if (!empty($plugview))
		{
			// Handle view related action ( save or etc).
			$actionDetail = $qtczoneShipHelper->_viewHandler($jinput, $plugview);
		}

		return $actionDetail;
	}

	/**
	 * Builds the layout to be shown, along with hidden fields.
	 *
	 * @param   object  $jinput  input object
	 *
	 * @since    1.0
	 * @return   html
	 */
	public function onTjShip_shipBuildLayout($jinput)
	{
		$qtczoneShipHelper = new qtczoneShipHelper;

		// Get plugview url param value
		$plugLayout = $jinput->get('plugview');

		// Plugin view is not found in URL then check in post array.
		if (empty($plugLayout))
		{
			$plugLayout = !empty($jinput->post->get('plugview')) ? $jinput->post->get('plugview') : 'default';
		}

		// Load view
		$shipFormData = $qtczoneShipHelper->_qtcLoadViewData($plugLayout, $jinput);

		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($plugLayout, $jinput);

		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * This method return array available shipping plugin methods
	 *
	 * @param   INT  $store_id  store id
	 *
	 * @since   1.0
	 * @return   Array of available shipping methods.
	 */
	public function onTjShip_getAvailableShipMethods($store_id)
	{
		$qtczoneShipHelper = new qtczoneShipHelper;

		return $qtczoneShipHelper->getAvailableShipMethods($store_id);
	}

	/**
	 * This method return shipping methods detail.
	 *
	 * @param   int  $shipMethId  shiping method id
	 *
	 * @since   1.0
	 *
	 * @return   Array of shipping method detail.
	 */
	public function onTjShip_getShipMethodDetail($shipMethId)
	{
		$qtczoneShipHelper = new qtczoneShipHelper;

		return $qtczoneShipHelper->getShipMethodDetail($shipMethId);
	}

	/**
	 * Method to build layout path
	 *
	 * @param   string  $layout  layout
	 * @param   object  $jinput  input data
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function buildLayoutPath($layout = 'default', $jinput = '')
	{
		$app       = Factory::getApplication();
		$core_file = dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';
		$params    = ComponentHelper::getParams('com_quick2cart');
		$bsVersion = $params->get('bootstrap_version', '', 'STRING');

		// Check for override layout ( Is present )?
		$override = JPATH_BASE . '/' . 'templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if (File::exists($override))
		{
			$layoutPath = $override;
		}
		else
		{
			$isSite    = $app->isClient('site');

			if ($isSite)
			{
				if ($bsVersion == 'bs5')
				{
					$layoutPath = dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '_bs5.php';
				}
				elseif ($bsVersion == 'bs3')
				{
					$layoutPath = dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '_bs3.php';
				}
			}
			else
			{
				if (JVERSION < '4.0.0')
				{
					$layoutPath = dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '_bs2.php';
				}
				else
				{
					$layoutPath = dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '_bs5.php';
				}
			}
		}

		return  $layoutPath;
	}

	/**
	 * Method provides load helper file needed
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function _TjloadTaxHelperFiles()
	{
		// LOAD product HELPER
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/taxHelper.php';

		if (!class_exists('productHelper'))
		{
			// Require_once $path;
			JLoader::register('taxHelper', $path);
			JLoader::load('taxHelper');
		}
	}

	/**
	 * This method provide aplicable shipping charge detail using for provided shipping method.
	 *
	 * @param   object  $vars  gives billing, shipping, item_id, methodId(unique plug shipping method id) etc.
	 *
	 * @since   1.0
	 * @return  Shipping method charges detail.
	 */
	public function onTjShip_getShipMethodChargeDetail($vars)
	{
		$qtczoneShipHelper = new qtczoneShipHelper;

		return $qtczoneShipHelper->getShipMethodChargeDetail($vars);
	}

	/**
	 * While placing the order, this method validates shipping charges so
	 * that if any one changes the shipping charges from Hidden fields will not affect.
	 *
	 * @param   object  $vars  gives shipping method id, unqiue plugin shecific -rate id (which point to price related table row), shipping cost, etc.
	 *
	 * @since   1.0
	 * @return  Shipping method charges.
	 */
	public function TjShip_validateShipCharges($vars)
	{
		$qtczoneShipHelper = new qtczoneShipHelper;

		return $qtczoneShipHelper->qtcValidateShipCharges($vars);
	}
}
