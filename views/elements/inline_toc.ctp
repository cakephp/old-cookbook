<div id="inlineToc"><ul class="inlineToc"><?php
foreach ($directChildren as $row) {
	echo '<li>' . $html->link($row['Revision']['title'], array($row['Node']['id'], $row['Revision']['slug'])) . '</li>';
}
?></ul></div>