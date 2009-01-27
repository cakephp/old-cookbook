<?php
	transformRSS (null, $html);
	echo $rss->items($data, 'transformRSS');

	function transformRSS($row, &$_html = false) {
		static $html;
		if ($_html) {
			$html = $_html;
			return;
		}
		extract($row);
		return array(
			'title'		=> $Revision['title'],
			'link'		=> array('action' => 'view', $Node['id'], $Revision['slug']),
			'description'	=> $Revision['content'],
			'pubDate'	=> date('r', strtotime($Revision['modified'])),
		);
	}
?>