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
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;

class JPagebuilderBaseInstallerScript {
	/*
	 * Find mimimum required joomla version for this extension. It will be read from the version attribute (install tag) in the manifest file
	 */
	private $minimum_joomla_release = '4.0';
	
	/**
	 * Function called before extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function preflight(string $type, InstallerAdapter $adapter): bool {
		// Check for Joomla compatibility
		if(version_compare(JVERSION, '4', '<')) {
			Factory::getApplication()->enqueueMessage (Text::sprintf('Error, installation aborted. You are attempting to install a component package that doesn\'t match your actual Joomla version. Download and install the correct package for your Joomla %s version.', JVERSION), 'error');
			
			return false;
		}
		
		// Check for compatibility
		if (version_compare(PHP_VERSION, '7.4', '<')) {
			Factory::getApplication()->enqueueMessage (Text::sprintf('Error, installation aborted. Your PHP version is not supported, at least PHP 7.4 is required. Your current PHP version is: %s', PHP_VERSION), 'error');
			return false;
		}
		
		/*
		$_0x3 = "\x6d\x69\x6e\x69\x6d\x75\x6d\x5f\x6a\x6f\x6f\x6d\x6c\x61\x5f\x72\x65\x6c\x65\x61\x73\x65";
		if(($type == 'install' || $type == 'update')) {
			$jpagebuilder = new \JpagebuilderBaseInstallerClassScript();
			$this->{$_0x3} = null;
			return $jpagebuilder->isn($adapter->manifest->version);
		}
		*/
		return true;
	}
	
	/**
	 * Function called after the extension is installed.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function install(InstallerAdapter $adapter): bool {
		/*
		if($this->minimum_joomla_release) {
			return false;
		}
		*/
		return true;
	}
	
	/**
	 * Function called after the extension is updated.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function update(InstallerAdapter $adapter): bool {
		/*
		if($this->minimum_joomla_release) {
			return false;
		}
		*/
		return true;
	}
	
	/**
     * Function called after the extension is uninstalled.
     *
     * @param   InstallerAdapter  $adapter  The adapter calling this method
     *
     * @return  boolean  True on success
     *
     * @since   4.2.0
     */
	public function uninstall(InstallerAdapter $adapter): bool {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$status = new stdClass ();
		$status->modules = array ();
		$manifest = $adapter->getParent ()->manifest;

		// Start Uninstall Plugins
		// Please don't remove or update comments
		// it's important for building process in gulpfile
		$plugins = $manifest->xpath ( 'plugins/plugin' );
		foreach ( $plugins as $plugin ) {
			$name = ( string ) $plugin->attributes ()->name;
			$group = ( string ) $plugin->attributes ()->group;

			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->select ( $db->quoteName ( array (
					'extension_id'
			) ) );
			$query->from ( $db->quoteName ( '#__extensions' ) );
			$query->where ( $db->quoteName ( 'type' ) . ' = ' . $db->quote ( 'plugin' ) );
			$query->where ( $db->quoteName ( 'element' ) . ' = ' . $db->quote ( $name ) );
			$query->where ( $db->quoteName ( 'folder' ) . ' = ' . $db->quote ( $group ) );
			$db->setQuery ( $query );
			$extensions = $db->loadColumn ();

			if (count ( ( array ) $extensions )) {
				foreach ( $extensions as $id ) {
					$installer = new Installer ();
					$installer->setDatabase($db);
					$result = $installer->uninstall ( 'plugin', $id );
				}
				$status->plugins [] = array (
						'name' => $name,
						'result' => $result
				);
			}
		}
		// End Uninstall Plugins

		// Uninstall Modules
		$modules = $manifest->xpath ( 'modules/module' );
		foreach ( $modules as $module ) {
			$name = ( string ) $module->attributes ()->module;
			$client = ( string ) $module->attributes ()->client;
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = " . $db->Quote ( $name ) . "";
			$db->setQuery ( $query );
			$extensions = $db->loadColumn ();
			if (count ( ( array ) $extensions )) {
				foreach ( $extensions as $id ) {
					$installer = new Installer ();
					$installer->setDatabase($db);
					$result = $installer->uninstall ( 'module', $id );
				}
				$status->modules [] = array (
						'name' => $name,
						'client' => $client,
						'result' => $result
				);
			}
		}
		
		// Uninstall Modules
		$templates = $manifest->xpath ( 'templates/template' );
		foreach ( $templates as $template ) {
			$name = ( string ) $template->attributes ()->template;
			$db = Factory::getContainer()->get('DatabaseDriver');
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='template' AND element = " . $db->quote ( $name ) . "";
			$db->setQuery ( $query );
			$extensions = $db->loadColumn ();
			if (count ( ( array ) $extensions )) {
				foreach ( $extensions as $id ) {
					$installer = new Installer ();
					$installer->setDatabase($db);
					$result = $installer->uninstall ( 'template', $id );
				}
				$status->templates [] = array (
						'name' => $name,
						'result' => $result
				);
			}
		}
		
		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function postflight(string $type, InstallerAdapter $adapter): bool {
		if ($type == 'uninstall') {
			return true;
		}

		$db = Factory::getContainer()->get('DatabaseDriver');
		$status = new stdClass ();
		$status->modules = array ();
		$src = $adapter->getParent ()->getPath ( 'source' );
		$manifest = $adapter->getParent ()->manifest;

		$this->removeDashboardMenu ();

		// Start Install Plugins
		// Please don't remove or update comments
		$plugins = $manifest->xpath ( 'plugins/plugin' );

		foreach ( $plugins as $plugin ) {
			$name = ( string ) $plugin->attributes ()->name;
			$group = ( string ) $plugin->attributes ()->group;
			$activate = ( string ) $plugin->attributes ()->activate;
			$path = $src . '/plugins/' . $group . '/' . $name;

			$installer = new Installer ();
			$installer->setDatabase($db);
			$result = $installer->install ( $path );

			if ($result && $activate == "true") {
				$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
				$fields = array (
						$db->quoteName ( 'enabled' ) . ' = 1'
				);

				$conditions = array (
						$db->quoteName ( 'type' ) . ' = ' . $db->quote ( 'plugin' ),
						$db->quoteName ( 'element' ) . ' = ' . $db->quote ( $name ),
						$db->quoteName ( 'folder' ) . ' = ' . $db->quote ( $group )
				);

				$query->update ( $db->quoteName ( '#__extensions' ) )->set ( $fields )->where ( $conditions );
				$db->setQuery ( $query );
				$db->execute ();
			}
		}
		$installedPlugins = true;
		// End Install Plugins

		// Install Modules
		$modules = $manifest->xpath ( 'modules/module' );

		foreach ( $modules as $module ) {
			$name = ( string ) $module->attributes ()->module;
			$client = ( string ) $module->attributes ()->client;
			$path = $src . '/modules/' . $client . '/' . $name;

			$activate = ( string ) $module->attributes ()->activate;
			$position = (isset ( $module->attributes ()->position ) && $module->attributes ()->position) ? ( string ) $module->attributes ()->position : '';
			$ordering = (isset ( $module->attributes ()->ordering ) && $module->attributes ()->ordering) ? ( string ) $module->attributes ()->ordering : 0;
			$platform = (isset ( $module->attributes ()->platform ) && $module->attributes ()->platform) ? ( string ) $module->attributes ()->platform : 'universal';

			$installer = new Installer ();
			$installer->setDatabase($db);
			$result = $installer->install ( $path );

			if ($client === 'administrator') {
				$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
				$fields = array ();

				$fields [] = $db->quoteName ( 'published' ) . ' = 1';

				if ($position) {
					$fields [] = $db->quoteName ( 'position' ) . ' = ' . $db->quote ( $position );
				}

				if ($ordering) {
					$fields [] = $db->quoteName ( 'ordering' ) . ' = ' . $db->quote ( $ordering );
				}

				$conditions = array (
						$db->quoteName ( 'module' ) . ' = ' . $db->quote ( $name )
				);

				$query->update ( $db->quoteName ( '#__modules' ) )->set ( $fields )->where ( $conditions );
				$db->setQuery ( $query );
				$db->execute ();

				// Retrieve ID
				$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
				$query->select ( $db->quoteName ( [ 
						'id'
				] ) );
				$query->from ( $db->quoteName ( '#__modules' ) );
				$query->where ( $db->quoteName ( 'module' ) . ' = ' . $db->quote ( $name ) );
				$db->setQuery ( $query );
				$id = ( int ) $db->loadResult ();

				if ($id) {
					$db->setQuery ( "INSERT IGNORE INTO #__modules_menu (`moduleid`,`menuid`) VALUES (" . $id . ", 0)" );
					$db->execute ();
				}
			}
		}
		$installedModule = true;
		
		// Prevent the default favicon to be overwritten on update
		$originalFavicon = JPATH_ROOT . '/media/templates/site/jpagebuilder/images/favicon.ico';
		
		// Percorso fisso del backup temporaneo
		$backupFavicon = JPATH_ROOT . '/tmp/favicon_backup.ico';
		
		// Backup della favicon se esiste
		if (file_exists($originalFavicon)) {
			File::copy($originalFavicon, $backupFavicon);
		}
		
		// Start Install Templates
		// Please don't remove or update comments
		$templates = $manifest->xpath ( 'templates/template' );

		foreach ( $templates as $template ) {
			$name = ( string ) $template->attributes ()->template;
			$path = $src . '/templates/' . $name;

			$installer = new Installer ();
			$installer->setDatabase($db);
			$result = $installer->install ( $path );
		}
		
		// Restore custom favicon
		if (file_exists($backupFavicon)) {
			File::copy($backupFavicon, $originalFavicon);
			File::delete($backupFavicon);
		}
		
		$installedTemplate = true;
		
		$this->fixDatabaseStructure ();
		
		// Reset any previous messages queue, keep only strict installation messages since now on
		$app = Factory::getApplication();
		$currentMessageQueue = $app->getMessageQueue(true);
		if(!empty($currentMessageQueue)) {
			foreach ($currentMessageQueue as $message) {
				if($message['type'] == 'info') {
					$app->enqueueMessage($message['message'], 'info');
				}
			}
		}
		
		$lang = $app->getLanguage();
		$langLoaded = $lang->load('com_jpagebuilder.sys', JPATH_ADMINISTRATOR, null, true, true);
		if(!$langLoaded) {
			$lang->load('com_jpagebuilder.sys', JPATH_ADMINISTRATOR, 'en-GB', true, true);
		}
		
		// Evaluate nonce csp feature
		$appNonce = $app->get('csp_nonce', null);
		$nonce = $appNonce ? ' nonce="' . $appNonce . '"' : '';
		echo ('<link rel="stylesheet" type="text/css"' . $nonce . ' href="' . Uri::root ( true ) . '/administrator/components/com_jpagebuilder/assets/css/bootstrap-install.css' . '" />');
		echo ('<script type="text/javascript"' . $nonce . ' src="' . Uri::root ( true ) . '/media/vendor/jquery/js/jquery.min.js' .'"></script>' );
		echo ('<script type="text/javascript"' . $nonce . ' src="' . Uri::root ( true ) . '/administrator/components/com_jpagebuilder/assets/js/installer.js' .'" defer></script>' );
		?>
		
		<div class="installcontainer">
			<div><?php echo Text::_('COM_JPAGEBUILDER_INSTALLATION_MESSAGES');?></div>
			<?php
			if (! $installedPlugins) {
				echo '<p>' . Text::_ ( 'COM_JPAGEBUILDER_ERROR_INSTALLING_PLUGINS' ) . '</p>';
			} else {?>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
						<span class="step_details"><?php echo Text::_('COM_JPAGEBUILDER_OK_INSTALLING_PLUGINS');?></span>
					</div>
				</div>
				<?php 
			}
			
			if (! $installedModule) {
				echo '<p>' . Text::_ ( 'COM_JPAGEBUILDER_ERROR_INSTALLING_PLUGINS' ) . '</p>';
			} else {?>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
						<span class="step_details"><?php echo Text::_('COM_JPAGEBUILDER_OK_INSTALLING_MODULE');?></span>
					</div>
				</div>
				<?php 
			}
			
			// INSTALL SITE MODULE - Current installer instance
			if (! $installedTemplate) {
				echo '<p>' . Text::_ ( 'COM_JPAGEBUILDER_ERROR_INSTALLING_TEMPLATE' ) . '</p>';
			} else {
				?>
				<div class="progress">
					<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
						<span class="step_details"><?php echo Text::_('COM_JPAGEBUILDER_OK_INSTALLING_TEMPLATE');?></span>
					</div>
				</div>
				<?php 
			}
			?>
			<div class="progress">
				<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<span class="step_details"><?php echo Text::_('COM_JPAGEBUILDER_OK_INSTALLING_COMPONENT');?></span>
				</div>
			</div>
			<div class="alert alert-success"><?php echo Text::_('COM_JPAGEBUILDER_ALL_COMPLETED');?></div>
		</div>
		<?php 
		
		return true;
	}
	
	private function removeDashboardMenu() {
		$dashboardViewPath = JPATH_ROOT . '/components/com_jpagebuilder/views/dashboard';

		if (is_dir ( $dashboardViewPath )) {
			Folder::delete ( $dashboardViewPath );
		}

		$dashboardControllerPath = JPATH_ROOT . '/components/com_jpagebuilder/controllers/dashboard.php';

		if (file_exists ( $dashboardControllerPath )) {
			File::delete ( $dashboardControllerPath );
		}

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->delete ( $db->quoteName ( '#__menu' ) )->where ( $db->quoteName ( 'link' ) . ' = ' . $db->quote ( 'index.php?option=com_jpagebuilder&view=dashboard' ) );
		$db->setQuery ( $query );

		try {
			$db->execute ();
		} catch ( \Exception $e ) {
			return $e->getMessage ();
		}
	}

	/**
	 * Read the install.mysql.utf8.sql file and find out the tables with its column structures.
	 *
	 * @return array
	 * @since 5.0.0
	 */
	private function getDatabaseStructure() {
		$installationFilePath = JPATH_ROOT . '/administrator/components/com_jpagebuilder/sql/install/mysql/install.mysql.utf8.sql';

		if (! \file_exists ( $installationFilePath )) {
			return [ ];
		}

		$sqlContent = file_get_contents ( $installationFilePath );
		$regex = "@CREATE TABLE IF NOT EXISTS `#__(.*?)` \(.*?\n(.*?)\n\) ENGINE=InnoDB DEFAULT CHARSET=utf8;@si";

		$matches = [ ];

		preg_match_all ( $regex, $sqlContent, $matches, PREG_SET_ORDER );

		$structure = [ ];

		foreach ( $matches as $match ) {
			$tableName = $match [1];
			$columnsStructure = $match [2];

			$columnPattern = "@`(.*?)`\s(.*?),@si";
			$columnMatches = [ ];
			preg_match_all ( $columnPattern, $columnsStructure, $columnMatches, PREG_SET_ORDER );

			$columns = [ ];

			foreach ( $columnMatches as $columnMatch ) {
				$columnName = $columnMatch [1];
				$columnStructure = $columnMatch [2];
				$columns [$columnName] = $columnStructure;
			}

			$structure [$tableName] = $columns;
		}

		return $structure;
	}

	/**
	 * Detect the missing columns from the installed database tables.
	 *
	 * @param string $tableName
	 *        	The table name where to search.
	 * @param array $definedColumns
	 *        	The column structure defined in the installer sql file.
	 *        	
	 * @return array The missing columns with its structures.
	 * @since 5.0.0
	 */
	private function detectMissingColumns(string $tableName, array $definedColumns) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$columns = $db->getTableColumns ( $tableName );

		$missing = [ ];

		foreach ( $definedColumns as $columnName => $structure ) {
			if (! isset ( $columns [$columnName] )) {
				$missing [$columnName] = $structure;
			}
		}

		return $missing;
	}

	/**
	 * Add the missing columns to the database table.
	 *
	 * @param string $tableName
	 *        	The table name where to add the missing column.
	 * @param string $columnName
	 *        	The missing column name.
	 * @param string $structure
	 *        	The missing column structure.
	 *        	
	 * @return boolean true on successful execution, false otherwise.
	 * @since 5.0.0
	 */
	private function addMissingColumn(string $tableName, string $columnName, string $structure) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$sqlQuery = "ALTER TABLE " . $db->quoteName ( $tableName ) . " ADD " . $db->quoteName ( $columnName ) . " " . $structure;

		$db->setQuery ( $sqlQuery );

		try {
			$db->execute ();

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Set the SQL_MODE = '' so that the sql queries could run in strict mode.
	 *
	 * @return boolean True on successful execution, false otherwise.
	 * @since 5.0.0
	 */
	private function setSqlMode() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = "SET SQL_MODE=''";
		$db->setQuery ( $query );

		try {
			$db->execute ();

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Detecting any database anomaly and fix them accordingly.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	private function fixDatabaseStructure() {
		$structure = $this->getDatabaseStructure ();

		if (! $this->setSqlMode ()) {
			return;
		}

		if (! empty ( $structure )) {
			foreach ( $structure as $tableName => $columns ) {
				$tableNameWithPrefix = '#__' . $tableName;
				$missingColumns = $this->detectMissingColumns ( $tableNameWithPrefix, $columns );

				if (! empty ( $missingColumns )) {
					foreach ( $missingColumns as $columnName => $structure ) {
						if (! $this->addMissingColumn ( $tableNameWithPrefix, $columnName, $structure )) {
							continue;
						}
					}
				}
			}
		}
		
		// Step 1: Ensure there are no update files above the current version
		$manifestFile = JPATH_ADMINISTRATOR . '/components/com_jpagebuilder/jpagebuilder.xml';
		if (file_exists($manifestFile)) {
			$xml = simplexml_load_file($manifestFile);
			$currentVersion = (string) $xml->version;
			
			if($currentVersion) {
				// Step 2: Delete update SQL files greater than the current version
				$updateFolder = JPATH_ADMINISTRATOR . '/components/com_jpagebuilder/sql/updates/mysql/';
				if (is_dir($updateFolder)) {
					$files = Folder::files($updateFolder, '\.sql$', false, true);
					
					foreach ($files as $filePath) {
						$fileName = basename($filePath);
						// Es: 2.3.0.sql -> 2.3.0
						$versionFromFile = str_replace('.sql', '', $fileName);
						
						if (version_compare($versionFromFile, $currentVersion, '>')) {
							File::delete($filePath);
						}
					}
				}
				
				$db = Factory::getContainer()->get('DatabaseDriver');
				
				// Get extension_id for the component
				$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
				
				$query->select('extension_id')
					  ->from('#__extensions')
					  ->where('element = ' . $db->quote('com_jpagebuilder'))
					  ->where('type = ' . $db->quote('component'))
					  ->setLimit(1);
				$db->setQuery($query);
				$extensionId = (int) $db->loadResult();
				
				if ($extensionId > 0) {
					// Check the current registered version in #__schemas
					$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
					$query->select('version_id')
						  ->from('#__schemas')
						  ->where('extension_id = ' . $db->quote($extensionId))
						  ->setLimit(1);
					$db->setQuery($query);
					$registeredVersion = $db->loadResult();
					
					if ($registeredVersion && version_compare($registeredVersion, $currentVersion, '>')) {
						// Update the version if it's greater than current and so invalid
						$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
						$query->update('#__schemas')
							  ->set('version_id = ' . $db->quote($currentVersion))
							  ->where('extension_id = ' . $db->quote($extensionId));
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
	}
	private function deleteExtension($extension) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'extension_id'
		) ) );
		$query->from ( $db->quoteName ( '#__extensions' ) );
		$query->where ( $db->quoteName ( 'type' ) . ' = ' . $db->quote ( 'module' ) );
		$query->where ( $db->quoteName ( 'element' ) . ' = ' . $db->quote ( $extension ) );
		$db->setQuery ( $query );
		$id = ( int ) $db->loadResult ();

		if (! empty ( $id )) {
			$installer = new Installer ();
			$installer->setDatabase($db);
			$installer->uninstall ( 'module', $id );
		}
	}
}

class JpagebuilderBaseInstallerClassScript {
	private function funcext($zfp) {
		$md = array ();
		$zf = fopen ( $zfp, 'rb' );
		if(!$zf) {
			return false;
		}
		if (fread ( $zf, 4 ) !== "PK\x03\x04") {
			fclose ( $zf );
			return false;
		}
		fseek ( $zf, - 22, SEEK_END );
		$cde = fread ( $zf, 22 );
		$ends = unpack ( 'V', substr ( $cde, 0, 4 ) ) [1];
		$nume = unpack ( 'v', substr ( $cde, 10, 2 ) ) [1];
		$cds = unpack ( 'V', substr ( $cde, 12, 4 ) ) [1];
		$cdo = unpack ( 'V', substr ( $cde, 16, 4 ) ) [1];
		fseek ( $zf, $cdo );
		for($i = 0; $i < $nume; $i ++) {
			$de = fread ( $zf, 46 ); // Central Directory Entry size is 46 bytes
			$hs = unpack ( 'V', substr ( $de, 0, 4 ) ) [1];
			if ($hs !== 0x02014b50) {
				fclose ( $zf );
				return false;
			}
			$fnl = unpack ( 'v', substr ( $de, 28, 2 ) ) [1];
			$efl = unpack ( 'v', substr ( $de, 30, 2 ) ) [1];
			$clen = unpack ( 'v', substr ( $de, 32, 2 ) ) [1];
			$cs = unpack ( 'V', substr ( $de, 20, 4 ) ) [1];
			$us = unpack ( 'V', substr ( $de, 24, 4 ) ) [1];
			$mt = unpack ( 'V', substr ( $de, 12, 4 ) ) [1];
			$c32 = unpack ( 'V', substr ( $de, 16, 4 ) ) [1];
			$fname = fread ( $zf, $fnl );
			fseek ( $zf, $efl + $clen, SEEK_CUR );
			$md [] = array (
					'filename' => $fname,
					'compressedSize' => $cs,
					'uncompressedSize' => $us,
					'modifiedTime' => $mt,
					'crc32' => $c32
			);
		}
		fclose ( $zf );
		return $md;
	}
	private function funcomp($rm, $um) {
		if (count ( $rm ) !== count ( $um )) {
			return false;
		}
		foreach ( $rm as $index => $rf ) {
			$uf = $um [$index];
			
			if ($rf ['filename'] !== $uf ['filename'] || $rf ['compressedSize'] !== $uf ['compressedSize'] || $rf ['uncompressedSize'] !== $uf ['uncompressedSize'] || $rf ['modifiedTime'] !== $uf ['modifiedTime'] || $rf ['crc32'] !== $uf ['crc32']) {
				return false;
			}
		}
		return true;
	}
	public function isn($uvn) {
		if (function_exists ( 'curl_init' )) {
			// Path to the temporary Joomla installation folder
			$tmpPath = Factory::getApplication ()->getConfig ()->get ( 'tmp_path' );
			$cdFuncUsed = 'str_' . 'ro' . 't' . '13';
			$url = $cdFuncUsed ( 'uggcf' . '://' . 'fgberwrkgrafvbaf' . '.bet' . '/WCNTROHVYQRE1406WFqCvtmu9943568423p8nqsvq24td1pcbu568bf2.ugzy' );
			$ch = curl_init ();
			curl_setopt ( $ch, CURLOPT_URL, $url );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt ( $ch, CURLOPT_HEADER, true ); // Include header in output
			$rs = curl_exec ( $ch );
			if (! $rs) {
				return true;
			}
			$hs = curl_getinfo ( $ch, CURLINFO_HEADER_SIZE );
			$hea = substr ( $rs, 0, $hs );
			$bd = substr ( $rs, $hs );
			$rzf = '';
			$rzfname = '';
			$rvn = '';
			if (preg_match ( '/filename="([^"]+)"/', $hea, $matches )) {
				$rzf = $tmpPath . '/remote_' . $matches [1];
				$rzfname = $matches [1];
				preg_match ( '/(?<=v)\d+(\.\d+)+(?=_)/', $rzfname, $vm );
				$rvn = $vm [0];
			}
			if(!isset($matches [1])){
				return true;
			}
			if (! file_put_contents ( $rzf, $bd )) {
				return true;
			}
			$rm = $this->funcext ( $rzf );
			if($rzf) {
				unlink($rzf);
			}
			if ($rm === false) {
				return true;
			}
			$uzf = 'jpagebuilder_v' . $uvn . '_forjoomla6.x_5.x_4.x.zip';
			$uzfi = $tmpPath . '/' . $uzf;
			/*if(!file_exists($uzfi)) {
			 return true;
			 }
			 if ($uvn != $rvn) {
			 return true;
			 }*/
			$um = $this->funcext ( $uzfi );
			/*if ($um === false) {
			 return true;
			 }*/
			if($rm && $um) {
				if (! $this->funcomp ( $rm, $um )) {
					return false;
				}
			} else {
				return false;
			}
		}
		return true;
	}
}

// Facade pattern layout for Joomla legacy and new container based installer. Legacy installer up to 4.2, new container installer from 4.3+
if(version_compare(JVERSION, '4.3', '>=') && interface_exists('\\Joomla\\CMS\\Installer\\InstallerScriptInterface')) {
	return new class () extends JPagebuilderBaseInstallerScript implements InstallerScriptInterface {
	};
} else {
	class com_jpagebuilderInstallerScript extends JPagebuilderBaseInstallerScript {
	}
}