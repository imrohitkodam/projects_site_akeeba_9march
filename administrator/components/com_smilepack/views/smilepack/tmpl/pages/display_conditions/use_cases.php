<?php

/**
 * @package         Smile Pack
 * @version         2.1.1 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$useCases = [
    [
        'title' => 'COM_SMILEPACK_USE_CASE_1_TITLE',
        'conditions' => [
            'NR_ASSIGN_DEVICES'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_2_TITLE',
        'conditions' => [
            'NR_ASSIGN_TIMERANGE',
            'NR_WEEKDAY'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_3_TITLE',
        'conditions' => [
            'NR_USERGROUP',
            'NR_ASSIGN_GROUP_JCONTENT'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_4_TITLE',
        'conditions' => [
            'NR_COUNTRY'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_5_TITLE',
        'conditions' => [
            'COM_SMILEPACK_NEW_VISITOR',
            'NR_URL'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_6_TITLE',
        'conditions' => [
            'NR_HOMEPAGE',
            'NR_ASSIGN_REFERRER'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_7_TITLE',
        'conditions' => [
            'COM_SMILEPACK_CART_CONTAINS_PRODUCTS'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_8_TITLE',
        'conditions' => [
            'COM_SMILEPACK_CART_AMOUNT'
        ]
    ],
    [
        'title' => 'COM_SMILEPACK_USE_CASE_9_TITLE',
        'conditions' => [
            'NR_COOKIE'
        ]
    ],
];

?>
<div class="sp-use-cases">
    <?php
    foreach ($useCases as $useCase)
    {
        ?>
        <div class="sp-use-cases--item">
            <h3><?php echo Text::_($useCase['title']); ?></h3>

            <div class="sp-use-cases--item--conditions">
                <div class="sp-use-cases--item--conditions--label"><?php echo Text::_('COM_SMILEPACK_CONDITIONS'); ?></div>
                <div class="sp-use-cases--item--conditions--list">
                    <?php
                    foreach ($useCase['conditions'] as $condition)
                    {
                        ?>
                        <span><?php echo Text::_($condition); ?></span>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php