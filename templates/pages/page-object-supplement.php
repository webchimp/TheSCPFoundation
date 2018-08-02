<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<nav class="site-navigation">
		</nav>
		<section>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="supplement-content page-content">

						<h1 class="page-title supplement-title"><?php echo $supplement->title; ?></h1>
						<div class="the-content">
							<?php echo str_replace('%baseDir%', $site->baseUrl(''), $supplement->content); ?>
						</div>
					</div>
				</div>
			</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>