<?php /* SVN FILE: $Id: recent.ctp 600 2008-08-07 17:55:23Z AD7six $ */
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
			'title'		=> $Comment['title'],
			'link'		=> array('controller' => 'comments', 'action' => 'view', $Comment['id']),
			'description'	=> $_this->element('comment', array('data' => $row['Comment'])),
			'pubDate'	=> date('r', strtotime($Comment['created'])),
		);
	}
?>