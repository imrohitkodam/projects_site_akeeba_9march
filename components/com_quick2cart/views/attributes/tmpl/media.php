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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
$document = Factory::getDocument();
$jinput   = Factory::getApplication()->input;
$js_key="techjoomla.jQuery( document ).ready(function() {
 var edit = ".$jinput->get( 'edits',0,'INT').";

 if(edit === 3) {
parent.SqueezeBox.close(); }
});";
$document->addScriptDeclaration($js_key);

HTMLHelper::_('script', 'components/com_quick2cart/assets/js/resumable.js');
?>
<?php
	$params                = ComponentHelper::getParams('com_quick2cart');
	$currencies            = $params->get('addcurrency');
	$curr                  = explode(',',$currencies);
	$mediaCount            = !empty($this->getMediaDetail)?count($this->getMediaDetail):0;
	$mediaDetail           = !empty($this->getMediaDetail) ? $this->getMediaDetail:array();
	$fileUploadMode        = $params->get('eProdUploadMode',1);
	$eProdUExpiryMode      = $params->get('eProdUExpiryMode','epboth');
	$allowedFileExtensions = $params->get('allowedFileExtensions');
	$eProdMaxFileLimit     = $params->get('eProdMaxFileLimit',5);
	$extArr                = explode(',', $params->get('allowedFileExtensions'));
	$extArrString          = "'". implode("','", $extArr) ."'" ;
	$item_id               = $jinput->get('item_id','','INT');
	$edit                  = $jinput->get( 'edit',0,'INTEGER');

	if(empty($item_id))
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
			<div class="well" >
				<div class="alert alert-danger">
					<span ><?php echo Text::_('QTC_MEDIA_INVALID_ITEM_ID'); ?> </span>
				</div>
			</div>
		</div>
		<?php
		return false;
	}
	$productHelper          = new productHelper;
	$m                      = 0;
	$attribute_container_id = "qtc_mediaContainer".$m;
	$mediafile_id           = $jinput->get('file_id','','INT');
	$mediaDetail            = array();

	// for edit media
	if(!empty($mediafile_id))
	{
		$mediaDetail = $productHelper->getMediaDetail(0,$mediafile_id);
	}
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
	<form method="POST" name="adminForm" class="form-validate" action="index.php">
		<legend><?php echo Text::_('QTC_CCK_ADD_MEDIA_FILES_TITLE')?></legend>
		<div class="">
			<div id=<?php echo $attribute_container_id ?> class="qtc_mediaContainer" >
				<div class="com_qtc_media_repeat_block well well-small com_qtc_img_border">
				<?php
				// CHECK for media file in front end
					$comquick2cartHelper = new comquick2cartHelper;
					$att_list_path=$comquick2cartHelper->getViewpath('product','media',"SITE","SITE");
					ob_start();
						include($att_list_path);
						$html_attri = ob_get_contents();
					ob_end_clean();

					echo $html_attri;
				?>
				<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="">
			<input type="hidden" name="edit" value="<?php echo $edit ?>" />
			<input type="hidden" name="item_id" value="<?php echo !empty($item_id) ? $item_id : ''; ?>" />
			<input type="hidden" name="mediafile_id" value="<?php echo !empty($mediafile_id) ? $mediafile_id : ''; ?>" />
			<input type="hidden" name="qtcMediaId" value="<?php echo !empty($qtcMediaId)?$qtcMediaId:'' ?>" />
			<input type="hidden" name="option" value="com_quick2cart">
			<input type="hidden" name="task" value="attributes.addMediaFile" />
			<input type="hidden" name="controller" value="attributes" />
			<input class="btn btn-success validate" type="submit"  value="<?php echo Text::_('QTC_ADDATTRI_SAVE')?>" />		<?php echo HTMLHelper::_( 'form.token' ); ?>
		</div>
	</form>
</div>

<script type="text/javascript">
	// globle params
	var media_current_id = <?php echo $mediaCount  ?>;
	var maxAllowedMedia = <?php echo $eProdMaxFileLimit-1  ?>;  // as media start from 0
	var r = new Array();
	var fileUpload_maxFileSize=<?php echo $params->get('eProdMaxSize')*1024*1024;?>;
	var fileUpload_acceptFileTypes = [<?php
			$arr=explode(',', $params->get('allowedFileExtensions'));
			echo "'". implode("','", $arr) ."'" ; /* Make format as 'avi','mp4','mpeg'. */
				?>];
	var qtcUploadTarget = '<?php echo Uri::root();?>index.php?option=com_quick2cart&task=product.mediaUpload';

	// CREATE OBJECT TO UPLOAD FILE
	var r = new Resumable({
		target:'<?php echo Uri::root();?>index.php?option=com_quick2cart&task=product.mediaUpload',
		fileParameterName:"qtcMediaFile<?php echo $m; ?>",
		chunkSize : 2*1024*1024,
		query:{},
		maxFiles:1,
		fileType: fileUpload_acceptFileTypes,
		maxFileSize: fileUpload_maxFileSize
	});

	r.assignBrowse(document.getElementById("qtcMediaFile<?php echo $m; ?>"));
	r.assignDrop(document.getElementById("qtcMediaFile<?php echo $m; ?>"));

	r.on('fileSuccess', function(file){
		var len = file.chunks.length ;
		if(jQuery.parseJSON(file.chunks[len-1].xhr.response).validate.error === 1)
		{
			alert('<?php echo Text::_('COM_QUICK2CART_FILE_UPLOAD_ERROR_MSG'); ?>');
		}
		else
		{
			jQuery("#ajax_upload_hidden<?php echo $m; ?>").val( jQuery.parseJSON(file.chunks[len-1].xhr.response).fileUpload.filePath );
			alert('<?php echo Text::_('COM_QUICK2CART_UPLOAD_SUCCESS_MSG'); ?>');
		}
	});

	r.on('fileAdded', function(file, event){
		jQuery("#qtc_progress-bar<?php echo $m; ?>").css('width',0 +'%');
		r.upload();
	});

	r.on('fileError', function(file, message){
		alert(message);
	});

	r.on('progress', function(){
		jQuery("#qtc_progress-bar<?php echo $m; ?>").css('width',(100 * r.progress(1)) + '%');
	});

	r.on('error', function(message, file){
		alert(message);
	});
</script>


