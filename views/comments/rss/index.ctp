<?php /* SVN FILE: $Id: index.ctp 600 2008-08-07 17:55:23Z AD7six $ */
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
		extract($row);
		return array(
			'title'		=> $Node['sequence'] . ' ' . $Revision['title'] . ' - ' . $html->clean(htmlspecialchars($Comment['title'])),
			'link'		=> array('controller' => 'comments', 'action' => 'index', $Comment['node_id'], 'lang' => $Comment['lang'], '#'
		=> "comment_{$Comment['id']}"),
			'description'	=> $_this->element('comment', array('data' => $row, 'fixedDates' => true)),
			'pubDate'	=> date('r', strtotime($Comment['created'])),
		);
	}
?>