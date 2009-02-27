<div class="nodes form">
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
echo $this->element('attachments', array('path' => IMAGES . 'Node/' . $this->data['Revision']['node_id']));
if ($session->read('Auth.User.Level') == ADMIN && $this->action == 'edit') {
	$menu->addm('Admin', array(
		array('title' => 'Edit Node Properties', 'url' => array('admin' => true, 'action' => 'edit', $this->data['Revision']['node_id'])),
		array('title' => 'TOC', 'url' => array('admin' => true, 'action' => 'toc', $this->data['Revision']['node_id'])),
		array('title' => 'Merge', 'url' => array('admin' => true, 'action' => 'merge', $this->data['Revision']['node_id'])),
		array('title' => 'Upload Image/File', 'url' => array('admin' => true, 'controller' => 'attachments', 'action' => 'add', 'Node', $this->data['Revision']['node_id'])),
	));
}
echo $this->element('preview');
echo $form->create(null, array('url' => '/' . $this->params['url']['url']));
$inputs = array (
	'fieldset' => false,
	'Revision.node_id' => array('type' => 'hidden'),
	'Revision.preview' => array('type' => 'checkbox', 'label' => __('Show me a preview before submitting', true), 'error' => false),
	'Revision.title',
	'Revision.content' => array (
		'label' => __('Contents. Code in pre tags will be escaped. Submissions with no html formatting will be formatted automatically', true),
		'cols' => 100,
		'rows' => 30,
		'value' => $contents
	),
	'Revision.reason' => array('label' => __('What is the reason for the edit? (In English Please) :)', true)),
);
if ($session->read('Auth.User.Level') == ADMIN) {
	$inputs = am(array('Node.show_in_toc' => array('type' => 'checkbox')), $inputs);
}
$note = $this->element('content_form_note');
$legend = sprintf($html->tags['legend'], sprintf(__('Edit %s', true), $this->data['Revision']['title']));
$contents = $form->inputs($inputs);
echo sprintf($html->tags['fieldset'], '', $legend . $note . $contents);

echo $form->submit('save');
echo $form->end();
?>
</div>
<?php
$menu->settings(__('Resources', true), array('class' => 'dialogs'));
$lang = Configure::read('Languages.default');
$menu->add(array(
	array('section' => __('Resources', true), 'title' => __('Current Version', true), 'url' => array('action' => 'view',
		$this->data['Node']['id'], $contentSlugs[$this->data['Revision']['lang']]))
));
$menu->add(array(
	array('title' => __('History', true), 'url' => array('action' => 'history',
		$this->data['Node']['id'], $contentSlugs[$this->data['Revision']['lang']]))
));
if ($data['Revision']['lang'] != $lang) {
	$menu->add(array(
		array('title' => __('English Version', true), 'url' => array('action' => 'view',
			'lang' => $lang, $this->data['Node']['id'], $contentSlugs[$lang]))
	));
	$menu->add(array(
		array('title' => __('Compare to English', true), 'url' => array('action' => 'compare',
			$this->data['Node']['id'], $contentSlugs[$lang]))
	));
	$menu->add(array(
		array('title' => __('English History', true), 'url' => array('action' => 'history',
			'lang' => $lang, $this->data['Node']['id'], $contentSlugs[$lang]))
	));
}

echo $this->element('markitup', array('process' => 'textarea'));
?>