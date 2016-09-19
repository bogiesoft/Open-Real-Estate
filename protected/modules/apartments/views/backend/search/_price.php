<div class="rowold">
    <div class=""><?php echo tc('Price') ?>:</div>
    <div>
        <?php echo CHtml::textField('Apartment[price_min]', $model->price_min, array('class' => 'width100', 'placeholder' => tc('Price from'), 'id' => 'price_min')); ?>
        <?php echo CHtml::textField('Apartment[price_max]', $model->price_max, array('class' => 'width100', 'placeholder' => tc('Price to'), 'id' => 'price_max')); ?>
        <span class=""><?php echo (issetModule('currency')) ? Currency::getDefaultCurrencyName() : param('siteCurrency'); ?></span>
    </div>
</div>