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

$conditionsList = \NRFramework\Conditions\ConditionsHelper::getConditionsList(strtolower(Text::_('NR_MODULE')));

// This removes the Virtuemart conditions, keeps the Hikashop and removes the "Hikashop" title prefix.
foreach ($conditionsList as $groupKey => $group)
{
    if ($groupKey === 'eCommerce')
    {
        foreach ($group['conditions'] as $conditionKey => $condition)
        {
            if (strpos($conditionKey, 'com_virtuemart#') === 0)
            {
                unset($conditionsList[$groupKey]['conditions'][$conditionKey]);
                continue;
            }

            $conditionsList[$groupKey]['conditions'][$conditionKey]['title'] = str_replace('Hikashop', '', Text::_($condition['title']));
        }
    }
}
?>
<div class="sp-index">
    <?php
    foreach ($conditionsList as $groupKey => $group)
    {
        ?>
        <div class="sp-index--group">
            <div class="sp-index--group--sidebar">
                <div class="sp-index--group--sidebar--inner">
                    <div class="sp-index--group--sidebar--inner--title"><?php echo Text::_($group['title']); ?></div>
                    <div class="sp-index--group--sidebar--inner--desc"><?php echo Text::_($group['desc']); ?></div>
                </div>
            </div>

            <div class="sp-index--group--conditions">
                <?php
                foreach ($group['conditions'] as $condition)
                {
                    ?>
                    <div class="sp-index--group--conditions--item">
                        <div class="sp-index--group--conditions--item--content">
                            <div class="sp-index--group--conditions--item--content--title"><?php echo Text::_($condition['title']); ?></div>
                            <div class="sp-index--group--conditions--item--content--desc"><?php echo Text::_($condition['desc']); ?></div>
                        </div>
                        <a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/display-conditions#available-display-conditions" target="_blank">
                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_28_839" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="18" height="19">
                                    <rect y="0.5" width="18" height="18" fill="currentColor"/>
                                </mask>
                                <g mask="url(#mask0_28_839)">
                                    <path d="M8.24805 13.2497H9.74805V8.74969H8.24805V13.2497ZM8.99805 7.24969C9.21055 7.24969 9.38867 7.17782 9.53242 7.03407C9.67617 6.89032 9.74805 6.71219 9.74805 6.49969C9.74805 6.28719 9.67617 6.10907 9.53242 5.96532C9.38867 5.82157 9.21055 5.74969 8.99805 5.74969C8.78555 5.74969 8.60742 5.82157 8.46367 5.96532C8.31992 6.10907 8.24805 6.28719 8.24805 6.49969C8.24805 6.71219 8.31992 6.89032 8.46367 7.03407C8.60742 7.17782 8.78555 7.24969 8.99805 7.24969ZM8.99805 16.9997C7.96055 16.9997 6.98555 16.8028 6.07305 16.4091C5.16055 16.0153 4.3668 15.4809 3.6918 14.8059C3.0168 14.1309 2.48242 13.3372 2.08867 12.4247C1.69492 11.5122 1.49805 10.5372 1.49805 9.49969C1.49805 8.46219 1.69492 7.48719 2.08867 6.57469C2.48242 5.66219 3.0168 4.86844 3.6918 4.19344C4.3668 3.51844 5.16055 2.98407 6.07305 2.59032C6.98555 2.19657 7.96055 1.99969 8.99805 1.99969C10.0355 1.99969 11.0105 2.19657 11.923 2.59032C12.8355 2.98407 13.6293 3.51844 14.3043 4.19344C14.9793 4.86844 15.5137 5.66219 15.9074 6.57469C16.3012 7.48719 16.498 8.46219 16.498 9.49969C16.498 10.5372 16.3012 11.5122 15.9074 12.4247C15.5137 13.3372 14.9793 14.1309 14.3043 14.8059C13.6293 15.4809 12.8355 16.0153 11.923 16.4091C11.0105 16.8028 10.0355 16.9997 8.99805 16.9997ZM8.99805 15.4997C10.673 15.4997 12.0918 14.9184 13.2543 13.7559C14.4168 12.5934 14.998 11.1747 14.998 9.49969C14.998 7.82469 14.4168 6.40594 13.2543 5.24344C12.0918 4.08094 10.673 3.49969 8.99805 3.49969C7.32305 3.49969 5.9043 4.08094 4.7418 5.24344C3.5793 6.40594 2.99805 7.82469 2.99805 9.49969C2.99805 11.1747 3.5793 12.5934 4.7418 13.7559C5.9043 14.9184 7.32305 15.4997 8.99805 15.4997Z" fill="currentColor"/>
                                </g>
                            </svg>
                        </a>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php