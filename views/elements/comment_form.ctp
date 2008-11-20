<?php /* SVN FILE: $Id: comment_form.ctp 661 2008-09-10 14:53:56Z AD7six $ */
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
echo $form->inputs(array (
			'legend' => sprintf(__('Comment on %s', true), $node['Revision']['title']),
			  'title',
			  'body' => array ('cols' => 100, 'rows' => 10)
			 ));
echo $form->submit('save');
echo $form->end();
?>
</div>