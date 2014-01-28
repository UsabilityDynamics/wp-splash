<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <title><?php wp_title(''); ?></title>
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <script type="application/javascript" data-main="/scripts/app" href="cdn.udx.io/udx.requires.js" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

  <?php do_action( 'header-navbar' ); ?>

  <div class="super_wrapper">
    <div class="background_header_image"></div>
    <div class="general_header_wrapper">
      <div class="header container clearfix flawless_dynamic_area" data-container-type="header">

        <?php if( current_theme_supports( 'header-logo' ) && wp_disco()->get( 'logo.url' ) ) : ?>
          <div <?php flawless_element( 'logo_area_wrapper cfct-block inner_container' ); ?>>
            <a href="<?php echo home_url(); ?>" class="header_logo_image" title="<?php bloginfo( 'name' ); ?>">
              <img class="header_logo_image"  src="<?php echo wp_disco()->get( 'logo.url' ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
            </a>
          </div>
        <?php endif; ?>

        <?php if( current_theme_supports( 'header-business-card' ) && flawless_have_business_card( 'header' ) ): ?>
          <div <?php flawless_element( 'header_business_card_wrapper cfct-block' ); ?>>
            <div class="header_business_card inner_container">
            <?php echo flawless_have_business_card( 'header' ); ?>
            </div>
          </div>
        <?php endif; ?>

        <?php  if( current_theme_supports( 'header-search' ) ): ?>
          <div <?php flawless_element( 'header_search_wrapper cfct-block no-print' ); ?>>
            <div class="header_search inner_container">
              <?php get_search_form(); ?>
            </div>
          </div>
        <?php endif; ?>

        <div <?php flawless_element( 'header_text' ); ?>>
          <?php echo do_shortcode( nl2br( wp_disco()->get( 'header.text' ) ) ); ?>
        </div>

        <?php if( $flawless_header_menu = wp_nav_menu( apply_filters( 'flawless_header_menu', array( 'theme_location' => 'header-menu', 'menu_class' => 'header-nav flawless-menu no-print clearfix', 'fallback_cb' => false, 'echo' => false ) ) ) ): ?>
          <div <?php flawless_element( 'header_menu cfct-block' ); ?>>
            <?php echo $flawless_header_menu; ?>
          </div>
        <?php endif; ?>

        <?php if( current_theme_supports( 'header-dropdowns' ) ): ?>
          <?php get_template_part( 'header-element', 'dropdown-links' ); ?>
        <?php endif; ?>

        <?php do_action( 'flawless::header_bottom' ); ?>

      </div>
    </div>

    <div class="content_container clearfix">

    <?php flawless_primary_notice_container( '' ); ?>

    <?php if( $flawless_sub_header_menu = wp_nav_menu( apply_filters( 'flawless_sub_header_menu', array( 'theme_location'=> 'header-sub-menu', 'menu_class' => 'header-sub-menu container flawless-menu no-print clearfix', 'fallback_cb' => false, 'depth' => 2 , 'echo' => false) ) ) ): ?>
    <div class="header_submenu">
      <?php echo $flawless_sub_header_menu; ?>
    </div>
    <?php endif; ?>

    <?php do_action( 'flawless::content_container_top' ); ?>
