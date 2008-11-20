<?php /* SVN FILE: $Id: default.ctp 611 2008-08-19 15:39:29Z AD7six $ */ ?>
<cake:nocache><?php header('Content-type: text/xml'); ?></cake:nocache><?php
e($xml->header());
echo $content_for_layout;
if (!$this->cacheAction) {
	$this->cacheAction = CACHE_DURATION;
}
$this->data = null;
Configure::write('debug', 0);
?>