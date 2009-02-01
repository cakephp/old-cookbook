<div class="message flashInfo"><?php
echo $content_for_layout;
$_lang = Configure::read('Config.language');
Configure::write('Config.language', $lang);
echo ' ' . $html->link(__('Want to know why?', true), array('controller' => 'nodes', 'action' => 'view', Configure::read('Site.methodologyNode'), 'lang' => $lang));
Configure::write('Config.language', $_lang);
?></div>