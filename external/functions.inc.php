<?php
	/**
	 * functions.inc.php
	 * Add your additional initialization routines here
	 */

	# Include additional Hummingbird dependencies
	include $site->baseDir('/external/utilities.inc.php');
	include $site->baseDir('/external/routes.inc.php');
	include $site->baseDir('/external/hooks.inc.php');
	include $site->baseDir('/external/model.inc.php');
	include $site->baseDir('/external/curly.inc.php');
	include $site->baseDir('/external/oppai.inc.php');
	include $site->baseDir('/external/norm.inc.php');
	include $site->baseDir('/external/crood.inc.php');

	include $site->baseDir('/external/model/scp.model.php');
	include $site->baseDir('/external/model/tag.model.php');
	include $site->baseDir('/external/model/tale.model.php');
	include $site->baseDir('/external/model/supplement.model.php');

	# Include Google Fonts
	$fonts = [
		'Open Sans' => [300, 400, '400italic', 700, '700italic'],
		'Lora' => [400, '400i', 700, '700i'],
		'Ubuntu Mono' => [400, 700]
	];
	$site->registerStyle('google-fonts', get_google_fonts($fonts), true);
	$site->registerStyle('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', true);
	$site->registerStyle('tooltipster', 'plugins/tooltipster.bundle.min.css', false);
	$site->registerStyle('tooltipster-borderless', 'plugins/tooltipster-sideTip-borderless.min.css', false);
	$site->registerStyle('tooltipster-shadow', 'plugins/tooltipster-sideTip-shadow.min.css', false);
	$site->registerStyle('reset', 'reset.css', false );
	$site->registerStyle('print', 'print.css', false, [], ['media' => 'print']);
	$site->registerStyle('site', 'site.less', false, ['reset', 'google-fonts', 'font-awesome', 'tooltipster', 'tooltipster-borderless', 'tooltipster-shadow']);
	$site->enqueueStyle('site');
	$site->enqueueStyle('print');

	$site->registerScript('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js', true);
	$site->registerScript('underscore', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js', true);
	$site->registerScript('velocity', 'https://cdnjs.cloudflare.com/ajax/libs/velocity/1.5.0/velocity.min.js', true);
	$site->registerScript('velocity.ui', 'https://cdnjs.cloudflare.com/ajax/libs/velocity/1.5.0/velocity.ui.min.js', true);
	$site->registerScript('tooltipster', 'plugins/tooltipster.bundle.min.js', false);
	$site->registerScript('mgGlitch', 'plugins/mgGlitch.min.js', false);
	$site->registerScript('plugins', 'plugins/plugins.min.js', false);
	$site->registerScript('class', 'class.js', false, ['jquery', 'underscore', 'velocity', 'velocity.ui', 'tooltipster', 'mgGlitch', 'plugins']);
	$site->registerScript('site', 'site.js', false, ['class']);
	$site->enqueueScript('site');

	# General meta tags
	$site->addMeta('utf-8', '', 'charset');
	$site->addMeta('x-ua-compatible', 'ie=edge', 'http-equiv');
	$site->addMeta('viewport', 'width=device-width, initial-scale=1');
	$site->addMeta('keywords', '');
	$site->addMeta('description', $site->getSiteTitle());

	# OpenGraph meta tags
	$site->addMeta('og:type', 'website', 'property');
	$site->addMeta('og:url', $site->urlTo('/'), 'property');
	$site->addMeta('og:title', $site->getPageTitle(), 'property');
	$site->addMeta('og:image', $site->img('branding/site-share.png', false), 'property');
	$site->addMeta('og:description', $site->getSiteTitle(), 'property');
	$site->addMeta('og:site_name', $site->getSiteTitle(), 'property');
	$site->addMeta('og:locale', 'en_US', 'property');

	# Twitter card
	$site->addMeta('twitter:card', 'summary');
	$site->addMeta('twitter:site', '@site_account');
	$site->addMeta('twitter:creator', '@individual_account');
	$site->addMeta('twitter:url', $site->urlTo('/'));
	$site->addMeta('twitter:title', $site->getPageTitle());
	$site->addMeta('twitter:description', $site->getSiteTitle());
	$site->addMeta('twitter:image', $site->img('branding/site-share.png', false));
?>