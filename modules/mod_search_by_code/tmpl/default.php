<div class="search-by-code-form-wrapper">
	<form id="search-by-code">
		<input type="text" class="code" name="code" placeholder="<?php echo JText::_('MOD_SEARCH_BY_CODE_PLACEHOLDER')?>" value="" >
		<input id="submit" type="submit" class="code-button" value="<?php echo JText::_('MOD_SEARCH_BY_CODE_SUBMIT') ?>">
		<input type="hidden" name='id' value="<?php echo $params->get('page_id'); ?>">
	</form>
</div>