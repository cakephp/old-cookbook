<?php /* SVN FILE: $Id$ */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php
		echo $html->charset('UTF-8');
		$app = cache('views/app_name_' . $this->params['lang']);
		if ($app) {
			$app = unserialize($app);
		} else {
			$__cache = Configure::read('Cache.check');
			Configure::write('Cache.check', false);
			$app = $this->requestAction(array('plugin' => false, 'prefix' => null, 'controller' => 'nodes',
				'action' => 'app_name',	'lang' => $this->params['lang']));
			Configure::write('Cache.check', $__cache);
		}
		if ($this->here == $this->webroot) {
			$title_for_layout = $app['tag_line'];
		}
		$link = '/';
		if ($this->params['lang'] != 'en') {
			$link .= $this->params['lang'];
		}
		echo $html->meta(
			'keywords',
				'CakePHP Documentation, ' . str_replace(' :: ', ', ', $title_for_layout)
		);
	?>
	<title><?php echo ($title_for_layout ? $title_for_layout : $app['tag_line']) . ' :: ' . $app['name']; ?></title>
	<?php echo $html->css(array('yui.reset-fonts-grids', 'cake.cookbook.mobile'), 'stylesheet', array('media' => 'screen')); ?>
	<link rel="apple-touch-icon" href="<?php e($this->base.'/img/iphone.png'); ?>"/>
	<style type="text/css">
		body{font-size:40px}
		#ft #copy p, #ft #secondary_nav ul, #ft #csf, #ft #img {float: none; display:block; text-align:center }
		#ft #img {padding-top: 0;}
		#ft #img a {display: block; text-align: center; }
		#ft #img img { width: 28%; margin: 0 auto;  display:block}
		#ft #csf { padding: 1em 0; }
		#search_row fieldset { width: 70%; padding-top: 0.2em; padding-right: 0.4em }
		#search_row input.query { padding: 0.2em }
	</style>
</head>
<body>
	<div id="doc3">

		<div id="hd">
			<h1>CakePHP Cookbook <sup>mobile!</sup></h1>
		</div>
   		<div id="bd">
   			<div class="yui-g" id="collections_row">
				<?php e($this->element('collections')); ?>
   			</div>
   			<div class="yui-g" id="search_row">
				<?php e($this->element('search')); ?>
   			</div>

   			<!--
   			<div class="yui-g" id="crumb_row">
   				<?php e($this->element('crumbs')); ?>
   			</div>
   			 -->

   			<div class="yui-g" id="document">

				<cake:nocache><?php
					if($session->check('Message.auth')):
						$session->flash('auth');
					endif;

					if($session->check('Message.flash')):
						$session->flash();
					endif;
				?></cake:nocache>

   				<?php e($content_for_layout); ?>
   			</div>

   			<div class="yui-gc" id="document_menu">
	            <div class="yui-u first">
					<?php
						if ($this->name == 'Nodes' && isset($data['Node']['Node']) && !$isAdmin) {
							$url = $html->url(array('admin' => false, 'controller' => 'nodes', 'action' => 'toc', $data['Node']['Node']['id']));
							echo $this->element('toc');
						}
					?>
	            </div>
	            <div class="yui-u">
	            	<cake:nocache>   <?php echo $this->element('side_menu'); ?></cake:nocache>
	            </div>
	        </div>


			</div>
   		</div>
   		<div id="ft">

			<cake:nocache> <?php echo $this->element('secondary_nav'); ?></cake:nocache>

			<div id="copy">
				<p id="img"><?php echo ' &nbsp; ';
					echo $html->link(
									$html->image('cake.power.gif', array('alt'=>"CakePHP: the PHP Rapid Development Framework")),
									'http://www.cakephp.org/',
									null,
									null,
									false
								);
					?>
					<a href="http://creativecommons.org/licenses/by-nc-nd/3.0/">
						<?php echo $html->image('license.png', array('alt' => "Creative Commons License")); ?>
					</a>
				</p>
				<p id="csf">&copy; <a href="http://cakefoundation.org">Cake Software Foundation, Inc.</a></p>
			</div>
   		</div>
   	</div>
</body>
</html>