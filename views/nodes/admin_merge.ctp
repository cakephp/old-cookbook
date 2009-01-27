<?php
if (isset($preview)) :
?>
<div id='preview' class="nodes view">
	<h2><?php echo htmlspecialchars($preview['title']) ?> </h2>
	<div class="body"><?php
		if (isset($highlight)) {
			echo $highlight->auto($preview['content']);
		} else {
			echo $preview['content'];
		}
	?></div>
</div>
<?php
endif;
echo $form->create('Node', array('url' => '/' . $this->params['url']['url']));
if (array_key_exists('merge_id', $this->data['Node'])) {
	echo $form->input('confirmation', array('type' => isset($this->data['Node']['merge_id'])?'checkbox':'hidden', 'label' => __('You are sure? Please check the preview', true), 'error' => false));
}
echo $form->inputs(array(
	'legend' => 'Merging contents',
	'id' => array('options' => $nodes, 'label' => 'Take the content from here', 'value' => $data['Node']['id']),
	'merge_id' => array('options' => $nodes, 'label' => 'And merge it with', 'value' =>
	isset($data['Node']['merge_id'])?$data['Node']['merge_id']:$data['Node']['id']),
));
echo $form->submit('merge it');
echo $form->end();
?>
<script type="text/javascript">
	url = '<?php echo Router::url('merge'); ?>';
	$('#NodeId').change(function() {
		if (typeof $(this).attr('value') != 'undefined') {
			url += '/' + $(this).attr('value');
			window.location = url;
		}
	});
</script>