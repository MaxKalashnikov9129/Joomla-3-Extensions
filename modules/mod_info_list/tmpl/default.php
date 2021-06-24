<div class="info-list-items-wrapper">
	<?php foreach ($elements as $key => $element): ?>
		<div class="info-list-item-wrapper">
			<div class="info-list-item-image-wrapper invisible" data-index="<?php echo $key+1 ?>">
				<div class="inner-rectangle"></div>
				<div class="info-list-item-icon-wrapper">
					<img class="info-list-item-icon" data-src="<?php echo $element['icon'] ?>">
				</div>
			</div>
			<div class="info-list-item-info-wrapper invisible" data-index="<?php echo $key+1 ?>">
				<div class="info-list-item-info-header">
					<?php echo $element['header'] ?>
				</div>
				<div class="info-list-item-info-text">
					<?php echo $element['text'] ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
</div>