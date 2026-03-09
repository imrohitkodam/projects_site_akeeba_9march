<?php

/**
 * @package         Smile Pack
 * @version         2.1.1 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

require_once __DIR__ . '/script.install.helper.php';

class Com_SmilePackInstallerScript extends Com_SmilePackInstallerScriptHelper
{
	public $name = 'SMILEPACK';
	public $alias = 'smilepack';
	public $extension_type = 'component';

	public function onAfterInstall()
	{
		if ($this->install_type == 'update') 
		{
			require_once __DIR__ . '/autoload.php';

			try {
				(new SmilePack\Migrator($this->installedVersion))->start();
			} catch (\Throwable $th)
			{
			}
        }
    }
}