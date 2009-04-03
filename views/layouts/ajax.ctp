<?php /* SVN FILE: $Id: ajax.ctp 600 2008-08-07 17:55:23Z AD7six $ */
if($session->check('Message.auth')):
	$session->flash('auth');
endif;
if($session->check('Message.flash')):
	$session->flash();
endif;
echo $content_for_layout;
//echo $miJavascript->link();
?>