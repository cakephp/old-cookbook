<h1>History</h1>
<?php if ($this->params['lang'] != $defaultLang) {
	$url = $this->passedArgs;
	$url[] = 1;
	echo '<p>' . $html->link(__('See English edits too', true), $url) . '</p>';
} ?>
<div class="container">
<table>
<?php
$pass = $this->passedArgs;
$paginator->options(array('url' => $pass));
$th = array(
	'Revision Id',
	'Language',
	'User',
	'Note',
	'Status',
	'Submitted'
);
$firstTranslation = true;
echo $html->tableHeaders($th);
foreach ($data as $row) {
	$defaultReason = 'edit';
	if ($row['Revision']['lang'] != $defaultLang) {
		$defaultReason = 'edit/translation';
	}
	if ($row['Revision']['status'] == 'pending') {
		$link = $row['Revision']['id'];
	} else {
		$link = $html->link($row['Revision']['id'], array('controller' => 'revisions', 'action' => 'view',
			$row['Revision']['id'], $row['Revision']['slug']));
	}
	$tr = array(
		$link,
		$row['Revision']['lang'],
		isset($users[$row['Revision']['user_id']])?$users[$row['Revision']['user_id']]:'-',
		$row['Revision']['reason']?$row['Revision']['reason']:$defaultReason,
		$row['Revision']['status'],
		$time->niceShort($row['Revision']['created'])
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>