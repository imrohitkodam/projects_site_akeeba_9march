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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

$doc = Factory::getDocument();
HTMLHelper::_('script',Uri::root(true) . '/libraries/techjoomla/assets/js/tjvalidator.js');
?>

<form name="adduserform" id="adduserform" method="post" class="form-validate form-horizontal" enctype="multipart/form-data" >
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('username'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('username'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('password1'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('password1'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('password2'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('password2'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('email'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('email'); ?></div>
	</div>
	<div class="control-group">
		<div class="controls">
			<a onclick="adduser()" class="btn btn-primary">
				<?php echo Text::_('JSUBMIT'); ?>
			</a>
			<a class="btn btn-default"
				onclick="window.parent.SqueezeBox.close();"
				title="<?php echo Text::_('JCANCEL'); ?>">
				<?php echo Text::_('JCANCEL'); ?>
			</a>
		</div>
	</div>
	<input type="hidden" name="option" value="com_quick2cart"/>
	<input type="hidden" name="task" value="customer_addressform.save"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
