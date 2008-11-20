<?php /* SVN FILE: $Id: filter.ctp 600 2008-08-07 17:55:23Z AD7six $ */ ?>
<p>
<?php
if (!isset($filters)) {
	return;
}
echo $html->link('Toggle Filter', '#', array('id' => 'toggleFilter'));
$currentFilter = $session->read($modelClass . '.filter');
if ($currentFilter) {
	$out = ' - currently filtering for :';
	foreach ($currentFilter as $field => $filter) {
		if (is_array($filter)) {
			$filter = 'In ' . implode(', ', $filter);
		} elseif ($filter === null) {
			$currentFilters[] = $field . ' IS NULL';
			continue;
		} elseif ($filter === 'NOT NULL') {
			$currentFilters[] = $field . ' IS NOT NULL';
			continue;
		}
		$currentFilters[] = $field . ' ' . $filter;
	}
	echo $out . implode(', ', $currentFilters);
}
?>
</p>
<div id="resultFilter">
<?php
$_data = $form->data;
$form->data = $session->read($modelClass . '.filterForm');
echo $form->create(null, array('url' => '/' . $this->params['url']['url']));
foreach ($filters as $filter => $settings) {
	if (!is_array($settings)) {
		$filter = $settings;
	}
	$settings = am(array('filterOptions' => $filterOptions), $settings);
	$selectOptions = am(array('empty' => true, 'div' => false, 'label' => $filter, 'options' => $settings['filterOptions']));
	unset($settings['filterOptions']);
	$select = $form->input($filter . '_type', $selectOptions);
	$inputOptions = am(array('div' => false, 'label' => false, 'empty' => true), $settings);
	if ($filter == 'id') {
		$inputOptions['type'] = 'text';
	}
	$input = $form->input($filter, $inputOptions);
	$out = $select . $input;
	echo $html->div('input', $out);
}
echo $form->end('apply filter');
$form->data = $_data;
?>
</div>