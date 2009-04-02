<div class="node-nav">
<?php
$prevUrl = $nextUrl = false;
if (isset($neighbours[0]['Node']['depth'])) {
		if ($this->name == 'Revisions') {
			$prevUrl = array('action' => 'view', $neighbours[0]['Revision']['id']);
		} else {
			$prevUrl = array('action' => 'view', $neighbours[0]['Node']['id'], $neighbours[0]['Revision']['slug']);
		}
		$prevTitle = '« ' . $neighbours[0]['Revision']['title'];
		$prevOptions = array(
		'title' => $neighbours[0]['Revision']['title']
		);

	if($prevUrl) {
		echo '<span class="prev">';
			echo $html->link($prevTitle, $prevUrl, $prevOptions);
		echo '</span>';
	}
}
if (isset($neighbours[0]['Node']['depth']) && isset($neighbours[1]['Node']['depth'])) {
	echo  '&nbsp;|&nbsp;';
}
if (isset($neighbours[1]['Node']['depth'])) {
	if ($this->name == 'Revisions') {
		$nextUrl = array('action' => 'view', $neighbours[1]['Revision']['id']);
	} else {
		$nextUrl = array('action' => 'view', $neighbours[1]['Node']['id'], $neighbours[1]['Revision']['slug']);
	}
	$nextTitle = $neighbours[1]['Revision']['title']. ' »';
	$nextOptions = array('title' => $neighbours[1]['Revision']['title']);
	if ($nextUrl) {
		echo '<span class="next">';
		echo $html->link($nextTitle, $nextUrl, $nextOptions);
		echo '</span>';
	}
}
?>
</div>