<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\Entity' ) ) {

    /**
     * Post Object Util
     */
    class Entity {

      /**
       *
       * @var type
       */
      public $_id;

      /**
       *
       * @var type
       */
      public $_post;

      /**
       *
       * @var type
       */
      public $_meta;

      /**
       *
       * @var type
       */
      public $_taxonomies;

      /**
       * Init
       * @param type $id
       */
      public function __construct( $id = null ) {

        if ( !$id ) {
          $id = get_the_ID();
        }

        $this->_id   = $id;

        $this->_post = $this->load_post();

        $this->_meta = $this->load_meta();

        $this->_taxonomies = $this->load_taxonomies();

      }

      /**
       * Load post data
       * @return type
       */
      private function load_post() {
        return get_post( $this->_id );
      }

      /**
       * Load meta data
       * @return type
       */
      private function load_meta() {
        return get_metadata( 'post', $this->_id );
      }

      /**
       * Load taxonomies
       * @return boolean
       */
      private function load_taxonomies() {

        $_taxonomies = get_post_taxonomies( $this->_id );

        $_return = array();

        if ( !empty( $_taxonomies ) ) {

          foreach( (array)$_taxonomies as $taxonomy_slug ) {

            $_return[$taxonomy_slug] = wp_get_post_terms( $this->_id, $taxonomy_slug );

          }

          if ( !empty( $_return ) ) return $_return;

        }

        return false;

      }

      /**
       * Getter for meta
       * @param type $key
       * @param null $value
       * @return type
       */
      public function meta( $key = null, $value = null ) {

        //die( '<pre>' . print_r( $this->_meta, true ) . '</pre>');
        //echo "\n {$key} -> {$value}";

        if ( !$key ) {
          return $this->_meta;
        }

        if ( !$value ) {
          if ( count( $this->_meta[ $key ] ) > 1 ) {
            return $this->_meta[ $key ];
          } elseif ( !count( $this->_meta[ $key ] ) ) {
            return false;
          }
          return maybe_unserialize( $this->_meta[ $key ][0] );
        }

        $this->_meta[ $key ] = array( $value );
      }

      /**
       * Return type of current object
       * @return type
       */
      public function type() {
        return $this->_type;
      }

      /**
       * Return post data by field
       * @param type $field
       * @return type
       */
      public function post( $field, $microdata_args = false ) {
        $ret = $this->_post->{$field};

        if ( is_array( $microdata_args ) ) {
          $special_args = array(
            'fields'      => array(),
            'origin'      => __FUNCTION__,
          );
          $special_args['fields'][ $field ] = $ret;

          return $this->microdataHandler( array_merge( $special_args, $microdata_args ) );
        }

        return $ret;
      }

      /**
       * Return taxonomies by parameters
       * @param type $slug
       * @param \DiscoDonniePresents\type|string $format
       * @param \DiscoDonniePresents\type|string $separator
       * @return boolean
       */
      public function taxonomies( $slug, $format = 'link', $separator = ', ', $microdata_args = false ) {

        if ( empty( $this->_taxonomies[ $slug ] ) ) return false;

        switch( $format ) {

          case 'link':

            return $this->termsToString( $slug, $this->_taxonomies[ $slug ], $separator, $microdata_args );

            break;

          case 'raw':

            return $this->_taxonomies[ $slug ];

            break;

          case 'elasticsearch':

            $_return = array();

            foreach( $this->_taxonomies[ $slug ] as $_term ) {
              $_return[] = array(
                'name'      => $_term->name,
                'url'       => get_term_link( $_term->slug, $slug ),
              );
            }

            return $_return;

            break;

          default: break;

        }

        return false;
      }

      /**
       * Load images for current object
       * @return type
       */
      public function load_images() {

        $args = array(
            'post_mime_type' => 'image',
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $this->_id,
            'exclude' => get_post_thumbnail_id( $this->_id )
        );

        return get_posts($args);

      }

      /**
       * Load events for current meta if exist
       * @param array|\DiscoDonniePresents\type $options
       * @return \DiscoDonniePresents\Event|boolean
       */
      public function load_events( $options = array() ) {

        switch( $options['period'] ) {
          case 'upcoming':
            $period = array(
                'key' => 'dateStart',
                'value' => date( 'Y-m-d H:i' ),
                'compare' => '>=',
                'type' => 'DATE'
            );
            break;
          case 'past':
            $period = array(
                'key' => 'dateStart',
                'value' => date( 'Y-m-d H:i' ),
                'compare' => '<',
                'type' => 'DATE'
            );
            break;
          default:
            $period = array();
            break;
        }

        $args = wp_parse_args( $args, array(
          'post_type' => 'event',
          'posts_per_page' => -1,
          'meta_query' => array(
              array(
                  'key' => $this->_meta_key,
                  'value' => $this->_id
              ),
              $period
          )
        ) );

        $_events = array();

        $query = new \WP_Query( $args );

        if ( !is_wp_error( $query ) && !empty( $query->posts ) ) {

          foreach( $query->posts as $event ) {
            $_events[] = new Event( $event->ID, false );
          }

          return $_events;

        }

        return false;

      }

      /**
       * Return events if exist
       * @param array|\DiscoDonniePresents\type $args
       * @return type
       */
      public function events( $args = array() ) {

        if ( empty( $this->_events ) ) {
          $this->_events = $this->load_events( $args );
        }

        return $this->_events;
      }

      /**
       * Convert terms to string and return
       * @param type $slug
       * @param type $terms
       * @param type $separator
       * @return boolean
       */
      protected function termsToString( $slug, $terms, $separator, $microdata_args = false ) {
        $links = array();

        if ( count( $terms ) == 0 ) return false;

        foreach ( $terms as $term ) {
          if ( is_array( $microdata_args ) ) {
            $special_args = array(
              'fields'      => array(),
              'origin'      => __FUNCTION__,
              'super_type'  => $slug,
            );
            $special_args['fields']['name'] = $term->name;
            $special_args['fields']['url'] = get_term_link( $term->slug, $slug );

            $links[] = $this->microdataHandler( array_merge( $special_args, $microdata_args ) );
          } else {
            $links[] = '<a href="'.get_term_link( $term->slug, $slug ).'">'.$term->name.'</a>';
          }
        }

        return implode( $separator, $links );
      }

      /**
       * TODO: This function is still a work in progress. It will be responsible for outputting microdata.
       * @param  array $microdata_args an array of microdata arguments
       * @return string the HTML string with microdata included
       */
      protected function microdataHandler( $microdata_args = array() ) {
        if ( !is_array( $microdata_args ) ) {
          return '';
        }

        extract( wp_parse_args( $microdata_args, array(
          'build_mode'        => '',
          'fields'            => array(),
          'origin'            => '',
          'super_type'        => null,
          'super_prop'        => null,
          'super_super_type'  => null,
        ) ) );

        if ( !is_array( $fields ) || count( $fields ) == 0 ) {
          return '';
        }

        if ( $build_mode == '' ) {
          $build_mode = 'text';
          if ( in_array( $origin, array( 'termsToString' ) ) ) {
            $build_mode = 'link';
          } /*elseif ( in_array( $origin, array( '...' ) ) ) {
            $build_mode = 'image';
          }*/
        }

        $output = '';

        $super_open = $super_close = '';
        if ( $super_type != null ) {
          $super_type = ucfirst( $super_type );
          if ( true /* ( $super_type = get_valid_type( $super_type ) ) != null */ ) {
            $super_open = '<span';
            if ( $super_prop != null ) {
              if ( true /* ( $super_prop = get_valid_prop( $super_prop, $super_super_type ) ) != null */ ) {
                $super_open .= ' itemprop="' . $super_prop . '"';
              }
            }
            $super_open .= ' itemscope';
            $super_open .= ' itemtype="' . $super_type . '"';
            $super_open .= '>';
            $super_close = '</span>';
          }
        }

        switch ( $build_mode ) {
          case 'image':
            break;
          case 'link':
            $text_prop = $url_prop = '';
            foreach ( $fields as $key => $value ) {
              if ( strpos( $value, 'http://' ) == 0 || strpos( $value, 'https://' ) == 0 ) {
                $url_prop = $key;
              } else {
                $text_prop = $key;
              }
              if ( $text_prop != '' && $url_prop != '' ) {
                break;
              }
            }
            $text_itemprop = '';
            if ( true /* ( $text_itemprop = get_valid_prop( $text_prop, $super_type ) ) != null */ ) {
              $text_itemprop = ' itemprop="' . $text_itemprop . '"';
            }
            $output = '<span' . $text_itemprop . '>' . $fields[ $text_prop ] . '</span>';
            if ( $url_prop != '') {
              $url_itemprop = '';
              if ( true /* ( $url_itemprop = get_valid_prop( $url_prop, $super_type ) ) != null */ ) {
                $url_itemprop = ' itemprop="' . $url_itemprop . '"';
              }
              $output = '<a' . $url_itemprop . ' href="' . $fields[ $url_prop ] . '">' . $output . '</a>';
            }
            break;
          case 'text':
          default:
            reset( $fields );
            $prop = key( $fields );
            $itemprop = '';
            if ( true /* ( $itemprop = get_valid_prop( $prop, $super_type ) ) != null */ ) {
              $itemprop = ' itemprop="' . $itemprop . '"';
            }
            $output = '<span' . $itemprop . '>' . $fields[ $prop ] . '</span>';
        }

        if( $super_open != '' )
        {
          $output = $super_open . $output . $super_close;
        }

        return $output;
      }

      /**
       * To Elastic Format
       * Need to extend by child class
       * @internal param \DiscoDonniePresents\type $param
       * @return \DiscoDonniePresents\type
       */
      public function toElasticFormat() {
        $this->_post->post_content = do_shortcode( $this->_post->post_content );
        return $this->_post;
      }

    }

  }

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\Taxonomy' ) ) {

    /**
     * Taxonomy object util
     */
    class Taxonomy {

      /**
       *
       * @var type
       */
      public $_term;

      /**
       *
       * @var type
       */
      public $_taxonomy;

      /**
       * Init
       */
      public function __construct( $id = false, $taxonomy = false ) {

        //if ( !is_tax() ) return;

        if ( !$id || !$taxonomy ) {
          $this->_term = get_queried_object();
        } else {
          $this->_term = get_term_by( 'id', $id, $taxonomy );
        }

        $this->_taxonomy = get_taxonomy( $this->_term->taxonomy );
      }

      /**
       *
       * @return type
       */
      public function getUrl() {
        return get_term_link( $this->term()->slug, $this->term()->taxonomy );
      }

      /**
       *
       * @return type
       */
      public function getField() {
        return $this->_taxToElasticField[ $this->term()->taxonomy ];
      }

      /**
       *
       * @return type
       */
      public function getValue() {
        return $this->term()->name;
      }

      /**
       *
       */
      public function getID() {
        return $this->term()->term_id;
      }

      /**
       *
       */
      public function getType() {
        return $this->term()->taxonomy;
      }

      /**
       *
       * @return type
       */
      public function term() {
        return $this->_term;
      }

      /**
       *
       * @return type
       */
      public function taxonomy() {
        return $this->_taxonomy;
      }

      /**
       *
       */
      public function toElasticFormat() {

        $_object = array();

        $_object['summary'] = $this->getValue();
        $_object['url']     = $this->getUrl();

        return $_object;

      }

    }
  }
}