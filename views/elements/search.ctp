<?php /* SVN FILE: $Id: search.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<div id="site_search"><?php
$currentCollection = 2;
if (isset($currentPath[1])) {
	list($_, $currentCollection) = $currentPath;
	if (isset($currentCollection['Node']['id'])) {
		$currentCollection = $currentCollection['Node']['id'];
	}
}
$query = isset($this->params['named']['query'])?$this->params['named']['query']:'';
if (isset($this->params['admin'])) {
	echo $form->create(null, array('action' => 'search', 'id' => 'search'));
} else {
	echo $form->create('Search', array('url' => '/search', 'id' => 'search'));
}
echo $form->inputs(array(
	'legend' => false,
	'query' => array('label' => false, 'div' => false, 'value' => $query, 'class' => 'query'),
	'collection' => array('type' => 'hidden', 'value' => $currentCollection),
	'lang' => array('type' => 'hidden', 'value' => $this->params['lang']),
));
echo $form->submit(__('Search', true), array('div' => false, 'id' => 'search_submit_btn'));
echo $form->end();
?></div>