<?php if (!empty($citiesListResult)):?>
<div class="summary-site-ads-information">
	<div class="title highlight-left-right">
		<span>
			<h2><?php echo tc('Listings by categories');?></h2>
		</span>
	</div>
	<?php foreach ($citiesListResult as $cityId => $cityValue):?>
		<div class="item-info">
			<h3>
				<?php 
					echo CHtml::link(
						$cityValue[Yii::app()->language]['name'], 
						Yii::app()->controller->createUrl('/seo/main/viewsummaryinfo', array(
								'cityUrlName' => $cityValue[Yii::app()->language]['url']
							)
						)
					);
				?>
			</h3>
			
			<?php if (!empty($objTypesListResult)):?>
				<ul class="summary-info-obj-types">
					<?php foreach ($objTypesListResult as $objTypeId => $objValue):?>
						<li>
							<?php
								$linkName = $objValue[Yii::app()->language]['name'];
								$addCount = '';
								$class = 'inactive-obj-type-url';
								if (!empty($countApartmentsByCategories)) {
									if (isset($countApartmentsByCategories[$cityId]) && isset($countApartmentsByCategories[$cityId][$objTypeId])) {
										$class = 'active-obj-type-url';
										$addCount = '<span class="obj-type-count">('.$countApartmentsByCategories[$cityId][$objTypeId].')</span>';
									}
								}
								echo CHtml::link(
									$linkName, 
									Yii::app()->controller->createUrl('/seo/main/viewsummaryinfo', array(
											'cityUrlName' => $cityValue[Yii::app()->language]['url'],
											'objTypeUrlName' => $objValue[Yii::app()->language]['url'],
										)
									),
									array('class' => $class)
								).$addCount;
							?>
						</li>
					<?php endforeach;?>
				</ul>
			<?php endif;?>
		</div>
	<?php endforeach;?>
	<div class="clear">&nbsp;</div>
</div>
<div class="clear">&nbsp;</div>
<?php endif; ?>