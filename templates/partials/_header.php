	<header class="site-header">
		<a class="hamburger js-toggle-site-navigation"><i class="fa fa-bars"></i></a>
		<div class="hamburger-title"></div>
		<img class="site-logo" src="<?php $site->img('template/scp-logo.svg'); ?>" alt="SCP">

		<nav class="header-navigation">
			<ul class="menu">
				<li class="menu-item"><a href="<?php $site->urlTo('/directory/series/1', true); ?>"><i class="fa fa-fw fa-folder"></i></a></li>
				<li class="menu-item"><a href="#"><i class="fa fa-fw fa-share-alt"></i></a></li>
				<li class="menu-item"><a href="#"><i class="fa fa-fw fa-heart"></i></a></li>
				<li class="menu-item"><a href="#"><i class="fa fa-fw fa-book"></i></a></li>
				<li class="menu-item"><a class="js-open-search" href="#"><i class="fa fa-fw fa-search"></i></a></li>
			</ul>
		</nav>
	</header>

	<div class="overlay-close"></div>
	<div class="search">
		<input type="text" id="search-query" autocomplete="off" class="search-input" placeholder="Search SCP">
		<div class="search-results"><!-- --></div>
	</div>

	<script type="text/template" id='search-result'>
		<article class="search-result">
			<a data-scp-tooltip="<%= object.item_number.toLowerCase() %>" data-side="left" data-max-width="320" class="result-link" href="<%= constants.siteUrl + 'object/' + object.item_number.toLowerCase() %>"></a>
			<h2 class="result-title"><%= object.item_number %> <small><%= object.name %></small></h2>
			<img class="result-class" src="<%= constants.siteUrl + 'assets/images/template/' + object.object_class.toLowerCase() + '.svg' %>" alt="">
		</article>
	</script>