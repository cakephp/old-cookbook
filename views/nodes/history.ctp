<?php /* SVN FILE: $Id: history.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
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
	extract($row);
	$defaultReason = 'edit';
	if ($Revision['lang'] != $defaultLang) {
		$defaultReason = 'edit/translation';
	}
	if ($Revision['status'] == 'pending') {
		$link = $Revision['id'];
	} else {
		$link = $html->link($Revision['id'], array('controller' => 'revisions', 'action' => 'view', $Revision['id']));
	}
	$tr = array(
		$link,
		$Revision['lang'],
		isset($users[$Revision['user_id']])?$users[$Revision['user_id']]:'-',
		$Revision['reason']?$Revision['reason']:$defaultReason,
		$Revision['status'],
		$time->niceShort($Revision['created'])
	);
	echo $html->tableCells($tr, array('class' => 'odd'), array('class' => 'even'));
}
?>
</table>
<?php echo $this->element('paging'); ?></div>