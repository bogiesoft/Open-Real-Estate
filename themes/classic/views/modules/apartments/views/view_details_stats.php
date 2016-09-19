<?php $isAdmin = Yii::app()->user->checkAccess("backend_access") ? true : false;?>

<?php if (!empty($resStats) && count($dataStats) > 1) :?>
	<script type="text/javascript">
		function drawChart() {
			var data = google.visualization.arrayToDataTable(<?php echo CJavaScript::jsonEncode($dataStats);?>);

			var options = {
				/*chart: {
				  title: '<?php echo tt('views_past_week', 'apartments');?>'
				},*/
				legend: { position: 'none', 'alignment': 'start' },
				vAxis: {minValue : 0, viewWindow: {min: 0}},
				width: <?php echo ($isAdmin) ? 850 : 600;?>,
				height: 300
			};

			var chart = new google.visualization.AreaChart(document.getElementById('stat_chart'));

			chart.draw(data, options);
		}

		jQuery(function ($) { 
			drawChart();
		});
	</script>

	<div class="apartment-stat-details-chart">
		<h3><?php echo tt('views_past_week', 'apartments');?></h3>
				
		<div id="stat_chart" style="width: <?php echo ($isAdmin) ? '850px' : '600px'?>; height: 350px;"></div>
		
		<?php if (issetModule('paidservices') && param('useUserads') && !$isAdmin):?>
			<?php 
				$wantTypes = HApartment::getI18nTypesArray();
				$typeName = (isset($wantTypes[$apartment->type]) && isset($wantTypes[$apartment->type]['current'])) ? mb_strtolower($wantTypes[$apartment->type]['current'], 'UTF-8') : '';				
			?>
			<?php if ($typeName) :?>
				<div class="promotion-paidservices-in-apartment">
					<div class="paidservices-promotion-title"><?php echo tt('Would you like to', 'apartments');?>&nbsp;<?php echo $typeName;?>&nbsp;<?php echo tt('quicker?', 'apartments');?></div>
					<div class="paidservices-promotion-description">
						<?php echo tt('Try to', 'apartments');?>&nbsp;
						<?php echo CHtml::link(tt('apply paid services', 'apartments'), Yii::app()->createUrl('/userads/main/update', array('id' => $apartment->id, 'show' => 'paidservices')), array('target'=>'_blank'));?>
					</div>
					<div class="clear"></div>
				</div>
			<?php endif;?>
		<?php endif;?>
		
	</div>
<?php else: ?>
	<div class="apartment-stat-details-chart">
		<h3><?php echo tc('No statistics available');?></h3>
	</div>
<?php endif; ?>
