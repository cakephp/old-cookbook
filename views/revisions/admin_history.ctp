<h1>History</h1>
<div class="container">
<table>
<?php
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action);
$paginator->options(array('url' => $pass));
$th = array(
	$paginator->sort('id'),
	$paginator->sort('Title', 'slug'),
	$paginator->sort('status'),
	$paginator->sort('lang'),
	$paginator->sort('User','User.username'),
	'<span>Actions</span>'
);
echo $html->tableHeaders($th);

foreach ($data as $node) {
	$actions = array();
	if ($node['Revision']['status'] == 'current') {
		$actions[] = $html->link('Hide', array('action' => 'hide', $node['Revision']['id']));
	} elseif ($node['Revision']['status'] == 'pending') {
		$actions[] = $html->link('Approve', array('action' => 'approve', $node['Revision']['id']));
	} elseif ($node['Revision']['status'] == 'previous') {
		$actions[] = $html->link('Revert', array('action' => 'approve', $node['Revision']['id']));
	}

	$actions[] = $html->link('Edit', array('action' => 'edit', $node['Revision']['id']));
	$actions[] = $html->link('Delete', array('action' => 'delete', $node['Revision']['id']));

	$tr = array (
		$node['Revision']['id'],
		$html->link($node['Revision']['title'],array('action'=>'view',$node['Revision']['id'])),
		$html->link($node['Revision']['status'], am($pass, array('page' => 1, 'status' => $node['Revision']['status']))),
		$html->link($node['Revision']['lang'], am($pass, array('page' => 1, 'lang' => $node['Revision']['lang']))),
		$node['User']?$html->link($node['User']['realname'], array('controller' => 'users', 'action' => 'view', $node['User']['id'])):'',
		implode($actions, ' - ')
	);
    echo $html->tableCells($tr);
}
?>
</table>
<?php echo $this->element('paging'); ?>
</div>