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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$input = Factory::getApplication()->input;
$tmpl  = $input->get('tmpl');
?>
<div class=" <?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	ob_start();
	include($this->toolbar_view_path);
	$html = ob_get_contents();
	ob_end_clean();

	if($tmpl != 'component')
	{
		echo $html;
	}
	if (!empty($this->form))
	{
		echo $this->form;
	}
	?>
</div>
