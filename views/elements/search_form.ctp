<div class="form">
<?php
echo $form->create('Search', array('url' => array('controller' => 'revisions', 'action' => 'search')));
echo $form->label('Search.query');
echo $form->text('Search.query', array('value' => $query));
echo $form->label('Search.collection');
echo $form->radio('Search.collection', array( 304 =>'1.1', 2 => '1.2'), array('default' => 2));
if($this->params['lang'] != 'en') {
echo $form->label('Search.lang');
echo $form->radio('Search.lang', array( $this->params['lang'] => $this->params['lang'], 'en' => 'en'), array('default' => $this->params['lang']));
}
echo $form->end(__('Search', true));
?>
</div>