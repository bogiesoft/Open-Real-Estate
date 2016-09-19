<?php echo CHtml::form(Yii::app()->createUrl('/seo/main/ajaxSave'), 'post', array('id'=>'seo_url_form')); ?>

<?php $afterRefresh = (isset($afterRefresh)) ? $afterRefresh : false; ?>

<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

<?php echo CHtml::errorSummary($friendlyUrl); ?>

<?php if($this->canUseDirectUrl){ ?>
    <div class="rowold no-mrg">
        <?php
        echo CHtml::activeCheckBox($friendlyUrl, 'direct_url');
        echo '&nbsp;'.CHtml::activeLabelEx($friendlyUrl, 'direct_url', array('class' => 'noblock'));;
        ?>
    </div>
<?php } ?>

<?php
echo CHtml::hiddenField('canUseDirectUrl', $this->canUseDirectUrl ? 1 : 0);

$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'url',
	'type' => 'string',
	'note' => $friendlyUrl->prefixUrl,
));
?>
<br/>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'title',
	'type' => 'string',
));
?>
<br/>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'description',
	'type' => 'string'
));
?>

<div class="clear"></div>
<br>

<?php
$this->widget('application.modules.lang.components.langFieldWidget', array(
	'model' => $friendlyUrl,
	'field' => 'keywords',
	'type' => 'string',
));
?>
<br/>

<?php if ($showBodyTextField):?>
	<div class="seo-body_text-block">
		<?php
		$this->widget('application.modules.lang.components.langFieldWidget', array(
			'model' => $friendlyUrl,
			'field' => 'body_text',
			'type' => 'text-editor',
		));
		?>
		<?php echo CHtml::hiddenField('showBodyTextField', 1); ?>
		
		<?php if ($afterRefresh):?>
			<?php 
				$filebrowserImageUploadUrl = '';
				$allowedContent = 'false';
				if (Yii::app()->user->checkAccess('upload_from_wysiwyg')) {
					$filebrowserImageUploadUrl = Yii::app()->createAbsoluteUrl('/site/uploadimage', array('type' => 'imageUpload', Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken));
					$allowedContent = 'true';
				}
			?>
			<script>
				jQuery('.seo-body_text-block').find('textarea').ckeditor({'toolbar':[['Source','-','Bold','Italic','Underline','Strike'],['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo'],['NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],['Styles','Format','Font','FontSize','TextColor','BGColor'],['Image','Link','Unlink','SpecialChar']],'forcePasteAsPlainText':true,'removeDialogTabs':'','contentsCss':['/ore_dev/assets/e83c64e2/contents.css'],'resize_enabled':true,'resize_dir':'both','language':'','baseHref':'','bodyClass':'','bodyId':'','docType':'<!DOCTYPE html>','filebrowserBrowseUrl':'','filebrowserFlashBrowseUrl':'','filebrowserImageBrowseUrl':'','filebrowserFlashUploadUrl':'','filebrowserUploadUrl':'','filebrowserImageBrowseLinkUrl':'','filebrowserImageUploadUrl':'<?php echo $filebrowserImageUploadUrl;?>','allowedContent':<?php echo $allowedContent;?>,'fullPage':false,'height':200,'width':'','uiColor':'','disableNativeSpellChecker':false,'autoUpdateElement':true});
			</script>
		<?php endif; ?>
	</div>
	<br/>
<?php endif;?>

<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_name]', $friendlyUrl->model_name); ?>
<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_id]', $friendlyUrl->model_id); ?>
<?php echo CHtml::hiddenField('SeoFriendlyUrl[id]', $friendlyUrl->id); ?>

<?php echo CHtml::submitButton(tc('Save'), array('onclick' => 'js:saveSeoUrl(); return false;')); ?>
&nbsp;<?php echo CHtml::button(tc('Close'), array('onclick' => 'js:$("#seo_dialog").dialog("close"); return false;', 'class' => 'button-blue button-gray')); ?>

<?php echo CHtml::endForm(); ?>