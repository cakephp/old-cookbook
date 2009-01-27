<?php
$singularHumanName = Inflector::Humanize( Inflector::Underscore($singularHumanName));
$pluralHumanName = Inflector::Humanize( Inflector::Underscore($pluralHumanName));
$namedString = implode ('\', \'', $fields);
$singularVar[0] = up($singularVar[0]);
$keyFields = array();
if (isset($associations['belongsTo'])) {
	foreach ($associations['belongsTo'] as $alias => $details) {
		$keyFields[$details['foreignKey']] = array(
			'alias' => $alias,
			'displayField' => $details['displayField'],
			'foreignKey' => $details['foreignKey']
		);
	}
}
?>
<h1><?php echo $pluralHumanName; ?></h1>
<div class="container">
<?php echo "<?php\r\n"; ?>
<?php //echo "\$pass = \$this->passedArgs;\r\n"; ?>
<?php echo "\$pass['action'] = str_replace(Configure::read('Routing.admin') . '_', '', \$this->action); // temp\r\n"; ?>
<?php echo "\$paginator->options(array('url' => \$pass));\r\n"; ?>
<?php echo "?>\r\n"; ?>
<table>
<?php echo "<?php\r\n"; ?>
<?php echo "\$th = array(\r\n"; ?>
<?php foreach ($fields as $field): ?>
<?php if (isset($keyFields[$field])) : ?>
	<?php echo "\$paginator->sort('" . Inflector::Humanize( Inflector::Underscore($keyFields[$field]['alias'])) . "', '{$keyFields[$field]['alias']}.{$keyFields[$field]['displayField']}'),\r\n"; ?>
<?php elseif(!in_array($schema[$field]['type'], array('text')) && !in_array($field, array('password', 'deleted', 'slug', 'temppassword', 'email_token'))) : ?>
	<?php echo "\$paginator->sort('{$field}'),\r\n"; ?>
<?php endif; ?>
<?php endforeach; ?>
	<?php echo "'actions'\r\n"; ?>
<?php echo ");\r\n"; ?>
<?php echo "echo \$html->tableHeaders(\$th);\r\n"; ?>
<?php echo "foreach (\$data as \$row) {\r\n"; ?>
	<?php echo "extract(\$row);\r\n" ?>
	<?php echo "\$actions = array();\r\n" ?>
	<?php echo "\$actions[] = \$html->link('V', array('action' => 'view', \${$modelClass}['id']), array('title' => 'view'));\r\n" ?>
	<?php echo "\$actions[] = \$html->link('E', array('action' => 'edit', \${$modelClass}['id']), array('title' => 'edit'));\r\n" ?>
	<?php echo "\$actions[] = \$html->link('X', array('action' => 'delete', \${$modelClass}['id']), array('title' => 'delete'));\r\n" ?>
	<?php echo "\$actions = implode(' - ', \$actions);\r\n" ?>
	<?php echo "\$tr = array(\r\n"; ?>
<?php foreach ($fields as $field): ?>
<?php if (isset($keyFields[$field])): ?>
		<?php echo "\${$keyFields[$field]['alias']}?\$html->link(\${$keyFields[$field]['alias']}['{$keyFields[$field]['displayField']}'], am(\$pass, array('page' => 1, '$field' => \${$modelClass}['$field']))):'',\r\n"; ?>
<?php elseif ($field == $primaryKey || $field == $displayField) : ?>
		<?php echo "\$html->link(\${$modelClass}['$field'], array('action' => 'view', \${$modelClass}['$primaryKey'])),\r\n"; ?>
<?php elseif (!in_array($schema[$field]['type'], array('text')) && !in_array($field, array('password', 'deleted', 'slug', 'temppassword', 'email_token'))) : ?>
<?php if (in_array($field, array('ip', 'signup_ip'))) : ?>
		<?php echo "\$html->link(long2ip(\${$modelClass}['$field']), am(\$pass, array('page' => 1, '$field' => \${$modelClass}['$field']))),\r\n"; ?>
<?php else : ?>
		<?php echo "\$html->link(\${$modelClass}['$field'], am(\$pass, array('page' => 1, '$field' => \${$modelClass}['$field']))),\r\n"; ?>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; ?>
		<?php echo "\$actions\r\n"; ?>
	<?php echo ");\r\n"; ?>
	<?php echo "echo \$html->tableCells(\$tr, array('class' => 'odd'), array('class' => 'even'));\r\n"; ?>
<?php echo "}\r\n"; ?>
<?php echo "?>\r\n"; ?>
</table>
<?php echo "<?php echo \$this->element('paging'); ?>"; ?>
</div>