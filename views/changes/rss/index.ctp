<?php
	transformRSS (null, $html);
	echo $rss->items($data, 'transformRSS');

	function transformRSS($row, &$_html = false) {
		static $html;
		if ($_html) {
			$html = $_html;
			return;
		}
		switch ($row['Change']['status_to']) {
			case 'accepted';
				$to = __('accepted', true);
				break;
			case 'rejected';
				$to = __('not accepted', true);
				break;
			case 'pending';
				$to = __('pending', true);
				break;
			default:
				$to = __($row['Change']['status_to'], true);
		}

		$desc = '<ul>';
		$author = isset($row['Author']['username'])?$row['Author']['username']:'unknown';
		$desc .= '<li>' . sprintf(__('Submitted by: %1$s', true), $author) . '</li>';
		if ($row['Change']['status_to'] != 'pending'){
			$desc .= '<li>' . sprintf(__('Changed by: %1$s', true), $row['User']['username']) . '</li>';
		}
		$comment = $html->clean(trim($row['Change']['comment']));
		if ($comment) {
			$desc .= '<li>' . $comment . '</li>';
		}
		$desc .= '</ul>';
		return array(
			'title'		=> $to . ' - ' . $row['Revision']['title'],
			'link'		=> array('controller' => 'revisions', 'action' => 'view', $row['Revision']['id'], $row['Revision']['slug']),
			'description'	=> $desc,
			'pubDate'	=> date('r', strtotime($row['Change']['created'])),
		);
	}
?>