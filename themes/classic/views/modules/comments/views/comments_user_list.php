<?php
$this->pageTitle .= ' - '.tc('Comments');
$this->breadcrumbs = array(
	tc('Control panel') => Yii::app()->createUrl('/usercpanel'),
	tc('Comments'),
);
?>

<?php if(param('useUserads') && $forMyListingsComments):?>
	<h2><?php echo tc('Comments for my listings');?></h2>
	<?php
	$this->widget('NoBootstrapGridView',
		array(
			'id'=>'for-my-listings-comments',
			'dataProvider'=>$forMyListingsComments->search(),
			'filter'=>null,
			'afterAjaxUpdate' => 'function(){$(".rating-block-comments-for-my-listings input").rating({"readOnly":true}); attachStickyTableHeader();}',
			'template'=>"{summary}\n{items}\n{pager}",
			'columns' => array(
				array(
					'name' => 'status',
					'type' => 'raw',
					'value' => 'Comment::getStatusesArray(false, $data->status)',
					'filter' => false,
					'sortable' => false,
				),
				array(
					'header' => tc('Sections'),
					'type' => 'raw',
					'value' => '$data->getLinkForSection()',
					'filter' => false,
					'sortable' => false,
				),
				array(
					'name' => 'body',
					'filter' => false,
					'sortable' => false,
				),
				array(
					'name' => 'dateCreated',
					'header' => Yii::t('module_comments', 'Creation date'),
					'headerHtmlOptions' => array('style' => 'width:130px;'),
					'filter' => false,
					'sortable' => false,
				),
				array(
					'name' => 'rating',
					'type' => 'raw',
					'value'=>'$this->grid->controller->widget("CStarRating", array(
						"name" => $data->id,
						"id" => $data->id,
						"value" => $data->rating,
						"readOnly" => true,
					), true)',
					'headerHtmlOptions' => array('style' => 'width:100px;'),
					'htmlOptions' => array('class' => 'rating-block-comments-for-my-listings', 'style' => 'width:100px;'),
					'filter' => false,
					'sortable' => false,
				),
			),
		)
	);
	?>
	<div class="clear"></div><br />
<?php endif;?>


<h2><?php echo tc('My comments');?></h2>
<?php
$this->widget('NoBootstrapGridView',
    array(
        'id'=>'my-comments',
        'dataProvider'=>$myComments->search(),
        'filter'=>null,
		'afterAjaxUpdate' => 'function(){$(".rating-block-my-comments input").rating({"readOnly":true}); attachStickyTableHeader();}',
		'template'=>"{summary}\n{items}\n{pager}",
        'columns' => array(
            array(
				'name' => 'status',
				'type' => 'raw',
				'value' => 'Comment::getStatusesArray(false, $data->status)',
				'filter' => false,
				'sortable' => false,
			),
			array(
				'header' => tc('Sections'),
				'type' => 'raw',
				'value' => '$data->getLinkForSection()',
				'filter' => false,
				'sortable' => false,
			),
			array(
				'name' => 'body',
				'filter' => false,
				'sortable' => false,
			),
			array(
				'name' => 'dateCreated',
				'header' => Yii::t('module_comments', 'Creation date'),
				'headerHtmlOptions' => array('style' => 'width:100px;'),
				'filter' => false,
				'sortable' => false,
			),
			array(
				'name' => 'rating',
				'type' => 'raw',
				'value'=>'$this->grid->controller->widget("CStarRating", array(
					"name" => $data->id,
					"id" => $data->id,
					"value" => $data->rating,
					"readOnly" => true,
				), true)',
				'headerHtmlOptions' => array('style' => 'width:100px;'),
				'htmlOptions' => array('class' => 'rating-block-my-comments', 'style' => 'width:100px;'),
				'filter' => false,
				'sortable' => false,
			),
        ),
    )
);