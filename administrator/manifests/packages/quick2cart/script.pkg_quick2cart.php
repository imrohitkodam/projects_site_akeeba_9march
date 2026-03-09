<?php
/**
 * @package    AdminTools
 * @copyright  Copyright (c)2012-2013 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

$tjInstallerPath = JPATH_ROOT . '/administrator/manifests/packages/quick2cart/tjinstaller.php';

if (File::exists(__DIR__ . '/tjinstaller.php'))
{
	include_once __DIR__ . '/tjinstaller.php';
}
elseif (File::exists($tjInstallerPath))
{
	include_once $tjInstallerPath;
}

/**
 * Quick2Cart package installer class
 *
 * @since  1.0
 */
class Pkg_Quick2cartInstallerScript extends TJInstaller
{
	protected $extensionName = 'Quick2Cart';

	/** @var array The list of extra modules and plugins to install */
	private $oldversion = "";

	protected $installationQueue = array(
		'postflight' => array(
			'easysocialApps' => array (
				'user' => array (
					'q2c_boughtproducts' => 0,
					'q2cMyProducts' => 0
				)
			),

			'plugins' => array(
				'system' => array(
					'tjassetsloader' => 1,
					'tjupdates' => 1
				),
				'payment' => array(
					'2checkout' => 0,
					'alphauserpoints' => 0,
					'authorizenet' => 0,
					'bycheck' => 1,
					'byorder' => 1,
					'ccavenue' => 0,
					'easysocialpoints' => 0,
					'jomsocialpoints' => 0,
					'linkpoint' => 0,
					'paypal' => 0,
					'paypal_adaptive_payment' => 0,
					'paypalpro' => 0,
					'payu' => 0,
					'razorpay' => 0
				),
				'tjsms' => array(
					'smshorizon' => 0,
					'clickatell' => 0,
					'twilio'     => 0,
					'mvaayoo'    => 0,
				),
				'emailalerts' => array(
					'jma_q2c_latestproducts' => 0,
				),
			),

			'files' => array(
				'tj_strapper' => 1
			),

			'libraries' => array(
				'techjoomla' => 1,
				'lib_tjfpdi' => 1,
				'tj_db_sync' => 1
			)
		)
	);

	/** @var  array  The list of extra modules and plugins to uninstall */
	protected $uninstallQueue = array (
		/*plugins => { (folder) => { (element) => (published) }}*/
		'plugins' => array ()
	);

	/** @var array The list of obsolete extra modules and plugins to uninstall when upgrading the component */
	protected $obsoleteExtensionsUninstallationQueue = array (
		// @modules => { (folder) => { (module) }* }*
		'modules' => array (
			'admin' => array (
			),
			'site' => array (
			)
		),
		// @plugins => { (folder) => { (element) }* }*
		'plugins' => array (
			'system' => array (
			)
		)
	);

	/**
	 * A list of extensions (modules, plugins) to enable after installation. Each item has four values, in this order:
	 * type (plugin, module, ...), name (of the extension), status (0 - unpublish, 1 - publish),
	 * client (0=site, 1=admin), group (for plugins), position (for modules).
	 *
	 * @var array
	 */
	protected $extensionsToEnable = array (
		// Quick2Cart modules
		array ('module', 'mod_q2ccart', 1, 0, '', ''),
		array ('module', 'mod_q2c_search', 1, 0, '', ''),
		array ('module', 'mod_qtc_categorylist', 1, 0, '', ''),
		array ('module', 'mod_qtc_categorypin', 1, 0, '', ''),
		array ('module', 'mod_quick2cart', 1, 0, '', ''),

		// Quick2Cart plugins
		array ('plugin', 'quick2cart', 1, 1, 'actionlog'),
		array ('plugin', 'quick2cart', 1, 1, 'privacy'),
		array ('plugin', 'qtc_shipping_default', 1, 1, 'qtcshipping'),
		array ('plugin', 'qtc_tax_default', 1, 1, 'qtctax'),
		array ('plugin', 'qtc_sys', 1, 1, 'system'),
		array ('plugin', 'qtc_sample_development_custom', 1, 1, 'system'),
		array ('plugin', 'qtc_default_zoneshipping', 1, 1, 'tjshipping'),
		array ('plugin', 'qtc_default_zonetaxation', 1, 1, 'tjtaxation')
	);

	/** @var array Obsolete files and folders to remove*/
	protected $removeFilesAndFolders = array(
		'files'	=> array(
			// Removed since 2.2
			'administrator/components/com_quick2cart/views/vendor/tmpl/approvestore.php',
			'administrator/components/com_quick2cart/views/vendor/tmpl/default.php',
			'administrator/components/com_quick2cart/views/vendor/tmpl/newvender.php',
			'components/com_quick2cart/views/vendor/tmpl/default.php',
			'components/com_quick2cart/views/vendor/tmpl/default.xml',
			'components/com_quick2cart/views/managecoupon/metadata.xml',
			'components/com_quick2cart/views/reports/metadata.xml',
			'components/com_quick2cart/views/reports/tmpl/mypayouts.xml',

			/* version 2.3.1*/
			'components/com_quick2cart/views/zones/tmpl/default2.php',
			'com_quick2cart/productpage/popupslide.php',

			/* version 2.5.1*/
			'components/com_quick2cart/assets/css/bootstrap-slider.css',
			'components/com_quick2cart/assets/js/bootstrap-slider.js',

			/* version 2.6*/
			'components/com_quick2cart/views/orders/tmpl/default_store_cartdetail.php',
			'components/com_quick2cart/helpers/user.php',

			/* Removed in version 2.8*/
			'components/com_quick2cart/views/coupons/metadata.xml',
			'components/com_quick2cart/views/productpage/tmpl/users.php',
			'modules/mod_quick2cart/tmpl/default_itemrow.php',
			'modules/mod_quick2cart/tmpl/default_itemshort.php',
			'components/com_quick2cart/views/cartcheckout/tmpl/default_cartdetail.php',
			'administrator/components/com_quick2cart/defines.php'
		),
		'folders' => array(
			// Removed since 2.2

			/*
				* // Uncomment this in version 2.2.1
			'administrator/components/com_quick2cart/views/managecoupon',
			'administrator/components/com_quick2cart/views/reports',
			'components/com_quick2cart/bootstrap',
			'components/com_quick2cart/css',
			'components/com_quick2cart/images',
			'components/com_quick2cart/js'
			'components/com_quick2cart/views/managecoupon',
			'components/com_quick2cart/views/reports',
			*/
			/* Version 2.3.1*/
			'components/com_quick2cart/views/managecoupon',
			'components/com_quick2cart/views/reports',
			'components/com_quick2cart/assets/font-awesome'
		)
	);

