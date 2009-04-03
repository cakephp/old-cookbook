<div class="nodes view">
<div class="summary">
<p>These sections have been marked as needing updating.</p>
</div>
<?php
foreach ($data as $id => $row) {
	$sequence = $row['Node']['sequence'];
	$sequence = $sequence?$sequence:'#';
	echo "<h2 id=\"{$row['Revision']['slug']}-{$row['Node']['id']}\">" .
		$html->link($sequence, array('action' => 'view', $row['Node']['id'], $row['Revision']['slug'])) . ' ' . h($row['Revision']['title']) . "</h2>";

	echo '<div class="options">';
		echo $this->element('node_options', array('data' => $row));
	echo '</div>';
}
?>
</div>
<?php echo $this->element('paging');