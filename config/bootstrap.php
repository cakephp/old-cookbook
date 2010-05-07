<?php
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */

/**
 * This is included in the path for css and js files, and should be updated whenever they are touched
 * Set to the date of the last update
 */
Configure::write('MiCompressor.fingerprint', '20100125'); // Last time the js or css was edited

// Which node to use for the home page
Configure::write('Site.homeNode', 876);

Configure::write('Site.name', 'CakePHP Cookbook');

Configure::write('Site.email', 'team@cakefoundation.org');

Configure::write('Site.database', 'bakery');

Configure::write('Languages.default', 'en');
$langs = array(
	'ar',
	'bg',
	'cn',
	'cz',
	'de',
	'el',
	'en',
	'es',
	'fa',
	'fr',
	'hu',
	'id',
	'it',
	'ja',
	'ko',
	'ms',
	'nl',
	'pt',
	'pl',
	'ro',
	'ru',
	'sk',
	'tr',
	'tw',
	'vi'
);
Configure::write('Languages.all', $langs);

Configure::write('MiCompressor.clearCache', false);
Configure::write('MiCompressor.cacheDir', CACHE . 'assets' . DS);
Configure::write('MiCompressor.salt', trim(file_get_contents(APP . '.git/refs/heads/master')));

define('ADMIN', '800');
define('EDITOR', '700');
define('MODERATOR', '600');
define('COMMENTER', '300');
define('READ', '200');
define('NONE', '100');
define('INVALID', '0');
if (Configure::read()) {
	define('CACHE_DURATION', '+1 minute');
} else {
	define('CACHE_DURATION', '+99 days');
	ob_start('ob_gzhandler');
}

/**
 * isProduction method
 *
 * @TODO base this off a path or something else that isn't potentially volatile
 * @return void
 * @access public
 */
function isProduction() {
	if (!isset($_SERVER['HTTP_HOST'])) {
		return false;
	}
	return ($_SERVER['HTTP_HOST'] === 'book.cakephp.org');
}