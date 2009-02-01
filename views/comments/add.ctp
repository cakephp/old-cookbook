<div class="comment">
<?php
echo $form->create('Comment',array('url' => '/' . $this->params['url']['url']));
echo $form->inputs(array (
	'legend' => sprintf(__('Comment: %s', true), $node['Revision']['title']),
	'parent_id' => array('type' => 'hidden'),
	'title',
	'body' => array ('cols' => 100, 'rows' => 10)
));
echo $form->submit('save');
echo $form->end();
?>
</div>