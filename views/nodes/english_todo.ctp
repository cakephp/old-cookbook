<div class="nodes view">
<div class="summary">
<p>These sections have been marked as needing updating.</p>
</div>
<?php
foreach ($data as $id => $row) {
	extract ($row);
	$sequence = $Node['sequence'];
	$sequence = $sequence?$sequence:'#';
	echo "<h2 id=\"{$Revision['slug']}-{$Node['id']}\">" .
		$html->link($sequence, array('action' => 'view', $Node['id'], $Revision['slug'])) . ' ' . htmlspecialchars($Revision['title']) . "</h2>";

	echo '<div class="options">';
		echo $this->element('node_options', array('data' => $row));
	echo '</div>';
}
?>
</div>
<?php echo $this->element('paging');