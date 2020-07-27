<?php $site->setPageTitle($scp->item_number . ' | ' . $scp->name . ' - ' . $site->getSiteTitle()); ?>
<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<?php
			require $site->baseDir('/external/lib/htmLawed.php');
			$special_containment_procedures = $scp->special_containment_procedures;
			$description = $scp->description;

			$config = [];
			$config['tidy'] = '1t';
			$config["make_tag_strict"] = true;

			$processed_special_containment_procedures = htmLawed($special_containment_procedures, $config);
			$processed_description = htmLawed($description, $config);

			$pattern = '/<a href="\/(scp-(?:\d)*)">/';
			$replace = '<a data-scp-tooltip="$1" href="%baseDir%/object/$1">';

			$processed_special_containment_procedures = preg_replace($pattern, $replace, $processed_special_containment_procedures);
			$processed_description = preg_replace($pattern, $replace, $processed_description);

			$pattern = '/ onclick="WIKIDOT.page.utils.scrollToReference\(\'footnote(?:ref)?-(?:\d*)\'\)"/';
			$replace = '';

			$processed_special_containment_procedures = preg_replace($pattern, $replace, $processed_special_containment_procedures);
			$processed_description = preg_replace($pattern, $replace, $processed_description);

			$pattern = '<div class="footnotes-footer">';
			$replace = '<div data-navigation="Footnotes" id="footnotes" class="footnotes-footer">';

			$processed_special_containment_procedures = str_replace($pattern, $replace, $processed_special_containment_procedures);
			$processed_description = str_replace($pattern, $replace, $processed_description);
		?>

		<section>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="the-content">

						<div class="form-group">
							<label class="control-label">Special Containment</label>
							<textarea id="tidy-scp" rows="30" class="input-block form-control"><?php echo $processed_special_containment_procedures; ?></textarea>
						</div>

						<div class="form-group">
							<label class="control-label">Description</label>
							<textarea id="tidy-description" rows="30" class="input-block form-control"><?php echo $processed_description; ?></textarea>
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

			if(!$scp->version) {

				$scp->special_containment_procedures = trim($processed_special_containment_procedures);
				$scp->description = trim($processed_description);
				$scp->version = 1;
				$scp->save();
			}
		?>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>