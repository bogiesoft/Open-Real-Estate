<?php
$this->pageTitle .= ' - '.tt('My agents');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tt('My agents'),
);

$columns = array(
    array(
        'header' => tc('Status'),
        'name' => 'agent_status',
        'type' => 'raw',
        'value' => 'HUser::returnStatusHtml($data, "agents-table")'
    ),

    array(
        'header' => tt('Name', 'contactform'),
        'name' => 'username',
    ),

    array(
        'header' => tc('Email'),
        'name' => 'email',
    ),

    array(
        'header' => tt('Phone', 'contactform'),
        'name' => 'phone',
    ),

    array(
        'header' => tc('Listings'),
        'value' => '$data->getLinkToAllListings()',
        'type' => 'raw',
    )
);

if (issetModule('paidservices')) {
    $columns[] = array(
        'header' => tc('Balance'),
        'value' => 'HUser::getAgentBalanceAndLink($data)',
        'type' => 'raw',
    );
}

$columns[] = array(
    'header' => '',
    'value' => 'HUser::getLinkDelAgent($data)',
    'type' => 'raw',
);

    $this->widget('NoBootstrapGridView', array(
            'id' => 'agents-table',
			'afterAjaxUpdate' => 'function(){attachStickyTableHeader();}',
            'dataProvider' => $model->search(),
            'columns' => $columns,
        )
    );

Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('editable_agents_table', "
        var agentStatuses = ".CJavaScript::encode(User::getAgentStatusList()).";

		function ajaxSetAgentStatus(elem, id, id_elem){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("/usercpanel/main/ajaxSetAgentStatus")."', {
				data   : agentStatuses,
				type   : 'select',
				cancel : '".tc('Cancel')."',
				submit : '".tc('Ok')."',
				style  : 'inherit',
				submitdata : function() {
					return {id : id_elem};
				}
			});
		}
	",
    CClientScript::POS_HEAD);

?>


