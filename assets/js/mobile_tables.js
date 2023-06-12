(function ($, Drupal) {

  Drupal.behaviors.mobile_tables = {
    attach: function (context, settings) {
      once('initControlTable', $('.body.field-item table:not(.tablesaw), .field-body table:not(.tablesaw)')).forEach(function (table) {

        table.outerHTML = '<div class="wrapper-table">' +
          '<div class="control-table">' +
          '<div class="prevButton"><span></span></div>' +
          '<div class="body-control"></div>' +
          '<div class="nextButton"><span></span></div>' +
          '</div>' +
          '<div class="table-content">' + table.outerHTML + '</div>' +
          '</div>';
      });

      once('controlTable', $('.body.field-item .wrapper-table, .field-body .wrapper-table')).forEach(function (wrapperTable) {
        const prevButton = wrapperTable.querySelector('.prevButton');
        const nextButton = wrapperTable.querySelector('.nextButton');
        const tableContent = wrapperTable.querySelector('.table-content');
        const table = wrapperTable.querySelector('table');
        const control = wrapperTable.querySelector('.control-table');
        const columns = [...tableContent.querySelectorAll('tr:nth-child(1) > td')];


        initButton(table, tableContent, prevButton, nextButton, control);
        tableContent.addEventListener('scroll', () => {
          initButton(table, tableContent, prevButton, nextButton, control);
        });
        window.addEventListener('resize', () => {
          initButton(table, tableContent, prevButton, nextButton, control);
        });

        nextButton.addEventListener('click', () => {
          let scroll = 0;
          columns.some(column => {
            scroll += column.offsetWidth;
            if (scroll > tableContent.scrollLeft) {
              $(tableContent).animate({
                  scrollLeft: scroll
                },
                300);
              return true;
            }
          });
        });

        prevButton.addEventListener('click', (event) => {
          let scroll = 0;
          columns.some(column => {
            if (scroll + column.offsetWidth >= tableContent.scrollLeft) {
              $(tableContent).animate({
                  scrollLeft: scroll
                },
                300);
              return true;
            }
            scroll += column.offsetWidth;
          });
        });
      });

      function initButton(table, tableContent, prevButton, nextButton, control) {

        if (tableContent.offsetWidth === table.offsetWidth) {
          control.classList.add('d-none');
        }
        else {
          control.classList.remove('d-none');
        }

        if (tableContent.scrollLeft === 0) {
          prevButton.classList.add('disabled');
        }
        else {
          prevButton.classList.remove('disabled');
        }

        if (tableContent.offsetWidth + tableContent.scrollLeft === table.offsetWidth) {
          nextButton.classList.add('disabled');
        }
        else {
          nextButton.classList.remove('disabled');
        }
      }
    }
  };

})(jQuery, Drupal);
