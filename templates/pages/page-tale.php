<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<nav class="site-navigation">
		</nav>
		<section>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="tale-content page-content">

						<h1 class="page-title tale-title"><?php echo $tale->title; ?></h1>
						<div class="the-content">
							<?php echo str_replace('%baseDir%', $site->baseUrl(''), $tale->content); ?>
						</div>
					</div>
				</div>
			</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>