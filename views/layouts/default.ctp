<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<cake:nocache>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
if (!empty($auth['User']['Level']) && $auth['User']['Level'] == ADMIN) {
	//echo "<!-- " . exec('git rev-parse HEAD') . " -->";
	echo "<!-- " . trim(file_get_contents(APP . '.git/refs/heads/master')) . " -->";
}
// Preventing automatic language detection
Configure::write('Config.language', $this->params['lang']);
?>
</cake:nocache>
<?php echo $html->charset('UTF-8');
$app = cache('views/app_name_' . $this->params['lang']);
if ($app) {
	$app = unserialize($app);
} else {
	$__cache = Configure::read('Cache.check');
	Configure::write('Cache.check', false);
	$app = $this->requestAction(array('plugin' => null, 'prefix' => null, 'controller' => 'nodes',
		'action' => 'app_name', 'lang' => $this->params['lang']));
	Configure::write('Cache.check', $__cache);
}
if ($this->here == $this->webroot) {
	$title_for_layout = $app['tag_line'];
}
echo $html->meta('keywords',
	'CakePHP Documentation, ' . str_replace(' :: ', ', ', $title_for_layout)
);

?>
<title>
	<?php echo  ($title_for_layout ? $title_for_layout:$app['tag_line']) . ' :: ' . $app['name'];?>
</title>

<link rel="icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
<?php
	echo $html->meta(array(
		'rel' => 'alternate', 'title' => __('Mobile version', true),
		'type' => 'text/html', 'media' => 'handheld',
		'url' => $html->url(array(
			'admin' => false, 'plugin' => null, 'controller' => 'nodes', 'action' => 'index', 'theme' => 'mobile'
		), true),
	));
	echo $html->meta('rss', array(
		'admin' => false, 'plugin' => null, 'controller' => 'changes', 'action' => 'index', 'ext' => 'rss'
		),
		array('title' => __('Recent Changes', true))
	);
	echo $html->meta('rss',
		$html->url(array(
			'admin' => false, 'plugin' => null, 'controller' => 'changes', 'action' => 'index'
		)) . '/language:*.rss',
		array('title' => __('Recent Changes for all languages', true))
	);
	echo $html->meta('rss', array(
		'admin' => false, 'plugin' => null, 'controller' => 'comments', 'action' => 'recent', 'ext' => 'rss'
		),
		array('title' => __('Recent comments', true))
	);
	echo $html->meta('rss',
		$html->url(array(
			'admin' => false, 'plugin' => null, 'controller' => 'comments', 'action' => 'recent'
		)) . '/language:*.rss',
		array('title' => __('Recent Comments for all languages', true))
	);
?>
<cake:nocache>
	<?php
		if ($session->read('Auth.User.id')) {
			$userName = $session->read('Auth.User.username');
			echo $html->meta('rss',
				array(
					'admin' => false, 'plugin' => null, 'controller' => 'changes', 'action' => 'index',
					'language' => '*', 'user' => $userName, 'ext' => 'rss'
				),
				array('title' => __('My Submissions', true))
			);
			$menu->settings('Feeds', array('order' => 99));
			$menu->add(array(
				'section' => 'Feeds', // __('Feeds') for the i18n console task
				'title' => __('My Submissions', true),
				'url' => array(
					'admin' => false, 'plugin' => null, 'controller' => 'changes', 'action' => 'index',
					'language' => '*', 'user' => $userName, 'ext' => 'rss'
				)
			));
			if ($session->read('Auth.User.Level') >= EDITOR) {
				$menu->add(array(
					'section' => 'Feeds', // __('Feeds') for the i18n console task
					'title' => __('Pending Submissions', true),
					'url' => array(
						'admin' => false, 'plugin' => null, 'controller' => 'revisions', 'action' => 'pending',
						'ext' => 'rss'
					),
				));
			}
		}
	?>
</cake:nocache>
<?php
	if (!isset($this->params['lang']) || $this->params['lang'] === $defaultLang) {
		$base = $html->url('/');
	} else {
		$base = $html->url('/' . $this->params['lang'] . '/');
	}
	echo $javascript->codeBlock("var baseUrl = '{$base}';");
	$asset->css(array(
			'cake.generic',
			'cake.cookbook',
			'theme/ui.core',
			'theme/ui.dialog',
			'theme/ui.resizable',
			'theme/ui.theme'
		),
		'stylesheet',
		array('media' => 'screen')
	);
	$asset->css('print', 'stylesheet', array('media' => 'print'));
	echo $asset->out('css');
	echo $scripts_for_layout;
?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>
				<?php
					$link = '/';
					if ($this->params['lang'] != $defaultLang) {
						$link .= $this->params['lang'];
					}
					echo $html->link(sprintf(__('Welcome to %1$s', true), $app['name']), $link);
				?>
			</h1>
		</div>
		<?php
			if (empty($this->params['admin'])) {
				echo $this->element('collections');
			} else {
				echo $this->element('menu/admin');
			}
		?>
		<cake:nocache>
			<?php echo $this->element('secondary_nav'); ?>
		</cake:nocache>
		<?php
			echo $this->element('sites_nav');
			echo $this->element('search');
		?>
		<div id="content">
			<div id="side">
				<?php
					if ($this->name == 'Nodes' && isset($data['Node']['Node']) && !$isAdmin) {
						$url = $html->url(array(
							'admin' => false, 'controller' => 'nodes', 'action' => 'toc',
							$data['Node']['Node']['id']
						));
						echo $this->element('toc');
					}
				?>
				<cake:nocache>
					<?php echo $this->element('side_menu'); ?>
				</cake:nocache>
			</div>
			<div id="body">
				<?php echo $this->element('crumbs'); ?>
				<cake:nocache>
					<?php
						echo $session->flash('auth');
						echo $session->flash();
					?>
				</cake:nocache>

				<?php echo $content_for_layout; ?>

				<div class="clear"></div>
			</div>

			<div class="clear"></div>
		</div>
		<div id="footer">
			<p>
				<?php
				echo $this->element('language_links') . ' &nbsp; ';
				echo $html->image('cake.power.gif', array(
						'alt' => 'CakePHP: the PHP Rapid Development Framework',
						'url' => 'http://www.cakephp.org/'
					));
				?>
				<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/">
					<?php echo $html->image('license.png', array('alt' => "Creative Commons License"));?>
				</a>
			</p>
			<p>&copy; <a href="http://cakefoundation.org">Cake Software Foundation, Inc.</a></p>
		</div>
	</div>
<?php
echo $asset->js(array(
	'jquery',
	'jquery.form',
	'jquery-ui',
	'scripts',
	'popup'
));
echo $asset->out('js');
if(isProduction()) {
	echo $this->element('analytics');
}
$this->set('data', false); $this->cache->data = false;
?>
</body>
</html>