<?php /* SVN FILE: $Id: add.ctp 672 2008-10-06 14:03:23Z AD7six $ */ ?>
<div class="nodes add">
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
echo $html->link(__('Please review the guidelines for submitting to the Cookbook to ensure consistency.', true),
	array('controller' => 'nodes', 'action' => 'view', 482, 'contributing-to-the-cookbook'));
echo $this->element('preview');
echo $form->create(null, array('url' => '/' .$this->params['url']['url']));

	$inputs['legend'] = __('Add a new section', true);

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

	echo $form->inputs($inputs);

echo $form->end('save');
?>
</div>
<?php echo $this->element('markitup', array('process' => 'textarea')); ?>