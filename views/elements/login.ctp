<div class="login">
<?php
	echo $form->create('User', array('action' => 'login'));
	echo $form->hidden('redirect', array('value' => $session->read('Auth.redirect')));
?>
	<fieldset>
		<legend><?php __('Login') ?></legend>
<?php
		echo $form->input('username');
		$after = '<p>' . $html->link(__('Forgot your password?', true), array('admin'=> false, 'action' => 'reset')) .'</p>';
		echo $form->input('password', array('after' => $after));
?>
	</fieldset>
<?php
	echo $form->end(__('Login', true));
?>
</div>