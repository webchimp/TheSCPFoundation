<?php $site->setPageTitle($scp->item_number . ' | ' . $scp->name . ' - ' . $site->getSiteTitle()); ?>
<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<?php $common_classes = ['Safe', 'Euclid', 'Keter']; ?>

		<nav class="site-navigation">
			<ul class="menu">
				<li class="menu-item"><a href="#item-number"><?php echo $scp->item_number; ?></a></li>
				<li class="menu-item">
					<a href="#special-containment-procedures">Special Containment Procedures</a>
					<ul class="submenu navigation-special-containment-procedures"></ul>
				</li>
				<li class="menu-item">
					<a href="#description">Description</a>
					<ul class="submenu navigation-description"></ul>
				</li>
			</ul>
		</nav>
		<section>
			<div class="scp-article-cover page-cover" <?php if($cover = $scp->getMeta('cover')): ?>style="background-image: url('<?php  $site->urlTo("/files/" . strtolower($scp->item_number) . "/{$cover}", true); ?>');"<?php endif; ?>></div>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="scp-article page-content <?php if($cover): ?>has-cover<?php endif; ?>">

						<img class="scp-object-class" src="<?php $site->img("template/{$scp->object_class_slug}.svg"); ?>" alt="<?php echo $scp->object_class; ?>">

						<h1 id="item-number" class="scp-title"><?php echo $scp->item_number; ?><?php if($scp->getMeta('item_number_note')): ?><span class="item-number-note"><?php echo $scp->getMeta('item_number_note'); ?></span><?php endif; ?></h1>
						<h2 class="scp-nickname"><?php echo $scp->name; ?></h2>

						<?php if(!in_array($scp->object_class, $common_classes) || $scp->getMeta('esoteric_object_class')): ?>
							<h3 class="scp-esoteric-object-class"><strong>Object Class:</strong> <?php echo $scp->getMeta('esoteric_object_class'); ?></h3>
						<?php endif; ?>

						<?php if($relations_html): ?><p class="scp-relations"><span><i class="fa fa-fw fa-link"></i> Connected to:</span> <?php echo $relations_html; ?></p><?php endif; ?>

						<?php if($special_content = $scp->getMeta('special_content')): ?>

							<div class="the-content scp-content">
								<?php echo str_replace('%baseDir%', $site->baseUrl(''), $special_content); ?>
							</div>

						<?php else: ?>

							<?php if($scp->getMeta('pre_restricted_content')): ?>
								<div class="the-content scp-content scp-pre-restricted-content">
									<?php echo str_replace('%baseDir%', $site->baseUrl(''), $scp->getMeta('pre_restricted_content')); ?>
								</div>
							<?php endif; ?>

							<div class="the-content scp-content <?php echo $scp->getMeta('restricted_access') ? 'is-restricted' : ''; ?>">

								<?php if($scp->getMeta('initial_content')): ?>
									<div class="the-content scp-content scp-initial-content">
										<?php echo str_replace('%baseDir%', $site->baseUrl(''), $scp->getMeta('initial_content')); ?>
									</div>
								<?php endif; ?>

								<?php if($scp->special_containment_procedures): ?>
									<div class="scp-special-containment-procedures">

										<?php if($featured_image = $scp->getMeta('image')): ?>
											<div class="scp-image-block block-right">
												<img src="<?php $site->urlTo('/temp/' . $featured_image, true); ?>" class="scp-image">

												<?php if($featured_image_caption = $scp->getMeta('image_caption')): ?>
													<div class="scp-image-caption">
														<p><?php echo $featured_image_caption; ?></p>
													</div>
												<?php endif; ?>
											</div>
										<?php endif; ?>

										<h2 id="special-containment-procedures">Special Containment Procedures</h2>

										<?php echo str_replace('%baseDir%', $site->baseUrl(''), $scp->special_containment_procedures); ?>
									</div>
								<?php endif; ?>

								<aside class="scp-sidebar">
									<?php if($scp->object_class): ?>
										<div class="scp-tag">
											<img src="<?php $site->img("template/{$scp->object_class_slug}.svg"); ?>" alt="<?php echo $scp->object_class; ?>">
											<span><?php echo $scp->object_class; ?></span>
										</div>
									<?php endif; ?>

									<?php if($scp->tags): foreach($scp->tags as $tag): if($tag->slug == $scp->object_class_slug) continue; ?>

										<div class="scp-tag">
											<img src="<?php $site->img("template/tags/{$tag->slug}.svg"); ?>" alt="<?php echo $tag->name; ?>">
											<span><?php echo $tag->name; ?></span>
										</div>

									<?php endforeach; endif; ?>
								</aside>

								<div class="scp-description">
									<?php echo $scp->getMeta('alternative_content') ? str_replace('%baseDir%', $site->baseUrl(''), $scp->getMeta('alternative_content')) : ''; ?>
									<?php if($scp->description): ?>
										<h2 id="description">Description</h2>
										<?php echo str_replace('%baseDir%', $site->baseUrl(''), $scp->description); ?>
									<?php endif; ?>
								</div>
							</div>

						<?php endif; ?>

						<div class="objects-navigation">
							<?php if($prev_scp): ?>
								<a href="<?php $site->urlTo('/object/' . strtolower($prev_scp), true); ?>" class="object-prev"><i class="fa fa-fw fa-angle-left"></i> <span class="object-title"><?php echo $prev_scp; ?></span></a>
							<?php endif; ?>
							<?php if($next_scp): ?>
								<a href="<?php $site->urlTo('/object/' . strtolower($next_scp), true); ?>" class="object-next"><span class="object-title"><?php echo $next_scp; ?></span> <i class="fa fa-fw fa-angle-right"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>