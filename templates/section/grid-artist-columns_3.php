<?php 

global $wp_query; 

extract( $wp_query->data );

//echo "<pre>"; print_r( $wp_query->data ); echo "</pre>";

$url = !empty( $featured_image ) ? wp_festival()->get_image_link_by_attachment_id( $featured_image, array( 'default' => false ) ) : false;

?>
<div class="artists-list <?php echo $artist_type; ?>" style="<?php echo $url ? "background-image: url( {$url} );" : ''; ?>" >

  <div class="container">

    <header class="row">
      <div class="col-md-12 col-sm-12 text-center">
        <?php if( isset( $title ) && $title ): ?>
          <h3><?php echo $title; ?></h3>
          <span class="hr"></span>
        <?php endif; ?>
        <?php if( isset( $tagline ) && $tagline ): ?>
          <p class="tagline"><?php echo $tagline; ?></p>
        <?php endif; ?>
        <?php if( isset( $description ) && $description ): ?>
          <p class="description"><?php echo $description; ?></p>
        <?php endif; ?>
      </div>
    </header>

    <section class="the-list">
      <div class="row">  
      
        <div class="col-md-4 clearfix">
          <?php if ( !empty ( $column_title_1 ) ) : ?>
            <div class="row">
              <div class="col-md-12 text-center">
                <h3><?php echo $column_title_1; ?></h3>
                <span class="hr"></span>
              </div>
            </div>
          <?php endif; ?>
          <div class="row">
            <div class="col-md-12 clearfix">
              <?php $counter = 0; ?>
              <?php if( have_posts() ) : ?>
                <?php while( have_posts() ) : the_post(); ?>
                  <?php if( isset( $col_position[ get_the_ID() ] ) && $col_position[ get_the_ID() ] != 1 ) { continue; } ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    <div class="row"><div class="row-artists clearfix">
                  <?php endif; ?>
                  <div class="<?php echo $map[0]; ?> <?php echo !( $counter % $artist_columns ) ? $map[1] : ''; ?>">
                    <?php get_template_part( 'templates/article/listing-artist', $artist_type ); ?>
                  </div>
                  <?php $counter++; ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    </div></div>
                  <?php endif; ?>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 clearfix">
          <div class="row">
            <?php if ( !empty ( $column_title_2 ) ) : ?>
              <div class="row">
                <div class="col-md-12 text-center">
                  <h3><?php echo $column_title_2; ?></h3>
                  <span class="hr"></span>
                </div>
              </div>
            <?php endif; ?>
            <div class="col-md-12 clearfix">
              <?php $counter = 0; ?>
              <?php if( have_posts() ) : ?>
                <?php while( have_posts() ) : the_post(); ?>
                  <?php if( isset( $col_position[ get_the_ID() ] ) && $col_position[ get_the_ID() ] != 2 ) { continue; } ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    <div class="row"><div class="row-artists clearfix">
                  <?php endif; ?>
                  <div class="<?php echo $map[0]; ?> <?php echo !( $counter % $artist_columns ) ? $map[1] : ''; ?>">
                    <?php get_template_part( 'templates/article/listing-artist', $artist_type ); ?>
                  </div>
                  <?php $counter++; ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    </div></div>
                  <?php endif; ?>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="col-md-4 clearfix">
          <div class="row">
            <?php if ( !empty ( $column_title_3 ) ) : ?>
              <div class="row">
                <div class="col-md-12 text-center">
                  <h3><?php echo $column_title_3; ?></h3>
                  <span class="hr"></span>
                </div>
              </div>
            <?php endif; ?>
            <div class="col-md-12 clearfix">
              <?php $counter = 0; ?>
              <?php if( have_posts() ) : ?>
                <?php while( have_posts() ) : the_post(); ?>
                  <?php if( isset( $col_position[ get_the_ID() ] ) && $col_position[ get_the_ID() ] != 3 ) { continue; } ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    <div class="row"><div class="row-artists clearfix">
                  <?php endif; ?>
                  <div class="<?php echo $map[0]; ?> <?php echo !( $counter % $artist_columns ) ? $map[1] : ''; ?>">
                    <?php get_template_part( 'templates/article/listing-artist', $artist_type ); ?>
                  </div>
                  <?php $counter++; ?>
                  <?php if ( !( $counter % $artist_columns ) ) : ?>
                    </div></div>
                  <?php endif; ?>
                <?php endwhile; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
      </div>
    </section>
  
  </div>
  
</div>