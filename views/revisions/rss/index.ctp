<?php
	transformRSS (null, $html);
	echo $rss->items($data, 'transformRSS');

	function transformRSS($row, &$_html = false) {
		static $html;
		if ($_html) {
			$html = $_html;
			return;
		}
		$desc = '<ul>';
		$author = isset($row['User']['username'])?$row['User']['username']:'unknown';
		$desc .= '<li>' . sprintf(__('Submitted by: %1$s', true), $author) . '</li>';
		$comment = $html->clean(trim($row['Revision']['reason']));
		if ($comment) {
			$desc .= '<li>' . $comment . '</li>';
		}
		$desc .= '</ul>';
		if ($row['Node']['sequence']) {
			$title = $row['Node']['sequence'] . ' - ' . $row['Revision']['title'];
		} else {
			$title = $row['Revision']['title'];
		}
		return array(
			'title'		=> $title,
			'link'		=> array('controller' => 'revisions', 'action' => 'view', $row['Revision']['id'], $row['Revision']['slug']),
			'description'	=> $desc,
			'pubDate'	=> date('r', strtotime($row['Revision']['created'])),
		);
	}
?>