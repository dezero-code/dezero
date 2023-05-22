(function(document, window, $) {
  $.dezeroGridview = {
    $grid: null,
    $filters: null,
    $pjax: null,

    init: function(grid, hash) {
      var self = this;

      self.$grid = $('#'+ grid);
      self.$filters = $('#'+ grid +'-filters');

      // PJAX - After AJAX update, trigger afterGridLoaded event
      if ( $.pjax) {
        self.$pjax = self.$grid.parents('[data-pjax-container]').first();
        if ( self.$pjax.length ) {
          self.$pjax
            .off('pjax:complete.' + hash)
            .on('pjax:complete.' + hash, function () {
              self.afterGridLoaded();
            });
        }
      }

      // First GridView loaded
      self.afterGridLoaded();

    },

    // Custom event after GridView is loaded
    // ----------------------------------------------------
    afterGridLoaded: function() {
      this.loadTooltip();
      this.loadSelect2();
    },


    // Load select2 for filters
    // ----------------------------------------------------
    loadSelect2: function() {
      this.$filters.find('select').select2({allowClear: false, placeholder: '- All -'});
    },

    // Load tooltip Javascript
    // ----------------------------------------------------
    loadTooltip: function() {
      var $tooltip_items = this.$grid.find('[data-toggle="tooltip"]');
      if ( $tooltip_items.length ) {
        $tooltip_items.tooltip('hide');
        $tooltip_items.tooltip();
      }
    }
  };
})(document, window, jQuery);
