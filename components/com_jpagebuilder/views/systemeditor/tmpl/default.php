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
$JoomlaVersion = $version->getShortVersion ();

/** @var CMSApplication */
$app = Factory::getApplication ();
$doc = $app->getDocument();
$wa = $doc->getWebAssetManager();
$input = $app->getInput ();
$content = $input->get ( 'system_editor_data', '', 'raw' );

$config = $app->getConfig ();

$type = $config->get ( 'editor' );
$editor = Editor::getInstance ( $type );
$exclude = [ 
		'pagebreak',
		'readmore'
];

$wa->addInlineStyle('button.btn.btn-secondary{background-color:#001b4c;color:#fff;display:inline-flex;font-weight:500;font-size:14px;line-height:16px;text-align:center;text-decoration:none;vertical-align:middle;cursor:pointer;user-select:none;border:0;padding:6px 16px;border-radius:6px;margin:10px 0;}');

$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
if ( $params->get ( 'dark_mode', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'administrator/components/com_jpagebuilder/assets/css/main-dark.css');
}
?>

<?php echo $editor->display('content', $content, '100%', 'max(calc(100vh - 300px), 600px)', '', '30', $exclude); ?>