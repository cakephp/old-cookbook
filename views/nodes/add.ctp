<div class="nodes add ajaxFormContainer">
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
echo $this->element('preview');
echo $form->create(null, array('url' => '/' .$this->params['url']['url']));
$inputs['fieldset'] = false;

if ($session->read('Auth.User.Level') == ADMIN) {
	$inputs['Node.show_in_toc'] = array('type' => 'checkbox');
}

$inputs['Revision.preview'] = array('type'=>'checkbox', 'label' => __('Show me a preview before submitting', true), 'error' => false);

$inputs['Revision.under_node_id'] = array('label'=> __('under', true), 'options' => $parents);

if (isset($afters)) {
	$inputs['Revision.after_node_id'] = array('label'=> __('after', true), 'options' => $afters);
}

$inputs[] = 'Revision.title';

$inputs['Revision.content'] = array(
	'label' => __('Contents. Code in pre tags will be escaped. Submissions with no html formatting will be formatted automatically', true),
	'cols' => 100, 'rows' => 30,
	'value' => $contents
);

$inputs['Revision.reason'] = array('label' => __('Optionally explain in brief why you are proposing this addition (In English Please) :)', true));


$note = $this->element('content_form_note');
$legend = sprintf($html->tags['legend'], __('Add a new section', true));
$contents = $form->inputs($inputs);
echo sprintf($html->tags['fieldset'], '', $legend . $note . $contents);

echo $form->end('save');
?>
</div>
<?php echo $this->element('markitup', array('process' => 'textarea')); ?>