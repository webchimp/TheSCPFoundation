<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<section class="section section-error">
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="the-content">
						<h1 class="section-title">Boop!</h1>
						<h3>The page you're looking for isn't here.</h3>
						<p>You may want to go <a href="<?php $site->urlTo('/', true); ?>">back to the home page</a>.</p>
						<p><small class="text-muted">SERVER RESPONSE: 404 ERROR</small></p>
					</div>
				</div>
			</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>