	/**
	 * Runs before install, update or discover_update
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function preflight($type, $parent)
	{
	}

	/**
	 * Runs after fresh install
	 *
	 * @param   JInstaller  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function install($parent)
	{
		// Enable the extensions on fresh install
		$this->enableExtensions();
	}

	/**
	 * Runs after update
	 *
	 * @param   JInstaller  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function update($parent)
	{
	}

	/**
	 * Method to uninstall the component
	 *
	 * @param   JInstaller  $parent  Class calling this method
	 *
	 * @return  void
	 */
	public function uninstall($parent)
	{
		// Uninstall subextensions
		$status = $this->uninstallSubextensions($parent);

		// Show the post-uninstallation page
		$this->renderPostUninstallation($status);
	}

	/**
	 * Runs after install, update or discover_update
	 *
	 * @param   string      $type    install, update or discover_update
	 * @param   JInstaller  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		// File, folder related activiy goes here eg move, delete, etc
		$this->moveFiles($parent);

		$zooEleStatus = $this->addZooElement();

		$flexipath = JPATH_ROOT . '/components/com_flexicontent';

		if (Folder::exists($flexipath))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);

			$fields = array(
				$db->quoteName('enabled') . ' = 0'
			);

			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('content_quick2cart')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$db->execute();
		}

		// Copy tjinstaller file into packages folder
		$this->copyInstaller($parent);

		// Install subextensions
		$status = $this->installSubextensions($parent, 'postflight');

		// Uninstall obsolete subextensions
		$uninstallStatus = $this->uninstallObsoleteSubextensions($parent);

		// Remove obsolete files and folders
		$this->removeObsoleteFilesAndFolders($this->removeFilesAndFolders);

		// Get in built reports from tjreports extension
		$this->_getInBuiltTJReports();

		// Show the post-installation page
		$this->renderPostInstallation($status);
	}

	/**
	 * Add zoo element
	 *
	 * @return  BOOLEAN
	 */
	public function addZooElement()
	{
		$installSourcePath = dirname(__FILE__);
		$zooPath = JPATH_ROOT . '/media/zoo';

		if (Folder::exists($zooPath))
		{
			if (!Folder::copy($installSourcePath . '/zoo_element', $zooPath . '/elements', null, 1))
			{
				return 0;
			}
			else
			{
				return 1;
			}
		}
	}

	/**
	 * Move files
	 *
	 * @return  null
	 */
	public function moveFiles()
	{
		$amazonPlug = JPATH_ROOT . '/plugins/system/qtcamazon_easycheckout';

		// If plugin files installed
		if (Folder::exists($amazonPlug))
		{
			$moveFileArray[0]['src'] = JPATH_ROOT . '/plugins/system/qtcamazon_easycheckout/lib/qtcamazonIOPN.php';
			$moveFileArray[0]['dest'] = JPATH_ROOT . '/qtcamazonIOPN.php';

			$moveFileArray[1]['src'] = JPATH_ROOT . '/plugins/system/qtcamazon_easycheckout/lib/qtcamazonSuccess.php';
			$moveFileArray[1]['dest'] = JPATH_ROOT . '/qtcamazonSuccess.php';

			$moveFileArray[2]['src'] = JPATH_ROOT . '/plugins/system/qtcamazon_easycheckout/lib/qtcamazonCancel.php';
			$moveFileArray[2]['dest'] = JPATH_ROOT . '/qtcamazonCancel.php';

			foreach ($moveFileArray as $file)
			{
				if (File::exists($file['src']))
				{
					File::copy($file['src'], $file['dest']);
				}
			}
		}
	}

	/**
	 * Method to copy installer file
	 *
	 * @param   JInstaller  $parent  Class calling this method
	 *
	 * @return  void
	 */
	protected function copyInstaller($parent)
	{
		$src  = $parent->getParent()->getPath('source') . '/tjinstaller.php';
		$dest = JPATH_ROOT . '/administrator/manifests/packages/quick2cart/tjinstaller.php';

		File::copy($src, $dest);
	}

		/**
	 * Get in built tjReports
	 * and install them with extension
	 *
	 * @return  void
	 */
	public function _getInBuiltTJReports()
	{
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjreports/models');
		$model = BaseDatabaseModel::getInstance('Reports', 'TjreportsModel');
		$installed = 0;

		if ($model)
		{
			$installed = $model->addTjReportsPlugins();
		}

		return $installed;
	}
}
