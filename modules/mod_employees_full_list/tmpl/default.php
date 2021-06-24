<div class="employees-full-list-wrapper<?php echo $params->get('moduleclass_sfx') ?>" data-limit="<?php echo $params->get('display_limit'); ?>" data-offset="<?php echo $params->get('display_limit'); ?>" data-id="<?php echo $lingualSettings->employee_page_id ?>">
		<?php 
			foreach ($activeEmployees as $activeEmployee):
				require dirname(__FILE__).'/default-list-item.php';
			endforeach;
		?>
</div>
<div class="employees-full-list-show-more-button-wrapper">
	<button class="more-employees">
		<?php echo JText::_('MOD_FULL_LIST_EMPLOYEES_SHOW_MORE') ?>
	</button>
</div>
