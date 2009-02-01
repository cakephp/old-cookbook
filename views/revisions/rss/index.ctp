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
		$desc = '<ul>';
		$author = isset($User['username'])?$User['username']:'unknown';
		$desc .= '<li>' . sprintf(__('Submitted by: %s', true), $author) . '</li>';
		$comment = $html->clean(trim($Revision['reason']));
		if ($comment) {
			$desc .= '<li>' . $comment . '</li>';
		}
		$desc .= '</ul>';
		if ($Node['sequence']) {
			$title = $Node['sequence'] . ' - ' . $Revision['title'];
		} else {
			$title = $Revision['title'];
		}
		return array(
			'title'		=> $title,
			'link'		=> array('controller' => 'revisions', 'action' => 'view', $Revision['id'], $Revision['slug']),
			'description'	=> $desc,
			'pubDate'	=> date('r', strtotime($Revision['created'])),
		);
	}
?>