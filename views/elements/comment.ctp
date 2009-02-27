<?php
if (1==2 && $data['user_id']==$data['Revision']['user_id']) {
	$class = " highlight";
} else {
	$class = "";
}
extract($data['Comment']);
$name = __('unknown', true);
if (isset($commenters[$user_id])) {
	$name = $commenters[$user_id];
}
echo "<div id='comment_{$id}' class=\"comment$class\">";
echo "<p class=\"commentmeta\">";
if (!empty($fixedDates)) {
	echo sprintf(__('By %s on %s', true),  $name, $time->nice($created));
} else {
	echo sprintf(__('By %s %s', true),  $name, $time->timeAgoInWords($created));
}
echo "</p>";
echo "<p class=\"commenttitle\">";
if ($this->action == 'recent') {
	echo $html->link('#', array('action' => 'index', $data['Node']['id'], $data['Revision']['slug'], '#' => "comment_{$id}")) . ' - ';
} elseif(!empty($count)) {
	echo $html->link($count, "#comment_{$id}") . ' - ';
}
if ($this->action == 'recent') {
	echo $html->link($data['Node']['sequence'] . ' ' . $data['Revision']['title'], array('controller' => 'nodes', 'action' => 'view', $data['Node']['id'],
		$data['Revision']['slug'], 'lang' => $lang)) . ' - ';
}
	echo htmlspecialchars($title);
echo "</p>";
echo "<div class=\"commentbody\">";
echo '<p>' . implode(explode("\n", htmlspecialchars($body)), '</p><p>') . '</p>';
echo "</div>";
echo "</div>";?>