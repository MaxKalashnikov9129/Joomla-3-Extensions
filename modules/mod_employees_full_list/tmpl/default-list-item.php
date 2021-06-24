<a href='<?php echo $menuLink.'?item='.$activeEmployee->id ?>' class='employee-link'>
	<div class='employee-wrapper'>
	 	<div class='employee-image-wrapper'>
	 		<img src='<?php echo $activeEmployee->image?>' alt='' />
	 	</div>
	 	<div class='employee-info-wrapper'>
	 		<div class='employee-full-name-wrapper wrapper'>
	 			<div class='employee-name'>
	 				<?php echo $activeEmployee->fio_real ?>
	 			</div>
	 		</div>
	 		<div class='employee-city-wrapper wrapper'>
	 			<div class='employee-city-label label'>
	 				<?php echo JText::_('MOD_FULL_LIST_EMPLOYEES_CITY_LABEL') ?>
	 			</div>
	 			<div class='employee-city'>
	 				<?php echo JText::_('MOD_FULL_LIST_EMPLOYEES_CITY') ?>
	 			</div>
	 		</div>
	 		<div class='employee-agency-wrapper wrapper'>
	 			<div class='employee-agency-label label'>
	 				<?php echo JText::_('MOD_FULL_LIST_EMPLOYEES_AGENCY_LABEL') ?>
	 			</div>
	 			<div class='employee-agency label'>
	 				<?php echo $activeEmployee->name_office ?>
	 			</div>
	 		</div>
	 		<div class='employee-phone-wrapper wrapper'>
	 			<div class='employee-phone-label label'>
	 				<?php echo JText::_('MOD_FULL_LIST_EMPLOYEES_PHONE_LABEL') ?>
	 			</div>
	 			<div class='employee-phone'>
	 				<?php echo $activeEmployee->tel ?>
	 			</div>
	 		</div>
	 	</div>
	</div>
</a>