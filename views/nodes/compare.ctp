<div class="nodes view">
<?php
$compare = array();
foreach ($data as $key => $row) {
	echo '<h2>{' . up($row['Revision']['lang']) . '} - ' . $row['Node']['sequence'] . ' ' . htmlspecialchars($row['Revision']['title']) . '</h2>';
	//echo $html->clean($currentNode['Revision']['content']);
	echo '<div class="summary">' . $row['Revision']['content'] . '</div>';
	$compare[$key] = '<title>' . $row['Revision']['title'] . "</title>\r\n" . $row['Revision']['content'];
}
echo '<h2>' . __('Differences', true) . '</h2>';
echo $diff->compare(htmlspecialchars($compare['compare']), htmlspecialchars($compare['original']));
?>
</div>