<?php
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
		$currentNode['Node']['sequence'] . ' ' . h($currentNode['Revision']['title']) . "</h2>";

	echo '<div class="options">';
		echo $this->element('node_options', array('data' => $currentNode));
	echo '</div>';
	if ($directChildren) {
		echo $this->element('inline_toc');
	}
	if (trim(html_entity_decode(strip_tags(str_replace('&nbsp;', '', $currentNode['Revision']['content'])))) != '') {
		echo '<div class="summary">';
			if (!$data['Node']['Revision']['id']) {
				echo '<p class="note contribute">' .
				sprintf(__('There is no translation yet for this section. You can %1$s.', true),
					$html->link(
						__('translate this' , true),
						array('action'=>'edit',$data['Node']['Node']['id'], $data['Node']['Revision']['slug']),
						array('title' => __('There is no translation for this section please submit one', true))
					)
				) . '</p>';
			} else {
				$out[] = $html->link(__('Edit', true), array('action'=>'edit',$data['Node']['id'], $data['Revision']['slug']), array('title' =>
					__('Don\'t like this text? Submit your thoughts', true), 'class' => 'contribute'));
			}

			echo $theme->out($currentNode['Revision']['content']);
		echo '</div>';
		echo $html->meta(
			'description',
			$text->truncate(strip_tags($currentNode['Revision']['content']), 150),
			array(),
			false
		);
	}

	array_shift ($data);
	foreach ($data as $id => $row) {
		$level = 2 - $currentNode['Node']['depth'] + $row['Node']['depth'];
		$level = min ($level, 6);

		$sequence = $row['Node']['sequence'];
		$sequence = $sequence?$sequence:'#';
		echo "<h$level id=\"{$row['Revision']['slug']}-{$row['Node']['id']}\">" .
			$html->link($sequence, '#' . $row['Revision']['slug'] . '-' . $row['Node']['id']) . ' ' . h($row['Revision']['title']) . "</h$level>";

		echo '<div class="options">';
			echo $this->element('node_options', array('data' => $row));
		echo '</div>';

		if (trim(html_entity_decode(strip_tags(str_replace('&nbsp;', '', $row['Revision']['content'])))) != '') {
			echo '<div class="body">';
				if (!$row['Revision']['id']) {
					echo '<p class="note contribute">' .
					sprintf(__('There is no translation yet for this section. You can %1$s.', true),
						$html->link(
							__('translate this' , true),
							array('action'=>'edit',$row['Node']['id'], $row['Revision']['slug']),
							array('title' => __('There is no translation for this section please submit one', true))
						)
					) . '</p>';
				}
				echo $theme->out($row['Revision']['content']);
			echo '</div>';
		}
		echo '<div class="comments" id="comments-' . $row['Node']['id'] . '">';
		echo '<div class="comment">';
		echo $html->link(__('See comments for this section', true), array('controller' => 'comments', 'action' => 'index', $row['Node']['id']));
		echo '</div></div>';
	}
?>
</div>
<?php echo $this->element('node_navigation');
if (isset($this->params['admin'])) {
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
		'url' => array('action' => 'complete', $currentNode['Node']['id'], $currentNode['Revision']['slug'])
	));
}
if ($currentNode['Node']['edit_level'] <= $auth['User']['Level']) {
	$menu->add(array(
		'section' => 'Options',
		'title' => __('Suggest a new section here', true),
		'url' => array('admin' => false, 'action' => 'add', $currentNode['Node']['id'], $currentNode['Revision']['slug'])
	));
}
$html->meta(
	'rss',
	array('theme' => 'default', 'plugin' => null, 'controller' => 'comments', 'action' => 'index',
		$this->data['Node']['id'], $this->data['Revision']['slug'], 'ext' => 'rss'),
	array('title' => sprintf(__('Comments for %1$s', true), $this->data['Revision']['title']))
	, false);
$html->meta('rss',
	array('theme' => 'default', 'plugin' => null, 'controller' => 'changes', 'action' => 'index',
		$this->data['Node']['id'], 'ext' => 'rss'),
	array('title' => sprintf(__('Change history for %1$s', true), $this->data['Revision']['title']))
	, false);
?><cake:nocache>     <?php
$menu->add(array(
	'section' => 'Feeds',
	'title' => sprintf(__('Comments for %1$s', true), $this->data['Revision']['title']),
	'url' => array('theme' => 'default', 'plugin' => null, 'controller' => 'comments', 'action' => 'index',
		$this->data['Node']['id'], $this->data['Revision']['slug'], 'ext' => 'rss'),
));

$menu->add(array(
	'section' => 'Feeds',
	'title' => sprintf(__('Change history for %1$s', true), $this->data['Revision']['title']),
	'url' => array('theme' => 'default', 'plugin' => null, 'controller' => 'changes', 'action' => 'index',
		$this->data['Node']['id'], 'ext' => 'rss'),
));
?></cake:nocache>