<?php /* SVN FILE: $Id: admin_change_status.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<h1><?php echo str_replace('admin_', '', $this->action) ?> Revision</h1>
<?php
echo $form->create(null, array('url' => '/' . $this->params['url']['url']));
$fields = array(
	'legend' => false,
	'Revision.reason' => array('label' => 'Public Log message')
);
if (isset($isSignificant) && $isSignificant) {
	$fields['is_significant'] = array('type' => 'radio', 'options' => array(1 => 'yes', 0 => 'no'),
	'legend' => 'Should translations be marked for review?');

}
echo $form->inputs($fields);
echo $form->submit();
echo $form->end();