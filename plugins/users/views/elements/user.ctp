<?php if (!empty($authUser)) :?>
<div class="plugin-users">
	<span class="username">
		<?php echo $html->link($authUser['User']['username'], array('controller' => 'users', 'action' => 'view'));?>
	</span>
	<span class="logout">
		<?php echo $html->link('logout', array('controller' => 'users', 'action' => 'logout'));?>
	</span>
</div>
<?php endif;?>