<?=$form->open()?>
	<?php foreach ($form->fields() as $field): ?>
		<div><?=$field->render(TRUE)?></div>
	<?php endforeach; ?>
<?=$form->close()?>