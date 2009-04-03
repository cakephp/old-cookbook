<div class="nodes view">
<?php
$compare = array();
foreach ($data as $key => $row) {
	echo '<h2>{' . up($row['Revision']['id']) . '} - ' . $row['Node']['sequence'] . ' ' . h($row['Revision']['title']) . '</h2>';
	//echo $html->clean($currentNode['Revision']['content']);
	echo '<div class="summary">' . $row['Revision']['content'] . '</div>';
	$compare[] = '<title>' . $row['Revision']['title'] . "</title>\r\n" . $row['Revision']['content'];
}
if (count($compare) > 1) {
	echo '<h2>' . __('Differences', true) . '</h2>';
	echo $diff->compare(h($compare[0]), h($compare[1]));
}
?>
</div>