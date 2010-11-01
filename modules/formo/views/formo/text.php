<p>
	<?=$field->css('width', '300px')->add_class('input')?>
	<span id="error-<?=$field->attr('id')?>" class="errorMessage"><?=ucfirst($field->_error)?></span>
</p>