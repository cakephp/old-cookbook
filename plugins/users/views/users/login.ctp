<div class="login">
<?php
	echo $form->create('User', array('action' => 'login'));
	echo $form->hidden('redirect', array('value' => $session->read('Auth.redirect')));
	$after = '<p>' . $html->link(__('Forgot your password?', true), 'http://bakery.cakephp.org/users/reset') . '</p>';
	echo $form->inputs(array(
		'legend' => 'Login',
		'username',
		'psword' => array('label' => __('Password', true), 'value' => '', 'after' => $after),
		'remember_me' => array('label' => __('Remember me', true), 'type' => 'checkbox')
	));
	echo $form->end(__('Login', true));
?>
</div>