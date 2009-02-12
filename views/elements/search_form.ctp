<div class="form">
<?php
echo $form->create('Search', array('url' => array('controller' => 'revisions', 'action' => 'search')));
echo $form->input('Search.collection', array('type' => 'radio', 'default' => 2, 'legend' => __('Collection', true)));
echo $form->input('Search.query', array('label' => __('Search term', true)));
echo $form->end(__('Search', true));
?>
</div>