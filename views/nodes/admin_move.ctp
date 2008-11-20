<?php /* SVN FILE: $Id: admin_move.ctp 600 2008-08-07 17:55:23Z AD7six $ */
echo $form->create('Node', array('url' => '/' . $this->params['url']['url']));
echo $form->inputs(array(
	'id' => array('options' => $nodes, 'label' => 'Move this', 'value' => $data['Node']['id']),
	'parent_id' => array('options' => $nodes, 'label' => 'Under this'),
));
echo $form->submit('move it');
echo $form->end();
?>
<script type="text/javascript">
	url = '<?php echo Router::url('move'); ?>';
	$('#NodeId').change(function() {
		if (typeof $(this).attr('value') != 'undefined') {
			url += '/' + $(this).attr('value');
			window.location = url;
		}
	});
</script>