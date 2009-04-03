<?php
	transformRSS (null, $html, $this);
	echo $rss->items($data, 'transformRSS');

	function transformRSS($row, &$_html = false, $_view = false) {
		static $html;
		static $_this;
		if ($_html) {
			$html = $_html;
			$_this = $_view;
			return;
		}
		return array(
			'title'		=> $row['Node']['sequence'] . ' ' . $row['Revision']['title'] . ' - ' . $html->clean(h($row['Comment']['title'])),
			'link'		=> array('controller' => 'comments', 'action' => 'index', $row['Comment']['node_id'], 'lang' => $row['Comment']['lang'], '#'
		=> "comment_{$row['Comment']['id']}"),
			'description'	=> $_this->element('comment', array('data' => $row, 'fixedDates' => true)),
			'pubDate'	=> date('r', strtotime($row['Comment']['created'])),
		);
	}
?>