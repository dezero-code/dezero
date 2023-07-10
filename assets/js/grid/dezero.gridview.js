(function(document, window, $) {

  // Gridview global object for Dezero Framework
  // ----------------------------------------------------
  $.dezeroGridview = {
    loadingClass: 'grid-view-loading',

    $grid: null,
    $filters: null,
    $table: null,
    $clearButton: null,
    $pjax: null,

    init: function(grid, hash) {
      var self = this;

      self.$grid = $('#'+ grid);
      self.$filters = $('#'+ grid +'-filters');
      self.$table = $('#'+ grid +'-table');
      self.$clearButton = $('#'+ grid +'-clear-btn');

      // PJAX - After AJAX update, trigger beforeGridLoaded and afterGridLoaded custom events
      if ( $.pjax) {
        self.$pjax = self.$grid.parents('[data-pjax-container]').first();
        if ( self.$pjax.length ) {
          self.$pjax
            .off('pjax:send.' + hash)
            .off('pjax:complete.' + hash)
            .on('pjax:send.' + hash, function () {
              self.beforeGridLoaded();
            })
            .on('pjax:complete.' + hash, function () {
              self.afterGridLoaded();
            });
        }
      }

      // First GridView loaded
      self.afterGridLoaded();
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
    },


    // Load select2 for filters
    // ----------------------------------------------------
    loadSelect2: function() {
      this.$filters.find('select').select2({allowClear: false, placeholder: '- All -'});
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
      this.$clearButton.on('click', function(e){
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
        this.$filters.find(':input').clearFields();             // this will clear all input in the current grid
        // this.$filters.find(':input').first().trigger('change'); // to submit the form

        // Reload GridView
        $.pjax.reload({
          container: "#"+ self.$grid.attr('id') + '-container',
          url: self.$clearButton.attr('href')
        });
      }
      catch(clear_err) {
          return false;
      }
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
