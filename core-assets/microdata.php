<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Entity' ) ) {

    class Microdata {

      const URL = 'http://schema.org/';
      const SCHEMA_JSON_DIR = STYLESHEETPATH . '/vendor/usabilitydynamics/lib-model/static/schemas/';

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
          $type = self::URL . $type;
          $type = ' itemscope itemtype="' . $type . '"';
        }

        return $prop . $type;
      }

      /**
       * This handler function is attached to some entity functions to make applying microdata as automatic as possible.
       * @param  array $microdata_args an array of microdata arguments
       * @return string the HTML string with microdata included
       */
      public static function handler( $microdata_args = array() ) {
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
          'novalidate'        => false,
        ) ) );

        if ( !is_array( $fields ) || count( $fields ) == 0 ) {
          return '';
        }

        if ( $build_mode == '' ) {
          $build_mode = 'text';
          if ( in_array( $origin, array( 'termsToString' ) ) ) {
            $build_mode = 'link';
          } /* TODO: check when to create image; elseif ( in_array( $origin, array( '...' ) ) ) {
            $build_mode = 'image';
          }*/
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

        switch ( $build_mode ) {
          case 'image':
            $prop = '';
            foreach ( $fields as $key => $value ) {
              if ( strpos( $value, 'http://' ) == 0 || strpos( $value, 'https://' ) == 0 ) {
                $prop = $key;
                break;
              }
            }
            $itemprop = '';
            if ( ( $itemprop = self::get_valid_prop( $prop, $super_type, $novalidate ) ) != null ) {
              $itemprop = ' itemprop="' . $itemprop . '"';
            }
            $output = '<img' . $itemprop . ' src="' . $fields[ $prop ] . '">';
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

          $type = self::URL . $type;
        }
        return $type;
      }

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

      protected static function get_type_json( $type ) {
        $type = strtolower( $type );
        if ( file_exists( self::SCHEMA_JSON_DIR . $type . '.json' ) ) {
          $data = file_get_contents( self::SCHEMA_JSON_DIR . $type . '.json' );
          $data = json_decode( $data, true );
          if ( is_array( $data ) ) {
            return $data;
          }
        }
        return false;
      }

    }

  }

}
