(function ($, Drupal) {

  'use strict';

  /**
   * Handle open mobile menu.
   */
  Drupal.behaviors.mobile_menu_toggle = {
    attach: function (context) {
      const header = $('.ws-header', context);
      const body = $('body', context);
      const btn = $(this, context);
      const submenu = $('.header-nav__submenu', context);
      const userMenu = $('.block-system-menu-blockaccount', context);
      const headerNavToggle = $('.header-navbar-toggler', context);
      headerNavToggle.unbind('click');
      headerNavToggle.click(function () {
        if (header.hasClass('open')) {
          header.removeClass('open');
          btn.attr('aria-expanded', false);
          body.css('overflow', 'auto');
          submenu.removeClass('open');
          userMenu.removeClass('container');
        } else {
          header.addClass('open');
          userMenu.addClass('container');
          btn.attr('aria-expanded', true);
          body.css('overflow', 'hidden');
        }
      });

      switchMainMenuType();

      $(window).resize(function () {
        if (header.hasClass('desktop')) {
          header.removeClass('open');
          btn.attr('aria-expanded', false);
          body.css('overflow', 'auto');
        }
        switchMainMenuType();
      });
      // Hide main menu until page is ready.
      header.find('nav').css('visibility', 'visible');
      // A fix for a small mobile screen.
      header.scroll(() => {
        if (header.scrollTop() > 0) {
          header.addClass('scrolled');
        } else {
          header.removeClass('scrolled');
        }
      });

      function switchMainMenuType() {
        const header = $('.ws-header');
        // This size is can be fixed.
        const logo =  220;
        var main_menu = document.querySelector('.header--bottom-middle-column');
        var right_menu = document.querySelector('.header--bottom-right-column');
        var max_available_width = $(window).width() - logo - right_menu.offsetWidth;

        if (max_available_width < main_menu.offsetWidth) {
          if (header.hasClass('desktop')) {
           header.removeClass('desktop');
          }
          header.addClass('mobile');
        }
        else {
          if (header.hasClass('mobile')) {
            header.removeClass('mobile');
          }
          header.addClass('desktop');
        }
      }
    }
  };

  /**
   * Handle dropdown for the 2nd level of the menu.
   */
  Drupal.behaviors.header_dropdown_menu = {
    attach: function (context) {
      const header = $('.ws-header', context);

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
        if (header.hasClass('mobile')) {
          $(this).parent().parent().toggleClass('open');
          $(this).parent().parent().parent().toggleClass('open');
        }
        subMenu.addClass('open');
      });

      const firstLevelItem = $('.header-nav__links .dropdown', context);
      const firstLevelItemLink = $(firstLevelItem, context).children('a');
      $(firstLevelItemLink, context).click(function (e) {
        if (header.hasClass('mobile')) {
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
      });

      $(window).resize(function () {
        if (header.hasClass('desktop')) {
          const headerSubMenu = $('.header-nav__submenu');
          headerSubMenu.removeClass('open');
          headerSubMenu.removeClass('show');
          $('.dropdown').removeClass('show');
        }
      });
    }
  };
})(jQuery, Drupal);
