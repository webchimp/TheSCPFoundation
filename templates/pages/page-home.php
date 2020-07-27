<?php
	$site->registerScript('glitch', 'glitch.js', false);
	$site->enqueueScript('glitch');
?>
<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<div class="glitch-wrapper">
			<div class="glitch"></div>
		</div>

		<section class="intro">
				<div class="warning">
					<h2 class="warning-title animable">WARNING: THE FOUNDATION DATABASE IS</h2>
					<h1 class="warning-classified animable">CLASSIFIED</h1>
					<p class="warning-subtitle animable">ACCESS BY UNAUTHORIZED PERSONNEL IS STRICTLY PROHIBITED PERPETRATORS WILL BE TRACKED, LOCATED, AND DETAINED</p>
					<p class="warning-cta animable"><a href="#" class="button button-outline">Access database</a></p>
				</div>

				<div class="login hide">
					<img class="login-logo animable" src="<?php $site->img('template/scp-logo-white.png'); ?>" alt="SCP Foundation">
					<canvas class="login-title animable" id="glitch-canvas" width="200" height="40"></canvas>
					<h2 class="login-subtitle animable">Enter credentials</h2>
					<form class="login-credentials animable" action="<?php $site->urlTo('/directory/series/1', true); ?>">
						<input type="password" maxlength="8" autocomplete="off">
						<button type="submit"><i class="fa fa-power-off"></i></button>
					</form>
				</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>