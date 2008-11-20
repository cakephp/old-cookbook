<?php /* SVN FILE: $Id: view_all.ctp 706 2008-11-19 12:16:42Z AD7six $ */
if ($this->params['isAjax']) {
	echo $this->element('crumbs');

	if($session->check('Message.auth')):
		$session->flash('auth');
	endif;

	if($session->check('Message.flash')):
		$session->flash();
	endif;
}
?>
<div class="nodes view">
<?php
	$currentNode = current($data);
	$this->set('currentNode', $currentNode['Node']);
	$attributes = '';
	echo "<h2 id= \"{$currentNode['Revision']['slug']}-{$currentNode['Node']['id']}\">" .
		$currentNode['Node']['sequence'] . ' ' . htmlspecialchars($currentNode['Revision']['title']) . "</h2>";

	echo '<div class="options">';
		echo $this->element('node_options', array('data' => $currentNode));
	echo '</div>';
	if (trim(html_entity_decode(strip_tags(str_replace('&nbsp;', '', $currentNode['Revision']['content'])))) != '') {
		echo '<div class="summary">';
			// TODO Identify why this is problematic
			//echo $html->clean($currentNode['Revision']['content']);
			echo $currentNode['Revision']['content'];
		echo '</div>';
		echo $html->meta(
			'description',
			$text->truncate(strip_tags($currentNode['Revision']['content']), 150),
			array(),
			false
		);
	}
	echo '<div class="comments" id="comments-' . $currentNode['Node']['id'] . '">';
	echo '<div class="comment">';
	echo $html->link(__('See comments for this section', true), array('controller' => 'comments', 'action' => 'index', $currentNode['Node']['id']));
	echo '</div></div>';

	array_shift ($data);
	foreach ($data as $id => $row) {
		extract ($row);
		$level = 2 - $currentNode['Node']['depth'] + $Node['depth'];
		$level = min ($level, 6);

		$sequence = $Node['sequence'];
		$sequence = $sequence?$sequence:'#';
		echo "<h$level id=\"{$Revision['slug']}-{$Node['id']}\">" .
			$html->link($sequence, '#' . $Revision['slug'] . '-' . $Node['id']) . ' ' . htmlspecialchars($Revision['title']) . "</h$level>";

		echo '<div class="options">';
			echo $this->element('node_options', array('data' => $row));
		echo '</div>';

		if (trim(html_entity_decode(strip_tags(str_replace('&nbsp;', '', $Revision['content'])))) != '') {
			echo '<div class="body">';
				// TODO Identify why this is problematic
				//echo $html->clean($Revision['content']);
				echo $Revision['content'];
			echo '</div>';
		}
		echo '<div class="comments" id="comments-' . $Node['id'] . '">';
		echo '<div class="comment">';
		echo $html->link(__('See comments for this section', true), array('controller' => 'comments', 'action' => 'index', $Node['id']));
		echo '</div></div>';
	}
?>
</div>
<?php echo $this->element('node_navigation');
if (isset($this->params['admin'])) {
	extract($currentPath[count($currentPath)-1]);
	$menu->add(array(
		'section' => 'Options',
		'title' => 'History',
		'url' => array('controller' => 'revisions', 'action' => 'history', $currentNode['Node']['id'], 'lang:' . $this->params['lang'])
	));
	$menu->add(array(
		'section' => 'Options',
		'title' => 'Comments',
		'url' => array('controller' => 'comments', 'action' => 'index', $currentNode['Node']['id'], 'lang:' . $this->params['lang'])
	));
	$menu->add(array(
		'section' => 'Options',
		'title' => 'Move around',
		'url' => array('action' => 'toc', $currentNode['Node']['id'])
	));

}
if ($currentNode['Node']['depth'] > 1 && $currentNode['Node']['depth'] < $viewAllLevel) {
	$menu->add(array(
		'section' => 'Options',
		'title' => __('All in one page', true),
		'url' => array('action' => 'single_page', $currentNode['Node']['id'], $currentNode['Revision']['slug'])
	));
}
if ($currentNode['Node']['edit_level'] <= $auth['User']['Level']) {
	$menu->add(array(
		'section' => 'Options',
		'title' => __('Suggest a new section here', true),
		'url' => array('admin' => false, 'action' => 'add', $currentNode['Node']['id'], $currentNode['Revision']['slug'])
	));
}
extract($this->data);
$html->meta(
	'rss',
	array('plugin' => false, 'controller' => 'comments', 'action' => 'index', $Node['id'], $Revision['slug'], 'ext' => 'rss'),
	array('title' => sprintf(__('Comments for %s', true), $Revision['title']))
	, false);
$html->meta('rss',
	array('plugin' => false, 'controller' => 'changes', 'action' => 'index', $Node['id'], 'ext' => 'rss'),
	array('title' => sprintf(__('Change history for %s', true), $Revision['title']))
	, false);
?><cake:nocache>     <?php
extract($this->data);
$menu->add(array(
	'section' => 'Feeds',
	'title' => sprintf(__('Comments for %s', true), $Revision['title']),
	'url' => array('plugin' => false, 'controller' => 'comments', 'action' => 'index', $Node['id'], $Revision['slug'], 'ext' => 'rss'),
));

$menu->add(array(
	'section' => 'Feeds',
	'title' => sprintf(__('Change history for %s', true), $Revision['title']),
	'url' => array('plugin' => false, 'controller' => 'changes', 'action' => 'index', $Node['id'], 'ext' => 'rss'),
));
?></cake:nocache>