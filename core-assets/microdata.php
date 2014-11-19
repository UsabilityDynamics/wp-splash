<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Microdata' ) ) {

    class Microdata {

      protected static $URL = '';
      protected static $SCHEMA_JSON_DIR = '';
      protected static $ENTITY_NAMESPACE = '';

      public static function init() {
        self::$URL = 'http://schema.org/';
        self::$SCHEMA_JSON_DIR = get_stylesheet_directory() . '/vendor/usabilitydynamics/lib-model/static/schemas/';
        self::$ENTITY_NAMESPACE = __NAMESPACE__;
      }

      /**
       * Function to output microdata manually in a valid format
       * @param string $prop the itemprop (optional)
       * @param string $type the itemtype (optional)
       * @return string valid microdata string containing the parameters or empty string if nothing provided
       */
      public static function manual( $prop = '', $type = '' ) {
        if ( !empty( $prop ) ) {
          $prop = ' itemprop="' . $prop . '"';
        }

        if ( !empty( $type ) ) {
          $type = ucfirst( $type );
          $type = self::$URL . $type;
          $type = ' itemscope itemtype="' . $type . '"';
        }

        return $prop . $type;
      }

      /**
       * Function to output microdata meta, invisible to the user, but visible for search engines.
       * This function should be used for properties which cannot actually be displayed anywhere on the page.
       * @param object $object an entity object to retrieve data from
       * @param array $fields the fields to include in the meta information
       * @return string HTML code for the meta information
       */
      public static function manual_meta( $object, $fields = array() ) {
        $output = '';

        if ( is_a( $object, self::$ENTITY_NAMESPACE . '\\Entity' ) ) {

          $id = $object->post( 'ID' );
          $mappings = array(
            'name'        => array( 'get_the_title', $id ),
            'url'         => array( 'get_permalink', $id ),
            'startDate'   => array( array( $object, 'meta' ), 'dateStart' ),
          );

          foreach ( $fields as $prop ) {
            if ( isset( $mappings[ $prop ] ) ) {
              $value = call_user_func( $mappings[ $prop ][0], $mappings[ $prop ][1] );
            } else {
              $value = $object->meta( $prop );
            }
            if ( $value && is_string( $value ) ) {
              if ( strpos( $value, 'http://' ) === 0 || strpos( $value, 'http://' ) === 0 ) {
                $output .= '<link itemprop="' . $prop . '" href="' . $value . '">';
              } elseif ( preg_match( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?/', $value ) ) {
                $output .= '<time itemprop="' . $prop . '" datetime="' . $value . '"></time>';
              } else {
                $output .= '<meta itemprop="' . $prop . '" content="' . $value . '">';
              }
            }
          }

        }

        return $output;
      }

      /**
       * This handler function is attached to some entity functions to make applying microdata as automatic as possible.
       * @param  array $args an array of microdata arguments
       * @return string the HTML string with microdata included
       */
      public static function handler( $args = array() ) {
        if ( !is_array( $args ) ) {
          return '';
        }

        extract( wp_parse_args( $args, array(
          'build_mode'        => '',
          'fields'            => array(),
          'origin_function'   => '',
          'origin_class'      => '',
          'super_type'        => null,
          'super_prop'        => null,
          'super_super_type'  => null,
          'novalidate'        => false,
          'image_size'        => 'thumbnail',
        ) ) );

        $origin_class = str_replace( self::$ENTITY_NAMESPACE . '\\', '', $origin_class );

        $fields = self::map( $fields, $origin_class, $origin_function );

        if ( !is_array( $fields ) || count( $fields ) == 0 ) {
          return '';
        }

        if ( $build_mode == '' ) {
          $build_mode = 'text';
          if ( in_array( $origin_function, array( 'termsToString' ) ) ) {
            $build_mode = 'link';
          } elseif ( in_array( $origin_function, array( 'image' ) ) ) {
            $build_mode = 'image';
          }
        }

        $output = '';

        $super_open = $super_close = '';
        if ( $super_type != null ) {
          $super_type = ucfirst( $super_type );
          if ( ( $super_type = self::get_valid_type( $super_type, $novalidate ) ) != null ) {
            $super_open = '<span';
            if ( $super_prop != null ) {
              if ( ( $super_prop = self::get_valid_prop( $super_prop, $super_super_type, $novalidate ) ) != null ) {
                $super_open .= ' itemprop="' . $super_prop . '"';
              }
            }
            $super_open .= ' itemscope';
            $super_open .= ' itemtype="' . $super_type . '"';
            $super_open .= '>';
            $super_close = '</span>';
          }
        }

        if ( $super_type == null && !empty( $origin_class ) ) {
          $super_type = self::get_mapped_type( $origin_class );
        }

        switch ( $build_mode ) {
          case 'image':
            $prop = '';
            foreach ( $fields as $key => $value ) {
              if ( is_int( $value ) ) {
                $prop = $key;
                break;
              }
            }
            $attr = array();
            if ( ( $itemprop = self::get_valid_prop( $prop, $super_type, $novalidate ) ) != null ) {
              $attr['itemprop'] = $itemprop;
            }
            $output = wp_get_attachment_image( $fields[ $prop ], $image_size, false, $attr );
            break;
          case 'link':
            $text_prop = $url_prop = '';
            foreach ( $fields as $key => $value ) {
              if ( strpos( $value, 'http://' ) === 0 || strpos( $value, 'https://' ) === 0 ) {
                $url_prop = $key;
              } else {
                $text_prop = $key;
              }
              if ( $text_prop != '' && $url_prop != '' ) {
                break;
              }
            }
            $text_itemprop = '';
            if ( ( $text_itemprop = self::get_valid_prop( $text_prop, $super_type, $novalidate ) ) != null ) {
              $text_itemprop = ' itemprop="' . $text_itemprop . '"';
            }
            $output = '<span' . $text_itemprop . '>' . $fields[ $text_prop ] . '</span>';
            if ( $url_prop != '') {
              $url_itemprop = '';
              if ( ( $url_itemprop = self::get_valid_prop( $url_prop, $super_type, $novalidate ) ) != null ) {
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
            if ( ( $itemprop = self::get_valid_prop( $prop, $super_type, $novalidate ) ) != null ) {
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
       * This function prepares arguments to be compatible with the handler() method.
       * @param mixed $args usually an array (with user-created arguments); if neither array nor boolean true, no arguments will be created
       * @param array $fields the fields for this microdata structure
       * @param string $origin_class the class where the fields were retrieved
       * @param string $origin_function the function where the fields were retrieved
       * @param array $more_args any additional arguments which are automatically created
       * @return mixed an array of microdata args or false, if no microdata should be used
       */
      public static function prepare_args( $args, $fields, $origin_class = '', $origin_function = '', $more_args = array() ) {
        if ( $args === true ) {
          $args = array();
        }

        foreach ( $fields as $field ) {
          if ( is_array( $field ) ) {
            return false;
          }
        }

        if ( is_array( $args ) ) {
          $special_args = array(
            'fields'            => $fields,
            'origin_class'      => $origin_class,
            'origin_function'   => $origin_function,
          );
          $special_args = array_merge( $special_args, $more_args );
          return array_merge( $special_args, $args );
        }

        return false;
      }

      /**
       * This function validates a type.
       * @param string $type the type to validate
       * @param boolean $novalidate if true, the type is not validated (optional, default is false)
       * @return mixed the validated type or null if invalid
       */
      protected static function get_valid_type( $type = null, $novalidate = false ) {
        if ( $type !== null ) {
          $type = (string) $type;
          $type = ucfirst( $type );

          if ( !$novalidate ) {

            $json = self::get_type_json( $type );
            if ( $json ) {
              $type = $json['type'];
            } else {
              return null;
            }

          }

          $type = self::$URL . $type;
        }
        return $type;
      }

      /**
       * This function validates a prop. However, it only does so if the superior type is provided.
       * @param string $prop the prop to validate
       * @param string $type the superior type of the prop
       * @param boolean $novalidate if true, the prop is not validated (optional, default is false)
       * @return mixed the validated prop or null if invalid
       */
      protected static function get_valid_prop( $prop = null, $type = null, $novalidate = false ) {
        if ( $prop !== null ) {
          $prop = (string) $prop;

          if ( $type !== null && !$novalidate ) {

            $json = self::get_type_json( $type );
            if ( $json ) {

              $found = false;
              foreach ( $json['bases'] as $base => $props ) {
                foreach ( $props as $p ) {
                  if ( isset( $p['name'] ) && $p['name'] == $prop ) {
                    $found = true;
                    break;
                  }
                }
                if ( $found ) {
                  break;
                }
              }

              if ( !$found ) {
                $prop = null;
              }

            } else {
              $prop = null;
            }
            
          }

        }
        return $prop;
      }

      /**
       * Gets the Schema JSON for a type.
       * @param string $type the type to retrieve the Schema JSON for
       * @return mixed an array of Schema information or false if nothing found
       */
      protected static function get_type_json( $type ) {
        $type = str_replace( self::$URL, '', $type );
        $type = strtolower( $type );
        if ( file_exists( self::$SCHEMA_JSON_DIR . $type . '.json' ) ) {
          $data = file_get_contents( self::$SCHEMA_JSON_DIR . $type . '.json' );
          $data = json_decode( $data, true );
          if ( is_array( $data ) ) {
            return $data;
          }
        }
        return false;
      }

      /**
       * Maps an array of keys to their actual properties if possible. If not, use the key as property.
       * Edit the get_mappings() function to adjust the key => prop mapping.
       * @param array $fields the array of prop keys and values
       * @param string $origin_class the class where the fields were retrieved
       * @param string $origin_function the function where the fields were retrieved
       * @return array the array of mapped prop keys and values
       */
      protected static function map( $fields, $origin_class = '', $origin_function = '' ) {
        $mapped_fields = array();

        $mappings = self::get_mappings( $origin_class, $origin_function, false );

        foreach ( $fields as $key => $value ) {

          if ( isset( $mappings[ $key ] ) ) {
            $mapped_fields[ $mappings[ $key ] ] = $value;
          } else {
            $mapped_fields[ $key ] = $value;
          }

        }
        return $mapped_fields;
      }

      /**
       * Retrieves mappings for a specific class and function. Edit the mappings array to adjust the key => prop mapping.
       * @param string $origin_class the class to retrieve mappings for
       * @param string $origin_function the function to retrieve mappings for
       * @param boolean $all_if_not_found if true, all mappings will be retrieved if class or function do not have any mappings (optional, default is false)
       * @return array an array of mappings, depending on the function parameters
       */
      protected static function get_mappings( $origin_class = '', $origin_function = '', $all_if_not_found = false ) {
        $mappings = array(
          'Event'           => array(
            'image'           => array(
              'posterImage'     => 'image',
            ),
          ),
        );

        if ( empty( $origin_function ) ) {
          $origin_function = '_default';
        }
        if ( empty( $origin_class ) ) {
          $origin_class = '_Default';
        }

        if ( isset( $mappings[ $origin_class ] ) && isset( $mappings[ $origin_class ][ $origin_function ] ) ) {
          return $mappings[ $origin_class ][ $origin_function ];
        }

        if ( $all_if_not_found ) {
          return $mappings;
        }

        return array();
      }

      /**
       * Gets a mapped type for an origin class (if possible). This function is only used if no super_type is provided in the microdata handler function.
       * @param string $origin_class class name to map to a type
       * @return mixed the mapped type or null if the class could not be mapped to a type
       */
      protected static function get_mapped_type( $origin_class ) {
        $mappings = array(
          'Event'         => 'Event',
        );

        $origin_class = ucfirst( $origin_class );

        if ( isset( $mappings[ $origin_class ] ) ) {
          return $mappings[ $origin_class ];
        }

        return null;
      }

    }

  }

}
