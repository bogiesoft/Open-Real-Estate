<?php
$this->pageTitle .= ' - '.tt('My bookings', 'usercpanel');
$this->breadcrumbs = array(
    tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
    tt('My bookings', 'usercpanel'),
);


if (issetModule('bookingcalendar')) {
    //echo "<div class='flash-notice'>".tt('booking_table_to_calendar', 'booking')."</div>";
}
?>

<?php
$this->widget('NoBootstrapGridView',
    array(
        'id'=>'users-booking-grid',
		'afterAjaxUpdate' => 'function(){attachStickyTableHeader();}',
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'columns' => array(
            /*array(
                'name' => 'id',
                'htmlOptions' => array(
                    'class' => 'id_column',
                ),
            ),*/
            array(
                'name' => 'active',
                'type' => 'raw',
                'value' => '$data->getMyBookingButton()',
                'htmlOptions' => array(
                    'style' => 'width: 150px;',
                    //'class'=>'apartments_status_column',
                ),
                'sortable' => false,
                'filter' => Bookingtable::getAllStatuses(),
            ),
            array(
                'header' => tt('Apartment ID', 'booking') . ' / ' . tt('Type', 'apartments'),
                'type' => 'raw',
                'value' => '$data->getIdType()',
                'filter' => false,
                'sortable' => false,
                'htmlOptions' => array('style' => 'width:100px;'),
            ),
            array(
                'name' => 'num_guest',
                'type' => 'raw',
                'value' => '$data->num_guest ? $data->num_guest : "-"',
                'filter' => false,
                'sortable' => false,
            ),
            array(
                'name' => 'comment',
                'value' => 'truncateText($data->comment)',
                'filter' => true,
                'sortable' => false,
            ),
            array(
                'name' => 'date_start',
                'value' => '$data->time_in ? $data->date_start . " (". $data->getTimeInName().")" : "" ',
                'filter' => true,
                'sortable' => false,
                'htmlOptions' => array('style' => 'width:150px;'),
            ),
            array(
                'name' => 'date_end',
                'value' => '$data->time_out ? $data->date_end . " (". $data->getTimeOutName().")" : "" ',
                'filter' => true,
                'sortable' => false,
                'htmlOptions' => array('style' => 'width:150px;'),
            ),
            array(
                'header' => tt('Creation date', 'booking'),
                'value' => '$data->date_created',
                'type' => 'raw',
                'filter' => false,
                'sortable' => false,
                //'htmlOptions' => array('style' => 'width:130px;'),
            ),
        ),
    )
);
//echo 'userID = '.Yii::app()->user->id;

Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery.jeditable.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScript('editable_select_booking_table', "
		function ajaxSetBookingTableStatus(elem, id, id_elem, items){
			$('#editable_select-'+id_elem).editable('".Yii::app()->controller->createUrl("bookingtableactivate")."', {
				data   : items,
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
