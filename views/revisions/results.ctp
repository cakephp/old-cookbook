<?php
//echo $this->element('search_form');
echo '<div class="searchresults">';
if(!empty($results)){
$pass = $this->passedArgs;
$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', $this->action);
$paginator->options(array('url' => $pass));
echo '<h2>' . __('Search Results', true) . ' ('.$paginator->counter(array('format' => '%start% - %end% of %count%')).')</h2>';
	echo '<ol id="results">';
        foreach($results as $result){
	switch($result['Result']['cake_model']){
		case 'Revision':
			echo '<li><h3>';
			$url = array('controller' => 'nodes',
			'action' => 'view', $result['Result']['node_id'], $result['Result']['slug']);
			break;
		case 'Comment':
			echo '<li>';
			$url = array('controller' => 'comments',
			'action' => 'view', $result['Result']['cake_id']);
			break;
	}
	echo $html->link($search->highlight($terms, $result['Result']['title'], false),
			$url, null, null, false );
	echo '</h3><p>';
		echo $search->highlight($terms, $result['Result']['content']);
		echo '</p></li>';
                }
	echo '</ol>';
	echo $this->element('paging');
} else {
    echo '<h2>' . __('No results', true) . '</h2>';
}
?>
</div>