<?php
if (isset($this->params['admin'])) {
	return;
}
$options = array();
if ($data['Node']['edit_level'] <= $auth['User']['Level']) {
	if (!$data['Revision']['id']) {
		$out[] = $html->link(__('Translate', true), array('action'=>'edit',$data['Node']['id'], $data['Revision']['slug']), array('title' =>
			__('There is no translation for this section please submit one', true), 'class' => 'contribute'));
	} else {
		$out[] = $html->link(__('Edit', true), array('action'=>'edit',$data['Node']['id'], $data['Revision']['slug']), array('title' =>
			__('Don\'t like this text? Submit your thoughts', true), 'class' => 'contribute'));
	}
}
if ($data['Node']['depth'] >= $viewAllLevel) {
	$out[] = $html->link(__('View just this section', true), array('action'=>'view',$data['Node']['id'], $data['Revision']['slug']), array('class'
	=> 'dialog'));
}
if ($data['Node']['comment_level'] <= $auth['User']['Level'] && $this->layout == 'default') {
	$out[] = $html->link(sprintf(__('Comments (%s)', true), count($data['Comment'])), array('controller' => 'comments', 'action' => 'index', $data['Node']['id']), array('class' => 'dialog'));
}
$flags = explode(',', trim($data['Revision']['flags']));
$flagLis = '';
if (in_array($data['Node']['id'], $pendingUpdates)) {
	$flagLis .= '<li class="flag pending">' . $html->link(__('there is a pending change for this section', true),
	array('controller' => 'changes', 'action' => 'index', $data['Node']['id']), array('class' => 'dialog')) . '</li>';
} {
	$out[] = $html->link(__('History', true), array('action' => 'history', $data['Node']['id'], $data['Revision']['slug']), array('class' => 'dialog'));
}
$compare = true;
foreach($flags as $flag) {
	if (trim($flag) == '') {
		continue;
	}
	if ($flag == 'englishChanged') {
		$compare = false;
		$flagLis .= '<li class="flag englishChanged">' .
			$html->link(__('This text may be out of sync with the English version', true),
			array('action' => 'compare', $data['Node']['id'], $data['Revision']['slug']), array('class' => 'dialog')) . '</li>';
	} else {
		$flagLis .= '<li class="flag warning">' . __($flag, true) . '</li>';
	}
}
if ($this->params['lang'] != $defaultLang && $data['Revision']['id'] && $compare) {
	$out[] = $html->link(__('Compare to original content', true), array('action' => 'compare', $data['Node']['id'], $data['Revision']['slug']), array('class' => 'dialog'));
}

if ($out) {
	echo '<ul class="node-options"><li>' . implode($out, '</li><li>') . '</li>' . $flagLis . '</ul>';
}
?>