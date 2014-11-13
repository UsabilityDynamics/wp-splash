<?php

namespace DiscoDonniePresents {

  /**
   * Prevent re-declaration
   */
  if ( !class_exists( 'DiscoDonniePresents\VenueTaxonomy' ) ) {

    /**
     * Venue related taxonomies
     */
    class VenueTaxonomy extends Taxonomy {

      /**
       *
       * @var type
       */
      public $_taxToElasticField = array(
          'venue-type' => 'venue.type.name',
          'city' => 'venue.address.city.name',
          'state' => 'venue.address.state.name',
          'country' => 'venue.address.country.name'
      );

    }

  }

}