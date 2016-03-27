<div id="portfolio-filter" class="portfolio-filter">
 	<div id="filters" class="button-group">
 	<?php $label = empty($_gettag)? "label-primary" : "label-default"; ?>
 	<a href="<?php echo esc_url( remove_query_arg( 'tag' ) ); ?>"><span class="label tags ng-binding tag-filter <?php echo $label; ?>" class="tag-filter">All</span></a>
 	<?php foreach ($this->hcz_portfolio['tags'] as $tags): ?>
 		<?php $label = in_array($tags['slug'], $_gettag)? "label-primary" : "label-default"; ?>
        <a href="<?php echo add_query_arg( 'tag', $tags['slug'] ); ?>"><span class="label tags ng-binding tag-filter <?php echo $label; ?>" class="tag-filter"><?= $tags['name']; ?></span></a>
    <?php endforeach; ?>
    </div>
</div>