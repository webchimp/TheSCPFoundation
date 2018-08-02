<?php $common_classes = ['Safe', 'Euclid', 'Keter']; ?>

<div class="tooltip-content scp-tooltip-content">

	<img class="scp-object-class" src="<?php $site->img("template/{$scp->object_class_slug}.svg"); ?>" alt="<?php echo $scp->object_class; ?>">

	<h1 id="item-number" class="scp-title"><?php echo $scp->item_number; ?><?php if($scp->getMeta('item_number_note')): ?><span class="item-number-note"><?php echo $scp->getMeta('item_number_note'); ?></span><?php endif; ?></h1>
	<h2 class="scp-nickname"><?php echo $scp->name; ?></h2>

	<?php if(!in_array($scp->object_class, $common_classes) || $scp->getMeta('esoteric_object_class')): ?>
		<h3 class="scp-esoteric-object-class"><strong>Object Class:</strong> <?php echo $scp->getMeta('esoteric_object_class'); ?></h3>
	<?php endif; ?>

	<div class="the-content scp-content">

		<div class="scp-description">
			<?php if($scp->description): ?>
				<p><?php echo get_excerpt($scp->description, 30); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>