<div class="rowold">
    <div class=""><?php echo tc('Floor') ?>:</div>
    <div>
        <?php echo CHtml::textField('Apartment[floor_min]', $model->floor_min, array('class' => 'width100', 'placeholder' => tc('Floor from'), 'id' => 'floor_min')); ?>
        <?php echo CHtml::textField('Apartment[floor_max]', $model->floor_max, array('class' => 'width100', 'placeholder' => tc('Floor to'), 'id' => 'floor_max')); ?>
    </div>
</div>