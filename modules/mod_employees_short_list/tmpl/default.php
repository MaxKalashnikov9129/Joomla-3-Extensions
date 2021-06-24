<div class="employees-short-list-wrapper<?php echo $params->get('moduleclass_sfx') ?>">
	<?php foreach ($activeEmployees as $key => $activeEmployee): ?>
		<a href="<?php echo $menuLink.'/?item='.$activeEmployee->id ?>" class="employee-wrapper invisible" data-index="<?php echo $key+1 ?>">
			<div class="employee-image-wrapper">
				<img src="<?php echo $activeEmployee->image ?>" alt="" class="employee-image">
			</div>
			<div class="employee-info-wrapper">
				<div class="employee-full-name-wrapper">
					<?php echo $activeEmployee->fio_real ?>
				</div>
			</div>
		</a>
	<?php endforeach ?>
</div>
<?php if($showMoreLink): ?>
	<div class="employees-short-list-show-more-button-wrapper invisible">
		<a href="<?php echo $showMoreLink ?>">
			<?php echo JText::_('MOD_EMPLOYEES_SHORT_LIST_SHOW_MORE') ?>
		</a>
	</div>
<?php endif; ?>
