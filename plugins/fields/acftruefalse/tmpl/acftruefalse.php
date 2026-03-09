<?php

/**
 * @package         Advanced Custom Fields
 * @version         3.1.3 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$fieldValue = $field->value;

if ($fieldValue == '')
{
	return;
}

echo ($fieldValue) ? $fieldParams->get('true', Text::_('JTRUE')) : $fieldParams->get('false', Text::_('JFALSE'));