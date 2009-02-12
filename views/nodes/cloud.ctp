<?php
$html->css('full_width', null, array(), false);
echo '<h2>' . $book['Revision']['title'] . '</h2>';
//echo '<h3>' . __('Table of Contents', true) . '</h3>'
echo '<div class="column"><ul>';
$depth = $data[0]['Node']['depth'];
$count = 0;
foreach ($data as $row) {
	$count++;
	foreach ($row['children'] as $row) {
		$count++;
	}
}
$split = $count / 4;
$counter = 0;
foreach ($data as $row) {
	if ($counter >= $split) {
		$counter = 0;
		echo '</ul></div><div class="column"><ul>';
	}
	$counter++;
	echo '<li>';
	echo $html->link($row['Node']['sequence'] . ' ' . $row['Revision']['title'],
		array('action'=>'view', $row['Node']['id'], $row['Revision']['slug']));
	$close = true;
	if ($row['children']) {
		foreach ($row['children'] as $i => $row) {
			if (!$i) {
				if ($counter >= $split) {
					$close = false;
					echo '</li>';
					echo '</ul></div><div class="column"><ul><li><ul>';
					$counter = 0;
				} else {
					echo '<ul>';
				}
			} else {
				if ($counter >= $split) {
					echo '</ul></ul></div><div class="column"><ul><li><ul>';
					$counter = 0;
				}
			}
			$counter++;
			echo '<li>';
			echo $html->link($row['Node']['sequence'] . ' ' . $row['Revision']['title'],
				array('action'=>'view', $row['Node']['id'], $row['Revision']['slug']));
			echo '</li>';
		}
		echo '</ul>';
	}
	if ($close) {
		echo '</li>';
	}
}
?></ul></div><p style="clear:both">&nbsp;</p>