<div class="featured-realty-items-wrapper" data-show="<?php echo $params->get('items_to_show'); ?>">
	<div class="prev"></div>
	<div class="next"></div>
	<?php foreach ($items as $index => $item): ?>		
		<a class="featured-realty-item-link" href="<?php echo $link.$item->unik_kode_type ?>" title="">
			<div class="featured-realty-item-wrapper">
				<div class="featured-realty-item-image-wrapper">
					<img 
						data-src="<?php echo ($helper->getItemImage($item, $params->get('base_images_folder'))) ? $helper->getItemImage($item, $params->get('base_images_folder')) : $params->get('default_image'); ?>" 
						alt="" 
						class="item-image" />
				</div>
				<div class="featured-realty-data-wrapper">
					<?php if($params->get('show_address')): ?>
						<div class="featured-realty-item-address-wrapper">
							<div class="featured-realty-item-address">
								<?php echo $item->reklama ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if($params->get('show_date_add')): ?>
						<div class="featured-realty-item-add-time-wrapper">
							<div class="featured-realty-item-add-time">
								<?php echo JText::_('MOD_FEATURED_REALTY_TIME_ADDED').' '.date('Y-m-d', strtotime($item->date_v)); ?>
							</div>
						</div>
					<?php endif; ?>
					<div class="featured-realty-item-meta-data-wrapper">
						<?php if($item->room_count): ?>
							<div class="featured-realty-item-rooms-number-wrapper">
								<div class="featured-realty-item-rooms-number-text-wrapper">
									<div class="featured-realty-item-rooms-number-text">
										<?php echo JText::_('MOD_FEATURED_REALTY_ROOMS_NUMBER')?>
									</div>
								</div>

								<div class="featured-realty-item-rooms-number-info-wrapper">
									<span class="featured-realty-item-rooms-number-icon">
										<img src="<?php echo '/'.$params->get('rooms_number_icon') ?>" alt="">
									</span>
									<span class="featured-realty-item-rooms-number">
										<?php echo $item->room_count ?>
									</span>								
								</div>
							</div>
						<?php endif; ?>
						<?php if($item->ploshad): ?>
							<div class="featured-realty-item-interior-area-wrapper">
								<div class="featured-realty-item-interior-text-wrapper">
									<div class="featured-realty-item-interior-area-text">
										<?php echo JText::_('MOD_FEATURED_REALTY_INTERIOR_AREA'); ?>
									</div>
								</div>
								<div class="featured-realty-item-interior-area-info-wrapper">
									<span class="featured-realty-item-interior-area-icon">
										<img src="<?php echo '/'.$params->get('interior_area_icon') ?>" alt="">
									</span>
									<span class="featured-realty-item-interior-area">
										<?php echo $item->ploshad ?>
										<span class="interior-area-type">
											<?php echo JText::_('MOD_FEATURED_REALTY_DIMENSION'); ?>
										</span>
									</span>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<div class="featured-realty-item-type-wrapper">
						<div class="realty-item-type">
							<?php echo JText::_('MOD_FEATURED_REALITY_OPERATION_TYPE_'.$item->id); ?>
						</div>
					</div>
					<div class="featured-realty-item-price-wrapper">
						<span class="featured-realty-item-price">
							<?php echo $item->price ?>
						</span>
						<span class="featured-realty-item-currency">
							<?php echo JText::_('MOD_FEATURED_REALTY_'.$item->valuta.'_SYMBOL'); ?>
						</span>
					</div>
				</div>
			</div>
		</a>
	<?php endforeach ?>
</div>