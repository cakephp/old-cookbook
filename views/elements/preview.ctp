<?php
if (isset($highlight)) {
	$highlight->auto = false;
}
$messages = array(
	'Perfect!' => __('How\'d you like them apples?', true),
	'Done!' => __('How\'s that for a slice of fried gold?', true),
	'Tastey!' => __('How\'s that for a slice of cake?', true),
	'Yeah!' => __('Are we done yet?', true)
);
$confirm = array_rand($messages);
$message = $messages[$confirm];
$previewText = $form->error('Revision.preview', $message);
if (!$previewText) {
	return;
}
$errors = $this->validationErrors;
unset($errors['Revision']['preview']);
if (empty($errors['Revision'])) {
	unset ($errors['Revision']);
} else {
	return;
}
?>
<fieldset>
	<?php echo $previewText ?>
	<div id='preview'>
		<h2><?php echo h($data['Revision']['title']) ?> </h2>
		<div class="view"><?php
			if (isset($highlight)) {
				echo $highlight->auto($data['Revision']['content']);
			} else {
				echo $data['Revision']['content'];
			}
		?></div>
	</div>
<?php
if (empty($errors)) {
	$inputs = array(
		'legend' => false,
		'fieldset' => false,
		'Revision.id' => array('type' => 'hidden'),
		'Revision.node_id' => array('type' => 'hidden'),
		'Revision.under_node_id' => array('type' => 'hidden'),
		'Revision.after_node_id' => array('type' => 'hidden'),
		'Revision.lang' => array('type' => 'hidden'),
		'Revision.title' => array('type' => 'hidden'),
		'Revision.content2' => array('type' => 'hidden', 'value' => $data['Revision']['content']),
		'Revision.preview' => array('type' => 'hidden', 'value' => '0'),
		'Revision.reason',
	);
	if (isset ($data['Node']['show_in_toc'])) {
		$inputs['Node.show_in_toc'] = array('type' => 'hidden', 'value' => $data['Node']['show_in_toc']);
	}
	echo $form->create(null, array('url' => '/' .$this->params['url']['url']));
	echo $form->inputs($inputs);
	echo $form->end($confirm . ' ' . __('Save it', true));
}
?>
</fieldset>