<?php /* SVN FILE: $Id: index.ctp 638 2008-09-01 23:24:26Z AD7six $ */
	transformRSS (null, $html);
	echo $rss->items($data, 'transformRSS');

	function transformRSS($row, &$_html = false) {
		static $html;
		if ($_html) {
			$html = $_html;
			return;
		}
		extract($row);
		switch ($Change['status_to']) {
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
				$to = __($Change['status_to'], true);
		}

		$desc = '<ul>';
		$author = isset($Author['username'])?$Author['username']:'unknown';
		$desc .= '<li>' . sprintf(__('Submitted by: %s', true), $author) . '</li>';
		if ($Change['status_to'] != 'pending'){
			$desc .= '<li>' . sprintf(__('Changed by: %s', true), $User['username']) . '</li>';
		}
		$comment = $html->clean(trim($Change['comment']));
		if ($comment) {
			$desc .= '<li>' . $comment . '</li>';
		}
		$desc .= '</ul>';
		return array(
			'title'		=> $to . ' - ' . $Revision['title'],
			'link'		=> array('controller' => 'revisions', 'action' => 'view', $Revision['id'], $Revision['slug']),
			'description'	=> $desc,
			'pubDate'	=> date('r', strtotime($Change['created'])),
		);
	}
?>