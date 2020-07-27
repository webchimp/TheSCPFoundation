<?php $this->partial('header-html'); ?>
	<?php $this->partial('header'); ?>

		<section>
			<div class="inner boxfix-vert">
				<div class="margins">
					<div class="the-content">

						<?php

							//Initial Information

							$initial_info = '';
							$re = '/<div style="text-align: right;"><div class="page-rate-widget-box">(?:.*)<\/div><\/div>(.*?)<p><strong>Item/is';
							preg_match_all($re, $scp->page->content, $matches, PREG_SET_ORDER, 0);

							if($matches) {

								$initial_info = $matches[0][count($matches[0])-1];
								$initial_info = trim($initial_info);
							}
						?>

						<h2>Initial Info</h2>
						<p><pre><?php echo $initial_info ? htmlentities($initial_info) : 'No initial info'; ?></pre></p>

						<?php

							//Special Containment Procedures

							$special_containment_procedures = '';
							$re = '/<p><strong>(?:Secure |Special )?Containment Pro(?:tocol|cedure)(?:s?)(?::?)<\/strong>(?::?)(.*?)<p><strong>Description/is';
							preg_match_all($re, $scp->page->content, $matches, PREG_SET_ORDER, 0);

							if($matches) {

								$special_containment_procedures = $matches[0][count($matches[0])-1];
								$special_containment_procedures = '<p>' . trim($special_containment_procedures);
							}
						?>
						<h2>Special Containment Procedures</h2>
						<p><pre><?php echo htmlentities($special_containment_procedures); ?></pre></p>

						<?php

							//Description

							$description = '';
							$re = '/Description(?::?)<\/strong>(?::?)(.*?)(?:<div class="footer-wikiwalk-nav">|<div class="page-tags">|<div class="footer wikiwalk nav">|<div id="page-info-break">)/is';
							preg_match_all($re, $scp->page->content, $matches, PREG_SET_ORDER, 0);

							if($matches) {

								$description = $matches[0][count($matches[0])-1];
								$description = '<p>' . trim($description);
							}

							$re = '/(.*?)     <\/div>(?:.*?)wikidot_bottom_300x250(?:.*?)googletag(?:.*?)<\/div>/is';
							preg_match_all($re, $description, $matches, PREG_SET_ORDER, 0);

							if($matches) {

								$description = $matches[0][count($matches[0])-1];
								$description = trim($description);
							}
						?>
						<h2>Description</h2>
						<p><pre><?php echo htmlentities($description); ?></pre></p>

						<?php

							//Addendums and more

							$extras = [
								'addendum' => 'Addendum',
								'document' => 'Document',
								'reference' => 'Reference',
								'appendix' => 'Appendix'
							];

							$description_with_links = $description;

							foreach($extras as $key => $extra) {

								$description_with_links = preg_replace('/<strong>(' . $extra . '(?:.*))?<\/strong>/i', '<strong class="navigation-' . $key . '">$1</strong>', $description_with_links);
							}

							//Toc titles

							$description_with_links = preg_replace('/<(h(?:1|2|3|4|5|6)) (id="toc(?:.*)")>(.*)(<\/h(?:1|2|3|4|5|6)>)/i', '<$1 class="toc-title" $2>$3$4', $description_with_links);
						?>
						<h2>Description with links</h2>
						<p><pre><?php echo htmlentities($description_with_links); ?></pre></p>

						<div class="objects-navigation">
							<?php if($prev_scp): ?>
								<a href="<?php $site->urlTo('/object/' . strtolower($prev_scp) . '/format/?save=1', true); ?>" class="object-prev"><i class="fa fa-fw fa-angle-left"></i> <span class="object-title"><?php echo $prev_scp; ?></span></a>
							<?php endif; ?>
							<?php if($next_scp): ?>
								<a href="<?php $site->urlTo('/object/' . strtolower($next_scp) . '/format/?save=1', true); ?>" class="object-next"><span class="object-title"><?php echo $next_scp; ?></span> <i class="fa fa-fw fa-angle-right"></i></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<?php

			if(get_item($_GET, 'save') && !$scp->version) {

				$scp->special_containment_procedures = $special_containment_procedures;
				$scp->description = $description_with_links;
				$scp->save();

				//if($initial_info) $scp->updateMeta($initial_info);
			}
		?>

	<?php $this->partial('footer'); ?>
<?php $this->partial('footer-html'); ?>