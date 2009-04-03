<div class="login">
<?php
	echo $form->create('User', array('action' => 'login'));
	echo $form->hidden('redirect', array('value' => $session->read('Auth.redirect')));
	$after = '<p>' . $html->link(__('Forgot your password?', true), 'http://bakery.cakephp.org') . '</p>';
	echo $form->inputs(array(
		'legend' => 'Login',
		'username',
		'psword' => array('label' => __('Password', true), 'value' => '', 'after' => $after),
		'remember_me' => array('label' => __('Remember me', true),
			'type' => 'checkbox', 'after' => '<p>' . __('for 2 weeks unless I sign out.', true) . '</p>'),
	));
	echo $form->end(__('Login', true));
?>
</div>