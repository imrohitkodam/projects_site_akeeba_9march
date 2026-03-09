<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Component\ComponentHelper;

$version = new Version ();
$JoomlaVersion = ( float ) $version->getShortVersion ();

/** @var CMSApplication */
$app = Factory::getApplication ();
$input = $app->getInput ();
$content = $input->get ( 'system_editor_data', '', 'raw' );

$config = $app->getConfig ();

$type = $config->get ( 'editor' );
$editor = Editor::getInstance ( $type );
$exclude = [ 
		'pagebreak',
		'readmore'
];

$doc = Factory::getApplication ()->getDocument ();
$wa = $doc->getWebAssetManager();

$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
if ( $params->get ( 'dark_mode', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'administrator/components/com_jpagebuilder/assets/css/main-dark.css');
}

?>

<?php echo $editor->display('content', $content, '100%', 'max(calc(100vh - 300px), 600px)', '', '30', $exclude); ?>