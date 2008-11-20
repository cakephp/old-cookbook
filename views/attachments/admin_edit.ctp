<?php /* SVN FILE: $Id: admin_edit.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<?php
$action = in_array($this->action, array('add', 'admin_add'))?'Add':'Edit';
$action = Inflector::humanize($action);
echo $form->create();
echo $form->inputs(array(
	'legend' => $action . ' Image',
));
echo $form->end('Submit');
?>