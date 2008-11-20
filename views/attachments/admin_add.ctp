<?php /* SVN FILE: $Id: admin_add.ctp 600 2008-08-07 17:55:23Z AD7six $ */
echo $form->create(null, array('id' => 'image_upload', 'type' => 'file', 'url' => '/' . $this->params['url']['url']));
$inputs = array(
	'legend' => 'Upload a file/image',
	'filename' => array ('type' => 'file'),
	'description' => array ()
);
echo $form->inputs($inputs);
echo $form->submit('upload');
echo $form->end();
?>