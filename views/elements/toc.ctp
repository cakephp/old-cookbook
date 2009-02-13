<div id="toc" class="context-menu toc tree">
<?php
$url = array('plugin' => null, 'prefix' => null, 'controller' => 'nodes', 'action' => 'toc', $data['Node']['Node']['id'], 'lang' => $this->params['lang']);
//$url = str_replace($this->base, '', Router::url($url));
$__cache = Configure::read('Cache.check');
Configure::write('Cache.check', false);
$data = $this->requestAction($url, array('currentPath' => $currentPath, 'currentNode' => $currentNode, 'complete' => $this->action=='complete'));
Configure::write('Cache.check', $__cache);
$i = false;
$title = '';
if (isset($currentPath[2])) {
	$i = 2;
	$title .= $html->link(__('Table of Contents', true), array(
		'action' => 'toc', $currentPath[2]['Node']['id'], $currentPath[2]['Revision']['slug'],
	), array('title' => __('see fully expanded table of contents (only)', true))) . ' : ';
} elseif (isset($currentPath[1])) {
	$i = 1;
	$title .= __('Books in ', true);
} else {
	$title .= __('Available collections', true);
}
if ($i) {
	$title .= $html->link($currentPath[$i]['Revision']['title'],
		array('controller' => 'nodes', 'action' => 'view', $currentPath[$i]['Node']['id'], $currentPath[$i]['Revision']['slug']));
}
echo '<h4>' . $title . '</h4>';
$selected = array();
if (isset($currentNode['lft'])) {
	$selected = array($currentNode['lft'],	$currentNode['rght']);
}
echo $tree->generate($data, array ('element' => 'toc/public_item', 'model' => 'Node', 'selected' => $selected));
?>
</div>