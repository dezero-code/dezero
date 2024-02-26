(function(document, window, $) {

  // Gridview global object for Dezero Framework
  // ----------------------------------------------------
  $.dezeroGridview = {
    loadingClass: 'grid-view-loading',

    id: null,
    hash: null,

    $grid: null,
    $filters: null,
    $table: null,
    $clearButton: null,
    $pjax: null,
    $summary: null,
    $export: null,

    init: function(grid, hash) {
      var self = this;

      self.id = grid;
      self.hash = hash;

      self.$grid = $('#'+ grid);
      self.$filters = $('#'+ grid +'-filters');
      self.$table = $('#'+ grid +'-table');
      self.$clearButton = $('#'+ grid +'-clear-btn');
      self.$summary = $('#'+ grid +'-summary');
      self.$exportButton = $('#'+ grid +'-export-btn');

      // PJAX - After AJAX update, trigger beforeGridLoaded and afterGridLoaded custom events
      if ( $.pjax) {
        self.$pjax = self.$grid.parents('[data-pjax-container]').first();
        // self.$pjax = $('#'+ grid +'-container');
        if ( self.$pjax.length ) {
          self.$pjax
            .off('pjax:send.' + hash)
            .off('pjax:complete.' + hash)
            .on('pjax:send.' + hash, function () {
              // Avoid problems with multiple PJAX gridviews
              self.$grid = $('#'+ grid);
              self.$filters = $('#'+ grid +'-filters');
              self.$table = $('#'+ grid +'-table');
              self.$clearButton = $('#'+ grid +'-clear-btn');

              self.beforeGridLoaded();
            })
            .on('pjax:complete.' + hash, function () {
              self.afterGridLoaded();
            });
        }
      }

      // First GridView loaded
      self.afterGridLoaded();

      // Export button
      if ( self.$exportButton.length ) {
        self.exportGrid();
      }
    },

    // Custom event before GridView is loaded
    // ----------------------------------------------------
    beforeGridLoaded: function() {
      $("html, body").animate({scrollTop: 0}, 100);
      this.$grid.addClass(this.loadingClass);
    },


    // Custom event after GridView is loaded
    // ----------------------------------------------------
    afterGridLoaded: function() {
      this.loadTooltip();
      this.loadSelect2();
      this.clearButton();
      this.$grid.removeClass(this.loadingClass);
      this.deleteAjaxButtons();
    },


    // Load select2 for filters
    // ----------------------------------------------------
    loadSelect2: function() {
      this.$filters.find('select').each(function() {
        // Exclude fitlers with "select2-custom" enabled
        if ( ! $(this).parent().hasClass('select2-custom') ) {

          // Custom class for SLIDEPANEL
          if ( $(this).parent().hasClass('select2-slidepanel') ) {
            $(this).select2({
              allowClear: false,
              dropdownCssClass: 'select2-slidepanel'
            });
          }

          // Default configuration
          else {
            $(this).select2({allowClear: false});
          }
        }
      });
    },


    // Load tooltip Javascript
    // ----------------------------------------------------
    loadTooltip: function() {
      var $tooltip_items = this.$table.find('[data-toggle="tooltip"]');
      if ( $tooltip_items.length ) {
        $tooltip_items.tooltip('hide');
        $tooltip_items.tooltip();
      }
    },


    // Clear Button click event
    // ----------------------------------------------------
    clearButton: function() {
      var self = this;
      self.$clearButton.on('click', function(e){
        e.preventDefault();
        self.$clearButton.tooltip('hide');
        self.clearFilters();
      });
    },


    // Clear GridView filters
    // ----------------------------------------------------
    clearFilters: function() {
      var self = this;

      try {
        self.$filters.find(':input').clearFields();             // this will clear all input in the current grid
        // this.$filters.find(':input').first().trigger('change'); // to submit the form

        // Reload GridView
        $.pjax.reload({
          container: '#'+ self.$grid.attr('id') + '-container',
          url: self.$clearButton.attr('href'),
          replace: ! self.isSlidepanel()
        });
      }
      catch(clear_err) {
          return false;
      }
    },


    // Check if the GridView is loaded inside a SlidPanel
    // ----------------------------------------------------
    isSlidepanel: function() {
      return $('#'+ this.$grid.attr('id') + '-container').is('[data-slidepanel]');
    },


    // Checks if there're delete custom buttons via AJAX
    // ----------------------------------------------------
    deleteAjaxButtons: function() {
      var $actions = this.$table.children('tbody').children('tr').children('td.button-column');
      $actions.children('a.delete-ajax-action').off('click').on('click', $.dezeroGridview.deleteAction);
    },


    // Delete action via AJAX
    // ----------------------------------------------------
    deleteAction: function(e) {
      e.preventDefault();
      var $this = $(this);

      bootbox.confirm({
        message: `<h3>${$this.data('ajax-confirm')}</h3>`,
        callback: function (confirmed) {
          if ( confirmed ) {
            $.ajax({
              url: $this.attr('href'),
              type: 'post',
              // dataType: 'json',
              // data: {  },

              success: function(data) {
                // SUCCESS --> Reload GridView
                if ( data.error_code === 0 ) {
                  $.pnotify({
                    sticker: false,
                    text: data.success,
                    type: 'success'
                  });

                  $.pjax.reload({
                    container: "#"+ $this.parent().data('grid') + '-container'
                  });
                }

                // ERROR --> Show error messages
                else {
                  var out = '';
                  $.each(data.errors, function(key, error_msg) {
                    out = out + '<li>'+ error_msg +'</li>';
                  });

                  // Show success message
                  $.pnotify({
                    sticker: false,
                    text: '<ul>'+ out + '</ul>',
                    type: 'error'
                  });
                }
              },

              // AJAX ERROR
              error: function(request, status, error) {
                $.pnotify({
                  sticker: false,
                  text: 'ERROR: '+ request.responseText,
                  type: 'error'
                });
              },
              cache: false
            });
          }
        }
      });
    },


    // Export results to Excel
    // ----------------------------------------------------
    exportGrid: function() {
      var self = this;

      self.$exportButton.off('click').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);

        bootbox.confirm({
          message: `<h3>EXPORTAR A EXCEL</h3><p class="font-size-18">Se van a exportar <strong class="font-size-18">${self.$summary.data('total')} productos</strong> a Excel.</p><p>Este proceso puede durar varios minutos. <span class="text-danger">Por favor, no refresque la página!</span></p>`,
          callback: function (confirmed) {
            if ( confirmed ) {
              self.ensureExportIframe();

              var $export = $('<input/>', {'name': 'export', 'value': 1, 'type': 'hidden'}),
              $csrf = $('<input/>', {
                'name': window.yii.getCsrfParam() || '_csrf',
                'value': window.yii.getCsrfToken(),
                'type': 'hidden'
              });

              console.log( self.$exportButton.attr('href') + window.location.search);

              $('<form/>', {
                'action': self.$exportButton.attr('href') + window.location.search,
                'target': self.targetIframe(),
                'method': 'post',
                css: {'display': 'none'}
              })
              .append($export, $csrf)
              .appendTo('body')
              .submit()
              .remove();
            }
          }
        });
      });
    },

    targetIframe: function() {
      return this.id +'-export-iframe';
    },

    exportUrl: function() {
      var export_url = this.$exportButton.data('url');
      var current_url = window.location.href;

    },


    ensureExportIframe: function() {
      var target_id = this.targetIframe();
      var $iframe = $('iframe[name="' + target_id +'"]');
      if ( $iframe.length ) {
        return $iframe;
      }

      return $('<iframe/>', {name: target_id, css: {'display': 'none'}}).appendTo('body');
    }
  };

  // GRIDVIEW - CLEAR BUTTON
  $.fn.clearFields = $.fn.clearInputs = function() {
    return this.each(function() {
      var t = this.type, tag = this.tagName.toLowerCase();
      if (t == 'text' || t == 'password' || tag == 'textarea') {
        this.value = '';
      }
      else if (t == 'checkbox' || t == 'radio') {
        this.checked = false;
      }
      else if (tag == 'select') {
        this.selectedIndex = -1;
      }
    });
  };
})(document, window, jQuery);
