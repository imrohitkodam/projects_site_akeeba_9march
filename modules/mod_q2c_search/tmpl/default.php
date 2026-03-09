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
use Joomla\CMS\Language\Text;
?>
<form method="get" name="qtcSearchForm" id="qtcSearchForm">
	<input type="hidden" name="option" value="com_quick2cart">
	<input type="hidden" name="view" value="category">
	<input type="hidden" name="layout" value="default">
	<div id="qtcFilterWrapper" class="qtc-Search-Mod-<?php echo $moduleclass_sfx?>">
		<input type="text" name="filter_search" placeholder="<?php echo Text::_("MOD_SEARCH_PRODUCT");?>" onkeydown="if (event.keyCode == 13) { qtcSearchForm.form.submit(); return false; }">
	</div>
</form>
