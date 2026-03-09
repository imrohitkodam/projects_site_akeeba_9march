<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

//no direct access
defined('_JEXEC') or die('restricted access');
$doc = Factory::getApplication ()->getDocument ();
$wa = $doc->getWebAssetManager();
$wa->useScript('jquery');
$wa->useScript('form.validate');
$wa->useScript('keepalive');

$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
if ( $params->get ( 'dark_mode', 1 )) {
	$wa->registerAndUseStyle('jpagebuilder.canvasdark', 'administrator/components/com_jpagebuilder/assets/css/main-dark.css');
}

?>
<form action="<?php echo Route::_('index.php?option=com_jpagebuilder&view=page&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php echo $this->form->renderField('title'); ?>
	<?php echo $this->form->renderFieldset('permissions'); ?>
	<?php echo $this->form->renderField('id'); ?>
	<input type="hidden" name="task" value="item.edit" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>