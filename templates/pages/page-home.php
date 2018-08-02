<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<div class="video">
			<video loop="loop" muted="" autoplay="autoplay" playsinline="playsinline">
				<source src="<?php $site->urlTo('/assets/video/particles.mp4', true); ?>">
			</video>
		</div>

		<section class="intro">
			<div class="glitch"></div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>