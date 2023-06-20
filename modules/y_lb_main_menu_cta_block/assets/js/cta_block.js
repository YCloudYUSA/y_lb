(function ($, Drupal) {

  'use strict';

  /**
   * Handle a displaying the CTA block in dropdown for the 2nd level of the menu.
   */
  Drupal.behaviors.cta_block_in_dropdown_menu = {
    attach: function (context) {
      const header = $('.ws-header', context);

      $('.dropdown-submenu a.menu-link-item').click(function (e) {
        if ($(this).parent().hasClass('children')) {
          e.stopPropagation();
          e.preventDefault();
        }

        const menuCtaBlock = $(this).parent().parent().parent().parent().find('.ws-menu-cta-block');
        if (menuCtaBlock !== 'undefined') {
          menuCtaBlock.hide();
        }
      });

      const firstLevelItem = $('.header-nav__links .dropdown', context);
      const firstLevelItemLink = $(firstLevelItem, context).children('a');

      $(firstLevelItemLink, context).click(function (e) {
        const menuCtaBlock = $(this).parent().find('.ws-menu-cta-block');
        const secondLevelItemLink = $(this).parent().find('.level-3');
        if (menuCtaBlock !== 'undefined' && header.hasClass('desktop')) {
          secondLevelItemLink.removeClass('open');
          menuCtaBlock.show();
        }
      });

      // Hide the menu CTA block after resizing.
      $(window).resize(function () {
        if ($('.ws-header', context).hasClass('mobile')) {
          $('.ws-menu-cta-block').hide();
        }
      });
    }
  };
})(jQuery, Drupal);
