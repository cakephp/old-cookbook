<?php
if ($this->name == 'Users') {
	echo $this->element('login_hint');
}
$sections = $menu->sections();
if (!$this->name && !empty($this->data['Node'])) {
	// rendered from cache
	include CONFIGS . 'routes.php';
	extract ($this->data['Node']);
	$slug = !empty($this->params['pass'][1])?$this->params['pass'][1]:null;
	if ($depth > 1) {
		$menu->add(array(
			'section' => 'Options',
			'title' => __('All in one page', true),
			'url' => array('controller' => 'nodes', 'action' => 'complete', $id, $slug)
		));
	}
	$authLevel = $session->read('Auth.User.Level');
	$authLevel = $authLevel?$authLevel:200;
	if ($edit_level <= $authLevel) {
		$menu->add(array(
			'section' => 'Options',
			'title' => __('Suggest a new section here', true),
			'url' => array('controller' => 'nodes', 'action' => 'add', $id, $slug)
		));
	}
	$sections = $menu->sections();
}
if (in_array('Options', $sections)) {
	echo '<div class="context-menu"><h4>' . __('Options', true) . '</h4>';
	echo $menu->generate('Options');
	echo '</div>';
	$keys = array_flip($sections);
	unset ($sections[$keys['Options']]);
}
foreach ($sections as $section) {
	echo '<div class="context-menu ' . $section . '"><h4>' . $section . '</h4>';
	$menu->settings($section, array('class' => $section));
	echo $menu->generate($section);
	echo '</div>';
}
?>