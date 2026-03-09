<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.JShortcodes
 *
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

$title      = $item->anchor_title ? ' title="' . $item->anchor_title . '"' : '';
$anchor_css = $item->anchor_css ?: '';
$linktype   = $item->title;



?>
<?php if ($item->deeper) : ?>
	<li class="nav-item dropdown">
	<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"><?php echo $linktype; ?></a>
		<?php else : ?>
			<li class="nav-item">
			<a class="nav-link disabled" href="<?php echo $item->flink; ?>"<?php echo $title; ?>><?php echo $linktype; ?></a>
			<?php endif; ?>


