<?php /* SVN FILE: $Id: secondary_nav.ctp 705 2008-11-19 12:15:50Z AD7six $ */ ?>
<div id="secondary_nav">
<ul class="navigation">
		<?php
		if ($session->read('Auth.User.id')) {
			echo '<li>' . $html->link(sprintf(__('Logged in as %s', true), $session->read('Auth.User.username')), '#', array('id' => 'loggedInAs')) . '</li>';
			echo '<li>' . $html->link(__('Logout', true), array('controller' => 'users', 'action' => 'logout')) . '</li>';
			if ($session->read('Auth.User.Level') === ADMIN) {
				echo '<li>' . $html->link('Admin', '/admin') . '</li>';
			}
		} else {
			echo '<li>' . $html->link(__('Login', true), '/users/login') . '</li>';
		}
		?>
		<li><?php
			if ($this->params['lang'] != 'en') {
				$stats = '/' . $this->params['lang'] . '/stats#' . $this->params['lang'];
			} else {
				$stats = '/stats';
			}
			echo $html->link(__('Top Contributors', true), $stats);
		?></li>
		<li><?php
			$todo = '/todo';
			if ($this->params['lang'] != 'en') {
				$todo = '/' . $this->params['lang'] . $todo;
			}
			echo $html->link(__('Todo', true), $todo);
		?></li>
		<li><a href="http://cakephp.org/"><?php __('About CakePHP') ?></a></li>
		<li><a href="http://cakefoundation.org/pages/donations"><?php __('Donate') ?></a></li>
	</ul>
</div>