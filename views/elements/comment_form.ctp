<?php
if (!isset($auth['User']['id'])) {
	echo '<div class="comment"><p class="commenttitle"><em>';
	echo $html->link(__('Login to add a comment', true), array('controller' => 'comments', 'action' => 'add'));
	echo '</em></p></div>';
	return;
}
?>
<div class="comment">
<?php
if (!isset($node)) {
	$node = $data['Node'];
}
echo $form->create('Comment',array('id' => 'CommentAddForm' . $node['Node']['id'],
	'url' => array('controller' => 'comments', 'action' => 'add', $node['Node']['id'], $node['Revision']['slug'])));
$note = $this->element('comment_form_note');
$legend = sprintf($html->tags['legend'], sprintf(__('Comment on %1$s', true), $node['Revision']['title']));
$contents = $form->inputs(array (
	'fieldset' => false,
	'title',
	'body' => array ('cols' => 100, 'rows' => 10)
));
echo sprintf($html->tags['fieldset'], '', $legend . $note . $contents);
echo $form->submit(__('add comment', true));
echo $form->end();
?>
</div>