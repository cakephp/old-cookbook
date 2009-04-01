<div class="nodes view">
<h2>Statistics</h2>
<div class="summary">
<p><?php echo __('Here\'s a shout out to those who have dedicated time, sweat and tears to write, translate and edit the cookbook contents.', true) ?></p>
</div>
<h3><?php echo sprintf(__('Top %s Contributors', true), 'EN') ?></h3>
<div class="options"><ul class="node-options">
	<li><?php echo sprintf(__('Last update: %s', true), $time->niceShort($data[$defaultLang]['last_update'])) ?></li>
</ul></div>
<div class="summary">
	<?php
	foreach ($data[$defaultLang]['top_contributors'] as $row) {
		if (isset($users[$row['Revision']['user_id']])) {
			$nick = $users[$row['Revision']['user_id']]['User']['username'];
			if ($users[$row['Revision']['user_id']]['Profile']['url']) {
				$url = $users[$row['Revision']['user_id']]['Profile']['url'];
			} else {
				$url = 'http://bakery.cakephp.org/profiles/view/' . $row['Revision']['user_id'] . '/' . $nick;
			}
		} else {
			$nick = 'user_' . $row['Revision']['user_id'];
			$url = 'http://bakery.cakephp.org/profiles/view/' . $row['Revision']['user_id'] . '/' . $nick;
		}
		$menu->add(array(
			'section' => $defaultLang,
			'title' => sprintf(__('%s (%s current)', true), $nick, $row[0]['count']),
			'url' => $url
		));
	}
	unset ($counts[$defaultLang]);
	echo '<br style="clear:left" />';
	echo '<div class="userstats">' . $menu->generate($defaultLang, array('class' => 'stats', 'splitCount' => 3)) . '</div>';
	echo '<br style="clear:left" />';
echo '</div>';
foreach ($counts as $lang => $count) {
	$row = $data[$lang];
	echo '<h3 id="' . $lang . '">' . $html->link(sprintf(__('Top %s Contributors', true), up($lang)), '#' . $lang) . '</h3><div class="options"><ul class="node-options">';
	echo '<li>' .  sprintf(__('Last update: %s', true), $time->niceShort($row['last_update'])) . '</li>';
	echo '<li>' .  sprintf(__('%s%% translated', true), (int)($count / $nodes * 100)) . '</li></ul></div><div class="summary">';
	if (!$row['last_update']) {
		echo '<p class="warning">' . __('The cookbook needs you! No submissions for this language!', true) . '</p>';
	} else {
		if (!$time->wasWithinLast('2 month', $row['last_update']))  {
			echo '<p class="warning">' . __('The cookbook needs you! This language will soon be removed if not updated.', true) . '</p>';
		} elseif (!$time->wasWithinLast('1 month', $row['last_update']))  {
			echo '<p class="note">' . __('The cookbook needs you! No updates for one month.', true) . '</p>';
		}
	}
	foreach ($row['top_contributors'] as $row) {
		if (isset($users[$row['Revision']['user_id']])) {
			$nick = $users[$row['Revision']['user_id']]['User']['username'];
			if ($users[$row['Revision']['user_id']]['Profile']['url']) {
				$url = $users[$row['Revision']['user_id']]['Profile']['url'];
			} else {
				$url = 'http://bakery.cakephp.org/profiles/view/' . $row['Revision']['user_id'] . '/' . $nick;
			}
		} else {
			$nick = 'user_' . $row['Revision']['user_id'];
			$url = 'http://bakery.cakephp.org/profiles/view/' . $row['Revision']['user_id'] . '/' . $nick;
		}
		$menu->add(array(
			'section' => $lang,
			'title' => sprintf(__('%s (%s current)', true), $nick, $row[0]['count']),
			'url' => $url
		));
	}
	echo '<div class="userstats">' . $menu->generate($lang, array('class' => 'stats', 'splitCount' => 3)) . '</div>';
	echo '</div>';
	echo '<br style="clear:left" />';
}
?>
</div>