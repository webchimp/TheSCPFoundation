<?php

	/**
	 * Hummingbird Lite
	 * Version: 	3.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
	 */

	# Define the absolute path
	define( 'BASE_DIR', dirname(__FILE__) );
	# Include required files
	include BASE_DIR . '/framework/config.inc.php';
	include BASE_DIR . '/framework/database.inc.php';
	include BASE_DIR . '/framework/request.inc.php';
	include BASE_DIR . '/framework/response.inc.php';
	include BASE_DIR . '/framework/router.inc.php';
	include BASE_DIR . '/framework/site.inc.php';
	include BASE_DIR . '/framework/utilities.inc.php';

	# Initialize environment
	$site = Site::getInstance();
	$site->initialize($settings);

	# Initialize plugins
	foreach ($site->getPlugins() as $plugin) {
		$file = $site->baseDir("/plugins/{$plugin}/plugin.php");
		include $file;
	}

	# External functions
	include $site->baseDir('/external/functions.inc.php');

	# Do routing
	$site->getRouter()->routeRequest();
?>