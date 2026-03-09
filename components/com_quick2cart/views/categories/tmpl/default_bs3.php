<?php
/**
 * @package     JTicketing
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2023 Techjoomla. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

// No direct access

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$app  = Factory::getApplication();
$menuParams = $app->getParams('com_quick2cart');
$show_child_categories = $menuParams->get('show_child_categories');
$show_categories_layout = $menuParams->get('show_categories_layout');

// Load helper file if not exist
if (!class_exists('comquick2cartHelper'))
{
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

?>

<div class="container">
	<?php
	if ($app->getParams()->get('show_page_heading', 1))
	{
		?>
		<div class="page-header"><h1><?php echo $this->escape($app->getParams()->get('page_heading'));?></h1></div>
		<?php
	}
	?>
</div>

<div class="container-fluid categories-list  q2c-wrapper techjoomla-bootstrap tjBs3 container-fluid qtc-cat-prod">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=categories&Itemid='. $this->itemId); ?>" method="post" name="adminForm" id="adminForm"
		class="q2cFilters">
		<div class="clearfix"></div>
		<hr class="hr-condensed"/>
		<div class="float-end">
		<?php
		echo LayoutHelper::render('joomla.searchtools.default',
			array (
				'view' => $this
			)
		);
		?>
		</div>
		<div class="clearfix"></div>
		<hr class="hr-condensed"/>
		<?php
		if (empty($this->items))
		{
			?>
			<div class="alert alert-info" role="alert"><?php echo Text::_('NODATA');?></div>
			<?php
		}
		else
		{
			?>
			<div class="row mt-3" id="q2c_pc_category">
				<?php 
				if ($show_categories_layout)
				{
					$layout = new FileLayout('categories_pin_view', JPATH_ROOT . '/components/com_quick2cart/layouts/categories');
				}
				else 
				{
					$layout = new FileLayout('categories_list_view', JPATH_ROOT . '/components/com_quick2cart/layouts/categories');
				}

				$displayData = array(
					'items' => $this->items,
					'display_pin_page' => 1
				);

				$output = $layout->render($displayData);

				echo $output;
				?>
			</div>
			<div class="row mt-2">
			<div class="col-xs-12">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<?php echo HTMLHelper::_('form.token');?>
	</form>
</div>

