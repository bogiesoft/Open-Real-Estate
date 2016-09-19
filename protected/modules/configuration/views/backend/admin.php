<?php

$this->pageTitle=Yii::app()->name . ' - ' . tc('Manage settings');
$this->breadcrumbs=array(
	tc('Settings'),
);
$this->menu = array(
	array(),
);
$this->adminTitle = tc('Manage settings');

$this->widget('CustomGridView', array(
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'afterAjaxUpdate' => 'function(){$("a[rel=\'tooltip\']").tooltip(); $("div.tooltip-arrow").remove(); $("div.tooltip-inner").remove(); attachStickyTableHeader(); showHideAdditionalSettingsBlock($("select[name=\'ConfigurationModel[section]\'] option:selected").val())}',
    'id'=>'config-table',
	'columns'=>array(
        array(
            'header'=>tt('Section'),
			'name' => 'section',
            'value' => 'tt($data->section)',
            'filter' => $this->getSections(false),
        ),
		array(
            'header' => tt('Setting'),
			'value'=>'$data->title',
			'type'=>'raw',
			'htmlOptions' => array('class' => 'width250'),
		),
		array(
            'header' => tt('Value'),
			'name'=>'value',
            'type'=>'raw',
			'value' => 'ConfigurationModel::getAdminValue($data)',
			'htmlOptions' => array('class' => 'width150'),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template' => '{update}',
			'buttons' => array(
				'update' => array(
					'visible' => 'ConfigurationModel::getVisible($data->type)',
					/*'options' => array('data-toggle' => 'modal'),*/
					'click' => 'js: function() { updateConfig($(this).attr("href")); return false; }',
				),
			),
		),
	),
)); ?>

<div class="additinal-settings-block well settings-mail">
	<div class="rowold">
		<h2><?php echo tt('Testing settings', 'configuration');?></h2>
	</div>
	<div class="rowold">
		<?php echo tt('To email', 'configuration');?>:&nbsp;
		<input type="text" value="" id="toEmail" name="toEmail" class="span3"/>
	</div>
	<div class="rowold">
		<?php 
			$this->widget('bootstrap.widgets.TbButton',
			array('buttonType' => 'button',
				'type' => 'primary',
				'icon' => 'ok white',
				'label' => tt('Send', 'messages'),
				'htmlOptions' => array(
					'onclick' => "sendTestEmail(); return false;",
				)
			));
		?>
	</div>
</div>


<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>

<div id="form_param"></div>

<div class="modal-footer">
    <a href="#" class="btn btn-primary" onclick="saveChanges(); return false;"><?php echo tc('Save'); ?></a>

    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'label'=>tc('Close'),
        'url'=>'#',
        'htmlOptions'=>array('data-dismiss'=>'modal'),
    )); ?>
</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">
	function sendTestEmail() {
		if ($("#toEmail").val().length) {
			$.ajax({
				url: '<?php echo Yii::app()->createUrl('/configuration/backend/main/sendTestEmail');?>',
				dataType: 'json',
				data: {"toEmail" : $("#toEmail").val()},
				success: function(data) {
					if (data.result == 'passed') {
						message(data.message);
					}
					else {
						error(data.message);
					}
				}
			});
		}
		else {
			error('<?php echo tc('Enter the required value');?>');
		}
	}
	
	function hideAllAdditionalSettingsBlocks() {
		$(".additinal-settings-block").hide();
	}
	
	function showHideAdditionalSettingsBlock(currentSection) {
		hideAllAdditionalSettingsBlocks();
		
		$(".settings-" + currentSection).show();
	}
	
    function updateConfig(href){
        $('#myModal').modal('show');
        $('#form_param').html('<img src="<?php echo Yii::app()->theme->baseUrl."/images/pages/indicator.gif"; ?>" alt="<?php echo tc('Content is loading ...'); ?>" style="position:absolute;margin: 10px;">');
        $('#form_param').load(href + '&ajax=1');
    }

    function saveChanges(){
        var val = $('#config_value').val();
		var required = $('#config_required').val();

        if(!val && required) {
            alert('<?php echo tt('Enter the required value');?>');
            return false;
        }

        var id = $('#config_id').val();
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->request->baseUrl.'/configuration/backend/main/updateAjax'; ?>",
            data: { "id": id, "val": val },
			success: function(msg){
				$('#config-table').yiiGridView.update('config-table');
				$('#myModal').modal('hide');

				if (msg == 'error_save') {
					document.location.href = '<?php echo Yii::app()->createUrl("/configuration/backend/main/admin"); ?>';
				}
			}
        });
        return;
    }

</script>