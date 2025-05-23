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
    $exportMenu: null,

    init: function(grid, hash) {
      var self = this;

      self.id = grid;
      self.hash = hash;

      self.$grid = $('#'+ grid);
      self.$filters = $('#'+ grid +'-filters');
      self.$table = $('#'+ grid +'-table');
      self.$clearButton = $('#'+ grid +'-clear-btn');
      self.$summary = $('#'+ grid +'-summary');
      self.$exportMenu = $('#'+ grid +'-export-menu');

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

      // Export menu button?
      if ( self.$exportMenu.length ) {
        self.exportGrid();
      }
    },

    // Custom event before GridView is loaded
    // ----------------------------------------------------
    beforeGridLoaded: function() {
      this.destroyTooltip();
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


    // Destroy all tooltips
    // ----------------------------------------------------
    destroyTooltip: function() {
      var $tooltip_items = this.$table.find('[data-toggle="tooltip"]');
      if ( $tooltip_items.length ) {
        $tooltip_items.tooltip('dispose');
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

      self.$exportMenu.find('.export-btn').each(function(){
        $(this).off('click').on('click', function(e) {
          e.preventDefault();
          var $this = $(this);
          var total_items = self.$summary.data('total');

          // Title
          var confirm_title = `<h3>EXPORT TO EXCEL</h3>`;
          if ( $this.is('[data-title]') ) {
            confirm_title = $this.attr('data-title');
          }

          // Message
          var confirm_message = `<p class="font-size-18"><strong class="font-size-18">${total_items} items</strong> will be exported to Excel.</p><p>This action could take up to several minutes. <span class="text-danger">Please, do not refresh the page!</span></p>`;
          if ( $this.is('[data-message]') ) {
            confirm_message = $this.attr('data-message').replace('{total}', total_items);
          }

          // Limit
          var limit_items = 1000;
          var limit_message = `<p class="font-size-18">You cannot export more than <strong class="font-size-18">1000 items</strong> to Excel</p><p class="text-danger">Consider applying a filter to reduce the items to export.</p>`;
          if ( $this.is('[data-limit]') ) {
            limit_items = parseInt($this.attr('data-limit'));
          }
          if ( $this.is('[data-limit-message]') ) {
            limit_message = $this.attr('data-limit-message');
          }

          // Max limit exceed?
          if ( total_items > limit_items ) {
            bootbox.alert({
              message: confirm_title + limit_message
            });
          }

          // Show confirm message
          else {
            bootbox.confirm({
              message: confirm_title + confirm_message,
              callback: function (confirmed) {
                if ( confirmed ) {
                  self.ensureExportIframe();

                  var $export = $('<input/>', {'name': 'export', 'value': 1, 'type': 'hidden'}),
                  $csrf = $('<input/>', {
                    'name': window.yii.getCsrfParam() || '_csrf',
                    'value': window.yii.getCsrfToken(),
                    'type': 'hidden'
                  });

                  $('<form/>', {
                    'action': $this.attr('href') + window.location.search,
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
          }
        });
      });
    },

    targetIframe: function() {
      return this.id +'-export-iframe';
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
