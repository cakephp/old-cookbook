<h1><?php echo str_replace('admin_', '', $this->action) ?> Revision</h1>
<?php
echo $form->create(null, array('url' => '/' . $this->params['url']['url']));
$fields = array(
	'legend' => false,
	'Revision.reason' => array('label' => 'Public Log message')
);
if (isset($isSignificant) && $isSignificant) {
	$fields['is_significant'] = array('type' => 'radio',
		'default' => 1,
		'options' => array(1 => 'yes', 0 => 'no'),
		'legend' => 'Should translations be marked for review?',
		'class' => 'radio'
	);

}
echo $form->inputs($fields);
echo $form->submit();
echo $form->end();