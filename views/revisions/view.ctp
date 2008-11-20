<div class="nodes view">
<?php
$compare = array();
foreach ($data as $key => $row) {
	extract($row);
	echo '<h2>{' . up($Revision['id']) . '} - ' . $Node['sequence'] . ' ' . htmlspecialchars($Revision['title']) . '</h2>';
	//echo $html->clean($currentNode['Revision']['content']);
	echo '<div class="summary">' . $Revision['content'] . '</div>';
	$compare[] = '<title>' . $Revision['title'] . "</title>\r\n" . $Revision['content'];
}
if (count($compare) > 1) {
	echo '<h2>' . __('Differences', true) . '</h2>';
	echo $diff->compare(htmlspecialchars($compare[0]), htmlspecialchars($compare[1]));
}
?>
</div>