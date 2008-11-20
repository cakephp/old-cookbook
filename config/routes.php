<?php
/* SVN FILE: $Id: routes.php 703 2008-11-19 12:13:40Z AD7six $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @since         CakePHP v 0.2.9
 * @version       $Revision: 703 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-11-19 13:13:40 +0100 (Wed, 19 Nov 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
if (!empty($fromUrl) && strpos($fromUrl, 'admin') === 0) {
	Router::connectNamed(true);
} else {
	Router::connectNamed(array('node', 'user', 'language', 'status'), array('default' => true));
}
Router::parseExtensions('rss', 'xml');

Router::connect('/', array('controller' => 'nodes', 'action' => 'index'), array('lang' => 'en'));
Router::connect('/:lang/', array('controller' => 'nodes', 'action' => 'index'), array('lang' => '[a-z]{2}'));

Router::connect('/:lang/stats/*', array('controller' => 'nodes', 'action' => 'stats'), array('lang' => '[a-z]{2}'));
Router::connect('/stats', array('controller' => 'nodes', 'action' => 'stats'), array('lang' => 'en'));

Router::connect('/:lang/todo/*', array('controller' => 'nodes', 'action' => 'todo'), array('lang' => '[a-z]{2}'));
Router::connect('/todo', array('controller' => 'nodes', 'action' => 'todo'), array('lang' => 'en'));

Router::connect('/:lang/view/*', array('controller' => 'nodes', 'action' => 'view'), array('lang' => '[a-z]{2}'));
Router::connect('/view/*', array('controller' => 'nodes', 'action' => 'view'), array('lang' => 'en'));

Router::connect('/search/*', array('controller' => 'revisions', 'action' => 'search'), array('lang' => 'en'));
Router::connect('/:lang/search/*', array('controller' => 'revisions', 'action' => 'search'), array('lang' => '[a-z]{2}'));

Router::connect('/results/*', array('controller' => 'revisions', 'action' => 'results'), array('lang' => 'en'));
Router::connect('/:lang/results/*', array('controller' => 'revisions', 'action' => 'results'), array('lang' => '[a-z]{2}'));

Router::connect('/compare/*', array('controller' => 'nodes', 'action' => 'compare'), array('lang' => 'en'));
Router::connect('/:lang/compare/*', array('controller' => 'nodes', 'action' => 'compare'), array('lang' => '[a-z]{2}'));

Router::connect('/changes/*', array('controller' => 'changes', 'action' => 'index'), array('lang' => 'en'));
Router::connect('/:lang/changes/*', array('controller' => 'changes', 'action' => 'index'), array('lang' => '[a-z]{2}'));

Router::connect('/history/*', array('controller' => 'nodes', 'action' => 'history'), array('lang' => 'en'));
Router::connect('/:lang/history/*', array('controller' => 'nodes', 'action' => 'history'), array('lang' => '[a-z]{2}'));

Router::connect('/toc/*', array('controller' => 'nodes', 'action' => 'toc'), array('lang' => 'en'));
Router::connect('/:lang/toc/*', array('controller' => 'nodes', 'action' => 'toc'), array('lang' => '[a-z]{2}'));

Router::connect('/complete/*', array('controller' => 'nodes', 'action' => 'single_page'), array('lang' => 'en'));
Router::connect('/:lang/complete/*', array('controller' => 'nodes', 'action' => 'single_page'), array('lang' => '[a-z]{2}'));

Router::connect('/edit/*', array('controller' => 'nodes', 'action' => 'edit'), array('lang' => 'en'));
Router::connect('/:lang/edit/*', array('controller' => 'nodes', 'action' => 'edit'), array('lang' => '[a-z]{2}'));

Router::connect('/add/*', array('controller' => 'nodes', 'action' => 'add'), array('lang' => 'en'));
Router::connect('/:lang/add/*', array('controller' => 'nodes', 'action' => 'add'), array('lang' => '[a-z]{2}'));

Router::connect('/comments/:id/*', array('controller' => 'comments', 'action' => 'index'), array('id' => '[0-9]+', 'lang' => 'en'));
Router::connect('/:lang/comments/:id/*', array('controller' => 'comments', 'action' => 'index'), array('id' => '[0-9]+', 'lang' => '[a-z]{2}'));

Router::connect('/comment/*', array('controller' => 'comments', 'action' => 'add'), array('lang' => 'en'));
Router::connect('/:lang/comment/*', array('controller' => 'comments', 'action' => 'add'), array('lang' => '[a-z]{2}'));

Router::connect('/admin', array('prefix' => 'admin', 'controller' => 'revisions', 'action' => 'pending', 'admin' => true), array('admin' => true));
Router::connect('/:lang/admin', array('prefix' => 'admin', 'controller' => 'revisions', 'action' => 'pending', 'admin' => true), array('admin' => true, 'lang' => '[a-z]{2}'));

Router::connect('/img/*', array('controller' => 'attachments', 'action' => 'view'), array('lang' => 'en'));
// Legacy
Router::connect('/chapter/*', array('controller' => 'redirect', 'action' => 'process', 'chapter'));
Router::connect('/appendix/*', array('controller' => 'redirect', 'action' => 'process', 'appendix'));

if (isset($fromUrl) && strpos('admin', $fromUrl) !== false) {
	Router::connectNamed(array('query', 'collection', 'lang'), array('greedy' => false, 'default' => true));
}
?>