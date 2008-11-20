<?php /* SVN FILE: $Id: reset.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<?php
	echo $form->create('User', array('action' => 'reset'));
	echo $form->inputs(array('email','legend' => 'Reset Password'));
	echo $form->end('Submit');
?>