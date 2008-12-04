<?php /* SVN FILE: $Id: comment.ctp 673 2008-10-06 14:05:17Z AD7six $ */
if (1==2 && $data['user_id']==$data['Revision']['user_id']) {
	$class = " highlight";
} else {
	$class = "";
}
extract($data);
extract($Comment);
$name = __('unknown', true);
if (isset($commenters[$user_id])) {
	$name = $commenters[$user_id];
}
echo "<div id='comment_{$id}' class=\"comment$class\">";
echo "<p class=\"commentmeta\">";
echo sprintf(__('By %s %s', true), $name, $time->timeAgoInWords($created));
echo "</p>";
echo "<p class=\"commenttitle\">";
if ($this->action == 'recent') {
	echo $html->link('#', array('action' => 'index', $Node['id'], $Revision['slug'], '#' => "comment_{$id}")) . ' - ';
} elseif(!empty($count)) {
	echo $html->link($count, "#comment_{$id}") . ' - ';
}
if ($this->action == 'recent') {
	echo $html->link($Node['sequence'] . ' ' . $Revision['title'], array('controller' => 'nodes', 'action' => 'view', $Node['id'],
		$Revision['slug'], 'lang' => $lang)) . ' - ';
}
	echo htmlspecialchars($title);
echo "</p>";
echo "<div class=\"commentbody\">";
echo '<p>' . implode(explode("\n", htmlspecialchars($body)), '</p><p>') . '</p>';
echo "</div>";
echo "</div>";
?>