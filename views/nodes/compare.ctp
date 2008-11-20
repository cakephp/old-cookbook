<div class="nodes view">
<?php
$compare = array();
foreach ($data as $key => $row) {
	extract ($row);
	echo '<h2>{' . up($Revision['lang']) . '} - ' . $Node['sequence'] . ' ' . htmlspecialchars($Revision['title']) . '</h2>';
	//echo $html->clean($currentNode['Revision']['content']);
	echo '<div class="summary">' . $Revision['content'] . '</div>';
	$compare[$key] = '<title>' . $Revision['title'] . "</title>\r\n" . $Revision['content'];
}
echo '<h2>' . __('Differences', true) . '</h2>';
echo $diff->compare(htmlspecialchars($compare['compare']), htmlspecialchars($compare['original']));
?>
</div>