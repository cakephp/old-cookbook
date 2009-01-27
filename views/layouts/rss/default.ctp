<cake:nocache><?php header('Content-type: application/rss-xml'); ?></cake:nocache><?php
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