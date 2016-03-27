<?php
global $post;
while ( $loop->have_posts() ) : $loop->the_post(); ?>
<?php
$hcz_terms = apply_filters('get_portfolio_tags',array('post_tag'),array( $this->parent->post_type ));
$hcz_project_pics = apply_filters( 'get_portfolio_photos', $this->parent->post_type );
$hcz_project_date       = apply_filters( 'get_portfolio_date', $this->parent->post_type );
$hcz_project_link       = apply_filters( 'get_portfolio_link', $this->parent->post_type );
?>
<h2 class="blog-title-pro"><?php the_title(); ?></h2>
<div class="isotope">
<?php if( !empty( $hcz_terms ) ): ?>
    <div id="portfolio-filter" class="portfolio-filter">
    	<div id="filters" class="button-group">
    		<?php foreach ($hcz_terms as $tags): ?>
                <a href="<?php echo add_query_arg( 'tag', $tags->slug ); ?>"><span class="label tags ng-binding tag-filter label-primary" class="tag-filter"><?= $tags->name; ?></span></a>
            <?php endforeach; ?>
    	</div>
    </div>
<?php endif; ?>
    <div class="row">
    	<div class="col-md-8">
        <?php if( count($hcz_project_pics) > 0 ){ ?>
            <ul id="testimonal" class="bxslider testimonal-slider">
                <?php foreach ($hcz_project_pics as $image) { ?>
                    <li><img src="<?php echo $image['hcz_image_url']; ?>" alt="<?php echo ucwords(get_the_title()); ?>"></li>
                <?php } ?>
                </li>
            </ul>
            <?php }else{ ?>
              <img src="<?php echo apply_filters('get_portfolio_photo', $this->parent->post_type ); ?>" alt="<?php echo ucwords(get_the_title()); ?>" class="img-responsive">
            <?php } ?>
    	</div>
    	<div class="col-md-4">
    	<?php if( $hcz_project_date || $hcz_project_link): ?>
    		<h4 class="home-subtitle">Project Stats</h4>
    		<ul class="project-stats">
    		<?php if($hcz_project_date): ?>
    			<li><span class="stat"><i class="fa fa-calendar-check-o"></i> Started On: </span> <?= $hcz_project_date; ?></li>
    		<?php endif; ?>
    		<?php if($hcz_project_link): ?>
    			<li><span class="stat"><i class="fa fa-external-link"></i> Link: </span> <a href="<?= $hcz_project_link; ?>"><?= $hcz_project_link; ?></a></li>
    		<?php endif; ?>
    		</ul>
    	<?php endif; ?>
    	</div>
    </div>
    <?php if($post->post_content!==""): ?>
    <div class="row">
    	<div class="col-md-12">
    		<h4 class="home-subtitle">About the project</h4>
    		<?php the_content(); ?>
    	</div>
    </div>  
    <?php endif; ?>  
</div>
<?php endwhile; ?>
<?php wp_reset_query(); 
?>