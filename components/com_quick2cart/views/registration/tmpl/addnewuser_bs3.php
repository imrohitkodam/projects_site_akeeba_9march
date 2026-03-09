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

$document = Factory::getDocument();
?>

<script type="text/javascript">
	function adduser()
	{
		var name = document.getElementById('name').value;
		var username = document.getElementById('username').value;
		var password1 = document.getElementById('password1').value;
		var password2 = document.getElementById('password2').value;
		var emailid = document.getElementById('emailid').value;
		var pattern = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if(name && username && password1 && password2 && emailid)
		{
			if (pattern.test(emailid))
			{
				if (password1 != password2)
				{
					var msg = Joomla.Text._('COM_QUICK2CART_REGISTRATION_PASSWORD_MATCH_ERROR');
					alert(msg);

					return false;
				}

				techjoomla.jQuery.ajax({
					url:'<?php echo Uri::root();?>index.php?option=com_quick2cart&task=registration.newUser&tmpl=component',
					type:'POST',
					dataType:'json',
					data:
					{
						name:name,
						username:username,
						password1:password1,
						password2:password2,
						emailid:emailid
					},
					success:function(data)
					{
						if (data == true)
						{
							var msg = Joomla.Text._('COM_QUICK2CART_NEW_USER_CREATED_SUCCESSFULLY');
							alert(msg);
							window.parent.document.location.reload();
							window.close();
						}
						else
						{
							var msg = Joomla.Text._('COM_QUICK2CART_UNABLE_TO_CREATE_USER_BZ_OF') + Joomla.Text._('JLIB_DATABASE_ERROR_EMAIL_INUSE');
							alert(msg);
						}
					}
				});
			}
			else
			{
				var msg = "<?php echo Text::_('COM_QUICK2CART_INVALID_EMAILID')?>";
				alert(msg);
				return false;
			}
		}
		else
		{
			var msg = "<?php echo Text::_('COM_QUICK2CART_FILL_MANDATORY_FIELD')?>";
			alert(msg);
			return false;
		}
	}
</script>
<form name="addnewuser" id="addnewuser" method="post" class="form-validate" action="" enctype="multipart/form-data" >
<legend><?php echo Text::_( "COM_QUICK2CART_CREATE_NEW_USER");?></legend>
	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="name">
				<?php echo  Text::_("COM_QUICK2CART_CREATE_NEW_USER_NAME") . ' * '; ?>
			</label>
			<div class="controls">
				<input
					type="text"
					id="name"
					name="name"
					class="form-control"
					placeholder="<?php echo  Text::_('COM_QUICK2CART_CREATE_NEW_USER_NAME'); ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="username">
				<?php echo  Text::_("COM_QUICK2CART_CREATE_NEW_USER_LOGIN_NAME") . ' * '; ?>
			</label>
			<div class="controls">
				<input
					type="text"
					id="username"
					name="username"
					class="form-control"
					placeholder="<?php echo  Text::_('COM_QUICK2CART_CREATE_NEW_USER_LOGIN_NAME'); ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password1">
				<?php echo  Text::_("COM_QUICK2CART_CREATE_NEW_USER_PASSWORD") . ' * '; ?>
			</label>
			<div class="controls">
				<input
					type="password"
					id="password1"
					name="password1"
					class="form-control"
					placeholder="<?php echo  Text::_('COM_QUICK2CART_CREATE_NEW_USER_PASSWORD'); ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="password2">
				<?php echo  Text::_("COM_QUICK2CART_CREATE_NEW_USER_CONFIRM_PASSWORD") . ' * '; ?>
			</label>
			<div class="controls">
				<input
					type="password"
					id="password2"
					name="password2"
					class="form-control"
					placeholder="<?php echo  Text::_('COM_QUICK2CART_CREATE_NEW_USER_CONFIRM_PASSWORD'); ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="emailid">
				<?php echo  Text::_("COM_QUICK2CART_CREATE_NEW_USER_EMAIL") . ' * '; ?>
			</label>
			<div class="controls">
				<input
					type="text"
					id="emailid"
					name="emailid"
					class="form-control"
					placeholder="<?php echo  Text::_('COM_QUICK2CART_CREATE_NEW_USER_EMAIL'); ?>">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-11">
				<button id="viewMoreRec" class="btn btn-primary validate pull-right float-end" type="button" onclick="adduser()">
					<?php echo Text::_('COM_QUICK2CART_REGISTRE');?>
				</button>
			</div>
			<div class="col-sm-1">
				<a
					class="btn btn-default pull-right float-end"
					onclick="window.parent.jQuery('#addClientModal .modal-header .close').click();"
					title="<?php echo Text::_('JCANCEL'); ?>">
					<?php echo Text::_('COM_QUICK2CART_REGISTRE_CANCEL'); ?>
				</a>
			</div>
		</div>
		<input type="hidden" name="option" value="com_quick2cart">
		<input type="hidden" name="task" value="">
	</div>
</form>
