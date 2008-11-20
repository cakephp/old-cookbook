<?php /* SVN FILE: $Id: development.ctp 658 2008-09-10 14:51:09Z AD7six $ */
$cakeDebug['Params'] = $this->params;
$cakeDebug['Session'] = $session->read();
$cookie = ClassRegistry::getObject('Component.Cookie');
if ($cookie) {
	$cakeDebug['Cookie'] = $cookie->read();
} else {
	$cakeDebug['Cookie'] = $_COOKIE;
}
if ($this->data) {
	$cakeDebug['ValidationErrors'] = $this->validationErrors;
	$cakeDebug['ViewVars'] = $this->viewVars;
}
echo '<pre class="cakeDebug">';
echo 'Debug Info' . "\r\n";
pr ($cakeDebug);
echo '</pre>';