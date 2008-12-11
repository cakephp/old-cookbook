<?php
/* SVN FILE: $Id: bootstrap.php 689 2008-11-05 10:30:07Z AD7six $ */
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
 * @version       $Revision: 689 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-05 11:30:07 +0100 (Wed, 05 Nov 2008) $
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
//EOF
// Which node to use for the home page
Configure::write('Site.homeNode', 3);

Configure::write('Site.name', 'CakePHP Cookbook');

Configure::write('Site.email', 'team@cakefoundation.org');

Configure::write('Site.database', 'bakery');

$langs = array('ar', 'en', 'fa', 'fr', 'de', 'es', 'pt', 'nl', 'id', 'it', 'ja', 'bg', 'hu', 'pl', 'cz', 'cn', 'ko', 'ro', 'ms');
sort($langs);
Configure::write('Languages.all', $langs);
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
?>