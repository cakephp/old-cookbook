<?php /* SVN FILE: $Id: default.ctp 611 2008-08-19 15:39:29Z AD7six $ */ ?>
<cake:nocache><?php header('Content-type: application/xhtml-xml'); ?></cake:nocache><?php
echo $rss->header();

if (!isset($channel)) {
	$channel = array();
}
if (!isset($channel['title'])) {
	$channel['title'] = 'Cookbook :: ' . $title_for_layout;
}

echo $rss->document(
	$rss->channel(
		array(), $channel, $content_for_layout
	)
);
if (!$this->cacheAction) {
	$this->cacheAction = CACHE_DURATION;
}
$this->data = null;
Configure::write('debug', 0);
?>