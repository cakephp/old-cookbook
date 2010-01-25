<h2>Missing Database Table</h2>
<p class="error">
	<strong>Error:</strong>
	<?php echo sprintf('Database table %1$s for model %2$s was not found.',"<em>" . $table . "</em>",  "<em>" . $model . "</em>");?>
</p>
<br />
<h3>Detected tables:</h3>
<?php
$plugin = ''; // stub
$Inst = ClassRegistry::init(array(
	'class' => $plugin . $model,
	'table'=> false
));
$db =& ConnectionManager::getDataSource($Inst->useDbConfig);
$db->cacheSources = ($Inst->cacheSources && $db->cacheSources);
if ($db->isInterfaceSupported('listSources')) {
	echo '<ul><li>' . implode($db->listSources(), '</li><li>') . '</li></ul>';
}
echo $this->element('trace');
$this->layout = 'error';