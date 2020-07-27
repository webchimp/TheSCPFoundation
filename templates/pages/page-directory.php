<?php $site->setPageTitle('SCP Database - ' . $site->getSiteTitle()); ?>
<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<nav class="site-navigation">
			<h2 class="navigation-title">Series I - Directory</h2>
			<ul class="menu">
				<?php foreach($series as $index => $chunk): ?>
					<?php
						$start = ($index*100) + (!$index ? 1 : 0);
						$end = ($index*100)+99;
					?>
					<li class="menu-item"><a href="#chunk<?php echo $index; ?>"><?php echo str_pad($start, 3, '0', STR_PAD_LEFT); ?> to <?php echo str_pad($end, 3, '0', STR_PAD_LEFT); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<section>
			<div class="inner">
				<div class="margins">
					<div class="series-list">
						<?php foreach($series as $index => $chunk): ?>
							<?php
								$start = ($index*100) + (!$index ? 1 : 0);
								$end = ($index*100)+99;
							?>
							<div class="series-chunk">
								<h2 class="chunk-title" id="chunk<?php echo $index; ?>"><?php echo str_pad($start, 3, '0', STR_PAD_LEFT); ?> to <?php echo str_pad($end, 3, '0', STR_PAD_LEFT); ?></h2>
								<?php foreach($chunk as $scp): ?>
									<?php
										if($scp->item_number == 'scp-000') continue;
										$object_class_slug = $scp->object_class ? $site->toAscii($scp->object_class) : '';
									?>
									<article class="object">
										<p><a href="<?php $site->urlTo('/object/' . strtolower($scp->item_number), true); ?>"><?php echo $scp->item_number; ?> <small><?php echo $scp->name; ?></small></a></p>
										<?php if($object_class_slug): ?><img class="scp-object-class" src="<?php $site->img("template/{$object_class_slug}.svg"); ?>" alt="<?php echo $scp->object_class; ?>"><?php endif; ?>
									</article>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>