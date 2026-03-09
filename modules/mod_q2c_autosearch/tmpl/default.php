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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('script','modules/mod_q2c_autosearch/assets/js/autosearch.js');
HTMLHelper::_('stylesheet','modules/mod_q2c_autosearch/assets/css/custom.css');
Text::script('QTC_NO_PRODUCTS_FOUND');

$noOfProductShow = $params->get('no_of_product_show', 10, 'INTEGER');

$ssession = Factory::getSession();
$moduleSearch = $ssession->get('module_search');
$ssession->set('module_search', '');
?>
<div id="qtcAutoSearchFilter" class="q2c_auto_suggest qtc-AutoSearch-Filter-<?php echo $moduleclass_sfx; ?>">
	<form action="" method="POST" class="form-inline" role="form">
		<div class="form-group">
			<input
				type="text"
				class="form-control search_query_q2c_auto_suggest"
				id="search_query_q2c_auto_suggest"
				value="<?php echo (isset($moduleSearch) && $moduleSearch) ? $moduleSearch : '';?>"
				placeholder="<?php echo Text::_('MOD_Q2C_AUTOSEARCH_PRODUCT');?>">
		</div>
	</form>
	<div id="modq2c-autosuggest-data-container"></div>
	<div class="selected_data_containter" id="selected_data_containter"></div>
</div>
<?php
$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

if (!class_exists('comquick2cartHelper'))
{
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

$comquick2cartHelper = new comquick2cartHelper;
$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=category&layout=default');

$script   = array();
$script[] = 'var baseurl = "' . Uri::root() . '"';
$script[] = 'var noOfProductShow = ' . $noOfProductShow;
$script[] = 'var Itemid = ' . $itemid;
$script[] = 'quick2cart.modAutosearch.initJs();';
Factory::getDocument()->addScriptDeclaration(implode("\n", $script));
?>
