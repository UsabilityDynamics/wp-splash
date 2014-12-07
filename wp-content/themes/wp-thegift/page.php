<?php get_header(); ?>
<div class="post-wrapper">
  <section class="container">
    
    <div id="post-single" class="">
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <div id="post_id_<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
        
          <h1 class="title post-title">
            <?php the_title(); ?>
          </h1>
          <div class="entry post-entry">
          	<?php the_content(); ?>
          </div>

      </div>
      <?php endwhile; else : ?>
        <p><?php _e( 'Sorry, no posts matched your criteria.', 'framework' ) ?></p>
      <?php endif; ?>

    </div>
    
    <?php //get_template_part( 'comments' ); ?>

</section>

</div>

<?php query_posts( 'post_type=post&posts_per_page=10' ); ?>
<?php get_template_part( 'loop' ); ?>

<?php get_footer(); ?>