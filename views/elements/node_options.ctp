<?php /* SVN FILE: $Id: node_options.ctp 705 2008-11-19 12:15:50Z AD7six $ */
if (isset($this->params['admin'])) {
	return;
}
extract($data);
$options = array();
if ($Node['edit_level'] <= $auth['User']['Level']) {
	if (!$Revision['id']) {
		$out[] = $html->link(__('Translate', true), array('action'=>'edit',$Node['id'], $Revision['slug']));
	} else {
		$out[] = $html->link(__('Edit', true), array('action'=>'edit',$Node['id'], $Revision['slug']));
	}
}
if ($Node['depth'] >= $viewAllLevel) {
	$out[] = $html->link(__('View just this section', true), array('action'=>'view',$Node['id'], $Revision['slug']));
}
if ($Node['comment_level'] <= $auth['User']['Level'] && $this->layout == 'default') {
	$out[] = $html->link(sprintf(__('Comments (%s)', true), count($Comment)), '#comments-' . $Node['id'], array('class' => 'show-comment'));
}
$flags = explode(',', trim($Revision['flags']));
$flagLis = '';
if (in_array($Node['id'], $pendingUpdates)) {
	$flagLis .= '<li class="flag pending">' . $html->link(__('there is a pending change for this section', true),
	array('controller' => 'changes', 'action' => 'index', $Node['id'])) . '</li>';
} {
	$out[] = $html->link(__('History', true), array('action' => 'history', $Node['id'], $Revision['slug']));
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
			array('action' => 'compare', $Node['id'], $Revision['slug'])) . '</li>';
	} else {
		$flagLis .= '<li class="flag warning">' . __($flag, true) . '</li>';
	}
}
if ($this->params['lang'] != $defaultLang && $Revision['id'] && $compare) {
	$out[] = $html->link(__('Compare to original content', true), array('action' => 'compare', $Node['id'], $Revision['slug']));
}

if ($out) {
	echo '<ul class="node-options"><li>' . implode($out, '</li><li>') . '</li>' . $flagLis . '</ul>';
}
?>