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
          $('.header--bottom').css('position', 'relative');
          header.css('overflow-y', 'revert');
          $('a.highlighted').show();
          // Google Translate can only exist in one place, so we have to move it.
          // See https://stackoverflow.com/questions/21759756/multiple-instances-of-google-translate#comment77539211_43314365
          $('.header--top-right-column .openy-google-translate').replaceWith($('.mobile-header--top-right-column .openy-google-translate'));
        } else {
          header.addClass('open');
          userMenu.addClass('container');
          btn.attr('aria-expanded', true);
          body.css('overflow', 'hidden');
          $('.header--bottom').css('position', 'absolute');
          header.css('overflow-y', 'auto');
          $('.mobile-header--top-right-column .openy-google-translate').replaceWith($('.header--top-right-column .openy-google-translate'));
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
      // A fix for a small mobile screen.
      header.scroll(() => {
        if (header.scrollTop() > 0) {
          header.addClass('scrolled');
        } else {
          header.removeClass('scrolled');
        }
      });

      // Hide main menu items until page is ready.
      header.find('nav').css('visibility', 'visible');
      header.find('.block-ws-search-bar').css('visibility', 'visible');
      header.find('.block-ws-site-logo').css('visibility', 'visible');

      function switchMainMenuType() {
        const header = $('.ws-header');
        // This size is can be fixed.
        const logo =  230;
        var right_menu = document.querySelector('.header--bottom-right-column');
        var main_menu = document.querySelector('.header--bottom-middle-column');
        var max_available_width = $(window).width() - logo;
        // In case the right menu was removed.
        if (right_menu) {
          max_available_width -= right_menu.offsetWidth;
        }
        let main_menu_width = 0;
        if (!right_menu) {
          main_menu.style.maxWidth = 'none';
        }
        if (main_menu) {
          main_menu_width = main_menu.offsetWidth;
        }

        if (max_available_width < main_menu_width) {
            header.removeClass('desktop').addClass('mobile');
        } else {
            header.removeClass('mobile').addClass('desktop');
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

      $('.dropdown-submenu .menu-link-item').click(function (e) {
        // If the item is already open, then go to the link.
        let parentLink = $(this).parent();
        if ($(this).attr('href') !== undefined && parentLink.hasClass('active') ) {
          window.location.href = $(this).attr('href');
        }
        // If the item is not open and it is a parent, then open it.
        else if (parentLink.hasClass('children')) {
          e.stopPropagation();
          e.preventDefault();
        }
        const subMenuTarget = $(this).attr('data-submenu-target');
        const subMenu = $('#' + subMenuTarget);

        // Manage states of 3rd level popups.
        const secondLevelDropdownLink = $('.header-nav__submenu.level-3');
        secondLevelDropdownLink.each(function () {
          $(this).removeClass('open');
          if ($(this).attr('id') !== subMenuTarget) {
            $(this).removeClass('active');
          }
        });

        // Set active 2nd level item and remove the state from siblings.
        $(this).parent().addClass('active').siblings().removeClass('active');
        if (header.hasClass('mobile')) {
          $(this).parent().parent().toggleClass('open');
          $(this).parent().parent().parent().toggleClass('open');
        }
        subMenu.addClass('open');
        // When we enter the 3rd level, move focus to it. Tabindex is required to be focusable by js.
        subMenu.attr('tabIndex', '-1').focus();
      });

      const firstLevelItem = $('.header-nav__links .dropdown', context);
      const firstLevelItemLink = $(firstLevelItem, context).children('a');
      $(firstLevelItemLink, context).click(function (e) {
        // Handle desktop dropdown for parent items with children
        if (header.hasClass('desktop') && $(this).parent().hasClass('children')) {
          e.preventDefault();
          e.stopPropagation();
          const $parent = $(this).parent();
          const $subMenu = $(this).siblings('.header-nav__submenu');

          // Close other open dropdowns
          $('.header-nav__links .dropdown').not($parent).removeClass('show');
          $('.header-nav__submenu').not($subMenu).removeClass('show').css('display', '');

          // Toggle current dropdown
          $parent.toggleClass('show');
          $subMenu.toggleClass('show');

          // Force display with inline style if needed
          if ($subMenu.hasClass('show')) {
            $subMenu.css('display', 'flex');
          } else {
            $subMenu.css('display', '');
          }
        }
        if (header.hasClass('mobile')) {
          if ($(this).parent().hasClass('children')) {
            e.preventDefault();
          }

          const subMenu = $(this).siblings('.header-nav__submenu');
          if (subMenu.hasClass('open')) {
            subMenu.removeClass('open');
          } else {
            subMenu.addClass('open');
            $('a.highlighted').hide();
          }
          if (header.hasClass('scrolled')) {
            $('.header--bottom').css('position', 'fixed');
          }
          header.css('overflow-y', 'hidden');
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
          $('.header--bottom').css('position', 'absolute');
          if (header.hasClass('open')) {
            header.css('overflow-y', 'auto');
          }
          $('a.highlighted').show();
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
      // Close dropdown when clicking outside (desktop only)
      $(document).click(function (e) {
        if (header.hasClass('desktop')) {
          const $target = $(e.target);
          // This prevents immediate close when opening dropdown
          if ($target.closest('.dropdown.children > a').length > 0) {
            return;
          }
          // If click is not on menu or inside dropdown
          if (!$target.closest('.header-nav__links').length) {
            $('.header-nav__links .dropdown').removeClass('show');
            $('.header-nav__submenu').removeClass('show').css('display', '');
          }
        }
      });
      // Set top position for submenus in mobile screen.
      const mobileHeaderHeight = $('.header--bottom').outerHeight();
      $('.header-nav__submenu').css('top', mobileHeaderHeight + 'px');
    }
  };
})(jQuery, Drupal);
