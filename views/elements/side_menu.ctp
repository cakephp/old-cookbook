<?php

$sections = $menu->sections();
if (!$this->name && !empty($this->data['Node'])) {
	// rendered from cache
	include CONFIGS . 'routes.php';
	extract ($this->data['Node']);
	$slug = !empty($this->params['pass'][1])?$this->params['pass'][1]:null;
	if ($depth > 1 && $this->action !== 'complete') {
		$menu->add(array(
			'section' => 'Options',
			'title' => __('All in one page', true),
			'url' => array('controller' => 'nodes', 'action' => 'complete', $id, $slug)
		));
	}
        if (!$authLevel = $session->read('Auth.User.Level')) {
         $authLevel = 200;
        }
	if ($edit_level <= $authLevel) {
		$menu->add(array(
			'section' => 'Options',
			'title' => __('Suggest a new section here', true),
			'url' => array('controller' => 'nodes', 'action' => 'add', $id, $slug)
		));
	}
	$sections = $menu->sections();
}
foreach ($sections as $section) {
	echo '<div class="context-menu ' . low($section). '"><h4>' . __($section, true) . '</h4>';
	echo $menu->generate($section);
	echo '</div>';
}
?>