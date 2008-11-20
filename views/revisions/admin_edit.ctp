<?php /* SVN FILE: $Id: admin_edit.ctp 673 2008-10-06 14:05:17Z AD7six $ */ ?>
<h1>Revisions -  edit Revision </h1>
<div class="form-container">
<?php
$contents = '';
if (isset($this->data['Revision']['content'])) {
	$contents = $this->data['Revision']['content'];
	preg_match_all('@<pre[^>]*>([\\s\\S]*?)</pre>@i', $contents, $result, PREG_PATTERN_ORDER);
	if (!empty($result['0'])) {
		$count = count($result['0']);
		for($i = 0; $i < $count; $i++) {
			$replaced = str_replace('&lt;', '<',  $result['1'][$i]);
			$replaced = str_replace('&gt;', '>', $replaced);
			$contents = str_replace($result[1][$i], $replaced, $contents);
		}
	}
}
$action = in_array($this->action, array('add', 'admin_add'))?'Add':'Edit';
$action = Inflector::humanize($action);
echo $form->create();
echo $form->inputs(array(
	'legend' => false,
	'id',
	'node_id' => array('empty' => true),
	'under_node_id',
	'after_node_id',
	'status',
	'user_id' => array('empty' => true),
	'lang',
	'title',
	'content' => array('value' => $contents),
));
echo $form->end('Submit');
echo $this->element('markitup', array('process' => 'textarea'));
?></div>