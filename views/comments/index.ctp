<?php if (empty($this->params['isAjax'])) : ?>
<h2><?php
if (isset($node)) {
	echo $html->link(sprintf(__('Comments: %1$s', true), h($node['Revision']['title'])), array('id' => $this->params['id']));
} else {
	__('Recent Comments');
}
?></h2>
<?php
endif;
if (!$data) {
	echo '<p>' . __('No Comments yet!', true) . '</p>';
} else {
	foreach ($data as $count => $row) {
		echo $this->element('comment', array('data' => $row, 'count' => $count + 1));
	}
}
if (isset($node)) {
	echo '<div class="comment"><p class="commenttitle"><em>';
	echo $html->link(__('Add a comment', true), am($this->passedArgs, array('controller' => 'comments', 'action' => 'add')));
	echo '</em></p></div>';
}
$html->meta('rss', $html->url($this->passedArgs) . '.rss', array('title' => __('This page as a feed', true)), false);
?>