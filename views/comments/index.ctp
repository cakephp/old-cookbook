<?php /* SVN FILE: $Id: index.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<h2><?php
if (isset($node)) {
	echo $html->link(sprintf(__('Comments: %s', true), htmlspecialchars($node['Revision']['title'])), array('id' => $this->params['id']));
} else {
	__('Recent Comments');
}
?></h2>
<?php
if (!$data) {
	echo '<p>' . __('No Comments yet!', true) . '</p>';
} else {
	foreach ($data as $count => $row) {
		echo $this->element('comment', array('data' => $row, 'count' => $count + 1));
	}
}
if (isset($node)) {
	echo $this->element('comment_form');
}
$html->meta('rss', am($this->params['pass'], array('theme' => 'defaut', 'ext' => 'rss')), array('title' => __('This page as a feed', true)), false);
?>