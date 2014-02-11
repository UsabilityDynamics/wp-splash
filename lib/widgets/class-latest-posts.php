<?php
/**
 * Name: Latest Posts Widgets
 * Description: HDDP Latest Posts Widget
 * Author: Usability Dynamics, Inc.
 * Version: 3.1.0
 *
 */
namespace UsabilityDynamics\Disco\Widget {

  if( !class_exists( 'UsabilityDynamics\Disco\Widget\Latest_Posts' ) ) {

    /**
     * HDDP Latest Posts Widget
     *
     * @author potanin@UD
     */
    class Latest_Posts extends \WP_Widget {

      /**
       * Initialize the widget.
       *
       */
      function __construct() {

        parent::__construct(
          'hdp_latest_posts',
          __( 'HDP Latest Posts', 'hdp' ),
          array(
            'classname'   => 'hdp_latest_posts',
            'description' => __( 'Display latest posts with a coda-esque slider effect.', 'hdp' )
          ),
          array(
            'width' => 300
          )
        );

      }

      /**
       * Renders the widget on the front-end.
       *
       */
      function widget( $args, $instance ) {
        global $hdp, $wpdb;

        extract( $args );

        if( empty( $instance ) ) {
          return;
        }

        $settings = array();

        foreach( (array) get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
          if( isset( $instance[ $post_type->name ] ) && $instance[ $post_type->name ] == 'true' ) {
            $settings[ 'post_types' ][ ] = $post_type->name;
          }
        }

        $posts = array();

        foreach( ( isset( $settings[ 'post_types' ] ) && is_array( $settings[ 'post_types' ] ) ? $settings[ 'post_types' ] : array() ) as $pt ) {
          $params = array();

          $params[ 'post_type' ]      = $pt;
          $params[ 'posts_per_page' ] = 12;

          if( !empty( $instance[ 'taxonomies' ] ) ) {
            if( !empty( $instance[ 'taxonomies' ][ $pt ] ) ) {
              $params[ 'tax_query' ] = array( 'relation' => 'OR' );
              foreach( $instance[ 'taxonomies' ][ $pt ] as $taxonomy => $ids ) {
                $params[ 'tax_query' ][ ] = array(
                  'taxonomy' => $taxonomy,
                  'field'    => 'id',
                  'terms'    => $ids,
                  'operator' => 'IN'
                );
              }
            }
          }

          $query = new \WP_Query( $params );

          foreach( $query->posts as $post ) {
            array_push( $posts, $post );
          }

        }

        if( !count( $posts ) ) {
          return;
        }

        $slides = array_chunk( $posts, 3 );

        $html[ ] = $before_widget;

        if( $instance[ 'title' ] ) {
          $html[ ] = $before_title . $instance[ 'title' ] . $after_title;
        }

        $html[ ] = '<div class="controls_container"></div>';

        $html[ ] = '<div class="hdp_lp_slides_container slides_container"><ul class="slides clearfix">';

        foreach( $slides as $count => $slide ) {
          $html[ ] = '<li class="hdp_lp_slide_count_' . $count . '"><div class="hdp_lp_slide hdp_lp_slide_count_' . $count . '" hdp_lp_slide_count="' . $count . '">';

          foreach( $slide as $post ) {
            //$post = get_post( $post_id );

            $post->thumbnail_src = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );

            $margin_left = ( isset( $post->thumbnail_src ) && $post->thumbnail_src && !empty( $post->thumbnail_src ) ? 'hdp_lp_ml' : '' );

            $html[ ] = '<ul class="hdp_lp_single_post hdp_lp_single_type-' . $post->post_type . '">';

            if( $margin_left ) {
              $html[ ] = '<li class="hdp_lp_thumbnail sidebar_thumbnail">';
              if( flawless_image_link( get_post_thumbnail_id( $post->ID ), 'sidebar_thumb' ) ) {
                $html[ ] = '<a href="' . get_permalink( $post->ID ) . '"><img class="hdp_lp_thumbnail" src="' . flawless_image_link( get_post_thumbnail_id( $post->ID ), 'sidebar_thumb' ) . '" /></a>';
              }
              $html[ ] = '</li>';
            }

            $html[ ] = '<li class="hdp_lp_post_title ' . $margin_left . '"><a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a></li>';

            $html[ ] = '<li class="hdp_lp_tagline hdp_lp_excerpt ' . $margin_left . '">' . $post->post_excerpt . '</li>';

            $html[ ] = '</ul>';
          }

          $html[ ] = '</div></li>';

        }

        $html[ ] = '</ul></div>';

        $html[ ] = '<script type="text/javascript">jQuery(document).ready(function(){ if( typeof jQuery.fn.flexslider == "function" ) { jQuery("#' . $widget_id . ' .hdp_lp_slides_container").css( "visibility", "hidden" ).flexslider({controlsContainer: "#' . $widget_id . ' .controls_container",animation: "slide", animationSpeed: 500, prevText: "", nextText: "", start: function( slider ) { slider.flexAnimate( 1 ); setTimeout( function() { slider.css( "visibility", "visible" ); }, 500 ); }}); } } );</script>';

        $html[ ] = $after_widget;

        echo implode( '', (array) $html );

      }

      /**
       * Handles any special functions when the widget is being updated.
       *
       * @since 3.0.0
       */
      function update( $new_instance, $old_instance ) {
        return $new_instance;
      }

      /**
       * Renders widget UI in control panel.
       *
       */
      function form( $instance = false ) {
        global $hdp;

        if( $this ) {
          $this_here = $this;
        }

        ?>

        <p>
        <input class="widefat" placeholder="<?php _e( 'Widget Title' ); ?>" id="<?php echo $this_here->get_field_id( 'title' ); ?>" name="<?php echo $this_here->get_field_name( 'title' ); ?>" type="text" value="<?php echo isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : ''; ?>"/>
        </p>

        <?php foreach( (array) get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) { ?>
        <p>
          <label>
            <input type="checkbox" <?php checked( isset( $instance[ $post_type->name ] ) ? $instance[ $post_type->name ] : null, 'true' ) ?> id="<?php echo $this_here->get_field_id( $post_type->name ); ?>" name="<?php echo $this_here->get_field_name( $post_type->name ); ?>" type="checkbox" value="true"/>
            <?php echo $post_type->labels->name; ?>
          </label>
        </p>
          <?php
          /**
           * @author korotkov@ud
           */
          switch( $post_type->name ) {

            case 'post':
              $categories = get_terms( 'category' );

              if( !empty( $categories ) ) {

                ?>
                <p><strong><?php _e( 'Categories' ); ?></strong></p>
                <div style="padding:10px;height:200px;overflow-y:scroll;border:1px solid #ccc;">
                <?php

                foreach( (array) $categories as $term ): ?>
                  <p>
                  <label>
                    <input type="checkbox" <?php echo isset( $instance[ 'taxonomies' ][ $post_type->name ] ) && in_array( $term->term_id, (array) $instance[ 'taxonomies' ][ $post_type->name ][ 'category' ] ) ? 'checked="checked"' : ''; ?> name="<?php echo $this_here->get_field_name( 'taxonomies' ); ?>[<?php echo $post_type->name; ?>][category][]" value="<?php echo $term->term_id; ?>"/>
                    <?php echo $term->name; ?>
                  </label>
                </p>
                <?php
                endforeach;

                ?></div><?php
              }

              break;

            default:
              break;
          }

        }

      }

    }

  }

}




