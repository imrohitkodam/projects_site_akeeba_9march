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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$link     = Uri::base();
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
    <div class="well" >
        <div class="alert alert-danger">
            <span ><?php echo Text::_('QTC_OPERATION_CANCELLED'); ?> </span>
        </div>
        <a class="btn" href="<?php echo $link; ?>">
            <?php echo Text::_('QTC_BACK'); ?>
        </a>
    </div>
</div><!-- eoc techjoomla-bootstrap -->
