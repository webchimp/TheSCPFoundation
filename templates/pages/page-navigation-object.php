<?php $site->setPageTitle($scp->item_number . ' | ' . $scp->name . ' - ' . $site->getSiteTitle()); ?>
<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<?php
			$special_containment_procedures = $scp->special_containment_procedures;
			$description = $scp->description;

			$pattern = '/<a href="\/(scp-(?:\d)*)">/';
			$replace = '<a data-scp-tooltip="$1" href="%baseDir%/object/$1">';

			$special_containment_procedures = preg_replace($pattern, $replace, $special_containment_procedures);
			$description = preg_replace($pattern, $replace, $description);

			$pattern = "/<p><strong>((?:Note|Recovery|Additional|Post|Lab|Incident|Note|Additional Notes|Appendix|Reference|Document|Addendum)(?:.*))(?::)?<\/strong>/";

			$special_containment_procedures = preg_replace_callback($pattern, function($m) {
				global $site;
				$m[1] = rtrim($m[1], ':');
				return '<p data-navigation="' . $m[1] . '" id="' . ($site->toAscii($m[1])) . '"><strong>' . $m[1] . ':</strong>';
			}, $special_containment_procedures);
			$description = preg_replace_callback($pattern, function($m) {
				global $site;
				$m[1] = rtrim($m[1], ':');
				return '<p data-navigation="' . $m[1] . '" id="' . ($site->toAscii($m[1])) . '"><strong>' . $m[1] . ':</strong>';
			}, $description);
		?>

		<section>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="the-content">

						<div class="form-group">
							<label class="control-label">Special Containment</label>
							<textarea id="tidy-scp" rows="30" class="input-block form-control"><?php echo $special_containment_procedures; ?></textarea>
						</div>

						<div class="form-group">
							<label class="control-label">Description</label>
							<textarea id="tidy-description" rows="30" class="input-block form-control"><?php echo $description; ?></textarea>
						</div>
					</div>

					<div class="objects-navigation">
						<?php if($prev_scp): ?>
							<a href="<?php $site->urlTo('/object/' . strtolower($prev_scp) . '/tidy', true); ?>" class="object-prev"><i class="fa fa-fw fa-angle-left"></i> <span class="object-title"><?php echo $prev_scp; ?></span></a>
						<?php endif; ?>
						<?php if($next_scp): ?>
							<a href="<?php $site->urlTo('/object/' . strtolower($next_scp) . '/tidy', true); ?>" class="object-next"><span class="object-title"><?php echo $next_scp; ?></span> <i class="fa fa-fw fa-angle-right"></i></a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>

		<?php
			$scp->special_containment_procedures = trim($special_containment_procedures);
			$scp->description = trim($description);
			$scp->version = 2;
			$scp->save();
		?>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>