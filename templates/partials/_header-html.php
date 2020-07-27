<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $site->getPageTitle(); ?></title>
	<?php $site->metaTags(); ?>
	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php $site->img('branding/favicon.ico'); ?>?v=1">
	<link rel="icon" href="<?php $site->img('branding/favicon-md.png'); ?>?v=1" type="image/png">
	<!-- Device-specific icons -->
	<link rel="apple-touch-icon" href="<?php $site->img('branding/favicon-sm.png'); ?>?v=1" />
	<link rel="apple-touch-icon" sizes="72x72" href="<?php $site->img('branding/favicon-md.png'); ?>?v=1" />
	<link rel="apple-touch-icon" sizes="114x114" href="<?php $site->img('branding/favicon-lg.png'); ?>?v=1" />
	<!-- Stylesheets -->
	<?php $site->includeStyles(); ?>
	<?php $site->executeHook('template.htmlHeader'); ?>
</head>
<body class="<?php $site->bodyClass() ?>">