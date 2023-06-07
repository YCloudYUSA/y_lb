(function ($, Drupal) {

  'use strict';

  /**
   * Handle open mobile menu.
   */
  Drupal.behaviors.mobile_menu_toggle = {
    attach: function (context) {
      const breakpoint = 1736;
      const $header = $('.header', context);
      const body = $('body', context);
      const btn = $(this, context);
      const submenu = $('.header-nav__submenu', context);
      const userMenu = $('.block-system-menu-blockaccount', context);
      const headerNavToggle = $('.header-navbar-toggler', context);
      headerNavToggle.unbind('click');
      headerNavToggle.click(function () {
        if ($header.hasClass('open')) {
          $header.removeClass('open');
          btn.attr('aria-expanded', false);
          body.css('overflow', 'auto');
          submenu.removeClass('open');
          userMenu.removeClass('container');
        } else {
          $header.addClass('open');
          userMenu.addClass('container');
          btn.attr('aria-expanded', true);
          body.css('overflow', 'hidden');
        }
      });

      $(window).resize(function () {
        if ($(window).width() >= breakpoint) {
          $header.removeClass('open');
          btn.attr('aria-expanded', false);
          body.css('overflow', 'auto');
        }
      });
    }
  };

  /**
   * Handle dropdown for the 2nd level of the menu.
   */
  Drupal.behaviors.header_dropdown_menu = {
    attach: function (context) {
      const breakpoint = 1736;

      $('.menu-link--level-1').click(function (e) {
        if ($(window).width() <= breakpoint) {
          $('.openy-google-translate').hide();
        }
      });

      $('.dropdown-submenu a.menu-link-item').click(function (e) {
        if ($(this).parent().hasClass('children')) {
          e.stopPropagation();
          e.preventDefault();
        }
        const subMenuTarget = $(this).attr('data-submenu-target');
        const subMenu = $('#' + subMenuTarget);

        const secondLevelDropdownLink = $('.header-nav__submenu.level-3');
        secondLevelDropdownLink.each(function () {
          $(this).removeClass('open');
          if ($(this).attr('data-submenu-target') !== subMenuTarget) {
            $(this).removeClass('active');
          }
        });
        $(this).parent().addClass('active');
        if ($(window).width() <= breakpoint) {
          $(this).parent().parent().toggleClass('open');
          $(this).parent().parent().parent().toggleClass('open');
        }
        subMenu.addClass('open');
      });

      const firstLevelItem = $('.header-nav__links .dropdown', context);
      const firstLevelItemLink = $(firstLevelItem, context).children('a');
      $(firstLevelItemLink, context).click(function (e) {
        if ($(window).width() <= breakpoint) {
          if ($(this).parent().hasClass('children')) {
            e.preventDefault();
          }

          const subMenu = $(this).siblings('.header-nav__submenu');
          if (subMenu.hasClass('open')) {
            subMenu.removeClass('open');
          } else {
            subMenu.addClass('open');
          }
        }
        const secondLevelDropdownLink = $(this).parent().find('li.active');
        secondLevelDropdownLink.each(function () {
          $(this).removeClass('active');
        });
      });

      $('.back', context).click(function () {
        const back_1_level = $(this, context).parent().parent().parent().parent().parent();
        if (back_1_level.hasClass('open')) {
          back_1_level.removeClass('open');
        }
        if (back_1_level.hasClass('show')) {
          back_1_level.removeClass('show');
        }
        const back_2_level = $(this, context).parent().parent().parent().parent();
        if (back_2_level.hasClass('open')) {
          back_2_level.removeClass('open');
        }
        if (back_2_level.hasClass('show')) {
          back_2_level.removeClass('show');
        }
        $('.openy-google-translate').show();
      });

      $(window).resize(function () {
        if ($(window).width() >= breakpoint) {
          const $headerSubMenu = $('.header-nav__submenu');
          $headerSubMenu.removeClass('open');
          $headerSubMenu.removeClass('show');
          $('.dropdown').removeClass('show');
        }
      });
    }
  };
})(jQuery, Drupal);
