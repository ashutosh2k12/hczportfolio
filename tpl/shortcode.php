<div class="isotope" id="hcz-portfolio-grid">
<?php
while ( $loop->have_posts() ) : $loop->the_post(); ?>
	<div class="col-lg-4 animated bounceIn">
	<div class="card">
			<div class="card-height-indicator"></div>
			<div class="card-content text-center">
                <a class="image" href="<?php echo get_permalink(); ?>" rel="prettyPhoto[main]" target="_blank" title="<?php echo ucwords(get_the_title()); ?>">
                	<div class="card-image">
                            
                        <img src="<?php echo apply_filters('get_portfolio_photo', $this->parent->post_type ); ?>" alt="<?php echo ucwords(get_the_title()); ?>">
                        <h3 class="card-image-headline-custom"><?php echo ucwords(get_the_title()); ?></h3>
                        
                    </div>
                </a>
                <div class="card-body">
                    <div class="tag-holder">
                   
                    <?php foreach (wp_get_post_terms( get_the_ID() ,'post_tag' ) as $term) : ?>
                    	<a href="<?php echo add_query_arg( 'tag', $term->slug); ?>"><span class="tags label label-danger"><?= $term->name ?></span></a>
                    <?php endforeach; ?>
                    </div>
                    <span><?php echo get_the_date(); ?></span>
                </div>

                <footer class="card-footer">
                    <?php if(get_permalink()): ?>
                    <a href="<?php echo get_permalink() ?>" class="btn btn-sm btn-raised btn-warning" target="_blank"><i class="fa fa-external-link"></i> View</a>
                    <?php endif; ?>
                </footer>
            </div>
            </div>
        </div>
        <?php endwhile; ?>
        <div class="clearfix" style="position: absolute; display: none;"></div>
    </div>
    <?php wp_reset_query(); 
?>