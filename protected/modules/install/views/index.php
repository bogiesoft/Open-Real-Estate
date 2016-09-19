<div class="install-select-lang">
	<h2 align="center">
		Select a language<br/>
		Выберите язык
	</h2>

	<br/>

	<?php
		foreach(Yii::app()->user->getFlashes() as $key => $message) {
			if ($key=='error' || $key == 'success' || $key == 'notice'){
				echo "<div class='flash-{$key}'>{$message}</div>";
			}
		}
	?>

	<br/>

	<div class="span-6" align="center">
		<a href="<?php echo $this->createUrl('config', array('lang' => 'ru')); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag_ru.png" alt="Russian / Русский / Russisch / Ruso" /><br/>Russian / Русский / Russisch / Ruso</a>
	</div>

	<div class="span-6" align="center">
		<a href="<?php echo $this->createUrl('config', array('lang' => 'en')); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag_us.png" alt="English / Английский / Englisch / Inglés" /><br/>English / Английский / Englisch / Inglés</a>
	</div>

	<div class="span-6" align="center">
		<a href="<?php echo $this->createUrl('config', array('lang' => 'de')); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag_de.png" alt="German / Немецкий / Deutsch / Alemán" /><br/>German / Немецкий / Deutsch / Alemán</a>
	</div>

	<div class="span-6" align="center">
		<a href="<?php echo $this->createUrl('config', array('lang' => 'es')); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/flag_es.png" alt="Spanish / Испанский / Spanisch / Español" /><br/>Spanish / Испанский / Spanisch / Español</a>
	</div>
</div>