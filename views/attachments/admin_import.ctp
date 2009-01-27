<?php
echo $form->create(null, array('type' => 'file', 'url' => '/' . $this->params['url']['url']));
echo $form->inputs(array(
	'legend' => 'Import files from another install, or restore to a previous backup',
	'file' => array('type' => 'file', 'label' => 'XML file to import'),
	'backup' => array('options' => $backups, 'label' => 'Or choose a previous backup', 'empty' => true),
	'take_backup' => array('type' => 'checkbox', 'checked' => 'checked', 'label' => 'Backup current attachments before import?'),
));
echo $form->submit();
echo $form->end();
?>